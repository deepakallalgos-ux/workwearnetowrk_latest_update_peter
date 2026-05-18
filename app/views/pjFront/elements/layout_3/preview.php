<div class="container-fluid pjScPreviewOrder">
	<h2 class="text-uppercase text-primary pjScPreviewTitle"><strong><?php __('front_preview_order'); ?></strong></h2><br>
	<?php
	if (isset($tpl['status']) && $tpl['status'] == 'OK')
	{
		$STORAGE = @$_SESSION[$controller->defaultForm];
		?>
		<form action="" method="post" class="scForm scSelectorPreviewForm">
			<input type="hidden" name="sc_preview" value="1" />
			
			<div class="col-sm-3">
				<?php include PJ_VIEWS_PATH . 'pjFront/elements/layout_3/price.php';?>
			</div>
			<div class="col-sm-9">
				<div class="row">
					<?php
					if (!$controller->isLoged())
					{
						?>
						<div class="panel panel-default">
							<div class="panel-heading"><strong><?php __('order_customer'); ?></strong></div>
							<div class="panel-body">
								<div class="row">
									<div class="col-sm-6">
									  	<label class="control-label"><?php __('client_email'); ?></label><br/>
									    <span class="text-muted"><?php echo pjSanitize::html(@$STORAGE['email']); ?></span>
									</div>
									<div class="col-sm-6">
									  	<div class="form-group">
									    	<label class="control-label"><?php __('client_password'); ?></label><br/>
									   		<span class="text-muted"><?php echo pjSanitize::html(@$STORAGE['password']); ?></span>
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
									  	<div class="form-group">
									    	<label class="control-label"><?php __('client_name'); ?></label><br/>
									    	<span class="text-muted"><?php echo pjSanitize::html(@$STORAGE['client_name']); ?></span>
									  	</div>
									</div>
									<?php
									$number_of_cols++;
								}
								if (in_array((int) $tpl['option_arr']['o_bf_c_phone'], array(2,3)))
								{
									?>
									<div class="col-sm-6">
									  	<div class="form-group">
									    	<label class="control-label"><?php __('client_phone'); ?></label><br/>
									    	<span class="text-muted"><?php echo pjSanitize::html(@$STORAGE['phone']); ?></span>
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
									  	<div class="form-group">
									    	<label class="control-label"><?php __('client_url'); ?></label><br/>
									    	<span class="text-muted"><?php echo pjSanitize::html(@$STORAGE['url']); ?></span>
									  	</div>
									</div>
									<?php
									$number_of_cols++;
								}
								$ob_fields = ob_get_contents();
								ob_end_clean();
								?>
								<div class="row"><?php echo $ob_fields; ?></div>
							</div>
						</div>
						<?php
					} 
					?>
					
					<?php
					$field_rows = array();
					ob_start();
					$number_of_cols = 0;
					if (in_array((int) $tpl['option_arr']['o_bf_b_name'], array(2,3)))
					{
						?>
						<div class="col-sm-6">
						  	<div class="form-group">
						    	<label class="control-label"><?php __('client_name'); ?></label><br/>
						    	<span class="text-muted"><?php echo pjSanitize::html(@$STORAGE['b_name']); ?></span>
						  	</div>
						</div>
						<?php
						$number_of_cols++;
					}
					if($number_of_cols == 2)
					{
						$ob_fields = ob_get_contents();
						ob_end_clean();
						$field_rows[] = $ob_fields;
						ob_start();
						$number_of_cols = 0;
					}
					if (in_array((int) $tpl['option_arr']['o_bf_b_country_id'], array(2,3)))
					{ 
						?>
						<div class="col-sm-6">
						  	<div class="form-group">
						    	<label class="control-label"><?php __('client_country'); ?></label><br/>
						    	<span class="text-muted">
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
						  	</div>
						</div>
						<?php
						$number_of_cols++;
					}
					if($number_of_cols == 2)
					{
						$ob_fields = ob_get_contents();
						ob_end_clean();
						$field_rows[] = $ob_fields;
						ob_start();
						$number_of_cols = 0;
					}
					if (in_array((int) $tpl['option_arr']['o_bf_b_state'], array(2,3)))
					{
						?>
						<div class="col-sm-6">
						  	<div class="form-group">
						    	<label class="control-label"><?php __('client_state'); ?></label><br/>
				   	 			<span class="text-muted"><?php echo pjSanitize::html(@$STORAGE['b_state']); ?></span>
						  	</div>
						</div>
						<?php
						$number_of_cols++;
					}
					if($number_of_cols == 2)
					{
						$ob_fields = ob_get_contents();
						ob_end_clean();
						$field_rows[] = $ob_fields;
						ob_start();
						$number_of_cols = 0;
					}
					
					if (in_array((int) $tpl['option_arr']['o_bf_b_city'], array(2,3)))
					{
						?>
						<div class="col-sm-6">
						  	<div class="form-group">
						    	<label class="control-label"><?php __('client_city'); ?></label><br/>
				   	 			<span class="text-muted"><?php echo pjSanitize::html(@$STORAGE['b_city']); ?></span>
						  	</div>
						</div>
						<?php
						$number_of_cols++;
					}
					if($number_of_cols == 2)
					{
						$ob_fields = ob_get_contents();
						ob_end_clean();
						$field_rows[] = $ob_fields;
						ob_start();
						$number_of_cols = 0;
					}
					if (in_array((int) $tpl['option_arr']['o_bf_b_zip'], array(2,3)))
					{
						?>
						<div class="col-sm-6">
						  	<div class="form-group">
						    	<label class="control-label"><?php __('client_zip'); ?></label><br/>
				   	 			<span class="text-muted"><?php echo pjSanitize::html(@$STORAGE['b_zip']); ?></span>
						  	</div>
						</div>
						<?php
						$number_of_cols++;
					} 
					if($number_of_cols == 2)
					{
						$ob_fields = ob_get_contents();
						ob_end_clean();
						$field_rows[] = $ob_fields;
						ob_start();
						$number_of_cols = 0;
					}
					if (in_array((int) $tpl['option_arr']['o_bf_b_address_1'], array(2,3)))
					{
						?>
						<div class="col-sm-6">
						  	<div class="form-group">
						    	<label class="control-label"><?php __('client_address_1'); ?></label><br/>
				   	 			<span class="text-muted"><?php echo pjSanitize::html(@$STORAGE['b_address_1']); ?></span>
						  	</div>
						</div>
						<?php
						$number_of_cols++;
					}
					if($number_of_cols == 2)
					{
						$ob_fields = ob_get_contents();
						ob_end_clean();
						$field_rows[] = $ob_fields;
						ob_start();
						$number_of_cols = 0;
					}
					if (in_array((int) $tpl['option_arr']['o_bf_b_address_2'], array(2,3)))
					{
						?>
						<div class="col-sm-6">
						  	<div class="form-group">
						    	<label class="control-label"><?php __('client_address_2'); ?></label><br/>
				   	 			<span class="text-muted"><?php echo pjSanitize::html(@$STORAGE['b_address_2']); ?></span>
						  	</div>
						</div>
						<?php
						$number_of_cols++;
					}
					$ob_fields = ob_get_contents();
					ob_end_clean();
					if(!empty($ob_fields))
					{
					    $field_rows[] = $ob_fields;
					}
					if(!empty($field_rows))
					{
					   ?>
					   <div class="panel panel-default">
							<div class="panel-heading"><strong><?php __('order_billing_details'); ?></strong></div>
							<div class="panel-body">
								<?php
								foreach($field_rows as $row)
								{
								    ?><div class="row"><?php echo $row;?></div><?php
								}
								?>
							</div>
						</div><!-- panel-default -->
					   <?php    
					}
					
					if ($controller->pjActionShowShipping())
					{
					    if (isset($STORAGE['same_as']) && !empty($field_rows))
						{
							?>
							<div class="panel panel-default">
    							<div class="panel-heading"><strong><?php __('order_billing_details'); ?></strong></div>
    							<div class="panel-body">
        							<div class="row">
        								<div class="col-sm-6">
        								  	<div class="form-group">
        						   	 			<span class="text-muted"><?php __('order_same'); ?></span>
        								  	</div>
        								</div>
        							</div>
        						</div>
        					</div>
							<?php
						}else{
						    $field_rows = array();
							ob_start();
							$number_of_cols = 0;
							if (in_array((int) $tpl['option_arr']['o_bf_s_name'], array(2,3)))
							{
								?>
								<div class="col-sm-6">
								  	<div class="form-group">
								    	<label class="control-label"><?php __('client_name'); ?></label><br/>
						   	 			<span class="text-muted"><?php echo pjSanitize::html(@$STORAGE['s_name']); ?></span>
								  	</div>
								</div>
								<?php
								$number_of_cols++;
							}
							if($number_of_cols == 2)
							{
								$ob_fields = ob_get_contents();
								ob_end_clean();
								$field_rows[] = $ob_fields;
								ob_start();
								$number_of_cols = 0;
							}
							if (in_array((int) $tpl['option_arr']['o_bf_s_country_id'], array(2,3)))
							{ 
								?>
								<div class="col-sm-6">
								  	<div class="form-group">
								    	<label class="control-label"><?php __('client_country'); ?></label><br/>
								    	<span class="text-muted">
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
								  	</div>
								</div>
								<?php
								$number_of_cols++;
							}
							if($number_of_cols == 2)
							{
								$ob_fields = ob_get_contents();
								ob_end_clean();
								$field_rows[] = $ob_fields;
								ob_start();
								$number_of_cols = 0;
							}
							if (in_array((int) $tpl['option_arr']['o_bf_s_state'], array(2,3)))
							{
								?>
								<div class="col-sm-6">
								  	<div class="form-group">
								    	<label class="control-label"><?php __('client_state'); ?></label><br/>
						   	 			<span class="text-muted"><?php echo pjSanitize::html(@$STORAGE['s_state']); ?></span>
								  	</div>
								</div>
								<?php
								$number_of_cols++;
							}
							if($number_of_cols == 2)
							{
								$ob_fields = ob_get_contents();
								ob_end_clean();
								$field_rows[] = $ob_fields;
								ob_start();
								$number_of_cols = 0;
							}
							if (in_array((int) $tpl['option_arr']['o_bf_s_city'], array(2,3)))
							{
								?>
								<div class="col-sm-6">
								  	<div class="form-group">
								    	<label class="control-label"><?php __('client_city'); ?></label><br/>
						   	 			<span class="text-muted"><?php echo pjSanitize::html(@$STORAGE['s_city']); ?></span>
								  	</div>
								</div>
								<?php
								$number_of_cols++;
							}
							if($number_of_cols == 2)
							{
								$ob_fields = ob_get_contents();
								ob_end_clean();
								$field_rows[] = $ob_fields;
								ob_start();
								$number_of_cols = 0;
							}
							if (in_array((int) $tpl['option_arr']['o_bf_s_zip'], array(2,3)))
							{
								?>
								<div class="col-sm-6">
								  	<div class="form-group">
								    	<label class="control-label"><?php __('client_zip'); ?></label><br/>
						   	 			<span class="text-muted"><?php echo pjSanitize::html(@$STORAGE['s_zip']); ?></span>
								  	</div>
								</div>
								<?php
								$number_of_cols++;
							} 
							if($number_of_cols == 2)
							{
								$ob_fields = ob_get_contents();
								ob_end_clean();
								$field_rows[] = $ob_fields;
								ob_start();
								$number_of_cols = 0;
							}
							if (in_array((int) $tpl['option_arr']['o_bf_s_address_1'], array(2,3)))
							{
								?>
								<div class="col-sm-6">
								  	<div class="form-group">
								    	<label class="control-label"><?php __('client_address_1'); ?></label><br/>
						   	 			<span class="text-muted"><?php echo pjSanitize::html(@$STORAGE['s_address_1']); ?></span>
								  	</div>
								</div>
								<?php
								$number_of_cols++;
							}
							if($number_of_cols == 2)
							{
								$ob_fields = ob_get_contents();
								ob_end_clean();
								$field_rows[] = $ob_fields;
								ob_start();
								$number_of_cols = 0;
							}
							if (in_array((int) $tpl['option_arr']['o_bf_s_address_2'], array(2,3)))
							{
								?>
								<div class="col-sm-6">
								  	<div class="form-group">
								    	<label class="control-label"><?php __('client_address_2'); ?></label><br/>
						   	 			<span class="text-muted"><?php echo pjSanitize::html(@$STORAGE['s_address_2']); ?></span>
								  	</div>
								</div>
								<?php
								$number_of_cols++;
							}
							$ob_fields = ob_get_contents();
							ob_end_clean();
							if(!empty($ob_fields))
							{
							    $field_rows[] = $ob_fields;
							}
							if(!empty($field_rows))
							{
							    ?>
        					   <div class="panel panel-default">
        							<div class="panel-heading"><strong><?php __('order_shipping_details'); ?></strong></div>
        							<div class="panel-body">
        								<?php
        								foreach($field_rows as $row)
        								{
        								    ?><div class="row"><?php echo $row;?></div><?php
        								}
        								?>
        							</div>
        						</div><!-- panel-default -->
        					   <?php    
        					}
						} 
					}
					if ((int) $tpl['option_arr']['o_disable_payments'] !== 1)
					{
						$payment_methods = $tpl['payment_titles'];
						?>
						<div class="panel panel-default">
							<div class="panel-heading"><strong><?php __('order_payment_details'); ?></strong></div>
							<div class="panel-body">
								<div class="row">
									<div class="col-sm-6">
									  	<div class="form-group">
									    	<label class="control-label"><?php __('bf_payment'); ?></label><br/>
									    	<span class="text-muted"><?php echo $payment_methods[$STORAGE['payment_method']]; ?></span>
									  	</div>
									</div>
									<div class="col-sm-6 scBankWrap" style="display: <?php echo @$STORAGE['payment_method'] != 'bank' ? 'none' : NULL; ?>">
										<div class="form-group">
									    	<label class="control-label"><?php __('bf_bank_account'); ?></label><br/>
									    	<span class="text-muted"><?php echo nl2br(pjSanitize::html($tpl['bank_account'])); ?></span>
									  	</div>
									</div>
								</div>
							</div>
						</div>
						<?php
					}
					?>
					<div class="panel panel-default">
						<div class="panel-heading"><strong><?php __('order_other_details'); ?></strong></div>
						<div class="panel-body">
							<?php
							if (in_array((int) $tpl['option_arr']['o_bf_notes'], array(2,3)))
							{
								?>
								<div class="row">
									<div class="col-sm-12">
										<div class="form-group">
									    	<label class="control-label"><?php __('bf_notes'); ?></label><br/>
									    	<div class="text-muted"><?php echo nl2br(pjSanitize::html(@$STORAGE['notes'])); ?></div>
									  	</div>
									</div>
								</div>
								<?php
							} 
							?>
						</div>
					</div>
					
					<div class="alert scSelectorNoticeMsg" role="alert" style="display:none;"></div>
					
					<div>
						<button type="button" class="btn btn-default scSelectorButton scSelectorEditOrder pjScBtnSecondary"><?php __('front_edit_order', false, true); ?></button>
						<button type="submit" class="btn btn-default scSelectorButton pjScBtnPrimary" ><?php __('front_confirm_procees', false, true); ?></button>
					</div>
					<br/>
					
				</div><!-- col-sm-9 -->
			</div>
		</form>
		<?php
	}elseif (isset($tpl['status']) && $tpl['status'] == 'ERR') {
		if (isset($tpl['code']))
		{
			switch ($tpl['code'])
			{
				case 100:
					?><div class="alert alert-warning" role="alert"><?php __('front_empty_shipping_location'); ?></div><?php
					break;
				case 101:
					?><div class="alert alert-warning" role="alert"><?php __('front_empty_shopping_cart'); ?></div><?php
					break;
				case 102:
					?><div class="alert alert-warning" role="alert">v<?php __('front_empty_checkout_form'); ?></div><?php
					break;
			}
		}
	} 
	?>
</div>