var jQuery = jQuery || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";

		var $frmCreateProduct = $("#frmCreateProduct"),
			$frmUpdateProduct = $("#frmUpdateProduct"),
			$frmPrintSelectedStock = $('#frmPrintSelectedStock'),
			$frmProduct = $('.frmProduct'),
			$gallery = $("#gallery"),
			gallery = ($.fn.gallery !== undefined),
			dialog = ($.fn.dialog !== undefined),
			multilang = ($.fn.multilang !== undefined),
			validate = ($.fn.validate !== undefined),
			datagrid = ($.fn.datagrid !== undefined),
			m = window.location.href.match(/&id=(\d+)/),
			product_id, $similar;

		if (m !== null) {
			product_id = m[1];
		}

		if (multilang && 'pjCmsLocale' in window) {
			$(".multilang").multilang({
				langs: pjCmsLocale.langs,
				flagPath: pjCmsLocale.flagPath,
				tooltip: "",
				select: function (event, ui) {
					$("input[name='locale_id']").val(ui.index);
				}
			});
		}

		function myTinyMceDestroy() {
			if (window.tinymce === undefined) {
				return;
			}

			var iCnt = tinymce.editors.length;

			if (!iCnt) {
				return;
			}

			for (var i = 0; i < iCnt; i++) {
				tinymce.remove(tinymce.editors[i]);
			}
		}

		function myTinyMceInit(pSelector) {
			if (window.tinymce === undefined) {
				return;
			}

			tinymce.init({
				relative_urls: false,
				remove_script_host: false,
				convert_urls: true,
				browser_spellcheck: true,
				contextmenu: false,
				selector: pSelector,
				theme: "modern",
				height: 480,
				plugins: [
					"advlist autolink link image lists charmap print preview hr anchor pagebreak",
					"searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
					"save table contextmenu directionality emoticons template paste textcolor"
				],
				toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons",
				image_advtab: true,
				menubar: "file edit insert view table tools",
				setup: function (editor) {
					editor.on('change', function (e) {
						editor.editorManager.triggerSave();
					});
				}
			});
		}

		if ($('.mceEditor').length > 0) {
			myTinyMceDestroy.call(null);
			myTinyMceInit.call(null, 'textarea.mceEditor');
		}

		if ($(".select-item").length) {
			$(".select-item").select2({
				allowClear: true
			});
		};

		if ($('.i-checks').length > 0) {
			$('.i-checks').iCheck({
				checkboxClass: 'icheckbox_square-green',
				radioClass: 'iradio_square-green'
			});
		}

		if ($frmCreateProduct.length > 0 && validate) {
			$frmCreateProduct.validate({
				rules: {
					sku: {
						remote: "index.php?controller=pjAdminProducts&action=pjActionCheckSku"
					}
				},
				errorPlacement: function (error, element) {
					if (element.hasClass('select2-hidden-accessible')) {
						error.insertAfter(element.next('.select2-container'));
					} else if (element.parent('.input-group').length) {
						error.insertAfter(element.parent());
					} else if (element.parent().parent('.btn-group').length) {
						error.insertAfter(element.parent().parent());
					} else {
						error.insertAfter(element);
					}
				},
				ignore: ".ignore",
				invalidHandler: function (event, validator) {
					$(".pj-multilang-wrap").each(function (index) {
						if ($(this).attr('data-index') == myLabel.localeId) {
							$(this).css('display', 'block');
						} else {
							$(this).css('display', 'none');
						}
					});
					$(".pj-form-langbar-item").each(function (index) {
						if ($(this).attr('data-index') == myLabel.localeId) {
							$(this).addClass('btn-primary');
						} else {
							$(this).removeClass('btn-primary');
						}
					});
				},
				submitHandler: function (form) {
					var ladda_buttons = $(form).find('.ladda-button');
					if (ladda_buttons.length > 0) {
						var l = ladda_buttons.ladda();
						l.ladda('start');
					}
					form.submit();
					return false;
				}
			});
		}
		if ($frmUpdateProduct.length > 0 && validate) {
			$frmUpdateProduct.validate({
				rules: {
					sku: {
						remote: "index.php?controller=pjAdminProducts&action=pjActionCheckSku&id=" + $frmUpdateProduct.find('input[name="id"]').val()
					}
				},
				errorPlacement: function (error, element) {
					if (element.hasClass('select2-hidden-accessible')) {
						error.insertAfter(element.next('.select2-container'));
					} else if (element.parent('.input-group').length) {
						error.insertAfter(element.parent());
					} else if (element.parent().parent('.btn-group').length) {
						error.insertAfter(element.parent().parent());
					} else {
						error.insertAfter(element);
					}
				},
				ignore: ".ignore",
				invalidHandler: function (event, validator) {
					console.log(1)
					if (validator.numberOfInvalids()) {
						var $_id = $(validator.errorList[0].element, this).closest("div.tab-pane").attr("id");
						$('.tab-' + $_id).trigger("click");
					};
					$(".pj-multilang-wrap").each(function (index) {
						if ($(this).attr('data-index') == myLabel.localeId) {
							$(this).css('display', 'block');
						} else {
							$(this).css('display', 'none');
						}
					});
					$(".pj-form-langbar-item").each(function (index) {
						if ($(this).attr('data-index') == myLabel.localeId) {
							$(this).addClass('btn-primary');
						} else {
							$(this).removeClass('btn-primary');
						}
					});
				},
				submitHandler: function (form) {
					var ladda_buttons = $(form).find('.ladda-button');
					if (ladda_buttons.length > 0) {
						var l = ladda_buttons.ladda();
						l.ladda('start');
					}

					$.post("index.php?controller=pjAdminProducts&action=pjActionCheckStockAttributes", $(form).serialize()).done(function (data) {
						if (data.status == 'OK') {
							handleDigitalInStock.call(null);
							form.submit();
						} else {
							swal({
								title: myLabel.alert_overlapping_attributes_title,
								text: myLabel.alert_overlapping_attributes_text,
								type: "error",
								confirmButtonColor: "#DD6B55",
								confirmButtonText: myLabel.alert_btn_close,
								closeOnConfirm: false,
								showLoaderOnConfirm: false
							}, function () {
								swal.close();
							});
						}
					}).always(function () {
						l.ladda('stop');
					});
					return false;
				}
			});
			fireGroupSortable();
			fireItemSortable();
		}

		if ($frmUpdateProduct.length > 0) {
			function formatSimilar(str, obj) {
				if (myLabel.has_update) {
					return ['<a href="index.php?controller=pjAdminProducts&action=pjActionUpdate&id=' + obj.similar_id + '">', str, '</a>'].join("");
				} else {
					return str;
				}
			}
			if ($("#boxSimilar").length > 0 && datagrid && product_id) {
				var $buttons = [];
				var $actions = [];
				var $editable = false;
				var $select = false;
				if (myLabel.has_update) {
					$editable = true;
					$buttons.push({ type: "edit", url: "index.php?controller=pjAdminProducts&action=pjActionUpdate&id={:similar_id}" });
				}
				$buttons.push({ type: "delete", url: "index.php?controller=pjAdminProducts&action=pjActionDeleteSimilar&id={:id}" });
				$actions.push({ text: myLabel.delete_selected, url: "index.php?controller=pjAdminProducts&action=pjActionDeleteSimilarBulk", render: true, confirmation: myLabel.delete_confirmation });
				if ($actions.length > 0) {
					$select = {
						field: "id",
						name: "record[]",
						cellClass: 'cell-width-2'
					};
				}
				$similar = $("#boxSimilar").datagrid({
					buttons: $buttons,
					columns: [{ text: myLabel.name, type: "text", sortable: true, editable: false, renderer: formatSimilar },
					{ text: myLabel.sku, type: "text", sortable: true, editable: false },
					{ text: myLabel.status, type: "toggle", sortable: true, editable: false, positiveClass: "pj-toggle-on", negativeClass: "pj-toggle-off", positiveLabel: myLabel.active, positiveValue: "1", negativeLabel: myLabel.inactive, negativeValue: "0" }],
					dataUrl: "index.php?controller=pjAdminProducts&action=pjActionGetSimilar&id=" + product_id,
					dataType: "json",
					fields: ['name', 'sku', 'status'],
					paginator: {
						actions: $actions,
						gotoPage: true,
						paginate: true,
						total: true,
						rowCount: true
					},
					saveUrl: null,
					select: $select
				});
			}

			if ($("#similar_id").length > 0 && product_id && $similar && $similar.length) {
				$("#similar_id").autocompleter({
					minLength: 2,
					limit: 50,
					highlightMatches: true,
					cache: false,
					source: "index.php?controller=pjAdminProducts&action=pjActionSearchProducts&id=" + product_id,
					callback: function (value, index, selected) {
						if (selected) {
							$.post("index.php?controller=pjAdminProducts&action=pjActionAddSimilar", {
								"product_id": product_id,
								"similar_id": selected.id
							}).done(function (data) {
								$("#similar_id").val("");
								var content = $similar.datagrid("option", "content");
								$similar.datagrid("load", "index.php?controller=pjAdminProducts&action=pjActionGetSimilar&id=" + product_id, "name", "ASC", content.page, content.rowCount);
							});
						}
					}
				});
			}

			if ($("#boxHistory").length > 0 && product_id) {
				$.get("index.php?controller=pjAdminProducts&action=pjActionGetHistory&id=" + product_id).done(function (data) {
					$('#boxHistory').html(data);
				});
			}
		}

		if ($gallery.length > 0 && gallery) {
			$gallery.gallery({
				compressUrl: "index.php?controller=pjGallery&action=pjActionCompressGallery&model=pjProduct&foreign_id=" + myGallery.foreign_id + "&hash=" + myGallery.hash,
				getUrl: "index.php?controller=pjGallery&action=pjActionGetGallery&model=pjProduct&foreign_id=" + myGallery.foreign_id + "&hash=" + myGallery.hash,
				deleteUrl: "index.php?controller=pjGallery&action=pjActionDeleteGallery",
				emptyUrl: "index.php?controller=pjGallery&action=pjActionEmptyGallery&model=pjProduct&foreign_id=" + myGallery.foreign_id + "&hash=" + myGallery.hash,
				rebuildUrl: "index.php?controller=pjGallery&action=pjActionRebuildGallery&model=pjProduct&foreign_id=" + myGallery.foreign_id + "&hash=" + myGallery.hash,
				resizeUrl: "index.php?controller=pjGallery&action=pjActionCrop&model=pjProduct&id={:id}&foreign_id=" + myGallery.foreign_id + "&hash=" + myGallery.hash + ($frmUpdateProduct.length > 0 ? "&query_string=" + encodeURIComponent("controller=pjAdminProducts&action=pjActionUpdate&id=" + myGallery.foreign_id + "&tab=photos") : ""),
				rotateUrl: "index.php?controller=pjGallery&action=pjActionRotateGallery",
				sortUrl: "index.php?controller=pjGallery&action=pjActionSortGallery",
				updateUrl: "index.php?controller=pjGallery&action=pjActionUpdateGallery",
				uploadUrl: "index.php?controller=pjGallery&action=pjActionUploadGallery&model=pjProduct&foreign_id=" + myGallery.foreign_id + "&hash=" + myGallery.hash,
				watermarkUrl: "index.php?controller=pjGallery&action=pjActionWatermarkGallery&model=pjProduct&foreign_id=" + myGallery.foreign_id + "&hash=" + myGallery.hash
			});
		}

		function formatImage(path, obj) {
			var src = (typeof myLabel !== 'undefined' && myLabel.placeholderImage)
				? myLabel.placeholderImage
				: 'app/web/img/frontend/80x106.png';
			if (path !== null && path !== undefined && String(path).length > 0) {
				src = path;
			}
			return ['<a href="index.php?controller=pjAdminProducts&action=pjActionUpdate&id=', obj.id, '" class="s-Pic"><img src="', src, '" alt="" class="s-Img" /></a>'].join('');
		}
		function formatProductName(val, obj) {
			var name = obj.name || '';
			if (myLabel.has_update) {
				var editUrl = 'index.php?controller=pjAdminProducts&action=pjActionUpdate&id=' + obj.id;
				return '<div style="text-align:left;"><span class="s-Name"><a href="' + editUrl + '">' + name + '</a></span></div>';
			}
			return '<div style="text-align:left;"><span class="s-Name">' + name + '</span></div>';
		}
		function formatImageFlatFile(path, obj) {
			return pjGridImage.link(path, 'index.php?controller=pjAdminProducts&action=pjActionUpdateFlatFile&id=' + obj.id, true);
		}
		function formatModelImageFlatFile(path) {
			return pjGridImage.thumb(path, true);
		}

		function formatMinPrice(price, obj) {
			return obj.min_price_format;
		}
		function formatStock(stock, obj) {
			if (stock == 0 || stock == '0') {
				return '<span class="text-danger">' + stock + '</span>';
			} else {
				return stock;
			}
		}
		if ($("#grid").length > 0 && datagrid) {
			var $buttons = [];
			var $actions = [];
			var $editable = false;
			var $select = false;
			if (myLabel.has_update) {
				$editable = true;
				$buttons.push({ type: "edit", url: "index.php?controller=pjAdminProducts&action=pjActionUpdate&id={:id}" });
			}
			if (myLabel.has_delete) {
				$buttons.push({ type: "delete", url: "index.php?controller=pjAdminProducts&action=pjActionDeleteProduct&id={:id}" });
			}
			if (myLabel.exported) {
				$actions.push({ text: myLabel.exported, url: "index.php?controller=pjAdminProducts&action=pjActionExportProduct", ajax: false });
			}
			if (myLabel.has_delete_bulk) {
				$actions.push({ text: myLabel.delete_selected, url: "index.php?controller=pjAdminProducts&action=pjActionDeleteProductBulk", render: true, confirmation: myLabel.delete_confirmation });
			}
			if ($actions.length > 0) {
				$select = {
					field: "id",
					name: "record[]",
					cellClass: 'cell-width-2'
				};
			}
			var $grid = $("#grid").datagrid({
				buttons: $buttons,
				columns: [
				{ text: myLabel.image, type: "text", sortable: false, editable: false, renderer: formatImage, width: 70, cellClass: 'col-product-image' },
				{ text: myLabel.name, type: "text", align: "left", sortable: true, editable: $editable, renderer: formatProductName, width: 320, cellClass: 'col-product-name' },
				{ text: myLabel.sku, type: "text", sortable: true, editable: $editable },
				{ text: myLabel.stock, type: "text", sortable: true, editable: false, renderer: formatStock },
				{ text: myLabel.price, type: "text", sortable: true, editable: false, renderer: formatMinPrice },
				{ text: myLabel.status, type: "toggle", sortable: true, editable: $editable, positiveClass: "pj-toggle-on", negativeClass: "pj-toggle-off", positiveLabel: myLabel.active, positiveValue: "1", negativeLabel: myLabel.inactive, negativeValue: "0" }],
				dataUrl: "index.php?controller=pjAdminProducts&action=pjActionGetProduct" + pjGrid.queryString,
				dataType: "json",
				fields: ['pic', 'name', 'sku', 'total_stock', 'min_price', 'status'],
				paginator: {
					actions: $actions,
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminProducts&action=pjActionSaveProduct&id={:id}",
				select: $select
			});
		}
		if ($("#grid_flat_file").length > 0 && datagrid) {
			var $editable = false;
			function formatLongText(val) {
				if (val == null || val == '') {
					return '';
				}

				// HTML remove
				var text = $('<div>').html(val).text();

				// limit
				var short_text = text.length > 80
					? text.substring(0, 80) + '...'
					: text;

				return '<div title="' + text.replace(/"/g, '&quot;') + '">' + short_text + '</div>';
			}
			var $buttons = [];
			var $actions = [];
			var $select = false;

			/* ENABLE CHECKBOX SELECT */

			if (myLabel.has_update) {
				$editable = true;
				$buttons.push({ type: "edit", url: "index.php?controller=pjAdminProducts&action=pjActionUpdateFlatFile&id={:id}" });
			}
			if (myLabel.has_delete) {
				$buttons.push({ type: "delete", url: "index.php?controller=pjAdminProducts&action=pjActionDeleteStock&id={:id}" });
			}
			if (myLabel.exported) {
				$actions.push({ text: myLabel.exported, url: "index.php?controller=pjAdminProducts&action=pjActionExportProduct", ajax: false });
			}
			if (myLabel.has_delete_bulk) {
				$actions.push({ text: myLabel.delete_selected, url: "index.php?controller=pjAdminProducts&action=pjActionDeleteProductBulk", render: true, confirmation: myLabel.delete_confirmation });
			}
			if ($actions.length > 0) {
				$select = {
					field: "id",
					name: "record[]",
					cellClass: 'cell-width-2'
				};
			}

			var $grid_flat_file = $("#grid_flat_file").datagrid({

				buttons: $buttons,

				columns: [

					{ text: myLabel.import_image, type: "text", sortable: false, editable: false, width: 70, cellClass: "col-product-image", renderer: formatImageFlatFile },
					{ text: myLabel.import_model_image, type: "text", sortable: false, editable: false, width: 70, cellClass: "col-product-image", renderer: formatModelImageFlatFile },
					{ text: myLabel.import_status, type: "toggle", sortable: true, editable: $editable, positiveClass: "pj-toggle-on", negativeClass: "pj-toggle-off", positiveLabel: myLabel.active, positiveValue: "1", negativeLabel: myLabel.inactive, negativeValue: "0" },
					{ text: myLabel.import_model, type: "text", sortable: true, editable: true },

					{ text: myLabel.import_model_name, type: "text", sortable: true, editable: true },

					{ text: myLabel.import_sku, type: "text", sortable: true, editable: true },

					{ text: myLabel.import_brand, type: "text", sortable: true, editable: false },

					{ text: myLabel.import_category, type: "text", sortable: true, editable: false },

					{ text: myLabel.import_article_number, type: "text", sortable: true, editable: true },

					{ text: myLabel.import_article_name, type: "text", sortable: true, editable: true },

					{ text: myLabel.import_material, type: "text", sortable: true, editable: true },

					{ text: myLabel.import_ean, type: "text", sortable: true, editable: true },

					{ text: myLabel.import_safety_standard, type: "text", sortable: true, editable: true },

					{ text: myLabel.import_size, type: "text", sortable: true, editable: true },

					{ text: myLabel.import_color, type: "text", sortable: true, editable: true },

					{ text: myLabel.import_stock, type: "text", sortable: true, editable: true },

					{ text: myLabel.import_buying_price, type: "text", sortable: true, editable: true },

					{ text: myLabel.import_price, type: "text", sortable: true, editable: true },

					{ text: myLabel.import_name_en, type: "text", sortable: true, editable: true },

					{ text: myLabel.import_short_desc_en, type: "text", sortable: false, editable: true , renderer: formatLongText },

					{ text: myLabel.import_full_description_en, type: "text", sortable: false, editable: true, renderer: formatLongText  },

					// { text: myLabel.import_sync_status, type: "text", sortable: true, editable: false },

					// { text: myLabel.import_created_at, type: "text", sortable: true }

				],

				dataUrl: "index.php?controller=pjAdminProducts&action=pjActionGetProductFlatfile",

				dataType: "json",

				fields: [

					'image',
					'model_image',
					'status',
					'model',
					'model_name',
					'sku',
					'brand',
					'category',
					'article_number',
					'article_name',
					'material',
					'ean',
					'safety_standard',
					'size',
					'color',
					'qty',
					'buying_price',
					'price',
					'name_en',
					'short_desc_en',
					'full_description_en',
					// 'sync_status',
					// 'created_at'

				],

				paginator: {
					// actions: $actions,
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},

				saveUrl: "index.php?controller=pjAdminProducts&action=pjActionSaveFlatfileRow&id={:id}",

				// select: $select,

				/* MAINTAIN CHECK STATE AFTER PAGINATION */

				// onRender: function () {

				// 	var $grid = $("#grid_flat_file");

				// 	var total_rows = 0;
				// 	var unchecked_count = 0;

				// 	/* ROW CHECKBOXES */

				// 	$grid.find(".pj-table-select-row").each(function () {

				// 		var val = $(this).val();
				// 		total_rows++;

				// 		if (unchecked_rows.indexOf(val) !== -1) {

				// 			$(this).iCheck('uncheck');
				// 			unchecked_count++;

				// 		} else {

				// 			$(this).iCheck('check');

				// 		}

				// 	});

				// 	/* HEADER CHECKBOX */

				// 	if (unchecked_count === 0) {

				// 		$grid.find(".pj-table-toggle-rows").iCheck('check');

				// 	} else {

				// 		$grid.find(".pj-table-toggle-rows").iCheck('uncheck');

				// 	}

				// }

			});

		}
		if ($("#grid_stock").length > 0 && datagrid) {
			function formatName(val, obj) {
				var parts, arr = [];
				for (var i = 0, iCnt = obj.stock_attr.length; i < iCnt; i++) {
					parts = obj.stock_attr[i].split("~:~");
					if (parts.length == 2) {
						arr.push(parts[0] + ": " + parts[1]);
					}
				}
				var src = (obj.pic && String(obj.pic).length > 0) ? obj.pic : ((typeof myLabel !== 'undefined' && myLabel.placeholderImage) ? myLabel.placeholderImage : 'app/web/img/frontend/80x106.png');
				var stockUrl = 'index.php?controller=pjAdminProducts&action=pjActionUpdate&id=' + obj.product_id + '&tab=stock';
				var picHtml = myLabel.has_update
					? '<a href="' + stockUrl + '" class="s-Pic"><img src="' + src + '" alt="" class="s-Img" /></a>'
					: '<span class="s-Pic"><img src="' + src + '" alt="" class="s-Img" /></span>';
				return [picHtml,
					'<span class="s-Name"><a href="' + (myLabel.has_update ? stockUrl : 'javascript:void(0);') + '">', obj.name, '</a></span>',
					(arr.length > 0 ? ['<span class="s-Attr">(', arr.join(", "), ')</span>'].join('') : '')
				].join("");
			}

			function formatPrice(val, obj) {
				return obj.price_formated;
			}
			function formatQty(qty, obj) {
				if (qty == 0 || qty == '0') {
					return '<span class="bold red">' + qty + '</span>';
				} else {
					return qty;
				}
			}

			var $editable = false;
			if (myLabel.has_update) {
				$editable = true;
			}
			var $grid_stock = $("#grid_stock").datagrid({
				buttons: [],
				columns: [{ text: myLabel.name, type: "text", sortable: true, editable: false, renderer: formatName },
				{ text: myLabel.price, type: "text", sortable: true, editable: $editable, renderer: formatPrice },
				{ text: myLabel.qty, type: "text", renderer: formatQty, sortable: true, editable: $editable }],
				dataUrl: "index.php?controller=pjAdminProducts&action=pjActionGetStock",
				dataType: "json",
				fields: ['name', 'price', 'qty'],
				paginator: {
					actions: [
						{ text: myLabel.print_selected, url: "javascript:void(0);", render: false },
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminProducts&action=pjActionSaveStock&id={:id}",
				select: {
					field: "id",
					name: "record[]",
					cellClass: 'cell-width-2'
				}
			});

			$("#grid_stock").on("click", '.pj-paginator-action', function (e) {
				e.preventDefault();
				var stock_id = $('.pj-table-select-row:checked').map(function (e) {
					return $(this).val();
				}).get();
				if (stock_id != '' && stock_id != null) {
					$('.scStockIdHidden').remove();
					$.each(stock_id, function (key, value) {
						$frmPrintSelectedStock.append('<input type="hidden" name="record[]" value="' + value + '" class="scStockIdHidden" />');
					});
					$frmPrintSelectedStock.submit();
				}
				return false;
			});
		}

		$(document).on("submit", ".frm-filter-stock", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid_stock.datagrid("option", "content"),
				cache = $grid_stock.datagrid("option", "cache");
			$.extend(cache, {
				q: $this.find("input[name='q']").val()
			});
			$grid_stock.datagrid("option", "cache", cache);
			$grid_stock.datagrid("load", "index.php?controller=pjAdminProducts&action=pjActionGetStock", "name", "ASC", content.page, content.rowCount);
			return false;
		}).on("click", ".btn-all", function (e) {
			$(this).addClass("btn-primary active").removeClass("btn-default")
				.siblings(".btn").removeClass("btn-primary active").addClass("btn-default");
			var content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				status: "",
				q: "",
				is_out: ""
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminProducts&action=pjActionGetProduct", "name", "ASC", content.page, content.rowCount);
			return false;
		}).on("click", ".btn-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache"),
				obj = {};
			$this.addClass("btn-primary active").removeClass("btn-default")
				.siblings(".btn").removeClass("btn-primary active").addClass("btn-default");
			obj.status = "";
			if ($this.data("value") == '3' && $this.data("column") == 'status') {
				obj['is_out'] = 'yes';
			} else {
				obj['is_out'] = '';
				obj[$this.data("column")] = $this.data("value");
			}
			$.extend(cache, obj);
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminProducts&action=pjActionGetProduct", "name", "ASC", content.page, content.rowCount);
			return false;
		})
			.on("submit", ".frm-filter", function (e) {
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
				$grid.datagrid("load", "index.php?controller=pjAdminProducts&action=pjActionGetProduct", "id", "ASC", content.page, content.rowCount);
				return false;
			})
			.on("submit", ".frm-filter-flat-file", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $this = $(this),
					content = $grid_flat_file.datagrid("option", "content"),
					cache = $grid_flat_file.datagrid("option", "cache");
				$.extend(cache, {
					q: $this.find("input[name='q']").val()
				});
				$grid_flat_file.datagrid("option", "cache", cache);
				$grid_flat_file.datagrid("load", "index.php?controller=pjAdminProducts&action=pjActionGetProductFlatfile", "id", "ASC", content.page, content.rowCount);
				return false;
			})
			.on("submit", ".frm-filter-stock", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $this = $(this),
					content = $grid_stock.datagrid("option", "content"),
					cache = $grid_stock.datagrid("option", "cache");
				$.extend(cache, {
					q: $this.find("input[name='q']").val()
				});
				$grid_stock.datagrid("option", "cache", cache);
				$grid_stock.datagrid("load", "index.php?controller=pjAdminProducts&action=pjActionGetStock", "name", "ASC", content.page, content.rowCount);
				return false;
			}).on("submit", ".frm-filter-advanced", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var obj = {},
					$this = $(this),
					arr = $this.serializeArray(),
					content = $grid.datagrid("option", "content"),
					cache = $grid.datagrid("option", "cache");
				for (var i = 0, iCnt = arr.length; i < iCnt; i++) {
					obj[arr[i].name] = arr[i].value;
				}
				$.extend(cache, obj);
				$grid.datagrid("option", "cache", cache);
				$grid.datagrid("load", "index.php?controller=pjAdminReservations&action=pjActionGetReservation", content.column, content.direction, content.page, content.rowCount);
				return false;
			}).on("reset", ".frm-filter-advanced", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $frm = $('.frm-filter-advanced');
				$frm.find("input[name='c_name']").val('');
				$frm.find("input[name='c_email']").val('');
				$frm.find("input[name='uuid']").val('');
				$frm.find("select[name='calendar_id']").val('');
				$frm.find("select[name='status']").val('');
				$frm.find("input[name='date_from']").val('');
				$frm.find("input[name='date_to']").val('');
				$frm.find("input[name='amount_from']").val('');
				$frm.find("input[name='amount_to']").val('');
				$(".btn-advance-search").trigger("click");
				$('.frm-filter-advanced').submit();
				return false;
			}).on("change", '#category_id', function (e) {
				$(this).valid();
			}).on("change", ".onoffswitch-digital .onoffswitch-checkbox", function (e) {
				if ($(this).prop('checked')) {
					$('#boxDigitalOuter').show();
					$('.pjProductAttr').addClass('disabled');
					$('.tblStocks').find('.pjScQuantity').removeClass('required');
				} else {
					$('#boxDigitalOuter').hide();
					$('.pjProductAttr').removeClass('disabled');
					$('.tblStocks').find('.pjScQuantity').addClass('required');
				}
			}).on("ifChecked", "input[name='digital_choose']", function (e) {
				switch (parseInt($(this).val(), 10)) {
					case 1:
						$(".digitalFile").show();
						$(".digitalPath").hide();
						break;
					case 2:
						$(".digitalFile").hide();
						$(".digitalPath").show();
						break;
				}
			}).on("shown.bs.tab", '#frmUpdateProduct a[data-toggle="tab"]', function (e) {
				var $a = $(e.target),
					tab = $a.attr('data-tab'),
					$form = $a.closest('form');
				if (!tab) {
					var hash = $a.attr('href') || '';
					if (hash.indexOf('#product-') === 0) {
						tab = hash.replace('#product-', '');
					}
				}
				if (!tab || !$form.length) {
					return;
				}
				$form.find('input[name="tab"]').val(tab);
				if (window.history && window.history.replaceState) {
					var url = new URL(window.location.href);
					url.searchParams.set('tab', tab);
					window.history.replaceState(null, '', url.toString());
				}
			}).on("click", ".btnDigitalDelete", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var id = $(this).attr('data-id');
				var $this = $(this);
				swal({
					title: myLabel.alert_del_digital_title,
					text: myLabel.alert_del_digital_text,
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: myLabel.btn_delete,
					cancelButtonText: myLabel.btn_cancel,
					closeOnConfirm: false,
					showLoaderOnConfirm: true
				}, function () {
					$.post($this.attr("href"), { id: id }).done(function (data) {
						swal.close();
						$("#boxDigitalOuter").html(data);
					});
				});
			}).on("click", ".btnAddAttribute", function (e) {
				console.log(2)
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $clone = $("#boxAddAttribute").clone(),
					$this = $(this),
					c = $clone.html(),
					index1 = Math.ceil(Math.random() * 999999).toString(),
					index2 = Math.ceil(Math.random() * 999999).toString();
				c = c.replace(/\{INDEX\}/g, "x_" + index1).replace(/\{X\}/g, "y_" + index2);
				if ($('#boxAttributes').find('.attrBox').length > 0) {
					$(c).appendTo("#boxAttributes");
				} else {
					$('#boxAttributes').html(c);
				}
				fireGroupSortable();
				fireItemSortable();
				return false;
			}).on("click", ".btnAddAttr", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $clone = $("#boxAddAttr").clone(),
					$this = $(this),
					c = $clone.html(),
					index = Math.ceil(Math.random() * 999999).toString();
				c = c.replace(/\{INDEX\}/g, $this.attr("rel")).replace(/\{X\}/g, "y_" + index);
				$(c).appendTo($this.closest(".attrBox").find(".attrBoxRowStick"));
				fireItemSortable();
				return false;
			}).on("click", ".btnAttrGroupRemove", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				$(this).closest(".attrBox").remove();
				if ($('#boxAttributes').find('.attrBox').length == 0) {
					$('#boxAttributes').html(myLabel.no_attrs);
				}
				fireGroupSortable();
				return false;
			}).on("click", ".btnAttrRemove", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				$(this).closest(".attrBoxRowItems").remove();
				fireItemSortable();
				return false;
			}).on("click", ".btnCopyAttribute", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				$('#modalCopyAttr').modal('show');
				return false;
			}).on("click", ".btnAttrGroupDelete", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $id = $(this).attr('data-id');
				swal({
					title: myLabel.alert_del_attr_group_title,
					text: myLabel.alert_del_attr_group_text,
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: myLabel.btn_delete,
					cancelButtonText: myLabel.btn_cancel,
					closeOnConfirm: false,
					showLoaderOnConfirm: true
				}, function () {
					$.post("index.php?controller=pjAdminProducts&action=pjActionAttrGroupDelete", {
						"id": $id
					}).done(function () {
						getAttributes.call(null);
						swal.close();
					});
				});
			}).on("click", ".btnAttrDelete", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $id = $(this).attr('data-id');
				swal({
					title: myLabel.alert_del_attr_title,
					text: myLabel.alert_del_attr_text,
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: myLabel.btn_delete,
					cancelButtonText: myLabel.btn_cancel,
					closeOnConfirm: false,
					showLoaderOnConfirm: true
				}, function () {
					$.post("index.php?controller=pjAdminProducts&action=pjActionAttrDelete", {
						"id": $id
					}).done(function () {
						getAttributes.call(null);
						swal.close();
					});
				});
			}).on("click", ".btnCopy", function () {
				var product_id, hash,
					$this = $(this),
					obj = { "from_product_id": $this.val() };
				product_id = $frmProduct.find("input[name='id']").val();
				hash = $frmProduct.find("input[name='hash']").val();
				if (product_id !== undefined && product_id !== "") {
					obj.product_id = product_id;
				} else if (hash !== undefined && hash !== "") {
					obj.hash = hash;
				}

				if ($this.hasClass("copyAttr")) {

					$.post("index.php?controller=pjAdminProducts&action=pjActionAttrCopy", obj).done(function (data) {
						getAttributes.call(null);
					});
					$('#modalCopyAttr').modal('hide');

				} else if ($this.hasClass("copyExtra")) {
					$.post("index.php?controller=pjAdminProducts&action=pjActionExtraCopy", obj).done(function (data) {
						getExtras.call(null);
					});
					$('#modalCopyExtra').modal('hide');
				}
			}).on("change", ".pj-model-image-upload", function () {
				var $input = $(this),
					file = this.files && this.files[0],
					productId = $input.data("product-id") || $(":input[name='id']").val();

				if (!file || !productId) {
					return;
				}

				var formData = new FormData();
				formData.append("model_image", file);
				formData.append("product_id", productId);

				$input.prop("disabled", true);

				$.ajax({
					url: "index.php?controller=pjAdminProducts&action=pjActionUploadModelImage",
					type: "POST",
					data: formData,
					processData: false,
					contentType: false,
					dataType: "json"
				}).done(function (data) {
					if (data && data.status === "OK" && data.id) {
						pjSelectModelImage(data.id);
					} else {
						swal({
							title: myLabel.productModelImageUploadFailed,
							text: (data && data.text) ? data.text : myLabel.productModelImageUploadFailedText,
							type: "error",
							confirmButtonColor: "#d9534f"
						});
					}
				}).fail(function () {
					swal({
						title: myLabel.productModelImageUploadFailed,
						text: myLabel.productModelImageUploadFailedText,
						type: "error",
						confirmButtonColor: "#d9534f"
					});
				}).always(function () {
					$input.val("").prop("disabled", false);
				});
			}).on("click", ".pj-model-image-select", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				pjSelectModelImage($(this).attr("rel"));
				return false;
			}).on("click", ".pj-model-image-delete", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				if (e && e.stopPropagation) {
					e.stopPropagation();
				}

				var $item = $(this).closest(".pj-model-image-item"),
					$field = $(this).closest(".pj-model-image-field"),
					productId = $field.data("product-id") || $(":input[name='id']").val(),
					galleryId = $item.data("id");

				if (!productId || !galleryId) {
					return false;
				}

				swal({
					title: "",
					text: myLabel.delete_confirmation,
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: myLabel.btn_delete,
					cancelButtonText: myLabel.btn_cancel,
					closeOnConfirm: true,
					html: true
				}, function (isConfirm) {
					if (isConfirm !== true) {
						return;
					}
					$.post("index.php?controller=pjAdminProducts&action=pjActionDeleteModelImage", {
						product_id: productId,
						id: galleryId
					}, null, "json").done(function (data) {
						if (data && data.status === "OK") {
							$field.find("input[name='model_image_id']").val(data.model_image_id || "");
							pjRefreshModelImageGrid();
						} else {
							swal({
								title: myLabel.productModelImageUploadFailed,
								text: (data && data.text) ? data.text : myLabel.productModelImageUploadFailedText,
								type: "error",
								confirmButtonColor: "#d9534f"
							});
						}
					});
				});

				return false;
			}).on("click", ".btnImageStock", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				$('#modalImageStock').data("lnk", $(this)).data("target", "stock").modal('show');
				return false;
			})
			.on("click", "#modalImageStock .stock-image", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}

				var $this = $(this),
					id = $this.attr("rel"),
					target = 'stock',
					btnClass = 'btnImageStock',
					inputSelector = "input[name^='stock_image_id']",
					$a = $("<a>", {
						"href": "#"
					}).addClass(btnClass).attr("rel", id);

				$("<img>", {
					"src": $this.find("img").attr("src")
				}).addClass("in-stock s-Img").appendTo($a);

				var $input = $('#modalImageStock')
					.data("lnk")
					.closest('.form-group, td')
					.find(inputSelector);
				if (!$input.length) {
					$input = $('#modalImageStock')
						.data("lnk")
						.siblings("div")
						.find(inputSelector);
				}

				$input.val(id);

				if (
					$input.length &&
					$input.closest("form").length &&
					$input.closest("form").data('validator')
				) {
					$input.valid();
				}
				$('#modalImageStock').data("lnk").replaceWith($a);

				$('#modalImageStock').removeData("target").modal('hide');

				return false;
			})
			// .on("click", ".stock-image", function (e) {
			// 	if (e && e.preventDefault) {
			// 		e.preventDefault();
			// 	}
			// 	var $this = $(this),
			// 		id = $this.attr("rel"),
			// 		$a = $("<a>", {
			// 			"href": "#"
			// 		}).addClass("btnImageStock").attr("rel", id);

			// 	$("<img>", {
			// 		"src": $this.find("img").attr("src")
			// 	}).addClass("in-stock").appendTo($a);

			// 	$('#modalImageStock').data("lnk").siblings("div").find("input[name^='stock_image_id']").val(id).valid();
			// 	$('#modalImageStock').data("lnk").replaceWith($a);
			// 	$('#modalImageStock').modal('hide');
			// 	return false;
			// })
			.on("click", ".btnRemoveStock", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				$(this).closest('tr').remove();
				return false;
			})
			.on("click", ".btnStockAdd", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $clone, c, index,
					$tbody = $(this).parent().siblings('.stockContainer').children(".tblStocks").find("tbody");

				handleDigitalInStock.call(null, function () {
					$clone = $("#boxStockCloneTbl").find("tbody").clone();
					c = $clone.html();
					index = Math.ceil(Math.random() * 999999).toString();
					c = c.replace(/\{INDEX\}/g, "x_" + index);
					$(c).appendTo($tbody);
					if ($('#scHiddenImageId').length > 0) {
						var $btnImage = $tbody.find('tr:last').find('.btnImageStock'),
							id = $('#scHiddenImageId').val(),
							src = $('#scHiddenImageId').attr('data-src');

						var $a = $("<a>", {
							"href": "#"
						}).addClass("btnImageStock").attr("rel", id);

						$("<img>", {
							"src": src
						}).addClass("in-stock s-Img").appendTo($a);

						$btnImage.siblings("span").find("input[name^='stock_image_id']").val(id).valid();
						$btnImage.replaceWith($a);
					}
				});
				return false;
			}).on("click", ".btnDeleteStock", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $parent = $(this).closest('tr'),
					$id = $(this).attr('rel');
				swal({
					title: myLabel.alert_del_stock_title,
					text: myLabel.alert_del_stock_text,
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: myLabel.btn_delete,
					cancelButtonText: myLabel.btn_cancel,
					closeOnConfirm: false,
					showLoaderOnConfirm: true
				}, function () {
					$.post("index.php?controller=pjAdminProducts&action=pjActionDeleteStock", {
						id: $id
					}).done(function (data) {
						if (data.status == 'OK') {
							$parent.remove();
							swal.close();
						}
					});
				});
			}).on("click", ".btnAddExtra", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $clone = $("#boxClone").clone(),
					c = $clone.html(),
					index1 = Math.ceil(Math.random() * 999999).toString(),
					index2 = Math.ceil(Math.random() * 999999).toString();
				c = c.replace(/\{INDEX\}/g, "x_" + index1);
				c = c.replace(/\{X\}/g, "y_" + index2);
				if ($('#boxExtras').find('.extraBox').length > 0) {
					$(this).parent().prev().append(c);
				} else {
					$(this).parent().prev().html(c);
				}
				if ($('.i-checks-x_' + index1).length > 0) {
					$('.i-checks-x_' + index1).iCheck({
						checkboxClass: 'icheckbox_square-green',
						radioClass: 'iradio_square-green'
					});
				}
			}).on("change", ":input[name^='extra_type[']", function (e) {
				var $this = $(this),
					$boxSingle = $this.closest(".extraBox").find(".boxSingle"),
					$boxMulti = $this.closest(".extraBox").find(".boxMulti");
				switch ($("option:selected", $this).val()) {
					case 'single':
						$boxSingle.find(":input").prop("disabled", false);
						$boxSingle.show();
						$boxMulti.hide();
						$boxMulti.find(":input").prop("disabled", true);
						break;
					case 'multi':
						$boxSingle.hide();
						$boxSingle.find(":input").prop("disabled", true);
						$boxMulti.find(":input").prop("disabled", false);
						$boxMulti.show();
						break;
				}
			}).on("click", ".btnAddExtraItem", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $clone = $("#boxCloneTbl").find("tbody").clone(),
					c = $clone.html(),
					index2 = Math.ceil(Math.random() * 999999).toString();
				c = c.replace(/\{INDEX\}/g, $(this).data("index"));
				c = c.replace(/\{X\}/g, "y_" + index2);
				$(c).appendTo($(this).siblings("table").eq(0).find("tbody"));
				return false;
			}).on("click", ".btnRemoveExtraItem", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				$(this).closest('tr').remove();
				return false;
			}).on("click", ".btnDeleteExtraTmp", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				$(this).closest(".extraBox").remove();
				if ($('#boxExtras').find('.extraBox').length == 0) {
					$('#boxExtras').html(myLabel.no_extras);
				}
				return false;
			}).on("click", ".btnDeleteExtra", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $parent = $(this).closest('.extraBox'),
					$id = $(this).attr('rel');
				swal({
					title: myLabel.alert_del_extra_title,
					text: myLabel.alert_del_extra_text,
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: myLabel.btn_delete,
					cancelButtonText: myLabel.btn_cancel,
					closeOnConfirm: false,
					showLoaderOnConfirm: true
				}, function () {
					$.post("index.php?controller=pjAdminProducts&action=pjActionDeleteExtra", {
						id: $id
					}).done(function (data) {
						if (data.status == 'OK') {
							$parent.remove();
							if ($('#boxExtras').find('.extraBox').length == 0) {
								$('#boxExtras').html(myLabel.no_extras);
							}
							swal.close();
						}
					});
				});
			}).on("click", ".btnCopyExtra", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				$('#modalCopyExtra').modal('show');
				return false;
			});

		$('#modalCopyAttr').on('shown.bs.modal', function (e) {
			var cOpt = {};
			if ($frmUpdateProduct.length > 0) {
				cOpt.product_id = $frmUpdateProduct.find("input[name='id']").val();
			}
			$.get("index.php?controller=pjAdminProducts&action=pjActionGetProducts&copy=Attr", cOpt).done(function (data) {
				$('#modalCopyAttr').find('.modal-body').html(data);
			});
		});

		$('#modalCopyExtra').on('shown.bs.modal', function (e) {
			var cOpt = {};
			if ($frmUpdateProduct.length > 0) {
				cOpt.product_id = $frmUpdateProduct.find("input[name='id']").val();
			}
			$.get("index.php?controller=pjAdminProducts&action=pjActionGetProducts&copy=Extra", cOpt).done(function (data) {
				$('#modalCopyExtra').find('.modal-body').html(data);
			});
		});

		function pjRefreshModelImageGrid() {
			var $field = $(".pj-model-image-field").first();
			if (!$field.length) {
				return;
			}
			var productId = $field.data("product-id") || $(":input[name='id']").val(),
				modelImageId = $field.find("input[name='model_image_id']").val() || 0;

			if (!productId) {
				return;
			}

			$.get("index.php?controller=pjAdminProducts&action=pjActionLoadModelImageGrid", {
				product_id: productId,
				model_image_id: modelImageId
			}).done(function (html) {
				$field.find(".pj-model-image-grid-wrap").html(html);
			});
		}

		function pjSelectModelImage(id) {
			var $field = $(".pj-model-image-field").first();
			if (!$field.length) {
				return;
			}
			$field.find("input[name='model_image_id']").val(id);
			pjRefreshModelImageGrid();
		}

		$('#modalImageStock').on('shown.bs.modal', function (e) {
			var $modal = $(this),
				$lnk = $modal.data("lnk"),
				$title = $modal.find(".modal-title");

			$title.text($title.data("default-title") || $title.text());

			$.get("index.php?controller=pjAdminProducts&action=pjActionLoadImages", {
				product_id: $(":input[name='id']").val(),
				image_id: $lnk.attr("rel"),
				picker: "stock"
			}).done(function (data) {
				$modal.find('.modal-body').html(data);
			});
		});

		$('#modalImageStock').on('show.bs.modal', function () {
			var $title = $(this).find(".modal-title");
			if (!$title.data("default-title")) {
				$title.data("default-title", $title.text());
			}
		});

		function getUrlParameter(sParam, sPageURL) {
			var sURLVariables = sPageURL.split('&');
			for (var i = 0; i < sURLVariables.length; i++) {
				var sParameterName = sURLVariables[i].split('=');
				if (sParameterName[0] == sParam) {
					return sParameterName[1];
				}
			}
		}

		function fireGroupSortable() {
			$("#boxAttributes").sortable({
				handle: '.group-move-icon',
				update: function () {
					var data = $(this).sortable('toArray');
					$('#orderAttributes').val(data.join("|"));
				}
			});
			var data = $("#boxAttributes").sortable('toArray');
			$('#orderAttributes').val(data.join("|"));
		}

		function fireItemSortable() {
			$('#frmUpdateProduct').find(".attrBoxRowStick").each(function (index, e) {
				var index = $(e).attr('data-id');
				$("#attrBoxRowStick_" + index).sortable({
					handle: '.item-move-icon',
					helper: 'clone',
					update: function () {
						var data = $("#attrBoxRowStick_" + index).sortable('toArray');
						$('#orderItems_' + index).val(data.join("|"));
					}
				});

				var data = $("#attrBoxRowStick_" + index).sortable('toArray');
				$('#orderItems_' + index).val(data.join("|"));
			});
		}

		function getAttributes() {
			var product_id, hash, obj = {};
			product_id = $frmProduct.find("input[name='id']").val();
			hash = $frmProduct.find("input[name='hash']").val();
			if (product_id !== undefined && product_id !== "") {
				obj.product_id = product_id;
			} else if (hash !== undefined && hash !== "") {
				obj.hash = hash;
			}

			$.get("index.php?controller=pjAdminProducts&action=pjActionGetAttributes", obj).done(function (data) {
				$("#boxAttributes").html(data);
				fireGroupSortable();
				fireItemSortable();
			});
		}

		function getExtras() {
			$.get("index.php?controller=pjAdminProducts&action=pjActionGetExtras", {
				"product_id": $frmProduct.find("input[name='id']").val()
			}).done(function (data) {
				$("#boxExtras").html(data);
			});
		}

		function handleDigitalInStock(callback) {
			var $tbody = $(".btnStockAdd").parent().siblings(".tblStocks").find("tbody");
			if ($("input[name='is_digital']").is(":checked") && $tbody.find("tr").length > 0) {
				$tbody.find("tr:gt(0)").remove();
			} else {
				if (callback !== undefined && typeof callback === "function") {
					callback.call(null);
				}
			}
		}
	});
})(jQuery);