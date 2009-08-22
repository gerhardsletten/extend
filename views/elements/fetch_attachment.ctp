<?php //$image = $this->requestAction('/images/images/fetch/' . $id); 
App::import('Model', 'Media.Attachment');
$attachment = new Attachment();
$attachment->recursive = 0;
$image = $attachment->read(null, $id);
$image = $image['Attachment'];
$class= "embed-image embed-" . $size;
$div_pre = "";
$div_post = "";
if(!empty($align) and $align !== 0) {
	$div_pre = '<div class="object-' . $align . '">';
	$div_post = "</div>";
}
if($show_lightbox === 1) {
	echo $div_pre . $mediaImage->lightbox($image, array('class'=>$class), array(), $size) . $div_post;
	//echo $div_pre . $mediaImage->resize($image, $size, array('class'=>$class)) . $div_post;
} else {
	echo $div_pre . $mediaImage->resize($image, $size, array('class'=>$class)) . $div_post;
}
?>