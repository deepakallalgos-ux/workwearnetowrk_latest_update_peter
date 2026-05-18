<table id="boxStockCloneTbl" style="display: none">
	<tbody>
		<tr>
			<td>
				<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-primary btn-outline btn-sm btnImageStock"><?php __('product_stock_choose_image'); ?></a>
				<div class="boxStockImageId">
					<div class="form-group"><input type="hidden" name="stock_image_id[{INDEX}]" value="" class="required" data-msg-required="<?php __('pj_field_required', false, true); ?>" /></div>
				</div>
			</td>
			<?php
			if (isset($tpl['attr_arr'])) {
				foreach ($tpl['attr_arr'] as $attr) {
					if (isset($attr['child']) && count(($attr['child'])) > 0) {
			?>
						<td>
							<div class="form-group">
								<select name="stock_attribute[{INDEX}][<?php echo $attr['id']; ?>]" class="form-control required" data-msg-required="<?php __('pj_field_required', false, true); ?>">
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
			}
			?>
			<td>
				<div class="form-group">
					<input type="text" name="stock_article_name[{INDEX}]"
						class="form-control" />
				</div>
			</td>
			<td>
				<div class="form-group">
					<!-- <label class="control-label"><?php __('product_status'); ?></label> -->

					<select name="status[{INDEX}]" class="form-control required">
						<option value="T">Active</option>
						<option value="F">Hidden</option>
					</select>
				</div>
			</td>
			<td>
				<div class="form-group">
					<input type="text" name="stock_article_number[{INDEX}]"
						class="form-control" />
				</div>
			</td>

			<td>
				<div class="form-group">
					<input type="text" name="stock_ean[{INDEX}]"
						class="form-control" />
				</div>
			</td>
			<td>
				<div class="form-group"><input type="text" name="stock_qty[{INDEX}]" class="form-control required digits pjScQuantity" data-msg-required="<?php __('pj_field_required', false, true); ?>" /></div>
			</td>
			<td>
				<div class="form-group">
					<div class="input-group">
						<input type="text" name="stock_price[{INDEX}]" class="form-control required number" data-msg-required="<?php __('pj_field_required', false, true); ?>" data-msg-number="<?php __('pj_field_number'); ?>" />

						<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']); ?></span>
					</div>
				</div>
			</td>
			<td align="right">
				<div class="form-group">
					<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-danger btn-outline btn-sm btnRemoveStock"><i class="fa fa-trash"></i></a>
				</div>
			</td>
		</tr>
	</tbody>
</table>