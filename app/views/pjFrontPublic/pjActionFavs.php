<?php
if (isset($tpl['status']) && $tpl['status'] == 'IP_BLOCKED') {
	?>
	<div class="container-fluid"><div class="alert alert-danger"><?php __('front_ip_address_blocked'); ?></div></div>
	<?php
} else {
	include PJ_VIEWS_PATH . 'pjFront/elements/header.php';
	if($controller->_get->toInt('layout') != 3)
	{
		?>
		<h1 class="scHeading"><?php __('front_my_favorites'); ?></h1>
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
					<div class="scTable1ThTotal"><div class="scTable1Th">&nbsp;</div></div>
				</div>
				<?php
				$cookie_value = base64_decode(stripslashes($_COOKIE[$controller->defaultCookie]));
				$favs = unserialize($cookie_value);
				foreach ($favs as $fav => $whatever)
				{
					$item = unserialize($fav);
					$product = NULL;
					foreach ($tpl['arr'] as $p)
					{
						if ($p['id'] == $item['product_id'])
						{
							$product = $p;
							break;
						}
					}
					if (is_null($product))
					{
						continue;
					}
					
					$price = /*round*/(@$tpl['stock_arr'][$item['stock_id']]);
					$hash = md5(serialize($item));
					
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
								?><a href="<?php echo pjUtil::getReferer(); ?>" class="scButtonPicto scButtonPictoDel scSelectorRemoveFromFavs" data-hash="<?php echo $hash; ?>"><img src="<?php echo PJ_INSTALL_URL . PJ_IMG_PATH; ?>frontend/layout-2/sc-btn-del-gray.png" data-original="<?php echo PJ_INSTALL_URL . PJ_IMG_PATH; ?>frontend/layout-2/sc-btn-del-gray.png" data-src="<?php echo PJ_INSTALL_URL . PJ_IMG_PATH; ?>frontend/layout-2/sc-btn-del-orange.png" alt="" /></a><a href="<?php echo $href; ?>" class="scButtonPicto scButtonPictoEye scSelectorEyeProduct"><img src="<?php echo PJ_INSTALL_URL . PJ_IMG_PATH; ?>frontend/layout-2/sc-btn-eye-gray.png" data-original="<?php echo PJ_INSTALL_URL . PJ_IMG_PATH; ?>frontend/layout-2/sc-btn-eye-gray.png" data-src="<?php echo PJ_INSTALL_URL . PJ_IMG_PATH; ?>frontend/layout-2/sc-btn-eye-blue.png" alt="" /></a><?php
								break;
							case 1:
							default:
								?><a href="<?php echo pjUtil::getReferer(); ?>" class="scCartRemove scSelectorRemoveFromFavs" data-hash="<?php echo $hash; ?>"></a><?php
								break;
						}
						?></div></div>
						<div class="scTable1TdPic"><div class="scTable1Td"><a href="<?php echo $href; ?>" class="scProductImageLink scSelectorProduct" data-id="<?php echo $product['id']; ?>" data-slug="<?php echo pjSanitize::html($slug); ?>"><img src="<?php echo PJ_INSTALL_URL . (!empty($tpl['image_arr'][$item['stock_id']]) && is_file($tpl['image_arr'][$item['stock_id']]) ? $tpl['image_arr'][$item['stock_id']] : PJ_IMG_PATH . 'frontend/noimg.png'); ?>" alt="<?php echo pjSanitize::html($product['name']); ?>" /></a></div></div>
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
									if ($attr['id'] == $attr_parent_id)
									{
										if(isset($attr['child']) && is_array($attr['child']))
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
											$price += $extra['price'];
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
													$price += $extra_item['price'];
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
						?>
						</div></div>
						<div class="scTable1TdPrice"><div class="scTable1Td">
						<?php
						if ((int) $product['status'] === 1)
						{
						    echo pjCurrency::formatPrice($price);
						} else {
							__('front_not_available');
						}
						?>
						</div></div>
						<div class="scTable1TdTotal"><div class="scTable1Td">
						<?php
						if ((int) $tpl['option_arr']['o_disable_orders'] === 0)
						{
							if ((int) $product['status'] === 1)
							{
								?>
								<form action="" method="post" style="display: inline">
									<input type="hidden" name="product_id" value="<?php echo (int) $item['product_id']; ?>" />
									<input type="hidden" name="is_digital" value="<?php echo (int) $item['is_digital']; ?>" />
									<?php
									if (isset($item['attr']) && !empty($item['attr']))
									{
										foreach ($item['attr'] as $ak => $av)
										{
											?><input type="hidden" name="attr[<?php echo (int) $ak; ?>]" value="<?php echo (int) $av; ?>" /><?php
										}
									}
									if (isset($item['extra']) && !empty($item['extra']))
									{
										foreach ($item['extra'] as $ek => $ev)
										{
											?><input type="hidden" name="extra[<?php echo (int) $ek; ?>]" value="<?php echo pjSanitize::html($ev); ?>" /><?php
										}
									}
									?>
									<input type="hidden" name="stock_id" value="<?php echo (int) $item['stock_id']; ?>" />
									<input type="hidden" name="qty" value="1" />
									<input type="button" value="<?php __('front_add_to_cart', false, true); ?>" class="scButton scButtonLight scButtonLightPlus scSelectorButton scSelectorFav2Cart" data-hash="<?php echo $hash; ?>" />
								</form>
								<?php
							} else {
								?><span class="scProductOutOfStock"><?php __('front_out_of_stock'); ?></span><?php
							}
						}
						?>
						</div></div>
					</div>
					<?php
				}
				?>
				</div>
		
			<p>
				<input type="button" value="<?php __('front_back_products', false, true); ?>" class="scButton scButtonLight scButtonLightPrev scSelectorButton scSelectorContinueShopping" />
				<input type="button" value="<?php __('front_empty_favs', false, true); ?>" class="scButton scButtonDark scButtonDarkDel scSelectorButton scSelectorEmptyFavs" />
			</p>
			<?php
		} else {
			?><div class="scMessage"><div class="scMessageIcon"></div><?php __('front_favs_not_found'); ?></div><?php
		}
	}else{
		include PJ_VIEWS_PATH . 'pjFront/elements/layout_3/favs.php';
	}
}
?>