<ul class="scAccountMenu">
	<?php
	if (!$controller->isLoged())
	{
		?>
		<li><a href="<?php echo pjUtil::getReferer(); ?>#!/Register" class="scSelectorRegister"><?php __('front_register'); ?></a></li>
		<li><a href="<?php echo pjUtil::getReferer(); ?>#!/Login" class="scSelectorLogin"><?php __('front_login'); ?></a></li>
		<?php
	} else {
		?>
		<li><a href="<?php echo pjUtil::getReferer(); ?>#!/Logout" class="scSelectorLogout"><?php __('front_logout'); ?></a></li>
		<li><a href="<?php echo pjUtil::getReferer(); ?>#!/Profile" class="scSelectorProfile"><?php __('front_profile'); ?></a></li>
		<?php
	}
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
	<li><a href="<?php echo pjUtil::getReferer(); ?>#!/Favs" class="scSelectorViewFavs"><?php __('front_favs'); ?><?php echo !empty($myFavs) ? sprintf(" (%u)", $number_of_favs): NULL; ?></a></li>
</ul>