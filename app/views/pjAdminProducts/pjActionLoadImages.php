<?php
$is_model_picker = isset($tpl['picker']) && $tpl['picker'] === 'model';
if ($is_model_picker) {
	?>
	<p class="text-muted small m-b-md"><?php __('infoModelImagePickerLead', false, false); ?></p>
	<?php
} else {
	?>
	<p class="text-muted small m-b-md"><?php __('infoStockImagePickerLead', false, true); ?></p>
	<?php
}
if (isset($tpl['arr']) && is_array($tpl['arr']))
{
	if (count($tpl['arr']) > 0)
	{
		echo '<div class="pj-stock-image-grid">';
		foreach ($tpl['arr'] as $k => $item)
		{
			?>
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="stock-image s-Pic" rel="<?php echo $item['id']; ?>"><img src="<?php echo PJ_INSTALL_URL . (!empty($item['small_path']) ? $item['small_path'] : IMG_PATH . 'no_image.png'); ?>?<?php echo rand(1, 9999999); ?>" alt="<?php echo pjSanitize::html($item['alt']); ?>" class="s-Img <?php echo $controller->_get->check('image_id') && $controller->_get->toInt('image_id') == $item['id'] ? 'current' : ''; ?>" /></a>
			<?php
		}
		echo '</div>';
	}else{
		if ($is_model_picker) {
			echo sprintf(__('lblProductModelImagePickerEmpty', false, false), '<strong>' . __('btnUploadModelImage', false, true) . '</strong>');
		} else {
			__('lblNoImageUploaded');
		}
	}
}
?>
