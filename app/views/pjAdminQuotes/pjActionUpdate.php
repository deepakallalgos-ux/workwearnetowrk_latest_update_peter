<script type="text/javascript">
var myLabel = {
    choose: "Choose",
    product_choose: "Choose Product",
    uuid: "UUID",
    client: "Client",
    created: "Created",
    total: "Total",
    status: "Status",
    has_update: true,
    has_delete: true,
    has_delete_bulk: true,
    delete_selected: "Delete Selected",
    delete_confirmation: "Are you sure you want to delete?",
    exported: "Export",
    alert_del_stock_title: "Delete Stock",
    alert_del_stock_text: "Are you sure to delete this stock?",
    btn_delete: "Delete",
    btn_cancel: "Cancel",
    uuid_used: "UUID already used",
    currencysign: "$",
    currency: "USD",
    days: "Sunday_Monday_Tuesday_Wednesday_Thursday_Friday_Saturday",
    months: "Jan_Feb_Mar_Apr_May_Jun_Jul_Aug_Sep_Oct_Nov_Dec",
    qty: "Quantity"
};
</script>

<?php
$titles = __('error_titles', true);
$bodies = __('error_bodies', true);
?>
<div class="row wrapper bquote-bottom white-bg page-heading">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-10">
                <h2><?php __('infoUpdateQuoteTitle');?></h2>
            </div>
        </div><!-- /.row -->

        <p class="m-b-none"><i class="fa fa-info-circle"></i> <?php __('infoUpdateQuoteDesc');?></p>
    </div><!-- /.col-md-12 -->
</div>
<div class="row wrapper wrapper-content animated fadeInRight">
    <div class="col-lg-12">
    	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminQuotes&amp;action=pjActionUpdate" method="post" id="frmUpdateQuote">
			<input type="hidden" name="update_form" value="1" />
			<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']; ?>" />
			
			<div class="tabs-container tabs-reservations m-b-lg">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a class="tab-quote-details" href="#quote-details" aria-controls="quote-details" role="tab" data-toggle="tab"><?php __('quote_tab_quote'); ?></a></li>
                    <li role="presentation"><a class="tab-client-details" href="#client-details" aria-controls="client-details" role="tab" data-toggle="tab"><?php __('quote_tab_client'); ?></a></li>
                    <li role="presentation"><a class="tab-shipping-details" href="#shipping-details" aria-controls="shipping-details" role="tab" data-toggle="tab"><?php __('quote_tab_shipping'); ?></a></li>
                </ul>
    
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="quote-details">
                        <div class="panel-body">
                        	<div class="alert alert-success"><strong><?php echo @$titles['AQ10']; ?></strong> <?php echo @$bodies['AQ10'];?></div>
                        	<div class="row">
                        		<div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label"><?php __('quote_uuid'); ?></label>
    
                                        <input type="text" name="uuid" id="uuid" class="form-control required" value="<?php echo pjSanitize::html($tpl['arr']['uuid']); ?>" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>" />
                                    </div>
                                </div><!-- /.col-md-3 -->
                        		<div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label"><?php __('quote_created'); ?></label>
    
                                        <p class="form-control-static"><?php echo date($tpl['option_arr']['o_date_format'] . ', ' . $tpl['option_arr']['o_time_format'], strtotime($tpl['arr']['created'])); ?></p>
                                    </div>
                                </div><!-- /.col-md-3 -->
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">&nbsp;</label>
    
                                        <p class="form-control-static"><a href="javascript:void(0);" id="btnEmailQuote" data-id="<?php echo $tpl['arr']['id'];?>" class="btn btn-primary btn-md btn-block btn-outline"><i class="fa fa-bell-o"></i> <?php __('quote_send_confirm'); ?></a></p>
                                    </div>
                                </div><!-- /.col-md-3 -->
                                
                        	</div>
                            <div class="row">                                
                                <div class="col-lg-3 col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label"><?php __('quote_status'); ?></label>
    
                                        <select name="status" id="status" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                                            <?php
    										foreach (__('quote_statuses', true, false) as $k => $v)
    										{
    										    ?><option value="<?php echo $k; ?>"<?php echo $tpl['arr']['status'] == $k ? ' selected="selected"' : NULL;?>><?php echo stripslashes($v); ?></option><?php
    										}
    										?>
                                        </select>
                                    </div>
                                </div><!-- /.col-md-3 -->
                                
                                <?php
								$plugins_payment_methods = pjObject::getPlugin('pjPayments') !== NULL? pjPayments::getPaymentMethods(): array();
								$haveOnline = $haveOffline = false;
								foreach ($tpl['payment_titles'] as $k => $v)
								{
								    if( $k != 'cash' && $k != 'bank' )
								    {
								        if( (int) $tpl['payment_option_arr'][$k]['is_active'] == 1)
								        {
								            $haveOnline = true;
								            break;
								        }
								    }
								}
								foreach ($tpl['payment_titles'] as $k => $v)
								{
								    if( $k == 'cash' || $k == 'bank' )
								    {
								        if( (int) $tpl['payment_option_arr'][$k]['is_active'] == 1)
								        {
								            $haveOffline = true;
								            break;
								        }
								    }
								}
								?>
                                <div class="col-lg-3 col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label"><?php __('quote_payment');?></label>
    
                                        <select name="payment_method" id="payment_method" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
    										<option value="">-- <?php __('plugin_base_choose'); ?> --</option>
                                            <?php
												if ($haveOnline && $haveOffline)
												{
												    ?><optgroup label="<?php __('script_online_payment_gateway', false, true); ?>"><?php
		                                        }
		                                        foreach ($tpl['payment_titles'] as $k => $v)
		                                        {
		                                            if($k == 'cash' || $k == 'bank' ){
		                                                continue;
		                                            }
		                                            if (array_key_exists($k, $plugins_payment_methods))
		                                            {
		                                                if(!isset($tpl['payment_option_arr'][$k]['is_active']) || (isset($tpl['payment_option_arr']) && $tpl['payment_option_arr'][$k]['is_active'] == 0) )
		                                                {
		                                                    continue;
		                                                }
		                                            }
		                                            ?><option value="<?php echo $k; ?>"<?php echo $tpl['arr']['payment_method']==$k ? ' selected="selected"' : NULL;?>><?php echo $v; ?></option><?php
		                                        }
		                                        if ($haveOnline && $haveOffline)
		                                        {
		                                            ?>
		                                        	</optgroup>
		                                        	<optgroup label="<?php __('script_offline_payment', false, true); ?>">
		                                        	<?php 
		                                        }
		                                        foreach ($tpl['payment_titles'] as $k => $v)
		                                        {
		                                            if( $k == 'cash' || $k == 'bank' )
		                                            {
		                                                if( (int) $tpl['payment_option_arr'][$k]['is_active'] == 1)
		                                                {
		                                                    ?><option value="<?php echo $k; ?>"<?php echo $tpl['arr']['payment_method']==$k ? ' selected="selected"' : NULL;?>><?php echo $v; ?></option><?php
		                                                }
		                                            }
		                                        }
		                                        if ($haveOnline && $haveOffline)
		                                        {
		                                            ?></optgroup><?php
		                                        }
												?>
    									</select>
                                    </div>
                                </div><!-- /.col-md-3 -->
    
    							<div class="col-lg-3 col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label"><?php __('quote_voucher'); ?></label>
    
                                        <input type="text" name="voucher" id="voucher" class="form-control" value="<?php echo pjSanitize::html($tpl['arr']['voucher']); ?>" />
                                    </div>
                                </div><!-- /.col-md-3 -->
                                <div class="col-lg-3 col-md-3 col-sm-6">
                                	<div class="form-group">
                                        <label class="control-label"><?php __('quote_shipping_location'); ?></label>
    
                                        <select name="tax_id" class="form-control">
											<option value=""><?php __('quote_choose'); ?></option>
											<?php
											foreach ($tpl['tax_arr'] as $item)
											{
												?><option value="<?php echo $item['id']; ?>"<?php echo $tpl['arr']['tax_id'] != $item['id'] ? NULL : ' selected="selected"'; ?>><?php echo pjSanitize::html($item['location']); ?></option><?php
											}
											?>
										</select>
                                    </div>
                                </div><!-- /.col-md-3 -->
                                
    						</div>
    						<div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <div class="row">
                                    	<div class="col-sm-6 col-xs-12">
                                    		<div class="form-group">
		                                        <label class="control-label"><?php __('quote_price'); ?></label>
		    
		                                        <div class="input-group">
													<input class="form-control number" type="text" name="price" id="price" value="<?php echo $tpl['arr']['price'];?>" data-msg-number="<?php __('pj_field_number');?>">
													<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']);?></span>
												</div>
		                                    </div>
                                    	</div>
                                    	<div class="col-sm-6 col-xs-12">
                                    		<div class="form-group">
		                                        <label class="control-label"><?php __('quote_discount'); ?></label>
		    
		                                        <div class="input-group">
													<input class="form-control number" type="text" name="discount" id="discount" value="<?php echo $tpl['arr']['discount'];?>" data-msg-number="<?php __('pj_field_number');?>">
													<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']);?></span>
												</div>
		                                    </div>
                                    	</div>
                                    </div>
                                    <div class="row">
                                    	<div class="col-sm-6 col-xs-12">
                                    		<div class="form-group">
		                                        <label class="control-label"><?php __('quote_insurance'); ?></label>
		    
		                                        <div class="input-group">
													<input class="form-control number" type="text" name="insurance" id="insurance" value="<?php echo $tpl['arr']['insurance'];?>" data-msg-number="<?php __('pj_field_number');?>">
													<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']);?></span>
												</div>
		                                    </div>
                                    	</div>
                                    	<div class="col-sm-6 col-xs-12">
                                    		<div class="form-group">
		                                        <label class="control-label"><?php __('quote_shipping'); ?></label>
		    
		                                        <div class="input-group">
													<input class="form-control number" type="text" name="shipping" id="shipping" value="<?php echo $tpl['arr']['shipping'];?>" data-msg-number="<?php __('pj_field_number');?>">
													<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']);?></span>
												</div>
		                                    </div>
                                    	</div>
                                    </div>
                                    <div class="row">
                                    	<div class="col-sm-6 col-xs-12">
                                    		<div class="form-group">
		                                        <label class="control-label"><?php __('quote_tax'); ?></label>
		    
		                                        <div class="input-group">
													<input class="form-control number" type="text" name="tax" id="tax" value="<?php echo $tpl['arr']['tax'];?>" data-msg-number="<?php __('pj_field_number');?>">
													<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']);?></span>
												</div>
		                                    </div>
                                    	</div>
                                    	<div class="col-sm-6 col-xs-12">
                                    		<div class="form-group">
		                                        <label class="control-label"><?php __('quote_total'); ?></label>
		    
		                                        <div class="input-group">
													<input class="form-control number" type="text" name="total" id="total" value="<?php echo $tpl['arr']['total'];?>" data-msg-number="<?php __('pj_field_number');?>">
													<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']);?></span>
												</div>
		                                    </div>
                                    	</div>
                                    </div>
                                </div><!-- /.col-md-3 -->
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="control-label"><?php __('quote_notes'); ?></label>
            							<textarea name="notes" id="notes" rows="7" class="form-control" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>"><?php echo stripslashes($tpl['arr']['notes']); ?></textarea>
                                    </div>
                                </div><!-- /.col-md-3 -->
   
                            </div><!-- /.row -->
                            
                            <div class="hr-line-dashed"></div>
        
                            <div class="form-group ibox-content">
                            	<div class="sk-spinner sk-spinner-double-bounce"><div class="sk-double-bounce1"></div><div class="sk-double-bounce2"></div></div>
                            	<div id="boxStockProducts"></div>
                            </div>
    						<!-- <div class="m-b-md">
                                <a href="javascript:void(0);" class="btn btn-primary btn-outline m-t-xs stock-add" data-id="<?php echo $tpl['arr']['id'];?>"><i class="fa fa-plus"></i> <?php __('btnAddProduct');?></a>
                                <a href="javascript:void(0);" class="btn btn-primary btn-outline m-t-xs quote-calc"><?php __('btnRecalcualteThePrice');?></a>
                            </div> -->
                            <div class="hr-line-dashed"></div>
    
                            <div class="clearfix">
                                <button type="submit" class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader pull-left" data-style="zoom-in" style="margin-right: 15px;">
                                    <span class="ladda-label"><?php __('btnSave'); ?></span>
                                    <?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
                                </button>
                                <a class="btn btn-white btn-lg pull-right" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminQuotes&action=pjActionIndex"><?php __('btnCancel'); ?></a>
                            </div><!-- /.clearfix -->
                        </div>
                    </div>
    
                    <div role="tabpanel" class="tab-pane" id="client-details">
                        <div class="panel-body">
                        	<div class="alert alert-success"><strong><?php echo @$titles['AQ11']; ?></strong> <?php echo @$bodies['AQ11'];?></div>
                            <h3><?php __('quote_customer'); ?></h3>
                            <div class="form-group">
								<label class="control-label"><?php __('quote_client'); ?></label>
								<div class="row">
									<div class="col-md-10">
										<select name="client_id" id="client_id" class="form-control select-item" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
											<option value=""><?php __('quote_choose'); ?></option>
											<?php
											foreach ($tpl['client_arr'] as $client)
											{
												?><option value="<?php echo $client['id']; ?>"<?php echo $client['id'] == $tpl['arr']['client_id'] ? ' selected="selected"' : NULL; ?>><?php echo pjSanitize::html($client['client_name']); ?></option><?php
											}
											?>
										</select>
									</div>
									<!-- <?php if (pjAuth::factory('pjAdminClients', 'pjActionUpdate')->hasAccess()) { ?>
										<div class="col-md-2">
											<a id="pjScEditClient" class="btn btn-primary btn-outline btn-sm m-l-xs" href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminClients&action=pjActionUpdate&id=<?php echo $tpl['arr']['client_id']; ?>" target="blank" data-href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminClients&amp;action=pjActionUpdate&id={ID}" style="display:inline-block;"><i class="fa fa-pencil"></i></a>
										</div>
									<?php } ?> -->
							   </div>
							</div>
							<div class="form-group">
								<div id="boxClient">
								<?php
								if ($tpl['arr']['client_id'] > 0)
								{
									?>
									<p>
										<label><?php __('quote_email'); ?>:</label>
										<span><?php echo pjSanitize::html($tpl['arr']['client_email']); ?></span>
									</p>
									<p>
										<label><?php __('quote_phone'); ?>:</label>
										<span><?php echo pjSanitize::html($tpl['arr']['client_phone']); ?></span>
									</p>
									<p>
										<label><?php __('quote_url'); ?>:</label>
										<span><?php echo pjSanitize::html($tpl['arr']['client_url']); ?></span>
									</p>
									<?php
								}
								?>
								</div>
							</div>
							 <div class="hr-line-dashed"></div> 
							 
							<h3><?php __('quote_all_list'); ?></h3>
							<div class="ibox-content">
								<div id="grid_client_quotes"></div>
							</div>
							
                            <div class="hr-line-dashed"></div>    						
                            <div class="clearfix">
                                <button type="submit" class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader pull-left" data-style="zoom-in" style="margin-right: 15px;">
                                    <span class="ladda-label"><?php __('btnSave'); ?></span>
                                    <?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
                                </button>
                                <a class="btn btn-white btn-lg pull-right" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminQuotes&action=pjActionIndex"><?php __('btnCancel'); ?></a>
                            </div><!-- /.clearfix -->
                        </div>                        
                    </div>
                    
                    <div role="tabpanel" class="tab-pane" id="shipping-details">
						<div class="panel-body">
							<div class="alert alert-success"><strong><?php echo @$titles['AQ12']; ?></strong> <?php echo @$bodies['AQ12'];?></div>
                            <h3><?php __('client_address_book'); ?></h3>
                            <div id="boxAddressBook">
							<?php
							if ($tpl['arr']['client_id'] > 0)
							{
								?>
								<div class="row form-group">
									<?php
									if(count($tpl['address_arr']))
									{ 
										?>
										<div class="col-sm-5 col-xs-12">
											<label><?php __('quote_address'); ?>:</label>
											<select name="address_id" id="address_id" class="form-control">
												<option value=""><?php __('quote_choose'); ?></option>
												<?php
												$disabled = ' disabled="disabled"';
												foreach ($tpl['address_arr'] as $address)
												{
													$selected = NULL;
													if ($address['id'] == $tpl['arr']['address_id'])
													{
														$selected = ' selected="selected"';
														$disabled = NULL;
													}
													?><option value="<?php echo $address['id']; ?>"<?php echo $selected; ?>><?php echo pjSanitize::html($address['name']); ?></option><?php
												}
												?>
											</select>
										</div>
										<div class="col-sm-7 col-xs-12">
											<label class="block">&nbsp;</label>
											<input type="button" value="<?php __('quote_copy_b'); ?>" class="btn btn-primary btn-outline btnCopy btnCopyBilling"<?php echo $disabled; ?> />
											<input type="button" value="<?php __('quote_copy_s'); ?>" class="btn btn-primary btn-outline btnCopy btnCopyShipping"<?php echo $disabled; ?> />
										</div>
										<?php
									}else{
										$no_address_book = __('lblNoAddressBook', true);
										if (pjAuth::factory('pjAdminClients', 'pjActionIndex')->hasAccess()) {
											$no_address_book = str_replace("[STAG]", '<a href="'.$_SERVER['PHP_SELF'].'?controller=pjAdminClients&amp;action=pjActionIndex">', $no_address_book);
											$no_address_book = str_replace("[ETAG]", '</a>', $no_address_book);
										}
										?><div class="col-xs-12"><?php echo $no_address_book;?></div><?php
									} 
									?>
								</div>
								<div id="boxAddress">
								<?php
								foreach ($tpl['address_arr'] as $address)
								{
									if ($address['id'] == $tpl['arr']['address_id'])
									{
										$tpl['address_arr'] = $address;
										include dirname(__FILE__) . '/pjActionGetAddress.php';
										break;
									}
								}
								?>
								</div>
								<?php
							}
							?>
							</div>
							<div class="hr-line-dashed"></div> 
							<h3><?php __('quote_billing_details'); ?></h3>
							<div class="row">
								<div class="col-md-4 col-sm-6 form-group">
									<label><?php __('quote_country'); ?>:</label>
									<select name="b_country_id" id="b_country_id" class="form-control select-item">
										<option value=""><?php __('quote_choose'); ?></option>
										<?php
										foreach ($tpl['country_arr'] as $country)
										{
											?><option value="<?php echo $country['id']; ?>"<?php echo $country['id'] == $tpl['arr']['b_country_id'] ? ' selected="selected"' : NULL; ?>><?php echo pjSanitize::html($country['country_title']); ?></option><?php
										}
										?>
									</select>
								</div>
								<div class="col-md-4 col-sm-6 form-group">
									<label><?php __('quote_state'); ?>:</label>
									<input type="text" name="b_state" id="b_state" class="form-control" value="<?php echo pjSanitize::html($tpl['arr']['b_state']); ?>" />
								</div>
								<div class="col-md-4 col-sm-6 form-group">
									<label><?php __('quote_city'); ?>:</label>
									<input type="text" name="b_city" id="b_city" class="form-control" value="<?php echo pjSanitize::html($tpl['arr']['b_city']); ?>" />
								</div>
								<div class="col-md-4 col-sm-6 form-group">
									<label><?php __('quote_zip'); ?>:</label>
									<input type="text" name="b_zip" id="b_zip" class="form-control" value="<?php echo pjSanitize::html($tpl['arr']['b_zip']); ?>" />
								</div>
								<div class="col-md-4 col-sm-6 form-group">
									<label><?php __('quote_name'); ?>:</label>
									<input type="text" name="b_name" id="b_name" class="form-control" value="<?php echo pjSanitize::html($tpl['arr']['b_name']); ?>" />
								</div>
								<div class="col-md-4 col-sm-6 form-group">
									<label><?php __('quote_address_1'); ?>:</label>
									<input type="text" name="b_address_1" id="b_address_1" class="form-control" value="<?php echo pjSanitize::html($tpl['arr']['b_address_1']); ?>" />
								</div>
								<div class="col-md-4 col-sm-6 form-group">
									<label><?php __('quote_address_2'); ?>:</label>
									<input type="text" name="b_address_2" id="b_address_2" class="form-control" value="<?php echo pjSanitize::html($tpl['arr']['b_address_2']); ?>" />
								</div>
							</div>
							
							<div class="hr-line-dashed"></div> 
							<h3><?php __('quote_shipping_details'); ?></h3>
							<?php
							$isSame = false;
							if ((int) $tpl['arr']['same_as'] === 1)
							{
								$isSame = true;
							}
							?>
							<div class="form-group">
                                <label class="control-label"><?php __('quote_same') ?></label>
                                <div class="clearfix">
                                    <div class="switch onoffswitch-data onoffswitch-fa-change pull-left">
                                        <div class="onoffswitch">
                                            <input type="checkbox" class="onoffswitch-checkbox" name="same_as" id="same_as" value="1"<?php echo $isSame ? ' checked="checked"' : NULL; ?>>
                                            <label class="onoffswitch-label" for="same_as">
                                                <span class="onoffswitch-inner" data-on="<?php __('_yesno_ARRAY_T'); ?>" data-off="<?php __('_yesno_ARRAY_F'); ?>"></span>
                                                <span class="onoffswitch-switch"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div><!-- /.clearfix -->
                            </div><!-- /.form-group -->
                            
                            <div class="boxSame" style="display: <?php echo $isSame ? 'none' : NULL; ?>">
	                            <div class="row">
									<div class="col-md-4 col-sm-6 form-group">
										<label><?php __('quote_country'); ?>:</label>
										<select name="s_country_id" id="s_country_id" class="form-control select-item">
											<option value=""><?php __('quote_choose'); ?></option>
											<?php
											foreach ($tpl['country_arr'] as $country)
											{
												?><option value="<?php echo $country['id']; ?>"<?php echo $country['id'] == $tpl['arr']['s_country_id'] ? ' selected="selected"' : NULL; ?>><?php echo pjSanitize::html($country['country_title']); ?></option><?php
											}
											?>
										</select>
									</div>
									<div class="col-md-4 col-sm-6 form-group">
										<label><?php __('quote_state'); ?>:</label>
										<input type="text" name="s_state" id="s_state" class="form-control" value="<?php echo pjSanitize::html($tpl['arr']['s_state']); ?>" />
									</div>
									<div class="col-md-4 col-sm-6 form-group">
										<label><?php __('quote_city'); ?>:</label>
										<input type="text" name="s_city" id="s_city" class="form-control" value="<?php echo pjSanitize::html($tpl['arr']['s_city']); ?>" />
									</div>
									<div class="col-md-4 col-sm-6 form-group">
										<label><?php __('quote_zip'); ?>:</label>
										<input type="text" name="s_zip" id="s_zip" class="form-control" value="<?php echo pjSanitize::html($tpl['arr']['s_zip']); ?>" />
									</div>
									<div class="col-md-4 col-sm-6 form-group">
										<label><?php __('quote_name'); ?>:</label>
										<input type="text" name="s_name" id="s_name" class="form-control" value="<?php echo pjSanitize::html($tpl['arr']['s_name']); ?>" />
									</div>
									<div class="col-md-4 col-sm-6 form-group">
										<label><?php __('quote_address_1'); ?>:</label>
										<input type="text" name="s_address_1" id="s_address_1" class="form-control" value="<?php echo pjSanitize::html($tpl['arr']['s_address_1']); ?>" />
									</div>
									<div class="col-md-4 col-sm-6 form-group">
										<label><?php __('quote_address_2'); ?>:</label>
										<input type="text" name="s_address_2" id="s_address_2" class="form-control" value="<?php echo pjSanitize::html($tpl['arr']['s_address_2']); ?>" />
									</div>
								</div>                            
							</div>
							
							<div class="hr-line-dashed"></div>    						
                            <div class="clearfix">
                                <button type="submit" class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader pull-left" data-style="zoom-in" style="margin-right: 15px;">
                                    <span class="ladda-label"><?php __('btnSave'); ?></span>
                                    <?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
                                </button>
                                <a class="btn btn-white btn-lg pull-right" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminQuotes&action=pjActionIndex"><?php __('btnCancel'); ?></a>
                            </div><!-- /.clearfix -->
                            
						</div>
					</div>
                </div>
            </div>
        </form>
    </div>  
    
</div><!-- /.wrapper wrapper-content -->

<div class="modal fade" id="stockAddModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">
  	<div class="modal-dialog modal-lg" role="document">
	    <div class="modal-content">
		      <div class="modal-header">
		        	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        	<h4 class="modal-title"><?php __('quote_stock_add_title');?></h4>
		      </div>
		      <div id="stockAddContentWrapper" class="modal-body"></div>
		      <div class="modal-footer">
	        	<button type="button" class="btn btn-default" data-dismiss="modal"><?php __('btnCancel');?></button>
	        	<button id="btnStockAddConfirm" type="button" class="btn btn-primary"><?php __('btnAdd');?></button>
		      </div>
	    </div><!-- /.modal-content -->
  	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="stockEditModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">
  	<div class="modal-dialog modal-lg" role="document">
	    <div class="modal-content">
		      <div class="modal-header">
		        	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        	<h4 class="modal-title"><?php __('quote_stock_edit_title');?></h4>
		      </div>
		      <div id="stockEditContentWrapper" class="modal-body"></div>
		      <div class="modal-footer">
	        	<button type="button" class="btn btn-default" data-dismiss="modal"><?php __('btnCancel');?></button>
	        	<button id="btnStockEditConfirm" type="button" class="btn btn-primary"><?php __('btnUpdate');?></button>
		      </div>
	    </div><!-- /.modal-content -->
  	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="confirmEmailModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  	<div class="modal-dialog modal-lg" role="document">
	    <div class="modal-content">
		      <div class="modal-header">
		        	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        	<h4 class="modal-title"><?php __('quote_confirmation_email_title');?></h4>
		      </div>
		      <div id="confirmEmailContentWrapper" class="modal-body"></div>
		      <div class="modal-footer">
		        	<button type="button" class="btn btn-default" data-dismiss="modal"><?php __('btnCancel');?></button>
		        	<button id="btnSendEmailConfirm" type="button" class="btn btn-primary"><?php __('btnSend');?></button>
		      </div>
	    </div><!-- /.modal-content -->
  	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="paymentEmailModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  	<div class="modal-dialog modal-lg" role="document">
	    <div class="modal-content">
		      <div class="modal-header">
		        	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        	<h4 class="modal-title"><?php __('quote_payment_email_title');?></h4>
		      </div>
		      <div id="paymentEmailContentWrapper" class="modal-body"></div>
		      <div class="modal-footer">
		        	<button type="button" class="btn btn-default" data-dismiss="modal"><?php __('btnCancel');?></button>
		        	<button id="btnSendEmailPayment" type="button" class="btn btn-primary"><?php __('btnSend');?></button>
		      </div>
	    </div><!-- /.modal-content -->
  	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
var pjGrid = pjGrid || {};
var myLabel = myLabel || {};
myLabel.uuid_used = "<?php __('uuid_used'); ?>";
myLabel.uuid = "<?php __('quote_uuid'); ?>";
myLabel.client = "<?php __('quote_client'); ?>";
myLabel.created = "<?php __('quote_created'); ?>";
myLabel.status = "<?php __('quote_status'); ?>";
myLabel.total = "<?php __('quote_total'); ?>";
myLabel.statuses = <?php echo pjAppController::jsonEncode(__('quote_statuses', true)); ?>;
myLabel.exported = "<?php __('lblExport'); ?>";
myLabel.delete_selected = <?php x__encode('delete_selected'); ?>;
myLabel.delete_confirmation = <?php x__encode('delete_confirmation'); ?>;
myLabel.product_choose = "-- <?php __('quote_p_name'); ?> --";
myLabel.choose = "<?php __('quote_choose'); ?>";
myLabel.currency = "<?php echo $tpl['option_arr']['o_currency']; ?>";
myLabel.currencysign = "<?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']); ?>";
myLabel.alert_del_stock_title = <?php x__encode('quote_stock_delete_title'); ?>;
myLabel.alert_del_stock_text = <?php x__encode('quote_stock_delete_desc'); ?>;
myLabel.btn_delete = <?php x__encode('btnDelete'); ?>;
myLabel.btn_cancel = <?php x__encode('btnCancel'); ?>;
myLabel.has_delete = <?php echo (int) $tpl['has_delete']; ?>;
myLabel.has_delete_bulk = <?php echo (int) $tpl['has_delete_bulk']; ?>;
</script>