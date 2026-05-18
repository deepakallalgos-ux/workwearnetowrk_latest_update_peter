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
	$titles = __('error_titles', true);
	$bodies = __('error_bodies', true);
	pjUtil::printNotice(__('plugin_gallery_info_add_gallery_title', true), __('plugin_gallery_info_add_gallery_desc', true));
	?>
	
	<?php if ($tpl['is_flag_ready']) : ?>
	<div class="multilang"></div>
	<?php endif; ?>
	
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjGallerySet&amp;action=pjActionCreate" method="post" id="frmCreateGallery" class="pj-form form">
		<input type="hidden" name="gallery_create" value="1" />
		<?php
		foreach ($tpl['lp_arr'] as $v)
		{
			?>
			<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
				<label class="title"><?php __('plugin_gallery_name'); ?></label>
				<span class="inline_block">
					<input type="text" name="i18n[<?php echo $v['id']; ?>][name]" class="pj-form-field w300<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" />
					<?php if ($tpl['is_flag_ready']) : ?>
					<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
					<?php endif; ?>
				</span>
			</p>
			<?php
		}
		?>
		<p>
			<label class="title"><?php __('plugin_gallery_medium_width'); ?></label>
			<span class="inline_block">
				<input type="text" name="medium_width" id="medium_width" class="pj-form-field w100" maxlength="5" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('plugin_gallery_medium_height'); ?></label>
			<span class="inline_block">
				<input type="text" name="medium_height" id="medium_height" class="pj-form-field w100" maxlength="5" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('plugin_gallery_thumb_width'); ?>:</label>
			<span class="inline_block">
				<input type="text" name="small_width" id="small_width" class="pj-form-field w100" maxlength="5" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('plugin_gallery_thumb_height'); ?></label>
			<span class="inline_block">
				<input type="text" name="small_height" id="small_height" class="pj-form-field w100" maxlength="5" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('plugin_gallery_status'); ?></label>
			<span class="inline_block">
				<select name="status" id="status" class="pj-form-field required">
					<option value="">-- <?php __('lblChoose'); ?>--</option>
					<?php
					foreach (__('plugin_gallery_statuses', true) as $k => $v)
					{
						?><option value="<?php echo $k; ?>"<?php echo $k == 'T' ? ' selected="selected"' : NULL;?>><?php echo $v; ?></option><?php
					}
					?>
				</select>
			</span>
		</p>
		<p>
			<label class="title">&nbsp;</label>
			<input type="submit" value="<?php __('plugin_gallery_btn_save'); ?>" class="pj-button" />
			<input type="button" value="<?php __('plugin_gallery_btn_cancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjGallerySet&action=pjActionIndex';" />
		</p>
	</form>
	
	<script type="text/javascript">
	<?php if ($tpl['is_flag_ready']) : ?>
	var pjLocale = pjLocale || {};
	pjLocale.langs = <?php echo $tpl['locale_str']; ?>;
	pjLocale.flagPath = "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/";
	pjLocale.localeId = "<?php echo $controller->getLocaleId(); ?>";
	(function ($) {
		$(function() {
			$(".multilang").multilang({
				langs: pjLocale.langs,
				flagPath: pjLocale.flagPath,
				tooltip: "",
				select: function (event, ui) {
					// Callback, e.g. ajax requests or whatever
				}
			});
		});
	})(jQuery_1_8_2);
	<?php endif; ?>
	</script>
	<?php
}
?>