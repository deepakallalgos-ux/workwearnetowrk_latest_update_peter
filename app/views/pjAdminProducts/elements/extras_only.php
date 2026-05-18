<?php
if (isset($tpl['extra_arr']) && count($tpl['extra_arr']) > 0)
{
	$product_extra_types = __('product_extra_types', true);
	krsort($product_extra_types);
	foreach ($tpl['extra_arr'] as $extra)
	{
		switch ($extra['type'])
		{
			case 'single':
				?>
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
										<select name="extra_type[<?php echo $extra['id']; ?>]" class="form-control required" data-msg-required="<?php __('pj_field_required', false, true);?>">
											<?php
											$product_extra_types = __('product_extra_types', true);
											krsort($product_extra_types);
											foreach ($product_extra_types as $k => $v)
											{
												?><option value="<?php echo $k; ?>"<?php echo $k == $extra['type'] ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option><?php
											}
											?>
										</select>
									</div>
								</td>
								<td>
									<div class="form-group">
										<input type="checkbox" class="i-checks" name="extra_is_mandatory[<?php echo $extra['id']; ?>]" value="1"<?php echo (int) $extra['is_mandatory'] === 1 ? ' checked="checked"' : NULL; ?> />
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
																	<input type="text" class="form-control<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" name="i18n[<?php echo $v['id']; ?>][extra_name][<?php echo $extra['id']; ?>]" data-msg-required="<?php __('pj_field_required', false, true);?>" value="<?php echo pjSanitize::html(@$extra['i18n'][$v['id']]['extra_name']); ?>" >	
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
										                        <input type="text" name="extra_price[<?php echo $extra['id']; ?>]" value="<?php echo (float) $extra['price']; ?>" class="form-control required number" data-msg-required="<?php __('pj_field_required', false, true);?>" data-msg-number="<?php __('pj_field_number');?>"/>
										    
										                        <span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']); ?></span> 
										                    </div>
										            	</div>
													</td>
													<td>
														<div class="form-group">
															<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-danger btn-outline btn-sm btnDeleteExtra" rel="<?php echo $extra['id']; ?>" title="<?php __('product_extra_delete'); ?>"><i class="fa fa-trash"></i></a>
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
																	<input type="text" class="form-control<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" disabled="disabled" name="i18n[<?php echo $v['id']; ?>][extra_title][<?php echo $extra['id']; ?>]" data-msg-required="<?php __('pj_field_required', false, true);?>" >	
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
													<td><a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-danger btn-outline btn-sm btnDeleteExtra" rel="<?php echo $extra['id']; ?>" title="<?php __('product_extra_delete'); ?>"><i class="fa fa-trash"></i></a></td>
												</tr>
												<tr>
													<td><?php __('product_extra_name'); ?></td>
													<td><?php __('product_extra_price'); ?></td>
													<td>&nbsp;</td>
												</tr>
												<tr>
													<td>
														<?php
														mt_srand();
														$rand = mt_rand(0, 999999);
														foreach ($tpl['lp_arr'] as $v)
														{
															?>
															<div class="form-group pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 1 ? NULL : 'none'; ?>">					
																<div class="<?php echo $tpl['is_flag_ready'] ? 'input-group' : '';?>" data-index="<?php echo $v['id']; ?>">
																	<input type="text" class="form-control<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" disabled="disabled" name="i18n[<?php echo $v['id']; ?>][extra_name][<?php echo $extra['id']; ?>][y_<?php echo $rand; ?>]" data-msg-required="<?php __('pj_field_required', false, true);?>" >	
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
										                        <input type="text" name="extra_price[<?php echo $extra['id']; ?>][y_<?php echo $rand; ?>]" disabled="disabled" class="form-control required number" data-msg-required="<?php __('pj_field_required', false, true);?>" data-msg-number="<?php __('pj_field_number');?>"/>
										    
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
										<input type="button" class="btn btn-primary btn-outline btn-sm btnAddExtraItem" data-index="<?php echo $extra['id']; ?>" value="<?php __('product_extra_item_add'); ?>" />
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<?php
				break;
			case 'multi':
				?>
				<div class="table-responsive table-responsive-secondary extraBox">
					<table class="table table-striped table-hover tblExtras">
						<tbody>
							<tr>
								<td><?php __('product_extra_type'); ?></td>
								<td><?php __('product_extra_mandatory'); ?></td>
								<td>
									<span class="boxSingle" style="display: none"><?php __('product_extra_name'); ?></span>
									<span class="boxMulti"><?php __('product_extra_title'); ?></span>
								</td>
								<td><span class="boxSingle" style="display: none"><?php __('product_extra_price'); ?></span></td>
							</tr>
							<tr>
								<td>
									<div class="form-group">
										<select name="extra_type[<?php echo $extra['id']; ?>]" class="form-control" data-msg-required="<?php __('pj_field_required', false, true);?>">
											<?php
											$product_extra_types = __('product_extra_types', true);
											krsort($product_extra_types);
											foreach ($product_extra_types as $k => $v)
											{
												?><option value="<?php echo $k; ?>"<?php echo $k == $extra['type'] ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option><?php
											}
											?>
										</select>
									</div>
								</td>
								<td>
									<div class="form-group">
										<input type="checkbox" class="i-checks" name="extra_is_mandatory[<?php echo $extra['id']; ?>]" value="1"<?php echo (int) $extra['is_mandatory'] === 1 ? ' checked="checked"' : NULL; ?> />
									</div>
								</td>
								<td class="tdExtrasClean" colspan="2">
									<div class="table-responsive table-responsive-secondary boxSingle" style="display: none;">
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
																	<input type="text" class="form-control<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" disabled="disabled" name="i18n[<?php echo $v['id']; ?>][extra_name][<?php echo $extra['id']; ?>]" data-msg-required="<?php __('pj_field_required', false, true);?>" >	
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
										                        <input type="text" name="extra_price[<?php echo $extra['id']; ?>]" class="form-control required number" disabled="disabled" data-msg-required="<?php __('pj_field_required', false, true);?>" data-msg-number="<?php __('pj_field_number');?>"/>
										    
										                        <span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']); ?></span> 
										                    </div>
										            	</div>
													</td>
													<td>
														<div class="form-group">
															<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-danger btn-outline btn-sm btnDeleteExtra" rel="<?php echo $extra['id']; ?>" title="<?php __('product_extra_delete'); ?>"><i class="fa fa-trash"></i></a>
														</div>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
									<div class="table-responsive table-responsive-secondary boxMulti">
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
																	<input type="text" class="form-control<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" name="i18n[<?php echo $v['id']; ?>][extra_title][<?php echo $extra['id']; ?>]" data-msg-required="<?php __('pj_field_required', false, true);?>" value="<?php echo pjSanitize::html(@$extra['i18n'][$v['id']]['extra_title']); ?>" >	
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
															<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-danger btn-outline btn-sm btnDeleteExtra" rel="<?php echo $extra['id']; ?>" title="<?php __('product_extra_delete'); ?>"><i class="fa fa-trash"></i></a>
														</div>
													</td>
												</tr>
												<tr>
													<td><?php __('product_extra_name'); ?></td>
													<td><?php __('product_extra_price'); ?></td>
													<td>&nbsp;</td>
												</tr>
												<?php
												foreach ($extra['extra_items'] as $item)
												{
													?>
													<tr>
														<td>
															<?php
															foreach ($tpl['lp_arr'] as $v)
															{
																?>
																<div class="form-group pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 1 ? NULL : 'none'; ?>">					
																	<div class="<?php echo $tpl['is_flag_ready'] ? 'input-group' : '';?>" data-index="<?php echo $v['id']; ?>">
																		<input type="text" class="form-control<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" name="i18n[<?php echo $v['id']; ?>][extra_name][<?php echo $extra['id']; ?>][<?php echo $item['id']; ?>]" data-msg-required="<?php __('pj_field_required', false, true);?>" value="<?php echo pjSanitize::html(@$item['i18n'][$v['id']]['extra_name']); ?>" >	
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
											                        <input type="text" name="extra_price[<?php echo $extra['id']; ?>][<?php echo $item['id']; ?>]" value="<?php echo (float) $item['price']; ?>" class="form-control required number" data-msg-required="<?php __('pj_field_required', false, true);?>" data-msg-number="<?php __('pj_field_number');?>"/>
											    
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
													<?php
												}
												?>
											</tbody>
										</table>
										<input type="button" class="btn btn-primary btn-outline btn-sm btnAddExtraItem" data-index="<?php echo $extra['id']; ?>" value="<?php __('product_extra_item_add'); ?>" />
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<?php
				break;
		}
	}
}else{
	echo __('lblNoExtrasFound', true) . '<br/><br/>';
}
?>