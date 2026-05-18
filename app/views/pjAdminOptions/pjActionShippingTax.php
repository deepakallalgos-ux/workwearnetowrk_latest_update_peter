<?php 
$titles = __('error_titles', true);
$bodies = __('error_bodies', true);
?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-lg-9 col-md-8 col-sm-6">
                <h2><?php echo @$titles['AO24']; ?></h2>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 btn-group-languages">
                <?php if ($tpl['is_flag_ready']) : ?>
				<div class="multilang"></div>
				<?php endif; ?>
        	</div>
        </div><!-- /.row -->

        <p class="m-b-none"><i class="fa fa-info-circle"></i><?php echo @$bodies['AO24']; ?></p>
    </div><!-- /.col-md-12 -->
</div>

<div class="row wrapper wrapper-content animated fadeInRight">
	<div class="col-lg-12">
		<div class="ibox float-e-margins">
			<div class="ibox-content">
				<?php 
				$error_code = $controller->_get->toString('err');
				if (!empty($error_code))
				{
					$titles = __('error_titles', true);
					$bodies = __('error_bodies', true);
					switch (true)
					{
						case in_array($error_code, array('AOP06')):
							?>
							<div class="alert alert-success">
								<i class="fa fa-check m-r-xs"></i>
								<strong><?php echo @$titles[$error_code]; ?></strong>
								<?php echo @$bodies[$error_code]?>
							</div>
							<?php
							break;
						case in_array($error_code, array('')):
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
				?>
				<form id="frmUpdateOptions" action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionUpdate" class="form-horizontal" method="post">
					<input type="hidden" name="options_update" value="1" />
					<input type="hidden" name="tab" value="6" />
					<input type="hidden" name="next_action" value="pjActionShippingTax" />
	
					<div class="table-responsive table-responsive-secondary">
						<table id="tblShipping" class="table table-striped table-hover">
							<thead>
								<tr>
									<th><?php __('tax_location'); ?></th>
									<th><?php __('tax_shipping'); ?></th>
									<th><?php __('tax_free'); ?></th>
									<th><?php __('tax_tax'); ?></th>
									<th>&nbsp;</th>
								</tr>
							</thead>
							<tbody>
								<?php if (isset($tpl['arr']) && !empty($tpl['arr'])) { ?>
									<?php foreach ($tpl['arr'] as $item) { ?>
										<tr>
											<td>
								                <?php
								            	foreach ($tpl['lp_arr'] as $v)
								            	{
								                	?>
								                    <div class=" pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 1 ? NULL : 'none'; ?>">
								                        <div class="<?php echo $tpl['is_flag_ready'] ? 'input-group' : '';?>" data-index="<?php echo $v['id']; ?>">
															<input type="text" name="i18n[<?php echo $v['id']; ?>][location][<?php echo $item['id']; ?>]" value="<?php echo pjSanitize::html(@$item['i18n'][$v['id']]['location']); ?>" class="form-control<?php echo (int) $v['is_default'] === 0 ? NULL : ' scRequired required'; ?>" lang="<?php echo $v['id']; ?>" data-msg-required="<?php __('pj_field_required', false, true);?>"/>	
															<?php if ($tpl['is_flag_ready']) : ?>
															<span class="input-group-addon pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="<?php echo pjSanitize::html($v['name']); ?>"></span>
															<?php endif; ?>
														</div>
								                    </div>
								                    <?php
								                }
								                ?>
								            </td>
											<td>
												<div class="">
								                    <div class="input-group">
								                        <input type="text" name="shipping[<?php echo $item['id']; ?>]" value="<?php echo (float) $item['shipping']; ?>" class="form-control number" data-msg-number="<?php __('pj_field_number');?>"/>
								    
								                        <span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']); ?></span> 
								                    </div>
								                </div>
											</td>
											<td>
												<div class="">
								                    <div class="input-group">
								                        <input type="text" name="free[<?php echo $item['id']; ?>]" value="<?php echo (float) $item['free']; ?>" class="form-control number" data-msg-number="<?php __('pj_field_number');?>"/>
								    
								                        <span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']); ?></span> 
								                    </div>
								                </div>
											</td>	
											<td>
												<div class="">
								                    <div class="input-group">
								                        <input type="text" name="tax[<?php echo $item['id']; ?>]" value="<?php echo (float) $item['tax']; ?>" class="form-control number" data-msg-number="<?php __('pj_field_number');?>"/>
								    
								                        <span class="input-group-addon">%</span> 
								                    </div>
								                </div>
											</td>
											<td>
												<div class="text-right">
													<a href="#" class="btn btn-danger btn-outline btn-sm m-n btnDeleteShipping" data-id="<?php echo $item['id']; ?>"><i class="fa fa-trash"></i></a>
												</div>
											</td>
										</tr> 
									<?php } ?>
								<?php } ?>
							</tbody>
						</table>
					</div>
					<button type="button" class="btn btn-primary btn-outline btnAddShipping"><i class="fa fa-plus"></i> <?php __('shipping_tax_add'); ?></button>
					<div class="hr-line-dashed"></div>
	
					<div class="row">
						<div class="col-xs-12">
							<button type="submit" class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader" data-style="zoom-in">
								<span class="ladda-label"><?php __('plugin_base_btn_save'); ?></span>
								<?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div><!-- /.col-lg-12 -->
</div>

<table style="display: none" id="tblShippingClone">
	<tbody>
		<tr>
			<td>
                <?php
            	foreach ($tpl['lp_arr'] as $v)
            	{
                	?>
                    <div class=" pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 1 ? NULL : 'none'; ?>">
                        <div class="<?php echo $tpl['is_flag_ready'] ? 'input-group' : '';?>" data-index="<?php echo $v['id']; ?>">
							<input type="text" name="i18n[<?php echo $v['id']; ?>][location][{INDEX}]" class="form-control<?php echo (int) $v['is_default'] === 0 ? NULL : ' scRequired required'; ?>" lang="<?php echo $v['id']; ?>" data-msg-required="<?php __('pj_field_required', false, true);?>"/>	
							<?php if ($tpl['is_flag_ready']) : ?>
							<span class="input-group-addon pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="<?php echo pjSanitize::html($v['name']); ?>"></span>
							<?php endif; ?>
						</div>
                    </div>
                    <?php
                }
                ?>
            </td>
			<td>
				<div class="">
                    <div class="input-group">
                        <input type="text" name="shipping[{INDEX}]" class="form-control number" data-msg-number="<?php __('pj_field_number');?>"/>
    
                        <span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']); ?></span> 
                    </div>
                </div>
			</td>
			<td>
				<div class="">
                    <div class="input-group">
                        <input type="text" name="free[{INDEX}]" class="form-control number" data-msg-number="<?php __('pj_field_number');?>"/>
    
                        <span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']); ?></span> 
                    </div>
                </div>
			</td>	
			<td>
				<div class="">
                    <div class="input-group">
                        <input type="text" name="tax[{INDEX}]" class="form-control number" data-msg-number="<?php __('pj_field_number');?>"/>
    
                        <span class="input-group-addon">%</span> 
                    </div>
                </div>
			</td>
			<td>
				<div class="text-right">
					<a href="#" class="btn btn-danger btn-outline btn-sm m-n btnRemoveShipping"><i class="fa fa-trash"></i></a>
				</div>
			</td>
		</tr> 
	</tbody>
</table>
<script type="text/javascript">
var myLabel = myLabel || {};
myLabel.alert_delete_shipping_title = <?php x__encode('lblDeleteShipping'); ?>;
myLabel.alert_delete_shipping_text = <?php x__encode('lblDeleteShippingConfirm'); ?>;
myLabel.btn_delete = <?php x__encode('btnDelete'); ?>;
myLabel.btn_cancel = <?php x__encode('btnCancel'); ?>;
<?php if ($tpl['is_flag_ready']) : ?>
	var pjCmsLocale = pjCmsLocale || {};
	pjCmsLocale.langs = <?php echo $tpl['locale_str']; ?>;
	pjCmsLocale.flagPath = "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/";
<?php endif; ?>
</script>