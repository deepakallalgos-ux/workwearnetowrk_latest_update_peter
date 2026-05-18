<?php
if (isset($tpl['status']) && $tpl['status'] == 'IP_BLOCKED') {
	?>
	<div class="container-fluid"><div class="alert alert-danger"><?php __('front_ip_address_blocked'); ?></div></div>
	<?php
} else {
	include PJ_VIEWS_PATH . 'pjFront/elements/header.php';
	if ($controller->_get->toInt('layout') != 3) {
		?>
		<h1 class="scHeading"><?php __('front_view_order_details'); ?></h1>
		<?php
		if (isset($tpl['status']) && $tpl['status'] == 'OK') {
			echo '<p>' . pjSanitize::html($tpl['arr']['uuid']) . '</p>';
		} else {
			?><div class="alert alert-warning"><?php __('front_order_not_found'); ?></div><?php
		}
	} else {
		include PJ_VIEWS_PATH . 'pjFront/elements/layout_3/order_details.php';
	}
}
