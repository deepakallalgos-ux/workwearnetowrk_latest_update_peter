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
		<h1 class="scHeading"><?php __('front_checkout'); ?></h1>
		<?php
		if (isset($tpl['status']) && $tpl['status'] == 'OK')
		{
			$bSaveReady = $sSaveReady = $bSaveChecked = $sSaveChecked = false;
			
			$STORAGE = @$_SESSION[$controller->defaultForm];
			$billing = $shipping = $STORAGE;
			$isLoged = $controller->isLoged();
			if ($isLoged && is_null($STORAGE))
			{
				if (isset($tpl['address_arr']) && !empty($tpl['address_arr']))
				{
					foreach ($tpl['address_arr'] as $address)
					{
						if ((int) $address['is_default_shipping'] === 1)
						{
							$shipping = array(
								's_address_id' => $address['id'],
								's_name' => $address['name'],
								's_country_id' => $address['country_id'],
								's_city' => $address['city'],
								's_state' => $address['state'],
								's_zip' => $address['zip'],
								's_address_1' => $address['address_1'],
								's_address_2' => $address['address_2']
							);
						}
					
						if ((int) $address['is_default_billing'] === 1)
						{
							$billing = array(
								'b_address_id' => $address['id'],
								'b_name' => $address['name'],
								'b_country_id' => $address['country_id'],
								'b_city' => $address['city'],
								'b_state' => $address['state'],
								'b_zip' => $address['zip'],
								'b_address_1' => $address['address_1'],
								'b_address_2' => $address['address_2']
							);;
						}
					}
				}
				
				if (empty($shipping))
				{
					$shipping = array(
						's_name' => $_SESSION[$controller->defaultUser]['client_name']
					);
				}
				if (empty($billing))
				{
					$billing = array(
						'b_name' => $_SESSION[$controller->defaultUser]['client_name']
					);
				}
			}
			
			if ($isLoged && isset($tpl['address_arr']) && !empty($tpl['address_arr']) && isset($STORAGE['b_save']))
			{
				$bSaveReady = true;
				$bSaveChecked = true;
			}
			
			if ($isLoged && isset($tpl['address_arr']) && !empty($tpl['address_arr']) && isset($STORAGE['s_save']))
			{
				$sSaveReady = true;
				$sSaveChecked = true;
			}
			?>
			<form action="" method="post" class="scForm scSelectorCheckoutForm">
				<input type="hidden" name="sc_checkout" value="1" />
				
				<div class="scPaper">
					<div class="scPaperSidebar">
					<?php include dirname(__FILE__) . '/elements/price_info.php'; ?>
					</div>
					<div class="scPaperSheet">
						<?php
						if (!$isLoged)
						{
							?>
							<div class="scPaperHeading"><?php __('order_customer'); ?></div>
							<div class="scPaperContent">
								<p class="scPaperChain">
									<label class="scTitle"><?php __('client_email'); ?> <span class="scRequired">*</span></label>
									<input type="text" name="email" class="scText required email" placeholder="<?php __('front_placeholder_email', false, true); ?>" value="<?php echo pjSanitize::html(@$STORAGE['email']); ?>" data-err="<?php echo $validate['email'];?>" data-email="<?php echo $validate['email_invalid'];?>"/>
								</p>
								<p class="scPaperChain">
									<label class="scTitle"><?php __('client_password'); ?> <span class="scRequired">*</span></label>
									<input type="password" name="password" class="scText required" placeholder="<?php __('front_placeholder_password', false, true); ?>" value="<?php echo pjSanitize::html(@$STORAGE['password']); ?>" data-err="<?php echo $validate['password'];?>"/>
								</p>
								<?php if (in_array((int) $tpl['option_arr']['o_bf_c_name'], array(2,3))) : ?>
								<p class="scPaperChain">
									<label class="scTitle"><?php __('client_name'); ?><?php if ((int) $tpl['option_arr']['o_bf_c_name'] === 3) : ?> <span class="scRequired">*</span><?php endif; ?></label>
									<input type="text" name="client_name" class="scText<?php echo (int) $tpl['option_arr']['o_bf_c_name'] === 3 ? ' required' : NULL; ?>" placeholder="<?php __('front_placeholder_name', false, true); ?>" value="<?php echo pjSanitize::html(@$STORAGE['client_name']); ?>" data-err="<?php echo $validate['name'];?>"/>
								</p>
								<?php endif; ?>
								<?php if (in_array((int) $tpl['option_arr']['o_bf_c_phone'], array(2,3))) : ?>
								<p class="scPaperChain">
									<label class="scTitle"><?php __('client_phone'); ?><?php if ((int) $tpl['option_arr']['o_bf_c_phone'] === 3) : ?> <span class="scRequired">*</span><?php endif; ?></label>
									<input type="text" name="phone" class="scText<?php echo (int) $tpl['option_arr']['o_bf_c_phone'] === 3 ? ' required' : NULL; ?>" placeholder="<?php __('front_placeholder_phone', false, true); ?>" value="<?php echo pjSanitize::html(@$STORAGE['phone']); ?>" data-err="<?php echo $validate['phone'];?>"/>
								</p>
								<?php endif; ?>
								<?php if (in_array((int) $tpl['option_arr']['o_bf_c_url'], array(2,3))) : ?>
								<p class="scPaperChain">
									<label class="scTitle"><?php __('client_url'); ?><?php if ((int) $tpl['option_arr']['o_bf_c_url'] === 3) : ?> <span class="scRequired">*</span><?php endif; ?></label>
									<input type="text" name="url" class="scText<?php echo (int) $tpl['option_arr']['o_bf_c_url'] === 3 ? ' required' : NULL; ?>" placeholder="<?php __('front_placeholder_url', false, true); ?>" value="<?php echo pjSanitize::html(@$STORAGE['url']); ?>" data-err="<?php echo $validate['url'];?>"/>
								</p>
								<?php endif; ?>
							</div>
							<?php
						}
						?>
						<div class="scPaperHeading<?php echo !$isLoged ? ' scPaperHeadingTop' : NULL; ?>"><?php __('order_billing_details'); ?></div>
						<div class="scPaperContent">
							<?php
							if ($isLoged && isset($tpl['address_arr']) && !empty($tpl['address_arr']))
							{
								?>
								<p class="scPaperChain">
									<label class="scTitle"><?php __('order_address'); ?></label>
									<select name="b_address_id" class="scSelect scSelectorAddressId">
										<option value=""><?php __('choose_address'); ?></option>
										<?php
										foreach ($tpl['address_arr'] as $address)
										{
											$addr = array($address['city'], $address['zip'], $address['address_1']);
											?><option value="<?php echo $address['id']; ?>"<?php echo $address['id'] != @$billing['b_address_id'] ? NULL : ' selected="selected"'; ?>><?php echo pjSanitize::html(join(", ", array_filter($addr))); ?></option><?php
										}
										?>
									</select>
								</p>
								<?php
							}
							?>
							<?php if (in_array((int) $tpl['option_arr']['o_bf_b_name'], array(2,3))) : ?>
							<p class="scPaperChain">
								<label class="scTitle"><?php __('client_name'); ?><?php if ((int) $tpl['option_arr']['o_bf_b_name'] === 3) : ?> <span class="scRequired">*</span><?php endif; ?></label>
								<input type="text" name="b_name" class="scText scSelectorOriginalB<?php echo (int) $tpl['option_arr']['o_bf_b_name'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$billing['b_name']); ?>" placeholder="<?php __('front_placeholder_b_name', false, true); ?>" data-original="<?php echo pjSanitize::html(@$billing['b_name']); ?>" data-err="<?php echo $validate['name'];?>"/>
							</p>
							<?php endif; ?>
							<?php if (in_array((int) $tpl['option_arr']['o_bf_b_country_id'], array(2,3))) : ?>
							<p class="scPaperChain">
								<label class="scTitle"><?php __('client_country'); ?><?php if ((int) $tpl['option_arr']['o_bf_b_country_id'] === 3) : ?> <span class="scRequired">*</span><?php endif; ?></label>
								<select name="b_country_id" class="scSelect scSelectorOriginalB<?php echo (int) $tpl['option_arr']['o_bf_b_country_id'] === 3 ? ' required' : NULL; ?>" data-original="<?php echo pjSanitize::html(@$billing['b_country_id']); ?>" data-err="<?php echo $validate['country'];?>">
									<option value=""><?php __('choose_country'); ?></option>
									<?php
									foreach ($tpl['country_arr'] as $country)
									{
										?><option value="<?php echo $country['id']; ?>"<?php echo $country['id'] != @$billing['b_country_id'] ? NULL : ' selected="selected"'; ?>><?php echo pjSanitize::html($country['name']); ?></option><?php
									}
									?>
								</select>
							</p>
							<?php endif; ?>
							<?php if (in_array((int) $tpl['option_arr']['o_bf_b_state'], array(2,3))) : ?>
							<p class="scPaperChain">
								<label class="scTitle"><?php __('client_state'); ?><?php if ((int) $tpl['option_arr']['o_bf_b_state'] === 3) : ?> <span class="scRequired">*</span><?php endif; ?></label>
								<input type="text" name="b_state" class="scText scSelectorOriginalB<?php echo (int) $tpl['option_arr']['o_bf_b_state'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$billing['b_state']); ?>" placeholder="<?php __('front_placeholder_b_state', false, true); ?>" data-original="<?php echo pjSanitize::html(@$billing['b_state']); ?>" data-err="<?php echo $validate['state'];?>"/>
							</p>
							<?php endif; ?>
							<?php if (in_array((int) $tpl['option_arr']['o_bf_b_city'], array(2,3))) : ?>
							<p class="scPaperChain">
								<label class="scTitle"><?php __('client_city'); ?><?php if ((int) $tpl['option_arr']['o_bf_b_city'] === 3) : ?> <span class="scRequired">*</span><?php endif; ?></label>
								<input type="text" name="b_city" class="scText scSelectorOriginalB<?php echo (int) $tpl['option_arr']['o_bf_b_city'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$billing['b_city']); ?>" placeholder="<?php __('front_placeholder_b_city', false, true); ?>" data-original="<?php echo pjSanitize::html(@$billing['b_city']); ?>" data-err="<?php echo $validate['city'];?>"/>
							</p>
							<?php endif; ?>
							<?php if (in_array((int) $tpl['option_arr']['o_bf_b_zip'], array(2,3))) : ?>
							<p class="scPaperChain">
								<label class="scTitle"><?php __('client_zip'); ?><?php if ((int) $tpl['option_arr']['o_bf_b_zip'] === 3) : ?> <span class="scRequired">*</span><?php endif; ?></label>
								<input type="text" name="b_zip" class="scText scSelectorOriginalB<?php echo (int) $tpl['option_arr']['o_bf_b_zip'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$billing['b_zip']); ?>" placeholder="<?php __('front_placeholder_b_zip', false, true); ?>" data-original="<?php echo pjSanitize::html(@$billing['b_zip']); ?>" data-err="<?php echo $validate['zip'];?>"/>
							</p>
							<?php endif; ?>
							<?php if (in_array((int) $tpl['option_arr']['o_bf_b_address_1'], array(2,3))) : ?>
							<p class="scPaperChain">
								<label class="scTitle"><?php __('client_address_1'); ?><?php if ((int) $tpl['option_arr']['o_bf_b_address_1'] === 3) : ?> <span class="scRequired">*</span><?php endif; ?></label>
								<input type="text" name="b_address_1" class="scText scSelectorOriginalB<?php echo (int) $tpl['option_arr']['o_bf_b_address_1'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$billing['b_address_1']); ?>" placeholder="<?php __('front_placeholder_b_address_1', false, true); ?>" data-original="<?php echo pjSanitize::html(@$billing['b_address_1']); ?>" data-err="<?php echo $validate['address_1'];?>"/>
							</p>
							<?php endif; ?>
							<?php if (in_array((int) $tpl['option_arr']['o_bf_b_address_2'], array(2,3))) : ?>
							<p class="scPaperChain">
								<label class="scTitle"><?php __('client_address_2'); ?><?php if ((int) $tpl['option_arr']['o_bf_b_address_2'] === 3) : ?> <span class="scRequired">*</span><?php endif; ?></label>
								<input type="text" name="b_address_2" class="scText scSelectorOriginalB<?php echo (int) $tpl['option_arr']['o_bf_b_address_2'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$billing['b_address_2']); ?>" placeholder="<?php __('front_placeholder_b_address_2', false, true); ?>" data-original="<?php echo pjSanitize::html(@$billing['b_address_2']); ?>" data-err="<?php echo $validate['address_2'];?>"/>
							</p>
							<?php endif; ?>
							<p class="scPaperChain scSelectorSaveB" style="display: <?php echo $bSaveReady ? NULL : 'none'; ?>">
								<label class="scUpperCase"><input type="checkbox" name="b_save" value="1"<?php echo $bSaveChecked ? ' checked="checked"' : NULL; ?> /> <?php __('save_to_address_book'); ?></label>
							</p>
						</div>
						
						<?php if ($controller->pjActionShowShipping()) : ?>
						<div class="scPaperHeading scPaperHeadingTop"><?php __('order_shipping_details'); ?></div>
						<div class="scPaperContent">
							<p class="scPaperChain">
								<label class="scUpperCase">
									<input type="checkbox" name="same_as" value="1"<?php echo isset($STORAGE['same_as']) ? ' checked="checked"' : NULL; ?>" class="scSelectorSameAs" />
									<?php __('order_same'); ?>
								</label>
							</p>
							<div class="scSelectorBoxShipping" style="clear: left; display: <?php echo isset($STORAGE['same_as']) ? 'none' : NULL; ?>">
								<?php
								if ($isLoged && isset($tpl['address_arr']) && !empty($tpl['address_arr']))
								{
									?>
									<p class="scPaperChain">
										<label class="scTitle"><?php __('order_address'); ?></label>
										<select name="s_address_id" class="scSelect scSelectorAddressId">
											<option value=""><?php __('choose_address'); ?></option>
											<?php
											foreach ($tpl['address_arr'] as $address)
											{
												$addr = array($address['city'], $address['zip'], $address['address_1']);
												?><option value="<?php echo $address['id']; ?>"<?php echo $address['id'] != @$shipping['s_address_id'] ? NULL : ' selected="selected"'; ?>><?php echo pjSanitize::html(join(", ", array_filter($addr))); ?></option><?php
											}
											?>
										</select>
									</p>
									<?php
								}
								?>
								<?php if (in_array((int) $tpl['option_arr']['o_bf_s_name'], array(2,3))) : ?>
								<p class="scPaperChain">
									<label class="scTitle"><?php __('client_name'); ?><?php if ((int) $tpl['option_arr']['o_bf_s_name'] === 3) : ?> <span class="scRequired">*</span><?php endif; ?></label>
									<input type="text" name="s_name" class="scText scSelectorOriginalS<?php echo (int) $tpl['option_arr']['o_bf_s_name'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$shipping['s_name']); ?>" placeholder="<?php __('front_placeholder_s_name', false, true); ?>" data-original="<?php echo pjSanitize::html(@$billing['s_name']); ?>" data-err="<?php echo $validate['name'];?>"/>
								</p>
								<?php endif; ?>
								<?php if (in_array((int) $tpl['option_arr']['o_bf_s_country_id'], array(2,3))) : ?>
								<p class="scPaperChain">
									<label class="scTitle"><?php __('client_country'); ?><?php if ((int) $tpl['option_arr']['o_bf_s_country_id'] === 3) : ?> <span class="scRequired">*</span><?php endif; ?></label>
									<select name="s_country_id" class="scSelect scSelectorOriginalS<?php echo (int) $tpl['option_arr']['o_bf_s_country_id'] === 3 ? ' required' : NULL; ?>" data-original="<?php echo pjSanitize::html(@$billing['s_country_id']); ?>" data-err="<?php echo $validate['country'];?>">
										<option value=""><?php __('choose_country'); ?></option>
										<?php
										foreach ($tpl['country_arr'] as $country)
										{
											?><option value="<?php echo $country['id']; ?>"<?php echo $country['id'] != @$shipping['s_country_id'] ? NULL : ' selected="selected"'; ?>><?php echo pjSanitize::html($country['name']); ?></option><?php
										}
										?>
									</select>
								</p>
								<?php endif; ?>
								<?php if (in_array((int) $tpl['option_arr']['o_bf_s_state'], array(2,3))) : ?>
								<p class="scPaperChain">
									<label class="scTitle"><?php __('client_state'); ?><?php if ((int) $tpl['option_arr']['o_bf_s_state'] === 3) : ?> <span class="scRequired">*</span><?php endif; ?></label>
									<input type="text" name="s_state" class="scText scSelectorOriginalS<?php echo (int) $tpl['option_arr']['o_bf_s_state'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$shipping['s_state']); ?>" placeholder="<?php __('front_placeholder_s_state', false, true); ?>" data-original="<?php echo pjSanitize::html(@$billing['s_state']); ?>" data-err="<?php echo $validate['state'];?>"/>
								</p>
								<?php endif; ?>
								<?php if (in_array((int) $tpl['option_arr']['o_bf_s_city'], array(2,3))) : ?>
								<p class="scPaperChain">
									<label class="scTitle"><?php __('client_city'); ?><?php if ((int) $tpl['option_arr']['o_bf_s_city'] === 3) : ?> <span class="scRequired">*</span><?php endif; ?></label>
									<input type="text" name="s_city" class="scText scSelectorOriginalS<?php echo (int) $tpl['option_arr']['o_bf_s_city'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$shipping['s_city']); ?>" placeholder="<?php __('front_placeholder_s_city', false, true); ?>" data-original="<?php echo pjSanitize::html(@$billing['s_city']); ?>" data-err="<?php echo $validate['city'];?>"/>
								</p>
								<?php endif; ?>
								<?php if (in_array((int) $tpl['option_arr']['o_bf_s_zip'], array(2,3))) : ?>
								<p class="scPaperChain">
									<label class="scTitle"><?php __('client_zip'); ?><?php if ((int) $tpl['option_arr']['o_bf_s_zip'] === 3) : ?> <span class="scRequired">*</span><?php endif; ?></label>
									<input type="text" name="s_zip" class="scText scSelectorOriginalS<?php echo (int) $tpl['option_arr']['o_bf_s_zip'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$shipping['s_zip']); ?>" placeholder="<?php __('front_placeholder_s_zip', false, true); ?>" data-original="<?php echo pjSanitize::html(@$billing['s_zip']); ?>" data-err="<?php echo $validate['zip'];?>"/>
								</p>
								<?php endif; ?>
								<?php if (in_array((int) $tpl['option_arr']['o_bf_s_address_1'], array(2,3))) : ?>
								<p class="scPaperChain">
									<label class="scTitle"><?php __('client_address_1'); ?><?php if ((int) $tpl['option_arr']['o_bf_s_address_1'] === 3) : ?> <span class="scRequired">*</span><?php endif; ?></label>
									<input type="text" name="s_address_1" class="scText scSelectorOriginalS<?php echo (int) $tpl['option_arr']['o_bf_s_address_1'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$shipping['s_address_1']); ?>" placeholder="<?php __('front_placeholder_s_address_1', false, true); ?>" data-original="<?php echo pjSanitize::html(@$billing['s_address_1']); ?>" data-err="<?php echo $validate['address_1'];?>"/>
								</p>
								<?php endif; ?>
								<?php if (in_array((int) $tpl['option_arr']['o_bf_s_address_2'], array(2,3))) : ?>
								<p class="scPaperChain">
									<label class="scTitle"><?php __('client_address_2'); ?><?php if ((int) $tpl['option_arr']['o_bf_s_address_2'] === 3) : ?> <span class="scRequired">*</span><?php endif; ?></label>
									<input type="text" name="s_address_2" class="scText scSelectorOriginalS<?php echo (int) $tpl['option_arr']['o_bf_s_address_2'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$shipping['s_address_2']); ?>" placeholder="<?php __('front_placeholder_s_address_2', false, true); ?>" data-original="<?php echo pjSanitize::html(@$billing['s_address_2']); ?>" data-err="<?php echo $validate['address_2'];?>"/>
								</p>
								<?php endif; ?>
								<p class="scPaperChain scSelectorSaveS" style="display: <?php echo $sSaveReady ? NULL : 'none'; ?>">
									<label class="scUpperCase"><input type="checkbox" name="s_save" value="1"<?php echo $sSaveChecked ? ' checked="checked"' : NULL; ?> /> <?php __('save_to_address_book'); ?></label>
								</p>
							</div>
						</div>
						<?php endif; ?>
						
						<?php
						if (!pjUtil::isOptionEnumYes($tpl['option_arr'], 'o_disable_payments'))
						{
							?>
							<div class="scPaperHeading scPaperHeadingTop"><?php __('order_payment_details'); ?></div>
							<div class="scPaperContent">
								<p class="scPaperChain">
									<label class="scTitle"><?php __('bf_payment'); ?> <span class="scRequired">*</span></label>
									<select name="payment_method" class="scSelect required" data-err="<?php echo $validate['payment'];?>">
										<option value=""><?php __('choose_payment'); ?></option>
										<?php
										foreach (__('payment_methods', true) as $k => $v)
										{
											if ((int) $tpl['option_arr']['o_allow_'.$k] !== 1)
											{
												continue;
											}
											?><option value="<?php echo $k; ?>"<?php echo @$STORAGE['payment_method'] != $k ? NULL : ' selected="selected"'; ?>><?php echo $v; ?></option><?php
										}
										?>
									</select>
								</p>
								
								<p class="scPaperChain scCcWrap" style="display: <?php echo @$STORAGE['payment_method'] != 'creditcard' ? 'none' : NULL; ?>">
									<label class="scTitle"><?php __('bf_cc_type'); ?> <span class="scRequired">*</span></label>
									<select name="cc_type" class="scSelect required">
										<option value="">---</option>
										<?php
										foreach (__('cc_types', true) as $k => $v)
										{
											?><option value="<?php echo $k; ?>"<?php echo @$STORAGE['cc_type'] != $k ? NULL : ' selected="selected"'; ?>><?php echo $v; ?></option><?php
										}
										?>
									</select>
								</p>
								<p class="scPaperChain scCcWrap" style="display: <?php echo @$STORAGE['payment_method'] != 'creditcard' ? 'none' : NULL; ?>">
									<label class="scTitle"><?php __('bf_cc_num'); ?> <span class="scRequired">*</span></label>
									<input type="text" name="cc_num" class="scText required" value="<?php echo pjSanitize::html(@$STORAGE['cc_num']); ?>" placeholder="<?php __('front_placeholder_cc_number', false, true); ?>" />
								</p>
								<p class="scPaperChain scCcWrap" style="display: <?php echo @$STORAGE['payment_method'] != 'creditcard' ? 'none' : NULL; ?>">
									<label class="scTitle"><?php __('bf_cc_sec'); ?> <span class="scRequired">*</span></label>
									<input type="text" name="cc_code" class="scText required" value="<?php echo pjSanitize::html(@$STORAGE['cc_code']); ?>" placeholder="<?php __('front_placeholder_cc_code', false, true); ?>" />
								</p>
								<p class="scPaperChain scCcWrap" style="display: <?php echo @$STORAGE['payment_method'] != 'creditcard' ? 'none' : NULL; ?>">
									<label class="scTitle"><?php __('bf_cc_exp'); ?> <span class="scRequired">*</span></label>
									<?php
									$rand = rand(1, 99999);
									$time = pjTime::factory()
										->attr('name', 'cc_exp_month')
										->attr('id', 'cc_exp_month_' . $rand)
										->attr('class', 'scText scW150 required')
										->prop('format', 'F');
									if (isset($STORAGE['cc_exp_month']) && !is_null($STORAGE['cc_exp_month']))
									{
										$time->prop('selected', $STORAGE['cc_exp_month']);
									}
									echo $time->month();
									?>
									<?php
									$time = pjTime::factory()
										->attr('name', 'cc_exp_year')
										->attr('id', 'cc_exp_year_' . $rand)
										->attr('class', 'scText scW100 required')
										->prop('left', 0)
										->prop('right', 10);
									if (isset($STORAGE['cc_exp_year']) && !is_null($STORAGE['cc_exp_year']))
									{
										$time->prop('selected', $STORAGE['cc_exp_year']);
									}
									echo $time->year();
									?>
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
								<textarea name="notes" class="scTextarea<?php echo (int) $tpl['option_arr']['o_bf_notes'] === 3 ? ' required' : NULL; ?>" placeholder="<?php __('front_placeholder_notes', false, true); ?>" data-err="<?php echo $validate['notes'];?>"><?php echo pjSanitize::html(@$STORAGE['notes']); ?></textarea>
							</p>
							<?php endif; ?>
							<?php if (in_array((int) $tpl['option_arr']['o_bf_captcha'], array(3))) : ?>
							<p class="scPaperUnchained">
								<label class="scTitle"><?php __('bf_captcha'); ?><?php if ((int) $tpl['option_arr']['o_bf_captcha'] === 3) : ?> <span class="scRequired">*</span><?php endif; ?></label>
								<img src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&amp;action=pjActionCaptcha&amp;rand=<?php echo rand(1, 99999); ?><?php echo $controller->_get->check('session_id') && $controller->_get->toString('session_id') != '' ? '&session_id=' . pjSanitize::clean($controller->_get->toString('session_id')) : NULL;?>" alt="Captcha" style="vertical-align: middle" />
								<input type="text" name="captcha" class="scText scW100<?php echo (int) $tpl['option_arr']['o_bf_captcha'] === 3 ? ' required' : NULL; ?>" maxlength="6" data-err="<?php echo $validate['captcha'];?>" data-captcha="<?php echo $validate['captcha_wrong'];?>"/>
							</p>
							<?php endif; ?>
								<?php
								$terms_url_key  = 'terms_url_' . (int) $company_id;
								$terms_body_key = 'terms_body_' . (int) $company_id;
								?>

								<?php if (in_array((int) $tpl['option_arr']['o_bf_terms'], array(3))) : ?>
									<p class="scPaperUnchained" style="position: relative">
										<?php $rand = rand(1, 999999); ?>
										<input type="checkbox" 
											name="terms" 
											value="1" 
											class="<?php echo (int) $tpl['option_arr']['o_bf_terms'] === 3 ? ' required' : NULL; ?>" 
											style="margin: 0" 
											data-err="<?php echo $validate['terms'];?>"/>

										<label style="position: absolute; top: 0; left: 20px" class="scUpperCase">
											<?php
											if (
												isset($tpl['terms'][$terms_url_key]) &&
												!empty($tpl['terms'][$terms_url_key]) &&
												preg_match('/^http(s)?:\/\//i', $tpl['terms'][$terms_url_key])
											) {
												?><a href="<?php echo $tpl['terms'][$terms_url_key]; ?>" target="_blank"><?php __('bf_terms'); ?></a><?php
											} elseif (
												isset($tpl['terms'][$terms_body_key]) &&
												!empty($tpl['terms'][$terms_body_key])
											) {
												?><a href="#" class="scSelectorTerms" data-title="<?php __('front_terms_title', false, true); ?>"><?php __('bf_terms'); ?></a><?php
											} else {
												__('bf_terms');
											}
											?>
										</label>
									</p>

									<?php if (isset($tpl['terms'][$terms_body_key]) && !empty($tpl['terms'][$terms_body_key])): ?>
										<div class="scSelectorTermsBody" style="display: none">
											<?php echo nl2br(stripslashes($tpl['terms'][$terms_body_key])); ?>
										</div>
									<?php endif; ?>

								<?php endif; ?>

							<div class="scNotice scSelectorNoticeMsg" style="display: none"></div>
						</div>
					</div>
					<div class="scPaperControl">
						<div class="scPaperControlInner">
							<input type="button" value="<?php __('front_back', false, true); ?>" class="scButton scButtonLight scButtonLightPrev scSelectorButton scSelectorViewCart" />
							<button type="submit" class="scButton scButtonDark scButtonDarkNext scSelectorButton"><?php __('front_preview_order', false, true); ?></button>
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
				}
			}
		}
	}else{
		include PJ_VIEWS_PATH . 'pjFront/elements/layout_3/checkout.php';
	}
}
?>