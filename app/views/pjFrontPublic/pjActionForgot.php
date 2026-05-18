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
		<h1 class="scHeading"><?php __('front_forgot_password'); ?></h1>
		<form action="" method="post" class="scForm scSelectorForgotForm">
			<input type="hidden" name="sc_forgot" value="1" />
			
			<div class="scPaper">
				<div class="scPaperSidebar">
					<div class="scPaperSidebarText"><?php __('front_forgot_note'); ?></div>
				</div>
				<div class="scPaperSheet">
					<div class="scPaperHeading"><?php __('front_forgot_password'); ?></div>
					<div class="scPaperContent">
						<div class="scNotice scSelectorNoticeMsg" style="display: none"></div>
						<p class="scPaperUnchained">
							<label class="scTitle"><?php __('client_email'); ?> <span class="scRequired">*</span>:</label>
							<input type="text" name="email" class="scText" placeholder="<?php __('front_placeholder_email', false, true); ?>" data-err="<?php echo $validate['email'];?>" data-email="<?php echo $validate['email_invalid'];?>"/>
						</p>
					</div>
				</div>
				<div class="scPaperControl">
					<div class="scPaperControlInner">
						<input type="submit" value="<?php __('front_send', false, true); ?>" class="scButton scButtonDark scButtonDarkNext scSelectorButton" />
						<a href="<?php echo pjUtil::getReferer(); ?>#!/Login" class="scLink scSelectorLogin"><?php __('front_login'); ?></a>
					</div>
				</div>
			</div>
			
		</form>
		<?php
	}else{
		include PJ_VIEWS_PATH . 'pjFront/elements/layout_3/forgot.php';
	} 
}
?>