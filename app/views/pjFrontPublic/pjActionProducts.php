<?php
if (isset($tpl['status']) && $tpl['status'] == 'IP_BLOCKED') {
	?>
	<div class="container-fluid"><div class="alert alert-danger"><?php __('front_ip_address_blocked'); ?></div></div>
	<?php
} else {
	include PJ_VIEWS_PATH . 'pjFront/elements/header.php';
	
	if (isset($tpl['product_arr']) && !empty($tpl['product_arr']))
	{
		if($controller->_get->toInt('layout') != 3)
		{
			foreach($tpl['product_arr'] as $product)
			{
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
				<div class="scProductItem scSelectorProductItem">
					<div class="scProductItemPic">
						<?php
						$src = PJ_INSTALL_URL . PJ_IMG_PATH . 'frontend/noimg.png';
						if (is_file(PJ_INSTALL_PATH . $product['pic']))
						{
							$src = PJ_INSTALL_URL . $product['pic'];
						}else if(is_file(PJ_INSTALL_PATH . $product['secondary_pic'])){
							$src = PJ_INSTALL_URL . $product['secondary_pic'];
						}
						?>
						<a href="<?php echo $href; ?>" class="scSelectorProduct" data-id="<?php echo $product['id']; ?>" data-slug="<?php echo pjSanitize::html($slug); ?>"><img src="<?php echo $src; ?>" alt="<?php echo pjSanitize::html($product['name']); ?>" /></a>
					</div>
					<div class="scProductItemInfo">
						<div class="scProductItemName"><a href="<?php echo $href; ?>" class="scSelectorProduct" data-id="<?php echo $product['id']; ?>" data-slug="<?php echo pjSanitize::html($slug); ?>"><?php echo pjSanitize::html($product['name']); ?></a></div>
						<div class="scProductItemPrice">
						<?php
						if ((float) $product['min_price'] < (float) $product['max_price'])
						{
							__('front_price_from');
						}
						?>
						<span class="scProductItemPriceValue"><?php echo pjCurrency::formatPrice($product['min_price']); ?></span></div>
						<?php
						if ($product['status'] != 3 && $product['stockQty'] - (int) @$tpl['order_arr'][$product['stockId']] > 0 && !empty($product['stockId']))
						{
							?>
							<div class="scProductItemButtons">
								<?php if (!pjUtil::isOptionEnumYes($tpl['option_arr'], 'o_disable_orders')) : ?>
								<form action="" method="post" class="scSelectorBuyNowForm" style="display: inline; vertical-align: top">
									<input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
									<input type="hidden" name="is_digital" value="<?php echo (int) $product['is_digital']; ?>" />
									<?php
									if (!empty($product['stockId_attr']))
									{
										$attrs = explode(",", $product['stockId_attr']);
										foreach ($attrs as $attr)
										{
											list($attr_id, $attr_parent_id) = explode("_", $attr);
											?>
											<input type="hidden" name="attr[<?php echo $attr_parent_id; ?>]" value="<?php echo $attr_id; ?>" />
											<?php
										}
									}
									if (!empty($product['m_extras']))
									{
										foreach ($product['m_extras'] as $ek => $ev)
										{
											?><input type="hidden" name="extra[<?php echo $ek; ?>]" value="<?php echo $ev; ?>" /><?php
										}
									}
									?>
									<input type="hidden" name="qty" value="1" />
									<input type="hidden" name="stock_id" value="<?php echo $product['stockId']; ?>" />
									
									<?php
									switch ($controller->_get->toInt('layout'))
									{
										case 2:
											?><input type="submit" value="" class="scButtonPicto scButtonPictoCart scSelectorButton" /><?php
											break;
										case 1:
										default:
											?><input type="submit" value="<?php __('front_buy_now', false, true); ?>" class="scButton scButtonDark scSelectorButton" /><?php
											break;
									}
									?>
								</form>
								<?php endif; ?>
								<?php
								switch ($controller->_get->toInt('layout'))
								{
									case 2:
										?><a href="<?php echo $href; ?>" data-id="<?php echo $product['id']; ?>" data-slug="<?php echo pjSanitize::html($slug); ?>" class="scButtonPicto scButtonPictoPlus scSelectorButton scSelectorProduct"></a><?php
										break;
									case 1:
									default:
										?><a href="<?php echo $href; ?>" data-id="<?php echo $product['id']; ?>" data-slug="<?php echo pjSanitize::html($slug); ?>" class="scButton scButtonLight scSelectorButton scSelectorProduct"><?php __('front_view_details'); ?></a><?php
										break;
								}
								?>
							</div>
							<?php
						} else {
							if (!pjUtil::isOptionEnumYes($tpl['option_arr'], 'o_disable_orders'))
							{
								?><div class="scProductItemOutOfStock"><span class="scProductOutOfStock"><?php __('front_out_of_stock'); ?></span></div><?php
							}
						}
						?>
					</div>
				</div>
				<?php
			}
			?>
			<div style="clear: left"></div>
			<?php
			if (isset($tpl['paginator']))
			{
				if($tpl['paginator']['count'] > $tpl['paginator']['row_count'])
				{
					?>
					<ul class="scPaginator">
					<?php
					if ($tpl['paginator']['pages'] > 1 && $tpl['paginator']['page'] > 1)
					{
						$i = $tpl['paginator']['page'] - 1;
						?><li><a href="<?php echo pjUtil::getReferer(); ?>#!/Products/q:<?php echo urlencode($controller->_get->toString('q')); ?>/category:<?php echo $controller->_get->toInt('category_id'); ?>/page:<?php echo $i; ?>" class="scSelectorPage" data-page="<?php echo $i; ?>" title="<?php __('front_prev', false, true); ?>"><?php
						switch ($controller->_get->toInt('layout'))
						{
							case 2:
								__('front_prev');
								break;
							case 1:
							default:
								echo '&lt;';
								break;
						}
						?></a></li><?php
					}
					for ($i = 1; $i <= $tpl['paginator']['pages']; $i++)
					{
						?><li><a href="<?php echo pjUtil::getReferer(); ?>#!/Products/q:<?php echo urlencode($controller->_get->toString('q')); ?>/category:<?php echo $controller->_get->toInt('category_id'); ?>/page:<?php echo $i; ?>" class="scSelectorPage<?php echo $tpl['paginator']['page'] != $i ? NULL : ' scPaginatorFocus'; ?>" data-page="<?php echo $i; ?>"><?php echo $i; ?></a></li><?php
					}
					if ($tpl['paginator']['pages'] > $tpl['paginator']['page'])
					{
						$i = $tpl['paginator']['page'] + 1;
						?><li><a href="<?php echo pjUtil::getReferer(); ?>#!/Products/q:<?php echo urlencode($controller->_get->toString('q')); ?>/category:<?php echo $controller->_get->toInt('category_id'); ?>/page:<?php echo $i; ?>" class="scSelectorPage" data-page="<?php echo $i; ?>" title="<?php __('front_next', false, true); ?>"><?php
						switch ($controller->_get->toInt('layout'))
						{
							case 2:
								__('front_next');
								break;
							case 1:
							default:
								echo '&gt;';
								break;
						}
						?></a></li><?php
					}
					?>
					</ul>
					<?php
				}
			}
		}else{
			include PJ_VIEWS_PATH . 'pjFront/elements/layout_3/products.php';
		}
	} else {
		if($controller->_get->toInt('layout') != 3)
		{
			?><div class="scMessage"><div class="scMessageIcon"></div><?php __('front_products_not_found'); ?></div><?php
		}else{
			?><div class="container-fluid"><p class="pjScEmptyCategoryMessage"><?php __('front_products_not_found'); ?></p><!-- /.pjScEmptyCategoryMessage --></div><?php
		}
	}
}
?>