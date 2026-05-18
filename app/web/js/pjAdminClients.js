var jQuery = jQuery || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		
		var $frmCreateClient = $("#frmCreateClient"),
			$frmUpdateClient = $("#frmUpdateClient"),
			datagrid = ($.fn.datagrid !== undefined),
			validate = ($.fn.validate !== undefined),
			select2 = ($.fn.select2 !== undefined);
		
		if ($(".select-item").length && select2) {
            $(".select-item").select2({
                placeholder: myLabel.choose,
                allowClear: true
            });
        }
		
		function formatOrders(val, obj) {
			if(val != '0')
			{
				return ['<a href="index.php?controller=pjAdminOrders&action=pjActionIndex&client_id=', obj.id, '">', val, '</a>'].join("");
			}else{
				return '0';
			}
		}
		if ($("#grid").length > 0 && datagrid) {
			var $buttons = [];
			var $actions = [];
			var $editable = false;
			var $select = false;
			if (myLabel.has_update) {
				$editable = true;
				$buttons.push({type: "edit", url: "index.php?controller=pjAdminClients&action=pjActionUpdate&id={:id}"});
			}
			if (myLabel.has_delete) {
				$buttons.push({type: "delete", url: "index.php?controller=pjAdminClients&action=pjActionDeleteClient&id={:id}"});
			}
			if (myLabel.has_delete_bulk) {
				$actions.push({text: myLabel.delete_selected, url: "index.php?controller=pjAdminClients&action=pjActionDeleteClientBulk", render: true, confirmation: myLabel.delete_confirmation});
			}
			if (myLabel.has_export) {
				$actions.push({text: myLabel.exported, url: "index.php?controller=pjAdminClients&action=pjActionExportClient", ajax: false});
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
				columns: [{text: myLabel.name, type: "text", sortable: true, editable: true},
				          {text: myLabel.email, type: "text", sortable: true, editable: true},
				          {text: myLabel.last_order, type: "date", sortable: true, editable: false},
				          {text: myLabel.orders, type: "text", sortable: true, renderer: formatOrders, align: 'center'},
				          {text: myLabel.status, type: "toggle", sortable: true, editable: $editable, positiveClass: "pj-toggle-on", negativeClass: "pj-toggle-off", positiveLabel: myLabel.active, positiveValue: "T", negativeLabel: myLabel.inactive, negativeValue: "F"}
				       ],
				dataUrl: "index.php?controller=pjAdminClients&action=pjActionGetClient" + pjGrid.queryString,
				dataType: "json",
				fields: ['client_name', 'email', 'last_order', 'cnt_orders', 'status'],
				paginator: {
					actions: $actions,
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminClients&action=pjActionSaveClient&id={:id}",
				select: $select
			});
		}

		$(document).on("click", ".btn-all", function (e) {
			$(this).addClass("btn-primary active").removeClass("btn-default")
				.siblings(".btn").removeClass("btn-primary active").addClass("btn-default");
			var content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				status: "",
				q: ""
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminClients&action=pjActionGetClient", "client_name", "ASC", content.page, content.rowCount);
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
			obj.status = $this.data("value");
			$.extend(cache, obj);
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminClients&action=pjActionGetClient", "client_name", "ASC", content.page, content.rowCount);
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
			$grid.datagrid("load", "index.php?controller=pjAdminClients&action=pjActionGetClient", "client_name", "ASC", content.page, content.rowCount);
			return false;
		}).on("click", ".btnAddAddress", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $clone = $("#boxCloneAddress").clone(),
				index = 'new_' + Math.ceil(Math.random() * 99999);
			$('#pjScAddressBookList').append($clone.html().replace(/\{INDEX\}/g, index));
			if ($("select[name='country_id["+index+"]']").length && select2) {
				$("select[name='country_id["+index+"]']").select2({
	                placeholder: myLabel.choose,
	                allowClear: true
	            });
	        }
			return false;
		}).on("click", ".btnRemoveAddress", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(this).closest('.boxAddress').remove();
			return false;
		}).on("click", ".btnDeleteAddress", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				$id = $this.attr('data-id'),
				$client_id = $this.attr('data-client_id');
			swal({
				title: myLabel.alert_del_da_title,
				text: myLabel.alert_del_da_text,
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: myLabel.btn_delete,
				cancelButtonText: myLabel.btn_cancel,
				closeOnConfirm: false,
				showLoaderOnConfirm: true
			}, function () {
				$.post("index.php?controller=pjAdminClients&action=pjActionDeleteAddress", {id: $id}).done(function (data) {
					$this.closest('.boxAddress').remove();
					swal.close();
				});
			});
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
			$grid.datagrid("load", "index.php?controller=pjAdminClients&action=pjActionGetClient", "id", "ASC", content.page, content.rowCount);
			return false;
		});
		
		if ($frmCreateClient.length > 0 && validate) {
			$frmCreateClient.validate({
				rules: {
					"email": {
						required: true,
						email: true,
						remote: "index.php?controller=pjAdminClients&action=pjActionCheckEmail"
					}
				},
				messages: {
					"email": {
						remote: myLabel.email_exists
					}
				}
			});
			
			$(".btnAddAddress").trigger("click");
			$(".form").find("input[name='is_default_shipping']").prop("checked", true);
			$(".form").find("input[name='is_default_billing']").prop("checked", true);
		}
		
		if ($frmUpdateClient.length > 0 && validate) {
			$frmUpdateClient.validate({
				rules: {
					"email": {
						required: true,
						email: true,
						remote: "index.php?controller=pjAdminClients&action=pjActionCheckEmail&id=" + $frmUpdateClient.find("input[name='id']").val()
					}
				},
				messages: {
					"email": {
						remote: myLabel.email_exists
					}
				}
			});
		}
	});
})(jQuery);