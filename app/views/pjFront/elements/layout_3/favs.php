<div class="container-fluid pjScFavs">
	<h2 class="text-uppercase text-primary pjScFavsTitle"><strong><?php __('front_my_favorites'); ?></strong></h2><br>
	<?php
	if (isset($tpl['arr']) && !empty($tpl['arr']))
	{ 
		?>
		<div class="panel panel-default">
			<table class="table table-striped">
				<thead class="text-uppercase">
					<tr>
						<th></th>
						<th><?php __('front_product'); ?></th>
				      	<th class="hidden-xs"><?php __('front_price'); ?></th>
				      	<th></th>
					</tr>
				</thead>
				<tbody>
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
							$href = $tpl['option_arr']['o_install_url'] . '/' . $slug;
						} else {
							# Non-Seo friendly URLs ---------------------
							$href = $tpl['option_arr']['o_install_url'] . '/' . ((!empty($tpl['option_arr']['o_page_prefix'])) ? $tpl['option_arr']['o_page_prefix'] . '-' : '') . 'product/' . $product['id'];
						}
						
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
						}
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
						}
						?>
						<tr>
							<td>
					      		<a href="<?php echo pjUtil::getReferer(); ?>" class="scSelectorRemoveFromFavs" data-hash="<?php echo $hash; ?>"><span class="glyphicon glyphicon-remove"></span></a>
					      	</td>
					      	<td>
					      		<div class="media">
					      			<div class="media-left">
						      			<a href="<?php echo $href; ?>" class="scProductImageLink scSelectorProduct" data-id="<?php echo $product['id']; ?>" data-slug="<?php echo pjSanitize::html($slug); ?>"><img src="<?php echo PJ_INSTALL_URL . (!empty($tpl['image_arr'][$item['stock_id']]) && is_file($tpl['image_arr'][$item['stock_id']]) ? $tpl['image_arr'][$item['stock_id']] : PJ_IMG_PATH . 'frontend/80x106.png'); ?>" alt="<?php echo pjSanitize::html($product['name']); ?>" /></a>
								  		<p class="form-control-static visible-xs"><strong><?php echo (int) $product['status'] === 1 ? pjCurrency::formatPrice($price) : __('front_not_available', true, false);?></strong></p>
								  	</div>
								  	<div class="media-body">
								  		<a href="<?php echo $href; ?>" class="scProductLink scSelectorProduct" data-id="<?php echo $product['id']; ?>" data-slug="<?php echo pjSanitize::html($slug); ?>"><?php echo pjSanitize::html($product['name']); ?></a><br>
								  		<?php
								  		if (!empty($attributes))
								  		{
								  			?><span class="text-muted"><?php echo join(", ", $attributes);?></span><?php
								  		}
								  		if (!empty($extras))
								  		{
								  			?><span class="text-muted"><?php echo join(", ", $extras);?></span><?php
								  		} 
								  		?>
								  	</div>
					      		</div>
					      	</td>
					      	<td class="hidden-xs pjScFavsProductPrice"><strong><?php echo (int) $product['status'] === 1 ? pjCurrency::formatPrice($price) : __('front_not_available', true, false);?></strong></td>
					      	<td class="text-right">
					      		<?php
					      		if (!pjUtil::isOptionEnumYes($tpl['option_arr'], 'o_disable_orders'))
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
											<button type="button" class="btn btn-default scSelectorButton scSelectorFav2Cart pjScBtnPrimary" data-hash="<?php echo $hash; ?>"><?php __('front_add_to_cart', false, true); ?></button>
					      				</form>
					      				<?php
					      			}else{
					      				__('front_out_of_stock');
					      			}
					      		} 
					      		?>
					      	</td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
		</div><!-- panel-default -->
		<?php
	}else{
		?><p><?php __('front_favs_not_found');?></p><?php
	} 
	?>
	<p>
		<button type="button" class="btn btn-default scSelectorButton scSelectorContinueShopping pjScBtnSecondary"><?php __('front_back_products', false, true); ?></button>
		<?php
		if (isset($tpl['arr']) && !empty($tpl['arr']))
		{ 
			?>
			<button type="button" class="btn btn-default scSelectorButton scSelectorEmptyFavs pjScBtnPrimary"><?php __('front_empty_favs', false, true); ?></button>
			<?php
		} 
		?>
	</p>
</div>