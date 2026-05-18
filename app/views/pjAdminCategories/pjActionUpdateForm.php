<?php
$filter = __('filter', true);
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCategories&amp;action=pjActionUpdate" method="post" id="frmUpdateCategory">
	<input type="hidden" name="category_update" value="1" />
	<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']; ?>" />
	<div class="panel-heading bg-completed">
		<p class="lead m-n"><?php __('infoUpdateCategoryTitle'); ?></p>
	</div><!-- /.panel-heading -->

	<div class="panel-body">
		<?php
		$child_ids = isset($child_ids) && is_array($child_ids) ? $child_ids : [];

		foreach ($tpl['lp_arr'] as $v) {
		?>
			<div class="form-group pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 1 ? NULL : 'none'; ?>">
				<label class="control-label"><?php __('category_name'); ?></label>

				<div class="<?php echo $tpl['is_flag_ready'] ? 'input-group' : ''; ?>" data-index="<?php echo $v['id']; ?>">
					<input type="text" class="form-control<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" name="i18n[<?php echo $v['id']; ?>][name]" value="<?php echo pjSanitize::html(@$tpl['arr']['i18n'][$v['id']]['name']); ?>" data-msg-required="<?php __('pj_field_required', false, true); ?>">
					<?php if ($tpl['is_flag_ready']) : ?>
						<span class="input-group-addon pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="<?php echo pjSanitize::html($v['name']); ?>"></span>
					<?php endif; ?>
				</div>
			</div>
		<?php
		}
		?>
		<div class="form-group">
			<label class="control-label"><?php __('category_parent'); ?></label>
			<select name="parent_id" id="parent_id" class="form-control" data-msg-required="<?php __('pj_field_required', false, true); ?>">
				<option value="1"><?php __('category_no_parent'); ?></option>
				<?php
				foreach ($tpl['node_arr'] as $node) {
					$disabled = NULL;
					if ($node['data']['id'] == $tpl['arr']['id'] || in_array($node['data']['id'], $child_ids)) {
						$disabled = ' disabled="disabled"';
					}
				?><option value="<?php echo $node['data']['id']; ?>" <?php echo $disabled; ?><?php echo $tpl['arr']['parent_id'] == $node['data']['id'] ? ' selected="selected"' : NULL; ?>><?php echo str_repeat('------', $node['deep']) . " " . $node['data']['name']; ?></option><?php
																																																																						}
																																																																							?>
			</select>
		</div>

		<div class="m-t-lg">
			<button type="submit" class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader pull-left" data-style="zoom-in" style="margin-right: 15px;">
				<span class="ladda-label"><?php __('btnSave'); ?></span>
				<?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
			</button>
			<button type="button" class="btn btn-white btn-lg pull-right pjScBtnCancel"><?php __('btnCancel'); ?></button>
		</div><!-- /.clearfix -->
	</div><!-- /.panel-body -->
</form>