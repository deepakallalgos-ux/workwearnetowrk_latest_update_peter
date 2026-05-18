<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <div class="row">
        	<div class="col-lg-9 col-md-8 col-sm-6">
                <h2><?php __('infoCompaniesTitle');?></h2>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 btn-group-languages">
                <?php if ($tpl['is_flag_ready']) : ?>
				<div class="multilang"></div>
				<?php endif; ?>
        	</div>
        </div><!-- /.row -->

        <p class="m-b-none"><i class="fa fa-info-circle"></i> <?php __('infoCompaniesDesc', false, true);?></p>
    </div><!-- /.col-md-12 -->
</div>

<div class="row wrapper wrapper-content animated fadeInRight">
    <?php
    	$error_code = $controller->_get->toString('err');
    	if (!empty($error_code))
    	{
    	    $titles = __('error_titles', true);
    	    $bodies = __('error_bodies', true);
    	    switch (true)
    	    {
    	        case in_array($error_code, array('AE01', 'AE03')):
    	            ?>
    				<div class="alert alert-success">
    					<i class="fa fa-check m-r-xs"></i>
    					<strong><?php echo @$titles[$error_code]; ?></strong>
    					<?php echo @$bodies[$error_code]?>
    				</div>
    				<?php
    				break;
                case in_array($error_code, array('ACN001')):
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
    <div class="<?php echo $tpl['has_create'] || $tpl['has_update'] ? 'col-lg-8' : 'col-lg-12';?> ">
        <div class="ibox float-e-margins">
            <div class="ibox-content">
                <div class="row">
                	<?php if ($tpl['has_create']) { ?>
	                	<div class="col-md-4">
	                    	<a href="#" class="btn btn-primary pjFdAddCompany"><i class="fa fa-plus"></i> <?php __('btnAddCompany') ?></a>
	                    </div><!-- /.col-md-6 -->
                    <?php } ?>
                    <div class="col-md-4 col-sm-8">
                    	<form action="" method="get" class="form-horizontal frm-filter">
                            <div class="input-group m-b-md">
                                <input type="text" name="q" placeholder="<?php __('plugin_base_btn_search', false, true); ?>" class="form-control">
                                <div class="input-group-btn">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div><!-- /.col-lg-6 -->
                	<?php
					$filter = __('filter', true);
					?>
                    <div class="col-md-4 text-right">
                        <div class="btn-group" role="group" aria-label="...">
                            <button type="button" class="btn btn-primary btn-all active"><?php __('lblAll'); ?></button>
                            <button type="button" class="btn btn-default btn-filter" data-column="status" data-value="T"><i class="fa fa-check m-r-xs"></i><?php echo $filter['active']; ?></button>
                            <button type="button" class="btn btn-default btn-filter" data-column="status" data-value="F"><i class="fa fa-times m-r-xs"></i><?php echo $filter['inactive']; ?></button>
                        </div>
                    </div><!-- /.col-md-6 -->
                </div><!-- /.row -->
                
                <div id="grid"></div>
                
            </div>
        </div>
    </div><!-- /.col-lg-8 -->
	<?php if ($tpl['has_create'] || $tpl['has_update']) { ?>
    <div class="col-lg-4">
        <div id="pjFdFormWrapper" class="panel no-borders">
        	
        </div><!-- /.panel panel-primary -->
    </div><!-- /.col-lg-3 -->
    <?php } ?>
</div>
<script type="text/javascript">
var myLabel = myLabel || {};
myLabel.company_name = <?php x__encode('lblCompanyName'); ?>;
myLabel.company_w_number = <?php x__encode('lblCompanyWNumber'); ?>;
myLabel.status = <?php x__encode('lblStatus'); ?>;
myLabel.active = "<?php echo $filter['active']; ?>";
myLabel.deactivate = "<?php echo $filter['inactive']; ?>";
// myLabel.active = <?php x__encode('filter_company_ARRAY_active'); ?>;
// myLabel.deactivate = <?php x__encode('filter_company_ARRAY_deactivate'); ?>;
myLabel.delete_selected = <?php x__encode('plugin_base_delete_selected'); ?>;
myLabel.delete_confirmation = <?php x__encode('plugin_base_delete_confirmation'); ?>;
myLabel.localeId = "<?php echo $controller->getLocaleId(); ?>";
myLabel.trigger_create = <?php echo $controller->_get->toInt('create'); ?>;
myLabel.has_create = <?php echo (int) $tpl['has_create']; ?>;
myLabel.has_update = <?php echo (int) $tpl['has_update']; ?>;
myLabel.has_delete = <?php echo (int) $tpl['has_delete']; ?>;
myLabel.has_delete_bulk = <?php echo (int) $tpl['has_delete_bulk']; ?>;
</script>
<?php if ($tpl['is_flag_ready']) : ?>
<script type="text/javascript">
var myLabel = myLabel || {};
var pjCmsLocale = pjCmsLocale || {};
pjCmsLocale.langs = <?php echo $tpl['locale_str']; ?>;
pjCmsLocale.flagPath = "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/";
</script>
<?php endif; ?>