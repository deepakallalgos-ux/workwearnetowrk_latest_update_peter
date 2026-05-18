<?php
if(count($arr) > 1)
{
	?>
	<div class="container-fluid">
		<ol class="breadcrumb">
			<?php
			switch ($controller->_get->toString('action'))
			{
				case 'pjActionProduct':
					$i = 0;
					foreach ($arr as $k => $category)
					{
						?><li><a href="<?php echo $tpl['option_arr']['o_install_url']; ?>/<?php echo (!empty($tpl['option_arr']['o_page_prefix'])) ? $tpl['option_arr']['o_page_prefix'] . '-' : ''; ?>products/q:/category:<?php echo $category['data']['id']; ?>/page:1"><?php echo pjSanitize::html($category['data']['name']); ?></a></li><?php
						$i += 1;
					}
					?><li class="active"><?php echo pjSanitize::html($tpl['product_arr']['name']); ?></li><?php
					break;
				case 'pjActionProducts':
					$i = 0;
					foreach ($arr as $k => $category)
					{
						?><li><a href="<?php echo $tpl['option_arr']['o_install_url']; ?>/<?php echo (!empty($tpl['option_arr']['o_page_prefix'])) ? $tpl['option_arr']['o_page_prefix'] . '-' : ''; ?>products/q:/category:<?php echo $category['data']['id']; ?>/page:1"><?php echo pjSanitize::html($category['data']['name']); ?></a></li><?php
						$i += 1;
					}
					break;
			} 
			?>
		</ol>
	</div>
	<?php
}else{
	?><br/><?php
} 
?>