<?php
if (count($tpl['history_arr']) > 0)
{
	?>
	<div class="table-responsive table-responsive-secondary">
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th><?php __('product_history_created'); ?></th>
					<th><?php __('product_history_product'); ?></th>
					<?php
					if($tpl['product']['is_digital'] == 0)
					{ 
						?>
						<th><?php __('product_history_attribute'); ?></th>
						<?php
					}
					?>
					<th><?php __('product_history_qty_before'); ?></th>
					<th><?php __('product_history_qty_after'); ?></th>
					<th><?php __('product_history_price_before'); ?></th>
					<th><?php __('product_history_price_after'); ?></th>
					
				</tr>
			</thead>
			<tbody>
			<?php
			foreach ($tpl['history_arr'] as $k => $history)
			{
				$before = unserialize(base64_decode($history['before']));
				$after = unserialize(base64_decode($history['after']));
				?>
				<tr>
					<td><?php echo date($tpl['option_arr']['o_date_format'] . ', ' . $tpl['option_arr']['o_time_format'], strtotime($history['created'])); ?></td>
					<td><?php echo pjSanitize::html($history['name']); ?></td>
					<?php
					if($tpl['product']['is_digital'] == 0)
					{ 
						?>
						<td><?php echo pjSanitize::html($history['attributes']); ?></td>
						<?php
					}
					?>
					
					<td><?php echo $before['qty']; ?></td>
					<td><?php echo $after['qty']; ?></td>
					<td><?php echo pjCurrency::formatPrice($before['price']); ?></td>
					<td><?php echo pjCurrency::formatPrice($after['price']); ?></td>
				</tr>
				<?php
			}
			?>
			</tbody>
		</table>
	</div>
	<?php
} else {
	pjUtil::printNotice(__('product_history_empty', true), '');
}
?>