<?php
/**
 * Model image tab — separate from product Photos gallery.
 */
?>
<div class="adm-card pj-model-image-tab">
	<h4 class="adm-card__heading">
		<i class="fa fa-user"></i>
		<?php __('product_model_image_tab'); ?>
	</h4>
	<p class="text-muted small m-b-md"><?php __('lblProductModelImageTabLead'); ?></p>
	<?php include_once dirname(__FILE__) . '/model_image_field.php'; ?>
</div>
