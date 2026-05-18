var jQuery = jQuery || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";

		var multilang = ($.fn.multilang !== undefined),
			$document = $(document),
			$frmUpdateOptions = $('#frmUpdateOptions'),
			$frmNotifications = $('#frmNotifications');

		if ($frmUpdateOptions.length > 0) {
			$frmUpdateOptions.validate({
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

		if ($(".field-int").length > 0) {
			$(".field-int").TouchSpin({
				verticalbuttons: true,
				buttondown_class: 'btn btn-white',
				buttonup_class: 'btn btn-white',
				max: 4294967295
			});
		}

		// ---------------------------------------------------------------------
		function notificationsGetMetaData() {
			var $box = $("#boxNotificationsMetaData");
			if (!$box.length) {
				return;
			}

			// show preloader
			$box.empty().addClass("ibox-content-notification");

			$('<div class="ibox-content-overlay"></div> \
				<div class="sk-spinner sk-spinner-double-bounce"> \
					<div class="sk-double-bounce1"></div> \
					<div class="sk-double-bounce2"></div> \
				</div>').appendTo($box);

			$box.find(".ibox-content-overlay, .sk-spinner").show();

			var search = window.location.search,
				variant = search.match(/&?variant=(\w+)/),
				transport = search.match(/&?transport=(\w+)/),
				params = {
					recipient: $('input[name="recipient"]:checked').val()
				};

			if (variant !== null && transport !== null) {
				params.variant = variant[1];
				params.transport = transport[1];
			}

			$.get("index.php?controller=pjAdminOptions&action=pjActionNotificationsGetMetaData", params).done(function (data) {

				$box.html(data);

				if (variant !== null && transport !== null) {
					$box.find(['#variant', transport[1], variant[1]].join("_")).trigger("change");
				} else {
					$box.find('input[name="variant"]:first').trigger("change");
				}

			});
		}

		function notificationsGetContent() {
			var $box = $("#boxNotificationsContent");
			if (!$box.length) {
				return;
			}

			// show preloader
			$box.empty().addClass("ibox-content-notification");

			$('<div class="ibox-content-overlay"></div> \
				<div class="sk-spinner sk-spinner-double-bounce"> \
					<div class="sk-double-bounce1"></div> \
					<div class="sk-double-bounce2"></div> \
				</div>').appendTo($box);

			$box.find(".ibox-content-overlay, .sk-spinner").show();

			var $checked = $('input[name="variant"]:checked');

			$.get("index.php?controller=pjAdminOptions&action=pjActionNotificationsGetContent", {
				recipient: $('input[name="recipient"]:checked').val(),
				variant: $checked.val(),
				transport: $checked.data("transport")
			}).done(function (data) {

				$box.html(data);

				myTinyMceDestroy.call(null);
				myTinyMceInit.call(null, 'textarea.mceEditor');

				var index = $(".pj-form-langbar-item.btn-primary").data("index");
				if (index !== undefined) {
					$box.find('.pj-multilang-wrap[data-index!="' + index + '"]').hide();
					$box.find('.pj-multilang-wrap[data-index="' + index + '"]').show();
				}
				$('.scNotifyTokens').hide();
				if ($checked.val() == 'account' || $checked.val() == 'forgot') {
					$('.scAccountTokens').show();
				} else if ($checked.val() == 'send_to_friend') {
					$('.scSendToFriendTokens').show();
				} else {
					$('.scBookingTokens').show();
				}
			});
		}

		function notificationsSetContent(toggle) {

			var $box = $("#boxNotificationsContent");
			if (!$box.length) {
				return;
			}

			// show preloader
			$box.addClass("notification-box");

			$('<div class="ibox-content-overlay"></div> \
				<div class="sk-spinner sk-spinner-double-bounce"> \
					<div class="sk-double-bounce1"></div> \
					<div class="sk-double-bounce2"></div> \
				</div>').appendTo($box);

			$box.find(".ibox-content-overlay, .sk-spinner").show();

			var postData,
				$form = $box.find("form");

			if (toggle) {
				postData = $.param({
					is_active: ($form.find("#is_active").is(":checked") ? 1 : 0),
					id: $form.find('input[name="id"]').val()
				});
			} else {
				postData = $form.serialize();
				postData = postData.replace(/&?is_active=(\w+)?/, "");

				var l = Ladda.create($form.find(":submit").get(0));
				l.start();
			}

			$.post("index.php?controller=pjAdminOptions&action=pjActionNotificationsSetContent", postData).done(function (data) {

				if (data && data.status && data.status === "OK") {

					notificationsGetMetaData.call(null);

				}

			});
		}

		$("#boxNotificationsWrapper").on("change", 'input[name="recipient"]', function () {

			var search = window.location.search,
				recipient = search.match(/&?recipient=(\w+)/),
				variant = search.match(/&?variant=(\w+)/),
				transport = search.match(/&?transport=(\w+)/);

			var arr = [];
			arr.push("index.php?controller=pjAdminOptions&action=pjActionNotifications&recipient=");
			arr.push(this.value);

			if (recipient !== null && recipient[1] === this.value) {
				if (variant !== null && transport !== null) {
					arr.push("&transport=");
					arr.push(transport[1]);
					arr.push("&variant=");
					arr.push(variant[1]);
				}
			}

			var url = arr.join("");
			history.pushState({
				url: url,
				title: null
			}, null, url);

			notificationsGetMetaData.call(null);

		}).on("change", 'input[name="variant"]', function () {

			var $this = $(this);

			var url = ["index.php?controller=pjAdminOptions&action=pjActionNotifications&recipient=", $('input[name="recipient"]:checked').val(), "&transport=", $this.data("transport"), "&variant=", $this.val()].join("");
			history.pushState({
				url: url,
				title: null
			}, null, url);

			notificationsGetContent.call(null);

		}).on("change", '#is_active', function () {

			notificationsSetContent.call(null, true);

			var $this = $(this),
				$hidden = $this.closest("form").find(".notification-area");

			if ($this.is(":checked")) {
				$hidden.removeClass("hidden");
			} else {
				$hidden.addClass("hidden");
			}

		}).on("submit", "form", function (e) {
			e.preventDefault();

			notificationsSetContent.call(null, false);

			return false;
		});

		$('input[name="recipient"]:checked').trigger("change");

		$(window).on("popstate", function (e) {
			var state = e.originalEvent.state;
			if (state !== null) {
				//load
			} else {
				//empty
			}
		});
		// ---------------------------------------------------------------------

		if ($frmNotifications.length && validate) {
			$frmNotifications.validate();

			changeEmailBox();
		}

		function changeEmailBox() {
			var tab_id = $('.nav-tabs .active a').attr('href').substring(1),
				id = $('#' + tab_id + '_email_notify').val();
			$('#' + tab_id).find('.boxEmail').hide();
			var activeSwitch = $('#switch_' + id);
			if (activeSwitch.length) {
				if (activeSwitch.is(':checked')) {
					$('.boxEmail-' + id).show();
				}
				else {
					activeSwitch.closest('.boxEmail-' + id).show();
				}
			}
		}

		if ($('#hidden_code').length > 0) {
			reDrawCode.call(null);
		}

		// function reDrawCode() {

		// 	var code = $("#hidden_code").text(),
		// 		category = $("select[name='install_category']").find("option:selected").val(),
		// 		locale = "",
		// 		hide = "",
		// 		company = "&company_id=" + COMPANY_ID,
		// 		role_id = "&data=" + ROLE_ID;
		// 	category = parseInt(category.length, 10) > 0 ? "&category_id=" + category : "";

		// 	if ($("select[name='install_locale']").length > 0) {
		// 		var locale_id = $("select[name='install_locale']").val();
		// 		locale = parseInt(locale_id, 10) > 0 ? "&locale=" + locale_id : "";
		// 	}

		// 	if ($("input[name='install_hide']").length > 0) {
		// 		hide = $("input[name='install_hide']").is(":checked") ? "&hide=1" : "";
		// 	}

		// 	// Replace only the action= parameter once, then append dynamic params + company_id
		// 	$("#install_code").text(
		// 		code.replace(/&action=pjActionLoad"/g, function (match) {
		// 			return ['&action=pjActionLoad', category, locale, hide, company,role_id, '"'].join("");
		// 		})
		// 	);
		// }
		function reDrawCode() {
			var code = $("#hidden_code").text(),
				category = $("select[name='install_category']").find("option:selected").val(),
				locale = "",
				hide = "",
				company = "&company_id=" + COMPANY_ID,
				role_id_param = "";

			// ✅ decide role_id based on current checkbox state
			if ($("#is_super_admin").is(":checked")) {
				role_id_param = "&data=" + ROLE_ID;
			} else {
				role_id_param = "";
			}

			category = parseInt(category.length, 10) > 0 ? "&category_id=" + category : "";

			if ($("select[name='install_locale']").length > 0) {
				var locale_id = $("select[name='install_locale']").val();
				locale = parseInt(locale_id, 10) > 0 ? "&locale=" + locale_id : "";
			}

			if ($("input[name='install_hide']").length > 0) {
				hide = $("input[name='install_hide']").is(":checked") ? "&hide=1" : "";
			}

			$("#install_code").text(
				code.replace(/&action=pjActionLoad"/g, function () {
					return [
						'&action=pjActionLoad',
						category,
						locale,
						hide,
						company,
						role_id_param,
						'"'
					].join("");
				})
			);
		}

		
		$document.on("focus", ".textarea_install", function (e) {
			var $this = $(this);
			$this.select();
			$this.mouseup(function () {
				$this.unbind("mouseup");
				return false;
			});
		}).on("change", "select[name='theme']", function (e) {

			reDrawCode.call(null);

		}).on("change", "select[name='install_category']", function (e) {

			reDrawCode.call(null);

		})
			.on("change", "input[name='is_super_admin']", function (e) {

				reDrawCode.call(null);

			}).on("change", "select[name='install_locale']", function (e) {

				reDrawCode.call(null);

			}).on("change", "input[name='install_hide']", function (e) {

				reDrawCode.call(null);

			}).on("change", "select[name='email_type']", function (e) {
				$.get("index.php?controller=pjAdminOptions&action=pjActionUpdateEmail", {
					"type": $(this).find("option:selected").val()
				}).done(function (data) {
					$("#boxEmails").html(data);
					attachTinyMce.call(null);
				});
			}).on("click", ".btn-example", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				if ($dialogExample.length > 0 && dialog) {
					$dialogExample.dialog("open");
				}
				return false;
			}).on("change", "select[name='value-enum-o_theme']", function () {
				var $this = $(this),
					value = $this.find("option:selected").val(),
					theme = value.match(/::(\d+)$/),
					$a = $this.siblings("a.pj-table-icon-eye");

				if ($a.length > 0 && theme !== null) {
					$a.attr("href", $a.attr("href").replace(/&theme=(\d+)/, '&theme=' + theme[1]));
				}
			}).on("keydown", ".digits", function (e) {
				if (e.shiftKey == true) {
					e.preventDefault();
				}
				if ((e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 96 && e.keyCode <= 105) || e.keyCode == 8 || e.keyCode == 9 || e.keyCode == 37 || e.keyCode == 39 || e.keyCode == 46) {

				} else {
					e.preventDefault();
				}
			}).on('change', '.onoffswitch-checkbox', function (e) {
				var name = $(this).attr('name');
				if ($(this).is(':checked')) {
					if (name == 'o_disable_payments') {
						$('input[name="value-enum-' + name + '"]').val('Yes|No::Yes');
					} else {
						$('input[name="value-enum-' + name + '"]').val('1|0::1');
					}
				} else {
					if (name == 'o_disable_payments') {
						$('input[name="value-enum-' + name + '"]').val('Yes|No::No');
					} else {
						$('input[name="value-enum-' + name + '"]').val('1|0::0');
					}
				}
				if ($(this).is(':checked')) {
					if (name == 'o_disable_orders') {
						$('input[name="value-enum-' + name + '"]').val('Yes|No::Yes');
					} else {
						$('input[name="value-enum-' + name + '"]').val('1|0::1');
					}
				} else {
					if (name == 'o_disable_orders') {
						$('input[name="value-enum-' + name + '"]').val('Yes|No::No');
					} else {
						$('input[name="value-enum-' + name + '"]').val('1|0::0');
					}
				}
				if ($(this).is(':checked')) {
					if (name == 'o_seo_url') {
						$('input[name="value-enum-' + name + '"]').val('Yes|No::Yes');
					} else {
						$('input[name="value-enum-' + name + '"]').val('1|0::1');
					}
				} else {
					if (name == 'o_seo_url') {
						$('input[name="value-enum-' + name + '"]').val('Yes|No::No');
					} else {
						$('input[name="value-enum-' + name + '"]').val('1|0::0');
					}
				}
			}).on("change", "select[name='value-enum-o_insurance_type']", function () {
				var $type = $('option:selected', this).attr('data-type');
				$('.insurance-group-addon').html($type);
			}).on("click", ".btnAddShipping", function () {
				var $c = $("#tblShippingClone tbody").clone(),
					r = $c.html().replace(/\{INDEX\}/g, 'new_' + Math.ceil(Math.random() * 99999));
				$("#tblShipping").find("tbody").append(r);
			}).on("click", ".btnDeleteShipping", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var id = $(this).attr('data-id'),
					$tr = $(this).closest("tr");
				swal({
					title: myLabel.alert_delete_shipping_title,
					text: myLabel.alert_delete_shipping_text,
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: myLabel.btn_delete,
					cancelButtonText: myLabel.btn_cancel,
					closeOnConfirm: false,
					showLoaderOnConfirm: true
				}, function () {
					$.post("index.php?controller=pjAdminOptions&action=pjActionDeleteLocation", { id: id }).done(function (data) {
						if (!(data && data.status)) {

						}
						switch (data.status) {
							case "OK":
								swal.close();
								$tr.css("backgroundColor", "#FFB4B4").fadeOut("slow", function () {
									$tr.remove();
								});
								break;
						}
					});
				});
				return false;
			}).on("click", ".btnRemoveShipping", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $tr = $(this).closest("tr");
				$tr.css("backgroundColor", "#FFB4B4").fadeOut("slow", function () {
					$tr.remove();
				});
				return false;
			});

		var $topMenu = $("#page-wrapper").children(".row.border-bottom"),
			$iframe = $("#iframeEditor"),
			$body = $("body"),
			$window = $(window);

		function resizeIframe() {
			if (!$iframe.length) {
				return;
			}

			$iframe.height($window.height() - $topMenu.outerHeight());
		}

		if ($iframe.length) {
			$iframe.on('load', function () {
				var body = this.contentWindow.document.body;
				if (body.getAttribute('data-editor')) {
					var script = document.createElement('script');
					script.type = 'text/javascript';
					script.async = true;
					script.src = body.getAttribute('data-editor');
					window.setTimeout(function () {
						body.appendChild(script);
					}, 1200);
				}
				var head = this.contentWindow.document.getElementsByTagName('head')[0],
					style = document.createElement('link');
				style.rel = 'stylesheet';
				style.href = 'third-party/font_awesome/4.7.0/css/font-awesome.min.css';

				head.appendChild(style);
			});

			$body.addClass("page-editor");
			resizeIframe.call(null);

			$window.on("resize", function () {
				resizeIframe.call(null);
			});
		}

		$(document).on('click', '.device-view', function (e) {
			e.preventDefault();

			var $this = $(this),
				device = $this.data('device'),
				orientation = $this.data('orientation'),
				$device = $('#iframeDevice'),
				$holder = $('#iframeHolder');

			$this.closest('.row').find('.device-view.active').removeClass('active').end().end().addClass('active');

			switch (device) {
				case 'desktop':
					$device.addClass('hidden');
					$iframe.insertBefore($device);
					$body.addClass('page-editor');
					$window.trigger('resize');
					break;
				case 'tablet':
				case 'phone':
					$iframe.appendTo($holder);
					$holder.removeClass().addClass(device + '-view-' + orientation);
					$device.removeClass('hidden');
					$body.removeClass('page-editor');
					$('#device_title').html($(['#', device, '_', orientation].join('')).html());
					$('#device_info').html($(['#', device, '_', orientation, '_info'].join('')).html());
					break;
			}

			return false;
		}).on('click', '[data-theme]', function (e) {
			e.preventDefault();

			var $this = $(this);
			$.post("index.php?controller=pjAdminOptions&action=pjActionUpdateTheme", {
				theme: $this.data("index")
			}).done(function (data) {
				if (data && data.status && data.status === "OK") {
					$this.closest(".dropdown-menu").find(".thumbnail").removeClass("active");
					$this.addClass("active");
				}
			});

			var $link = $(".open-new-window");
			if ($link.length) {
				$link.attr("href", $link.attr("href").replace(/(&?theme=)\d+/, '$1' + $this.data("index")));
			}
			$iframe.attr('src', $this.attr('href'));
		}).on("change", ".onoffswitch-allow-pending-time .onoffswitch-checkbox", function (e) {
			if ($(this).prop('checked')) {
				$('.set-pending-time').show();
			} else {
				$('.set-pending-time').hide();
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
		}).on("keyup", "#uri_page", function (e) {
			var tmpl = $("#hidden_htaccess").text(),
				tmpl_remote = $("#hidden_htaccess_remote").text(),
				index = this.value.indexOf("?");
			$("#install_htaccess").text(tmpl.replace('::URI_PAGE::', index >= 0 ? this.value.substring(0, index) : this.value));
			$("#install_htaccess_remote").text(tmpl_remote.replace('::URI_PAGE::', index >= 0 ? this.value.substring(0, index) : this.value));
		});
	});
})(jQuery);