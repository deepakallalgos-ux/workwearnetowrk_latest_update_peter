<!doctype html>
<?php
if($tpl['detail_page'] == false)
{ 
	?>
	<html>
		<head>
			<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		</head>
		<body>
			<?php
			require $content_tpl; 
			?>
		</body>
	</html>
	<?php
}else{
	if (isset($tpl['product_arr']) && !empty($tpl['product_arr']))
	{
		$slug = NULL;
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
		} else {
			# Non-Seo friendly URLs ---------------------
			$href = pjUtil::getReferer() . '#!/Product/' . $tpl['product_arr']['id'];
		}
		
		$medium_pic = $large_pic = $meta_image = NULL;
		if (!empty($tpl['product_arr']['pic']))
		{
			list($medium_pic, $large_pic) = explode("~:~", $tpl['product_arr']['pic']);
		}
		if (!empty($medium_pic) && is_file($medium_pic))
		{
			$meta_image = PJ_INSTALL_URL . $medium_pic;
		} else {
			$meta_image =  PJ_INSTALL_URL . PJ_IMG_PATH . 'frontend/noimg.png';
		}
		
		$short_desc = htmlentities(pjUtil::truncateDescription(pjUtil::html2txt($tpl['product_arr']['short_desc']), 160, ' '));
		?>
		<html itemscope itemtype="http://schema.org/Product"> 
			<head>
				<title><?php echo pjSanitize::html($tpl['product_arr']['name']);?>test</title>
				<meta name="description" content="<?php echo $short_desc;?>"/>
				<!-- Schema.org markup for Google+ -->
				<meta itemprop="name" content="<?php echo pjSanitize::html($tpl['product_arr']['name']);?>">
				<meta itemprop="description" content="<?php echo $short_desc;?>">
				<meta itemprop="image" content="<?php echo $meta_image;?>">
				<!-- Twitter Card data --> 
				<meta name="twitter:card" content="product">
				<meta name="twitter:site" content="@publisher_handle">
				<meta name="twitter:title" content="<?php echo pjSanitize::html($tpl['product_arr']['name']);?>">
				<meta name="twitter:description" content="<?php echo $short_desc;?>">
				<meta name="twitter:creator" content="@author_handle">
				<meta name="twitter:image" content="<?php echo $meta_image;?>" />
				<!-- Open Graph data -->
				<meta property="og:type" content="article" />
				<meta property="og:title" content="<?php echo pjSanitize::html($tpl['product_arr']['name']);?>" />
				<meta property="og:description" content="<?php echo $short_desc;?>" />
				<meta property="og:image" content="<?php echo $meta_image;?>" />
				<meta property="og:url" content="<?php echo pjUtil::getPageURL();?>" />
			</head>
			<body>
				<?php
				require $content_tpl; 
				?>
			</body>
		</html>
		<?php
	}
} 
?>