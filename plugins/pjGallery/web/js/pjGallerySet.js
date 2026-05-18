var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var $frmCreateGallery = $("#frmCreateGallery"),
			$frmUpdateGallery = $("#frmUpdateGallery"),
			validate = ($.fn.validate !== undefined),
			datagrid = ($.fn.datagrid !== undefined),
			$gallery = $("#gallery"),
			$tabs = $("#tabs"),
			tOpt = {
				activate: function (event, ui) {
					$(":input[name='tab_id']").val(ui.newPanel.attr('id'));
				}
			};

		if ($tabs.length > 0 && tabs) {
			$tabs.tabs(tOpt);
		}
		
		if ($gallery.length > 0 && gallery) {
			$gallery.gallery({
				compressUrl: ["index.php?controller=pjGallery&action=pjActionCompressGallery&foreign_id=", myGallery.foreign_id, '&model=', myGallery.model].join(''),
				getUrl: ["index.php?controller=pjGallery&action=pjActionGetGallery&foreign_id=", myGallery.foreign_id, '&model=', myGallery.model].join(''),
				deleteUrl: "index.php?controller=pjGallery&action=pjActionDeleteGallery",
				emptyUrl: ["index.php?controller=pjGallery&action=pjActionEmptyGallery&foreign_id=", myGallery.foreign_id, '&model=', myGallery.model].join(''),
				rebuildUrl: ["index.php?controller=pjGallery&action=pjActionRebuildGallery&foreign_id=", myGallery.foreign_id, '&model=', myGallery.model].join(''),
				resizeUrl: "index.php?controller=pjGallery&action=pjActionCrop&id={:id}" + ($frmUpdateGallery.length > 0 ? "&query_string=" + encodeURIComponent("controller=pjGallerySet&action=pjActionUpdate&id=" + myGallery.foreign_id) : ""),
				sortUrl: "index.php?controller=pjGallery&action=pjActionSortGallery",
				updateUrl: "index.php?controller=pjGallery&action=pjActionUpdateGallery",
				uploadUrl: ["index.php?controller=pjGallery&action=pjActionUploadGallery&foreign_id=", myGallery.foreign_id, '&model=', myGallery.model].join(''),
				watermarkUrl: ["index.php?controller=pjGallery&action=pjActionWatermarkGallery&foreign_id=", myGallery.foreign_id, '&model=', myGallery.model].join('')
			});
		}
		
		if ($frmCreateGallery.length > 0 && validate) {
			$frmCreateGallery.validate({
				rules: {
					medium_width: {
						required: true,
						digits: true
					},
					medium_height: {
						required: true,
						digits: true
					},
					small_width: {
						required: true,
						digits: true
					},
					small_height: {
						required: true,
						digits: true
					}
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: '',
				invalidHandler: function (event, validator) {
				    $(".pj-multilang-wrap").each(function( index ) {
						if($(this).attr('data-index') == pjLocale.localeId)
						{
							$(this).css('display','block');
						}else{
							$(this).css('display','none');
						}
					});
					$(".pj-form-langbar-item").each(function( index ) {
						if($(this).attr('data-index') == pjLocale.localeId)
						{
							$(this).addClass('pj-form-langbar-item-active');
						}else{
							$(this).removeClass('pj-form-langbar-item-active');
						}
					});
				}
			});
		}
		if ($frmUpdateGallery.length > 0 && validate) {
			$frmUpdateGallery.validate({
				rules: {
					medium_width: {
						required: true,
						digits: true
					},
					medium_height: {
						required: true,
						digits: true
					},
					small_width: {
						required: true,
						digits: true
					},
					small_height: {
						required: true,
						digits: true
					}
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
		}
		function onBeforeShow (obj) {
			var id = parseInt(obj.id, 10);
			if (id === 1) {
				return false;
			}
			return true;
		}
		function formatImage(val, obj) {
			var src = val ? val : myLabel.no_img;
			return ['<a href="index.php?controller=pjGallerySet&action=pjActionUpdate&id=', obj.id ,'"><img src="', src, '" style="width: 100px" /></a>'].join("");
		}
		if ($("#grid").length > 0 && datagrid) {
			var gridOpts = {
				buttons: [{type: "edit", url: "index.php?controller=pjGallerySet&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjGallerySet&action=pjActionDeleteGallery&id={:id}", beforeShow: onBeforeShow}
				          ],
				columns: [{text: myLabel.thumb, type: "text", width: 100, sortable: false, editable: false, renderer: formatImage},
				          {text: myLabel.name, type: "text", sortable: true, editable: true, width: 340, editableWidth: 280},
				          {text: myLabel.photos, type: "text", sortable: true, editable: false, width: 70, align: 'center'},
				          {text: myLabel.status, type: "select", sortable: true, editable: true, options: [
				                                                                                     {label: myLabel.active, value: "T"}, 
				                                                                                     {label: myLabel.inactive, value: "F"}
				                                                                                     ], applyClass: "pj-status"}],
				dataUrl: "index.php?controller=pjGallerySet&action=pjActionGetGallery",
				dataType: "json",
				fields: ['thumb', 'name', 'cnt_photos','status'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjGallerySet&action=pjActionDeleteGalleryBulk", render: true, confirmation: myLabel.delete_confirmation},
					   {text: myLabel.revert_status, url: "index.php?controller=pjGallerySet&action=pjActionStatusGallery", render: true}					   
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjGallerySet&action=pjActionSaveGallery&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			};
			
			if(myLabel.admin_mode == false)
			{
				gridOpts.buttons = [
				                   {type: "edit", url: "index.php?controller=pjGallerySet&action=pjActionUpdate&id={:id}"}
				];
				gridOpts.columns = [{text: myLabel.thumb, type: "text", width: 100, sortable: false, editable: false, renderer: formatImage},
							          {text: myLabel.name, type: "text", sortable: true, editable: true, width: 350, editableWidth: 280},
							          {text: myLabel.photos, type: "text", sortable: true, editable: false, width: 70, align: 'center'},
							          {text: myLabel.status, type: "select", sortable: true, editable: false, options: [
							                                                                                     {label: myLabel.active, value: "T"}, 
							                                                                                     {label: myLabel.inactive, value: "F"}
							                                                                                     ], applyClass: "pj-status"}
							          ];
			}
			var $grid = $("#grid").datagrid(gridOpts);
		}
		
		$(document).on("click", ".btn-all", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(this).addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
			var content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				status: "",
				q: ""
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjGallerySet&action=pjActionGetGallery", "name", "ASC", content.page, content.rowCount);
			return false;
		}).on("click", ".btn-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache"),
				obj = {};
			$this.addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
			obj.status = "";
			obj[$this.data("column")] = $this.data("value");
			$.extend(cache, obj);
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjGallerySet&action=pjActionGetGallery", "name", "ASC", content.page, content.rowCount);
			return false;
		}).on("submit", ".frm-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				q: $this.find("input[name='q']").val()
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjGallerySet&action=pjActionGetGallery", "name", "ASC", content.page, content.rowCount);
			return false;
		}).on('reset', '.frm-filter', function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				q: ""
			});
			$this.find('input[name="q"]').val("");
			$this.find('.pj-form-reset').css('visibility', 'hidden');
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjGallerySet&action=pjActionGetGallery", "name", "ASC", content.page, content.rowCount);
			return false;
		}).on('keyup', '.frm-filter input[name="q"]', function (e) {
			var $this = $(this),
				$reset = $this.closest('form').find('.pj-form-reset');
			if ($this.val().length) {
				$reset.css('visibility', 'visible');
			} else {
				$reset.css('visibility', 'hidden');
			}
		});
	});
})(jQuery_1_8_2);