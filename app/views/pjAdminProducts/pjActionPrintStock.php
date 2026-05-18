<table class="table" width="100%">
	<thead>
		<tr>
			<th><?php __('product_stock_image'); ?></th>
			<th><?php __('lblName'); ?></th>
			<th><?php __('product_stock_price'); ?></th>
			<th><?php __('product_stock_qty'); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
	foreach ($tpl['arr'] as $item)
	{
		?><tr>
			<td><img src="<?php echo PJ_INSTALL_URL . $item['pic']; ?>" alt="" class="stock_pic" /></td>
			<td><?php
			echo pjSanitize::html($item['name']);
			if (!empty($item['stock_attr']))
			{
				printf('<br>(%s)', str_replace('~:~', ': ', join(', ', $item['stock_attr'])));
			}
			?></td>
			<td><?php echo pjCurrency::formatPrice($item['price']); ?></td>
			<td><?php echo $item['qty']; ?></td>
		</tr><?php
	}
	?>
	</tbody>
</table>