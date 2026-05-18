<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <h2><?php __('infoBookingsTitle') ?></h2>
            </div>
        </div><!-- /.row -->

        <p class="m-b-none"><i class="fa fa-info-circle"></i><?php __('infoBookingsDesc') ?></p>
    </div><!-- /.col-md-12 -->
</div>

<div class="wrapper wrapper-content animated fadeInRight">
	<div class="row">
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
				    		case in_array($error_code, array('AOP02')):
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
    				if (isset($tpl['arr']) && is_array($tpl['arr']) && !empty($tpl['arr']))
    				{	    				    
                    ?>
	                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionUpdate" method="post" class="form-horizontal" id="frmUpdateOptions">
	                        <input type="hidden" name="options_update" value="1" />
	                        <input type="hidden" name="tab" value="2" />
	                        <input type="hidden" name="next_action" value="pjActionBooking" />
	                        <?php
	                        foreach ($tpl['arr'] as $option)
	                        {
	                            if(in_array($option['key'], array('o_allow_bank', 'o_allow_cash', 'o_allow_creditcard', 'o_bank_account', 'o_currency', 'o_allow_paypal','o_paypal_address','o_allow_authorize','o_authorize_transkey','o_authorize_merchant_id','o_authorize_timezone','o_authorize_timezone','o_authorize_md5_hash')))
	                            {
	                                continue;
	                            }
	                            if ($option['tab_id'] != 2 || (int) $option['is_visible'] === 0) 
	                            {
	                                continue;
	                            }
	                            ?>
	                            <div class="form-group <?php echo $rowClass; ?>" style="<?php echo $rowStyle; ?>">
	
	                                <label class="col-sm-3 control-label"><?php __('opt_' . $option['key']); ?></label>
	                                <div class="col-lg-8 col-sm-9">
	                                    <div class="row">
	                                        <div class="col-sm-6">
	                                            <?php
	                                            switch ($option['type'])
	                                            {
	                                                case 'string':
	                                                    ?><input type="text" name="value-<?php echo $option['type']; ?>-<?php echo $option['key']; ?>" class="form-control" value="<?php echo pjSanitize::html($option['value']); ?>"><?php
	                                                    break;
	                                                case 'text':
	                                                    ?><textarea name="value-<?php echo $option['type']; ?>-<?php echo $option['key']; ?>" class="form-control"><?php echo pjSanitize::html($option['value']); ?></textarea><?php
	                                                    break;
	                                                case 'int':
	                                                    if(in_array($option['key'], array('o_deposit_payment'))){
	                                                        ?>
	                                                        <div class="input-group">
	                                                            <input type="text" name="value-<?php echo $option['type']; ?>-<?php echo $option['key']; ?>" class="form-control decimal number text-right" value="<?php echo number_format($option['value'], 2) ?>" data-msg-number="<?php __('pj_please_enter_valid_number', false, true);?>">
	
	                                                            <span class="input-group-addon">%</span>
	                                                        </div>
	                										<?php
	                                                    }else{
	                                                        ?>
	                                                        <input type="text" name="value-<?php echo $option['type']; ?>-<?php echo $option['key']; ?>" class="form-control field-int" value="<?php echo pjSanitize::html($option['value']); ?>">
	                                                        <?php
	                                                    }
	                                                    break;
	                                                case 'float':
	                                                    if(in_array($option['key'], array('o_insurance'))) {
	                                                        ?>
	                                                        <div class="input-group">
	                                                            <input type="text" name="value-<?php echo $option['type']; ?>-<?php echo $option['key']; ?>" class="form-control decimal number text-right" value="<?php echo number_format($option['value'], 2); ?>" data-msg-number="<?php __('pj_please_enter_valid_number', false, true);?>">
																<span class="input-group-addon insurance-group-addon"><?php echo $tpl['option_arr']['o_insurance_type'] == 'amount' ? pjCurrency::getCurrencySign($tpl['option_arr']['o_currency'], false) : '%';?></span>
	                                                        </div>
	                                                        <?php
	                                                    } else if(in_array($option['key'], array('o_tax_payment'))) {
	                                                        ?>
	                                                        <div class="input-group">
	                                                            <input type="text" name="value-<?php echo $option['type']; ?>-<?php echo $option['key']; ?>" class="form-control number text-right" value="<?php echo (float)$option['value']; ?>" data-msg-number="<?php __('pj_please_enter_valid_number', false, true);?>">
																<span class="input-group-addon">%</span>
	                                                        </div>
	                                                        <?php
	                                                    } else {
	                                                        ?><input type="text" name="value-<?php echo $option['type']; ?>-<?php echo $option['key']; ?>" class="form-control field-float number" value="<?php echo number_format($option['value'], 2); ?>"><?php
	                                                    }
	                                                    break;
	                                                case 'enum':
	                                                    if(in_array($option['key'], array('o_seo_url', 'o_disable_orders', 'o_disable_payments'))) {
	                                                        include dirname(__FILE__) . '/elements/switch.php';
	                                                    } else {
	                                                        include dirname(__FILE__) . '/elements/enum.php';
	                                                    }
	                                                    break;
	                                            }
	                                            ?>
	                                        </div>
	                                        <?php if ($option['key'] == 'o_insurance') { ?>
	                                        	<div class="col-sm-4">
	                                        		<select name="value-enum-o_insurance_type" class="form-control">
													    <?php
													    $enum_arr = __('enum_arr', true);
													    foreach ($enum_arr as $k => $el)
													    {
													    	if (!in_array($k, array('amount','percent'))) {
													    		continue;
													    	}
													    	?><option value="amount|percent::<?php echo $k; ?>" data-type="<?php echo $k == 'amount' ? pjCurrency::getCurrencySign($tpl['option_arr']['o_currency'], false) : '%';?>" <?php echo $k == $tpl['option_arr']['o_insurance_type'] ? 'selected="selected"' : '';?>><?php echo $el; ?></option><?php
													    }
													    ?>
													</select>
	                                        	</div>
	                                        <?php } ?>
	                                        <?php if (in_array($option['key'], array('o_disable_orders', 'o_disable_payments', 'o_thankyou_page'))): ?>
	                                            <div class="col-sm-12">
	                                                <span class="form-control-static"><?php __("opt_{$option['key']}_text") ?></span>
	                                            </div>
	                                        <?php endif; ?>
	
	                                        <?php if (in_array($option['key'], array('o_min_hour'))): ?>
	                                            <p class="m-t-xs"><?php __("lblMinutes") ?></p>
	                                        <?php endif; ?>
	                                    </div>
	                                </div>
	                            </div>
	                            <?php
	                        }
	                        ?>
	                        <div class="hr-line-dashed"></div>
	
	                        <div class="clearfix">
	                            <button class="ladda-button btn btn-primary btn-lg pull-left btn-phpjabbers-loader" data-style="zoom-in">
	                                <span class="ladda-label"><?php __('plugin_base_btn_save'); ?></span>
	                                <?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
	                            </button>
	                        </div>
	                    </form>
	                    <?php
	                }
	                ?>
	            </div>
	        </div>
	    </div><!-- /.col-lg-12 -->
	</div>
</div>