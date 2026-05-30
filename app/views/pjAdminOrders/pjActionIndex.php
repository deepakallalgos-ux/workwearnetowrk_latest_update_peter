<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-10">
                <h2><?php __('infoOrdersTitle');?></h2>
            </div>
        </div><!-- /.row -->

        <p class="m-b-none"><i class="fa fa-info-circle"></i> <?php __('infoOrdersDesc');?></p>
    </div><!-- /.col-md-12 -->
</div>
<div class="row wrapper wrapper-content animated fadeInRight">
    <div class="col-lg-12">
    	<?php
    	$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
		$jqDateFormat = pjUtil::momentJsDateFormat($tpl['option_arr']['o_date_format']);
		$months = __('months', true);
		ksort($months);
		$short_days = __('short_days', true);
    	$error_code = $controller->_get->toString('err');
    	if (!empty($error_code))
    	{
    	    $titles = __('error_titles', true);
    	    $bodies = __('error_bodies', true);
    	    switch (true)
    	    {
    	        case in_array($error_code, array('AOR01', 'AOR03', 'AOR05')):
    	            ?>
    				<div class="alert alert-success">
    					<i class="fa fa-check m-r-xs"></i>
    					<strong><?php echo @$titles[$error_code]; ?></strong>
    					<?php echo @$bodies[$error_code]?>
    				</div>
    				<?php
    				break;
                case in_array($error_code, array('AOR02', 'AOR04', 'AOR08')):
    				?>
    				<div class="alert alert-danger">
    					<i class="fa fa-exclamation-triangle m-r-xs"></i>
    					<strong><?php echo @$titles[$error_code]; ?></strong>
    					<?php echo @$bodies[$error_code]?>
    				</div>
    				<?php
    				break;
    		}
    	}
    	$statuses = __('order_statuses', true, false);
    	?>
        <div class="ibox float-e-margins">
            <div class="ibox-content">
                <div class="row m-b-md">
                    <form action="" method="get" class="form-horizontal frm-filter">
                         <div class="col-lg-5 col-md-5 col-sm-8">
                            <div class="input-group">
                                <input type="text" name="q" placeholder="<?php __('plugin_base_btn_search', false, true); ?>" class="form-control">
                                <div class="input-group-btn">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div><!-- /.col-md-3 -->
                        <div class="col-lg-3 col-md-3 col-sm-4">
							<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" class="btn btn-primary btn-outline btn-advance-search"><?php __('btnAdvancedSearch'); ?></a>
						</div><!-- /.col-md-2 -->
    
                        <div class="col-lg-4 col-md-4 text-right">
                        	<select name="status" id="filter_status" class="form-control">
                				<option value="">-- <?php __('lblAll'); ?> --</option>
                				<?php
                				foreach (__('order_statuses', true, false) as $k => $v)
                				{
                					?><option value="<?php echo $k; ?>"><?php echo stripslashes($v); ?></option><?php
                				}
                				?>
                			</select>
                        </div><!-- /.col-md-6 -->
                    </form>
                </div><!-- /.row -->

				<div id="collapseOne" class="collapse">
					<div class="m-b-lg">
						<ul class="agile-list no-padding">
							<li class="success-element b-r-sm">
								<div class="panel-body">
									<form action="" method="get" class="frm-filter-advanced">
										<div class="row">
											<div class="col-md-3 col-md-4 col-sm-6">
												<div class="form-group">
													<label class="control-label"><?php __('order_client'); ?></label>
													<input type="text" name="q" id="q" class="form-control" value="<?php echo $controller->_get->check('q') ? pjSanitize::html($controller->_get->toString('q')) : NULL; ?>" />
												</div>
											</div>
											
											<div class="col-md-3 col-md-4 col-sm-6">
												<div class="form-group">
													<label class="control-label"><?php __('order_products'); ?></label>
													<select name="product_id" id="product_id" class="form-control select-item">
														<option value="">-- <?php __('lblChoose'); ?> --</option>
														<?php
														foreach ($tpl['product_arr'] as $item)
														{
															?><option value="<?php echo $item['id']; ?>"<?php echo $controller->_get->check('product_id') && $controller->_get->toInt('product_id') == $item['id'] ? ' selected="selected"' : NULL; ?>><?php echo pjSanitize::html($item['name']); ?></option><?php
														}
														?>
													</select>
												</div>
											</div>

											<div class="col-md-3 col-md-4 col-sm-6">
												<div class="form-group">
													<label class="control-label"><?php __('order_status'); ?></label>
													<select name="status" id="status" class="form-control">
														<option value="">-- <?php __('lblChoose'); ?> --</option>
														<?php
														foreach (__('order_statuses', true) as $k => $v)
														{
															?><option value="<?php echo $k; ?>"<?php echo $controller->_get->check('status') && $controller->_get->toString('status') == $k ? ' selected="selected"' : NULL; ?>><?php echo pjSanitize::html($v); ?></option><?php
														}
														?>
													</select>
												</div>
											</div>
											
											<div class="col-md-3 col-md-4 col-sm-6">
												<div class="form-group">
													<label class="control-label"><?php __('order_payment'); ?></label>
													<?php
				                                    $plugins_payment_methods = pjObject::getPlugin('pjPayments') !== NULL? pjPayments::getPaymentMethods(): array();
					                                $haveOnline = $haveOffline = false;
					                                foreach ($tpl['payment_titles'] as $k => $v)
					                                {
					                                   	if($k == 'creditcard') continue;
					                                   	if (array_key_exists($k, $plugins_payment_methods))
					                                   	{
					                                   		if(!isset($tpl['payment_option_arr'][$k]['is_active']) || (isset($tpl['payment_option_arr']) && $tpl['payment_option_arr'][$k]['is_active'] == 0) )
					                                   		{
					                                   			continue;
					                                   		}
					                                   	}else if( (isset($tpl['option_arr']['o_allow_'.$k]) && $tpl['option_arr']['o_allow_'.$k] == '0') || $k == 'cash' || $k == 'bank' ){
					                                   		continue;
					                                   	}
					                                   	$haveOnline = true;
					                                   	break;
					                                }
					                                foreach ($tpl['payment_titles'] as $k => $v)
					                                {
					                                  	if($k == 'creditcard') continue;
					                                   	if( $k == 'cash' || $k == 'bank' )
					                                   	{
					                                   		if( (isset($tpl['option_arr']['o_allow_'.$k]) && $tpl['option_arr']['o_allow_'.$k] == '1'))
					                                   		{
					                                   			$haveOffline = true;
					                                   			break;
					                                   		}
					                                   	}
					                                }
					                                $payment_method = $controller->_get->check('payment_method') ? $controller->_get->toString('payment_method') : '';
					                                ?>
													<select name="payment_method" id="payment_method" class="form-control">
														<option value="">-- <?php __('plugin_base_choose'); ?> --</option>
			                                            <?php 
			                                            if ($haveOnline && $haveOffline)
			                                            {
				                                            ?><optgroup label="<?php __('script_online_payment_gateway', false, true); ?>"><?php 
			                                            }
			                                            ?>
			                                                <?php
			                                                foreach ($tpl['payment_titles'] as $k => $v)
			                                                {
			                                                    if($k == 'creditcard') continue;
			                                                    if (array_key_exists($k, $plugins_payment_methods))
			                                                    {
			                                                        if(!isset($tpl['payment_option_arr'][$k]['is_active']) || (isset($tpl['payment_option_arr']) && $tpl['payment_option_arr'][$k]['is_active'] == 0) )
			                                                        {
			                                                            continue;
			                                                        }
			                                                    }else if( (isset($tpl['option_arr']['o_allow_'.$k]) && $tpl['option_arr']['o_allow_'.$k] == '0') || $k == 'cash' || $k == 'bank' ){
			                                                        continue;
			                                                    }
			                                                    ?><option value="<?php echo $k; ?>"<?php echo $payment_method==$k ? ' selected="selected"' : NULL;?>><?php echo $v; ?></option><?php
			                                                }
			                                                ?>
			                                            <?php
			                                            if ($haveOnline && $haveOffline)
			                                            {
			                                            	?>
			                                            	</optgroup>
			                                            	<optgroup label="<?php __('script_offline_payment', false, true); ?>">
			                                            	<?php 
			                                            }
			                                            ?>
			                                                <?php
			                                                foreach ($tpl['payment_titles'] as $k => $v)
			                                                {
			                                                    if($k == 'creditcard') continue;
			                                                    if( $k == 'cash' || $k == 'bank' )
			                                                    {
			                                                        if( (isset($tpl['option_arr']['o_allow_'.$k]) && $tpl['option_arr']['o_allow_'.$k] == '1'))
			                                                        {
			                                                            ?><option value="<?php echo $k; ?>"<?php echo $payment_method==$k ? ' selected="selected"' : NULL;?>><?php echo $v; ?></option><?php
			                                                        }
			                                                    }
			                                                }
			                                                ?>
			                                            <?php
			                                            if ($haveOnline && $haveOffline)
			                                            {
			                                            	?></optgroup><?php 
			                                            }
			                                            ?>
													</select>
												</div>
											</div>
										</div>
										
										<div class="hr-line-dashed"></div>
										<div class="row">
											<div class="col-sm-6">
												<h3 class="m-b-md"><?php __('order_created'); ?></h3>
												<div class="row">
													<div class="col-sm-6">
														<div class="form-group">
															<label class="control-label"><?php __('lblFrom'); ?></label>
															
															<div class="input-group date"
																	 data-provide="datepicker"
																	 data-date-autoclose="true"
																	 data-date-format="<?php echo $jqDateFormat ?>"
																	 data-date-week-start="<?php echo (int) $tpl['option_arr']['o_week_start'] ?>">
																<input type="text" name="date_from" id="date_from" class="form-control" autocomplete="off">
																<span class="input-group-addon">
																	<span class="fa fa-calendar"></span>
																</span>
															</div>
														</div>
													</div>
													<div class="col-sm-6">
														<div class="form-group">
															<label class="control-label"><?php __('lblTo'); ?></label>
															
															<div class="input-group date"
																 data-provide="datepicker"
																 data-date-autoclose="true"
																 data-date-format="<?php echo $jqDateFormat ?>"
																 data-date-week-start="<?php echo (int) $tpl['option_arr']['o_week_start'] ?>">
																<input type="text" name="date_to" id="date_to" class="form-control" autocomplete="off">
																<span class="input-group-addon">
																	<span class="fa fa-calendar"></span>
																</span>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="col-sm-6">
												<h3 class="m-b-md"><?php __('order_total'); ?></h3>
												<div class="row">
													<div class="col-sm-6">
														<div class="form-group">
															<label class="control-label"><?php __('lblFrom'); ?></label>
															<div class="input-group">
																<input class="form-control text-right" type="text" name="total_from" id="total_from">
																<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']);?></span>
															</div>
														</div>
														<!-- /.form-group -->
													</div>
													<!-- /.col-md-4 -->
													<div class="col-sm-6">
														<div class="form-group">
															<label class="control-label"><?php __('lblTo'); ?></label>
															<div class="input-group">
																<input class="form-control text-right" type="text" name="total_to" id="total_to">
																<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']);?></span>
															</div>
														</div>
														<!-- /.form-group -->
													</div>
													<!-- /.col-md-4 -->
												</div>
											</div>
										</div>
										<div class="hr-line-dashed"></div>
										<button class="btn btn-primary" type="submit"><?php __('btnSearch'); ?></button>
										<button class="btn btn-primary btn-outline" type="reset"><?php __('btnCancel'); ?></button>
									</form>
								</div>
							</li>
						</ul>
					</div>
				</div>
					
                <div id="grid"></div>
            </div>
        </div>
    </div><!-- /.col-lg-12 -->
</div>
<script type="text/javascript">
var pjGrid = pjGrid || {};
pjGrid.queryString = "";
<?php
if ($controller->_get->check('client_id') && $controller->_get->toInt('client_id') > 0)
{
    ?>pjGrid.queryString += "&client_id=<?php echo $controller->_get->toInt('client_id'); ?>";<?php
}
if ($controller->_get->check('date_from') && $controller->_get->toString('date_from') != '')
{
    ?>pjGrid.queryString += "&date_from=<?php echo $controller->_get->toString('date_from'); ?>";<?php
}
if ($controller->_get->check('date_to') && $controller->_get->toString('date_to') != '')
{
    ?>pjGrid.queryString += "&date_to=<?php echo $controller->_get->toString('date_to'); ?>";<?php
}
if ($controller->_get->check('status') && $controller->_get->toString('status') != '')
{
    ?>pjGrid.queryString += "&status=<?php echo $controller->_get->toString('status'); ?>";<?php
}
?>
var myLabel = myLabel || {};
myLabel.uuid = <?php x__encode('order_uuid'); ?>;
myLabel.client = <?php x__encode('order_client'); ?>;
myLabel.created = <?php x__encode('order_created'); ?>;
myLabel.status = <?php x__encode('order_status'); ?>;
myLabel.total = <?php x__encode('order_total'); ?>;
myLabel.exported = <?php x__encode('lblExport'); ?>;
myLabel.delete_selected = <?php x__encode('delete_selected'); ?>;
myLabel.delete_confirmation = <?php x__encode('delete_confirmation'); ?>;
myLabel.choose = "-- <?php __('lblChoose'); ?> --";
myLabel.months = "<?php echo implode("_", $months);?>";
myLabel.days = "<?php echo implode("_", $short_days);?>";
myLabel.has_update = <?php echo (int) $tpl['has_update']; ?>;
myLabel.has_delete = <?php echo (int) $tpl['has_delete']; ?>;
myLabel.has_delete_bulk = <?php echo (int) $tpl['has_delete_bulk']; ?>;
myLabel.statuses = <?php echo pjAppController::jsonEncode(__('order_statuses', true)); ?>;
</script>