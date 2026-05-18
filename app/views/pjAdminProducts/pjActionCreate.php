<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-lg-9 col-md-8 col-sm-6">
                <h2><?php __('infoAddProductTitle');?></h2>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 btn-group-languages">
                <?php if ($tpl['is_flag_ready']) : ?>
				<div class="multilang"></div>
				<?php endif; ?>
        	</div>
        </div><!-- /.row -->

        <p class="m-b-none"><i class="fa fa-info-circle"></i><?php __('infoAddProductDesc');?></p>
    </div><!-- /.col-md-12 -->
</div>

<div class="row wrapper wrapper-content animated fadeInRight">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-content">
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProducts&amp;action=pjActionCreate" method="post" id="frmCreateProduct" autocomplete="off" class="frmProduct" enctype="multipart/form-data">
            		<input type="hidden" name="product_create" value="1" />
                    
					<div class="row">
						<div class="col-md-6 col-sm-12 col-xs-12">
							<div class="form-group">
								<label class="control-label"><?php __('product_status'); ?></label>
							
								<select name="status" id="status" class="form-control required" data-msg-required="<?php __('pj_field_required', false, true);?>">
									<?php
									foreach (__('product_statuses', true) as $k => $v)
									{
										?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
									}
									?>
								</select>
							</div><!-- /.form-group -->
							<div class="form-group">
								<label class="control-label"><?php __('product_sku'); ?></label>
							
								<input name="sku" id="sku" class="form-control required" data-msg-required="<?php __('pj_field_required', false, true);?>" data-msg-remote="<?php __('product_v_sku', false, true); ?>" />
							</div><!-- /.form-group -->
							<div class="form-group">

								<label class="control-label"><?php __('product_model'); ?></label>
							
								<input name="model" id="model"  class="form-control required" data-msg-required="<?php __('pj_field_required', false, true);?>" />
							</div><!-- /.form-group -->
							<div class="form-group">
								<label class="control-label"><?php __('product_model_name'); ?></label>
							
								<input name="model_name" id="model_name" class="form-control required" data-msg-required="<?php __('pj_field_required', false, true);?>"  />
							</div><!-- /.form-group -->
							<?php 
							foreach ($tpl['lp_arr'] as $v)
                        	{
                            	?>
                                <div class="form-group pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 1 ? NULL : 'none'; ?>">
                                    <label class="control-label"><?php __('product_name');?></label>
                                                            
                                    <div class="<?php echo $tpl['is_flag_ready'] ? 'input-group' : '';?>" data-index="<?php echo $v['id']; ?>">
										<input type="text" class="form-control<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" name="i18n[<?php echo $v['id']; ?>][name]" data-msg-required="<?php __('pj_field_required', false, true);?>">	
										<?php if ($tpl['is_flag_ready']) : ?>
										<span class="input-group-addon pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="<?php echo pjSanitize::html($v['name']); ?>"></span>
										<?php endif; ?>
									</div>
                                </div>
                                <?php
                            }
                            if(!empty($tpl['category_arr']))
                            {
                                ?>
                                <div class="form-group">
                                    <label class="control-label"><?php __('product_category'); ?></label>
    
                                    <select name="category_id[]" id="category_id" multiple="multiple" class="form-control select-item select2-hidden-accessible required" data-placeholder="-- <?php __('lblChoose'); ?> --" data-msg-required="<?php __('pj_field_required', false, true);?>">
                						<?php
										foreach ($tpl['category_arr'] as $category)
										{
											?><option value="<?php echo $category['data']['id']; ?>"><?php echo str_repeat("-----", $category['deep']) . " " .pjSanitize::html($category['data']['name']); ?></option><?php
										}
										?>
                					</select>
                                </div><!-- /.form-group -->
                                <?php
                            }else{
                            	$add_category = __('lblAddCategoryText', true);
								$add_category = str_replace("{STAG}", '<a href="'.$_SERVER['PHP_SELF'].'?controller=pjAdminCategories&amp;action=pjActionIndex&amp;create=1">', $add_category);
								$add_category = str_replace("{ETAG}", "</a>", $add_category);
                                ?>
                                <div class="form-group">
                                	<label class="control-label"><?php __('product_category'); ?></label>
                                    <p class="form-control-static"><?php echo $add_category;?></p>
                                    <input type="hidden" name="hidden_category_id" id="hidden_category_id" class="required" data-msg-required="<?php __('pj_field_required', false, true);?>"/>
                                </div><!-- /.form-group -->
                                <?php
                            }
                            ?>
                            <div class="form-group">
								<label class="control-label"><?php __('product_is_featured'); ?></label>
							
								<div class="switch">
									<div class="onoffswitch onoffswitch-data">
										<input type="checkbox" class="onoffswitch-checkbox" name="is_featured" id="is_featured">
										<label class="onoffswitch-label" for="is_featured">
											<span class="onoffswitch-inner" data-on="<?php __('_yesno_ARRAY_T', false, true)?>" data-off="<?php __('_yesno_ARRAY_F', false, true)?>"></span>
											<span class="onoffswitch-switch"></span>
										</label>
									</div>
								</div>
							</div><!-- /.form-group -->
                            <?php 
                            foreach ($tpl['lp_arr'] as $v)
                        	{
                            	?>
                                <div class="form-group pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 1 ? NULL : 'none'; ?>">
                                    <label class="control-label"><?php __('product_short_desc');?></label>
                                                            
                                    <div class="<?php echo $tpl['is_flag_ready'] ? 'input-group' : '';?>" data-index="<?php echo $v['id']; ?>">
                                    	<textarea class="form-control form-control-lg <?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" name="i18n[<?php echo $v['id']; ?>][short_desc]" data-msg-required="<?php __('pj_field_required', false, true);?>"></textarea>
										<?php if ($tpl['is_flag_ready']) : ?>
										<span class="input-group-addon pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="<?php echo pjSanitize::html($v['name']); ?>"></span>
										<?php endif; ?>
									</div>
                                </div>
                                <?php
                            }
							?>
						</div>
						<div class="col-md-6 col-sm-12 col-xs-12">
							<?php 
							foreach ($tpl['lp_arr'] as $v)
                        	{
                            	?>
                                <div class="form-group pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 1 ? NULL : 'none'; ?>">
                                    <label class="control-label"><?php __('product_full_desc');?></label>
                                                            
                                    <div class="<?php echo $tpl['is_flag_ready'] ? 'input-group' : '';?>" data-index="<?php echo $v['id']; ?>">
                                    	<textarea class="form-control mceEditor" name="i18n[<?php echo $v['id']; ?>][full_desc]" data-msg-required="<?php __('pj_field_required', false, true);?>"></textarea>
										<?php if ($tpl['is_flag_ready']) : ?>
										<span class="input-group-addon pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="<?php echo pjSanitize::html($v['name']); ?>"></span>
										<?php endif; ?>
									</div>
                                </div>
                                <?php
                            }
							?>
						</div>
					</div>
					
                    <div class="hr-line-dashed"></div>

                    <div class="clearfix">
                        <button type="submit" class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader pull-left" data-style="zoom-in" style="margin-right: 15px;">
                            <span class="ladda-label"><?php __('btnSave'); ?></span>
                            <?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
                        </button>
                        <a class="btn btn-white btn-lg pull-right" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminProducts&action=pjActionIndex"><?php __('btnCancel'); ?></a>
                    </div><!-- /.clearfix -->
                </form>
            </div>
        </div>
    </div><!-- /.col-lg-12 -->
</div>
<script type="text/javascript">
var myLabel = myLabel || {};
myLabel.localeId = "<?php echo $controller->getLocaleId(); ?>";
<?php if ($tpl['is_flag_ready']) : ?>
var pjCmsLocale = pjCmsLocale || {};
pjCmsLocale.langs = <?php echo $tpl['locale_str']; ?>;
pjCmsLocale.flagPath = "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/";
<?php endif; ?>
</script>