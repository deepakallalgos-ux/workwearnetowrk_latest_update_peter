<?php
if (isset($tpl['status']) && $tpl['status'] == 'IP_BLOCKED') {
	?>
	<div class="container-fluid"><div class="alert alert-danger"><?php __('front_ip_address_blocked'); ?></div></div>
	<?php
} else {
	include PJ_VIEWS_PATH . 'pjFront/elements/header.php';
	$validate = str_replace(array('"', "'"), array('\"', "\'"), __('validate', true, true));
	if($controller->_get->toInt('layout') != 3)
	{
		?>
		<h1 class="scHeading"><?php __('front_shopping_cart'); ?></h1>
		<?php
		if (isset($tpl['arr']) && !empty($tpl['arr']))
		{
			?>
			<div class="scTable1">
				<div class="scTable1HeadRow">
					<div class="scTable1ThDel"><div class="scTable1Th">&nbsp;</div></div>
					<div class="scTable1ThPic"><div class="scTable1Th">&nbsp;</div></div>
					<div class="scTable1ThName"><div class="scTable1Th"><?php __('front_product'); ?></div></div>
					<div class="scTable1ThPrice"><div class="scTable1Th"><?php __('front_price'); ?></div></div>
					<div class="scTable1ThNamePrice"><div class="scTable1Th"><?php __('front_product_price'); ?></div></div>
					<div class="scTable1ThQty"><div class="scTable1Th"><?php __('front_quantity'); ?></div></div>
					<div class="scTable1ThTotal"><div class="scTable1Th"><?php __('front_total'); ?></div></div>
					<div class="scTable1ThQtyTotal"><div class="scTable1Th"><?php __('front_quantity_total'); ?></div></div>
				</div>
				<?php
				$price_arr = pjFrontCart::pjActionCalcPrice($tpl['option_arr'], $tpl['cart_arr'], $tpl['stock_arr'], $tpl['extra_arr'], isset($tpl['o_shipping']) ? $tpl['o_shipping'] : null, isset($tpl['o_tax']) ? $tpl['o_tax'] : null, isset($tpl['o_fee']) ? $tpl['o_fee'] : null, @$_SESSION[$controller->defaultVoucher]);
				foreach ($tpl['cart_arr'] as $key => $cart_item)
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
					
					$hash = md5($cart_item['key_data']);
					
					$slug = NULL;
					if ((int) $tpl['option_arr']['o_seo_url'] === 1)
					{
						# Seo friendly URLs ---------
						$category_id = NULL;
						if (!empty($product['category_ids']))
						{
							$category_id = max($product['category_ids']);
						}
					
						$category_slug = array();
						if (!is_null($category_id))
						{
							$arr = array();
							pjUtil::getBreadcrumbTree($arr, $tpl['category_arr'], $category_id);
							krsort($arr);
							$arr = array_values($arr);
							
							foreach ($arr as $k => $category)
							{
								$category_slug[] = pjAppController::friendlyURL($category['data']['name']);
							}
						}
						
						$slug = sprintf("%s-%u.html", pjAppController::friendlyURL($product['name']), $product['id']);
						if (!empty($category_slug))
						{
							$slug = join("/", $category_slug) . '/' . $slug;
						}
						$href = pjUtil::getReferer() . '#!/' . $slug;
					} else {
						# Non-Seo friendly URLs ---------------------
						$href = pjUtil::getReferer() . '#!/Product/' . $product['id'];
					}
					?>
					<div class="scTable1BodyRow scTable1TopRow">
						<div class="scTable1TdDel"><div class="scTable1Td"><?php
						switch ((int) $controller->_get->toInt('layout'))
						{
							case 2:
								?><a href="<?php echo pjUtil::getReferer(); ?>" class="scButtonPicto scButtonPictoDel scSelectorRemoveFromCart" data-hash="<?php echo $hash; ?>"><img src="<?php echo PJ_INSTALL_URL . PJ_IMG_PATH; ?>frontend/layout-2/sc-btn-del-gray.png" data-original="<?php echo PJ_INSTALL_URL . PJ_IMG_PATH; ?>frontend/layout-2/sc-btn-del-gray.png" data-src="<?php echo PJ_INSTALL_URL . PJ_IMG_PATH; ?>frontend/layout-2/sc-btn-del-orange.png" alt="" /></a><a href="<?php echo $href; ?>" class="scButtonPicto scButtonPictoEye scSelectorEyeProduct"><img src="<?php echo PJ_INSTALL_URL . PJ_IMG_PATH; ?>frontend/layout-2/sc-btn-eye-gray.png" data-original="<?php echo PJ_INSTALL_URL . PJ_IMG_PATH; ?>frontend/layout-2/sc-btn-eye-gray.png" data-src="<?php echo PJ_INSTALL_URL . PJ_IMG_PATH; ?>frontend/layout-2/sc-btn-eye-blue.png" alt="" /></a><?php
								break;
							case 1:
							default:
								?><a href="<?php echo pjUtil::getReferer(); ?>" class="scCartRemove scSelectorRemoveFromCart" data-hash="<?php echo $hash; ?>"></a><?php
								break;
						}
						?></div></div>
						<div class="scTable1TdPic"><div class="scTable1Td"><a href="<?php echo $href; ?>" class="scProductImageLink scSelectorProduct" data-id="<?php echo $product['id']; ?>" data-slug="<?php echo pjSanitize::html($slug); ?>"><img src="<?php echo PJ_INSTALL_URL . (!empty($tpl['image_arr'][$cart_item['stock_id']]) && is_file($tpl['image_arr'][$cart_item['stock_id']]) ? $tpl['image_arr'][$cart_item['stock_id']] : PJ_IMG_PATH . 'frontend/noimg.png'); ?>" alt="<?php echo pjSanitize::html($product['name']); ?>" /></a></div></div>
						<div class="scTable1TdName"><div class="scTable1Td"><a href="<?php echo $href; ?>" class="scProductLink scSelectorProduct" data-id="<?php echo $product['id']; ?>" data-slug="<?php echo pjSanitize::html($slug); ?>"><?php echo pjSanitize::html($product['name']); ?></a>
						<?php
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
						$remaining_qty = (int) $tpl['stock_arr'][$cart_item['stock_id']]['qty'] - (int) @$tpl['order_arr'][$cart_item['stock_id']];
						$max_qty = $cart_item['qty'] + $remaining_qty;
						?>
						</div></div>
						<div class="scTable1TdPrice"><div class="scTable1Td"><?php echo pjCurrency::formatPrice($price_arr['p_arr'][$key]); ?></div></div>
						<div class="scTable1TdQty"><div class="scTable1Td"><?php
						switch ((int) $controller->_get->toInt('layout'))
						{
							case 2:
								?>
								<select name="qty[<?php echo $hash; ?>]" class="scSelect scSelectorQty scCallbackUpdate">
								<?php
								foreach (range(1, (int) $max_qty) as $i)
								{
									?><option value="<?php echo $i; ?>"<?php echo (int) $cart_item['qty'] !== $i ? NULL : ' selected="selected"'; ?>><?php echo $i; ?></option><?php
								}
								?>
								</select>
								<?php
								break;
							case 1:
							default:
								?>
								<div class="scProductSpinner">
									<a href="#" class="scProductSpinButton scSelectorSpin scCallbackUpdate" data-direction="down" title="<?php __('front_down', false, true); ?>">-</a>
									<input type="text" name="qty[<?php echo $hash; ?>]" class="scProductSpinValue scSelectorSpinValue" value="<?php echo (int) $cart_item['qty']; ?>" data-step="1" data-min="1" data-max="<?php echo (int) $max_qty; ?>" maxlength="<?php echo strlen($max_qty); ?>" readonly="readonly" />
									<a href="#" class="scProductSpinButton scSelectorSpin scCallbackUpdate" data-direction="up" title="<?php __('front_up', false, true); ?>">+</a>
								</div>
								<?php
								break;
						}
						?>
						</div></div>
						<div class="scTable1TdTotal"><div class="scTable1Td"><?php echo pjCurrency::formatPrice($price_arr['subtotal']); ?></div></div>
					</div>
					<?php
				}
				?>
				<div class="scTable1FootRow scTable1TopRow">
					<div class="scTable1TdFootLabel"><div class="scTable1Td"><?php __('front_sub_total'); ?></div></div>
					<div class="scTable1TdFootValue"><div class="scTable1Td scTable1FormatSubTotal"><?php echo pjCurrency::formatPrice($price_arr['amount']); ?></div></div>
				</div>
				<?php
				
				if ($price_arr['discount'] > 0)
				{
					?>
					<div class="scTable1FootRow">
						<div class="scTable1TdFootLabel"><div class="scTable1Td"><?php __('front_discount'); ?> (<?php echo $price_arr['discount_print']; ?>)<br />
							<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="scCartRemove scSelectorRemoveCode scAlignMiddle"></a> <?php echo $price_arr['voucher_code']; ?>
						</div></div>
						<div class="scTable1TdFootValue"><div class="scTable1Td scTable1FormatPrices"><?php echo pjCurrency::formatPrice($price_arr['discount']); ?></div></div>
					</div>
					<?php
				} else {
					if (!isset($_SESSION[$controller->defaultVoucher]) || empty($_SESSION[$controller->defaultVoucher]))
					{
						?>
						<div class="scTable1FootRow">
							<div class="scTable1TdFootLabel"><div class="scTable1Td"><?php __('front_promo_code'); ?></div></div>
							<div class="scTable1TdFootValue"><div class="scTable1Td">
								<form action="" method="post" class="scSelectorVoucherForm">
									<input type="hidden" name="sc_voucher" value="1" />
									<div class="scNotice scSelectorNoticeMsg" style="display: none"></div>
									<input type="text" name="code" class="scText" style="margin-bottom: 5px" data-err="<?php echo $validate['voucher'];?>"/><br />
									<input type="submit" value="<?php __('front_apply_code', false, true); ?>" class="scButton scButtonLight scSelectorButton scSelectorApplyCode" />
								</form>
							</div></div>
						</div>
						<?php
					}
				}
				?>
				<form action="" method="post" class="scSelectorCartForm">
				<?php if ($price_arr['insurance'] > 0) : ?>
				<div class="scTable1FootRow">
					<div class="scTable1TdFootLabel"><div class="scTable1Td"><?php __('front_insurance'); ?></div></div>
					<div class="scTable1TdFootValue"><div class="scTable1Td scTable1FormatPrices"><?php echo pjCurrency::formatPrice($price_arr['insurance']); ?></div></div>
				</div>
				<?php endif;?>
				<?php if ($controller->pjActionShowShipping() && !empty($tpl['tax_arr'])) : ?>
					<div class="scTable1FootRow">
						<div class="scTable1TdFootLabel"><div class="scTable1Td"><?php __('front_shipping_location'); ?></div></div>
						<div class="scTable1TdFootValue"><div class="scTable1Td">
							<select name="tax_id" class="scSelect scSelectorShipping<?php echo !empty($tpl['tax_arr']) ? ' required' : NULL; ?>" data-err="<?php echo $validate['tax'];?>">
								<option value=""><?php __('front_choose_location'); ?></option>
								<?php
								foreach ($tpl['tax_arr'] as $item)
								{
									?><option value="<?php echo $item['id']; ?>"<?php echo !isset($_SESSION[$controller->defaultTax]) || $_SESSION[$controller->defaultTax] != $item['id'] ? NULL : ' selected="selected"'; ?>><?php echo pjSanitize::html($item['location']); ?></option><?php
								}
								?>
							</select>
						</div></div>
					</div>
					<?php if ($price_arr['shipping'] > 0) : ?>
					<div class="scTable1FootRow">
						<div class="scTable1TdFootLabel"><div class="scTable1Td"><?php __('front_shipping'); ?></div></div>
						<div class="scTable1TdFootValue"><div class="scTable1Td scTable1FormatPrices"><?php echo pjCurrency::formatPrice($price_arr['shipping']); ?></div></div>
					</div>
					<?php endif; ?>
				<?php endif; ?>
				<?php if ($price_arr['tax'] > 0) : ?>
				<div class="scTable1FootRow">
					<div class="scTable1TdFootLabel"><div class="scTable1Td"><?php __('front_tax'); ?></div></div>
					<div class="scTable1TdFootValue"><div class="scTable1Td scTable1FormatPrices"><?php echo pjCurrency::formatPrice($price_arr['tax']); ?></div></div>
				</div>
				<?php endif; ?>
				<div class="scTable1FootRow">
					<div class="scTable1TdFootLabel"><div class="scTable1Td"><?php __('front_total'); ?></div></div>
					<div class="scTable1TdFootValue"><div class="scTable1Td scTable1FormatTotal"><?php echo pjCurrency::formatPrice($price_arr['total']); ?></div></div>
				</div>
				</form>
			</div>
			
			<div class="scProductOpt scAlignRight">
				<input type="button" value="<?php __('front_back_products', false, true); ?>" class="scButton scButtonLight scButtonLightPrev scSelectorButton scSelectorContinueShopping" />
				<input type="button" value="<?php __('front_checkout', false, true); ?>" class="scButton scButtonDark scButtonDarkNext scSelectorButton scSelectorCheckout" />
			</div>
			<?php
		} else {
			?><div class="scMessage"><div class="scMessageIcon"></div><?php __('front_empty_shopping_cart'); ?></div><?php
		}
	}else{
		include PJ_VIEWS_PATH . 'pjFront/elements/layout_3/cart.php';
	}
}
?>