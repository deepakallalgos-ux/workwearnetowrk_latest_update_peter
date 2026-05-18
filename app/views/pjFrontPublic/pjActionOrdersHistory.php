<?php
if (isset($tpl['status']) && $tpl['status'] == 'IP_BLOCKED') {
	?>
	<div class="container-fluid"><div class="alert alert-danger"><?php __('front_ip_address_blocked'); ?></div></div>
	<?php
} elseif (isset($tpl['status']) && $tpl['status'] == 'LOGIN_REQUIRED') {
	include PJ_VIEWS_PATH . 'pjFront/elements/header.php';
	?>
	<div class="container-fluid pjScOrdersHistory">
		<div class="alert alert-warning"><?php __('front_order_login_required'); ?></div>
		<p><a href="<?php echo $tpl['option_arr']['o_install_url']; ?>/<?php echo (!empty($tpl['option_arr']['o_page_prefix'])) ? $tpl['option_arr']['o_page_prefix'] . '-' : ''; ?>login" class="scSelectorLogin btn btn-default"><?php __('front_login'); ?></a></p>
	</div>
	<?php
} else {
	include PJ_VIEWS_PATH . 'pjFront/elements/header.php';
	if ($controller->_get->toInt('layout') != 3) {
		?>
		<h1 class="scHeading"><?php __('front_orders_history'); ?></h1>
		<p class="text-muted"><?php __('front_orders_history'); ?></p>
		<?php
	} else {
		include PJ_VIEWS_PATH . 'pjFront/elements/layout_3/orders_history.php';
	}
}
