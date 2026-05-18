<?php
$titles = __('error_titles', true);
$bodies = __('error_bodies', true);
?>
<div class="row wrapper border-bottom white-bg page-heading">
	<div class="col-sm-12">
		<div class="row">
			<div class="col-lg-9 col-md-8 col-sm-6">
				<h2><?php echo @$titles['AO26']; ?></h2>
			</div>
			<div class="col-lg-3 col-md-4 col-sm-6 btn-group-languages">
				<?php if ($tpl['is_flag_ready']) : ?>
					<div class="multilang"></div>
				<?php endif; ?>
			</div>
		</div><!-- /.row -->

		<p class="m-b-none"><i class="fa fa-info-circle"></i><?php echo @$bodies['AO26']; ?></p>
	</div><!-- /.col-md-12 -->
</div>

<div class="row wrapper wrapper-content animated fadeInRight">
	<?php
	if (isset($tpl['arr']) && is_array($tpl['arr']) && !empty($tpl['arr'])) {
	?>
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<div class="ibox-content">
					<?php
					$error_code = $controller->_get->toString('err');
					if (!empty($error_code)) {
						$titles = __('error_titles', true);
						$bodies = __('error_bodies', true);
						switch (true) {
							case in_array($error_code, array('AOP04')):
					?>
								<div class="alert alert-success">
									<i class="fa fa-check m-r-xs"></i>
									<strong><?php echo @$titles[$error_code]; ?></strong>
									<?php echo @$bodies[$error_code] ?>
								</div>
							<?php
								break;
							case in_array($error_code, array('')):
							?>
								<div class="alert alert-danger">
									<i class="fa fa-exclamation-triangle m-r-xs"></i>
									<strong><?php echo @$titles[$error_code]; ?></strong>
									<?php echo @$bodies[$error_code] ?>
								</div>
					<?php
								break;
						}
					}

					?>
					<form id="frmUpdateOptions" action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionUpdate" class="form-horizontal" method="post">
						<input type="hidden" name="options_update" value="1" />
						<input type="hidden" name="tab" value="4" />
						<input type="hidden" name="next_action" value="pjActionTerm" />

						<div class="ibox-content ibox-heading">
							<h3><?php __('lblOptionsTermsURL'); ?></h3>
							<small><?php __('lblOptionsTermsURLDesc'); ?></small>
						</div>

						<div class="ibox-content">
							<div class="form-group">
								<?php
								foreach ($tpl['lp_arr'] as $v) {
									$company_id = (int) $tpl['default_company']['id'];
									$key = 'terms_url_' . $company_id;
								?>
									<div class="<?php echo $tpl['is_flag_ready'] ? 'input-group ' : NULL; ?>pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : 'table'; ?>">
										<span class="input-group-addon"><i class="fa fa-globe"></i></span>

										<input type="text" name="i18n[<?php echo $v['id']; ?>][terms_url_<?php echo $tpl['default_company']['id'] ?>]" type="text" value="<?php echo pjSanitize::html(@$tpl['arr']['i18n'][$v['id']][$key]); ?>" class="form-control url" />
										<?php if ($tpl['is_flag_ready']) : ?>
											<span class="input-group-addon pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="<?php echo pjSanitize::html($v['name']); ?>"></span>
										<?php endif; ?>
									</div>
								<?php
								}
								?>
							</div>
						</div>

						<div class="ibox-content ibox-heading">
							<h3><?php __('lblOptionsTermsContent'); ?></h3>
							<small><?php __('lblOptionsTermsContentDesc'); ?></small>
						</div>

						<div class="ibox-content">
							<div class="form-group">
								<?php
								foreach ($tpl['lp_arr'] as $v) {
									$company_id = (int) $tpl['default_company']['id'];
									$key = 'terms_body_' . $company_id;
								?>
									<div class="<?php echo $tpl['is_flag_ready'] ? 'input-group ' : NULL; ?>pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 1 ? NULL : 'none'; ?>">
										<textarea name="i18n[<?php echo $v['id']; ?>][terms_body_<?php echo $tpl['default_company']['id'] ?>]" rows="10" class="form-control mceEditor<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>"><?php echo isset($tpl['arr']['i18n'][$v['id']][$key]) && !empty($tpl['arr']['i18n'][$v['id']][$key]) ? stripslashes(@$tpl['arr']['i18n'][$v['id']][$key]) : ''; ?></textarea>
										<?php if ($tpl['is_flag_ready']) : ?>
											<span class="input-group-addon pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="<?php echo pjSanitize::html($v['name']); ?>"></span>
										<?php endif; ?>
									</div>
								<?php
								}
								?>
							</div>
						</div>

						<div class="hr-line-dashed"></div>

						<div class="row">
							<div class="col-xs-12">
								<button type="submit" class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader" data-style="zoom-in">
									<span class="ladda-label"><?php __('plugin_base_btn_save'); ?></span>
									<?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div><!-- /.col-lg-12 -->
	<?php
	}
	?>
</div>
<script type="text/javascript">
	<?php if ($tpl['is_flag_ready']) : ?>
		var pjCmsLocale = pjCmsLocale || {};
		pjCmsLocale.langs = <?php echo $tpl['locale_str']; ?>;
		pjCmsLocale.flagPath = "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/";
	<?php endif; ?>
</script>