<?php
	
	/*
	if (!class_exists('GershImageHelper')) {
		App::import('Helper', 'Extend.GershImage');
	}
	if (!class_exists('LightboxHelper')) {
		App::import('Helper', 'Extend.Lightbox');
	}*/
	

	class MediaEmbedHelper extends AppHelper {

		var $helpers = array('Extend.MediaImage');
		//var $images = array();
		
		
		function substituteEmbedTags ($text){
			
			$text = preg_replace( "/((\[image:)([^\]]+)\])/eU", '$this->gentag("\\1")', $text);
			return $text;
		}
		
		function gentag($match) {
			if(preg_match("/image:([0-9]+)\s+size:([a-z]+)/", $match, $matches)) {
				$view =& ClassRegistry::getObject('view');
				
				if(stripos($match, 'lightbox')) {
					$lightbox = 1;
				} else {
					$lightbox = 0;
				}
				if(preg_match("/align:([a-z]+)/", $match, $matches_align)) {
					$align = $matches_align[1];
				} else {
					$align = 0;
				}
				$key = 'embed_image' . $matches[1] . "_" . $matches[2] . "_" . $lightbox . "_" . $align;
				//
				return $view->element('fetch_attachment', array('plugin'=>'extend', 'cache'=> array('key' => $key, 'time' => '+1 day'), 'id'=>$matches[1], 'size'=>$matches[2], 'show_lightbox'=>$lightbox,'align'=>$align), true);
				
				
			} else {
				return '<p class="error">' . __('No image found', true) . '</p>';
			}
			
			//[image:{id} size:{thumb|medium|full|original} lightbox:{true}]  "|(\d{2}/\d{2}/)(\d{4})|U"

		}
		
	}
?>