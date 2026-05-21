<div class="table-responsive table-responsive-secondary stockContainer">
	<?php
	$table_width = 420;
	if (isset($tpl['attr_arr'])) {
		foreach ($tpl['attr_arr'] as $attr) {
			if (isset($attr['child']) && count(($attr['child'])) > 0) {
				$table_width += 150;
			}
		}
	}
	if ($table_width < 742) {
		$table_width = 742;
	}
	?>
	<table class="table table-striped table-hover tblStocks">
		<thead>
			<tr>
				<th><?php __('product_stock_image'); ?></th>
				<?php
				if (isset($tpl['attr_arr'])) {
					foreach ($tpl['attr_arr'] as $attr) {
						if (isset($attr['child']) && count(($attr['child'])) > 0) {
				?><th><?php echo pjSanitize::html($attr['name']); ?></th><?php
																		}
																	}
																}
																			?>
				<th><?php __('product_article_name'); ?></th>
				<th><?php __('product_status'); ?></th>
				<th><?php __('product_article_number'); ?></th>
				<th><?php __('product_ean'); ?></th>
				<th><?php __('product_stock_qty'); ?></th>
				<th><?php __('product_stock_buying_price'); ?></th>
				<th><?php __('product_stock_price'); ?></th>
				<?php
				if (count($tpl['attr_arr']) > 0) {
				?>
					<th>&nbsp;</th>
				<?php
				}
				?>
			</tr>
		</thead>
		<tbody>
			<?php
			if (isset($tpl['stock_arr']) && count($tpl['stock_arr']) > 0) {
				foreach ($tpl['stock_arr'] as $stock) {
			?>
					<tr>
						<td>
							<?php
							if (!empty($stock['small_path'])) {
							?><a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btnImageStock s-Pic" rel="<?php echo $stock['image_id']; ?>"><img src="<?php echo PJ_INSTALL_URL . $stock['small_path']; ?>" alt="" class="in-stock s-Img" /></a><?php
																																																									} else {
																																																										?><a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-primary btn-outline btn-sm btnImageStock"><?php __('product_stock_choose_image'); ?></a><?php
																																																																																															}
																																																																																																?>
							<div class="boxStockImageId">
								<div class="form-group"><input type="hidden" name="stock_image_id[<?php echo $stock['id'] ?>]" value="<?php echo $stock['image_id']; ?>" class="required" data-msg-required="<?php __('pj_field_required', false, true); ?>" /></div>
							</div>
						</td>
						<?php
						foreach ($tpl['attr_arr'] as $attr) {
							if (isset($attr['child']) && count(($attr['child'])) > 0) {
						?>
								<td>
									<div class="form-group">
										<select name="stock_attribute[<?php echo $stock['id'] ?>][<?php echo $attr['id']; ?>]" class="form-control required" data-msg-required="<?php __('pj_field_required', false, true); ?>">
											<option value="">---</option>
											<?php
											foreach ($attr['child'] as $child) {
											?><option value="<?php echo $child['id']; ?>" <?php echo isset($stock['attrs'][$attr['id']]) && $stock['attrs'][$attr['id']] == $child['id'] ? ' selected="selected"' : NULL; ?>><?php echo pjSanitize::html($child['name']); ?></option><?php
																																																																					}
																																																																						?>
										</select>
									</div>
								</td>
						<?php
							}
						}
						?>
						<td>
							<div class="form-group">
								<input type="text"
									name="stock_article_name[<?php echo $stock['id']; ?>]"
									class="form-control"
									value="<?php echo pjSanitize::html(@$stock['article_name']); ?>" />
							</div>
						</td>

						<td>
							<div class="form-group">
								<select name="status[<?php echo $stock['id']; ?>]" class="form-control">
									<option value="T" <?php echo @$stock['status'] == 'T' ? 'selected="selected"' : ''; ?>>Active</option>
									<option value="F" <?php echo @$stock['status'] == 'F' ? 'selected="selected"' : ''; ?>>Hidden</option>
								</select>
							</div>
						</td>

						<td>
							<div class="form-group">
								<input type="text" name="stock_article_number[<?php echo $stock['id'] ?>]"
									class="form-control" value="<?php echo pjSanitize::html(@$stock['article_number']); ?>" />
							</div>
						</td>

						<td>
							<div class="form-group">
								<input type="text" name="stock_ean[<?php echo $stock['id'] ?>]"
									class="form-control" value="<?php echo pjSanitize::html(@$stock['ean']); ?>" />
							</div>
						</td>
						<td>
							<div class="form-group"><input type="text" name="stock_qty[<?php echo $stock['id'] ?>]" class="form-control <?php echo (int) $tpl['arr']['is_digital'] === 1 ? null : ' required'; ?> digits pjScQuantity" value="<?php echo $stock['qty']; ?>" data-msg-required="<?php __('pj_field_required', false, true); ?>" data-msg-digits="<?php __('pj_field_digits'); ?>" /></div>
						</td>
						<td>
							<div class="form-group">
								<div class="input-group">
									<input type="text" name="stock_buying_price[<?php echo $stock['id']; ?>]" value="<?php echo isset($stock['buying_price']) ? $stock['buying_price'] : ''; ?>" class="form-control number" data-msg-number="<?php __('pj_field_number'); ?>" />
									<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']); ?></span>
								</div>
							</div>
						</td>
						<td>
							<div class="form-group">
								<div class="input-group">
									<input type="text" name="stock_price[<?php echo $stock['id']; ?>]" value="<?php echo $stock['price']; ?>" class="form-control required number" data-msg-required="<?php __('pj_field_required', false, true); ?>" data-msg-number="<?php __('pj_field_number'); ?>" />

									<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']); ?></span>
								</div>
							</div>
						</td>
						<?php
						if (count($tpl['attr_arr']) > 0) {
						?>
							<td align="right">
								<div class="form-group">
									<a href="<?php echo $_SERVER['PHP_SELF']; ?>" rel="<?php echo $stock['id']; ?>" class="btn btn-danger btn-outline btn-sm btnDeleteStock"><i class="fa fa-trash"></i></a>
								</div>
							</td>
						<?php
						}
						?>
					</tr>
				<?php
				}
			} else {
				mt_srand();
				$index = 'x_' . mt_rand(0, 999999);
				?>
				<tr>
					<td>
						<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-primary btn-outline btn-sm btnImageStock"><?php __('product_stock_choose_image'); ?></a>
						<div class="boxStockImageId">
							<div class="form-group"><input type="hidden" name="stock_image_id[<?php echo $index; ?>]" value="" class="required" data-msg-required="<?php __('pj_field_required', false, true); ?>" /></div>
						</div>
					</td>
					<?php
					if (isset($tpl['attr_arr'])) {
						foreach ($tpl['attr_arr'] as $attr) {
					?>
							<td>
								<div class="form-group">
									<select name="stock_attribute[<?php echo $index; ?>][<?php echo $attr['id']; ?>]" class="form-control required" data-msg-required="<?php __('pj_field_required', false, true); ?>">
										<option value="">---</option>
										<?php
										foreach ($attr['child'] as $child) {
										?><option value="<?php echo $child['id']; ?>"><?php echo pjSanitize::html($child['name']); ?></option><?php
																																			}
																																				?>
									</select>
								</div>
							</td>
					<?php
						}
					}
					?>
					<td>
						<div class="form-group">
							<input type="text" name="stock_article_name[<?php echo $index; ?>]"
								class="form-control" />
						</div>
					</td>

					<td>
						<div class="form-group">
							<select name="status[<?php echo $index; ?>]" class="form-control">
								<option value="T">Active</option>
								<option value="F">Hidden</option>
							</select>
						</div>
					</td>
					<td>
						<div class="form-group">
							<input type="text" name="stock_article_number[<?php echo $index; ?>]"
								class="form-control" />
						</div>
					</td>

					<td>
						<div class="form-group">
							<input type="text" name="stock_ean[<?php echo $index; ?>]"
								class="form-control" />
						</div>
					</td>
					<td>
						<div class="form-group">
							<input type="text" name="stock_qty[<?php echo $index; ?>]" class="form-control <?php echo (int) $tpl['arr']['is_digital'] === 1 ? null : ' required'; ?> digits pjScQuantity" data-msg-required="<?php __('pj_field_required', false, true); ?>" data-msg-digits="<?php __('pj_field_digits'); ?>" />
						</div>
					</td>
					<td>
						<div class="form-group">
							<div class="input-group">
								<input type="text" name="stock_buying_price[<?php echo $index; ?>]" class="form-control number" data-msg-number="<?php __('pj_field_number'); ?>" />
								<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']); ?></span>
							</div>
						</div>
					</td>
					<td>
						<div class="form-group">
							<div class="input-group">
								<input type="text" name="stock_price[<?php echo $index; ?>]" class="form-control required number" data-msg-required="<?php __('pj_field_required', false, true); ?>" data-msg-number="<?php __('pj_field_number'); ?>" />

								<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']); ?></span>
							</div>
						</div>
					</td>
					<?php
					if (count($tpl['attr_arr']) > 0) {
					?>
						<td align="right">
							<div class="form-group">
								<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-danger btn-outline btn-sm btnRemoveStock"><i class="fa fa-trash"></i></a>
							</div>
						</td>
					<?php
					}
					?>
				</tr>
			<?php
			}
			?>
		</tbody>
	</table>
</div>
<?php
if (count($tpl['attr_arr']) > 0) {
?>
	<div>
		<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-primary btn-outline btn-sm btnStockAdd"><?php __('product_stock_add'); ?></a>
	</div>
<?php
}
if (count($tpl['gallery_arr']) == 1) {
?><input type="hidden" id="scHiddenImageId" name="scHiddenImageId" value="<?php echo $tpl['gallery_arr'][0]['id']; ?>" data-src="<?php echo PJ_INSTALL_URL . (!empty($tpl['gallery_arr'][0]['small_path']) ? $tpl['gallery_arr'][0]['small_path'] : PJ_IMG_PATH . 'no_image.png'); ?>?<?php echo rand(1, 9999999); ?>" /><?php } ?>
<div class="hr-line-dashed"></div>

<div class="clearfix">
	<button type="submit" class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader pull-left" data-style="zoom-in" style="margin-right: 15px;">
		<span class="ladda-label"><?php __('btnSave'); ?></span>
		<?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
	</button>
	<a class="btn btn-white btn-lg pull-right" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminProducts&action=pjActionIndex"><?php __('btnCancel'); ?></a>
</div><!-- /.clearfix -->