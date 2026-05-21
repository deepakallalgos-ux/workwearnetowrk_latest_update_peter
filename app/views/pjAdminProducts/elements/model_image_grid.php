<?php
$model_image_id = isset($tpl['model_image_id']) ? (int) $tpl['model_image_id'] : 0;
$product_id = !empty($tpl['arr']['id']) ? (int) $tpl['arr']['id'] : 0;
$items = !empty($tpl['model_image_arr']) && is_array($tpl['model_image_arr']) ? $tpl['model_image_arr'] : array();
?>
<div class="pj-model-image-grid" data-product-id="<?php echo $product_id; ?>">
	<?php if (count($items) === 0) { ?>
	<p class="text-muted pj-model-image-grid-empty m-b-none"><?php __('lblProductModelImageGridEmpty', false, true); ?></p>
	<?php } else {
		foreach ($items as $item) {
			$id = (int) $item['id'];
			$is_active = ($id === $model_image_id);
			$src = PJ_INSTALL_URL . (!empty($item['small_path']) ? $item['small_path'] : IMG_PATH . 'no_image.png');
			?>
	<span class="pj-model-image-item" data-id="<?php echo $id; ?>">
		<button type="button" class="pj-model-image-delete btn btn-danger btn-xs" title="<?php __('btnDeleteModelImage', false, true); ?>" aria-label="<?php __('btnDeleteModelImage', false, true); ?>">&times;</button>
		<a href="#" class="pj-model-image-select s-Pic" rel="<?php echo $id; ?>">
			<img src="<?php echo $src; ?>?<?php echo rand(1, 9999999); ?>" alt="" class="s-Img<?php echo $is_active ? ' current' : ''; ?>" />
		</a>
		<?php if ($is_active) { ?>
		<span class="pj-model-image-active-badge label label-primary"><?php __('lblProductModelImageActive', false, true); ?></span>
		<?php } ?>
	</span>
			<?php
		}
	} ?>
</div>
