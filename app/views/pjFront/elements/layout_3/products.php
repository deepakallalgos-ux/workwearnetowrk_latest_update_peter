<?php
if (isset($tpl['product_arr']) && !empty($tpl['product_arr']))
{
    $products_in_fav_arr = array();
    $cookie_value = isset($_COOKIE[$controller->defaultCookie]) && !empty($_COOKIE[$controller->defaultCookie]) ? base64_decode(stripslashes($_COOKIE[$controller->defaultCookie])) : '';
    $favs = unserialize($cookie_value);
    if ($favs) {
        foreach ($favs as $fav => $whatever)
        {
            $item = unserialize($fav);
            $hash = md5(serialize($item));
            $products_in_fav_arr[] = $hash;
        }
    }
	?>
	<div class="container-fluid">
		<ul class="list-unstyled row text-center pjScProducts">
			<?php
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
					$href = $tpl['option_arr']['o_install_url'] . '/' . $slug;
				} else {
					# Non-Seo friendly URLs ---------------------
					$href = $tpl['option_arr']['o_install_url'] . '/' . ((!empty($tpl['option_arr']['o_page_prefix'])) ? $tpl['option_arr']['o_page_prefix'] . '-' : '') . 'product/' . $product['id'];
				}
				$cnt_product_images = isset($tpl['product_image_arr'][$product['id']]) ? count($tpl['product_image_arr'][$product['id']]) : 0;
				$src = PJ_INSTALL_URL . PJ_IMG_PATH . 'frontend/noimg.png';
				if (isset($tpl['product_image_arr'][$product['id']][0]['medium_path']) && is_file(PJ_INSTALL_PATH . $tpl['product_image_arr'][$product['id']][0]['medium_path']))
				{
				    $src = PJ_INSTALL_URL . $tpl['product_image_arr'][$product['id']][0]['medium_path'];
				}
				list($max_width, ) = explode(',', PJ_GALLERY_MEDIUM);
				
				$stock_id = $product['stockId'];
				$stock_arr = array();
				$is_available = false;
				if ($product['status'] != 3) {
				    if (!empty($product['stockId_attr']))
				    {
				        $attrs = explode(",", $product['stockId_attr']);
				        foreach ($attrs as $attr)
				        {
				            list($_attr_id, $_attr_parent_id, $_stock_id, $_qty) = explode("_", $attr);
				            if ($product['is_digital'] == 0 && (int)$_qty > 0 && (!isset($tpl['order_arr'][$_stock_id]) || (isset($tpl['order_arr'][$_stock_id]) && ((int)$_qty - (int)$tpl['order_arr'][$_stock_id]) > 0))) {
				                $stock_arr[$_stock_id][] = $attr;
				                $stock_id = $_stock_id;
				            }
				        }
				        if ($stock_arr) {
				            $stock_arr = reset($stock_arr);
				            $is_available = true;
				        }
				    } elseif ($product['stockQty'] - (int) @$tpl['order_arr'][$product['stockId']] > 0 && !empty($product['stockId']) && $product['is_digital'] == 0) {
				        $is_available = true;
				    }
				}
				if ($product['is_digital'] == 1) {
				    $is_available = true;
				}
				?>
				<li class="col-xs-12 col-sm-4 col-md-3 col-lg-3 pjScProduct">
					<div class="pjScProductImages" data-id="<?php echo $product['id']; ?>" style="max-width: <?php echo $max_width;?>px;">
						<?php if ($cnt_product_images > 1) { ?>
							<div class="pjScProductDefaultGalleryImage">
								<a href="<?php echo $href; ?>" class="scSelectorProduct" data-id="<?php echo $product['id']; ?>" data-slug="<?php echo pjSanitize::html($slug); ?>">
        			        		<img class="img-responsive pjScProductImage" src="<?php echo $src; ?>" alt="<?php echo pjSanitize::html($product['name']); ?>" />
        			        	</a>
							</div>
    						<div class="owl-carousel owl-theme owl-carousel-hidden" id="owl-carousel-<?php echo $product['id']; ?>">
    							<?php foreach ($tpl['product_image_arr'][$product['id']] as $pimg) { 
    							    $gi_src = PJ_INSTALL_URL . PJ_IMG_PATH . 'frontend/noimg.png';
    							    if (is_file(PJ_INSTALL_PATH . $pimg['medium_path']))
    							    {
    							        $gi_src = PJ_INSTALL_URL . $pimg['medium_path'];
    							    }
    							    ?>
            			        	<a href="<?php echo $href; ?>" class="scSelectorProduct" data-id="<?php echo $product['id']; ?>" data-slug="<?php echo pjSanitize::html($slug); ?>">
            			        		<img class="owl-lazy pjScProductImage" data-src="<?php echo $gi_src; ?>" alt="<?php echo pjSanitize::html($product['name']); ?>">
            			        	</a>
        			        	<?php } ?>
        			        </div>
        			    <?php } else { ?>
        			    	<a href="<?php echo $href; ?>" class="scSelectorProduct" data-id="<?php echo $product['id']; ?>" data-slug="<?php echo pjSanitize::html($slug); ?>">
    			        		<img class="img-responsive pjScProductImage" src="<?php echo $src; ?>" alt="<?php echo pjSanitize::html($product['name']); ?>" />
    			        	</a>
        			    <?php } ?>
        			    <?php if ($is_available) { 
        			         $product_fav_arr = array();
        			         $product_fav_arr['product_id'] = $product['id'];
        			         $product_fav_arr['is_digital'] = (int) $product['is_digital'];
        			        ?>
        			    	<form action="" method="post" class="scSelectorProductForm" id="scSelectorProductForm_<?php echo $product['id'];?>" style="display: inline; vertical-align: top">
    		     				<input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
    							<input type="hidden" name="is_digital" value="<?php echo (int) $product['is_digital']; ?>" />
    							<?php
    							if ($stock_arr)
    							{
    							    foreach ($stock_arr as $attr)
    								{
    									list($attr_id, $attr_parent_id, $stock_id, $qty) = explode("_", $attr);
    									$product_fav_arr['attr'][$attr_parent_id] = $attr_id;
    									?>
    									<input type="hidden" name="attr[<?php echo $attr_parent_id; ?>]" class="scSelectorAttr" value="<?php echo $attr_id; ?>" />
    									<?php
    								}
    							}
    							if (!empty($product['m_extras']))
    							{
    								foreach ($product['m_extras'] as $ek => $ev)
    								{
    								    $product_fav_arr['extra'][$ek] = $ev;
    									?><input type="hidden" name="extra[<?php echo $ek; ?>]" value="<?php echo $ev; ?>" /><?php
    								}
    							}
    							?>
    							<input type="hidden" name="qty" value="1" />
    							<input type="hidden" name="stock_id" value="<?php echo $stock_id; ?>"/>
    		     			</form>
    		     			<?php 
    		     			$product_fav_arr['qty'] = 1;
    		     			$product_fav_arr['stock_id'] = $stock_id;
    		     			$product_fav_hash = md5(serialize(pjUtil::stripFav($product_fav_arr)));
    		     			?>
    			        	<a href="javascript:void(0);" class="btn scButtonAdd2Favs <?php echo in_array($product_fav_hash, $products_in_fav_arr) ? 'scSelectorRemoveFavFromProductsList' : 'scSelectorAddFavFromProductsList';?>" data-hash="<?php echo $product_fav_hash;?>" data-id="<?php echo $product['id'];?>"><span class="glyphicon <?php echo in_array($product_fav_hash, $products_in_fav_arr) ? 'glyphicon-heart' : 'glyphicon-heart-empty';?>" id="scProductGlyphiconFavs_<?php echo $product['id'];?>"></span></a>
    			        <?php } 
    			        if (isset($tpl['product_attr_arr'][$product['id']])) {
    			            $product_attr_arr = array();
    			            foreach ($tpl['product_attr_arr'][$product['id']] as $p_attr) {
    			                $p_attr_child_arr = array();
    			                if (isset($p_attr['child'])) {
    			                    foreach ($p_attr['child'] as $p_attr_child) {
    			                        $p_attr_child_arr[] = pjSanitize::html($p_attr_child['name']);
    			                    }
    			                }
    			                $product_attr_arr[] = pjSanitize::html($p_attr['name']).': '.implode('; ', $p_attr_child_arr);
    			            }
    			            ?>
    			     		<div class="pjScProductAttributes"><?php echo implode('<br/>', $product_attr_arr);?></div>
    			     		<?php 
    			         }
    			        ?>
			        </div>
			        <div class="pjScProductNamePrice">
    			     	<p class="pjScProductName">
    			     		<a href="<?php echo $href; ?>" class="scSelectorProduct" data-id="<?php echo $product['id']; ?>" data-slug="<?php echo pjSanitize::html($slug); ?>"><?php echo pjSanitize::html($product['name']); ?></a>
    			     	</p>
    			     	<?php
    			     	if($product['is_digital'] == 0)
    			     	{ 
    				     	?>
    			     		<p class="pjScProductPrice"><?php if ((float) $product['min_price'] < (float) $product['max_price']){ __('front_price_from'); }?> <?php echo pjCurrency::formatPrice($product['min_price']); ?></p>
    			     		<?php
    			     	}else{
    			     		?>
         					<p class="pjScProductPrice"><?php echo pjCurrency::formatPrice($product['price']); ?></p>
         					<?php
    			     	} ?>
    			     </div>
			     	<?php
			     	if ($is_available)
			     	{
			     		?>
			     		<div aria-label="Default button group" role="group" class="btn-group">
				     		<?php
				     		if (!pjUtil::isOptionEnumYes($tpl['option_arr'], 'o_disable_orders'))
				     		{
				     			?>
				     			<form action="" method="post" class="scSelectorBuyNowForm" style="display: inline; vertical-align: top">
				     				<input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
									<input type="hidden" name="is_digital" value="<?php echo (int) $product['is_digital']; ?>" />
									<?php
									if ($stock_arr)
									{
										foreach ($stock_arr as $attr)
										{
											list($attr_id, $attr_parent_id, $stock_id, $qty) = explode("_", $attr);
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
									<input type="hidden" name="stock_id" value="<?php echo $stock_id; ?>" class="btn btn-primary scSelectorButton"/>
									<button class="btn btn-default scButton scButtonDark scSelectorButton pjScBtnPrimary" type="submit"><?php __('front_buy_now', false, true); ?></button>
				        			<a href="<?php echo $href; ?>" data-id="<?php echo $product['id']; ?>" data-slug="<?php echo pjSanitize::html($slug); ?>"  class="btn btn-default scSelectorButton scSelectorProduct pjScBtnSecondary" type="button"><?php __('front_view_details'); ?></a>
				     			</form>
				     			<?php
				     		}
				     		?>
				      	</div>
			     		<?php	
			     	}else{
		     			if (!pjUtil::isOptionEnumYes($tpl['option_arr'], 'o_disable_orders'))
		     			{
		     				?>
		     				<button class="btn btn-primary pjScBtnPrimary" disabled="disabled" type="button"><?php __('front_out_of_stock'); ?></button>
							<?php		     				
		     			}
		     		} 
			     	?>
			      	<br><br>
				</li>
				<?php
			} 
			?>
		</ul>
	</div>
	<?php
	if (isset($tpl['paginator']))
	{
	 	if($tpl['paginator']['count'] > $tpl['paginator']['row_count'])
	 	{
			?>
			<div align="center">
				<nav>
					<ul class="pagination">
						<?php
						if ($tpl['paginator']['pages'] > 1 && $tpl['paginator']['page'] > 1)
						{ 
							$i = $tpl['paginator']['page'] - 1;
							?><li><a href="<?php echo pjUtil::getReferer(); ?>#!/Products/q:<?php echo urlencode($controller->_get->toString('q')); ?>/category:<?php echo $controller->_get->toInt('category_id'); ?>/page:<?php echo $i; ?>" class="scSelectorPage pjScPaginationPrev" data-page="<?php echo $i; ?>" title="<?php __('front_prev', false, true); ?>"><span aria-hidden="true">&laquo;</span><span class="sr-only"><?php __('front_prev');?></span></a></li><?php
						}
						for ($i = 1; $i <= $tpl['paginator']['pages']; $i++)
						{
							?><li><a href="<?php echo pjUtil::getReferer(); ?>#!/Products/q:<?php echo urlencode($controller->_get->toString('q')); ?>/category:<?php echo $controller->_get->toInt('category_id'); ?>/page:<?php echo $i; ?>" class="scSelectorPage<?php echo $tpl['paginator']['page'] != $i ? NULL : ' scPaginatorFocus'; ?>" data-page="<?php echo $i; ?>"><?php echo $i; ?></a></li><?php
						}
						if ($tpl['paginator']['pages'] > $tpl['paginator']['page'])
						{
							$i = $tpl['paginator']['page'] + 1;
							?><li><a href="<?php echo pjUtil::getReferer(); ?>#!/Products/q:<?php echo urlencode($controller->_get->toString('q')); ?>/category:<?php echo $controller->_get->toInt('category_id'); ?>/page:<?php echo $i; ?>" class="scSelectorPage pjScPaginationNext" data-page="<?php echo $i; ?>" title="<?php __('front_next', false, true); ?>"><span aria-hidden="true">&raquo;</span><span class="sr-only"><?php __('front_next');?></span></a></li><?php
						}
						?>
					</ul>
				</nav>
			</div>
			<?php
	 	}
	}
} 
?>
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