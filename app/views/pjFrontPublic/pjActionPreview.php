<?php 
if (isset($tpl['status']) && $tpl['status'] == 'IP_BLOCKED') {
	?>
	<div class="container-fluid"><div class="alert alert-danger"><?php __('front_ip_address_blocked'); ?></div></div>
	<?php
} else {
	include PJ_VIEWS_PATH . 'pjFront/elements/header.php';
	if($controller->_get->toInt('layout') != 3)
	{ 
		?>
		<h1 class="scHeading"><?php __('front_preview_order'); ?></h1>
		<?php
		if (isset($tpl['status']) && $tpl['status'] == 'OK')
		{
			$STORAGE = @$_SESSION[$controller->defaultForm];
			?>
			<form action="" method="post" class="scForm scSelectorPreviewForm">
				<input type="hidden" name="sc_preview" value="1" />
				
				<div class="scPaper">
					<div class="scPaperSidebar">
					<?php include dirname(__FILE__) . '/elements/price_info.php'; ?>
					</div>
					<div class="scPaperSheet">
						<?php
						if (!$controller->isLoged())
						{
							?>
							<div class="scPaperHeading"><?php __('order_customer'); ?></div>
							<div class="scPaperContent">
								<p class="scPaperChain">
									<label class="scTitle"><?php __('client_email'); ?></label>
									<span class="scValue"><?php echo pjSanitize::html(@$STORAGE['email']); ?></span>
								</p>
								<p class="scPaperChain">
									<label class="scTitle"><?php __('client_password'); ?></label>
									<span class="scValue"><?php echo pjSanitize::html(@$STORAGE['password']); ?></span>
								</p>
								<?php if (in_array((int) $tpl['option_arr']['o_bf_c_name'], array(2,3))) : ?>
								<p class="scPaperChain">
									<label class="scTitle"><?php __('client_name'); ?></label>
									<span class="scValue"><?php echo pjSanitize::html(@$STORAGE['client_name']); ?></span>
								</p>
								<?php endif; ?>
								<?php if (in_array((int) $tpl['option_arr']['o_bf_c_phone'], array(2,3))) : ?>
								<p class="scPaperChain">
									<label class="scTitle"><?php __('client_phone'); ?></label>
									<span class="scValue"><?php echo pjSanitize::html(@$STORAGE['phone']); ?></span>
								</p>
								<?php endif; ?>
								<?php if (in_array((int) $tpl['option_arr']['o_bf_c_url'], array(2,3))) : ?>
								<p class="scPaperChain">
									<label class="scTitle"><?php __('client_url'); ?></label>
									<span class="scValue"><?php echo pjSanitize::html(@$STORAGE['url']); ?></span>
								</p>
								<?php endif; ?>
							</div>
							<?php
						}
						?>
						<div class="scPaperHeading scPaperHeadingTop"><?php __('order_billing_details'); ?></div>
						<div class="scPaperContent">
							<?php if (in_array((int) $tpl['option_arr']['o_bf_b_name'], array(2,3))) : ?>
							<p class="scPaperChain">
								<label class="scTitle"><?php __('client_name'); ?></label>
								<span class="scValue"><?php echo pjSanitize::html(@$STORAGE['b_name']); ?></span>
							</p>
							<?php endif; ?>
							<?php if (in_array((int) $tpl['option_arr']['o_bf_b_country_id'], array(2,3))) : ?>
							<p class="scPaperChain">
								<label class="scTitle"><?php __('client_country'); ?></label>
								<span class="scValue">
								<?php
								foreach ($tpl['country_arr'] as $country)
								{
									if ($country['id'] == @$STORAGE['b_country_id'])
									{
										echo pjSanitize::html($country['name']);
										break;
									}
								}
								?>
								</span>
							</p>
							<?php endif; ?>
							<?php if (in_array((int) $tpl['option_arr']['o_bf_b_state'], array(2,3))) : ?>
							<p class="scPaperChain">
								<label class="scTitle"><?php __('client_state'); ?></label>
								<span class="scValue"><?php echo pjSanitize::html(@$STORAGE['b_state']); ?></span>
							</p>
							<?php endif; ?>
							<?php if (in_array((int) $tpl['option_arr']['o_bf_b_city'], array(2,3))) : ?>
							<p class="scPaperChain">
								<label class="scTitle"><?php __('client_city'); ?></label>
								<span class="scValue"><?php echo pjSanitize::html(@$STORAGE['b_city']); ?></span>
							</p>
							<?php endif; ?>
							<?php if (in_array((int) $tpl['option_arr']['o_bf_b_zip'], array(2,3))) : ?>
							<p class="scPaperChain">
								<label class="scTitle"><?php __('client_zip'); ?></label>
								<span class="scValue"><?php echo pjSanitize::html(@$STORAGE['b_zip']); ?></span>
							</p>
							<?php endif; ?>
							<?php if (in_array((int) $tpl['option_arr']['o_bf_b_address_1'], array(2,3))) : ?>
							<p class="scPaperChain">
								<label class="scTitle"><?php __('client_address_1'); ?></label>
								<span class="scValue"><?php echo pjSanitize::html(@$STORAGE['b_address_1']); ?></span>
							</p>
							<?php endif; ?>
							<?php if (in_array((int) $tpl['option_arr']['o_bf_b_address_2'], array(2,3))) : ?>
							<p class="scPaperChain">
								<label class="scTitle"><?php __('client_address_2'); ?></label>
								<span class="scValue"><?php echo pjSanitize::html(@$STORAGE['b_address_2']); ?></span>
							</p>
							<?php endif; ?>
						</div>
			
						<?php if ($controller->pjActionShowShipping()) : ?>
						<div class="scPaperHeading scPaperHeadingTop"><?php __('order_shipping_details'); ?></div>
						<div class="scPaperContent">
						<?php
						if (isset($STORAGE['same_as']))
						{
							?>
							<p class="scPaperChain">
								<span class="scValue scUpperCase"><?php __('order_same'); ?></span>
							</p>
							<?php
						} else {
							?>
							<?php if (in_array((int) $tpl['option_arr']['o_bf_s_name'], array(2,3))) : ?>
							<p class="scPaperChain">
								<label class="scTitle"><?php __('client_name'); ?></label>
								<span class="scValue"><?php echo pjSanitize::html(@$STORAGE['s_name']); ?></span>
							</p>
							<?php endif; ?>
							<?php if (in_array((int) $tpl['option_arr']['o_bf_s_country_id'], array(2,3))) : ?>
							<p class="scPaperChain">
								<label class="scTitle"><?php __('client_country'); ?></label>
								<span class="scValue">
								<?php
								foreach ($tpl['country_arr'] as $country)
								{
									if ($country['id'] == @$STORAGE['s_country_id'])
									{
										echo pjSanitize::html($country['name']);
										break;
									}
								}
								?>
								</span>
							</p>
							<?php endif; ?>
							<?php if (in_array((int) $tpl['option_arr']['o_bf_s_state'], array(2,3))) : ?>
							<p class="scPaperChain">
								<label class="scTitle"><?php __('client_state'); ?></label>
								<span class="scValue"><?php echo pjSanitize::html(@$STORAGE['s_state']); ?></span>
							</p>
							<?php endif; ?>
							<?php if (in_array((int) $tpl['option_arr']['o_bf_s_city'], array(2,3))) : ?>
							<p class="scPaperChain">
								<label class="scTitle"><?php __('client_city'); ?></label>
								<span class="scValue"><?php echo pjSanitize::html(@$STORAGE['s_city']); ?></span>
							</p>
							<?php endif; ?>
							<?php if (in_array((int) $tpl['option_arr']['o_bf_s_zip'], array(2,3))) : ?>
							<p class="scPaperChain">
								<label class="scTitle"><?php __('client_zip'); ?></label>
								<span class="scValue"><?php echo pjSanitize::html(@$STORAGE['s_zip']); ?></span>
							</p>
							<?php endif; ?>
							<?php if (in_array((int) $tpl['option_arr']['o_bf_s_address_1'], array(2,3))) : ?>
							<p class="scPaperChain">
								<label class="scTitle"><?php __('client_address_1'); ?></label>
								<span class="scValue"><?php echo pjSanitize::html(@$STORAGE['s_address_1']); ?></span>
							</p>
							<?php endif; ?>
							<?php if (in_array((int) $tpl['option_arr']['o_bf_s_address_2'], array(2,3))) : ?>
							<p class="scPaperChain">
								<label class="scTitle"><?php __('client_address_2'); ?></label>
								<span class="scValue"><?php echo pjSanitize::html(@$STORAGE['s_address_2']); ?></span>
							</p>
							<?php endif; ?>
							<?php
						}
						?>
						</div>
						<?php endif; ?>
				
						<?php
						if ((int) $tpl['option_arr']['o_disable_payments'] !== 1)
						{
							$payment_methods = __('payment_methods', true);
							$cc_types = __('cc_types', true);
							?>
							<div class="scPaperHeading scPaperHeadingTop"><?php __('order_payment_details'); ?></div>
							<div class="scPaperContent">
								<p class="scPaperChain">
									<label class="scTitle"><?php __('bf_payment'); ?></label>
									<span class="scValue"><?php echo @$payment_methods[$STORAGE['payment_method']]; ?></span>
								</p>
								<p class="scPaperChain scCcWrap" style="display: <?php echo @$STORAGE['payment_method'] != 'creditcard' ? 'none' : NULL; ?>">
									<label class="scTitle"><?php __('bf_cc_type'); ?></label>
									<span class="scValue"><?php echo @$cc_types[@$STORAGE['cc_type']]; ?></span>
								</p>
								<p class="scPaperChain scCcWrap" style="display: <?php echo @$STORAGE['payment_method'] != 'creditcard' ? 'none' : NULL; ?>">
									<label class="scTitle"><?php __('bf_cc_num'); ?></label>
									<span class="scValue"><?php echo pjSanitize::html(@$STORAGE['cc_num']); ?></span>
								</p>
								<p class="scPaperChain scCcWrap" style="display: <?php echo @$STORAGE['payment_method'] != 'creditcard' ? 'none' : NULL; ?>">
									<label class="scTitle"><?php __('bf_cc_sec'); ?></label>
									<span class="scValue"><?php echo pjSanitize::html(@$STORAGE['cc_code']); ?></span>
								</p>
								<p class="scPaperChain scCcWrap" style="display: <?php echo @$STORAGE['payment_method'] != 'creditcard' ? 'none' : NULL; ?>">
									<label class="scTitle"><?php __('bf_cc_exp'); ?></label>
									<span class="scValue"><?php printf("%s/%s", @$STORAGE['cc_exp_month'], @$STORAGE['cc_exp_year']); ?></span>
								</p>
								<p class="scPaperChain scBankWrap" style="display: <?php echo @$STORAGE['payment_method'] != 'bank' ? 'none' : NULL; ?>">
									<label class="scTitle"><?php __('bf_bank_account'); ?></label>
									<span class="scValue"><?php echo pjSanitize::html(nl2br($tpl['option_arr']['o_bank_account'])); ?></span>
								</p>
							</div>
							<?php
						}
						?>
				
						<div class="scPaperHeading scPaperHeadingTop"><?php __('order_other_details'); ?></div>
						<div class="scPaperContent">
							<?php if (in_array((int) $tpl['option_arr']['o_bf_notes'], array(2,3))) : ?>
							<p class="scPaperUnchained">
								<label class="scTitle"><?php __('bf_notes'); ?></label>
								<span class="scValue"><?php echo pjSanitize::html(@$STORAGE['notes']); ?></span>
							</p>
							<?php endif; ?>
							<div class="scNotice scSelectorNoticeMsg" style="display: none"></div>
						</div>
					</div>
					<div class="scPaperControl">
						<div class="scPaperControlInner">
							<input type="button" value="<?php __('front_edit_order', false, true); ?>" class="scButton scButtonLight scButtonLightPrev scSelectorButton scSelectorEditOrder" />
							<button type="submit" class="scButton scButtonDark scButtonDarkNext scSelectorButton"><span class="icon-refresh scRefreshIcon"></span>&nbsp;<?php __('front_confirm_procees', false, true); ?></button>
						</div>
					</div>
				</div>
			</form>
			<?php
		} elseif (isset($tpl['status']) && $tpl['status'] == 'ERR') {
			if (isset($tpl['code']))
			{
				switch ($tpl['code'])
				{
					case 100:
						?><div class="scMessage"><div class="scMessageIcon"></div><?php __('front_empty_shipping_location'); ?></div><?php
						break;
					case 101:
						?><div class="scMessage"><div class="scMessageIcon"></div><?php __('front_empty_shopping_cart'); ?></div><?php
						break;
					case 102:
						?><div class="scMessage"><div class="scMessageIcon"></div><?php __('front_empty_checkout_form'); ?></div><?php
						break;
				}
			}
		}
	}else{
		include PJ_VIEWS_PATH . 'pjFront/elements/layout_3/preview.php';
	}
}
?>