<?php
if (isset($tpl['status']))
{
	$status = __('status', true);
	switch ($tpl['status'])
	{
		case 2:
			pjUtil::printNotice(NULL, $status[2]);
			break;
	}
} else {
	$plugin_menu = PJ_VIEWS_PATH . sprintf('pjLayouts/elements/menu_%s.php', $controller->getConst('PLUGIN_NAME'));
	$tabs_start = PJ_VIEWS_PATH . sprintf('pjLayouts/elements/tabs_start_%s.php', $controller->getConst('PLUGIN_NAME'));
	$tabs_end = PJ_VIEWS_PATH . sprintf('pjLayouts/elements/tabs_end_%s.php', $controller->getConst('PLUGIN_NAME'));
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<?php 
			if (is_file($tabs_start))
			{
				include $tabs_start;
			}
			?>
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjGallerySet&amp;action=pjActionIndex"><?php __('plugin_gallery_tab_gallery'); ?></a></li>
			<?php 
			if (is_file($tabs_end))
			{
				include $tabs_end;
			}
			?>
		</ul>
	</div>
	<?php
	if (is_file($plugin_menu))
	{
		include $plugin_menu;
	}
	pjUtil::printNotice(__('plugin_gallery_info_galleries_title', true), __('plugin_gallery_info_galleries_desc', true));
	
	$titles = __('error_titles', true);
	$bodies = __('error_bodies', true);
	if (isset($_GET['err']))
	{
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	$statuses = __('plugin_gallery_statuses', true);
	
	?>
	
	<div class="b10">
		<?php
		if($controller->getConst('PLUGIN_ADMIN_MODE') == true)
		{ 
			?>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="float_left pj-form r10">
				<input type="hidden" name="controller" value="pjGallerySet" />
				<input type="hidden" name="action" value="pjActionCreate" />
				<input type="submit" class="pj-button" value="<?php __('plugin_gallery_btn_add'); ?>" />
			</form>
			<?php
		} 
		?>
		<form action="" method="get" class="float_left pj-form frm-filter">
			<input type="text" name="q" class="pj-form-field pj-form-field-search w150" placeholder="<?php __('plugin_gallery_btn_search'); ?>" />
			<button type="reset" class="pj-form-reset" title="<?php __('plugin_gallery_reset_search', false, true); ?>"></button>
		</form>
		<div class="float_right t5">
			<a href="#" class="pj-button btn-all"><?php __('plugin_gallery_btn_all'); ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="T"><?php echo $statuses['T']; ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="F"><?php echo $statuses['F']; ?></a>
		</div>
		<br class="clear_both" />
	</div>
	
	<div id="grid"></div>
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.thumb = "<?php __('plugin_gallery_thumb'); ?>";
	myLabel.name = "<?php __('plugin_gallery_name'); ?>";
	myLabel.photos = "<?php echo ucfirst(__('plugin_gallery_photos', true)); ?>";
	myLabel.status = "<?php __('plugin_gallery_status'); ?>";
	myLabel.revert_status = "<?php __('plugin_gallery_revert_status'); ?>";
	myLabel.active = "<?php echo $statuses['T']; ?>";
	myLabel.inactive = "<?php echo $statuses['F']; ?>";
	myLabel.delete_confirmation = "<?php __('plugin_gallery_delete_set_confirmation'); ?>";
	myLabel.delete_selected = "<?php __('plugin_gallery_delete_selected'); ?>";
	myLabel.no_img = "<?php echo PJ_INSTALL_URL . $controller->getConst('PLUGIN_IMG_PATH') . '150x100.png'; ?>";
	myLabel.admin_mode = "<?php echo $controller->getConst('PLUGIN_ADMIN_MODE');?>";
	</script>
	<?php
}
?>