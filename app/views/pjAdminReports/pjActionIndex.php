<div class="row border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-10">
                <h2><?php __('infoReportTitle', false, true);?></h2>
            </div>
        </div><!-- /.row -->

        <p class="m-b-none"><i class="fa fa-info-circle"></i> <?php __('infoReportDesc', false, true);?></p>
    </div><!-- /.col-md-12 -->
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                	<form action="" method="get" id="frmReport" autocomplete="off">
                		<input type="hidden" name="controller" value="pjAdminReports" />
						<input type="hidden" name="action" value="pjActionIndex" />
                		<?php
                        $months = __('months', true);
                        ksort($months);
                        $short_days = __('short_days', true);
                        ?>
        				<div id="datePickerOptions" style="display:none;" data-wstart="<?php echo (int) $tpl['option_arr']['o_week_start']; ?>" data-format="<?php echo pjUtil::toBootstrapDate($tpl['option_arr']['o_date_format']); ?>" data-months="<?php echo implode("_", $months);?>" data-days="<?php echo implode("_", $short_days);?>"></div>
        				
                        <div class="row m-b-md">
                            <div class="col-md-4">
                                <label><?php __('lblDate'); ?></label>
    
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span> 
    
                                        <input type="text" name="date_from" id="date_from" value="<?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['date_from']));?>" class="form-control" readonly>
    
                                        <span class="input-group-addon"><?php __('lblTo'); ?></span>
    
                                        <input type="text" name="date_to" id="date_to" value="<?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['date_to']));?>" class="form-control" readonly>
                                    </div>
                                </div><!-- /.form-group -->
                            </div>
    						<div class="col-md-3">
    							<label>&nbsp;</label>
    							<div class="form-group m-b-md">
    								<button type="submit" class="btn btn-primary"><?php __('btnReport');?></button>
    							</div>
    						</div>
                        </div><!-- /.row -->
                    </form>

                    <div class="hr-line-dashed"></div>
                    
					<div class="row form-group">
					    <div class="col-lg-3 col-md-4 col-xs-6">
					        <div class="form-group">
					            <label class="control-label"><?php __('lblUpTotalOrders'); ?>:</label>
					
					            <div>
					            	<?php
									if($tpl['total_orders'] > 0 && pjAuth::factory('pjAdminOrders', 'pjActionIndex')->hasAccess())
									{ 
										?>
										<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOrders&action=pjActionIndex&status=completed&date_from=<?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['date_from']));?>&date_to=<?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['date_to']));?>"><?php echo $tpl['total_orders'];?></a>
										<?php
									}else{
										echo $tpl['total_orders'];
									} 
									?>
					            </div>
					        </div><!-- /.form-group -->
					    </div>
					    
					    <div class="col-lg-3 col-md-4 col-xs-6">
					        <div class="form-group">
					            <label class="control-label"><?php __('lblTotalAmount'); ?>:</label>
					
					            <div><?php echo pjCurrency::formatPrice($tpl['total_amount']);?></div>
					        </div><!-- /.form-group -->
					    </div>
					    
					    <div class="col-lg-3 col-md-4 col-xs-6">
					        <div class="form-group">
					            <label class="control-label"><?php __('lblUniqueClients'); ?>:</label>
					
					            <div>
					            	<?php
									if($tpl['unique_clients'] > 0 && pjAuth::factory('pjAdminClients', 'pjActionIndex')->hasAccess())
									{ 
										?>
										<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminClients&action=pjActionIndex&client_ids=<?php echo $tpl['unique_client_ids'];?>"><?php echo $tpl['unique_clients'];?></a>
										<?php
									}else{
										echo $tpl['unique_clients'];
									} 
									?>
					            </div>
					        </div><!-- /.form-group -->
					    </div>
					    
					    <div class="col-lg-3 col-md-4 col-xs-6">
					        <div class="form-group">
					            <label class="control-label"><?php __('lblFirstTimeClients'); ?>:</label>
					
					            <div>
					            	<?php
									if($tpl['first_time_clients'] > 0 && pjAuth::factory('pjAdminClients', 'pjActionIndex')->hasAccess())
									{ 
										?>
										<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminClients&action=pjActionIndex&client_ids=<?php echo $tpl['first_time_client_ids'];?>"><?php echo $tpl['first_time_clients'];?></a>
										<?php
									}else{
										echo $tpl['first_time_clients'];
									} 
									?>
					            </div>
					        </div><!-- /.form-group -->
					    </div>					    
					</div>
					
					<div class="table-responsive table-responsive-secondary">
					    <table class="table table-striped table-hover">
					        <thead>
					            <tr>
					                <th><?php __('lblProductsPrice');?></th>
									<th><?php __('order_discount');?></th>
									<th><?php __('order_insurance');?></th>
									<th><?php __('order_shipping');?></th>
									<th><?php __('order_tax');?></th>
					            </tr>
					        </thead>					
					        <tbody>
					            <tr>
									<td><?php echo pjCurrency::formatPrice($tpl['sub_arr']['price']);?></td>
									<td><?php echo pjCurrency::formatPrice($tpl['sub_arr']['discount']);?></td>
									<td><?php echo pjCurrency::formatPrice($tpl['sub_arr']['insurance']);?></td>
									<td><?php echo pjCurrency::formatPrice($tpl['sub_arr']['shipping']);?></td>
									<td><?php echo pjCurrency::formatPrice($tpl['sub_arr']['tax']);?></td>
								</tr>
					        </tbody>
					    </table>
					</div>
					<div class="hr-line-dashed"></div>
					<div class="row form-group">
						<div class="col-md-4 col-xs-12">
					        <div class="form-group">
					            <label class="control-label"><?php __('lblAvgOrderAmount'); ?>:</label>
					
					            <div><?php echo pjCurrency::formatPrice($tpl['avg_amount']);?></div>
					        </div><!-- /.form-group -->
					    </div>
					    <div class="col-md-4 col-xs-12">
					        <div class="form-group">
					            <label class="control-label"><?php __('lblMinOrderAmount'); ?>:</label>
					
					            <div><?php echo pjCurrency::formatPrice($tpl['min_amount']);?></div>
					        </div><!-- /.form-group -->
					    </div>
					    <div class="col-md-4 col-xs-12">
					        <div class="form-group">
					            <label class="control-label"><?php __('lblMaxOrderAmount'); ?>:</label>
					
					            <div><?php echo pjCurrency::formatPrice($tpl['max_amount']);?></div>
					        </div><!-- /.form-group -->
					    </div>
					</div>
					
					<div class="row form-group">
						<div class="col-md-3 col-sm-6 col-xs-12">
					        <div class="form-group">
					            <label class="control-label"><?php __('lblAverageProductsPerOrder'); ?>:</label>
					
					            <div><?php echo number_format($tpl['avg_product'], 2, '.', ' ');?></div>
					        </div><!-- /.form-group -->
					    </div>
					    <div class="col-md-3 col-sm-6 col-xs-12">
					        <div class="form-group">
					            <label class="control-label"><?php __('lblMinProductsPerOrder'); ?>:</label>
					
					            <div><?php echo $tpl['min_product'];?></div>
					        </div><!-- /.form-group -->
					    </div>
					    <div class="col-md-3 col-sm-6 col-xs-12">
					        <div class="form-group">
					            <label class="control-label"><?php __('lblMaxProductsPerOrder'); ?>:</label>
					
					            <div><?php echo $tpl['max_product'];?></div>
					        </div><!-- /.form-group -->
					    </div>
					    <div class="col-md-3 col-sm-6 col-xs-12">
					        <div class="form-group">
					            <label class="control-label"><?php __('lblMostPopularProduct'); ?>:</label>
					
					            <div>
					            	<?php
									if(!empty($tpl['popular_arr']['name']))
									{ 
										if (pjAuth::factory('pjAdminProducts', 'pjActionUpdate')->hasAccess()) {
											?>
											<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProducts&action=pjActionUpdate&id=<?php echo $tpl['popular_arr']['id'];?>"><?php echo pjSanitize::html($tpl['popular_arr']['name']);?></a> / <?php echo str_replace("{NUM}", $tpl['times'], __('lblSoldTimes', true))?>
											<?php
										} else {
											?>
											<?php echo pjSanitize::html($tpl['popular_arr']['name']);?> / <?php echo str_replace("{NUM}", $tpl['times'], __('lblSoldTimes', true))?>
											<?php 
										}
									}
									?>
					            </div>
					        </div><!-- /.form-group -->
					    </div>
					</div>
					
                </div>
            </div>
        </div>
    </div>
</div>