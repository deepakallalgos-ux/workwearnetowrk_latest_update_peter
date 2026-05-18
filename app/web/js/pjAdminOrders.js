var jQuery = jQuery || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		
		var validator,
			$frmUpdateOrder = $("#frmUpdateOrder"),
			datagrid = ($.fn.datagrid !== undefined),
			dialog = ($.fn.dialog !== undefined),
			validate = ($.fn.validate !== undefined),
			select2 = ($.fn.select2 !== undefined),
			datepicker = ($.fn.datepicker !== undefined),
			validate = ($.fn.validate !== undefined),
			validator;

		var scStockObj = {},
			scStockIds = {},
			scQtyObj = {},
			scPriceObj = {},
			scPrice = 0.00,
			scPriceStocks = 0,
			scPriceExtras = 0,
			scAttrObj = {},
			$frmAddProduct = null;

		if (datepicker && myLabel.days !== undefined) {
			$.fn.datepicker.dates['en'] = {
	        	days: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
	        	daysMin: myLabel.days.split("_"),
	        	daysShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
	        	months: myLabel.months.split("_"),
	        	monthsShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
    		}
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
				relative_urls : false,
				remove_script_host : false,
				convert_urls : true,
				browser_spellcheck : true,
			    contextmenu: false,
			    selector: pSelector,
			    theme: "modern",
			    height: 400,
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
		
		if($('.frm-filter-advanced .date').length > 0 && datepicker)
        {
            $('.frm-filter-advanced .date').datepicker({
	            autoclose: true
	        }).on('changeDate', function (selected) {
	        	var $input = $(this).find('input'),
	        		elementName = $input.attr('name'),
	        		$form = $input.closest('form');
	        	if(elementName == 'date_from')
	        	{
	        		var $toElement = $('#date_to').parent(),
	        			date_to_value = $toElement.datepicker("getUTCDate"),
	        			$minDate = new Date(selected.date.valueOf());
	        		if(date_to_value < selected.date)
					{
    					$toElement.find('input').val($input.val());
					}
	        		$toElement.datepicker('setStartDate', $minDate);
	        	}else if(elementName == 'date_to'){
	        		var $fromElement = $("#date_from").parent(),
	        			$toElement = $('#date_to').parent();
	        		if($fromElement.length > 0 && $toElement.length > 0)
        			{
	        			var $maxDate = new Date(selected.date.valueOf()),
        					date_from_value = $fromElement.datepicker("getUTCDate");
        				if(date_from_value > selected.date)
    					{
        					$fromElement.find('input').val($input.val());
    					}
        				$fromElement.datepicker('setEndDate', $maxDate);
        			}
	        	}
			});
        }
		
		if ($(".select-item").length && select2) {
            $(".select-item").select2({
                placeholder: myLabel.choose,
                allowClear: true
            });
        }
		
		function getStockProducts(order_id) {
			$.get("index.php?controller=pjAdminOrders&action=pjActionStockGet", {
				"order_id": order_id
			}).done(function (data) {
				$("#boxStockProducts").html(data);
			});
		}
		
		function inObject(val, obj) {
			var key;
			for (key in obj) {
				if (obj.hasOwnProperty(key)) {
					if (obj[key] == val) {
						return true;
					}
				}
			}
			return false;
		}
		function compare(obj1, obj2) {
			var p;
			for (p in obj1) {
				if (obj2[p] === undefined) {
					return false;
				}
			}
			for (p in obj1) {
				if (obj1[p]) {
					switch (typeof(obj1[p])) {
						case 'object':
							if (!obj1[p].equals(obj2[p])) {
								return false;
							}
							break;
						case 'function':
							if (obj2[p] === undefined || (p != 'equals' && obj1[p].toString() != obj2[p].toString())) {
								return false;
							}
							break;
		              default:
		                  if (obj1[p] != obj2[p]) {
		                	  return false;
		                  }
					}
				} else {
					if (obj2[p])
					{
						return false;
					}
				}
			}

			for (p in obj2) {
				if (obj1[p] === undefined) {
					return false;
				}
			}

			return true;
		}
		function loopAttr(el)
		{
			var oid, valid, k, kCnt, j, jCnt, b, bCnt, pid, $select, $option,
				self = this,
				$el = $(el),
				row = $el.data("row"),
				id = $el.find("option:selected").val(),
				stocks = [];
			if (scStockObj !== undefined && scStockObj.length > 0) {
				for (k = 0, kCnt = scStockObj.length; k < kCnt; k++) {
					if (inObject.call(null, id, scStockObj[k])) {
						stocks.push(scStockObj[k]);
					}
				}
			}
			$frmAddProduct.find(".scSelectorAttr").each(function (i, select) {
				if (i > row) {
					$select = $(select);
					$select.empty();
					pid = $select.data("id");
					
					for (k = 0, kCnt = scAttrObj.length; k < kCnt; k++) {
						if (scAttrObj[k].id != pid) {
							continue;
						}
						for (j = 0, jCnt = scAttrObj[k].child.length; j < jCnt; j++) {
							for (b = 0, bCnt = stocks.length; b < bCnt; b++) {
								if (inObject.call(null, scAttrObj[k].child[j].id, stocks[b]) || (stocks[b][pid] && stocks[b][pid] == 0)) {
									$("<option>")
										.attr("value", scAttrObj[k].child[j].id)
										.text(scAttrObj[k].child[j].name)
										.appendTo($select);
									break;
								}
							}
						}
					}
				}
			});
		}
		function priceStock() {
			var m, $el, qs, i, iCnt, j, productObj = {}, $qty,
				$thumb, src, href,
				attr = $frmAddProduct.find(".scSelectorAttr").serializeArray();
			
			for (i = 0, iCnt = attr.length; i < iCnt; i++) {
				m = attr[i].name.match(/attr\[(\d+)\]/);
				productObj[m[1]] = attr[i].value; 
			}
			if (scStockObj != undefined && scStockObj.length > 0) {
				for (i = 0, iCnt = scStockObj.length; i < iCnt; i++) {
					if (compare.call(null, scStockObj[i], productObj)) {
						scPriceStocks = parseFloat(scPriceObj[i]);
						
						// Set qty attrs
						$qty = $frmAddProduct.find(":input[name='qty']");
						
						switch ($qty.get(0).nodeName) {
						case 'INPUT':
							$qty.val(1)
								.data("max", scQtyObj[i])
								.attr("data-max", scQtyObj[i])
								.attr("maxlength", scQtyObj[i].length);
							$qty.TouchSpin({
								verticalbuttons: true,
					            buttondown_class: 'btn btn-white',
					            buttonup_class: 'btn btn-white',
					            min: 0,
					            max: scQtyObj[i]
					        });
							$frmAddProduct.find(".scSelectorCurrentQty").html(scQtyObj[i]);
							break;
						case 'SELECT':
							$qty.empty();
							for (j = 1; j <= scQtyObj[i]; j++) {
								$("<option>")
									.attr("value", j)
									.text(j)
									.appendTo($qty);
							}
							break;
						}
						break;
					}
				}
				
				// Apply only if product have not attributes
				if (scStockObj.length === 0 && scPriceObj[0]) {
					scPriceStocks = parseFloat(scPriceObj[0]);
					console.log("console2", scPriceObj);
				}
			}
			setPrice.call(null);
			showPrice.call(null);
		}
		function priceExtra() {
			var $ele, $selected,
				price = 0;
			$frmAddProduct.find(".scSelectorExtra").each(function (i, ele) {
				$ele = $(ele);
				switch (ele.nodeName) {
					case 'INPUT':
						if ($ele.is(":checked")) {
							price += parseFloat($ele.data("price"));
						}
						break;
					case 'SELECT':
						$selected = $("option:selected", $ele);
						if ($selected) {
							price += parseFloat($selected.data("price"));
						}
						break;
				}
			});
			scPriceExtras = price;			
			
			setPrice.call(null);
			showPrice.call(null);
		}
		function formatCurrencySign(price)
		{
			var	format = '---';
			switch (myLabel.currency)
			{
				case 'USD':
					format = myLabel.currencysign + price;
					break;
				case 'GBP':
					format = myLabel.currencysign + price;
					break;
				case 'EUR':
					format = myLabel.currencysign + price;
					break;
				case 'JPY':
					format = myLabel.currencysign + price;
					break;
				case 'AUD':
				case 'CAD':
				case 'NZD':
				case 'CHF':
				case 'HKD':
				case 'SGD':
				case 'SEK':
				case 'DKK':
				case 'PLN':
					format = price + myLabel.currencysign;
					break;
				case 'NOK':
				case 'HUF':
				case 'CZK':
				case 'ILS':
				case 'MXN':
					format = myLabel.currencysign + price;
					break;
				default:
					format = price + myLabel.currencysign;
					break;
			}
			return format;
		}
		function setPrice() 
		{
			// scPrice = parseFloat(scPriceStocks).toFixed(2);
			scPrice = parseFloat(scPriceObj).toFixed(2);	
			$frmAddProduct.find("input[name='price']").val(scPrice);
		}
		function showPrice() 
		{
			$frmAddProduct.find(".scSelectorPrice").html(formatCurrencySign.call(null, scPrice));
		}
		function buildQueryString() 
		{
			var m, $el, qs, i, iCnt, productObj = {},
				attr = $frmAddProduct.find(".scSelectorAttr").serializeArray(),
				qs = $frmAddProduct.serialize();
			for (i = 0, iCnt = attr.length; i < iCnt; i++) {
				m = attr[i].name.match(/attr\[(\d+)\]/);
				productObj[m[1]] = attr[i].value; 
			}
			
			for (i = 0, iCnt = scStockObj.length; i < iCnt; i++) {
				if (compare.call(null, productObj, scStockObj[i])) {
					qs += "&stock_id=" + scStockIds[i];
					return qs;
					break;
				}
			}
			
			// Apply only if product have not attributes
			if (scStockObj.length === 0 && scStockIds[0]) {
				return [qs, "&stock_id=", scStockIds[0]].join("");
			}
			
			return false;
		}
		
		if ($frmUpdateOrder.length > 0) {
			$frmUpdateOrder.on("change", "#client_id", function () {
				var $pjScEditClient = $('#pjScEditClient');
				if($(this).val() != '')
				{
					var href = $pjScEditClient.attr('data-href');
					href = href.replace("{ID}", $(this).val());
					$pjScEditClient.attr('href', href);
					$pjScEditClient.css('display', 'inline-block');
				} else {
					$pjScEditClient.css('display', 'none');
				}
			});
			
			if (validate) {
				$frmUpdateOrder.validate({
					rules: {
						"uuid": {
							remote: "index.php?controller=pjAdminOrders&action=pjActionCheckUID&id=" + $frmUpdateOrder.find("input[name='id']").val()
						}
					},
					messages: {
						"uuid": {
							remote: myLabel.uuid_used
						}
					},
					invalidHandler: function (event, validator) {
					    if (validator.numberOfInvalids()) {
					    	var $_id = $(validator.errorList[0].element, this).closest("div.tab-pane").attr("id");
					    	$('.tab-'+$_id).trigger("click");
					    };
					}
				});
			}
			getStockProducts.call(null, $frmUpdateOrder.find("input[name='id']").val());
		}
		
		function formatTotal(val, obj) {
			return obj.total_formated;
		}
		
		function formatClient(val, obj) {
			return ['<a href="index.php?controller=pjAdminClients&action=pjActionUpdate&id=', obj.client_id, '">', obj.client_name, '</a>'].join("");
		}
		function formatUuid(val, obj) {
			return ['<a href="index.php?controller=pjAdminOrders&action=pjActionUpdate&id=', obj.id, '">', val, '</a>'].join("");
		}
		var orderStatusOptions = [];
		if (myLabel.statuses && typeof myLabel.statuses === "object") {
			$.each(myLabel.statuses, function (key, label) {
				orderStatusOptions.push({ value: key, label: label });
			});
		}
		if ($("#grid").length > 0 && datagrid) {
			var $buttons = [];
			var $actions = [];
			var $editable = false;
			var $select = false;
			if (myLabel.has_update) {
				$editable = true;
				$buttons.push({type: "edit", url: "index.php?controller=pjAdminOrders&action=pjActionUpdate&id={:id}"});
			}
			if (myLabel.has_delete) {
				$buttons.push({type: "delete", url: "index.php?controller=pjAdminOrders&action=pjActionDeleteOrder&id={:id}"});
			}
			if (myLabel.has_delete_bulk) {
				$actions.push({text: myLabel.delete_selected, url: "index.php?controller=pjAdminOrders&action=pjActionDeleteOrderBulk", render: true, confirmation: myLabel.delete_confirmation});
			}
			$actions.push({text: myLabel.exported, url: "index.php?controller=pjAdminOrders&action=pjActionExportOrder", ajax: false});
			if ($actions.length > 0) {
				$select = {
						field: "id",
						name: "record[]",
						cellClass: 'cell-width-2'
					};
			}
			var $grid = $("#grid").datagrid({
				buttons: $buttons,
				columns: [{text: myLabel.uuid, type: "text", sortable: true, editable: false, renderer: formatUuid, cellClass: 'col-orders-uuid'},
				          {text: myLabel.client, type: "text", sortable: true, editable: false, renderer: formatClient, cellClass: 'col-orders-client'},
				          {text: myLabel.created, type: "text", sortable: true, editable: false, cellClass: 'col-orders-date'},
				          {text: myLabel.total, type: "text", sortable: true, renderer: formatTotal, cellClass: 'col-orders-total'},
				          {text: myLabel.status, type: "select", sortable: true, editable: !!myLabel.has_update, options: orderStatusOptions, applyClass: "pj-status", cellClass: 'col-orders-status'}
				       ],
				dataUrl: "index.php?controller=pjAdminOrders&action=pjActionGetOrder" + pjGrid.queryString,
				dataType: "json",
				fields: ['uuid', 'client_name', 'created', 'total', 'status'],
				paginator: {
					actions: $actions,
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminOrders&action=pjActionSaveOrder&id={:id}",
				select: $select
			});			
		}
		
		if ($("#grid_client_orders").length > 0 && datagrid) {
			var $buttons = [];
			var $actions = [];
			var $editable = true;
			var $select = false;
			$buttons.push({type: "edit", url: "index.php?controller=pjAdminOrders&action=pjActionUpdate&id={:id}"});
			if (myLabel.has_delete) {
				$buttons.push({type: "delete", url: "index.php?controller=pjAdminOrders&action=pjActionDeleteOrder&id={:id}"});
			}
			if (myLabel.has_delete_bulk) {
				$actions.push({text: myLabel.delete_selected, url: "index.php?controller=pjAdminOrders&action=pjActionDeleteOrderBulk", render: true, confirmation: myLabel.delete_confirmation});
			}
			$actions.push({text: myLabel.exported, url: "index.php?controller=pjAdminOrders&action=pjActionExportOrder", ajax: false});
			if ($actions.length > 0) {
				$select = {
						field: "id",
						name: "record[]",
						cellClass: 'cell-width-2'
					};
			}
			var $grid_client_orders = $("#grid_client_orders").datagrid({
				buttons: $buttons,
				columns: [{text: myLabel.uuid, type: "text", sortable: true, editable: false, renderer: formatUuid, cellClass: 'col-orders-uuid'},
				          {text: myLabel.created, type: "text", sortable: true, editable: false, cellClass: 'col-orders-date'},
				          {text: myLabel.total, type: "text", sortable: true, renderer: formatTotal, cellClass: 'col-orders-total'},
				          {text: myLabel.status, type: "select", sortable: true, editable: !!myLabel.has_update, options: orderStatusOptions, applyClass: "pj-status", cellClass: 'col-orders-status'}
				       ],
				dataUrl: "index.php?controller=pjAdminOrders&action=pjActionGetOrder&is_client_orders=1&client_id=" + $("#client_id").find("option:selected").val() + "&order_id=" + $("input[name='id']").val(),
				dataType: "json",
				fields: ['uuid', 'created', 'total', 'status'],
				paginator: {
					actions: $actions,
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminOrders&action=pjActionSaveOrder&id={:id}",
				select: $select
			});
		}

		$(document).on("change", "#grid select.pj-selector-editable, #grid_client_orders select.pj-selector-editable", function () {
			if ($(this).data("name") === "status") {
				$(this).trigger("save");
			}
		});

		$(document).on("change", "#client_id", function () {
			var client_id = $(this).find("option:selected").val();
			if (parseInt(client_id, 10) > 0) {
				$.get("index.php?controller=pjAdminOrders&action=pjActionGetClient", {
					"client_id": client_id
				}).done(function (data) {
					$("#boxClient").html(data);
				});
				$.get("index.php?controller=pjAdminOrders&action=pjActionGetAddressBook", {
					"client_id": client_id,
					"order_id": $("input[name='id']").val()
				}).done(function (data) {
					$("#boxAddressBook").html(data);
				});
			} else {
				$("#boxClient").html("");
			}
			
			var content = $grid_client_orders.datagrid("option", "content"),
				cache = $grid_client_orders.datagrid("option", "cache"),
				obj = {};
			obj.is_client_orders = 1;
			obj.order_id = $("input[name='id']").val();
			obj.client_id = parseInt(client_id, 10) > 0 ? client_id : 0;
			
			$.extend(cache, obj);
			$grid_client_orders.datagrid("option", "cache", cache);
			$grid_client_orders.datagrid("load", "index.php?controller=pjAdminOrders&action=pjActionGetOrder", "id", "DESC", content.page, content.rowCount);
			
		}).on("change", "#address_id", function () {
			if (parseInt($("option:selected", this).val(), 10) > 0) {
				$(".btnCopy").prop("disabled", false);
				$.get("index.php?controller=pjAdminOrders&action=pjActionGetAddress", {
					"id": $("option:selected", this).val()
				}).done(function (data) {
					$("#boxAddress").html(data);
				});
			} else {
				$(".btnCopy").prop("disabled", true);
				$("#boxAddress").html("");
			}
		}).on("click", ".btnCopyShipping", function () {
			
			$.get("index.php?controller=pjAdminOrders&action=pjActionGetAddress", {
				"id": $("option:selected", $("#address_id")).val(),
				"json": 1
			}).done(function (data) {
				$("#s_name").val(data.name);
				$("#s_country_id").val(data.country_id).trigger('change');
				$("#s_state").val(data.state);
				$("#s_city").val(data.city);
				$("#s_zip").val(data.zip);
				$("#s_address_1").val(data.address_1);
				$("#s_address_2").val(data.address_2);
				$("#same_as").prop("checked", false);
				$(".boxSame").show();
			});
			
		}).on("click", ".btnCopyBilling", function () {
			
			$.get("index.php?controller=pjAdminOrders&action=pjActionGetAddress", {
				"id": $("option:selected", $("#address_id")).val(),
				"json": 1
			}).done(function (data) {
				$("#b_name").val(data.name);
				$("#b_country_id").val(data.country_id).trigger('change');
				$("#b_state").val(data.state);
				$("#b_city").val(data.city);
				$("#b_zip").val(data.zip);
				$("#b_address_1").val(data.address_1);
				$("#b_address_2").val(data.address_2);
			});
			
		}).on("change", "#same_as", function () {
			if ($(this).is(":checked")) {
				$(".boxSame").hide();
			} else {
				$(".boxSame").show();
			}
		}).on("click", ".stock-edit", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $order_stock_id = $(this).attr('data-id');
			var $stockEditContentWrapper = $('#stockEditContentWrapper');	
			$stockEditContentWrapper.html("");
			$.get("index.php?controller=pjAdminOrders&action=pjActionStockEdit", {
				"order_stock_id": $order_stock_id
			}).done(function (data) {
				$stockEditContentWrapper.html(data);
				$('#stockEditModal').modal('show');
				if ($('#stockEditModal').find(".stock-product").length && select2) {
					$('#stockEditModal').find(".stock-product").select2({
						placeholder: myLabel.product_choose,
		                allowClear: true,
		                dropdownParent: $("#stockEditModal")
		            });
		        }
				$stockEditContentWrapper.find("input[name='qty']").each(function (i) {
					var $this = $(this);
					$this.TouchSpin({
						verticalbuttons: true,
			            buttondown_class: 'btn btn-white',
			            buttonup_class: 'btn btn-white',
			            min: 1,
			            max: $this.data("max")
			        });
				});
			});
			return false;
		}).on("click", ".stock-add", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $order_id = $(this).attr('data-id');
			var $stockAddContentWrapper = $('#stockAddContentWrapper');			
			$('#btnStockAddConfirm').attr('data-id', $order_id);			
			$stockAddContentWrapper.html("");
			$.get("index.php?controller=pjAdminOrders&action=pjActionStockAdd", {
				"order_id": $order_id
			}).done(function (data) {
				$stockAddContentWrapper.html(data);
				$('#stockAddModal').modal('show');
				if ($('#stockAddModal').find(".stock-product").length && select2) {
					$('#stockAddModal').find(".stock-product").select2({
						placeholder: myLabel.product_choose,
		                allowClear: true,
		                dropdownParent: $("#stockAddModal")
		            });
		        }
			});
			return false;
		}).on("click", ".stock-delete", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var id = $(this).attr('data-id');
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
				$.post("index.php?controller=pjAdminOrders&action=pjActionStockDelete", {id: id}).done(function (data) {
					swal.close();
					getStockProducts.call(null, $frmUpdateOrder.find("input[name='id']").val());
				});
			});
		}).on("click", ".order-calc", function () {
			var $this = $(this),
				$form = $this.closest("form");
			$.post("index.php?controller=pjAdminOrders&action=pjActionGetPrice", $form.serialize()).done(function (data) {
				if (data.status == 'OK') {
					$form.find("#price").val(data.data.price.toFixed(2));
					$form.find("#discount").val(data.data.discount.toFixed(2));
					$form.find("#insurance").val(data.data.insurance.toFixed(2));
					$form.find("#shipping").val(data.data.shipping.toFixed(2));
					$form.find("#tax").val(data.data.tax.toFixed(2));
					$form.find("#total").val(data.data.total.toFixed(2));
				}
			});
		}).on("focusin", ".datepick", function (e) {
			var $this = $(this);
			$this.datepicker({
				firstDay: $this.attr("rel"),
				dateFormat: $this.attr("rev"),
				onClose: function (selectedDate) {
					var name = $this.attr("name");
					if (name == "date_from") {
						$this.closest("p").find(".datepick[name='date_to']").datepicker("option", "minDate", selectedDate);
					} else if (name == "date_to") {
						$this.closest("p").find(".datepick[name='date_from']").datepicker("option", "maxDate", selectedDate);
					}
				}
			});
		}).on("click", "#btnEmailConfirmation", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $id = $(this).attr('data-id'),
				$confirmEmailContentWrapper = $('#confirmEmailContentWrapper');			
			$('#btnSendEmailConfirm').attr('data-id', $id);
			
			$confirmEmailContentWrapper.html("");
			$.get("index.php?controller=pjAdminOrders&action=pjActionConfirmationEmail", {
				"id": $id
			}).done(function (data) {
				$confirmEmailContentWrapper.html(data);
				if(data.indexOf("pjResendAlert") == -1)
				{
					if ($('#mceEditor').length > 0) {
						myTinyMceDestroy.call(null);
						myTinyMceInit.call(null, 'textarea#mceEditor');
			        }					
					validator = $confirmEmailContentWrapper.find("form").validate({});
					$('#btnSendEmailConfirm').show();
				}else{
					$('#btnSendEmailConfirm').hide();
				}	
				$('#confirmEmailModal').modal('show');
			});
			return false;
		}).on("click", "#btnSendEmailConfirm", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this);
			var $confirmEmailContentWrapper = $('#confirmEmailContentWrapper');
			if (validator.form()) {
				$('#mceEditor').html( tinymce.get('mceEditor').getContent() );
				$(this).attr("disabled", true);
				var l = Ladda.create(this);
			 	l.start();
				$.post("index.php?controller=pjAdminOrders&action=pjActionConfirmationEmail", $confirmEmailContentWrapper.find("form").serialize()).done(function (data) {
					if (data.status == "OK") {
						$('#confirmEmailModal').modal('hide');
					} else {
						$('#confirmEmailModal').modal('hide');
					}
					$this.attr("disabled", false);
					l.stop();
				});
			}
			return false;
		}).on("click", "#btnEmailPayment", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $id = $(this).attr('data-id'),
				$paymentEmailContentWrapper = $('#paymentEmailContentWrapper');			
			$('#btnSendEmailPayment').attr('data-id', $id);
			
			$paymentEmailContentWrapper.html("");
			$.get("index.php?controller=pjAdminOrders&action=pjActionPaymentEmail", {
				"id": $id
			}).done(function (data) {
				$paymentEmailContentWrapper.html(data);
				if(data.indexOf("pjResendAlert") == -1)
				{
					if ($('#mceEditor').length > 0) {
						myTinyMceDestroy.call(null);
						myTinyMceInit.call(null, 'textarea#mceEditor');
			        }					
					validator = $paymentEmailContentWrapper.find("form").validate({});
					$('#btnSendEmailPayment').show();
				}else{
					$('#btnSendEmailPayment').hide();
				}	
				$('#paymentEmailModal').modal('show');
			});
			return false;
		}).on("click", "#btnSendEmailPayment", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this);
			var $paymentEmailContentWrapper = $('#paymentEmailContentWrapper');
			if (validator.form()) {
				$('#mceEditor').html( tinymce.get('mceEditor').getContent() );
				$(this).attr("disabled", true);
				var l = Ladda.create(this);
			 	l.start();
				$.post("index.php?controller=pjAdminOrders&action=pjActionPaymentEmail", $paymentEmailContentWrapper.find("form").serialize()).done(function (data) {
					if (data.status == "OK") {
						$('#paymentEmailModal').modal('hide');
					} else {
						$('#paymentEmailModal').modal('hide');
					}
					$this.attr("disabled", false);
					l.stop();
				});
			}
			return false;
		}).on("change", "#payment_method", function () {
			if ($(this).find("option:selected").val() == 'creditcard') {
				$(".sscCC").show();
			} else {
				$(".sscCC").hide();
			}
		});
		
		$("#stockAddModal").on("hidden.bs.modal", function () {
			$('#stockAddModal').find(".stock-product").select2('destroy');
        });
		
		$(document).on("change", ".stock-product", function () {
			var $this = $(this);
			$.get("index.php?controller=pjAdminOrders&action=pjActionGetStocks", {
				"id": $this.find("option:selected").val()
			}).done(function (data) {
				scStockIds = data.stock_ids;
				scStockObj = data.stocks;
				scQtyObj = data.qty;
				scPriceObj = data.price;
				scAttrObj = data.attributes;
				$.get("index.php?controller=pjAdminOrders&action=pjActionStockGetByProduct", {
					"product_id": $this.find("option:selected").val()
				}).done(function (data) {
					var $wrapper = $this.closest("form").find(".stock-products");
					console.log("console1");
					$wrapper.html(data);
					$wrapper.find("input[name='qty']").each(function (i) {
						var $this = $(this);
						$this.TouchSpin({
							verticalbuttons: true,
				            buttondown_class: 'btn btn-white',
				            buttonup_class: 'btn btn-white',
				            min: 0,
				            max: $this.data("max")
				        });
					});
					$frmAddProduct = $this.closest("form");
					loopAttr.call(null, $frmAddProduct.find(".scSelectorAttr:first").get(0));
					priceStock.call(null);
					priceExtra.call(null);
				});
			});
		}).on("change", ".scSelectorExtra", function (e) {
			priceExtra.call(null);
		}).on("change", ".scSelectorAttr", function (e) {
			loopAttr.call(null, this);
			priceStock.call(null);
		}).on("submit", ".frm-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				q: $this.find("input[name='q']").val(),
				status: $this.find("select[name='status']").val()
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminOrders&action=pjActionGetOrder", "id", "DESC", content.page, content.rowCount);
			return false;
			
		}).on("change", "#filter_status", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(this).closest('form').trigger('submit');
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
			cache.q = "";
			cache.status = "";
			$.extend(cache, obj);
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminOrders&action=pjActionGetOrder", "id", "DESC", content.page, content.rowCount);
			return false;
		}).on("reset", ".frm-filter-advanced", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(".btn-advance-search").trigger("click");
			$('#q').val('');
			$('#product_id').val('').trigger('change');
			$('#status').val('');
			$('#payment_method').val('');
			$('#date_from').val('');
			$('#date_to').val('');
			$('#total_from').val('');
			$('#total_to').val('');
			var obj = {},
				$this = $(this),
				arr = $this.serializeArray(),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			for (var i = 0, iCnt = arr.length; i < iCnt; i++) {
				obj[arr[i].name] = arr[i].value;
			}
			cache.q = "";
			cache.status = "";
			$.extend(cache, obj);
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminOrders&action=pjActionGetOrder", "id", "DESC", content.page, content.rowCount);
			return false;
		}).on("click", "#btnStockAddConfirm", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var qs = buildQueryString.call(null);
			if (!qs) {
				log("Stock Id not set");
				return;
			}
			$.post("index.php?controller=pjAdminOrders&action=pjActionStockAdd", qs).done(function (data) {
				getStockProducts.call(null, $frmUpdateOrder.find("input[name='id']").val());
				$('#stockAddModal').modal('hide');
			});
		}).on("click", "#btnStockEditConfirm", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this);
			$.post("index.php?controller=pjAdminOrders&action=pjActionStockEdit", $('#stockEditModal').find("form").serialize()).done(function (data) {
				getStockProducts.call(null, $frmUpdateOrder.find("input[name='id']").val());
				$('#stockEditModal').modal('hide');
			});
		});
	});
})(jQuery);