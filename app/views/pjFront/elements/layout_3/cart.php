<div class="container-fluid pjScCart">
	<h2 class="text-uppercase text-primary pjScCartTitle"><strong><?php __('front_shopping_cart'); ?></strong></h2><br>
	<?php
	if (isset($tpl['arr']) && !empty($tpl['arr']))
	{
		?>
		<div class="panel panel-default">
			<table class="table table-striped">
				<thead class="text-uppercase">
					<tr>
						<th></th>
						<th class="hidden-xs"></th>
				      	<th width="100%">
				      		<div class="row">
					      		<div class="col-sm-6 col-md-7"><?php __('front_product'); ?></div>
					      		<div class="col-sm-2 hidden-xs"><?php __('front_price'); ?></div>
					      		<div class="col-sm-4 col-md-3 hidden-xs"><?php __('front_quantity'); ?></div>
					      	</div>
				      	</th>
				      	<th class="text-right"><?php __('front_total'); ?></th>
					</tr>
				</thead>
				
				<tbody>
					<?php
					$price_arr = pjFrontCart::pjActionCalcPrice($tpl['option_arr'], $tpl['cart_arr'], $tpl['stock_arr'], $tpl['extra_arr'], isset($tpl['o_shipping']) ? $tpl['o_shipping'] : null, isset($tpl['o_tax']) ? $tpl['o_tax'] : null, isset($tpl['o_free']) ? $tpl['o_free'] : null, @$_SESSION[$controller->defaultVoucher]);
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
						<tr>
							<td align="center">
				      			<a href="<?php echo pjUtil::getReferer(); ?>" class="scSelectorRemoveFromCart" data-hash="<?php echo $hash; ?>"><span class="glyphicon glyphicon-remove"></span></a>
				      		</td>
				      		<?php
				      		$attributes = array();
				      		if (isset($item['attr']) && !empty($item['attr']))
				      		{
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
				      			
				      		}
				      		$extras = array();
				      		if (isset($item['extra']) && !empty($item['extra']))
				      		{
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
				      		}
				      		$remaining_qty = (int) $tpl['stock_arr'][$cart_item['stock_id']]['qty'] - (int) @$tpl['order_arr'][$cart_item['stock_id']];
				      		if($product['is_digital'] == 1)
				      		{
				      			$remaining_qty = 99999;
				      		}
				      		$max_qty = $cart_item['qty'] + $remaining_qty;
				      		?>
				      		<td class="hidden-xs">
				      			<div>
					      			<a class="scSelectorProduct" href="<?php echo $href; ?>" data-id="<?php echo $product['id']; ?>" data-slug="<?php echo pjSanitize::html($slug); ?>">
			      						<img src="<?php echo PJ_INSTALL_URL . (!empty($tpl['image_arr'][$cart_item['stock_id']]) && is_file($tpl['image_arr'][$cart_item['stock_id']]) ? $tpl['image_arr'][$cart_item['stock_id']] : PJ_IMG_PATH . 'frontend/80x106.png'); ?>" alt="<?php echo pjSanitize::html($product['name']); ?>">
			      					</a>
			      					<p class="form-control-static visible-xs"><strong><?php echo pjCurrency::formatPrice($price_arr['p_arr'][$key]); ?></strong></p>
		      					</div>
				      		</td>
				      		<td>
				      			<div class="visible-xs text-center">
					      			<a class="scSelectorProduct" href="<?php echo $href; ?>" data-id="<?php echo $product['id']; ?>" data-slug="<?php echo pjSanitize::html($slug); ?>">
			      						<img src="<?php echo PJ_INSTALL_URL . (!empty($tpl['image_arr'][$cart_item['stock_id']]) && is_file($tpl['image_arr'][$cart_item['stock_id']]) ? $tpl['image_arr'][$cart_item['stock_id']] : PJ_IMG_PATH . 'frontend/80x106.png'); ?>" alt="<?php echo pjSanitize::html($product['name']); ?>">
			      					</a>
		      					</div>
				      			<div class="row">
				      				<div class="col-sm-6 col-md-7">
				      					<a href="<?php echo $href; ?>" class="scProductLink scSelectorProduct" data-id="<?php echo $product['id']; ?>" data-slug="<?php echo pjSanitize::html($slug); ?>"><?php echo pjSanitize::html($product['name']); ?></a><br>
				      					<p class="text-muted">
				      						<?php
				      						if (!empty($attributes))
				      						{
				      							echo join(", ", $attributes);
				      						} 
				      						?>
				      					</p>
				      					<p class="text-muted">
				      						<?php
				      						if (!empty($extras))
				      						{
				      							echo join(", ", $extras);
				      						} 
				      						?>
				      					</p>
				      				</div>
				      				<div class="col-sm-2 hidden-xs pjScCartPrice"><strong><?php echo pjCurrency::formatPrice($price_arr['p_arr'][$key]); ?></strong></div>
				      				<div class="col-sm-4 col-md-3">
				      					<div class="input-group" style="max-width: 150px;">
						    				<span class="input-group-btn scSelectorSpin scCallbackUpdate" data-direction="down">
						    					<button class="btn btn-default" type="button">
						    						<span class="glyphicon glyphicon-minus"></span>
						    					</button>
						    				</span>
						    				<input type="text" name="qty[<?php echo $hash; ?>]" class="form-control text-center scSelectorSpinValue" value="<?php echo (int) $cart_item['qty']; ?>" data-step="1" data-min="1" data-max="<?php echo (int) $max_qty; ?>" maxlength="<?php echo strlen($max_qty); ?>" readonly="readonly">
						    				<span class="input-group-btn scSelectorSpin scCallbackUpdate" data-direction="up">
						    					<button class="btn btn-default" type="button">
						    						<span class="glyphicon glyphicon-plus"></span>
						    					</button>
						    				</span>
						    			</div>
				      				</div>
									<div class="col-sm-12 text-right text-warning scMaximumItems" data-text="<?php __('front_maximum_items'); ?>"></div>
				      			</div>
				      		</td>
				      		<td class="text-right pjScCartPrice"><strong><?php echo pjCurrency::formatPrice($price_arr['subtotal_arr'][$key]); ?></strong></td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
			
			<div class="panel-body form-horizontal">
				<div class="form-group">
					<label for="" class="col-xs-6 col-sm-4 col-sm-offset-4 text-right"><?php __('front_sub_total'); ?></label>
					<div class="col-xs-6 col-sm-4 pjScCartPrice"><?php echo pjCurrency::formatPrice($price_arr['amount']); ?></div>
				</div>
				<div class="form-group">
					<label for="" class="col-xs-6 control-label col-sm-4 col-sm-offset-4 text-right"><?php __('front_promo_code'); ?></label>
					<div class="col-xs-6 col-sm-4">
						<form action="" method="post" class="scSelectorVoucherForm">
							<input type="hidden" name="sc_voucher" value="1" />
							<p><input type="text" name="code" value="<?php echo $price_arr['discount'] > 0 ? pjSanitize::html($price_arr['voucher_code']) : NULL;?>" class="form-control" data-err="<?php echo $validate['voucher'];?>" autocomplete="off"></p>
							<p class="text-danger scSelectorNoticeMsg" style="display:none;"></p>
							<button class="btn btn-info scSelectorButton scSelectorApplyCode pjScBtnApply"><?php __('front_apply_code', false, true); ?></button>
							<?php
							if ($price_arr['discount'] > 0)
							{
								?>
								<button type="button" class="btn btn-default scSelectorButton pjScBtnSecondary pjScBtnRemoveCode"><?php __('front_btn_remove_discount', false, true); ?></button>
								<?php
							} 
							?>
						</form>
					</div>
				</div>
				<?php
				if ($price_arr['discount'] > 0)
				{
					?>
					<div class="form-group">
					    <label for="" class="col-xs-6 col-sm-4 col-sm-offset-4 text-right"><?php __('front_discount'); ?> (<?php echo $price_arr['discount_print']; ?>)</label>
					    <div class="col-xs-6 col-sm-4 pjScCartPrice"><?php echo pjCurrency::formatPrice($price_arr['discount']); ?></div>
					</div>
					<?php
				} 
				?>
				<form action="" method="post" class="scSelectorCartForm">
					<?php
					if ($price_arr['insurance'] > 0)
					{
						?>
						<div class="form-group">
						    <label for="" class="col-xs-6 col-sm-4 col-sm-offset-4 text-right"><?php __('front_insurance'); ?></label>
						    <div class="col-xs-6 col-sm-4 pjScCartPrice"><?php echo pjCurrency::formatPrice($price_arr['insurance']); ?></div>
						</div>
						<?php
					}
					if ($controller->pjActionShowShipping() && !empty($tpl['tax_arr']))
					{
						?>
						<div class="form-group">
						    <label for="" class="col-xs-6 control-label col-sm-4 col-sm-offset-4 text-right"><?php __('front_shipping_location'); ?></label>
						    <div class="col-xs-6 col-sm-4">
								<select name="tax_id" class="form-control scSelectorShipping<?php echo !empty($tpl['tax_arr']) ? ' required' : NULL; ?>" data-err="<?php echo $validate['tax'];?>">
									<option value=""><?php __('front_choose_location'); ?></option>
									<?php
									foreach ($tpl['tax_arr'] as $item)
									{
										?><option value="<?php echo $item['id']; ?>"<?php echo !isset($_SESSION[$controller->defaultTax]) || $_SESSION[$controller->defaultTax] != $item['id'] ? NULL : ' selected="selected"'; ?>><?php echo pjSanitize::html($item['location']); ?></option><?php
									}
									?>
								</select>
						    </div>
						</div>
						<?php
						if ($price_arr['shipping'] > 0)
						{
							?>
							<div class="form-group">
				    			<label for="" class="col-xs-6 col-sm-4 col-sm-offset-4 text-right"><?php __('front_shipping'); ?></label>
				    			<div class="col-xs-6 col-sm-4 pjScCartPrice"><?php echo pjCurrency::formatPrice($price_arr['shipping']); ?></div>
				  			</div>
							<?php
						}
					}
					if($price_arr['tax'] > 0)
					{
						?>
						<div class="form-group">
					    	<label for="" class="col-xs-6 col-sm-4 col-sm-offset-4 text-right"><?php __('front_tax'); ?></label>
					    	<div class="col-xs-6 col-sm-4 pjScCartPrice"><?php echo pjCurrency::formatPrice($price_arr['tax']); ?></div>
					 	</div>
					 	<?php
					} 
					?>
				 	<div class="form-group">
				    	<label for="" class="col-xs-6 col-sm-4 col-sm-offset-4 text-right"><?php __('front_total'); ?></label>
				    	<div class="col-xs-6 col-sm-4 pjScCartPrice"><strong><?php echo pjCurrency::formatPrice($price_arr['total']); ?></strong></div>
				 	</div>
				</form>
			</div><!-- panel-body -->
		</div><!-- panel-default -->
		<p align="right">
			<button type="button" class="btn btn-default scSelectorButton scSelectorContinueShopping pjScBtnSecondary"><?php __('front_back_products', false, true); ?></button>
			<button type="button" class="btn btn-default scSelectorButton scSelectorCheckout pjScBtnPrimary"><?php __('front_checkout', false, true); ?></button>
		</p>
		<?php
	}else{
		?><p><?php __('front_empty_shopping_cart'); ?></p><?php
	} 
	?>
</div>