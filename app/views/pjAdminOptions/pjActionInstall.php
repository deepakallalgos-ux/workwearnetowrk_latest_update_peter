<?php
$STORAGE = @$_SESSION[$controller->defaultCompany];
$ROLE_ID = @$_SESSION[$controller->defaultUser]['role_id'];
$previewCompanyId = pjUtil::resolvePreviewCompanyId();
$company_id = pjUtil::encodePreviewCompanyId($previewCompanyId);
$previewUrl = pjUtil::getPreviewUrl(array(
	'page_prefix' => isset($tpl['option_arr']['o_page_prefix']) ? $tpl['option_arr']['o_page_prefix'] : '',
	'with_company_id' => false,
), $previewCompanyId);
if (!empty($ROLE_ID)) {
	$role_id = base64_encode($ROLE_ID);
}
?>
<div class="row wrapper border-bottom white-bg page-heading  ">
	<div class="col-sm-12">
		<div class="row">
			<div class="col-sm-10">
				<h2><?php __('lblInstallJs1_title'); ?></h2>
			</div>
		</div><!-- /.row -->

		<p class="m-b-none"><i class="fa fa-info-circle"></i><?php __('lblInstallJs1_body'); ?></p>
	</div><!-- /.col-md-12 -->
</div>

<div class="row wrapper wrapper-content animated fadeInRight">
	<div class="col-lg-12">
		<?php
		$error_code = $controller->_get->toString('err');
		if (!empty($error_code)) {
			$titles = __('error_titles', true);
			$bodies = __('error_bodies', true);
			switch (true) {
				case in_array($error_code, array('AO01')):
		?>
					<div class="alert alert-success">
						<i class="fa fa-check m-r-xs"></i>
						<strong><?php echo @$titles[$error_code]; ?></strong>
						<?php echo @$bodies[$error_code] ?>
					</div>
		<?php
					break;
			}
		}
		?>
		<div class="tabs-container tabs-reservations m-b-lg">
			<ul class="nav nav-tabs" role="tablist">
				<li role="presentation" class="active"><a href="#tab-install" aria-controls="tab-install" role="tab" data-toggle="tab"><?php __('menuInstall'); ?></a></li>
				<li role="presentation"><a href="#tab-seo" aria-controls="tab-seo" role="tab" data-toggle="tab"><?php __('menuSeo'); ?></a></li>
			</ul>
			<div class="tab-content">
				<div role="tabpanel" class="tab-pane active" id="tab-install">
					<div class="panel-body">
						<form action="index.php?controller=pjAdminOptions&action=pjActionInstall" method="post">
							<div class="m-b-lg">
								<input type="hidden" id="role_id" value="<?php echo $ROLE_ID; ?>">
								<h2 class="no-margins"><?php __('lblInstallOptions'); ?></h2>
							</div>
							<div class="row">
								<div class="col-md-6 col-sm-12">
									<div class="form-group">
										<label class="control-label"><?php __('lblInstallUrl'); ?></label>

										<div class="input-group">
											<span class="input-group-addon"><i class="fa fa-globe"></i></span>
											<!-- <input type="text" name="o_install_url" id="o_install_url" value="<?php echo pjSanitize::html($tpl['option_arr']['o_install_url']); ?>" class="form-control" maxlength="255"> -->
											<?php
											$installUrl = pjUtil::getStorefrontBaseUrl($tpl['option_arr']['o_install_url']);
											$companyId  = $company_id;
											?>
											<input
												type="text"
												name="o_install_url"
												id="o_install_url"
												value="<?php echo pjSanitize::html($installUrl); ?>"
												class="form-control"
												maxlength="255">
											<span class="input-group-btn">
												<a href="<?php echo pjSanitize::html($previewUrl); ?>" class="btn btn-secondary" target="_blank" title="<?php __('script_preview_your_website', false, true); ?>">
													<i class="fa fa-external-link"></i> <?php __('script_preview_your_website'); ?>
												</a>
											</span>
										</div>
									</div>
								</div>
								<div class="col-md-3 col-sm-6">
									<div class="form-group">
										<label class="control-label"><?php __('lblPagePrefix'); ?></label>

										<input type="text" name="o_page_prefix" id="o_page_prefix" class="form-control" value="<?php echo pjSanitize::html($tpl['option_arr']['o_page_prefix']); ?>" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true); ?>" />
									</div>
								</div>
								<div class="col-md-3 col-sm-6">
									<div class="form-group">
										<label class="control-label">&nbsp;</label>

										<div class="clearfix">
											<button type="submit" class="ladda-button btn btn-primary btn-phpjabbers-loader pull-left" data-style="zoom-in" style="margin-right: 15px;">
												<span class="ladda-label"><?php __('btnSave'); ?></span>
												<?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
											</button>
										</div><!-- /.clearfix -->
									</div>
								</div>
							</div>
						</form>
						<div class="hr-line-dashed"></div>
						<form action="" method="get" class="form-horizontal">
							<style>
								.form-group-inline {
									display: flex;
									align-items: center;
									justify-content: space-between;
								}

								.form-group-inline .m-b-lg {
									margin-bottom: 0;
									/* so heading is vertically centered */
								}
							</style>
							<?php if ($ROLE_ID == 1) { ?>
								<div class="row">
									<div class="col-lg-6">
										<div class="form-group form-group-inline">
											<div class="m-b-lg">
												<h2 class="no-margins">
													<?php echo $tpl['is_flag_ready'] ? __('lblInstallConfig', true) : __('menuSuperAdmin', true); ?>
												</h2>
											</div>

											<div class="switch">
												<div class="onoffswitch onoffswitch-data">
													<input type="checkbox" class="onoffswitch-checkbox" name="is_super_admin" id="is_super_admin">
													<label class="onoffswitch-label" for="is_super_admin">
														<span class="onoffswitch-inner"
															data-on="<?php __('_yesno_ARRAY_T', false, true) ?>"
															data-off="<?php __('_yesno_ARRAY_F', false, true) ?>"></span>
														<span class="onoffswitch-switch"></span>
													</label>
												</div>
											</div>
										</div><!-- /.form-group -->
									</div>
								</div>
								<div class="hr-line-dashed"></div>
							<?php } ?>
							<div class="m-b-lg">
								<h2 class="no-margins"><?php echo $tpl['is_flag_ready'] ? __('lblInstallConfig', true) : __('menuCategories', true); ?></h2>
							</div>
							<div class="row">
								<div class="col-lg-8">
									<div class="form-group">
										<label class="col-lg-3 col-md-4 control-label"><?php __('lblInstallCategory'); ?></label>
										<div class="col-lg-7 col-md-8">
											<select name="install_category" id="install_category" class="form-control">
												<option value="">-- <?php __('lblAllCatgories'); ?> --</option>
												<?php
												foreach ($tpl['category_arr'] as $category) {
												?><option value="<?php echo $category['data']['id']; ?>"><?php echo str_repeat("-----", $category['deep']) . " " . pjSanitize::html($category['data']['name']); ?></option><?php
																																																						}
																																																							?>
											</select>
										</div>
									</div>
								</div>
							</div>

							<div style="display: <?php echo $tpl['is_flag_ready'] ? null : 'none'; ?>">
								<div class="row">
									<div class="col-lg-8">
										<div class="form-group">
											<label class="col-lg-3 col-md-4 control-label"><?php __('lblInstallConfigLocale'); ?></label>
											<div class="col-lg-7 col-md-8">
												<select name="install_locale" id="install_locale" class="form-control">
													<option value="">-- <?php __('lblChoose'); ?> --</option>
													<?php
													foreach ($tpl['locale_arr'] as $locale) {
													?><option value="<?php echo $locale['id']; ?>"><?php echo pjSanitize::html($locale['title']); ?></option><?php
																																							}
																																								?>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label class="col-lg-3 col-md-4 control-label"><?php __('lblInstallConfigHide'); ?></label>
											<div class="col-lg-7 col-md-8">
												<div class="clearfix">
													<div class="switch onoffswitch-data pull-left">
														<div class="onoffswitch">
															<input type="checkbox" class="onoffswitch-checkbox" id="install_hide" name="install_hide" value="1">
															<label class="onoffswitch-label" for="install_hide">
																<span class="onoffswitch-inner" data-on="Yes" data-off="No"></span>
																<span class="onoffswitch-switch"></span>
															</label>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="m-b-lg">
								<h2 class="no-margins"><?php __('lblInstallCode'); ?></h2>
							</div>
							<p class="alert alert-info alert-with-icon m-t-xs">
								<?php __('lblInstallJs1_1'); ?>
								<i class="fa fa-info-circle"></i><?php __('lblInstallCodeDesc'); ?>
							</p>

							<div class="row">
								<div class="col-lg-12">
									<div class="form-group">
										<div class="col-xs-12">
											<textarea class="form-control textarea_install" id="install_code" rows="4">
								&lt;link href="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/css/'; ?>pj.bootstrap.min.css" type="text/css" rel="stylesheet" /&gt; &lt;link href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&action=pjActionLoadCss" type="text/css" rel="stylesheet" /&gt;
								&lt;script type="text/javascript" src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&action=pjActionLoad"&gt;&lt;/script&gt;</textarea>
										</div>
									</div>
								</div>
								<div style="display:none" id="hidden_code">&lt;link href="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/css/'; ?>pj.bootstrap.min.css" type="text/css" rel="stylesheet" /&gt;
									&lt;link href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&action=pjActionLoadCss" type="text/css" rel="stylesheet" /&gt;
									&lt;script type="text/javascript" src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&action=pjActionLoad"&gt;&lt;/script&gt;</div>
							</div>
							<p class="alert alert-info alert-with-icon m-t-xs">
								<i class="fa fa-info-circle"></i><?php __('lblInstallJs2_1'); ?>
							</p>
							<div class="row">
								<div class="col-lg-12">
									<div class="form-group">
										<div class="col-xs-12">
											<textarea class="form-control textarea_install" rows="2">&lt;!doctype html&gt;</textarea>
										</div>
									</div>
								</div>
							</div>
							<p class="alert alert-info alert-with-icon m-t-xs">
								<i class="fa fa-info-circle"></i><?php __('lblInstallJs2_2'); ?>
							</p>
							<div class="row">
								<div class="col-lg-12">
									<div class="form-group">
										<div class="col-xs-12">
											<textarea class="form-control textarea_install" rows="2">&lt;meta http-equiv="Content-type" content="text/html; charset=utf-8" /&gt;&lt;meta name="viewport" content="width=device-width"&gt;</textarea>
										</div>
									</div>
								</div>
							</div>

						</form>
					</div>
				</div>
				<div role="tabpanel" class="tab-pane " id="tab-seo">
					<div class="panel-body">
						<p class="alert alert-info alert-with-icon m-t-xs">
							<i class="fa fa-info-circle"></i><?php __('lblInstallSeo_1'); ?>
						</p>
						<div class="form-group">
							<input type="text" id="uri_page" class="form-control" value="myPage.php" />
						</div>
						<p class="alert alert-info alert-with-icon m-t-xs">
							<i class="fa fa-info-circle"></i><?php __('lblInstallSeo_2'); ?>
						</p>
						<div class="form-group">
							<textarea class="form-control textarea_install" rows="2"> &lt;meta name="fragment" content="!"&gt;</textarea>
						</div>
						<p class="alert alert-info alert-with-icon m-t-xs">
							<i class="fa fa-info-circle"></i><?php __('lblInstallSeo_3'); ?>
						</p>
						<div class="form-group">
							<textarea class="form-control textarea_install" id="install_htaccess" rows="4"> RewriteEngine On RewriteCond %{QUERY_STRING} _escaped_fragment_=(.*) RewriteRule ^myPage.php <?php echo PJ_INSTALL_FOLDER; ?>index.php?controller=pjFrontPublic&action=pjActionRouter&_escaped_fragment_=%1 [L,NC]</textarea>

							<div style="display: none" id="hidden_htaccess">RewriteEngine On
								RewriteCond %{QUERY_STRING} _escaped_fragment_=(.*)
								RewriteRule ^::URI_PAGE:: <?php echo PJ_INSTALL_FOLDER; ?>index.php?controller=pjFrontPublic&action=pjActionRouter&_escaped_fragment_=%1 [L,NC]</div>
						</div>

						<p class="alert alert-info alert-with-icon m-t-xs">
							<i class="fa fa-info-circle"></i><?php __('lblInstallSeo_4'); ?>
						</p>
						<div class="form-group">
							<textarea class="form-control textarea_install" id="install_htaccess_remote" rows="4">RewriteEngine On RewriteCond %{QUERY_STRING} _escaped_fragment_=(.*)RewriteRule ^myPage.php <?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFrontPublic&action=pjActionRouter&_escaped_fragment_=%1 [L,NC,R=302]</textarea>

							<div style="display: none" id="hidden_htaccess_remote">RewriteEngine On
								RewriteCond %{QUERY_STRING} _escaped_fragment_=(.*)
								RewriteRule ^::URI_PAGE:: <?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFrontPublic&action=pjActionRouter&_escaped_fragment_=%1 [L,NC,R=302]</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	var COMPANY_ID = "<?php echo (string) $company_id; ?>";
	var ROLE_ID = "<?php echo (string) $role_id; ?>";
	// console.log('role_id---',ROLE_ID)
</script>