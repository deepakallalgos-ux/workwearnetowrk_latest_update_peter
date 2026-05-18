<?php
if (isset($tpl['arr']) && is_array($tpl['arr']))
{
	if (count($tpl['arr']) > 0)
	{
		foreach ($tpl['arr'] as $k => $item)
		{
			?>
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="stock-image" rel="<?php echo $item['id']; ?>"><img src="<?php echo PJ_INSTALL_URL . (!empty($item['small_path']) ? $item['small_path'] : IMG_PATH . 'no_image.png'); ?>?<?php echo rand(1, 9999999); ?>" alt="<?php echo pjSanitize::html($item['alt']); ?>" class="<?php echo $controller->_get->check('image_id') && $controller->_get->toInt('image_id') == $item['id'] ? 'current' : NULL; ?>" /></a>
			<?php
		}
	}else{
		__('lblNoImageUploaded');
	}
}
?>