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
	<style type="text/css">
	/*.ui-widget-content{
		border: medium none;
	}
	.ui-tabs .ui-tabs-nav li a {
		padding: 0.5em 0.8em;
	}*/
	.mceEditor > table{
		width: 570px !important;
	}
	.ui-menu{
		height: 230px;
		overflow-y: scroll;
	}
	.ui-tabs .ui-tabs-panel{
		overflow: visible;
	}
	</style>
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
	if (isset($_GET['err']))
	{
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	?>
	
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjGallerySet&amp;action=pjActionUpdate" method="post" id="frmUpdateGallery" class="pj-form form">
		<input type="hidden" name="gallery_update" value="1" />
		<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']; ?>" />
		<input type="hidden" name="tab_id" value="<?php echo isset($_GET['tab_id']) && !empty($_GET['tab_id']) ? $_GET['tab_id'] : 'tabs-1'; ?>" />
		
		<div id="tabs">
			<?php
			if($controller->getConst('PLUGIN_ADMIN_MODE') == true)
			{ 
				?>
				<ul>
					<li><a href="#tabs-1"><?php __('plugin_gallery_tab_images'); ?></a></li>
					<li><a href="#tabs-2"><?php __('plugin_gallery_tab_options'); ?></a></li>
				</ul>
				<?php
			} 
			?>
			<div id="tabs-1">
				<?php
				pjUtil::printNotice(__('plugin_gallery_info_images_title', true), __('plugin_gallery_info_images_desc', true));
				?>
				<div id="gallery"></div>
			</div><!-- #tabs-1 -->
			<?php
			if($controller->getConst('PLUGIN_ADMIN_MODE') == true)
			{ 
				?>
				<div id="tabs-2">
					<?php
					pjUtil::printNotice(__('plugin_gallery_info_edit_gallery_title', true), __('plugin_gallery_info_edit_gallery_desc', true));
					?>
					<?php if ($tpl['is_flag_ready']) : ?>
					<div class="multilang"></div>
					<?php endif; ?>
					<div class="clear_both">
						<?php
						foreach ($tpl['lp_arr'] as $v)
						{
							?>
							<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
								<label class="title"><?php __('plugin_gallery_name'); ?></label>
								<span class="inline_block">
									<input type="text" name="i18n[<?php echo $v['id']; ?>][name]" class="pj-form-field w300<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" value="<?php echo htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']]['name'])); ?>"/>
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
								<input type="text" name="medium_width" id="medium_width" class="pj-form-field w100" value="<?php echo pjSanitize::html($tpl['arr']['medium_width']);?>" maxlength="5" />
							</span>
						</p>
						<p>
							<label class="title"><?php __('plugin_gallery_medium_height'); ?></label>
							<span class="inline_block">
								<input type="text" name="medium_height" id="medium_height" class="pj-form-field w100" value="<?php echo pjSanitize::html($tpl['arr']['medium_height']);?>" maxlength="5" />
							</span>
						</p>
						<p>
							<label class="title"><?php __('plugin_gallery_thumb_width'); ?>:</label>
							<span class="inline_block">
								<input type="text" name="small_width" id="small_width" class="pj-form-field w100" value="<?php echo pjSanitize::html($tpl['arr']['small_width']);?>" maxlength="5" />
							</span>
						</p>
						<p>
							<label class="title"><?php __('plugin_gallery_thumb_height'); ?></label>
							<span class="inline_block">
								<input type="text" name="small_height" id="small_height" class="pj-form-field w100" value="<?php echo pjSanitize::html($tpl['arr']['small_height']);?>" maxlength="5" />
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
					</div>
				</div><!-- #tabs-2 -->
				<?php
			} 
			?>
		</div><!-- #tabs -->
	</form>
	
	<script type="text/javascript">
	var myGallery = myGallery || {};
	myGallery.foreign_id = "<?php echo $tpl['arr']['id']; ?>";
	myGallery.model = "pjGallerySet";
	myGallery.hash = "";
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
					
				}
			});
		});
	})(jQuery_1_8_2);
	<?php endif; ?>
	</script>
	<?php
	if (isset($_GET['tab_id']) && !empty($_GET['tab_id']))
	{
		$tab_id = explode("-", $_GET['tab_id']);
		$tab_id = (int) $tab_id[1] - 1;
		$tab_id = $tab_id < 0 ? 0 : $tab_id;
		?>
			<script type="text/javascript">
			(function ($) {
				$(function () {
					$("#tabs").tabs("option", "active", <?php echo $tab_id; ?>);
				});
			})(jQuery_1_8_2);
			</script>
		<?php
	}
}
?>