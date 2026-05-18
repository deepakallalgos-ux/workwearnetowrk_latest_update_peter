<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-10">
                <h2><?php __('infoProductsTitle', false, true);?></h2>
            </div>
        </div><!-- /.row -->

        <p class="m-b-none"><i class="fa fa-info-circle"></i> <?php __('infoProductsDesc', false, true);?></p>
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
                case in_array($error_code, array('AP04', 'AP05', 'AP08', 'AP09')):
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
	                    	<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProducts&amp;action=pjActionCreate" class="btn btn-primary"><i class="fa fa-plus"></i> <?php __('btnAddProduct') ?></a>
	                    </div><!-- /.col-md-6 -->
					<?php } ?>
                    <div class="col-lg-3 col-md-3 col-sm-5">
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
                    <div class="col-lg-2 col-md-3 col-sm-4">
						<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" class="btn btn-primary btn-outline btn-advance-search"><?php __('btnAdvancedSearch'); ?></a>
					</div><!-- /.col-md-2 -->
					<div class="col-lg-5 col-md-12 text-right">
						<div class="btn-group" role="group" aria-label="...">
                            <button type="button" class="btn btn-primary btn-all <?php echo !$controller->_get->check('is_active_out') && !$controller->_get->check('is_out') ? ' active' : null;?>"><?php __('lblAll'); ?></button>
                            <button type="button" class="btn btn-default btn-filter" data-column="status" data-value="1"><i class="fa fa-check m-r-xs"></i><?php echo $product_statuses[1]; ?></button>
                            <button type="button" class="btn btn-default btn-filter" data-column="status" data-value="0"><i class="fa fa-times m-r-xs"></i><?php echo $product_statuses[2]; ?></button>
                            <button type="button" class="btn btn-default btn-filter <?php echo $controller->_get->check('is_active_out') || $controller->_get->check('is_out') ? ' active' : NULL;?>" data-column="status" data-value="3"><i class="fa fa-times-circle m-r-xs"></i><?php __('lblOutOfStock'); ?></button>
                            <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProductImportHistory&action=pjActionIndex" class="btn btn-primary btn-outline">Import</a>
                        
						</div>
					</div><!-- /.col-md-6 -->
                </div><!-- /.row -->
                
                <div id="collapseOne" class="collapse" style="height: 0;" aria-expanded="false">
					<div class="m-b-lg">
						<ul class="agile-list no-padding">
							<li class="success-element b-r-sm">
							<div class="panel-body">
								<form method="get" class="frm-filter-advanced">
									
									<div class="row">
										<div class="col-sm-4 col-xs-12">
											<div class="form-group">
												<label class="control-label"><?php __('product_name'); ?></label>
												<input class="form-control" type="text" name="name" id="name" value="<?php echo $controller->_get->check('name') ? $controller->_get->toString('name') : '';?>">
											</div>
										</div>
										<div class="col-sm-4 col-xs-12">
											<div class="form-group">
												<label class="control-label"><?php __('product_sku'); ?></label>
												<input class="form-control" type="text" name="sku" id="sku" value="<?php echo $controller->_get->check('sku') ? $controller->_get->toString('sku') : '';?>">
											</div>
										</div>
										<div class="col-sm-4 col-xs-12">
											<div class="form-group">
												<label class="control-label"><?php __('product_category'); ?></label>
												<select name="category_id" class="form-control">
													<option value="">-- <?php __('lblChoose'); ?> --</option>
													<?php
													foreach ($tpl['category_arr'] as $category)
													{
														?><option value="<?php echo $category['data']['id']; ?>"<?php echo $controller->_get->check('category_id') && $controller->_get->toInt('category_id') == $category['data']['id'] ? ' selected="selected"' : NULL; ?>><?php echo str_repeat("-----", $category['deep']) . " " . pjSanitize::html($category['data']['name']); ?></option><?php
													}
													?>
													</select>
											</div>
										</div>
										<div class="col-sm-4 col-xs-12">
											<div class="form-group">
												<label class="control-label"><?php __('product_status'); ?></label>
												<select name="status" class="form-control">
													<option value="">-- <?php __('lblChoose'); ?> --</option>
													<?php
													foreach ($product_statuses as $k => $v)
													{
														?><option value="<?php echo $k; ?>"<?php echo $controller->_get->check('status') && $controller->_get->toString('status') == $k ? ' selected="selected"' : NULL; ?>><?php echo pjSanitize::html($v); ?></option><?php
													}
													?>
												</select>
											</div>
										</div>
										<div class="col-sm-4 col-xs-12">
											<div class="form-group">
												<label class="control-label"><?php __('product_is_digital'); ?></label>
												<div>
													<input type="checkbox" class="i-checks" id="is_digital" name="is_digital" value="1" <?php echo $controller->_get->check('is_digital') ? ' checked="checked"' : NULL; ?> />
												</div>
											</div>
										</div>
										<div class="col-sm-4 col-xs-12">
											<div class="form-group">
												<label class="control-label"><?php __('product_is_featured'); ?></label>
												<div>
													<input type="checkbox" class="i-checks" id="is_featured" name="is_featured" value="1" <?php echo $controller->_get->check('is_featured') ? ' checked="checked"' : NULL; ?>  />
												</div>
											</div>
										</div>
									</div>
									
									<div class="m-t-sm">
										<button class="btn btn-primary" type="submit"><?php __('btnSearch');?></button>
										<button class="btn btn-primary btn-outline" type="reset"><?php __('btnCancel');?></button>
									</div>
								</form>
							</div>
							<!-- /.panel-body -->
							</li>
							<!-- /.panel panel-primary -->
						</ul>
					</div>
					<!-- /.m-b-lg -->
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
if ($controller->_get->check('is_out'))
{
    ?>pjGrid.queryString += "&is_out=yes";<?php
}
if ($controller->_get->check('is_active_out'))
{
	?>pjGrid.queryString += "&is_active_out=yes";<?php
}
?>
var myLabel = myLabel || {};
myLabel.image = <?php x__encode('product_image'); ?>;
myLabel.name = <?php x__encode('lblName'); ?>;
myLabel.sku = <?php x__encode('product_sku'); ?>;
myLabel.stock = <?php x__encode('product_stock'); ?>;
myLabel.price = <?php x__encode('product_stock_price'); ?>;
myLabel.status = <?php x__encode('lblStatus'); ?>;
myLabel.active = "<?php echo $product_statuses[1]; ?>";
myLabel.inactive = "<?php echo $product_statuses[2]; ?>";
myLabel.delete_selected = <?php x__encode('delete_selected'); ?>;
myLabel.delete_confirmation = <?php x__encode('delete_confirmation'); ?>;
myLabel.exported = <?php x__encode('lblExport'); ?>;

myLabel.has_create = <?php echo (int) $tpl['has_create']; ?>;
myLabel.has_update = <?php echo (int) $tpl['has_update']; ?>;
myLabel.has_delete = <?php echo (int) $tpl['has_delete']; ?>;
myLabel.has_delete_bulk = <?php echo (int) $tpl['has_delete_bulk']; ?>;
</script>