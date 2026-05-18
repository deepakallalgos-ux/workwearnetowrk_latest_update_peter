<div id="boxClone" style="display: none;">
	<div class="table-responsive table-responsive-secondary extraBox">
		<table class="table table-striped table-hover tblExtras">
			<tbody>
				<tr>
					<td><?php __('product_extra_type'); ?></td>
					<td><?php __('product_extra_mandatory'); ?></td>
					<td>
						<span class="boxSingle"><?php __('product_extra_name'); ?></span>
						<span class="boxMulti" style="display: none"><?php __('product_extra_title'); ?></span>
					</td>
					<td><span class="boxSingle"><?php __('product_extra_price'); ?></span></td>
				</tr>
				<tr>
					<td>
						<div class="form-group">
							<select name="extra_type[{INDEX}]" class="form-control" data-msg-required="<?php __('pj_field_required', false, true);?>">
								<?php
								$product_extra_types = __('product_extra_types', true);
								krsort($product_extra_types);
								foreach ($product_extra_types as $k => $v)
								{
									?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
								}
								?>
							</select>
						</div>
					</td>
					<td>
						<div class="form-group">
							<input type="checkbox" class="i-checks-{INDEX}" name="extra_is_mandatory[{INDEX}]" value="1" />
						</div>
					</td>
					<td class="tdExtrasClean" colspan="2">
						<div class="table-responsive table-responsive-secondary boxSingle">
							<table class="table table-striped table-hover">
								<tbody>
									<tr>
										<td>
											<?php
											foreach ($tpl['lp_arr'] as $v)
											{
												?>
												<div class="form-group pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 1 ? NULL : 'none'; ?>">					
													<div class="<?php echo $tpl['is_flag_ready'] ? 'input-group' : '';?>" data-index="<?php echo $v['id']; ?>">
														<input type="text" class="form-control<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" name="i18n[<?php echo $v['id']; ?>][extra_name][{INDEX}]" data-msg-required="<?php __('pj_field_required', false, true);?>" >	
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
											<div class="form-group">
							                    <div class="input-group">
							                        <input type="text" name="extra_price[{INDEX}]" class="form-control required number" data-msg-required="<?php __('pj_field_required', false, true);?>" data-msg-number="<?php __('pj_field_number');?>"/>
							    
							                        <span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']); ?></span> 
							                    </div>
							            	</div>
										</td>
										<td>
											<div class="form-group">
												<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-danger btn-outline btn-sm btnDeleteExtraTmp" title="<?php __('product_extra_delete'); ?>"><i class="fa fa-trash"></i></a>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="table-responsive table-responsive-secondary boxMulti" style="display: none;">
							<table class="table table-striped table-hover">
								<tbody>
									<tr>
										<td>
											<?php
											foreach ($tpl['lp_arr'] as $v)
											{
												?>
												<div class="form-group pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 1 ? NULL : 'none'; ?>">					
													<div class="<?php echo $tpl['is_flag_ready'] ? 'input-group' : '';?>" data-index="<?php echo $v['id']; ?>">
														<input type="text" class="form-control<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" name="i18n[<?php echo $v['id']; ?>][extra_title][{INDEX}]" data-msg-required="<?php __('pj_field_required', false, true);?>" disabled="disabled" >	
														<?php if ($tpl['is_flag_ready']) : ?>
														<span class="input-group-addon pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="<?php echo pjSanitize::html($v['name']); ?>"></span>
														<?php endif; ?>
													</div>
												</div>
												<?php
											}
											?>
										</td>
										<td>&nbsp;</td>
										<td>
											<div class="form-group">
												<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-danger btn-outline btn-sm btnDeleteExtraTmp" title="<?php __('product_extra_delete'); ?>"><i class="fa fa-trash"></i></a>
											</div>
										</td>
									</tr>
									<tr>
										<td><?php __('product_extra_name'); ?></td>
										<td><?php __('product_extra_price'); ?></td>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td>
											<?php
											foreach ($tpl['lp_arr'] as $v)
											{
												?>
												<div class="form-group pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 1 ? NULL : 'none'; ?>">					
													<div class="<?php echo $tpl['is_flag_ready'] ? 'input-group' : '';?>" data-index="<?php echo $v['id']; ?>">
														<input type="text" class="form-control<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" name="i18n[<?php echo $v['id']; ?>][extra_name][{INDEX}][{X}]" data-msg-required="<?php __('pj_field_required', false, true);?>" disabled="disabled" >	
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
											<div class="form-group">
							                    <div class="input-group">
							                        <input type="text" name="extra_price[{INDEX}][{X}]" disabled="disabled" class="form-control required number" data-msg-required="<?php __('pj_field_required', false, true);?>" data-msg-number="<?php __('pj_field_number');?>"/>
							    
							                        <span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']); ?></span> 
							                    </div>
							            	</div>
										</td>
										<td>
											<div class="form-group">
												<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-danger btn-outline btn-sm btnRemoveExtraItem"><i class="fa fa-trash"></i></a>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
							<input type="button" class="btn btn-primary btn-outline btn-sm btnAddExtraItem" data-index="{INDEX}" value="<?php __('product_extra_item_add'); ?>" />
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	
	</div>
</div>

<table id="boxCloneTbl" style="display: none">
	<tbody>
		<tr>
			<td>
				<?php
				foreach ($tpl['lp_arr'] as $v)
				{
					?>
					<div class="form-group pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 1 ? NULL : 'none'; ?>">					
						<div class="<?php echo $tpl['is_flag_ready'] ? 'input-group' : '';?>" data-index="<?php echo $v['id']; ?>">
							<input type="text" class="form-control<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" name="i18n[<?php echo $v['id']; ?>][extra_name][{INDEX}][{X}]" data-msg-required="<?php __('pj_field_required', false, true);?>" >	
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
				<div class="form-group">
                    <div class="input-group">
                        <input type="text" name="extra_price[{INDEX}][{X}]" class="form-control required number" data-msg-required="<?php __('pj_field_required', false, true);?>" data-msg-number="<?php __('pj_field_number');?>"/>
    
                        <span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']); ?></span> 
                    </div>
            	</div>
			</td>
			<td>
				<div class="form-group">
					<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-danger btn-outline btn-sm btnRemoveExtraItem"><i class="fa fa-trash"></i></a>
				</div>
			</td>
		</tr>
	</tbody>
</table>