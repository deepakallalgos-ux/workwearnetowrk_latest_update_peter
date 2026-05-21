<?php 
$titles = __('error_titles', true);
$bodies = __('error_bodies', true);
?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-10">
                <h2><?php echo @$titles['AP11'];?></h2>
            </div>
        </div><!-- /.row -->

        <p class="m-b-none"><i class="fa fa-info-circle"></i> <?php echo @$bodies['AP11'];?></p>
    </div><!-- /.col-md-12 -->
</div>

<div class="row wrapper wrapper-content animated fadeInRight">
    <div class="col-lg-12">
    	<?php
    	$product_statuses = __('product_statuses', true);
    	$error_code = $controller->_get->toString('err');
    	if (!empty($error_code))
    	{
    	    $titles = __('error_titles', true);
    	    $bodies = __('error_bodies', true);
    	    switch (true)
    	    {
    	        case in_array($error_code, array('AP01', 'AP03')):
    	            ?>
    				<div class="alert alert-success">
    					<i class="fa fa-check m-r-xs"></i>
    					<strong><?php echo @$titles[$error_code]; ?></strong>
    					<?php echo @$bodies[$error_code]?>
    				</div>
    				<?php
    				break;
                case in_array($error_code, array('AP04', 'AP05', 'AP08', 'AP09', 'AP11')):
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
                	<div class="col-lg-4 col-md-5 col-sm-6">
                        <form action="" method="get" class="form-horizontal frm-filter-stock">
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
                    <div class="col-lg-8 col-md-7 col-sm-6 text-right">
                    	<a class="btn btn-primary" href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProducts&amp;action=pjActionPrintStock" target="_blank"><?php __('product_stock_print_all');?></a>
                    	<form id="frmPrintSelectedStock" action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProducts&amp;action=pjActionPrintStock" method="post" target="_blank"></form>
					</div><!-- /.col-md-6 -->
                </div><!-- /.row -->                
				
				<div id="grid_stock"></div>
            </div>
        </div>
    </div><!-- /.col-lg-12 -->
</div>
<style type="text/css">
.s-Name{
	color: #306dab;
	display: block;
	text-transform: uppercase;
}
.s-Attr{
	color: #babcbe;
	display: block;
	margin: 3px 0 0;
}
</style>
<script type="text/javascript">
var pjGrid = pjGrid || {};
pjGrid.queryString = "";

var myLabel = myLabel || {};
myLabel.name = <?php x__encode('lblName'); ?>;
myLabel.qty = <?php x__encode('product_stock_qty'); ?>;
myLabel.price = <?php x__encode('product_stock_price'); ?>;
myLabel.delete_selected = <?php x__encode('delete_selected'); ?>;
myLabel.delete_confirmation = <?php x__encode('delete_confirmation'); ?>;
myLabel.print_selected = "<?php __('product_stock_print_selected', false, true); ?>";

myLabel.has_update = <?php echo (int) $tpl['has_update']; ?>;
</script>