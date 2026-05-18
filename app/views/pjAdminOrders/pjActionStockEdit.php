<form action="" method="post">
	<input type="hidden" name="stock_edit" value="1" />
	<input type="hidden" name="order_id" value="<?php echo $tpl['os_arr']['order_id']; ?>" />
	<input type="hidden" name="order_stock_id" value="<?php echo $tpl['os_arr']['id']; ?>" />
<?php
if (isset($tpl['os_arr']) && !empty($tpl['os_arr']))
{
	?>
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
					<th><?php __('order_p_qty'); ?></th>
					<th><?php __('order_current_stock'); ?></th>
					<th><?php __('order_unit_price'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php
			if (isset($tpl['stock_arr']))
			{
				$cnt = isset($tpl['attr_arr']) ? count($tpl['attr_arr']) : 0;
				?>
				<tr>
					<?php
					if (isset($tpl['attr_arr']) && !empty($tpl['attr_arr']))
					{
						foreach ($tpl['attr_arr'] as $attr)
						{
							?>
							<td>
							<?php
							foreach ($attr['child'] as $child)
							{
								if (isset($tpl['stock_arr']['attrs'][$attr['id']]) && $tpl['stock_arr']['attrs'][$attr['id']] == $child['id'])
								{
									echo pjSanitize::html($child['name']);
									break;
								}
							}
							?>
							</td>
							<?php
						}
					}
					?>
					<td><input type="text" name="qty" value="<?php echo $tpl['os_arr']['qty']; ?>" class="form-control" data-max="<?php echo $tpl['os_arr']['qty'] + @$tpl['stock_arr']['qty']; ?>" readonly="readonly" /></td>
					<td><input type="hidden" name="current_qty" value="<?php echo @$tpl['stock_arr']['qty']; ?>" /><?php echo @$tpl['stock_arr']['qty']; ?></td>
					<td><input type="hidden" name="price" value="<?php echo $tpl['os_arr']['price']; ?>" /><?php echo pjCurrency::formatPrice($tpl['os_arr']['price']); ?></td>
				</tr>
				<?php
				if (!empty($tpl['extra_arr']))
				{
					?>
					<tr>
						<td colspan="<?php echo 3 + $cnt; ?>">
							<h3><?php __('lblOrderExtras');?></h3>
							<div class="row">
								<?php
								foreach ($tpl['extra_arr'] as $extra)
								{
									switch ($extra['type'])
									{
										case 'single':
											?>
											<div class="col-md-4 col-sm-6">
												<label><input type="checkbox" name="extra_id[<?php echo $extra['id']; ?>]" value="<?php echo $extra['type']; ?>|<?php echo $extra['price']; ?>"<?php echo array_key_exists($extra['id'], $tpl['oe_arr']) ? ' checked="checked"' : NULL; ?> /> <?php echo pjSanitize::html($extra['name']); ?>
												(<?php echo pjCurrency::formatPrice($extra['price']); ?>)</label>
											</div>
											<?php
											break;
										case 'multi':
											?>
											<div class="col-md-4 col-sm-6">
												<select name="extra_id[<?php echo $extra['id']; ?>]" class="form-control">
													<option value="">-- Select --</option>
													<?php
													foreach ($extra['extra_items'] as $k => $item)
													{
														?><option value="<?php echo $extra['type']; ?>|<?php echo $item['price']; ?>|<?php echo $item['id']; ?>"<?php echo isset($tpl['oe_arr'][$extra['id']]) && $item['id'] == $tpl['oe_arr'][$extra['id']] ? ' selected="selected"' : NULL; ?>><?php echo pjSanitize::html($item['name']); ?> (<?php echo pjCurrency::formatPrice($item['price']); ?>)</option><?php
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
	<?php
}
?>
</form>