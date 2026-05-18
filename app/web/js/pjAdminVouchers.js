var jQuery = jQuery || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var $frmCreateVoucher = $("#frmCreateVoucher"),
			$frmUpdateVoucher = $("#frmUpdateVoucher"),
			datepicker = ($.fn.datepicker !== undefined),
			datagrid = ($.fn.datagrid !== undefined);

		if($('.date').length > 0 && datepicker)
        {
			var $optionsEle = $('#dateTimePickerOptions');
            if($optionsEle.length > 0)
            {
	            $.fn.datepicker.dates['en'] = {
	        	    days: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
	        	    daysShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
	        	    daysMin: $optionsEle.data('days').split("_"),
	        	    months: $optionsEle.data('months').split("_"),
	        	    monthsShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
	        	    today: "Today",
	        	    clear: "Clear",
	        	    format: "mm/dd/yyyy",
	        	    titleFormat: "MM yyyy", 
	        	    weekStart: parseInt($optionsEle.data('wstart'), 10)
	        	};
            }
            
            $('.date').datepicker({
            	startDate:'0d'
            });
        }
		
		if ($(".select-item").length) {
            $(".select-item").select2({
                allowClear: true
            });
        };

		$.validator.addMethod('validateFixedTime', function (value) {
		    if($("#valid").val() != 'fixed')
            {
                return true;
            }

            var start_hour = parseInt($('#f_hour_from').val(), 10),
	     		start_min =  parseInt($('#f_minute_from').val(), 10),
	     		end_hour =  parseInt($('#f_hour_to').val(), 10),
	     		end_min =  parseInt($('#f_minute_to').val(), 10);
			if($('#f_ampm_from').length > 0)
			{
				if($('#f_ampm_from').val() == $('#f_ampm_to').val())
				{
					if(($('#f_ampm_from').val() == 'am' || $('#f_ampm_from').val() == 'AM' || $('#f_ampm_from').val() == 'pm' || $('#f_ampm_from').val() == 'PM') && start_hour == 12)
					{
						start_hour = 0;
					}
					if(($('#f_ampm_to').val() == 'am' || $('#f_ampm_to').val() == 'AM' || $('#f_ampm_to').val() == 'pm' || $('#f_ampm_to').val() == 'PM') && end_hour == 12)
					{
						end_hour = 0;
					}
					if(end_hour < start_hour)
					{
						return false;
					}else if(end_hour == start_hour){
						if(end_min <= start_min)
						{
							return false;
						}else{
							return true;
						}
					}else{
						return true;
					}
				}else if(($('#f_ampm_from').val() == 'am' || $('#f_ampm_from').val() == 'AM') && ($('#f_ampm_to').val() == 'pm' || $('#f_ampm_to').val() == 'PM')){
					return true;
				}else{
					return false;
				}
			}else{
				if(end_hour < start_hour)
				{
					return false;
				}else if(end_hour == start_hour){
					if(end_min <= start_min)
					{
						return false;
					}else{
						return true;
					}
				}else{
					return true;
				}
			}
        }, 'Error');

		$.validator.addMethod('validateTime', function (value) {
		    if($("#valid").val() != 'recurring')
            {
                return true;
            }

            var start_hour = parseInt($('#r_hour_from').val(), 10),
	     		start_min =  parseInt($('#r_minute_from').val(), 10),
	     		end_hour =  parseInt($('#r_hour_to').val(), 10),
	     		end_min =  parseInt($('#r_minute_to').val(), 10);
			if($('#r_ampm_from').length > 0)
			{
				if($('#r_ampm_from').val() == $('#r_ampm_to').val())
				{
					if(($('#r_ampm_from').val() == 'am' || $('#r_ampm_from').val() == 'AM' || $('#r_ampm_from').val() == 'pm' || $('#r_ampm_from').val() == 'PM') && start_hour == 12)
					{
						start_hour = 0;
					}
					if(($('#r_ampm_to').val() == 'am' || $('#r_ampm_to').val() == 'AM'  || $('#r_ampm_to').val() == 'pm' || $('#r_ampm_to').val() == 'PM') && end_hour == 12)
					{
						end_hour = 0;
					}
					if(end_hour < start_hour)
					{
						return false;
					}else if(end_hour == start_hour){
						if(end_min <= start_min)
						{
							return false;
						}else{
							return true;
						}
					}else{
						return true;
					}
				}else if(($('#r_ampm_from').val() == 'am' || $('#r_ampm_from').val() == 'AM') && ($('#r_ampm_to').val() == 'pm' || $('#r_ampm_to').val() == 'PM')){
					return true;
				}else{
					return false;
				}
			}else{
				if(end_hour < start_hour)
				{
					return false;
				}else if(end_hour == start_hour){
					if(end_min <= start_min)
					{
						return false;
					}else{
						return true;
					}
				}else{
					return true;
				}
			}
        }, 'Error');
		
		if ($frmCreateVoucher.length > 0) {
							
			$frmCreateVoucher.validate({
				rules: {
					"code": {
						required: true,
						remote: "index.php?controller=pjAdminVouchers&action=pjActionCheckCode"
					}, 
					"discount": {
						min: 0,
						number: true,
						required: true
					},
					"f_date" :{
						required: function(){
							return $("#valid").val() == 'fixed';
						}
					},
					"p_date_from" :{
						required: function(){
							return $("#valid").val() == 'period';
						}
					},
					"p_date_to" :{
						required: function(){
							return $("#valid").val() == 'period';
						}
					},
                    "validate_fixedtime": {
					    validateFixedTime: true
                    },
					"validate_datetime": {
					    remote: {
                            param: {
					            url: "index.php?controller=pjAdminVouchers&action=pjActionCheckDate",
                                type: "post",
                                data: {
                                    p_date_from: function() {
                                        return $( "#p_date_from" ).val();
                                    },
                                    p_hour_from: function() {
                                        return $( "#p_hour_from" ).val();
                                    },
                                    p_minute_from: function() {
                                        return $( "#p_minute_from" ).val();
                                    },
                                    p_ampm_from: function() {
                                        if($( "#p_ampm_from" ).length > 0)
                                        {
                                            return $( "#p_ampm_from" ).val();
                                        }else{
                                            return '';
                                        }
                                    },
                                    p_date_to: function() {
                                        return $( "#p_date_to" ).val();
                                    },
                                    p_hour_to: function() {
                                        return $( "#p_hour_to" ).val();
                                    },
                                    p_minute_to: function() {
                                        return $( "#p_minute_to" ).val();
                                    },
                                    p_ampm_to: function() {
                                        if($( "#p_ampm_to" ).length > 0)
                                        {
                                            return $( "#p_ampm_to" ).val();
                                        }else{
                                            return '';
                                        }
                                    }
                                }
                            },
                            depends: function(){
                                return $("#valid").val() == 'period' && $("#p_date_from").val() != '' && $("#p_date_to").val() != '';
                            }
                        }
                    },
                    "validate_time": {
					    validateTime: true
                    }
				},
				ignore: ""
			});
		}
		if ($frmUpdateVoucher.length > 0) {
			$frmUpdateVoucher.validate({
				rules: {
					"code": {
						required: true,
						remote: "index.php?controller=pjAdminVouchers&action=pjActionCheckCode&id=" + $frmUpdateVoucher.find("input[name='id']").val()
					},
					"discount": {
						min: 0,
						number: true,
						required: true
					},
                    "f_date" :{
						required: function(){
							return $("#valid").val() == 'fixed';
						}
					},
					"p_date_from" :{
						required: function(){
							return $("#valid").val() == 'period';
						}
					},
					"p_date_to" :{
						required: function(){
							return $("#valid").val() == 'period';
						}
					},
					"validate_fixedtime": {
					    validateFixedTime: true
                    },
					"validate_datetime": {
					    remote: {
                            param: {
					            url: "index.php?controller=pjAdminVouchers&action=pjActionCheckDate",
                                type: "post",
                                data: {
                                    p_date_from: function() {
                                        return $( "#p_date_from" ).val();
                                    },
                                    p_hour_from: function() {
                                        return $( "#p_hour_from" ).val();
                                    },
                                    p_minute_from: function() {
                                        return $( "#p_minute_from" ).val();
                                    },
                                    p_ampm_from: function() {
                                        if($( "#p_ampm_from" ).length > 0)
                                        {
                                            return $( "#p_ampm_from" ).val();
                                        }else{
                                            return '';
                                        }
                                    },
                                    p_date_to: function() {
                                        return $( "#p_date_to" ).val();
                                    },
                                    p_hour_to: function() {
                                        return $( "#p_hour_to" ).val();
                                    },
                                    p_minute_to: function() {
                                        return $( "#p_minute_to" ).val();
                                    },
                                    p_ampm_to: function() {
                                        if($( "#p_ampm_to" ).length > 0)
                                        {
                                            return $( "#p_ampm_to" ).val();
                                        }else{
                                            return '';
                                        }
                                    }
                                }
                            },
                            depends: function(){
                                return $("#valid").val() == 'period' && $("#p_date_from").val() != '' && $("#p_date_to").val() != '';
                            }
                        }
                    },
                    "validate_time": {
					    validateTime: true
                    }
				},
				ignore: ""
			});
			
		}
		if($(".decimal").length > 0)
		{
			$(".decimal").keyup(function(){
				var $this = $(this);
				var value = $this.val();
				if(value.indexOf(".") >= 0)
				{
					var number = ($this.val().split('.'));
				    if (number[1].length > 2)
				    {
				        var salary = parseFloat($this.val());
				        $this.val( salary.toFixed(2));
				    }
				}
			   
			});
		}
				
		if ($("#grid").length > 0 && datagrid) {
			function formatProducts(str, obj) {
				return obj.products;
			}
			function formatDiscount(str, obj) {
				return obj.discount_f;
			}
			function formatValid(str, obj) {
				return obj.valid_f;
			}
			
			var $buttons = [];
			var $actions = [];
			var $editable = false;
			var $select = false;
			if (myLabel.has_update) {
				$editable = true;
				$buttons.push({type: "edit", url: "index.php?controller=pjAdminVouchers&action=pjActionUpdate&id={:id}"});
			}
			if (myLabel.has_delete) {
				$buttons.push({type: "delete", url: "index.php?controller=pjAdminVouchers&action=pjActionDeleteVoucher&id={:id}"});
			}
			if (myLabel.has_delete_bulk) {
				$actions.push({text: myLabel.delete_selected, url: "index.php?controller=pjAdminVouchers&action=pjActionDeleteVoucherBulk", render: true, confirmation: myLabel.delete_confirmation});
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
				columns: [{text: myLabel.code, type: "text", sortable: true, editable: $editable},
				          {text: myLabel.products, type: "text", sortable: true, editable: false, renderer: formatProducts},
				          {text: myLabel.discount, type: "text", sortable: true, editable: $editable, renderer: formatDiscount},
				          {text: myLabel.valid, type: "text", sortable: false, editable: false, renderer: formatValid}
				       ],
				dataUrl: "index.php?controller=pjAdminVouchers&action=pjActionGetVoucher",
				dataType: "json",
				fields: ['code', 'id', 'discount', 'valid'],
				paginator: {
					actions: $actions,
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminVouchers&action=pjActionSaveVoucher&id={:id}",
				select: $select
			});
		}
		
		$(document).on("click", ".btn-all", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(this).addClass("btn-primary active").removeClass("btn-default")
				.siblings(".btn").removeClass("btn-primary active").addClass("btn-default");
			var content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				valid: "",
				q: ""
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminVouchers&action=pjActionGetVoucher", "code", "DESC", content.page, content.rowCount);
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
			obj.valid = "";
			obj[$this.data("column")] = $this.data("value");
			$.extend(cache, obj);
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminVouchers&action=pjActionGetVoucher", "code", "DESC", content.page, content.rowCount);
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
			$grid.datagrid("load", "index.php?controller=pjAdminVouchers&action=pjActionGetVoucher", "code", "ASC", content.page, content.rowCount);
			return false;
		}).on("change", "#valid", function (e) {
			var val = $(this).val();
			if($(this).val() == "")
			{
				$(".valid-box").hide();
			}else{
				$('#valid_' + val).siblings(".valid-box").hide().end().show();
			}
		}).on('change', '#switch_type', function (e) {
		    if($(this).is(':checked'))
            {
                $('#type').val('amount');
                $('.group-fa-change .input-group-addon strong').html($('.group-fa-change').attr('data-currency-sign'));
                $("#discount").rules("remove", "max");
            } else {
		        $('#type').val('percent');
		        $('.group-fa-change .input-group-addon strong').html('%');
		        $("#discount").rules("add", {max: 100});
            }
		}).on("change", ".number", function (e) {
			var v = parseFloat(this.value);
		    if (isNaN(v)) {
		        this.value = '';
		    } else {
		        this.value = v.toFixed(2);
		    }
		    if (parseFloat(this.value) >= 99999999999999.99) {
		    	this.value = '99999999999999.99';
		    }
		});
		
		if ($("#switch_type").length) {
			$("#switch_type").trigger("change");
		}
	});
})(jQuery);