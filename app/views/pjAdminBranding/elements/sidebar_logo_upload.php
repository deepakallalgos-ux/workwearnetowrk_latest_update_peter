<?php
$adminSidebarLogoUrl = pjUtil::getAdminSidebarLogoUrl($tpl['option_arr']);
?>
<div class="row">
	<div class="col-lg-12">
		<div class="ibox float-e-margins m-b-md">
			<div class="ibox-content">
				<div class="form-horizontal">
					<div class="form-group m-b-none">
						<label class="col-lg-3 col-md-4 control-label"><?php __('lbl_admin_sidebar_logo'); ?></label>
						<div class="col-sm-9 col-md-8">
							<?php if (!empty($adminSidebarLogoUrl)) { ?>
								<div class="admin-sidebar-logo-preview m-b-md">
									<img src="<?php echo htmlspecialchars($adminSidebarLogoUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars(__('lbl_admin_sidebar_logo_alt', true, true), ENT_QUOTES, 'UTF-8'); ?>" class="admin-sidebar-logo-img">
								</div>
							<?php } ?>
							<form id="frmUploadSidebarLogo" action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjBaseOptions&amp;action=pjActionUploadSidebarLogo" method="post" enctype="multipart/form-data" class="m-b-sm">
								<div class="input-group">
									<input type="file" name="sidebar_logo" id="sidebar_logo_file" class="form-control" accept="image/png,image/jpeg,image/gif,image/webp,image/svg+xml,.png,.jpg,.jpeg,.gif,.webp,.svg" required>
									<span class="input-group-btn">
										<button type="submit" class="btn btn-primary"><?php __('btn_admin_sidebar_logo_upload'); ?></button>
									</span>
								</div>
							</form>
							<p class="text-muted m-b-sm"><?php __('info_admin_sidebar_logo_hint'); ?></p>
							<?php if (!empty($adminSidebarLogoUrl)) { ?>
								<?php
								$removeConfirmMsg = __('lbl_admin_sidebar_logo_remove_fallback', true, true);
								if (empty($removeConfirmMsg)) {
									$removeConfirmMsg = 'Remove the sidebar logo?';
								}
								?>
								<form id="frmRemoveSidebarLogo" action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjBaseOptions&amp;action=pjActionRemoveSidebarLogo" method="post" class="inline m-t-xs" onsubmit="return confirm(<?php echo pjAppController::jsonEncode($removeConfirmMsg); ?>);">
									<button type="submit" class="btn btn-default btn-sm"><?php __('btn_admin_sidebar_logo_remove'); ?></button>
								</form>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
