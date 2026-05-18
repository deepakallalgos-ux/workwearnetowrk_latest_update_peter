<input type="hidden" id="orderAttributes" name="orderAttributes" value=""/>
<div id="boxAttributes" class="form-group">
	<?php include dirname(__FILE__) . '/attributes_only.php'; ?>
</div>

<div class="form-group">
	<a href="javascript:void(0);" class="btn btn-primary btn-outline btn-sm btnAddAttribute"><?php __('product_attr_add'); ?></a>
	<?php __('product_attr_or'); ?>
	<a href="javascript:void(0);" class="btn btn-primary btn-outline btn-sm btnCopyAttribute"><?php __('product_attr_copy'); ?></a>
</div>

<div class="hr-line-dashed"></div>    						
    						
<div class="clearfix">
	<button type="submit" class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader pull-left" data-style="zoom-in" style="margin-right: 15px;">
		<span class="ladda-label"><?php __('btnSave'); ?></span>
		<?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
	</button>
	<a class="btn btn-white btn-lg pull-right" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminProducts&action=pjActionIndex"><?php __('btnCancel'); ?></a>
</div><!-- /.clearfix -->