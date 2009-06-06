<?php
class LightboxHelper extends AppHelper {

    var $helpers = array('Html', 'GershImage');

    function img($thumb = null, $full, $imgAttributes = array(), $linkAttributes = array(), $thumb_class = 'thumb', $full_class = 'full_lightbox') {
		$img_settings = Configure::read('Image.lightboxclass');
		if(!empty($img_settings)) {
			$defaultLinkAttributes = array('class'=>$img_settings);
		} else {
			$defaultLinkAttributes = array('class'=>'lightbox');
		}
        
        $linkAttributes = array_merge($defaultLinkAttributes, $linkAttributes);

		// Gersh: remove 'img/' 
		$this->fix_path($thumb);
		$this->fix_path($full);
		
		if($thumb == null) {
			$thumb = $this->GershImage->resize($full, $thumb_class, $imgAttributes);
		} else {
			$thumb = $this->Html->image($thumb, $imgAttributes);
		}
		
        
        if (strpos($full, '://') === false) {
            $full = $this->Html->webroot(IMAGES_URL . $full);
        }
        return $this->Html->output(sprintf($this->Html->tags['link'], $full, $this->Html->_parseAttributes($linkAttributes), $thumb));
    }
	function fix_path(&$url) {
		if(substr($url, 0, 4) == 'img/') {
			$url = substr($url, 4, strlen($url));
		}
	}
}

?>