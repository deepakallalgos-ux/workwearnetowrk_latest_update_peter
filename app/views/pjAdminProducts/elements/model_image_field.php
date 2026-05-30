<?php
$model_image_id = !empty($tpl['arr']['model_image_id']) ? (int) $tpl['arr']['model_image_id'] : 0;
$product_id = !empty($tpl['arr']['id']) ? (int) $tpl['arr']['id'] : 0;
if (!isset($tpl['model_image_arr'])) {
	$tpl['model_image_arr'] = array();
}
?>
<div class="pj-model-image-field" data-product-id="<?php echo $product_id; ?>">
	<p class="text-muted small m-b-sm"><?php __('infoModelImageTabGrid', false, true); ?></p>
	<div class="pj-model-image-grid-wrap m-b-md">
		<?php include dirname(__FILE__) . '/model_image_grid.php'; ?>
	</div>
	<p class="m-b-none">
		<label class="btn btn-primary btn-outline btn-sm m-b-0">
			<i class="fa fa-upload"></i> <?php __('btnUploadModelImage', false, true); ?>
			<input type="file" class="pj-model-image-upload" accept="image/jpeg,image/png,image/gif,image/webp" style="display:none;" data-product-id="<?php echo $product_id; ?>" />
		</label>
	</p>
	<input type="hidden" name="model_image_id" value="<?php echo $model_image_id; ?>" />
</div>
