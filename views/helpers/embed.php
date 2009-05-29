<?php
	
	/*
	if (!class_exists('GershImageHelper')) {
		App::import('Helper', 'Extend.GershImage');
	}
	if (!class_exists('LightboxHelper')) {
		App::import('Helper', 'Extend.Lightbox');
	}*/
	

	class EmbedHelper extends AppHelper {

		var $helpers = array('Extend.GershImage', 'Extend.Lightbox');
		var $images = array();
		
		function init() {
			debug("init");
		}
		
		function substituteEmbedTags ($text, $img_array = array()){
			
			$this->images = $img_array;
			$text = preg_replace( "/((\[image:)([^\]]+)\])/eU", '$this->gentag("\\1")', $text);
			return $text;
		}
		
		function gentag($match) {
			//debug($this->images);
			if(preg_match("/image:([0-9]+)\s+size:([a-z]+)/", $match, $matches)) {
				$view =& ClassRegistry::getObject('view');
				//debug($view);
				if(stripos($match, 'lightbox')) {
					$lightbox = true;
				} else {
					$lightbox = false;
				}
				if(preg_match("/align:([a-z]+)/", $match, $matches_align)) {
					$align = $matches_align[1];
				} else {
					$align = false;
				}
				 return $view->element('fetch_image', array('id'=>$matches[1], 'size'=>$matches[2], 'show_lightbox'=>$lightbox,'align'=>$align));
			} else {
				return '<p class="error">' . __('No image found', true) . '</p>';
			}
			
			//[image:{id} size:{thumb|medium|full|original} lightbox:{true}]  "|(\d{2}/\d{2}/)(\d{4})|U"

		}
		
	}
?>