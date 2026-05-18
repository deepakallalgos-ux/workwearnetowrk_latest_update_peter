<?php
if(isset($_SESSION[$controller->defaultCategoryMenu]) && $_SESSION[$controller->defaultCategoryMenu] == 'show')
{ 
	?>
	<ul role="menubar" class="scMenuBar">
		<li role="menuitem" class="scMenuBarItem<?php echo (!$controller->_get->check('category_id') || $controller->_get->toInt('category_id') <= 0) && !in_array($controller->_get->toString('action'),
			array('pjActionCart', 'pjActionCheckout', 'pjActionPreview', 'pjActionLogin',
			'pjActionRegister', 'pjActionForgot', 'pjActionFavs', 'pjActionProfile')) ? ' scMenuBarItemActive' : NULL; ?>">
			<a href="<?php echo pjUtil::getReferer(); ?>#!/Products"><?php __('front_all'); ?></a>
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
					<li role="menuitem" class="scMenuBarItem<?php echo $category['children'] > 0 ? ' scMenuBarItemHub' : NULL; ?><?php echo !isset($ancestor) || $ancestor != $category['data']['id'] ? NULL : ' scMenuBarItemActive'; ?>">
						<a class="scDropDownMenu" href="javascript:void(0);" data-href="<?php echo pjUtil::getReferer(); ?>#!/Products/q:/category:<?php echo $category['data']['id']; ?>/page:1"><?php echo pjSanitize::html($category['data']['name']); ?></a>
						<?php pjUtil::treeMenu($tpl['category_arr'], $category); ?>
					</li>
					<?php
				}
			}
		}
		?>
	</ul>
	<?php
}else{
	?><br /><?php
} 
?>