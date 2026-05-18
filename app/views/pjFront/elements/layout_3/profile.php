<div class="container-fluid">
	<h2 class="text-uppercase text-primary"><strong><?php __('front_profile'); ?></strong></h2><br>
	<div class="col-sm-4">
		<div class="row">
			<p><?php __('front_profile_note'); ?></p>
		</div>
	</div>
	<div class="col-sm-8">
		<form action="" method="post" class="scSelectorProfileForm">
			<input type="hidden" name="sc_profile" value="1" />
			
			<div class="alert scSelectorNoticeMsg" role="alert" style="display:none;"></div>
			
			<div class="panel panel-default">
				<div class="panel-heading"><strong class="text-uppercase"><?php __('client_general'); ?></strong></div>
				<div class="panel-body">
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group required">
						    	<label class="control-label"><?php __('client_email'); ?></label>
								<div class="input-group">
								  	<span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span></span>
								  	<input type="email" name="email" class="form-control" placeholder="<?php __('front_placeholder_email', false, true); ?>" value="<?php echo isset($tpl['arr']['email']) ? pjSanitize::html($tpl['arr']['email']) : NULL; ?>" data-err="<?php echo $validate['email'];?>" data-email="<?php echo $validate['email_invalid'];?>">
								</div>
					  		</div>
					  	</div>
					  	<div class="col-sm-6">
					  		<div class="form-group required">
						    	<label class="control-label"><?php __('client_password'); ?></label>
								<div class="input-group">
								  	<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
								  	<input type="password" name="password" class="form-control" placeholder="<?php __('front_placeholder_password', false, true); ?>" value="<?php echo isset($tpl['arr']['password']) ? pjSanitize::html($tpl['arr']['password']) : NULL; ?>" data-err="<?php echo $validate['password'];?>">
								  	<span class="input-group-addon"><span class="glyphicon glyphicon-eye-open"></span></span>
								</div>
					  		</div>
						</div>
					</div>
					<?php
					ob_start();
					$number_of_cols = 0;
					if (in_array((int) $tpl['option_arr']['o_bf_c_name'], array(2,3)))
					{
						?>
						<div class="col-sm-6">
							<div class="form-group<?php echo (int) $tpl['option_arr']['o_bf_c_name'] === 3 ? ' required' : null;?>">
					   			<label class="control-label"><?php __('client_name'); ?></label>
					    		<input name="client_name" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_c_name'] === 3 ? ' required' : NULL; ?>" placeholder="<?php __('front_placeholder_name', false, true); ?>" value="<?php echo isset($tpl['arr']['client_name']) ? pjSanitize::html($tpl['arr']['client_name']) : NULL; ?>" data-err="<?php echo $validate['name'];?>">
					  		</div>
						</div>
						<?php
						$number_of_cols++;
					}
					if (in_array((int) $tpl['option_arr']['o_bf_c_phone'], array(2,3)))
					{
						?>
						<div class="col-sm-6">
							<div class="form-group<?php echo (int) $tpl['option_arr']['o_bf_c_phone'] === 3 ? ' required' : null;?>">
					    		<label class="control-label"><?php __('client_phone'); ?></label>
								<div class="input-group">
							  		<span class="input-group-addon"><span class="glyphicon glyphicon-earphone"></span></span>
							  		<input name="phone" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_c_phone'] === 3 ? ' required' : NULL; ?>" placeholder="<?php __('front_placeholder_phone', false, true); ?>" value="<?php echo isset($tpl['arr']['phone']) ? pjSanitize::html($tpl['arr']['phone']) : NULL; ?>" data-err="<?php echo $validate['phone'];?>">
								</div>
					  		</div>
						</div>
						<?php
						$number_of_cols++;
					}
					if($number_of_cols == 2)
					{
						$ob_fields = ob_get_contents();
						ob_end_clean();
						?>
						<div class="row"><?php echo $ob_fields; ?></div>
						<?php
						ob_start();
						$number_of_cols = 0;
					}
					if (in_array((int) $tpl['option_arr']['o_bf_c_url'], array(2,3)))
					{
						?>
						<div class="col-sm-6">
							<div class="form-group<?php echo (int) $tpl['option_arr']['o_bf_c_url'] === 3 ? ' required' : null;?>">
					   			<label class="control-label"><?php __('client_url'); ?></label>
								<input name="url" class="form-control" placeholder="<?php __('front_placeholder_url', false, true); ?>" value="<?php echo isset($tpl['arr']['url']) ? pjSanitize::html($tpl['arr']['url']) : NULL; ?>" data-err="<?php echo $validate['url'];?>">
					  		</div>
						</div>
						<?php
						$number_of_cols++;
					}
					$ob_fields = ob_get_contents();
					ob_end_clean();
					?>
					<div class="row"><?php echo $ob_fields; ?></div>
					
					<button type="submit" class="btn btn-primary scSelectorButton"><?php __('front_save_changes', false, true); ?></button>
					
				</div><!-- panel-body -->
			</div><!-- panel-default -->
			<div class="panel panel-default">
				<div class="panel-heading"><strong class="text-uppercase"><?php __('client_address_book'); ?></strong></div>
				<div class="panel-body scSelectorAddresses">
					<?php
					foreach ($tpl['address_arr'] as $k => $address)
					{
						?>
						<div class="scAddressHolder">
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
							   			<label class="control-label"><?php __('client_name'); ?></label>
										<input name="name[<?php echo $address['id']; ?>]" class="form-control" value="<?php echo pjSanitize::html($address['name']); ?>">
							  		</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
							   			<label class="control-label"><?php __('client_country'); ?></label>
										<select name="country_id[<?php echo $address['id']; ?>]" class="form-control">
											<option value=""><?php __('client_choose'); ?></option>
											<?php
											foreach ($tpl['country_arr'] as $country)
											{
												?><option value="<?php echo $country['id']; ?>"<?php echo $country['id'] == $address['country_id'] ? ' selected="selected"' : NULL; ?>><?php echo pjSanitize::html($country['name']); ?></option><?php
											}
											?>
										</select>
							  		</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
							   			<label class="control-label"><?php __('client_state'); ?></label>
										<input name="state[<?php echo $address['id']; ?>]" class="form-control" value="<?php echo pjSanitize::html($address['state']); ?>">
							  		</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
							   			<label class="control-label"><?php __('client_city'); ?></label>
										<input name="city[<?php echo $address['id']; ?>]" class="form-control" value="<?php echo pjSanitize::html($address['city']); ?>">
							  		</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
							   			<label class="control-label"><?php __('client_zip'); ?></label>
										<input name="zip[<?php echo $address['id']; ?>]" class="form-control" value="<?php echo pjSanitize::html($address['zip']); ?>">
							  		</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
							   			<label class="control-label"><?php __('client_address_1'); ?></label>
										<input name="address_1[<?php echo $address['id']; ?>]" class="form-control" value="<?php echo pjSanitize::html($address['address_1']); ?>">
							  		</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
							   			<label class="control-label"><?php __('client_address_2'); ?></label>
										<input name="address_2[<?php echo $address['id']; ?>]" class="form-control" value="<?php echo pjSanitize::html($address['address_2']); ?>">
							  		</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label class="control-label">&nbsp;</label><br/>
										<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-default text-uppercase scSelectorDeleteAddress" data-id="<?php echo $address['id']; ?>" data-client_id="<?php echo $address['client_id']; ?>"><?php __('client_del_address'); ?></a>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-6">
									<div class="radio">
										<label>
											<input type="radio" name="is_default_shipping" value="<?php echo $address['id']; ?>"<?php echo (int) $address['is_default_shipping'] === 1 ? ' checked="checked"' : NULL; ?> /> <?php __('client_default_shipping'); ?>
										</label>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="radio">
										<label>
											<input type="radio" name="is_default_billing" value="<?php echo $address['id']; ?>"<?php echo (int) $address['is_default_billing'] === 1 ? ' checked="checked"' : NULL; ?> /> <?php __('client_default_billing'); ?>
										</label>
									</div>
								</div>
							</div>
						</div>
						<?php
					} 
					?>
				</div><!-- panel-body -->
			</div><!-- panel-default -->
			<div>
				<button type="submit" class="btn btn-primary scSelectorButton" ><?php __('front_save_changes', false, true); ?></button>
				<button class="btn btn-default scSelectorButton scSelectorAddAddress"><?php __('client_add_address', false, true); ?></button>
			</div>
		</form>
		<div class="scSelectorCloneAddress" style="display: none"><?php include PJ_VIEWS_PATH . 'pjFront/elements/layout_3/address_tpl.php';; ?></div>
	</div>
</div>