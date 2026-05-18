<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-10">
                <h2><?php __('infoCreateClientTitle', false, true);?></h2>
            </div>
        </div><!-- /.row -->

        <p class="m-b-none"><i class="fa fa-info-circle"></i> <?php __('infoCreateClientDesc', false, true);?></p>
    </div><!-- /.col-md-12 -->
</div>
<?php
$u_statarr = __('u_statarr', true);
$titles = __('error_titles', true);
$bodies = __('error_bodies', true);
?>
<div class="row wrapper wrapper-content animated fadeInRight">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-content">
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminClients&amp;action=pjActionCreate" method="post" id="frmCreateClient">
					<input type="hidden" name="client_create" value="1" />			
					<div class="tabs-container m-b-lg">
		                <ul class="nav nav-tabs" role="tablist">
		                    <li role="presentation" class="active"><a href="#general-info" aria-controls="general-info" role="tab" data-toggle="tab"><?php __('client_general'); ?></a></li>
		                    <li role="presentation"><a href="#address-book" aria-controls="address-book" role="tab" data-toggle="tab"><?php __('client_address_book'); ?></a></li>
		                </ul>
		    
		                <div class="tab-content">
		                    <div role="tabpanel" class="tab-pane active" id="general-info">
		                        <div class="panel-body">
		                        	<div class="row">
				                        <div class="col-md-4 col-sm-6">
				                            <div class="form-group">
				                                <label class="control-label"><?php __('lblStatus'); ?></label>
				
				                                <div class="clearfix">
				                                    <div class="switch onoffswitch-data pull-left">
				                                        <div class="onoffswitch">
				                                            <input type="checkbox" class="onoffswitch-checkbox" id="status" name="status" checked>
				                                            <label class="onoffswitch-label" for="status">
				                                                <span class="onoffswitch-inner" data-on="<?php echo $u_statarr['T'];?>" data-off="<?php echo $u_statarr['F'];?>"></span>
				                                                <span class="onoffswitch-switch"></span>
				                                            </label>
				                                        </div>
				                                    </div>
				                                </div><!-- /.clearfix -->
				                            </div><!-- /.form-group -->
				                        </div>
								                        
								        <div class="col-md-4 col-sm-6">
				                            <div class="form-group">
				                                <label class="control-label"><?php __('client_email'); ?></label>
				
												<div class="input-group">
													<span class="input-group-addon"><i class="fa fa-at"></i></span>
				                                	<input type="text" name="email" id="email" class="form-control email required" placeholder="info@domain.com" data-msg-required="<?php __('fd_field_required', false, true);?>" maxlength="255">
				                                </div>
				                            </div>
				                        </div>
				                        
				                        <div class="col-md-4 col-sm-6">
				                            <div class="form-group">
				                                <label class="control-label"><?php __('client_password'); ?></label>
												
												<div class="input-group">
													<span class="input-group-addon"><i class="fa fa-lock"></i></span>
				                                	<input type="text" name="password" id="password" class="form-control required" data-msg-required="<?php __('fd_field_required', false, true);?>" maxlength="255">
				                                </div>
				                            </div>
				                        </div>
				                                           
										<div class="col-md-4 col-sm-6">
				                            <div class="form-group">
				                                <label class="control-label"><?php __('client_client_name'); ?></label>
				
				                                <input type="text" id="client_name" name="client_name" class="form-control required" data-msg-required="<?php __('fd_field_required', false, true);?>" maxlength="255">
				                            </div>
				                        </div><!-- /.col-md-3 -->
				                        
				                        <div class="col-md-4 col-sm-6">
				                            <div class="form-group">
				                                <label class="control-label"><?php __('client_phone'); ?></label>
												
												<div class="input-group">
													<span class="input-group-addon"><i class="fa fa-phone"></i></span>
				                                	<input type="text" name="phone" id="phone" class="form-control digits" placeholder="1234567890" maxlength="255" data-msg-digits="<?php __('front_digits_required');?>" data-msg-minlength="<?php __('front_phone_invalid');?>" data-msg-maxlength="<?php __('front_phone_invalid');?>">
				                                </div>
				                            </div>
				                        </div><!-- /.col-md-3 -->
				
				                        <div class="col-md-4 col-sm-6">
				                            <div class="form-group">
				                                <label class="control-label"><?php __('client_url'); ?></label>
												
												<div class="input-group">
													<span class="input-group-addon"><i class="fa fa-globe"></i></span>
				                                	<input type="text" name="url" id="url" class="form-control" maxlength="255" >
				                                </div>
				                            </div>
				                        </div><!-- /.col-md-3 -->
				                    </div><!-- /.row -->
				
				                    <div class="hr-line-dashed"></div>
		    
		                            <div class="clearfix">
		                                <button type="submit" class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader pull-left" data-style="zoom-in" style="margin-right: 15px;">
		                                    <span class="ladda-label"><?php __('btnSave'); ?></span>
		                                    <?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
		                                </button>
		                                <a class="btn btn-white btn-lg pull-right" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminClients&action=pjActionIndex"><?php __('btnCancel'); ?></a>
		                            </div><!-- /.clearfix -->
		                        </div>
		                    </div>
		    
		                    <div role="tabpanel" class="tab-pane" id="address-book">
		                        <div class="panel-body">
		                        	<div class="alert alert-success"><strong><?php echo @$titles['AC10']; ?></strong> <?php echo @$bodies['AC10'];?></div>
		                            <div class="form-group" id="pjScAddressBookList">
		                            
		                            </div>
		                            <div class="form-group">
										<a href="#" class="btn btn-sm btn-primary btn-outline btnAddAddress"><i class="fa fa-plus"></i> <?php __('client_add_address'); ?></a>
									</div>									
		                            <div class="hr-line-dashed"></div>    						
		                            <div class="clearfix">
		                                <button type="submit" class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader pull-left" data-style="zoom-in" style="margin-right: 15px;">
		                                    <span class="ladda-label"><?php __('btnSave'); ?></span>
		                                    <?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
		                                </button>
		                                <a class="btn btn-white btn-lg pull-right" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminClients&action=pjActionIndex"><?php __('btnCancel'); ?></a>
		                            </div><!-- /.clearfix -->
		                        </div>                        
		                    </div>
		                </div>
		            </div>
		        </form>
            </div>
        </div>
    </div><!-- /.col-lg-12 -->
</div>
<div id="boxCloneAddress" style="display: none"><?php include dirname(__FILE__) . '/elements/address.php'; ?></div>
<script type="text/javascript">
var myLabel = myLabel || {};
myLabel.email_exists = <?php x__encode('vr_email_taken'); ?>;
myLabel.choose = "<?php __('order_choose'); ?>";
</script>