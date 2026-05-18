<?php
$titles = __('error_titles', true);
$bodies = __('error_bodies', true);
?>
<div class="alert alert-success"><strong><?php echo @$titles['AOR07']; ?></strong> <?php echo @$bodies['AOR07'];?></div>
<div class="table-responsive table-responsive-secondary">
	<table class="table table-striped table-hover">
		<thead>
			<tr>
				<?php
				if (isset($tpl['attr_arr']) && !empty($tpl['attr_arr']))
				{
					foreach ($tpl['attr_arr'] as $attr)
					{
						?><th><?php echo pjSanitize::html($attr['name']); ?></th><?php
					}
				}
				?>
				<th><?php __('quote_p_qty'); ?></th>
				<th><?php __('quote_current_stock'); ?></th>
				<th><?php __('quote_unit_price'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
		if (isset($tpl['stock_arr']))
		{
			$cnt = isset($tpl['attr_arr']) ? count($tpl['attr_arr']) : 0;
			$xtras = !empty($tpl['extra_arr']);
			$inStock = isset($tpl['stock_attr_arr']) && !empty($tpl['stock_attr_arr']);
			if ($inStock)
			{
				?>
				<tr>
					<?php
					if (isset($tpl['attr_arr']) && !empty($tpl['attr_arr']))
					{
						foreach ($tpl['attr_arr'] as $row => $attr)
						{
							?>
							<td>
								<select name="attr[<?php echo $attr['id']; ?>]" class="form-control scSelectorAttr" data-row="<?php echo $row; ?>" data-id="<?php echo $attr['id']; ?>">
									<?php
									if (isset($attr['child']) && !empty($attr['child']))
									{
										foreach ($attr['child'] as $child_index => $child)
										{
											foreach ($tpl['stock_attr_arr'] as $stock_id => $stock)
											{
												if (in_array($child['id'], $stock) || (isset($stock[$child['parent_id']]) && (int) $stock[$child['parent_id']] === 0))
												{
													if ($row == 0 && $child_index == 0)
													{
														$tmp_stock_id = $stock_id;
													}
													if ($row > 0)
													{
														if (!isset($tpl['stock_attr_arr'][$tmp_stock_id][$child['parent_id']]) ||
															$tpl['stock_attr_arr'][$tmp_stock_id][$child['parent_id']] != $child['id'])
														{
															continue;
														}
													}
													?><option value="<?php echo $child['id']; ?>"><?php echo pjSanitize::html($child['name']); ?></option><?php
													break;
												}
											}
										}
									} else {
										?><option value=""><?php __('front_not_available'); ?></option><?php
									}
									?>
								</select>
							</td>
							<?php
						}
					}


					$max_qty = isset($tpl['stock_arr'][0]['qty']) ? (int) $tpl['stock_arr'][0]['qty'] : 0;

					if (!empty($tpl['product_arr']['is_digital']) && $tpl['product_arr']['is_digital'] == 1) {
					    $max_qty = 99999;
					}
					$current_qty = $max_qty;
					?>
					<td>
					    <input type="text" name="qty" value="1" class="form-control" data-max="<?php echo $max_qty; ?>" maxlength="<?php echo strlen($max_qty); ?>" readonly="readonly" />
					</td>
					<td>
					    <input type="hidden" name="current_qty" value="<?php echo $current_qty; ?>" />
					    <span class="scSelectorCurrentQty"><?php echo $current_qty; ?></span>
					</td>
					<td>
					    <input type="hidden" name="price" value="<?php echo $tpl['product_arr']['price']; ?>" />
					    <span class="scSelectorPrice"><?php echo pjCurrency::formatPrice($tpl['product_arr']['price']); ?></span>
					</td>
				</tr>
				<?php
			}
			if ($xtras)
			{
				?>
				<tr>
					<td colspan="<?php echo 3 + $cnt; ?>">
						<h3><?php __('lblQuoteExtras');?></h3>
						<div class="row">
						<?php
						foreach ($tpl['extra_arr'] as $extra)
						{
							switch ($extra['type'])
							{
								case 'single':
									?>
									<div class="col-md-4 col-sm-6">
										<label><input type="checkbox" class="scSelectorExtra" name="extra_id[<?php echo $extra['id']; ?>]" data-price="<?php echo $extra['price'];?>" value="<?php echo $extra['type']; ?>|<?php echo $extra['price']; ?>" /> <?php echo pjSanitize::html($extra['name']); ?>
										(<?php echo pjCurrency::formatPrice($extra['price']); ?>)</label>
									</div>
									<?php
									break;
								case 'multi':
									?>
									<div class="col-md-4 col-sm-6">
										<select name="extra_id[<?php echo $extra['id']; ?>]" class="form-control scSelectorExtra">
											<option value="" data-price="0">-- Select --</option>
											<?php
											foreach ($extra['extra_items'] as $k => $item)
											{
												?><option value="<?php echo $extra['type']; ?>|<?php echo $item['price']; ?>|<?php echo $item['id']; ?>" data-price="<?php echo $item['price'];?>"><?php echo pjSanitize::html($item['name']); ?> (<?php echo pjCurrency::formatPrice($item['price']); ?>)</option><?php
											}
											?>
										</select>
									</div>
									<?php
									break;
							}
						}
						?>
						</div>
					</td>
				</tr>
				<?php
			}
		}
		?>
		</tbody>
	</table>
</div>