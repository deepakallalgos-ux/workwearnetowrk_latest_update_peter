<?php
if (isset($tpl['status']) && $tpl['status'] == 'IP_BLOCKED') {
	?>
	<div class="container-fluid"><div class="alert alert-danger"><?php __('front_ip_address_blocked'); ?></div></div>
	<?php
} else {
	include PJ_VIEWS_PATH . 'pjFront/elements/header.php';
	$validate = str_replace(array('"', "'"), array('\"', "\'"), __('validate', true, true));
	if($controller->_get->toInt('layout') != 3)
	{
		?>
		<h1 class="scHeading"><?php __('front_login'); ?></h1>
		<form action="" method="post" class="scForm scSelectorLoginForm">
			<input type="hidden" name="sc_login" value="1" />
			
			<div class="scPaper">
				<div class="scPaperSidebar">
					<div class="scPaperSidebarText"><?php __('front_login_note'); ?></div>
				</div>
				<div class="scPaperSheet">
					<div class="scPaperHeading"><?php __('front_login'); ?></div>
					<div class="scPaperContent">
						<div class="scNotice scSelectorNoticeMsg" style="display: none"></div>
						<p class="scPaperUnchained">
							<label class="scTitle"><?php __('client_email'); ?> <span class="scRequired">*</span>:</label>
							<input type="text" name="email" class="scText" placeholder="<?php __('front_placeholder_email', false, true); ?>" data-err="<?php echo $validate['email'];?>" data-email="<?php echo $validate['email_invalid'];?>"/>
						</p>
						<p class="scPaperUnchained">
							<label class="scTitle"><?php __('client_password'); ?> <span class="scRequired">*</span>:</label>
							<input type="password" name="password" class="scText" placeholder="<?php __('front_placeholder_password', false, true); ?>" data-err="<?php echo $validate['password'];?>"/>
						</p>
					</div>
				</div>
				<div class="scPaperControl">
					<div class="scPaperControlInner">
						<input type="submit" value="<?php __('front_login', false, true); ?>" class="scButton scButtonDark scButtonDarkNext scSelectorButton" />
						<a href="<?php echo pjUtil::getReferer(); ?>#!/Forgot" class="scLink scSelectorForgot"><?php __('front_forgot'); ?></a>
					</div>
				</div>
			</div>
				
		</form>
		<?php
	}else{
		include PJ_VIEWS_PATH . 'pjFront/elements/layout_3/login.php';
	} 
}
?>