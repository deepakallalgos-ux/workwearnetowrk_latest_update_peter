<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-10">
                <h2><?php __('infoVouchersTitle', false, true);?></h2>
            </div>
        </div><!-- /.row -->

        <p class="m-b-none"><i class="fa fa-info-circle"></i> <?php __('infoVouchersDesc', false, true);?></p>
    </div><!-- /.col-md-12 -->
</div>

<div class="row wrapper wrapper-content animated fadeInRight">
    <div class="col-lg-12">
    	<?php
    	$vt = __('voucher_types', true);
		$vv = __('voucher_valids', true);
    	$error_code = $controller->_get->toString('err');
    	if (!empty($error_code))
    	{
    	    $titles = __('error_titles', true);
    	    $bodies = __('error_bodies', true);
    	    switch (true)
    	    {
    	        case in_array($error_code, array('AV01', 'AV03', 'AV05')):
    	            ?>
    				<div class="alert alert-success">
    					<i class="fa fa-check m-r-xs"></i>
    					<strong><?php echo @$titles[$error_code]; ?></strong>
    					<?php echo @$bodies[$error_code]?>
    				</div>
    				<?php
    				break;
                case in_array($error_code, array('AV04', 'AV08', 'AV09')):
                    $bodies_text = str_replace("{SIZE}", ini_get('post_max_size'), @$bodies[$error_code]);
    				?>
    				<div class="alert alert-danger">
    					<i class="fa fa-exclamation-triangle m-r-xs"></i>
    					<strong><?php echo @$titles[$error_code]; ?></strong>
    					<?php echo $bodies_text;?>
    				</div>
    				<?php
    				break;
    		}
    	}
    	?>
        <div class="ibox float-e-margins">
            <div class="ibox-content">
                <div class="row m-b-md">
                	<?php if ($tpl['has_create']) { ?>
	                    <div class="col-lg-2 col-md-3 col-sm-3">
	                    	<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminVouchers&amp;action=pjActionCreate" class="btn btn-primary"><i class="fa fa-plus"></i> <?php __('voucher_create') ?></a>
	                    </div><!-- /.col-md-6 -->
					<?php } ?>
                    <div class="col-lg-4 col-md-4 col-sm-5">
                        <form action="" method="get" class="form-horizontal frm-filter">
                            <div class="input-group">
                                <input type="text" name="q" placeholder="<?php __('plugin_base_btn_search', false, true); ?>" class="form-control">
                                <div class="input-group-btn">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div><!-- /.col-md-3 -->
                    <div class="col-lg-6 col-md-8 text-right">
						<div class="btn-group" role="group" aria-label="...">
                            <button type="button" class="btn btn-primary btn-all"><?php __('lblAll'); ?></button>
                            <button type="button" class="btn btn-default btn-filter" data-column="valid" data-value="fixed"><?php echo $vv['fixed']; ?></button>
                            <button type="button" class="btn btn-default btn-filter" data-column="valid" data-value="period"><?php echo $vv['period']; ?></button>
                            <button type="button" class="btn btn-default btn-filter" data-column="valid" data-value="recurring"><?php echo $vv['recurring']; ?></button>
                        </div>
					</div><!-- /.col-md-6 -->
                </div><!-- /.row -->
                
                <div id="grid"></div>
            </div>
        </div>
    </div><!-- /.col-lg-12 -->
</div>

<script type="text/javascript">
var pjGrid = pjGrid || {};
var myLabel = myLabel || {};
myLabel.code = <?php x__encode('voucher_code'); ?>;
myLabel.products = <?php x__encode('voucher_products'); ?>;
myLabel.discount = <?php x__encode('voucher_discount'); ?>;
myLabel.type = <?php x__encode('voucher_type'); ?>;
myLabel.valid = <?php x__encode('voucher_valid'); ?>;
myLabel.amount = "<?php echo $vt['amount']; ?>";
myLabel.percent = "<?php echo $vt['percent']; ?>";
myLabel.fixed = "<?php echo $vv['fixed']; ?>";
myLabel.period = "<?php echo $vv['period']; ?>";
myLabel.recurring = "<?php echo $vv['recurring']; ?>";
myLabel.delete_selected = <?php x__encode('delete_selected'); ?>;
myLabel.delete_confirmation = <?php x__encode('delete_confirmation'); ?>;
myLabel.currency = "<?php echo $tpl['option_arr']['o_currency']; ?>";

myLabel.has_create = <?php echo (int) $tpl['has_create']; ?>;
myLabel.has_update = <?php echo (int) $tpl['has_update']; ?>;
myLabel.has_delete = <?php echo (int) $tpl['has_delete']; ?>;
myLabel.has_delete_bulk = <?php echo (int) $tpl['has_delete_bulk']; ?>;
</script>