var jQuery = jQuery || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var $frmCreateBrand = $("#frmCreateBrand"),
			$frmUpdateBrand = $("#frmUpdateBrand"),
			$pjScFormWrapper = $("#pjScFormWrapper"),
			multilang = ($.fn.multilang !== undefined),
			dialog = ($.fn.dialog !== undefined),
			validate = ($.fn.validate !== undefined),
			datagrid = ($.fn.datagrid !== undefined),
			locale_id = myLabel.localeId,
			$tr = null;
		
		if (multilang && 'pjCmsLocale' in window) {
			$(".multilang").multilang({
				langs: pjCmsLocale.langs,
				flagPath: pjCmsLocale.flagPath,
				tooltip: "",
				select: function (event, ui) {
					locale_id = ui.index;
				}
			});
		}
		function getCreateForm()
		{
			$.get("index.php?controller=pjAdminBrands&action=pjActionCreateForm").done(function (data) {
				$pjScFormWrapper.html(data);
				bindCreateForm();
			});
		}
		if ($pjScFormWrapper.length > 0) 
		{
			getCreateForm();
		}
		function highlightLanguage()
		{
			$(".pj-form-langbar-item").removeClass('btn-primary');
			$(".pj-form-langbar-item").each(function( index ) {
				if($(this).attr('data-index') == myLabel.localeId)
				{
					$(this).addClass('btn-primary');
				}
			});
			$(".pj-multilang-wrap").each(function( index ) {
				if($(this).attr('data-index') == myLabel.localeId)
				{
					$(this).css('display','block');
				}else{
					$(this).css('display','none');
				}
			});
		}
		function bindCreateForm()
		{
			$frmCreateBrand = $("#frmCreateBrand");
			if ($frmCreateBrand.length > 0 && validate) {
				$frmCreateBrand.validate({
					invalidHandler: function (event, validator) {
					    $(".pj-multilang-wrap").each(function( index ) {
							if($(this).attr('data-index') == myLabel.localeId)
							{
								locale_id = myLabel.localeId;
								$(this).css('display','block');
							}else{
								$(this).css('display','none');
							}
						});
						$(".pj-form-langbar-item").each(function( index ) {
							if($(this).attr('data-index') == myLabel.localeId)
							{
								locale_id = myLabel.localeId;
								$(this).addClass('btn-primary');
							}else{
								$(this).removeClass('btn-primary');
							}
						});
					},
					ignore: "",
					submitHandler: function(form){
						var ladda_buttons = $(form).find('.ladda-button');
					    if(ladda_buttons.length > 0)
	                    {
	                        var l = ladda_buttons.ladda();
	                        l.ladda('start');
	                    }
					    $.post("index.php?controller=pjAdminBrands&action=pjActionCreate", $(form).serialize()).done(function (data) {
					    	l.ladda('stop');
					    	if(data.status == 'OK')
					    	{
					    		getCreateForm();
					    		var content = $grid.datagrid("option", "content"),
									cache = $grid.datagrid("option", "cache");
								$.extend(cache, {
									status: "",
									q: ""
								});
								$grid.datagrid("option", "cache", cache);
								$grid.datagrid("load", "index.php?controller=pjAdminBrands&action=pjActionGetBrand", "order", "ASC", content.page, content.rowCount);
					    	}else if(data.code == '104'){
					    		swal({
					    			title: "",
									text: data.text,
									type: "warning",
									confirmButtonColor: "#DD6B55",
									confirmButtonText: "OK",
									closeOnConfirm: false,
									showLoaderOnConfirm: false
								}, function () {
									swal.close();
								});
					    	}
						});
						return false;
					}
				});
				highlightLanguage();
			}
		}
		function bindUpdateForm()
		{
			$frmUpdateBrand = $("#frmUpdateBrand");
			if ($frmUpdateBrand.length > 0 && validate) {
				$frmUpdateBrand.validate({
					invalidHandler: function (event, validator) {
					    $(".pj-multilang-wrap").each(function( index ) {
							if($(this).attr('data-index') == myLabel.localeId)
							{
								locale_id = myLabel.localeId;
								$(this).css('display','block');
							}else{
								$(this).css('display','none');
							}
						});
						$(".pj-form-langbar-item").each(function( index ) {
							if($(this).attr('data-index') == myLabel.localeId)
							{
								locale_id = myLabel.localeId;
								$(this).addClass('btn-primary');
							}else{
								$(this).removeClass('btn-primary');
							}
						});
					},
					ignore: "",
					submitHandler: function(form){
						var ladda_buttons = $(form).find('.ladda-button');
					    if(ladda_buttons.length > 0)
	                    {
	                        var l = ladda_buttons.ladda();
	                        l.ladda('start');
	                    }
					    $.post("index.php?controller=pjAdminBrands&action=pjActionUpdate", $(form).serialize()).done(function (data) {
					    	l.ladda('stop');
					    	if(data.status == 'OK')
					    	{
					    		getCreateForm();
					    		var content = $grid.datagrid("option", "content"),
									cache = $grid.datagrid("option", "cache");
								$.extend(cache, {
									status: "",
									q: ""
								});
								$grid.datagrid("option", "cache", cache);
								$grid.datagrid("load", "index.php?controller=pjAdminBrands&action=pjActionGetBrand", "order", "ASC", content.page, content.rowCount);
					    	}else if(data.code == '105'){
					    		swal({
					    			title: "",
									text: data.text,
									type: "warning",
									confirmButtonColor: "#DD6B55",
									confirmButtonText: "OK",
									closeOnConfirm: false,
									showLoaderOnConfirm: false
								}, function () {
									swal.close();
								});
					    	}
						});
						return false;
					}
				});
				highlightLanguage();
			}
		}
		
		function formatName(val, obj) {
			return Array(obj.deep+1).join('-------') + " " + val.name;
		}
		function formatDown(val, obj) {
			return (obj.down === 1) ? ['<a href="index.php?controller=pjAdminBrands" class="arrow_down" rev="down" rel="', val.id , '" title="', myLabel.down, '"><i class="fa fa-arrow-circle-down"></i></a>'].join("") : '';
		}
		function formatUp(val, obj) {
			return (obj.up === 1) ? ['<a href="index.php?controller=pjAdminBrands" class="arrow_up" rev="up" rel="', val.id , '" title="', myLabel.up, '"><i class="fa fa-arrow-circle-up"></i></a>'].join("") : '';
		}
		
		if ($("#grid").length > 0 && datagrid) {
			var $buttons = [];
			var $actions = [];
			var $editable = false;
			var $select = false;
			if (myLabel.has_update) {
				$editable = true;
				$buttons.push({type: "edit", url: "index.php?controller=pjAdminBrands&action=pjActionUpdateForm&id={:id}"});
			}
			if (myLabel.has_delete) {
				$buttons.push({type: "delete", url: "index.php?controller=pjAdminBrands&action=pjActionDeleteBrand&id={:id}"});
			}
			if (myLabel.has_delete_bulk) {
				$actions.push({text: myLabel.delete_selected, url: "index.php?controller=pjAdminBrands&action=pjActionDeleteBrandBulk", render: true, confirmation: myLabel.delete_confirmation});
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
				columns: [{text: myLabel.brand_name, type: "text", sortable: false, editable: false, renderer: formatName},
				          {text: myLabel.products, type: "text", sortable: false, editable: false},
				          {text: "", type: "text", sortable: false, editable: false, renderer: formatDown, align: "left"},
				          {text: "", type: "text", sortable: false, editable: false, renderer: formatUp, align: "right"}
				          ],
				dataUrl: "index.php?controller=pjAdminBrands&action=pjActionGetBrand",
				dataType: "json",
				fields: ['data', 'products', 'data', 'data'],
				paginator: {
					actions: $actions,
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminBrands&action=pjActionSaveBrand&id={:id}",
				select: $select,
				onRender: function(){
					if ($pjScFormWrapper.length > 0) 
					{
						getCreateForm();
					}
				}
			});
			
			$grid.on("click", ".arrow_up, .arrow_down", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				$.post("index.php?controller=pjAdminBrands&action=pjActionSetOrder", {
					"id": $(this).attr("rel"),
					"direction": $(this).attr("rev")
				}).done(function (data) {
					var content = $grid.datagrid("option", "content");
					$grid.datagrid("load", "index.php?controller=pjAdminBrands&action=pjActionGetBrand", "id", "ASC", content.page, content.rowCount);
				});
				return false;
			});
		}
		
		if(myLabel.trigger_create == 1)
		{
			getCreateForm();
		}
		$(document).on("click", ".pjScAddBrand", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			getCreateForm();
			if($tr != null)
			{
				$tr.find('.pj-table-icon-edit').show();
				$tr.find('.pj-table-icon-delete').show();
				$tr = null;
			}
		}).on("click", ".pjScBtnCancel", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			getCreateForm();
			if($tr != null)
			{
				$tr.find('.pj-table-icon-edit').show();
				$tr.find('.pj-table-icon-delete').show();
				$tr = null;
			}
		}).on("click", ".pj-table-icon-edit", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this);
			var href = $this.attr('href');
			$.get(href).done(function (data) {
				$pjScFormWrapper.html(data);
				bindUpdateForm();
				if($tr != null)
				{
					$tr.find('.pj-table-icon-edit').show();
					$tr.find('.pj-table-icon-delete').show();
					$tr = null;
				}
				$tr = $this.closest("tr");
				$tr.find('.pj-table-icon-edit').hide();
				$tr.find('.pj-table-icon-delete').hide();
			});
		});
	});
})(jQuery);