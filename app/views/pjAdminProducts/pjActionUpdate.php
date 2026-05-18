<div class="row wrapper border-bottom white-bg page-heading sc-product-page-heading">
    <div class="col-sm-12">
        <h2><?php __('infoUpdateProductTitle');?></h2>
        <div class="product-page-heading__row clearfix">
            <p class="m-b-none pull-left"><i class="fa fa-info-circle"></i> <?php __('infoUpdateProductDesc');?></p>
            <?php if ($tpl['is_flag_ready']) : ?>
            <div class="btn-group-languages pull-right">
				<div class="multilang"></div>
			</div>
            <?php endif; ?>
        </div>
    </div><!-- /.col-sm-12 -->
</div>
<div class="row wrapper wrapper-content animated fadeInRight">
    <div class="col-lg-12">
    	<?php
    	$info = __('info', true);
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
                case in_array($error_code, array('AP04', 'AP05', 'AP08', 'AP09', 'AP10')):
                    $bodies_text = str_replace("{SIZE}", ini_get('upload_max_filesize'), @$bodies[$error_code]);
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
    	$active_tab = $controller->_get->check('tab') ? $controller->_get->toString('tab') : 'details';
    	?>
    	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProducts&amp;action=pjActionUpdate" method="post" id="frmUpdateProduct" class="frmProduct sc-product-update" enctype="multipart/form-data">
			<input type="hidden" name="product_update" value="1" />
			<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']?>" />
			<input type="hidden" name="tab" value="<?php echo $controller->_get->check('tab') ? $controller->_get->toString('tab') : 'details'; ?>" />
			<div class="tabs-container tabs-product-info m-b-lg">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="<?php echo $active_tab == 'details' ? 'active' : NULL;?>"><a class="tab-product-details" href="#product-details" aria-controls="product-details" role="tab" data-toggle="tab" data-tab="details"><?php __('product_details'); ?></a></li>
                    <li role="presentation" class="<?php echo $active_tab == 'digital' ? 'active' : NULL;?>"><a class="tab-product-digital" href="#product-digital" aria-controls="product-digital" role="tab" data-toggle="tab" data-tab="digital"><?php __('product_digital'); ?></a></li>
                    <li role="presentation" class="pjProductAttr <?php echo (int) $tpl['arr']['is_digital'] === 1 ? 'disabled' : '';?> <?php echo $active_tab == 'attr' ? 'active' : NULL;?>"><a class="tab-product-attr" href="#product-attr" aria-controls="product-attr" role="tab" data-toggle="tab"><?php __('product_attr'); ?></a></li>
                    <li role="presentation" class="<?php echo $active_tab == 'photos' ? 'active' : NULL;?>"><a class="tab-product-photos" href="#product-photos" aria-controls="product-photos" role="tab" data-toggle="tab" data-tab="photos"><?php __('product_photos'); ?></a></li>
                    <li role="presentation" class="<?php echo $active_tab == 'stock' ? 'active' : NULL;?>"><a class="tab-product-stock" href="#product-stock" aria-controls="product-stock" role="tab" data-toggle="tab" data-tab="stock"><?php __('product_stock'); ?></a></li>
                    <li role="presentation" class="<?php echo $active_tab == 'extras' ? 'active' : NULL;?>"><a class="tab-product-extras" href="#product-extras" aria-controls="product-extras" role="tab" data-toggle="tab" data-tab="extras"><?php __('product_extras'); ?></a></li>
                    <li role="presentation" class="<?php echo $active_tab == 'similar' ? 'active' : NULL;?>"><a class="tab-product-similar" href="#product-similar" aria-controls="product-similar" role="tab" data-toggle="tab" data-tab="similar"><?php __('product_similar'); ?></a></li>
                    <li role="presentation" class="<?php echo $active_tab == 'history' ? 'active' : NULL;?>"><a class="tab-product-history" href="#product-history" aria-controls="product-history" role="tab" data-toggle="tab" data-tab="history"><?php __('product_history'); ?></a></li>
                </ul>
    
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane <?php echo $active_tab == 'details' ? 'active' : NULL;?>" id="product-details">
                        <div class="panel-body">
                        	<div class="alert alert-success"><?php echo $info['product_details_body'];?></div>
                            <div class="adm-card">
                                <h4 class="adm-card__heading">
                                    <i class="fa fa-cube"></i>
                                    <?php __('product_details'); ?>
                                </h4>
                            <div class="row">
								<div class="col-md-6 col-sm-12 col-xs-12">
									<div class="form-group">
										<label class="control-label"><?php __('product_status'); ?></label>
									
										<select name="status" id="status" class="form-control required" data-msg-required="<?php __('pj_field_required', false, true);?>">
											<?php
											foreach (__('product_statuses', true) as $k => $v)
											{
												?><option value="<?php echo $k; ?>" <?php echo $tpl['arr']['status'] == $k ? 'selected="selected"' : '';?>><?php echo $v; ?></option><?php
											}
											?>
										</select>
									</div><!-- /.form-group -->
									<div class="form-group">
										<label class="control-label"><?php __('product_sku'); ?></label>
									
										<input name="sku" id="sku" value="<?php echo pjSanitize::html($tpl['arr']['sku']);?>" class="form-control required" data-msg-required="<?php __('pj_field_required', false, true);?>" data-msg-remote="<?php __('product_v_sku', false, true); ?>" />
									</div><!-- /.form-group -->

									<div class="form-group">
										<label class="control-label"><?php __('product_model'); ?></label>
									
										<input name="model" id="model" value="<?php echo pjSanitize::html($tpl['arr']['model']);?>" class="form-control required" data-msg-required="<?php __('pj_field_required', false, true);?>" data-msg-remote="<?php __('product_v_model', false, true); ?>" />
									</div><!-- /.form-group -->
									<div class="form-group">
										<label class="control-label"><?php __('product_model_name'); ?></label>
									
										<input name="model_name" id="model_name" value="<?php echo pjSanitize::html($tpl['arr']['model_name']);?>" class="form-control required" data-msg-required="<?php __('pj_field_required', false, true);?>" data-msg-remote="<?php __('product_v_model_name', false, true); ?>" />
									</div><!-- /.form-group -->
									<?php 
									foreach ($tpl['lp_arr'] as $v)
		                        	{
		                            	?>
		                                <div class="form-group pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 1 ? NULL : 'none'; ?>">
		                                    <label class="control-label"><?php __('product_name');?></label>
		                                                            
		                                    <div class="<?php echo $tpl['is_flag_ready'] ? 'input-group' : '';?>" data-index="<?php echo $v['id']; ?>">
												<input type="text" class="form-control<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" name="i18n[<?php echo $v['id']; ?>][name]" value="<?php echo pjSanitize::html(@$tpl['arr']['i18n'][$v['id']]['name']); ?>" data-msg-required="<?php __('pj_field_required', false, true);?>">	
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
													?><option value="<?php echo $category['data']['id']; ?>" <?php echo in_array($category['data']['id'], $tpl['pc_arr']) ? 'selected="selected"' : '';?>><?php echo str_repeat("-----", $category['deep']) . " " .pjSanitize::html($category['data']['name']); ?></option><?php
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
									 if(!empty($tpl['brand_arr']))
		                            {
		                                ?>
		                                <div class="form-group">
		                                    <label class="control-label"><?php __('product_brand'); ?></label>
		    
		                                    <select name="brand_id" id="brand_id"  class="form-control select-item   required" data-placeholder="-- <?php __('lblChoose'); ?> --" data-msg-required="<?php __('pj_field_required', false, true);?>">
		                						<?php
												foreach ($tpl['brand_arr'] as $brand)
												{
													?><option value="<?php echo $brand['data']['id']; ?>" <?php echo in_array($brand['data']['id'], $tpl['pb_arr']) ? 'selected="selected"' : '';?>><?php echo str_repeat("-----", $brand['deep']) . " " .pjSanitize::html($brand['data']['name']); ?></option><?php
												}
												?>
		                					</select>
		                                </div><!-- /.form-group -->
		                                <?php
		                            }else{
		                            	$add_brand = __('lblAddBrandText', true);
										$add_brand = str_replace("{STAG}", '<a href="'.$_SERVER['PHP_SELF'].'?controller=pjAdminBrands&amp;action=pjActionIndex&amp;create=1">', $add_brand);
										$add_brand = str_replace("{ETAG}", "</a>", $add_brand);
		                                ?>
		                                <div class="form-group">
		                                	<label class="control-label"><?php __('product_brand'); ?></label>
		                                    <p class="form-control-static"><?php echo $add_brand;?></p>
		                                    <input type="hidden" name="hidden_brand_id" id="hidden_brand_id" class="required" data-msg-required="<?php __('pj_field_required', false, true);?>"/>
		                                </div><!-- /.form-group -->
		                                <?php
		                            }
		                            ?>
		                            <div class="form-group">
										<label class="control-label"><?php __('product_is_featured'); ?></label>
									
										<div class="switch">
											<div class="onoffswitch onoffswitch-data">
												<input type="checkbox" class="onoffswitch-checkbox" name="is_featured" id="is_featured" <?php echo $tpl['arr']['is_featured'] == 1 ? 'checked="checked"' : '';?>>
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
		                                    	<textarea class="form-control form-control-lg <?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" name="i18n[<?php echo $v['id']; ?>][short_desc]" data-msg-required="<?php __('pj_field_required', false, true);?>"><?php echo pjSanitize::html(@$tpl['arr']['i18n'][$v['id']]['short_desc']); ?></textarea>
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
		                                    	<textarea class="form-control mceEditor" name="i18n[<?php echo $v['id']; ?>][full_desc]" data-msg-required="<?php __('pj_field_required', false, true);?>"><?php echo isset($tpl['arr']['i18n'][$v['id']]['full_desc']) && !empty($tpl['arr']['i18n'][$v['id']]['full_desc']) ? htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']]['full_desc'])) : ''; ?></textarea>
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
							</div><!-- /.adm-card -->
    
                            <div class="hr-line-dashed"></div>
    
                            <div class="clearfix">
                                <button type="submit" class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader pull-left" data-style="zoom-in" style="margin-right: 15px;">
                                    <span class="ladda-label"><?php __('btnSave'); ?></span>
                                    <?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
                                </button>
                                <a class="btn btn-white btn-lg pull-right" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminProducts&action=pjActionIndex"><?php __('btnCancel'); ?></a>
                            </div><!-- /.clearfix -->
                        </div>
                    </div>
    
                    <div role="tabpanel" class="tab-pane <?php echo $active_tab == 'digital' ? 'active' : NULL;?>" id="product-digital">
                        <div class="panel-body">
                            <div class="alert alert-success"><?php echo $info['product_digital_body'];?></div>
                            <div class="adm-card">
                                <h4 class="adm-card__heading">
                                    <i class="fa fa-cloud-download"></i>
                                    <?php __('product_digital'); ?>
                                </h4>
                            <div class="form-group">
								<label class="control-label"><?php __('product_is_digital'); ?></label>
							
								<div class="switch">
									<div class="onoffswitch onoffswitch-data onoffswitch-digital">
										<input type="checkbox" class="onoffswitch-checkbox" name="is_digital" id="is_digital" <?php echo $tpl['arr']['is_digital'] == 1 ? 'checked="checked"' : '';?>>
										<label class="onoffswitch-label" for="is_digital">
											<span class="onoffswitch-inner" data-on="<?php __('_yesno_ARRAY_T', false, true)?>" data-off="<?php __('_yesno_ARRAY_F', false, true)?>"></span>
											<span class="onoffswitch-switch"></span>
										</label>
									</div>
								</div>
							</div><!-- /.form-group -->
                            <div class="row" id="boxDigitalOuter" style="display:<?php echo (int) $tpl['arr']['is_digital'] === 1 ? 'block' : 'none';?>">                            	
                            	<?php if (!empty($tpl['arr']['digital_file'])) { ?>
                            		<div class="col-md-6 col-sm-6 col-xs-12">
	                            		<div class="form-group">
											<label class="control-label"><?php __('product_file'); ?></label>
											<div>
												<a href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminProducts&amp;action=pjActionOpenDigital&amp;id=<?php echo $tpl['arr']['id']; ?>" target="_blank"><?php echo pjSanitize::html($tpl['arr']['digital_name']); ?></a>
												<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProducts&amp;action=pjActionDeleteDigital&amp;id=<?php echo pjSanitize::html($tpl['arr']['id']);?>" class="btn btn-xs btn-danger btn-outline btnDigitalDelete" data-id="<?php echo pjSanitize::html($tpl['arr']['id']);?>"><i class="fa fa-trash"></i> <?php __('btnDelete'); ?></a>
											</div>
										</div><!-- /.form-group -->
	                            	</div>
                            	<?php } else { ?>
	                            	<div class="col-md-4 col-sm-6 col-xs-12">
	                            		<div class="form-group">
											<label class="control-label block">&nbsp;</label>
											<div>
												<input type="radio" class="i-checks" name="digital_choose" value="1" checked="checked"  /> <?php __('product_file_1');?>
												<input type="radio" class="i-checks" name="digital_choose" value="2"  /> <?php __('product_file_2');?>
											</div>
										</div><!-- /.form-group -->
	                            	</div>
	                            	<div class="col-md-4 col-sm-6 col-xs-12 digitalFile">
	                            		<div class="form-group">
											<label class="control-label"><?php __('product_file_1'); ?></label>
											<div>
												<input type="file" name="digital_file" class="form-control" />
											</div>
										</div><!-- /.form-group -->
	                            	</div>
	                            	<div class="col-md-4 col-sm-6 col-xs-12 digitalPath" style="display: none">
	                            		<div class="form-group">
											<label class="control-label"><?php __('product_file_2'); ?></label>
											<div>
												<input type="text" name="digital_file" class="form-control" maxlength="255" />
											</div>
										</div><!-- /.form-group -->
	                            	</div>
	                            <?php } ?>
	                            <div class="col-md-4 col-sm-6 col-xs-12">
                            		<div class="form-group">
										<label class="control-label block"><?php __('product_digital_expire'); ?></label>
										<?php
										$h = $m = NULL;
										if (!empty($tpl['arr']['digital_expire']))
										{
											list($h, $m,) = explode(":", $tpl['arr']['digital_expire']);
										}
										?>
										<span class="form-inline"><?php echo pjTime::factory()->prop('selected', $h)->attr('name', 'hour')->attr('id', 'hour')->attr('class', 'form-control')->hour(); ?></span>
										<span class="form-inline"><?php echo pjTime::factory()->prop('selected', $m)->attr('name', 'minute')->attr('id', 'minute')->attr('class', 'form-control')->prop('step', 5)->minute(); ?></span>
										<span>HH:MM</span>
									</div><!-- /.form-group -->
                            	</div>
                            </div>
                            </div><!-- /.adm-card -->
                            <div class="hr-line-dashed"></div>    						
    						
                            <div class="clearfix">
                                <button type="submit" class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader pull-left" data-style="zoom-in" style="margin-right: 15px;">
                                    <span class="ladda-label"><?php __('btnSave'); ?></span>
                                    <?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
                                </button>
                                <a class="btn btn-white btn-lg pull-right" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminProducts&action=pjActionIndex"><?php __('btnCancel'); ?></a>
                            </div><!-- /.clearfix -->
                        </div>
                    </div>
                    
                    <div role="tabpanel" class="tab-pane <?php echo $active_tab == 'attr' ? 'active' : NULL;?>" id="product-attr">
                        <div class="panel-body">
                            <div class="alert alert-success"><?php echo $info['product_attr_body'];?></div>
                            <div class="adm-card">
                                <h4 class="adm-card__heading">
                                    <i class="fa fa-tags"></i>
                                    <?php __('product_attr'); ?>
                                </h4>
                            <?php include_once dirname(__FILE__) . '/elements/attributes.php'; ?>
                            </div><!-- /.adm-card -->
                        </div>
                  	</div>
                  	
                  	<div role="tabpanel" class="tab-pane <?php echo $active_tab == 'photos' ? 'active' : NULL;?>" id="product-photos">
                        <div class="panel-body">
                            <div class="alert alert-success"><?php echo $info['product_photos_body'];?></div>
                            <div class="adm-card">
                                <h4 class="adm-card__heading">
                                    <i class="fa fa-picture-o"></i>
                                    <?php __('product_photos'); ?>
                                </h4>
                                <p class="text-muted small m-b-md"><?php __('lblProductEditPhotosLead'); ?></p>
                                <div id="gallery"></div>
                            </div><!-- /.adm-card -->
                        </div>
                  	</div>
                  	
                  	<div role="tabpanel" class="tab-pane <?php echo $active_tab == 'stock' ? 'active' : NULL;?>" id="product-stock">
                        <div class="panel-body">
                            <div class="alert alert-success"><?php echo $info['product_stock_body'];?></div>
                            <div class="adm-card">
                                <h4 class="adm-card__heading">
                                    <i class="fa fa-th"></i>
                                    <?php __('product_stock'); ?>
                                </h4>
                            <?php include_once dirname(__FILE__) . '/elements/stocks.php'; ?>
                            </div><!-- /.adm-card -->
                        </div>
                  	</div>
                  	
                  	<div role="tabpanel" class="tab-pane <?php echo $active_tab == 'extras' ? 'active' : NULL;?>" id="product-extras">
                        <div class="panel-body">
                          <div class="alert alert-success"><?php echo $info['product_extras_body'];?></div>
                            <div class="adm-card">
                                <h4 class="adm-card__heading">
                                    <i class="fa fa-plus-circle"></i>
                                    <?php __('product_extras'); ?>
                                </h4>
                            <?php include_once dirname(__FILE__) . '/elements/extras.php'; ?>
                            </div><!-- /.adm-card -->
                        </div>
                  	</div>
                  	
                  	<div role="tabpanel" class="tab-pane <?php echo $active_tab == 'similar' ? 'active' : NULL;?>" id="product-similar">
                        <div class="panel-body">
                        	<div class="adm-card">
                                <h4 class="adm-card__heading">
                                    <i class="fa fa-link"></i>
                                    <?php __('product_similar'); ?>
                                </h4>
	                            <div class="alert alert-success"><?php echo $info['product_similar_body'];?></div>
	                            <p class="text-muted small m-b-md"><?php __('lblProductSimilarSearchHelp'); ?></p>
	                            <div class="row form-group">
	                            	<div class="col-sm-6">
										<input type="text" name="similar_id" id="similar_id" class="form-control" placeholder="<?php __('btnSearch'); ?>" />
									</div>
								</div>
								<div id="boxSimilar" data-wrapper=".adm-card"></div>
							</div><!-- /.adm-card -->
                        </div>
                  	</div>
                  	
                  	<div role="tabpanel" class="tab-pane <?php echo $active_tab == 'history' ? 'active' : NULL;?>" id="product-history">
                        <div class="panel-body">
                            <div class="alert alert-success"><?php echo $info['product_history_body'];?></div>
                            <div class="adm-card">
                                <h4 class="adm-card__heading">
                                    <i class="fa fa-history"></i>
                                    <?php __('product_history'); ?>
                                </h4>
                            <div id="boxHistory"></div>
                            </div><!-- /.adm-card -->
                        </div>
                  	</div>
                    
                </div>
            </div>
        </form>
    </div><!-- /.col-lg-8 -->

</div><!-- /.wrapper wrapper-content -->

<div class="modal fade" id="modalCopyAttr" tabindex="-1" role="dialog" aria-labelledby="myCopyAttributesLabel">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myCopyAttributesLabel"><?php __('product_attr_copy_title');?></h4>
      </div>
      <div class="modal-body">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php __('btnClose');?></button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="modalCopyExtra" tabindex="-1" role="dialog" aria-labelledby="myCopyAttributesLabel">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myCopyAttributesLabel"><?php __('product_extra_copy_title');?></h4>
      </div>
      <div class="modal-body">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php __('btnClose');?></button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalImageStock" tabindex="-1" role="dialog" aria-labelledby="myImageStockibutesLabel">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myImageStockibutesLabel"><?php __('product_stock_img_title');?></h4>
      </div>
      <div class="modal-body">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php __('btnClose');?></button>
      </div>
    </div>
  </div>
</div>
	
<?php
include_once dirname(__FILE__) . '/elements/attributes_other.php';
include_once dirname(__FILE__) . '/elements/stocks_other.php';
include_once dirname(__FILE__) . '/elements/extras_other.php';
?>
	
<style>
.pj-form-langbar-item{
	background: none !important;
	width: auto !important;
	height: auto !important;
	margin: auto !important;
}
.pjBaseTheme-theme1 .pj-form-langbar-item.btn-primary:focus,
.pjBaseTheme-theme1 .pj-form-langbar-item.btn-primary{ background: #36703E !important;}
.pjBaseTheme-theme2 .pj-form-langbar-item.btn-primary:focus,
.pjBaseTheme-theme2 .pj-form-langbar-item.btn-primary{ background: #621a77 !important;}
.pjBaseTheme-theme3 .pj-form-langbar-item.btn-primary:focus,
.pjBaseTheme-theme3 .pj-form-langbar-item.btn-primary{ background: #dd3d25 !important;}
</style>

<script type="text/javascript">
var myLabel = myLabel || {};
myLabel.localeId = "<?php echo $controller->getLocaleId(); ?>";
myLabel.alert_del_digital_title = <?php x__encode('product_digital_delete_title'); ?>;
myLabel.alert_del_digital_text = <?php x__encode('product_digital_delete_desc'); ?>;
myLabel.alert_del_attr_group_title = <?php x__encode('product_attr_group_delete'); ?>;
myLabel.alert_del_attr_group_text = <?php x__encode('product_attr_group_delete_body'); ?>;
myLabel.alert_del_attr_title = <?php x__encode('product_attr_erase'); ?>;
myLabel.alert_del_attr_text = <?php x__encode('product_attr_delete_body'); ?>;
myLabel.alert_del_stock_title = <?php x__encode('product_stock_delete_title'); ?>;
myLabel.alert_del_stock_text = <?php x__encode('product_stock_delete_desc'); ?>;
myLabel.alert_overlapping_attributes_title = <?php x__encode('product_overlapping_attributes_title'); ?>;
myLabel.alert_overlapping_attributes_text = <?php x__encode('product_overlapping_attributes_desc'); ?>;
myLabel.alert_del_extra_title = <?php x__encode('product_extra_delete_title'); ?>;
myLabel.alert_del_extra_text = <?php x__encode('product_extra_delete_desc'); ?>;
myLabel.btn_delete = <?php x__encode('btnDelete'); ?>;
myLabel.btn_cancel = <?php x__encode('btnCancel'); ?>;
myLabel.btn_close = <?php x__encode('btnClose'); ?>;
myLabel.no_extras = "<?php echo __('lblNoExtrasFound', true) . '<br/><br/>'; ?>";
myLabel.no_attrs = "<?php echo __('lblNoAttributesFound', true) . '<br/><br/>'; ?>";
myLabel.name = <?php x__encode('lblName'); ?>;
myLabel.sku = <?php x__encode('product_sku'); ?>;
myLabel.status = <?php x__encode('lblStatus'); ?>;
myLabel.active = "<?php echo $product_statuses[1]; ?>";
myLabel.inactive = "<?php echo $product_statuses[2]; ?>";
myLabel.delete_selected = <?php x__encode('delete_selected'); ?>;
myLabel.delete_confirmation = <?php x__encode('delete_confirmation'); ?>;

myLabel.has_update = <?php echo (int) $tpl['has_update']; ?>;

var myGallery = myGallery || {};
myGallery.foreign_id = <?php echo $tpl['arr']['id']; ?>;
myGallery.hash = "";
<?php if ($tpl['is_flag_ready']) : ?>
var pjCmsLocale = pjCmsLocale || {};
pjCmsLocale.langs = <?php echo $tpl['locale_str']; ?>;
pjCmsLocale.flagPath = "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/";
<?php endif; ?>
</script>