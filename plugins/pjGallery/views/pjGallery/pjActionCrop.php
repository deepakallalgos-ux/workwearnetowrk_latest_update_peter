<?php
if ($controller->_get->check('query_string'))
{
    parse_str($controller->_get->toString('query_string'), $output);
}
$size = 'large';
if($controller->_get->check('size') && in_array($controller->_get->toString('size'), array('large', 'medium', 'small')))
{
    $size = $controller->_get->toString('size');
}

$rec_width = $tpl['arr']['source_width'];
$rec_height = $tpl['arr']['source_height'];
switch ($size) {
	case 'large':
		$title = __('plugin_gallery_resize_large_title', true);
		$body = __('plugin_gallery_resize_large_body', true);
		break;
	case 'small':
		if (isset($tpl['gallery_set_arr']))
		{
			$rec_width = $tpl['gallery_set_arr']['small_width'];
			$rec_height = $tpl['gallery_set_arr']['small_height'];
		}
		$title = __('plugin_gallery_resize_small_title', true);
		$body = __('plugin_gallery_resize_small_body', true);
		
		if ($controller->_get->check('origin'))
		{
			$title = __('plugin_gallery_resize_thumb_title', true);
			$body = __('plugin_gallery_resize_thumb_body', true);
		}
		
		$title = str_replace("{SIZE}", $rec_width . ' x ' . $rec_height, $title);
		$body = str_replace("{STAG}", '<a href="'.$_SERVER['PHP_SELF'].'?controller=pjGallery&action=pjActionCrop&id='.$tpl['arr']['id'].'&size=small&origin=1">', $body);
		$body = str_replace("{ETAG}", '</a>', $body);
		break;

	case 'medium':
		if (isset($tpl['gallery_set_arr']))
		{
			$rec_width = $tpl['gallery_set_arr']['medium_width'];
			$rec_height = $tpl['gallery_set_arr']['medium_height'];
		}
		
		$title = __('plugin_gallery_resize_medium_title', true);
		$body = __('plugin_gallery_resize_medium_body', true);
		
		if ($controller->_get->check('origin'))
		{
			$title = __('plugin_gallery_resize_preview_title', true);
			$body = __('plugin_gallery_resize_preview_body', true);
		}
		
		$title = str_replace("{SIZE}", $rec_width . ' x ' . $rec_height, $title);
		$body = str_replace("{STAG}", '<a href="'.$_SERVER['PHP_SELF'].'?controller=pjGallery&action=pjActionCrop&id='.$tpl['arr']['id'].'&size=medium&origin=1">', $body);
		$body = str_replace("{ETAG}", '</a>', $body);
		break;
}
?>
<div class="row wrapper border-bottom white-bg page-heading">
	<div class="col-sm-12">
		<div class="row">
			<div class="col-lg-9 col-md-8 col-sm-6">
				<h2><?php echo $title;?></h2>
			</div>
		</div><!-- /.row -->

		<p class="m-b-none"><i class="fa fa-info-circle"></i> <?php echo $body;?></p>
	</div><!-- /.col-md-12 -->
</div>

<div class="row wrapper wrapper-content animated fadeInRight">
	<div class="col-lg-12">
		<div class="ibox float-e-margins">
			<div class="ibox-content">
				<label><?php __('plugin_gallery_image_version');?>:</label>
				<div class="btn-group" role="group" aria-label="...">
					<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjGallery&amp;action=pjActionCrop&amp;id=<?php echo $tpl['arr']['id'];?>&amp;size=large<?php echo isset($output) && !empty($output) ? '&amp;query_string=' . urlencode($controller->_get->toString('query_string')) : NULL;?>" class="btn<?php echo $size == 'large' ? ' btn-primary active' : ' btn-default';?>"><?php __('plugin_gallery_original'); ?></a>
        			<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjGallery&amp;action=pjActionCrop&amp;id=<?php echo $tpl['arr']['id'];?>&amp;size=small<?php echo isset($output) && !empty($output) ? '&amp;query_string=' . urlencode($controller->_get->toString('query_string')) : NULL;?>" class="btn<?php echo $size == 'small' ? ' btn-primary active' : ' btn-default';?>"><?php __('plugin_gallery_thumb'); ?></a>
        			<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjGallery&amp;action=pjActionCrop&amp;id=<?php echo $tpl['arr']['id'];?>&amp;size=medium<?php echo isset($output) && !empty($output) ? '&amp;query_string=' . urlencode($controller->_get->toString('query_string')) : NULL;?>" class="btn<?php echo $size == 'medium' ? ' btn-primary active' : ' btn-default';?>"><?php __('plugin_gallery_preview'); ?></a>
                </div>
                
                <br/>
                <div class="hr-line-dashed"></div>
                <?php
                if (isset($tpl['arr'], $tpl['arr'][$size . '_path']) && !empty($tpl['arr'][$size . '_path']) && is_file(PJ_INSTALL_PATH . $tpl['arr'][$size . '_path']))
                {
                	$back_url = sprintf('%s?controller=pjGallerySet&amp;action=pjActionUpdate&amp;id=%u', $_SERVER['PHP_SELF'], $tpl['arr']['foreign_id']);
                	if (isset($output) && !empty($output))
                	{
                		$back_url = sprintf('%s?%s', $_SERVER['PHP_SELF'], http_build_query($output));
                	}	
                	?>
                	<div class="plugin_gallery_crop_nav">
                		<div class="row">
                			<div class="col-lg-3 col-md-3 col-sm-3">
                				<a href="<?php echo $back_url; ?>" class="btn btn-primary btn-md btn-outline"> <?php __('plugin_gallery_crop_btn_back'); ?></a>
                			</div>
                			<div class="col-lg-6 col-md-6 col-sm-6 text-center">
                				<?php
                        		if($size == 'medium' || $size == 'small')
                        		{ 
                        			?>
                        			<div class="plugin_gallery_crop_actions">
                        				<button type="button" class="btn btn-primary btn-md btn-outline plugin_gallery_btn plugin_gallery_btn_icon btn-cropper-rotate-left" title="<?php __('plugin_gallery_crop_rotate_left', false, true); ?>"><i class="fa fa-rotate-left"></i></button>
                        				<button type="button" class="btn btn-primary btn-md btn-outline plugin_gallery_btn plugin_gallery_btn_icon btn-cropper-zoom-out" title="<?php __('plugin_gallery_crop_zoom_out', false, true); ?>"><i class="fa fa-search-minus" aria-hidden="true"></i></button>
                        				<button type="button" class="btn btn-primary btn-md btn-outline plugin_gallery_btn plugin_gallery_btn_icon btn-cropper-fit" title="<?php __('plugin_gallery_crop_fit_image', false, true); ?>"><i class="fa fa-arrows-alt" aria-hidden="true"></i></button>
                        				<button type="button" class="btn btn-primary btn-md btn-outline plugin_gallery_btn plugin_gallery_btn_icon btn-cropper-zoom-in" title="<?php __('plugin_gallery_crop_zoom_in', false, true); ?>"><i class="fa fa-search-plus" aria-hidden="true"></i></button>
                        				<button type="button" class="btn btn-primary btn-md btn-outline plugin_gallery_btn plugin_gallery_btn_icon btn-cropper-rotate-right" title="<?php __('plugin_gallery_crop_rotate_right', false, true); ?>"><i class="fa fa-rotate-right"></i></button>
                        			</div>
                        			<?php
                        		}else{
                        			?>
                        			<div class="plugin_gallery_rotate_actions">
                        				<button type="button" class="btn btn-primary btn-md btn-outline plugin_gallery_btn plugin_gallery_btn_icon btn-cropper-rotate-left" title="<?php __('plugin_gallery_crop_rotate_left', false, true); ?>"><i class="fa fa-rotate-left"></i></button>
                        				<button type="button" class="btn btn-primary btn-md btn-outline plugin_gallery_btn plugin_gallery_btn_icon btn-cropper-rotate-right" title="<?php __('plugin_gallery_crop_rotate_right', false, true); ?>"><i class="fa fa-rotate-right"></i></button>
                        			</div>
                        			<?php
                        		} 
                        		?>
                			</div>
                			<div class="col-lg-3 col-md-3 col-sm-3">
                				<form action="" method="post" class="plugin_gallery_crop_form" id="frmGalleryCrop" data-size="<?php echo $size;?>" data-rw="<?php echo $rec_width; ?>" data-rh="<?php echo $rec_height; ?>">
                        			<input type="hidden" name="do_crop" value="1" />
                        			<?php 
                        			if($controller->_get->check('origin'))
                        			{
                        				?><input type="hidden" name="<?php echo $size == 'small' ? 'create_thumb': 'create_preview';?>" value="1" /><?php
                        			}
                        			if ($controller->_get->check('query_string'))
                        			{
                        			    ?><input type="hidden" name="query_string" value="<?php echo pjSanitize::html($controller->_get->toString('query_string')); ?>" /><?php
                        			}
                        			?>
                        			<input type="hidden" name="size" value="<?php echo $size;?>" />
                        			<input type="hidden" name="id" value="<?php echo pjSanitize::html($tpl['arr']['id']); ?>" />
                        			<input type="hidden" name="foreign_id" value="<?php echo pjSanitize::html($tpl['arr']['foreign_id']); ?>" />
                        			<input type="hidden" name="x" value="" />
                        			<input type="hidden" name="y" value="" />
                        			<input type="hidden" name="width" value="" />
                        			<input type="hidden" name="height" value="" />
                        			<input type="hidden" name="rotate" value="" />
                        			<button type="submit" class="ladda-button btn btn-primary btn-md btn-phpjabbers-loader pull-left" data-style="zoom-in">
    									<span class="ladda-label"><?php $size == 'medium' || $size == 'small' ? __('plugin_gallery_crop_btn_crop') : __('plugin_gallery_crop_btn_save'); ?></span>
    									<?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
    								</button>
                        		</form>
                			</div>
                		</div>
                		
                		
                		
                	</div>
                	<?php
                	$source_path = PJ_INSTALL_FOLDER . $tpl['arr'][$size . '_path'];
                	if($controller->_get->check('origin'))
                	{
                		$source_path = PJ_INSTALL_FOLDER . $tpl['arr']['source_path'];
                	} 
                	?>
                	<div style="height: 500px" data-src="<?php echo pjSanitize::html($source_path); ?>?r=<?php echo rand(1,9999); ?>" id="plugin_gallery_img_wrap"></div>
                	<?php
                }
                ?>
			</div><!-- /.ibox-content -->
		</div><!-- /.ibox float-e-margins -->
	</div>
</div><!-- /.row wrapper wrapper-content animated fadeInRight -->
