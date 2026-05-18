<?php if ($tpl['price_arr']['status'] == 'OK') : ?>
<table cellpadding="0" cellspacing="0" class="scCheckoutTable">
<?php
foreach ($tpl['cart_arr'] as $cart_item)
{
	$item = unserialize($cart_item['key_data']);
	$product = NULL;
	foreach ($tpl['arr'] as $p)
	{
		if ($p['id'] == $cart_item['product_id'])
		{
			$product = $p;
			break;
		}
	}
	if (is_null($product))
	{
		continue;
	}
	
	?><tr><td colspan="2" class="scCheckoutTableProduct"><span class="scCheckoutTableProductName"><?php echo pjSanitize::html($product['name']); ?></span><?php
	//Attributes
	if (isset($item['attr']) && !empty($item['attr']))
	{
		$attributes = array();
		foreach ($item['attr'] as $attr_parent_id => $attr_id)
		{
			foreach ($tpl['attr_arr'] as $attr)
			{
				if ($attr['id'] == $attr_parent_id && isset($attr['child']) && is_array($attr['child']))
				{
					foreach ($attr['child'] as $child)
					{
						if ($child['id'] == $attr_id)
						{
							$attributes[] = sprintf('%s: %s', pjSanitize::html($attr['name']), pjSanitize::html($child['name']));
							break;
						}
					}
				}
			}
		}
		if (!empty($attributes))
		{
			printf('<br><span class="scColorGray">(%s)</span>', join(", ", $attributes));
		}
	}
	//Extras
	if (isset($item['extra']) && !empty($item['extra']))
	{
		$extras = array();
		foreach ($item['extra'] as $eid)
		{
			if (strpos($eid, ".") === FALSE)
			{
				//single
				foreach ($tpl['extra_arr'] as $extra)
				{
					if ($extra['id'] == $eid)
					{
					    $extras[] = sprintf("%s %s", pjSanitize::html($extra['name']), pjCurrency::formatPrice($extra['price']));
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
							    $extras[] = sprintf("%s %s", pjSanitize::html($extra_item['name']), pjCurrency::formatPrice($extra_item['price']));
								break;
							}
						}
						break;
					}
				}
			}
		}
		if (!empty($extras))
		{
			printf('<br><span class="scColorGray">(%s)</span>', join(", ", $extras));
		}
	}
	?></td></tr><?php
}
?>


	<tr>
		<td class="scCheckoutTableLabel"><?php __('front_sub_total'); ?></td>
		<td class="scCheckoutTableValue"><?php echo pjCurrency::formatPrice($tpl['price_arr']['data']['price']); ?></td>
	</tr>
	<tr>
		<td class="scCheckoutTableLabel"><?php __('front_discount'); ?></td>
		<td class="scCheckoutTableValue"><?php echo pjCurrency::formatPrice($tpl['price_arr']['data']['discount']); ?></td>
	</tr>
	<tr>
		<td class="scCheckoutTableLabel"><?php __('front_insurance'); ?></td>
		<td class="scCheckoutTableValue"><?php echo pjCurrency::formatPrice($tpl['price_arr']['data']['insurance']); ?></td>
	</tr>
	<tr>
		<td class="scCheckoutTableLabel"><?php __('front_shipping'); ?></td>
		<td class="scCheckoutTableValue"><?php echo pjCurrency::formatPrice($tpl['price_arr']['data']['shipping']); ?></td>
	</tr>
	<tr>
		<td class="scCheckoutTableLabel"><?php __('front_tax'); ?></td>
		<td class="scCheckoutTableValue"><?php echo pjCurrency::formatPrice($tpl['price_arr']['data']['tax']); ?></td>
	</tr>
	<tr>
		<td class="scCheckoutTableLabel scCheckoutTableTotalLabel"><?php __('front_total'); ?></td>
		<td class="scCheckoutTableValue scCheckoutTableTotal"><?php echo pjCurrency::formatPrice($tpl['price_arr']['data']['total']); ?></td>
	</tr>
</table>
<?php endif; ?>