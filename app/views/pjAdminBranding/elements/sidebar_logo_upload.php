<?php
$adminSidebarLogoUrl = pjUtil::getAdminSidebarLogoUrl($tpl['option_arr']);
?>
<div class="row">
	<div class="col-lg-12">
		<div class="ibox float-e-margins m-b-md">
			<div class="ibox-content">
				<div class="form-horizontal">
					<div class="form-group m-b-none">
						<label class="col-lg-3 col-md-4 control-label">Admin sidebar logo</label>
						<div class="col-sm-9 col-md-8">
							<?php if (!empty($adminSidebarLogoUrl)) { ?>
								<div class="admin-sidebar-logo-preview m-b-md">
									<img src="<?php echo htmlspecialchars($adminSidebarLogoUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="Admin sidebar logo" class="admin-sidebar-logo-img">
								</div>
							<?php } ?>
							<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBranding&amp;action=pjActionUploadSidebarLogo" method="post" enctype="multipart/form-data" class="m-b-sm">
								<div class="input-group">
									<input type="file" name="sidebar_logo" class="form-control" accept="image/png,image/jpeg,image/gif,image/webp,image/svg+xml" required>
									<span class="input-group-btn">
										<button type="submit" class="btn btn-primary">Upload logo</button>
									</span>
								</div>
							</form>
							<p class="text-muted m-b-sm">Recommended: wide logo on a dark background (PNG or SVG). Max display width 200px in the side menu.</p>
							<?php if (!empty($adminSidebarLogoUrl)) { ?>
								<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBranding&amp;action=pjActionRemoveSidebarLogo" method="post" class="inline" onsubmit="return confirm('Remove the sidebar logo?');">
									<button type="submit" class="btn btn-default btn-sm">Remove logo</button>
								</form>
							<?php } ?>
						</div>
					</div>
				</div>
				
			</div>
		</div>
	</div>
</div>
