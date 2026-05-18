<!-- product gallery styling in ShoppingCart3.css -->

<div class="container-fluid">
	<div class="pjScProductDetails">
		<div class="row">
			<?php
			list($max_height,) = explode(',', PJ_GALLERY_PRODUCT);

			// Verzamel alle geldige gallery afbeeldingen
			$gallery_images = [];
			if (!empty($tpl['product_arr']['gallery_arr'])) {
				foreach ($tpl['product_arr']['gallery_arr'] as $k => $image) {
					if (!empty($image['small_path']) && is_file(PJ_INSTALL_PATH . $image['small_path'])) {
						$display_path = !empty($image['default_path']) && is_file(PJ_INSTALL_PATH . $image['default_path'])
							? $image['default_path']
							: $image['large_path'];
						$cb = '?v=' . filemtime(PJ_INSTALL_PATH . $display_path);
						$gallery_images[] = [
							'small'   => $image['small_path'] . '?v=' . filemtime(PJ_INSTALL_PATH . $image['small_path']),
							'display' => $display_path . $cb,
							'large'   => $image['large_path'] . '?v=' . filemtime(PJ_INSTALL_PATH . $image['large_path']),
							'alt'     => pjSanitize::html($image['alt']),
						];
					}
				}
			}
			$first = !empty($gallery_images) ? $gallery_images[0] : null;

			$_pdetail_w = (int) (!empty($tpl['option_arr']['o_default_width']) ? $tpl['option_arr']['o_default_width'] : 0);
			$_pdetail_h = (int) (!empty($tpl['option_arr']['o_default_height']) ? $tpl['option_arr']['o_default_height'] : 0);
			if ($_pdetail_w <= 0 || $_pdetail_h <= 0) {
				list($_pdetail_w, $_pdetail_h) = explode(',', PJ_GALLERY_PRODUCT);
				$_pdetail_w = (int) $_pdetail_w;
				$_pdetail_h = (int) $_pdetail_h;
			}
			$detail_placeholder_ratio = $_pdetail_w . ' / ' . $_pdetail_h;
			?>
			<div class="col-md-7 col-sm-12">
				<div class="pjScProductGallery">
					<div class="sc-badge-stack">
						<span class="sc-image-badge sc-image-badge--detail" style="display: none;"></span>
						<?php if (!empty($tpl['product_arr']['badge_name'])): ?>
							<span class="sc-product-badge" style="background-color: <?php echo pjSanitize::html($tpl['product_arr']['badge_color']); ?>"><?php echo pjSanitize::html($tpl['product_arr']['badge_name']); ?></span>
						<?php endif; ?>
						<?php if (!empty($tpl['product_arr']['is_digital']) && (int) $tpl['product_arr']['is_digital'] === 1): ?>
							<span class="sc-product-badge sc-product-badge--digital"><?php echo __('front_badge_digital', true) ?: 'Digital product'; ?></span>
						<?php endif; ?>
					</div>

					<?php if ($first): ?>
					<div class="sc-gallery-main">
						<a href="<?php echo PJ_INSTALL_URL . $first['large']; ?>" class="scSelectorFancy sc-gallery-main-link">
							<img src="<?php echo PJ_INSTALL_URL . $first['display']; ?>"
								alt="<?php echo $first['alt']; ?>"
								class="sc-gallery-main-img scSelectorProductPic" />
							<span class="sc-gallery-zoom"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="10" cy="10" r="7"/><line x1="15" y1="15" x2="21" y2="21"/><line x1="10" y1="7" x2="10" y2="13"/><line x1="7" y1="10" x2="13" y2="10"/></svg></span>
						</a>
					</div>

					<?php if (count($gallery_images) > 1): ?>
					<div class="sc-gallery-thumbs">
						<?php foreach ($gallery_images as $idx => $img): ?>
						<div class="sc-gallery-thumb<?php echo $idx === 0 ? ' active' : ''; ?>"
							data-display="<?php echo PJ_INSTALL_URL . $img['display']; ?>"
							data-large="<?php echo PJ_INSTALL_URL . $img['large']; ?>">
							<img src="<?php echo PJ_INSTALL_URL . $img['small']; ?>"
								alt="<?php echo $img['alt']; ?>"
								loading="lazy" />
						</div>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>

					<?php foreach ($gallery_images as $img): ?>
					<a href="<?php echo PJ_INSTALL_URL . $img['large']; ?>" rel="fancy_group" style="display:none"></a>
					<?php endforeach; ?>
					<?php else: ?>
					<span class="sc-noimg-placeholder sc-noimg-placeholder--detail" style="aspect-ratio: <?php echo $detail_placeholder_ratio; ?>;" aria-label="<?php echo pjSanitize::html($tpl['product_arr']['name']); ?>">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
							<circle cx="8.5" cy="8.5" r="1.5"/>
							<path d="M21 15l-5-5L5 21"/>
						</svg>
					</span>
					<?php endif; ?>
				</div>
			</div>

			<div class="col-md-5 col-sm-12">
				<h3 class="text-primary text-uppercase pjScProductTitle"><strong><?php echo pjSanitize::html($tpl['product_arr']['name']); ?></strong></h3>
				<?php
				if ((int) $tpl['product_arr']['status'] == 1)
				{
					?>
					<p class="h4">
						<?php
						if($tpl['product_arr']['price'] == $tpl['product_arr']['max_price'])
						{
							?>
							<strong class="scSelectorPrice"><?php echo pjCurrency::formatPrice($tpl['product_arr']['price']); ?></strong>
							<span style="display:none;" class="scHiddenMinPrice"><?php echo pjCurrency::formatPrice($tpl['product_arr']['price']); ?></span>
							<?php
						}else{
							?>
							<strong class="scSelectorPrice"><?php __('front_price_from');?> <?php echo pjCurrency::formatPrice($tpl['product_arr']['price']); ?></strong>
							<span style="display:none;" class="scHiddenMinPrice"><?php __('front_price_from');?> <?php echo pjCurrency::formatPrice($tpl['product_arr']['price']); ?></span>
							<?php
						}
						?>
						<input type="hidden" class="scInputMinPrice" value="<?php echo $tpl['product_arr']['price'];?>" />
					</p>
					<?php
				} else {
					?><p class="h4"><strong><?php __('front_not_available'); ?></strong></p><?php
				}
				?>
				<?php if (!empty($tpl['product_arr']['short_desc'])) { ?>
					<div class="pjScProductShortDesc hidden-xs"><?php echo !empty($tpl['product_arr']['short_desc']) ? nl2br(stripslashes($tpl['product_arr']['short_desc'])) : '';?></div>
				<?php } ?>

				<?php
				$product_statuses = __('product_statuses', true);
				foreach ($tpl['product_arr']['image_arr'] as $k => $v)
				{
					$stock_display_path = !empty($v['default_path']) && is_file(PJ_INSTALL_PATH . $v['default_path'])
						? $v['default_path']
						: $v['large_path'];
					?><span class="scSelectorStockThumb" data-stock_id="<?php echo $v['stock_id']; ?>" data-src="<?php echo PJ_INSTALL_URL . $stock_display_path; ?>" data-large="<?php echo PJ_INSTALL_URL . $v['large_path']; ?>" style="display: none"></span><?php
				}
				?>
				<div class="panel panel-default pjScProductFormContainer">
					<div class="panel-body">
						<form action="" method="post" class="scSelectorProductForm">
							<input type="hidden" name="product_id" value="<?php echo $controller->_get->toInt('id'); ?>" />
							<input type="hidden" name="is_digital" value="<?php echo (int) $tpl['product_arr']['is_digital']; ?>" />
							<div class="pjScProductAttributesContainer">
								<div class="row">
									<?php
									$inStock = (isset($tpl['stock_attr_arr']) && !empty($tpl['stock_attr_arr'])) || ($tpl['product_arr']['is_digital'] == '1');
									if ($inStock)
									{
										if (isset($tpl['attr_arr']) && !empty($tpl['attr_arr']))
										{
											foreach ($tpl['attr_arr'] as $row => $attr)
											{
												?>
												<div class="col-xs-12 col-sm-6 col-md-6">
													<div class="form-group">
														<label><?php echo pjSanitize::html($attr['name']); ?></label>
														<select name="attr[<?php echo $attr['id']; ?>]" class="form-control scSelectorAttr pjScAttributes required" data-row="<?php echo $row; ?>" data-id="<?php echo $attr['id']; ?>" data-choose="-- <?php __('front_choose'); ?> --" data-msg-required="<?php __('front_field_required');?>">
															<option value="">-- <?php __('front_choose'); ?> --</option>
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
															}else {
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
											$max_qty = (int) $tpl['stock_arr'][0]['qty'];
											if($tpl['product_arr']['is_digital'] == '1')
											{
												$max_qty = 999999;
											}
											?>
											<div class="col-xs-12 col-sm-6 col-md-6">
												<div class="form-group">
													<label><?php __('front_quantity'); ?></label>
													<div class="input-group">
														<span class="input-group-btn scSelectorSpin" data-direction="down">
															<button class="btn btn-default" type="button">
																<span class="glyphicon glyphicon-minus"></span>
															</button>
														</span>
														<input type="text" name="qty" class="form-control scProductSpinValue scSelectorSpinValue text-center" value="1" data-step="1" data-min="1" data-max="<?php echo $max_qty; ?>" maxlength="<?php echo strlen($max_qty); ?>" readonly="readonly"/>
														<span class="input-group-btn scSelectorSpin" data-direction="up" >
															<button class="btn btn-default" type="button">
																<span class="glyphicon glyphicon-plus"></span>
															</button>
														</span>
													</div>
												</div>
											</div>
											<div class="col-xs-12 col-sm-6 col-md-6">
												<div class="form-group">
													<label class="label-empty">&nbsp;</label>
													<div>
														<?php
														if ((int) $tpl['product_arr']['status'] == 1 && !empty($tpl['product_arr']['stockId']))
														{
															?><button class="btn btn-primary scButton scButtonDark scButtonDarkCart scSelectorButton scSelectorAdd2Cart pjScBtnPrimary"><?php __('front_buy_now', false, true); ?></button><?php
														}else{
															?><button class="btn btn-primary" disabled="disabled"><?php __('front_out_of_stock', false, true); ?></button><?php
														}
														?>
													</div>
												</div>
											</div>
											<div class="col-sm-12 text-warning scMaximumItems" data-text="<?php __('front_maximum_items'); ?>"></div>
											<?php
											if (isset($tpl['extra_arr']) && !empty($tpl['extra_arr']))
											{
												foreach ($tpl['extra_arr'] as $k => $extra)
												{
													switch ($extra['type'])
													{
														case 'single':
															?>
															<div class="col-xs-12<?php echo (int) $extra['is_mandatory'] === 0 ? ' checkbox' : ' radio'?>">
																<label>
																	<?php
																	if ((int) $extra['is_mandatory'] === 0)
																	{
																		?><input type="checkbox" name="extra[<?php echo $k; ?>]" value="<?php echo $extra['id']; ?>" data-price="<?php echo $extra['price']; ?>" class="scSelectorExtra" /> <?php echo pjSanitize::html($extra['name']); ?> (<?php echo pjCurrency::formatPrice($extra['price']); ?>)<?php
																	}else{
																		?><input type="radio" checked="checked" name="extra[<?php echo $k; ?>]" value="<?php echo $extra['id']; ?>" data-price="<?php echo $extra['price']; ?>" class="scSelectorExtra" /> <?php echo pjSanitize::html($extra['name']); ?> (<?php echo pjCurrency::formatPrice($extra['price']); ?>)<?php
																	}
																	?>
																</label>
															</div>
															<?php
															break;
														case 'multi':
															?>
															<div class="col-md-6">
																<?php echo pjSanitize::html($extra['title']); ?>
																<select name="extra[<?php echo $k; ?>]" class="form-control scSelectorExtra<?php echo (int) $extra['is_mandatory'] !== 0 ? ' required' : NULL;?>"  data-msg-required="<?php __('front_field_required');?>">
																	<option value="" data-price="0"><?php __('front_select_extra'); ?></option>
																	<?php
																	foreach ($extra['extra_items'] as $ei)
																	{
																		?>
																		<option value="<?php echo $extra['id']; ?>.<?php echo $ei['id']; ?>" data-price="<?php echo $ei['price']; ?>"><?php echo pjSanitize::html($ei['name']); ?> (<?php echo pjCurrency::formatPrice($ei['price']); ?>)</option>
																		<?php
																	}
																	?>
																</select>
																<br/>
															</div>
															<?php
															break;
													}
												}

											}
										}
									}else{
										if ((int) $tpl['option_arr']['o_disable_orders'] === 0)
										{
											?><div class="col-sm-12"><span class="scProductOutOfStock"><?php __('front_out_of_stock'); ?></span></div><?php
										}
									}
									?>
								</div>
							</div>
						</form>
					</div>
					<div class="panel-footer hidden-xs">
						<div class="row">
							<div class="col-sm-5">
								<?php
								if ($inStock)
								{
									?>
									<a href="#" class="btn scButtonAdd2Favs scSelectorAdd2Favs">
										<span class="glyphicon glyphicon-heart"></span>
										<?php __('front_add_to_favs'); ?>
									</a>
									<?php
								}
								?>
								<a href="#" class="btn scButtonSend2Friend scSelectorSend2Friend" data-pj-target="scSelectorSend2FriendBox">
									<span class="glyphicon glyphicon-user"></span>
									<?php __('front_send_to_friend'); ?>
								</a>
							</div>
							<?php
							if (!isset($share_url))
							{
								$share_url = urlencode($href);
							}
							if (!isset($share_title))
							{
								$share_title = urlencode(pjSanitize::html($tpl['product_arr']['name']));
							}
							?>
							<div class="col-sm-7">
								<a target="_blank" href="https://www.facebook.com/sharer.php?u=<?php echo $share_url; ?>&t=<?php echo $share_title; ?>" href="#" class="btn">
									<span class="glyphicon glyphicon-share-alt"></span>
									Facebook
								</a>
								<a target="_blank" href="https://twitter.com/intent/tweet?source=webclient&text=<?php echo $share_title; ?>&url=<?php echo $share_url; ?>" class="btn">
									<span class="glyphicon glyphicon-share-alt"></span>
									Twitter
								</a>
								<a target="_blank" href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $share_url; ?>&title=<?php echo $share_title; ?>&source=<?php echo $linkedin_source; ?>" class="btn">
									<span class="glyphicon glyphicon-share-alt"></span>
									Linkedin
								</a>
							</div>
						</div>
					</div>
				</div>
				<div class="panel panel-default scSelectorSend2FriendBox" style="display:none;">
					<div class="panel-body">
						<h5 class="text-uppercase pjScProductDescriptionTitle"><strong><?php __('front_send_to_friend'); ?></strong></h5>
						<form role="form" action="" method="post" class="scSelectorSend2FriendForm">
							<input type="hidden" name="id" value="<?php echo (int) $tpl['product_arr']['id']; ?>" />
							<input type="hidden" name="url" value="<?php echo pjSanitize::html($href); ?>" />

							<div class="alert scSelectorNoticeMsg" role="alert" style="display:none;"></div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="your_email"><?php __('front_s2f_your_email'); ?></label>
										<input type="email" class="form-control" name="your_email" id="your_email" data-err="<?php echo $validate['email'];?>" data-email="<?php echo $validate['email_invalid'];?>"/>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="your_name"><?php __('front_s2f_your_name'); ?></label>
										<input type="text" class="form-control" name="your_name" id="your_name" data-err="<?php echo $validate['name'];?>">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="friend_email"><?php __('front_s2f_friend_email'); ?></label>
										<input type="email" class="form-control" name="friend_email" id="friend_email" data-err="<?php echo $validate['email'];?>" data-email="<?php echo $validate['email_invalid'];?>"/>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="friend_name"><?php __('front_s2f_friend_name'); ?></label>
										<input type="text" class="form-control" name="friend_name" id="friend_name" data-err="<?php echo $validate['name'];?>">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="captcha"><?php __('bf_captcha'); ?></label>
										<div>
											<img src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&amp;action=pjActionCaptcha&amp;rand=<?php echo rand(1, 99999); ?><?php echo $controller->_get->check('session_id') && $controller->_get->toString('session_id') != '' ? '&session_id=' . $controller->_get->toString('session_id') : '';?>" alt="Captcha" style="vertical-align: middle; display:block;float: left; margin-right: 3px;cursor: pointer;" class="scSelectorCaptcha"/>
											<input type="text" class="form-control" name="captcha" id="captcha" maxlength="6" style="width: 100px;" data-err="<?php echo $validate['captcha'];?>" data-captcha="<?php echo $validate['captcha_wrong'];?>">
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<button type="button" class="btn btn-default scSelectorSend2FriendSubmit pjScBtnPrimary"><?php __('front_send', false, true); ?></button>
										<button type="button" class="btn btn-default scSelectorSend2FriendCancel pjScBtnSecondary" data-pj-target="scSelectorSend2FriendBox"><?php __('front_cancel', false, true); ?></button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
				<?php
				if ((int) $tpl['option_arr']['o_disable_orders'] === 1)
				{
					if (isset($tpl['extra_arr']) && !empty($tpl['extra_arr']))
					{
						$required_extras = array();
						$not_required_extras = array();
						foreach ($tpl['extra_arr'] as $extra)
						{
							if((int) $extra['is_mandatory'] !== 0)
							{
								$required_extras[] = $extra;
							}else{
								$not_required_extras[] = $extra;
							}
						}
						if(!empty($required_extras))
						{
							?>
							<p class="text-uppercase pjScProductDescriptionTitle"><strong><?php __('front_extras'); ?>:</strong></p>
							<?php
							foreach ($required_extras as $extra)
							{
								switch ($extra['type'])
								{
									case 'single':
										?><p class=""><?php echo pjSanitize::html($extra['name']);?></p><?php
										break;
									case 'multi':
										?><p class=""><?php echo pjSanitize::html($extra['title']);?></p><?php
										foreach ($extra['extra_items'] as $ei)
										{
											?><p class="text-indent"><?php echo pjSanitize::html($ei['name']);?></p><?php
										}
										break;
								}
							}
						}
						if(!empty($not_required_extras))
						{
							?>
							<p class="text-uppercase pjScProductDescriptionTitle"><strong><?php __('front_not_required_extras'); ?>:</strong></p>
							<?php
							foreach ($not_required_extras as $extra)
							{
								switch ($extra['type'])
								{
									case 'single':
										?><p class=""><?php echo pjSanitize::html($extra['name']);?></p><?php
										break;
									case 'multi':
										?><p class=""><?php echo pjSanitize::html($extra['title']);?></p><?php
										foreach ($extra['extra_items'] as $ei)
										{
											?><p class="text-indent"><?php echo pjSanitize::html($ei['name']);?></p><?php
										}
										break;
								}
							}
						}
					}
				}
				?>
			</div>
		</div>

		<?php if (!empty($tpl['product_arr']['full_desc'])) { ?>
			<div class="hidden-xs">
				<p class="text-uppercase pjScProductDescriptionTitle"><strong><?php __('front_description'); ?></strong></p>
				<p class="pjScProductFullDescription"><?php echo !empty($tpl['product_arr']['full_desc']) ? stripslashes($tpl['product_arr']['full_desc']) : ''; ?></p>
			</div>
		<?php } ?>

		<div class="pjScSeeMoreOnMobile visible-xs">
			<div class="pjScProductInfo">
				<div class="panel panel-default">
					<div class="panel-footer">
						<div class="row">
							<div class="col-sm-5">
								<?php
								if ($inStock)
								{
									?>
									<a href="#" class="btn scButtonAdd2Favs scSelectorAdd2Favs">
										<span class="glyphicon glyphicon-heart"></span>
										<?php __('front_add_to_favs'); ?>
									</a>
									<?php
								}
								?>
								<a href="#" class="btn scButtonSend2Friend scSelectorSend2Friend" data-pj-target="scSelectorSend2FriendBoxOnMobile">
									<span class="glyphicon glyphicon-user"></span>
									<?php __('front_send_to_friend'); ?>
								</a>
							</div>
							<?php
							if (!isset($share_url))
							{
								$share_url = urlencode($href);
							}
							if (!isset($share_title))
							{
								$share_title = urlencode(pjSanitize::html($tpl['product_arr']['name']));
							}
							?>
							<div class="col-sm-7">
								<a target="_blank" href="https://www.facebook.com/sharer.php?u=<?php echo $share_url; ?>&t=<?php echo $share_title; ?>" href="#" class="btn">
									<span class="glyphicon glyphicon-share-alt"></span>
									Facebook
								</a>
								<a target="_blank" href="https://twitter.com/intent/tweet?source=webclient&text=<?php echo $share_title; ?>&url=<?php echo $share_url; ?>" class="btn">
									<span class="glyphicon glyphicon-share-alt"></span>
									Twitter
								</a>
								<a target="_blank" href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $share_url; ?>&title=<?php echo $share_title; ?>&source=<?php echo $linkedin_source; ?>" class="btn">
									<span class="glyphicon glyphicon-share-alt"></span>
									Linkedin
								</a>
							</div>
						</div>
					</div>
				</div>
				<div class="panel panel-default scSelectorSend2FriendBoxOnMobile" style="display:none;">
					<div class="panel-body">
						<h5 class="text-uppercase pjScProductDescriptionTitle"><strong><?php __('front_send_to_friend'); ?></strong></h5>
						<form role="form" action="" method="post" class="scSelectorSend2FriendForm">
							<input type="hidden" name="id" value="<?php echo (int) $tpl['product_arr']['id']; ?>" />
							<input type="hidden" name="url" value="<?php echo pjSanitize::html($href); ?>" />

							<div class="alert scSelectorNoticeMsg" role="alert" style="display:none;"></div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="your_email"><?php __('front_s2f_your_email'); ?></label>
										<input type="email" class="form-control" name="your_email" id="your_email" data-err="<?php echo $validate['email'];?>" data-email="<?php echo $validate['email_invalid'];?>"/>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="your_name"><?php __('front_s2f_your_name'); ?></label>
										<input type="text" class="form-control" name="your_name" id="your_name" data-err="<?php echo $validate['name'];?>">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="friend_email"><?php __('front_s2f_friend_email'); ?></label>
										<input type="email" class="form-control" name="friend_email" id="friend_email" data-err="<?php echo $validate['email'];?>" data-email="<?php echo $validate['email_invalid'];?>"/>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="friend_name"><?php __('front_s2f_friend_name'); ?></label>
										<input type="text" class="form-control" name="friend_name" id="friend_name" data-err="<?php echo $validate['name'];?>">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="captcha"><?php __('bf_captcha'); ?></label>
										<div>
											<img src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&amp;action=pjActionCaptcha&amp;rand=<?php echo rand(1, 99999); ?><?php echo $controller->_get->check('session_id') && $controller->_get->toString('session_id') != '' ? '&session_id=' . $controller->_get->toString('session_id') : '';?>" alt="Captcha" style="vertical-align: middle; display:block;float: left; margin-right: 3px;cursor: pointer;" class="scSelectorCaptcha"/>
											<input type="text" class="form-control" name="captcha" id="captcha" maxlength="6" style="width: 100px;" data-err="<?php echo $validate['captcha'];?>" data-captcha="<?php echo $validate['captcha_wrong'];?>">
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<button type="button" class="btn btn-default scSelectorSend2FriendSubmit pjScBtnPrimary"><?php __('front_send', false, true); ?></button>
										<button type="button" class="btn btn-default scSelectorSend2FriendCancel pjScBtnSecondary" data-pj-target="scSelectorSend2FriendBoxOnMobile"><?php __('front_cancel', false, true); ?></button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
				<?php if (!empty($tpl['product_arr']['short_desc'])) { ?>
					<div class="pjScProductShortDesc"><?php echo !empty($tpl['product_arr']['short_desc']) ? nl2br(stripslashes($tpl['product_arr']['short_desc'])) : '';?></div>
				<?php } ?>
				<?php if (!empty($tpl['product_arr']['full_desc'])) { ?>
					<p class="text-uppercase pjScProductDescriptionTitle"><strong><?php __('front_description'); ?></strong></p>
					<p class="pjScProductFullDescription"><?php echo !empty($tpl['product_arr']['full_desc']) ? stripslashes($tpl['product_arr']['full_desc']) : ''; ?></p>
				<?php } ?>
			</div>
		</div>

	</div>
	<br/><br/>
</div>

<div class="modal fade" id="scTermModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-pj-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title" id="myModalLabel"><?php __('front_select_attribute', false, true); ?></h4>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-default pjScBtnSecondary" data-pj-dismiss="modal">OK</button>
			</div>
		</div>
	</div>
</div>

<?php
if (isset($tpl['similar_arr']) && !empty($tpl['similar_arr']))
{
	?>
	<div class="container-fluid">
		<h4 class="text text-uppercase"><strong><?php __('front_similar'); ?></strong></h4>
		<br/>
		<ul class="list-unstyled row text-center">
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
					$href = $tpl['option_arr']['o_install_url'] . '/' . $slug;
				} else {
					# Non-Seo friendly URLs ---------------------
					$href = $tpl['option_arr']['o_install_url']. '/' . ((!empty($tpl['option_arr']['o_page_prefix'])) ? $tpl['option_arr']['o_page_prefix'] . '-' : '') . 'product/' . $similar['id'];
				}
				?>
				<li class="col-md-2">
					<p>
						<a href="<?php echo $href; ?>" data-id="<?php echo $similar['id']; ?>" data-slug="<?php echo pjSanitize::html($slug); ?>" class="scSelectorProduct"><img class="img-responsive" src="<?php echo is_file(PJ_INSTALL_PATH . $similar['pic']) ? PJ_INSTALL_URL . $similar['pic'] : PJ_INSTALL_URL . PJ_IMG_PATH . 'frontend/noimg.png'; ?>" alt="<?php echo pjSanitize::html($similar['name']); ?>" /></a>
					</p>
					<p><a href="<?php echo $href; ?>" class="scSelectorProduct" data-id="<?php echo $similar['id']; ?>" data-slug="<?php echo pjSanitize::html($slug); ?>"><strong><?php echo pjSanitize::html($similar['name']); ?></strong></a></p>
					<p><strong><?php echo pjCurrency::formatPrice($similar['price']); ?></strong></p>
				</li>
				<?php
			}
			?>
		</ul>
	</div>
	<?php
}
?>