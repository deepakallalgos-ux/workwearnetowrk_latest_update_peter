<?php
if(isset($_SESSION[$controller->defaultLangMenu]) && $_SESSION[$controller->defaultLangMenu] == 'show')
{
	if (isset($tpl['locale_arr']) && is_array($tpl['locale_arr']) && !empty($tpl['locale_arr']))
	{
		?>
			<ul class="scLocaleMenu"><?php
			$locale_id = $controller->pjActionGetLocale();
			foreach ($tpl['locale_arr'] as $locale)
			{
				?><li><a href="#" class="scSelectorLocale<?php echo $locale_id == $locale['id'] ? ' scLocaleFocus' : NULL; ?>" data-id="<?php echo $locale['id']; ?>" title="<?php echo pjSanitize::html($locale['title']); ?>"><img src="<?php echo PJ_INSTALL_URL . 'core/framework/libs/pj/img/flags/' . $locale['file'] ?>" alt="<?php echo pjSanitize::html($locale['title']); ?>" /><?php echo pjMultibyte::substr($locale['title'], 0, 2); ?></a></li><?php
			}
			?>
			</ul>
		<?php
	}
}
?>