<div class="table-responsive table-responsive-secondary">
	<table id="fdProductList" class="table table-striped table-hover">
		<thead>
			<tr>
				<th class="sc-order-line-thumb"><?php __('product_image'); ?></th>
				<th><?php __('order_p_name'); ?></th>
				<th><?php __('order_p_sku'); ?></th>
				<th><?php __('order_p_qty'); ?></th>
				<th><?php __('order_p_price'); ?></th>
				<th><?php __('order_p_subtotal'); ?></th>
				<th>&nbsp;</th>
			</tr>
		</thead>
							
		<tbody class="main-body">
			<?php
			$total = 0;
			$image_arr = isset($tpl['image_arr']) && is_array($tpl['image_arr']) ? $tpl['image_arr'] : array();
			foreach ($tpl['os_arr'] as $item)
			{
				$extra_price = 0;
				$stock_id = (int) @$item['stock_id'];
				$rel_path = ($stock_id && isset($image_arr[$stock_id])) ? $image_arr[$stock_id] : '';
				$thumb_src = PJ_INSTALL_URL . PJ_IMG_PATH . 'frontend/noimg.png';
				if ($rel_path !== '' && $rel_path !== null && is_file(PJ_INSTALL_PATH . $rel_path)) {
					$thumb_src = PJ_INSTALL_URL . $rel_path;
				}
				?>
				<tr>
					<td class="sc-order-line-thumb">
						<img src="<?php echo pjSanitize::html($thumb_src); ?>" alt="<?php echo pjSanitize::html($item['name']); ?>" class="sc-order-line-thumb__img s-Img" loading="lazy" />
					</td>
					<td><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProducts&amp;action=pjActionUpdate&amp;id=<?php echo $item['product_id']; ?>"><?php echo pjSanitize::html($item['name']); ?></a>
					<?php
					if (isset($item['attr']) && !empty($item['attr']))
					{
						$at = array();
						$a = explode(",", $item['attr']);
						foreach ($a as $v)
						{
							$t = explode("_", $v);
							$at[$t[1]] = $t[0];
						}
						foreach ($at as $attr_parent_id => $attr_id)
						{
							foreach ($tpl['attr_arr'] as $attr)
							{
								if ($attr['id'] == $attr_parent_id)
								{
									foreach ($attr['child'] as $child)
									{
										if ($child['id'] == $attr_id)
										{
											printf('<div><strong>%s</strong>: %s</div>', pjSanitize::html($attr['name']), pjSanitize::html($child['name']));
											break;
										}
									}
								}
							}
						}
					}
					//Extras
					if (isset($item['extra']) && !empty($item['extra']))
					{
						$a = explode(",", $item['extra']);
						foreach ($a as $eid)
						{
							if (strpos($eid, ".") === FALSE)
							{
								//single
								foreach ($tpl['extra_arr'] as $extra)
								{
									if ($extra['id'] == $eid)
									{
										printf('<div><strong>'.__('lblOrderExtra', true).':</strong> %s (%s)</div>', pjSanitize::html($extra['name']), pjCurrency::formatPrice($extra['price']));
										$extra_price += $extra['price'];
										break;
									}
								}
							} else {
								//multi
								list($e_id, $ei_id) = explode(".", $eid);
								foreach ($tpl['extra_arr'] as $extra)
								{
									if ($extra['id'] == $e_id && isset($extra['extra_items']) && !empty($extra['extra_items']))
									{
										foreach ($extra['extra_items'] as $extra_item)
										{
											if ($extra_item['id'] == $ei_id)
											{
												printf('<div><strong>'.__('lblOrderExtra', true).':</strong> %s (%s)</div>', pjSanitize::html($extra_item['name']), pjCurrency::formatPrice($extra_item['price']));
												$extra_price += $extra_item['price'];
												break;
											}
										}
										break;
									}
								}
							}
						}
					}
					$price = $item['price'] + $extra_price;
					$subtotal = $price * (int) $item['qty'];
					$total += $subtotal;
					?>
					</td>
					<td><?php echo pjSanitize::html($item['sku']); ?></td>
					<td><?php echo (int) $item['qty']; ?></td>
					<td><?php echo pjCurrency::formatPrice($price); ?></td>
					<td><?php echo pjCurrency::formatPrice($subtotal); ?></td>
					<td align="right">
						<a href="javascript:void(0);" class="btn btn-primary btn-outline btn-sm stock-edit" data-id="<?php echo $item['id']; ?>"><i class="fa fa-pencil"></i></a>
						<a href="javascript:void(0);" class="btn btn-danger btn-outline btn-sm stock-delete" data-id="<?php echo $item['id']; ?>"><i class="fa fa-trash"></i></a>
					</td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>
</div>