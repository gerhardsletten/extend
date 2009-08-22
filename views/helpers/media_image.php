<?php 
class MediaImageHelper extends AppHelper {

    var $helpers = array('Html', 'Media.Medium');
	
	var $cacheDir = 'imagecache'; // relative to IMAGES_URL path
	
	function url($image_obj, $image_class, $aspect = true) {
		return $this->output(
			$this->_resize($image_obj, $image_class, $aspect = true)
		);
	}
	
	function lightbox($image_obj, $options_img = array(), $options_link = array(), $thumb_class = 'thumb', $full_class = 'original') {
		$img_settings = Configure::read('Image.lightboxclass');
		if(!empty($img_settings)) {
			$defaultLinkAttributes = array('class'=>$img_settings);
		} else {
			$defaultLinkAttributes = array('class'=>'lightbox');
		}
		
		$options_link = array_merge($defaultLinkAttributes, $options_link);
		
		$full_img_url = $this->url($image_obj, $full_class);
		$thumb_img = $this->resize($image_obj, $thumb_class, $options_img);
		
		//'link' => '<a href="%s"%s>%s</a>',
		return $this->output(
			sprintf($this->Html->tags['link'], $full_img_url, $this->_parseAttributes($options_link), $thumb_img)
		);
	}
	
	function resize($image_obj, $image_class, $options = array(), $aspect = true) {
		
		$url_for_image = $this->_resize($image_obj, $image_class, $aspect = true);
		
		if(!isset($options['alt'])) {
			if(isset($image_obj['alternative']) and !empty($image_obj['alternative'])) {
				$options['alt'] = $image_obj['alternative'];
			} else {
				$options['alt'] = $image_obj['basename'];
			}
			
		}

		return $this->output(
			sprintf($this->Html->tags['image'], $url_for_image, $this->_parseAttributes($options) )
		);
	}
	
	function _resize($image_obj, $image_class, $aspect = true) {

		if(!isset($image_obj['basename']) and empty($image_obj['basename'])) {
			return false;
		}
		
		$orgfile_url = $this->Medium->url($this->Medium->file($image_obj));
		
		if($image_class == 'original') {
			return $orgfile_url;
		}
		
		$img_settings = Configure::read('Image');
		if($img_settings[$image_class]['width'] != '' and $img_settings[$image_class]['height'] != '') {
			$width = $img_settings[$image_class]['width'];
			$height = $img_settings[$image_class]['height'];
		} else {
			$width = 200;
			$height = 200;
		}		
		
		$types = array(1 => "gif", "jpeg", "png", "swf", "psd", "wbmp"); // used to determine image type
		
		$orgfile_path = WWW_ROOT.$this->themeWeb.MEDIA_URL.$image_obj['dirname'] . DS . $image_obj['basename'];

		if (!($size = getimagesize($orgfile_path))) {
			return; // image doesn't exist
		}
			
			
		if ($aspect) { // adjust to aspect.
			if (($size[1]/$height) > ($size[0]/$width)) {
				if($height > $size[1]) {
					$height = $size[1];
				}
				$width = ceil(($size[0]/$size[1]) * $height);
			} else {
				if($width > $size[0]) {
					$width = $size[0];
				}
				$height = ceil($width / ($size[0]/$size[1]));
			}
				
				
		}
		
		// Path to resized file
		$cachefile_url = $this->webroot.$this->themeWeb.MEDIA_FILTER_URL.$this->cacheDir.DS.$width.'x'.$height.'_'.basename($orgfile_path);

		$cachefile_path = WWW_ROOT.$this->themeWeb.MEDIA_FILTER_URL.$this->cacheDir.DS.$width.'x'.$height.'_'.basename($orgfile_path);  
		
		if (file_exists($cachefile_path)) {
			$csize = getimagesize($cachefile_path);
			$cached = ($csize[0] == $width && $csize[1] == $height); // image is cached
			if (@filemtime($cachefile_path) < @filemtime($orgfile_path)) {
				// check if up to date
				$cached = false;
			}
		} else {
			$cached = false;
		}
		
		if (!$cached) {
			$resize = ($size[0] >= $width || $size[1] >= $height) || ($size[0] <= $width || $size[1] <= $height);
		} else {
			$resize = false;
		}

		if ($resize) {
			$image = call_user_func('imagecreatefrom'.$types[$size[2]], $orgfile_path);
			if (function_exists("imagecreatetruecolor") && ($temp = imagecreatetruecolor ($width, $height))) {
				imagecopyresampled ($temp, $image, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
	  		} else {
				$temp = imagecreate ($width, $height);
				imagecopyresized ($temp, $image, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
			}
			call_user_func("image".$types[$size[2]], $temp, $cachefile_path);
			imagedestroy ($image);
			imagedestroy ($temp);
		} 		
		
		return $cachefile_url;
	}
	

}
?>