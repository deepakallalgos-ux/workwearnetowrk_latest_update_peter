<?php
if (isset($tpl['attr_arr']) && count($tpl['attr_arr']) > 0)
{
	foreach ($tpl['attr_arr'] as $attr)
	{
		$x = $attr['id'];
		if (isset($init))
		{
			mt_srand();
			$x = 'x_' . mt_rand(0, 999999);
		}
		?>
		<div id="attrBox_<?php echo $attr['id']; ?>" class="attrBox">
			<input type="hidden" name="attr[<?php echo $attr['id']; ?>]" value="1" />
			<div class="attrBoxRow" style="position: relative;">
				<div class="row form-group">
					<div class="col-md-3 col-sm-4 col-xs-12">
						<label class="control-label"><?php __('product_attr_group_name');?></label>
					</div>
					<div class="col-md-6 col-sm-6 col-xs-7">
						<?php
						foreach ($tpl['lp_arr'] as $v)
						{
							?>
							<div class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 1 ? NULL : 'none'; ?>">					
								<div class="<?php echo $tpl['is_flag_ready'] ? 'input-group' : '';?>" data-index="<?php echo $v['id']; ?>">
									<input type="text" class="form-control<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" name="i18n[<?php echo $v['id']; ?>][attr_group][<?php echo $attr['id']; ?>]" data-msg-required="<?php __('pj_field_required', false, true);?>" value="<?php echo pjSanitize::html($attr['i18n'][$v['id']]['name']); ?>" >	
									<?php if ($tpl['is_flag_ready']) : ?>
									<span class="input-group-addon pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="<?php echo pjSanitize::html($v['name']); ?>"></span>
									<?php endif; ?>
								</div>
							</div>
							<?php
						}
						?>
					</div>
					<div class="col-md-3 col-sm-6 col-xs-5 text-right">
						<a href="javascript:void(0);" class="btn btn-danger btn-outline btn-sm btnAttrGroupDelete" data-id="<?php echo $attr['id']; ?>"><i class="fa fa-trash"></i></a>
						<a href="javascript:void(0);" class="btn btn-success btn-outline btn-sm group-move-icon"><i class="fa fa-arrows"></i></a>
					</div>
				</div>
			</div>
			<input type="hidden" id="orderItems_<?php echo $attr['id']; ?>" name="orderItems_<?php echo $attr['id']; ?>" value="" class="w300"/>
			<div class="attrBoxRow">
				<div class="row form-group">
					<div class="col-md-3 col-sm-4 col-xs-12">
						<label class="control-label"><?php __('product_attr_name');?></label>
					</div>
					<div class="col-md-9 col-sm-8 col-xs-12">
						<div id="attrBoxRowStick_<?php echo $attr['id']; ?>" class="attrBoxRowStick row" data-id="<?php echo $attr['id']; ?>">
						<?php
						if (isset($attr['child']) && !empty($attr['child']))
						{
							foreach ($attr['child'] as $k => $child)
							{
								$y = $child['id'];
								if (isset($init))
								{
									mt_srand();
									$y = 'y_' . mt_rand(0, 999999);
								}
								?>
								<div id="attrBoxRowItems_<?php echo $child['id']; ?>" class="col-sm-6 attrBoxRowItems">
									<?php
									foreach ($tpl['lp_arr'] as $v)
									{
										?>
										<div class="form-group pj-multilang-wrap pjScAttrItem" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 1 ? NULL : 'none'; ?>">					
											<div class="<?php echo $tpl['is_flag_ready'] ? 'input-group' : '';?>" data-index="<?php echo $v['id']; ?>">
												<input type="text" class="form-control<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" name="i18n[<?php echo $v['id']; ?>][attr_item][<?php echo $attr['id']; ?>][<?php echo $child['id']; ?>]" value="<?php echo pjSanitize::html($child['i18n'][$v['id']]['name']); ?>" data-msg-required="<?php __('pj_field_required', false, true);?>">	
												<?php if ($tpl['is_flag_ready']) : ?>
												<span class="input-group-addon pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="<?php echo pjSanitize::html($v['name']); ?>"></span>
												<?php endif; ?>
											</div>
										</div>
										<?php
									}
									if($k > 0)
									{ 
										?><a href="#" class="btn btn-danger btn-outline btn-sm btnAttrDelete" data-id="<?php echo $child['id']; ?>"><i class="fa fa-trash"></i></a><?php
									} 
									?>
									<a href="javascript:void(0);" class="btn btn-success btn-outline btn-sm item-move-icon"><i class="fa fa-arrows"></i></a>
								</div>
								<?php
							}
						}
						?>
						</div>
					</div>
				</div>
			</div>
			<div class="row form-group">
				<div class="col-md-3 col-sm-4 col-xs-12">
					<label class="control-label">&nbsp;</label>
				</div>
				<div class="col-md-9 col-sm-8 col-xs-12"><a href="javascript:void(0);" class="btn btn-primary btn-outline btn-sm btnAddAttr" rel="<?php echo $attr['id']; ?>"><?php __('product_attr_create'); ?></a></div>
			</div>
		</div>
		<?php
	}
}else{
	echo __('lblNoAttributesFound', true) . '<br/><br/>';
}
?>