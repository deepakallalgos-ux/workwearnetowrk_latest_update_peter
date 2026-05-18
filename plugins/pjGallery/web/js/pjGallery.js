var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		var cropper = ($.fn.cropper !== undefined),
			$frmGalleryCrop = $("#frmGalleryCrop");
		
		if (frmGalleryCrop.length && cropper) {
			var $plugin_gallery_img,
				$plugin_gallery_img_wrap = $('#plugin_gallery_img_wrap'),
				ratio = $frmGalleryCrop.data('rw') / $frmGalleryCrop.data('rh'),
				size = $frmGalleryCrop.data('size');
			
			if ($plugin_gallery_img_wrap.length) {
				var image = new Image();
				
				image.src = $plugin_gallery_img_wrap.data('src');
				image.id = 'plugin_gallery_img';
				image.style.maxWidth = '100%';
				image.onload = function () {
					$plugin_gallery_img_wrap.append(image);
					$plugin_gallery_img = $plugin_gallery_img_wrap.find('#plugin_gallery_img');
					
					var w = this.width, 
						h = this.height;
					
					if(size == 'large')
					{
						$plugin_gallery_img.cropper({
							aspectRatio: ratio,
							autoCropArea: 1,
							zoomable: false,
							dragCrop: false,
						    cropBoxMovable: false,
						    cropBoxResizable: false,
							crop: function(e) {
								var data = $plugin_gallery_img.cropper('getData', true);
								$frmGalleryCrop.find('input[name="x"]').val(data.x);
								$frmGalleryCrop.find('input[name="y"]').val(data.y);
								$frmGalleryCrop.find('input[name="width"]').val(data.width);
								$frmGalleryCrop.find('input[name="height"]').val(data.height);
								$frmGalleryCrop.find('input[name="rotate"]').val(data.rotate);
							},
							built: function (e) {
								var data = $plugin_gallery_img.cropper('getContainerData');
								if (w < data.width && h < data.height) {
									$plugin_gallery_img.cropper('zoomTo', 1);
								}
							}
						});
					}else{
						$plugin_gallery_img.cropper({
							aspectRatio: ratio,
							autoCropArea: 1,
							strict: false,
							crop: function(e) {
								var data = $plugin_gallery_img.cropper('getData', true);
								$frmGalleryCrop.find('input[name="x"]').val(data.x);
								$frmGalleryCrop.find('input[name="y"]').val(data.y);
								$frmGalleryCrop.find('input[name="width"]').val(data.width);
								$frmGalleryCrop.find('input[name="height"]').val(data.height);
								$frmGalleryCrop.find('input[name="rotate"]').val(data.rotate);
							},
							built: function (e) {
								var data = $plugin_gallery_img.cropper('getContainerData');
								if (w < data.width && h < data.height) {
									$plugin_gallery_img.cropper('zoomTo', 1);
								}
							}
						});
					}
				};
			}
			
			$(document).on('click', '.btn-cropper-zoom-in', function () {
				$plugin_gallery_img.cropper('zoom', 0.1);
			}).on('click', '.btn-cropper-zoom-out', function () {
				$plugin_gallery_img.cropper('zoom', -0.1);
			}).on('click', '.btn-cropper-rotate-left', function () {
				$plugin_gallery_img.cropper('rotate', -45);
			}).on('click', '.btn-cropper-rotate-right', function () {
				$plugin_gallery_img.cropper('rotate', 45);
			}).on('click', '.btn-cropper-fit', function () {
				$plugin_gallery_img.cropper('zoomTo', 1);
			}).on('mouseenter', '.plugin_gallery_btn_primary', function () {
				$(this).addClass('plugin_gallery_btn_primary_hover');
			}).on('mouseleave', '.plugin_gallery_btn_primary', function () {
				$(this).removeClass('plugin_gallery_btn_primary_hover');
			});
		}
	});
})(jQuery_1_8_2);