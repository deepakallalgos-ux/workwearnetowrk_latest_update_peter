<?php
if (isset($tpl['status']) && $tpl['status'] == 'IP_BLOCKED') {
	?>
	<div class="container-fluid"><div class="alert alert-danger"><?php __('front_ip_address_blocked'); ?></div></div>
	<?php
} else {
	include PJ_VIEWS_PATH . 'pjFront/elements/header.php';
	$validate = str_replace(array('"', "'"), array('\"', "\'"), __('validate', true, true));
	if (isset($tpl['product_arr']) && !empty($tpl['product_arr']))
	{
		$slug = NULL;
		$linkedin_source = NULL;
		if ((int) $tpl['option_arr']['o_seo_url'] === 1)
		{
			# Seo friendly URLs ---------
			$category_id = NULL;
			if (!empty($tpl['product_arr']['category_ids']))
			{
				$category_id = max($tpl['product_arr']['category_ids']);
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
			
			$slug = sprintf("%s-%u.html", pjAppController::friendlyURL($tpl['product_arr']['name']), $tpl['product_arr']['id']);
			if (!empty($category_slug))
			{
				$slug = join("/", $category_slug) . '/' . $slug;
			}
			$href = pjUtil::getReferer() . '#!/' . $slug;
			$linkedin_source = PJ_INSTALL_URL . '?index.php&controller=pjFrontPublic&action=pjActionRouter&_escaped_fragment_=' . $slug;
		} else {
			# Non-Seo friendly URLs ---------------------
			$href = pjUtil::getReferer() . '#!/Product/' . $tpl['product_arr']['id'];
			$linkedin_source = PJ_INSTALL_URL . '?index.php&controller=pjFrontPublic&action=pjActionRouter&_escaped_fragment_=Product/' . $tpl['product_arr']['id'];
		}
		
		if($controller->_get->toInt('layout') != 3)
		{
			?>
			<div class="scProduct">
				<div class="scProductHeadingBox">
					<div class="scProductInfoInner">
						<div class="scProductName"><?php echo pjSanitize::html($tpl['product_arr']['name']); ?></div>
						<?php
						if ((int) $tpl['product_arr']['status'] == 1)
						{
							?>
							<div class="scProductPrice scSelectorPriceBox">
								<span class="scSelectorPrice"><?php echo pjCurrency::formatPrice($tpl['product_arr']['price']);?></span>
							</div>
							<?php
						} else {
							?>
							<div class="scProductPrice"><?php __('front_not_available'); ?></div>
							<?php
						}
						?>
					</div>
				</div>
						
				<div class="scProductGallery">
					<div class="scProductPic">
					<?php
					$medium_pic = $large_pic = NULL;
					if (!empty($tpl['product_arr']['pic']))
					{
						list($medium_pic, $large_pic) = explode("~:~", $tpl['product_arr']['pic']);
					}
					
					if (!empty($medium_pic) && is_file($medium_pic))
					{
						?><a href="<?php echo PJ_INSTALL_URL . $large_pic; ?>" rel="fancy_groupXXX" class="scSelectorFancy"><img src="<?php echo PJ_INSTALL_URL . $medium_pic; ?>" alt="<?php echo pjSanitize::html($tpl['product_arr']['name']); ?>" class="scSelectorProductPic" /></a><?php
					} elseif (!empty($tpl['product_arr']['gallery_arr'][0]['medium_path']) && is_file($tpl['product_arr']['gallery_arr'][0]['medium_path'])) {
						?><a href="<?php echo PJ_INSTALL_URL . $tpl['product_arr']['gallery_arr'][0]['large_path']; ?>" rel="fancy_groupXXX" class="scSelectorFancy"><img src="<?php echo PJ_INSTALL_URL . $tpl['product_arr']['gallery_arr'][0]['medium_path']; ?>" alt="<?php echo pjSanitize::html($tpl['product_arr']['name']); ?>" class="scSelectorProductPic" /></a><?php
					} else {
						?><img src="<?php echo PJ_INSTALL_URL . PJ_IMG_PATH; ?>frontend/noimg.jpg" alt="<?php echo pjSanitize::html($tpl['product_arr']['name']); ?>" class="scSelectorProductPic" /><?php
					}
					?>
					</div>
				<?php
				$img = 0;
				$cnt = count($tpl['product_arr']['gallery_arr']);
				foreach ($tpl['product_arr']['gallery_arr'] as $k => $image)
				{
					if ($img === 0)
					{
						?>
						<div class="scProductThumbs">
						<?php
					}
					if (!empty($image['small_path']) && is_file(PJ_INSTALL_PATH . $image['small_path']))
					{
						?><a href="<?php echo $href; ?>" rel="nofollow" data-src="<?php echo PJ_INSTALL_URL . $image['medium_path']; ?>" data-large="<?php echo PJ_INSTALL_URL . $image['large_path']; ?>" class="scSelectorProductThumb"><img src="<?php echo PJ_INSTALL_URL . $image['small_path']; ?>" alt="<?php echo pjSanitize::html($image['alt']); ?>" /></a>
						<a href="<?php echo PJ_INSTALL_URL . $image['large_path']; ?>" rel="fancy_group" style="display:none"></a>
						<?php
						$img++;
					}
					if ($k == $cnt - 1)
					{
						?>
						</div>
						<?php
					}
				}
				/*if ($img === 0)
				{
					?><img src="<?php echo PJ_INSTALL_URL . PJ_IMG_PATH; ?>frontend/noimg.png" alt="<?php echo pjSanitize::html($tpl['product_arr']['name']); ?>" /><?php
				}*/
				$product_statuses = __('product_statuses', true);
				foreach ($tpl['product_arr']['image_arr'] as $k => $v)
				{
					?><span class="scSelectorStockThumb" data-stock_id="<?php echo $v['stock_id']?>" data-src="<?php echo PJ_INSTALL_URL . $v['medium_path']; ?>" data-large="<?php echo PJ_INSTALL_URL . $v['large_path']; ?>" style="display: none"></span><?php
				}
				?>
				</div>
				<div class="scProductInfo">
					<div class="scProductInfoInner">
						<form action="" method="post" class="scSelectorProductForm">
							<input type="hidden" name="product_id" value="<?php echo $controller->_get->toInt('id'); ?>" />
							<input type="hidden" name="is_digital" value="<?php echo (int) $tpl['product_arr']['is_digital']; ?>" />
						
							<div class="scProductControl">
							<?php
							$inStock = isset($tpl['stock_attr_arr']) && !empty($tpl['stock_attr_arr']);
							if ($inStock)
							{
								?>
								<div class="scProductAttributes">
								<?php
								if (isset($tpl['attr_arr']) && !empty($tpl['attr_arr']))
								{
									foreach ($tpl['attr_arr'] as $row => $attr)
									{
										?>
										<div class="scProductAttrBox">
											<div class="scProductAttrName"><?php echo pjSanitize::html($attr['name']); ?>:</div>
											<div class="scProductAttrValues">
											<select name="attr[<?php echo $attr['id']; ?>]" class="scSelect scSelectorAttr" data-row="<?php echo $row; ?>" data-id="<?php echo $attr['id']; ?>">
											<?php
											if (isset($attr['child']) && !empty($attr['child']))
											{
												foreach ($attr['child'] as $child_index => $child)
												{
													foreach ($tpl['stock_attr_arr'] as $stock_id => $stock)
													{
														if (in_array($child['id'], $stock) || (isset($stock[$child['parent_id']]) && (int) $stock[$child['parent_id']] === 0))
														{
															if ($row == 0 && $child_index == 0)
															{
																$tmp_stock_id = $stock_id;
															}														
															if ($row > 0)
															{
																if (!isset($tpl['stock_attr_arr'][$tmp_stock_id][$child['parent_id']]) ||
																	$tpl['stock_attr_arr'][$tmp_stock_id][$child['parent_id']] != $child['id'])
																{
																	continue;
																}
															}
															?><option value="<?php echo $child['id']; ?>"><?php echo pjSanitize::html($child['name']); ?></option><?php
															break;
														}
													}
												}
											} else {
												?><option value=""><?php __('front_not_available'); ?></option><?php
											}
											?>
											</select>
											</div>
										</div>
										<?php
									}
								}
								if ((int) $tpl['option_arr']['o_disable_orders'] === 0)
								{
									?>
									<div class="scProductAttrBox">
										<div class="scProductAttrName"><?php __('front_quantity'); ?>:</div>
										<div class="scProductAttrValues">
										<?php
										switch ($controller->_get->toInt('layout'))
										{
											case 2:
												?>
												<select name="qty" class="scSelect scSelectorQty">
												<?php
												foreach (range(1, (int) $tpl['stock_arr'][0]['qty']) as $i)
												{
													?><option value="<?php echo $i; ?>"><?php echo $i; ?></option><?php
												}
												?>
												</select>
												<?php
												break;
											case 1:
											default:
												?>
												<div class="scProductSpinner">
													<a href="#" class="scProductSpinButton scSelectorSpin" data-direction="down" title="<?php __('front_down', false, true); ?>">-</a>
													<input type="text" name="qty" class="scProductSpinValue scSelectorSpinValue" value="1" data-step="1" data-min="1" data-max="<?php echo (int) $tpl['stock_arr'][0]['qty']; ?>" maxlength="<?php echo strlen($tpl['stock_arr'][0]['qty']); ?>" readonly="readonly" />
													<a href="#" class="scProductSpinButton scSelectorSpin" data-direction="up" title="<?php __('front_up', false, true); ?>">+</a>
												</div>
												<?php
												break;
										}
										?>
										</div>
									</div>
									<div class="scProductAttrBox">
										<div class="scProductAttrName">&nbsp;</div>
										<div class="scProductAttrValues">
										<?php
										if ((int) $tpl['product_arr']['status'] == 1 && !empty($tpl['product_arr']['stockId']))
										{
											?><input type="button" value="<?php __('front_buy_now', false, true); ?>" class="scButton scButtonDark scButtonDarkCart scSelectorButton scSelectorAdd2Cart" /><?php
										} else {
											?><span class="scProductOutOfStock"><?php __('front_out_of_stock'); ?></span><?php
										}
										?>
										</div>
									</div>
									<?php
								}
								?>
									<div class="scClearLeft"></div>
								</div>
								<?php
								if (isset($tpl['extra_arr']) && !empty($tpl['extra_arr']))
								{
									?>
									<div class="scProductExtras">
									<?php
									foreach ($tpl['extra_arr'] as $extra)
									{
										switch ($extra['type'])
										{
											case 'single':
												?>
												<div class="scProductExtraBox">
													<label><?php
													if ((int) $extra['is_mandatory'] === 0)
													{
													    ?><input type="checkbox" name="extra[]" value="<?php echo $extra['id']; ?>" data-price="<?php echo $extra['price']; ?>" class="scSelectorExtra" /> <?php echo pjSanitize::html($extra['name']); ?> (<?php echo pjCurrency::formatPrice($extra['price']); ?>)<?php
													} else {
													    ?><input type="radio" checked="checked" name="extra[]" value="<?php echo $extra['id']; ?>" data-price="<?php echo $extra['price']; ?>" class="scSelectorExtra" /> <?php echo pjSanitize::html($extra['name']); ?> (<?php echo pjCurrency::formatPrice($extra['price']); ?>)<?php
													}
													?></label>
												</div>
												<?php
												break;
											case 'multi':
												?>
												<div class="scProductExtraBox">
													<?php echo pjSanitize::html($extra['title']); ?>:
													<select name="extra[]" class="scSelect scSelectorExtra">
													<?php if ((int) $extra['is_mandatory'] === 0) : ?>
													<option value="" data-price="0"><?php __('front_select_extra'); ?></option>
													<?php endif; ?>
													<?php
													foreach ($extra['extra_items'] as $ei)
													{
														?>
														<option value="<?php echo $extra['id']; ?>.<?php echo $ei['id']; ?>" data-price="<?php echo $ei['price']; ?>"><?php echo pjSanitize::html($ei['name']); ?> (<?php echo pjCurrency::formatPrice($ei['price']); ?>)</option>
														<?php
													}
													?>
													</select>
												</div>
												<?php
												break;
										}
									}
									?></div><?php
								}
							} else {
								if ((int) $tpl['option_arr']['o_disable_orders'] === 0)
								{
									?><span class="scProductOutOfStock"><?php __('front_out_of_stock'); ?></span><?php
								}
							}
							?>
								<div class="scProductControlFoot">
									<div class="scInterMenu">
										<?php if ($inStock) : ?>
										<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="scButtonAdd2Favs scSelectorAdd2Favs"><?php __('front_add_to_favs'); ?></a>
										<?php endif; ?>
										<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="scButtonSend2Friend scSelectorSend2Friend"><?php __('front_send_to_friend'); ?></a>
									</div>
									
									<div class="scSocialMenu">
										<?php
										if (!isset($share_url))
										{
											$share_url = urlencode($href);
										}
										if (!isset($share_title))
										{
											$share_title = urlencode(stripslashes($tpl['product_arr']['name']));
										}
										?>
										<a target="_blank" href="https://www.facebook.com/sharer.php?u=<?php echo $share_url; ?>&t=<?php echo $share_title; ?>" class="scSocial scSocialFacebook"></a>
										<a target="_blank" href="https://twitter.com/intent/tweet?source=webclient&text=<?php echo $share_title; ?>&url=<?php echo $share_url; ?>" class="scSocial scSocialTwitter"></a>
										<a target="_blank" href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $share_url; ?>&title=<?php echo $share_title; ?>&source=<?php echo $share_url; ?>" class="scSocial scSocialLinkedIn"></a>
									</div>
								</div>
							</div>
							
						</form>
						
						<div class="scSelectorSend2FriendBox" style="display: none">
							<form action="" method="post" class="scSelectorSend2FriendForm">
								<input type="hidden" name="id" value="<?php echo (int) $tpl['product_arr']['id']; ?>" />
								<input type="hidden" name="url" value="<?php echo pjSanitize::html($href); ?>" />
								<h3 class="scHeading"><?php __('front_send_to_friend'); ?></h3>
								<div class="scNotice scSelectorNoticeMsg" style="display: none"></div>
								<p class="scPaperChain">
									<label class="scTitle"><?php __('front_s2f_your_email'); ?></label>
									<input type="text" name="your_email" class="scText" data-err="<?php echo $validate['email'];?>" data-email="<?php echo $validate['email_invalid'];?>"/>
								</p>
								<p class="scPaperChain">
									<label class="scTitle"><?php __('front_s2f_your_name'); ?></label>
									<input type="text" name="your_name" class="scText" data-err="<?php echo $validate['name'];?>"/>
								</p>
								<p class="scPaperChain">
									<label class="scTitle"><?php __('front_s2f_friend_email'); ?></label>
									<input type="text" name="friend_email" class="scText" data-err="<?php echo $validate['email'];?>" data-email="<?php echo $validate['email_invalid'];?>"/>
								</p>
								<p class="scPaperChain">
									<label class="scTitle"><?php __('front_s2f_friend_name'); ?></label>
									<input type="text" name="friend_name" class="scText" data-err="<?php echo $validate['name'];?>"/>
								</p>
								<p class="scPaperChain">
									<label class="scTitle"><?php __('bf_captcha'); ?></label>
									<img src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&amp;action=pjActionCaptcha&amp;rand=<?php echo rand(1, 99999); ?>" alt="Captcha" style="vertical-align: middle" class="scSelectorCaptcha"/>
									<input type="text" name="captcha" class="scText scW100" maxlength="6" data-err="<?php echo $validate['captcha'];?>" data-captcha="<?php echo $validate['captcha_wrong'];?>"/>
								</p>
								<p class="scPaperUnchained">
									<input type="submit" class="scButton scButtonDark scButtonDarkNext scSelectorSend2FriendSubmit" value="<?php __('front_send', false, true); ?>" />
									<input type="button" class="scButton scButtonLight scButtonLightPrev scSelectorSend2FriendCancel" value="<?php __('front_cancel', false, true); ?>" />
								</p>
							</form>
						</div>
						
						<h3 class="scHeading"><?php __('front_description'); ?></h3>
						<div class="scProductOpt scProductDesc scHTML"><?php echo stripslashes($tpl['product_arr']['full_desc']); ?></div>
					</div>
				</div>
				<div class="scClearBoth"></div>
				<?php
				if (isset($tpl['similar_arr']) && !empty($tpl['similar_arr']))
				{
					?>
					<div class="scProductSimilarWrapper">
						<h3 class="scHeading"><?php __('front_similar'); ?></h3>
						<div class="scProductSimilarBox">
						<?php
						foreach ($tpl['similar_arr'] as $similar)
						{
							$slug = NULL;
							if ((int) $tpl['option_arr']['o_seo_url'] === 1)
							{
								# Seo friendly URLs ---------
								$category_id = NULL;
								if (!empty($similar['category_ids']))
								{
									$category_id = max($similar['category_ids']);
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
								
								$slug = sprintf("%s-%u.html", pjAppController::friendlyURL($similar['name']), $similar['id']);
								if (!empty($category_slug))
								{
									$slug = join("/", $category_slug) . '/' . $slug;
								}
								$href = pjUtil::getReferer() . '#!/' . $slug;
							} else {
								# Non-Seo friendly URLs ---------------------
								$href = pjUtil::getReferer() . '#!/Product/' . $similar['id'];
							}
							?>
							<div class="scProductSimilar">
								<div class="scProductSimilarPic">
									<a href="<?php echo $href; ?>" data-id="<?php echo $similar['id']; ?>" data-slug="<?php echo pjSanitize::html($slug); ?>" class="scSelectorProduct"><img src="<?php echo is_file(PJ_INSTALL_PATH . $similar['pic']) ? PJ_INSTALL_URL . $similar['pic'] : PJ_INSTALL_URL . PJ_IMG_PATH . 'frontend/noimg.jpg'; ?>" alt="<?php echo pjSanitize::html($similar['name']); ?>" /></a>
								</div>
								<div class="scProductSimilarInfo">
									<div class="scProductSimilarName">
										<a href="<?php echo $href; ?>" data-id="<?php echo $similar['id']; ?>" data-slug="<?php echo pjSanitize::html($slug); ?>" class="scSelectorProduct"><?php echo pjSanitize::html($similar['name']); ?></a>
									</div>
									<div class="scProductSimilarPrice">
										<div class="scProductSimilarPriceValue">
										<?php echo pjCurrency::formatPrice($similar['price']); ?>
										</div>
									</div>
								</div>
							</div>
							<?php
						}
						?>
						</div>
					</div>
					<?php
				}
				?>
			</div>
			<?php
		}else{
			include PJ_VIEWS_PATH . 'pjFront/elements/layout_3/product.php';
		}
	} else {
		?><div class="scMessage"><div class="scMessageIcon"></div><?php __('front_product_not_found'); ?></div><?php
	}
}
?>