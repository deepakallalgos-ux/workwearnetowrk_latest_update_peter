<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <div class="row">
        	<div class="col-lg-9 col-md-8 col-sm-6">
                <h2><?php __('infoBrandsTitle');?></h2>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 btn-group-languages">
                <?php if ($tpl['is_flag_ready']) : ?>
				<div class="multilang"></div>
				<?php endif; ?>
        	</div>
        </div><!-- /.row -->

        <p class="m-b-none"><i class="fa fa-info-circle"></i> <?php __('infoBrandsDesc', false, true);?></p>
    </div><!-- /.col-md-12 -->
</div>

<div class="row wrapper wrapper-content animated fadeInRight">
    <div class="col-lg-8">
        <div class="ibox float-e-margins">
            <div class="ibox-content">
            	<?php if ($tpl['has_create']) { ?>
	                <div class="row">
	                	<div class="col-md-12">
	                    	<a href="#" class="btn btn-primary pjScAddBrand"><i class="fa fa-plus"></i> <?php __('btnAddBrand') ?></a>
	                    </div><!-- /.col-md-6 -->
	                </div><!-- /.row -->
                <?php } ?>
                <div id="grid"></div>
                
            </div>
        </div>
    </div><!-- /.col-lg-8 -->

    <div class="col-lg-4">
        <div id="pjScFormWrapper" class="panel no-borders">
        	
        </div><!-- /.panel panel-primary -->
    </div><!-- /.col-lg-3 -->
</div>
<script type="text/javascript">
var myLabel = myLabel || {};
myLabel.brand_name = <?php x__encode('brand_name'); ?>;
myLabel.products = <?php x__encode('brand_products'); ?>;
myLabel.down = <?php x__encode('_down'); ?>;
myLabel.up = <?php x__encode('_up'); ?>;
myLabel.delete_selected = <?php x__encode('delete_selected'); ?>;
myLabel.delete_confirmation = <?php x__encode('delete_confirmation'); ?>;
myLabel.localeId = "<?php echo $controller->getLocaleId(); ?>";
myLabel.trigger_create = <?php echo $controller->_get->toInt('create'); ?>;

myLabel.has_create = <?php echo (int) $tpl['has_create']; ?>;
myLabel.has_update = <?php echo (int) $tpl['has_update']; ?>;
myLabel.has_delete = <?php echo (int) $tpl['has_delete']; ?>;
myLabel.has_delete_bulk = <?php echo (int) $tpl['has_delete_bulk']; ?>;

<?php if ($tpl['is_flag_ready']) : ?>
var pjCmsLocale = pjCmsLocale || {};
pjCmsLocale.langs = <?php echo $tpl['locale_str']; ?>;
pjCmsLocale.flagPath = "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/";
<?php endif; ?>
</script>