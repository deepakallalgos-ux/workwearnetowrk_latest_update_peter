
<?php
if($controller->_get->toInt('layout') != 3)
{
	switch ((int) $controller->_get->toInt('layout'))
	{
		case 2:
			?><div class="scHeader"><?php
				?><div class="scHeaderTop"><?php
					include dirname(__FILE__) . '/menu_search.php';
					include dirname(__FILE__) . '/menu_cart.php';
					include dirname(__FILE__) . '/menu_account.php';
				?></div><?php
				
				?><div class="scHeaderMid"><?php
					include dirname(__FILE__) . '/menu_locale.php';
					include dirname(__FILE__) . '/menu_cart.php';
				?></div><?php
				
				?><div class="scHeaderLimiter scHeaderLimiter4"></div><?php
			?></div><?php
			?><div class="scHeaderClear"></div><?php
			
			include dirname(__FILE__) . '/menu_dropdown.php';
			?><div class="scHeaderCategory"><?php
			include dirname(__FILE__) . '/menu_categories.php';
			include dirname(__FILE__) . '/menu_locale.php';
			?></div><?php
			?><div class="scHeaderCategoryClear"></div><?php
			break;
		case 1:
		default:
			?><div class="scHeader"><?php
				include dirname(__FILE__) . '/menu_locale.php';
				?><div class="scHeaderLimiter scHeaderLimiter1"></div><?php
				include dirname(__FILE__) . '/menu_account.php';
				?><div class="scHeaderLimiter scHeaderLimiter2"></div><?php
				include dirname(__FILE__) . '/menu_cart.php';
				?><div class="scHeaderLimiter scHeaderLimiter3"></div><?php
				include dirname(__FILE__) . '/menu_search.php';
			?></div><?php
			include dirname(__FILE__) . '/menu_dropdown.php';
			include dirname(__FILE__) . '/menu_categories.php';
			?><div class="scHeaderClear"></div><?php
			break;
	}

}else{
	include PJ_VIEWS_PATH . 'pjFront/elements/layout_3/header.php';
}

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
		}
	} elseif ($isProducts) {
		$category_id = $controller->_get->toInt('category_id');
	}

	if (!is_null($category_id))
	{
		$arr = array();
		pjUtil::getBreadcrumbTree($arr, $tpl['category_arr'], $category_id);
		krsort($arr);
		$arr = array_values($arr);
		
		if($controller->_get->toInt('layout') != 3)
		{
			?>
			<ul class="scBreadcrumb">
				<?php
				switch ($controller->_get->toString('action'))
				{
					case 'pjActionProduct':
						$i = 0;
						foreach ($arr as $k => $category)
						{
							$bcClass = ($i > 0) ? 'scBreadcrumbLeft' : 'scBreadcrumbFirst';
							?><li><span class="<?php echo $bcClass; ?>"></span><span class="scBreadcrumbRight"></span><a href="<?php echo pjUtil::getReferer(); ?>#!/Products/q:/category:<?php echo $category['data']['id']; ?>/page:1"><?php echo pjSanitize::html($category['data']['name']); ?></a></li><?php
							$i += 1;
						}
						?><li class="scBreadcrumbActive"><?php echo pjSanitize::html($tpl['product_arr']['name']); ?></li><?php
						break;
					case 'pjActionProducts':
						$i = 0;
						foreach ($arr as $k => $category)
						{
							$bcClass = ($i > 0) ? 'scBreadcrumbLeft' : 'scBreadcrumbFirst';
							?><li><span class="<?php echo $bcClass; ?>"></span><span class="scBreadcrumbRight"></span><a href="<?php echo pjUtil::getReferer(); ?>#!/Products/q:/category:<?php echo $category['data']['id']; ?>/page:1"><?php echo pjSanitize::html($category['data']['name']); ?></a></li><?php
							$i += 1;
						}
						break;
				}
				?>
			</ul>
			<?php
		}else{
			include PJ_VIEWS_PATH . 'pjFront/elements/layout_3/breadcrumb.php';
		}
	}
}

?>