<div class="container-fluid">
	<nav class="navbar navbar-default pjScHeader" role="navigation">
    	<div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
            	<button type="button" class="navbar-toggle collapsed" data-pj-toggle="collapse" data-pj-target="#bs-example-navbar-collapse-1">
                	<span class="sr-only">Toggle navigation</span>
                	<span class="icon-bar"></span>
                	<span class="icon-bar"></span>
                	<span class="icon-bar"></span>
              </button>
              <a class="scStoreName navbar-brand" href="<?php echo $tpl['option_arr']['o_install_url']; ?>/products"><?php __('lblStoreName')?></a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

              	<ul class="nav navbar-nav navbar-right">
              		<?php
					$myFavs = array();
					$number_of_favs = 0;
					if (isset($_COOKIE[$controller->defaultCookie]) && !empty($_COOKIE[$controller->defaultCookie]))
					{
					    $cookie_value = base64_decode(stripslashes($_COOKIE[$controller->defaultCookie]));
					    $myFavs = unserialize($cookie_value);
						$number_of_favs = count($myFavs);
						foreach ($myFavs as $fav => $whatever)
						{
							$item = unserialize($fav);
							$product = NULL;
							if(in_array($item['product_id'], $tpl['hidden_ids_arr']))
							{
								$number_of_favs--;
							}
						}
					}
              		?>
                	<li<?php echo $controller->_get->toString('action') == 'pjActionFavs' ? ' class="active"' : null;?>><a href="<?php echo $tpl['option_arr']['o_install_url']; ?>/<?php echo (!empty($tpl['option_arr']['o_page_prefix'])) ? $tpl['option_arr']['o_page_prefix'] . '-' : ''; ?>favorites" class="pjSelectorFavorites" ><?php __('front_favs'); ?><span class="badge"><?php echo !empty($myFavs) ? sprintf(" (%u)", $number_of_favs): NULL; ?></span></a></li>
                	<?php
                	if (!$controller->isLoged())
                	{ 
	                	?>
	                	<li<?php echo $controller->_get->toString('action') == 'pjActionLogin' ? ' class="active"' : null;?>><a href="<?php echo $tpl['option_arr']['o_install_url']; ?>/<?php echo (!empty($tpl['option_arr']['o_page_prefix'])) ? $tpl['option_arr']['o_page_prefix'] . '-' : ''; ?>register" class="scSelectorLogin"><?php __('front_login'); ?></a></li>
	                	<li<?php echo $controller->_get->toString('action') == 'pjActionRegister' ? ' class="active"' : null;?>><a href="<?php echo $tpl['option_arr']['o_install_url']; ?>/<?php echo (!empty($tpl['option_arr']['o_page_prefix'])) ? $tpl['option_arr']['o_page_prefix'] . '-' : ''; ?>login" class="scSelectorRegister"><?php __('front_register'); ?></a></li>
	                	<?php
                	}else{
                		?>
	                	<li><a href="<?php echo $tpl['option_arr']['o_install_url']; ?>/<?php echo (!empty($tpl['option_arr']['o_page_prefix'])) ? $tpl['option_arr']['o_page_prefix'] . '-' : ''; ?>logout" class="scSelectorLogout"><?php __('front_logout'); ?></a></li>
	                	<li<?php echo $controller->_get->toString('action') == 'pjActionProfile' ? ' class="active"' : null;?>><a href="<?php echo $tpl['option_arr']['o_install_url']; ?>/<?php echo (!empty($tpl['option_arr']['o_page_prefix'])) ? $tpl['option_arr']['o_page_prefix'] . '-' : ''; ?>profile" class="scSelectorProfile"><?php __('front_profile'); ?></a></li>
	                	<li<?php echo in_array($controller->_get->toString('action'), array('pjActionOrdersHistory', 'pjActionOrderDetails'), true) ? ' class="active"' : null;?>><a href="<?php echo $tpl['option_arr']['o_install_url']; ?>/<?php echo (!empty($tpl['option_arr']['o_page_prefix'])) ? $tpl['option_arr']['o_page_prefix'] . '-' : ''; ?>orders" class="scSelectorOrdersHistory"><?php echo pjSanitize::html(__('front_orders_history', true) ?: 'My orders'); ?></a></li>
	                	<?php
                	}                	
                	if(isset($_SESSION[$controller->defaultLangMenu]) && $_SESSION[$controller->defaultLangMenu] == 'show')
                	{ 
                		if (isset($tpl['locale_arr']) && is_array($tpl['locale_arr']) && !empty($tpl['locale_arr']) && count($tpl['locale_arr']) > 1)
                		{
                			$locale_id = $controller->pjActionGetLocale();
                			$selected_title = null;
							$selected_src = NULL;
                			foreach ($tpl['locale_arr'] as $locale)
                			{
                				if($locale_id == $locale['id'])
                				{
                					$selected_title = $locale['language_iso'];
									$lang_iso = explode("-", $selected_title);
									if(isset($lang_iso[1]))
									{
										$selected_title = $lang_iso[1];
									}
									if (!empty($locale['flag']) && is_file(PJ_INSTALL_PATH . $locale['flag']))
									{
										$selected_src = PJ_INSTALL_URL . $locale['flag'];
									} elseif (!empty($locale['file']) && is_file(PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $locale['file'])) {
										$selected_src = PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $locale['file'];
									}
									break;
                				}
                			}
                			?>
		                	<li class="dropdown pjScLocale">
		                  		<a href="#" class="dropdown-toggle" data-pj-toggle="dropdown" role="button" aria-expanded="false"><img src="<?php echo $selected_src; ?>" alt=""><span class="title"><?php echo $selected_title;?></span> <span class="caret"></span></a>
			                  	<ul class="dropdown-menu" role="menu">
			                  		<?php
			                  		foreach ($tpl['locale_arr'] as $locale)
			                  		{
			                  			$selected_src = NULL;
			                  			if (!empty($locale['flag']) && is_file(PJ_INSTALL_PATH . $locale['flag']))
			                  			{
			                  				$selected_src = PJ_INSTALL_URL . $locale['flag'];
			                  			} elseif (!empty($locale['file']) && is_file(PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $locale['file'])) {
			                  				$selected_src = PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $locale['file'];
			                  			}
			                  			?>
			                  				<li>
			                  					<a href="#" class="scSelectorLocale<?php echo $locale_id == $locale['id'] ? ' scLocaleFocus' : NULL; ?>" data-id="<?php echo $locale['id']; ?>">
			                  						<img src="<?php echo $selected_src; ?>" alt="">
													<?php echo pjSanitize::html($locale['name']); ?>
			                  					</a>
			                  				</li>
			                  			<?php
			                  		}
			                  		?>
			                  	</ul>
		                	</li>
		                	<?php
                		}
                	} 
	                ?>
              	</ul>
            </div><!-- /.navbar-collapse -->
		</div><!-- /.container-fluid -->
	</nav>
	<div class="row pjScBar">
		<div class="col-sm-6">
            <div class="pjScSearchFormContainer">
            	<form action="" method="get" class="scSearchForm scSelectorSearchForm">
                	<div class="input-group">
                    	<input type="text" name="q" value="<?php echo pjSanitize::html(urldecode($controller->_get->toString('q'))); ?>" class="form-control" placeholder="<?php echo pjSanitize::html(__('front_search', true)); ?>">
                    	<span class="input-group-btn">
                      		<button class="btn btn-default hidden-xs" type="submit"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
                      		<button class="btn btn-default visible-xs pjScToggleSearch" type="button"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
                    	</span>
                  	</div>
                </form>
            </div>
            <br>
		</div>
		<div class="col-sm-6">
			<div class="pjScCategoriesContainer" style="display: none;">
				<div class="pjScCloseToggleWrap"><a href="javascript:void(0);" class="pjScToggleCategory"><span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span></a></div>
				<ul class="pjScNavCategories">
        			<?php
        			if (isset($tpl['category_arr']) && !empty($tpl['category_arr']))
        			{
        				if ($controller->_get->check('category_id') && $controller->_get->toInt('category_id') > 0)
        				{
        					$ancestor = pjUtil::getAncestor($tpl['category_arr'], $controller->_get->toInt('category_id'));
        				}
        				
        				foreach ($tpl['category_arr'] as $category)
        				{
        					if ($category['deep'] == 0)
        					{
        						?>
        						<li class="<?php echo $category['children'] > 0 ? 'sub-menu' : '';?>">
        							<a href="<?php echo $tpl['option_arr']['o_install_url']; ?>/<?php echo (!empty($tpl['option_arr']['o_page_prefix'])) ? $tpl['option_arr']['o_page_prefix'] . '-' : ''; ?>products/q:/category:<?php echo $category['data']['id']; ?>/page:1" data-href="/<?php echo (!empty($tpl['option_arr']['o_page_prefix'])) ? $tpl['option_arr']['o_page_prefix'] . '-' : ''; ?>products/q:/category:<?php echo $category['data']['id']; ?>/page:1" class="scVerticalDropDownMenu">
        				             	<?php echo pjSanitize::html($category['data']['name']); ?>
        				            </a>
        				            <?php pjUtil::verticalTreeMenuLayout3($tpl['category_arr'], $category, $tpl['option_arr']['o_page_prefix']); ?>
        						</li>
        						<?php
        					}
        				}
        			}
        			?>
        		</ul>
			</div>
            <div class="clearfix">
            	<div class="pull-left pjScCategoriesSearch visible-xs">
            		<a href="javascript:void(0);" title="<?php __('front_label_search');?>" class="pjScToggleCategory"><i class="glyphicon glyphicon-th-list"></i></a>
            		<a href="javascript:void(0);" title="<?php __('front_label_select_category');?>" class="pjScToggleSearch"><i class="glyphicon glyphicon-search"></i></a>
            	</div>
            	<?php
        		$isCheckoutReady = isset($tpl['price_arr']) && $tpl['price_arr']['status'] == 'OK';
        		if ((int) $tpl['option_arr']['o_disable_orders'] === 0)
        		{
        			$qty = 0;
        			foreach($tpl['cart_arr'] as $v)
        			{
        				$qty += $v['qty'];
        			}
        			?>
        	      	<div class="btn-group pull-right" role="group" aria-label="...">
                    	<a href="<?php echo $tpl['option_arr']['o_install_url']; ?>/bookings/<?php echo (!empty($tpl['option_arr']['o_page_prefix'])) ? $tpl['option_arr']['o_page_prefix'] . '-' : ''; ?>cart" class="btn btn-default scSelectorViewCart">
                      		<span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span>
                      		<span class="text-warning"><?php echo $qty; ?></span>
                      		<span class="text-uppercase"><?php $qty !== 1 ? __('front_items') : __('front_item'); ?></span>
                    	</a>
                    	<a href="<?php echo $tpl['option_arr']['o_install_url']; ?>/bookings/<?php echo (!empty($tpl['option_arr']['o_page_prefix'])) ? $tpl['option_arr']['o_page_prefix'] . '-' : ''; ?>cart" class="btn btn-default scSelectorViewCart">
                      		<span class="text-warning"><?php echo pjCurrency::formatPrice($isCheckoutReady ? $tpl['price_arr']['data']['total'] : '0.00'); ?></span>
                    	</a>
                    	<a href="<?php echo $tpl['option_arr']['o_install_url']; ?>/bookings/<?php echo (!empty($tpl['option_arr']['o_page_prefix'])) ? $tpl['option_arr']['o_page_prefix'] . '-' : ''; ?>cart" class="btn btn-default scSelectorViewCart"><?php __('front_cart'); ?></a>
                  	</div>  
        			<?php
        		} 
        		?>
            </div>
		</div>		
	</div>
	<br class="visible-xs-inline-block">
	<div class="row">
    	<div class="col-sm-8 hidden-xs">
    		<?php
        	if(isset($_SESSION[$controller->defaultCategoryMenu]) && (int) $_SESSION[$controller->defaultCategoryMenu] == 0)
        	{
        		$isProduct = $controller->_get->toString('controller') == 'pjFrontPublic' && $controller->_get->toString('action') == 'pjActionProduct';
        		$isProducts = $controller->_get->toString('controller') == 'pjFrontPublic' && $controller->_get->toString('action') == 'pjActionProducts' && $controller->_get->check('category_id') && $controller->_get->toInt('category_id') > 0;
        		if ($isProduct || $isProducts)
        		{
        			$category_id = NULL;
        			if ($isProduct)
        			{
        				if (!empty($tpl['product_arr']['category_ids']))
        				{
        					$category_id = max($tpl['product_arr']['category_ids']);
        					$_REQUEST['category_id'] = $category_id;
        				}
        			} elseif ($isProducts) {
        				$category_id = $controller->_get->toInt('category_id');
        			}
        		}
        		?>
        		<ul class="nav nav-pills pjScFilterCat">
        			<li class="<?php echo (!$controller->_get->check('category_id') || $controller->_get->toInt('category_id') <= 0) && !in_array($controller->_get->toString('action'),
        				array('pjActionCart', 'pjActionCheckout', 'pjActionPreview', 'pjActionLogin',
        				'pjActionRegister', 'pjActionForgot', 'pjActionFavs', 'pjActionProfile', 'pjActionOrdersHistory', 'pjActionOrderDetails', 'pjActionGetPaymentForm')) ? ' active' : NULL; ?>" role="presentation">
        				<a href="<?php echo $tpl['option_arr']['o_install_url']; ?>/<?php echo (!empty($tpl['option_arr']['o_page_prefix'])) ? $tpl['option_arr']['o_page_prefix'] . '-' : ''; ?>products" data-href="/<?php echo (!empty($tpl['option_arr']['o_page_prefix'])) ? $tpl['option_arr']['o_page_prefix'] . '-' : ''; ?>products" class="scDropDownMenu"><?php __('front_all'); ?></a>
        			</li>
        			
        			<?php
        			if (isset($tpl['category_arr']) && !empty($tpl['category_arr']))
        			{
        				if ($controller->_get->check('category_id') && $controller->_get->toInt('category_id') > 0)
        				{
        					$ancestor = pjUtil::getAncestor($tpl['category_arr'], $controller->_get->toInt('category_id'));
        				}
        				
        				foreach ($tpl['category_arr'] as $category)
        				{
        					if ($category['deep'] == 0)
        					{
        						?>
        						<li class="dropdown<?php echo !isset($ancestor) || $ancestor != $category['data']['id'] ? NULL : ' active'; ?>" role="presentation">
        							<a aria-expanded="false" role="button" href="<?php echo $tpl['option_arr']['o_install_url']; ?>/<?php echo (!empty($tpl['option_arr']['o_page_prefix'])) ? $tpl['option_arr']['o_page_prefix'] . '-' : ''; ?>products/q:/category:<?php echo $category['data']['id']; ?>/page:1" data-href="/<?php echo (!empty($tpl['option_arr']['o_page_prefix'])) ? $tpl['option_arr']['o_page_prefix'] . '-' : ''; ?>products/q:/category:<?php echo $category['data']['id']; ?>/page:1" data-hover="dropdown" class="scDropDownMenu dropdown-toggle">
        				             	<?php echo pjSanitize::html($category['data']['name']); ?> <span class="caret"></span>
        				            </a>
        				            <?php pjUtil::treeMenuLayout3($tpl['category_arr'], $category, $tpl['option_arr']['o_page_prefix']); ?>
        						</li>
        						<?php
        					}
        				}
        			}
        			?>
        		</ul>
        		<br>
        		<?php
        	} 
        	?>
    	</div>
    	<div class="col-sm-4 col-xs-12 text-right">
    		<?php 
    		$sort_arr = array('featured','newest','price_asc','price_desc','name_asc','name_desc');
    		$label_product_sort_by = __('front_product_sort_by', true);
    		$page = $controller->_get->check('page') && $controller->_get->toInt('page') > 0 ? $controller->_get->toInt('page') : 1;
    		$sort_by = $controller->_get->check('sort') && in_array($controller->_get->toString('sort'), $sort_arr) ? $controller->_get->toString('sort') : 'featured';
    		?>
    		<div class="pjScSort">
    			<div class="dropdown">
                    <button class="btn btn-default pjScBtnPrimary dropdown-toggle" type="button" data-pj-toggle="dropdown"><?php __('front_order_by');?>
                    <span class="caret"></span></button>
                    <ul class="dropdown-menu">
                    	<?php foreach ($sort_arr as $k) { ?>
                      		<li class="<?php echo $sort_by == $k ? 'active' : '';?>"><a href="<?php echo $tpl['option_arr']['o_install_url']; ?>/<?php echo (!empty($tpl['option_arr']['o_page_prefix'])) ? $tpl['option_arr']['o_page_prefix'] . '-' : ''; ?>products/q:<?php echo urlencode($controller->_get->toString('q')); ?>/category:<?php echo $controller->_get->toInt('category_id'); ?>/page:<?php echo $page; ?>/sort:<?php echo $k;?>" class="scSelectorSort" data-sort="<?php echo $k;?>"><?php echo @$label_product_sort_by[$k];?></a></li>
                      	<?php } ?>
                    </ul>
                  </div>
       		</div>
    	</div>    	
    </div>
    <br class="visible-xs-inline-block">
</div>