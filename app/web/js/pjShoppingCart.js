/*!
 * Shopping Cart v4.2
 * http://phpjabbers.com/shopping-cart/
 * 
 * Copyright 2015, StivaSoft Ltd.
 * 
 * Date: Wed May 06 10:57:28 2012 +0300
 */
(function (window, undefined) {
	"use strict";

	pjQ.$.ajaxSetup({
		xhrFields: {
			withCredentials: true
		}
	});

	var document = window.document,
		validate = (pjQ.$.fn.validate !== undefined),
		fancybox = (pjQ.$.fn.fancybox !== undefined),
		dialog = (pjQ.$.fn.dialog !== undefined),
		routes = [
			{ pattern: /^\/bookings\/cart$/, eventName: "loadCart" },
			{ pattern: /^\/bookings\/checkout$/, eventName: "loadCheckout" },
			{ pattern: /^\/bookings\/preview$/, eventName: "loadPreview" },
			{ pattern: /^\/favorites$/, eventName: "loadFavs" },
			{ pattern: /^\/login$/, eventName: "loadLogin" },
			{ pattern: /^\/forgot-password$/, eventName: "loadForgot" },
			{ pattern: /^\/register$/, eventName: "loadRegister" },
			{ pattern: /^\/profile$/, eventName: "loadProfile" },
			{ pattern: /^\/orders$/, eventName: "loadOrdersHistory" },
			{ pattern: /^\/orders\/page:(\d+)$/, eventName: "loadOrdersHistory" },
			{ pattern: /^\/order\/([^/]+)$/, eventName: "loadOrderDetails" },
			{ pattern: /^\/product\/(\d+)$/, eventName: "loadProduct" },
			{ pattern: /^\/.*-(\d+)\.html$/, eventName: "loadProduct" },
			{ pattern: /^\/products$/, eventName: "loadProducts" },
			{ pattern: /^\/products\/q:(.*)?\/category:(\d+)?\/page:(\d+)?$/, eventName: "loadProducts" },
			{ pattern: /^\/products\/q:(.*)?\/category:(\d+)?\/page:(\d+)?\/sort:(.*)?$/, eventName: "loadProducts" }
		];

	function log() {
		if (window.console && window.console.log) {
			for (var x in arguments) {
				if (arguments.hasOwnProperty(x)) {
					window.console.log(arguments[x]);
				}
			}
		}
	}

	function assert() {
		if (window && window.console && window.console.assert) {
			window.console.assert.apply(window.console, arguments);
		}
	}

	function ShoppingCart(options) {
		if (!(this instanceof ShoppingCart)) {
			return new ShoppingCart(options);
		}

		this.reset.call(this);
		this.init.call(this, options);

		return this;
	}

	ShoppingCart.inObject = function (val, obj) {
		var key;
		for (key in obj) {
			if (obj.hasOwnProperty(key)) {
				if (obj[key] == val) {
					return true;
				}
			}
		}
		return false;
	};

	ShoppingCart.size = function (obj) {
		var key,
			size = 0;
		for (key in obj) {
			if (obj.hasOwnProperty(key)) {
				size += 1;
			}
		}
		return size;
	};

	ShoppingCart.compare = function (obj1, obj2) {
		var p;
		for (p in obj1) {
			if (obj2[p] === undefined) {
				return false;
			}
		}
		for (p in obj1) {
			if (obj1[p]) {
				switch (typeof (obj1[p])) {
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
				if (obj2[p]) {
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
	};

	ShoppingCart.prototype = {
		reset: function () {
			this.$container = null;
			this.container = null;
			this.page = null;
			this.q = null;
			this.category_id = null;
			this.product_id = null;
			this.sort = 'featured';
			//Product
			this.stockObj = {};
			this.stockIds = {};
			this.qtyObj = {};
			this.priceObj = {};
			this.price = 0.00;
			this.priceStocks = 0;
			this.priceExtras = 0;
			this.unitPrice = 0;
			this.qty = 1;
			this.canBuy = true;
			//--Product
			this.options = {};

			return this;
		},
		normalizeUrl: function (url) {
			return String(url || "").replace(/\/$/, "");
		},
		normalizeRoutePath: function (page) {
			var install, match;

			if (page === undefined || page === null) {
				return page;
			}

			page = String(page);
			install = this.normalizeUrl(this.options.installUrl);

			if (install && page.indexOf(install) === 0) {
				page = page.substring(install.length);
			}

			match = page.match(/^https?:\/\/[^/]+(\/[^?#]*)?/i);
			if (match) {
				page = match[1] || "/";
			}

			if (page === "") {
				page = "/";
			} else if (page.charAt(0) !== "/") {
				page = "/" + page;
			}

			return page;
		},
		stripPagePrefixFromPath: function (path) {
			var prefix, escaped;

			if (!this.options.pagePrefix || path === undefined || path === null) {
				return path;
			}

			path = String(path);
			prefix = String(this.options.pagePrefix);
			escaped = prefix.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");

			return path.replace(new RegExp("^/" + escaped + "-"), "/");
		},
		isStandaloneStorefront: function () {
			var install, current, suffix,
				routePattern = /^\/(products|product|favorites|login|register|profile|orders|order|forgot-password|bookings)(\/|$)/i;

			install = this.normalizeUrl(this.options.installUrl);
			current = this.normalizeUrl(window.location.href.split("#")[0].split("?")[0]);

			if (current === install) {
				return true;
			}

			if (install && current.indexOf(install) === 0) {
				suffix = this.stripPagePrefixFromPath(current.substring(install.length));
				if (suffix === "" || routePattern.test(suffix)) {
					return true;
				}
			}

			return false;
		},
		getHash: function () {
			var self = this,
				hash,
				rawHash;

			if (!self.isStandaloneStorefront()) {
				rawHash = window.location.hash || "";
				if (rawHash.indexOf("#!") === 0) {
					hash = rawHash.substring(2);
				} else if (rawHash.indexOf("#") === 0) {
					hash = rawHash.substring(1);
				} else {
					hash = "";
				}

				return self.stripPagePrefixFromPath(self.normalizeRoutePath(hash));
			}

			return self.stripPagePrefixFromPath(self.normalizeRoutePath(String(window.location.href).replace(self.options.installUrl, "")));
		},
		hashBang: function (page) {
			var self = this,
				hash,
				target;

			page = self.normalizeRoutePath(page);
			hash = self.getHash.call(self);

			if (page !== undefined && page != hash) {
				if (!self.isStandaloneStorefront()) {
					target = "#!" + page;
					if (window.location.hash !== target) {
						window.location.hash = target;
					} else {
						self.onHashChange.call(self);
					}
					return true;
				}

				window.history.pushState({}, "", self.options.installUrl + page);
				self.onHashChange.call(self);
				return true;
			}

			return false;
		},
		onHashChange: function () {
			var self = this;
			var hash = self.getHash.call(self);

			for (var i = 0; i < routes.length; i++) {
				var match = hash.match(routes[i].pattern);
				if (match !== null) {
					pjQ.$(window).trigger(routes[i].eventName, match.slice(1));
					return true;
				}
			}

			pjQ.$(window).trigger("loadProducts");
			return true;
		},
		getLogin: function () {
			var self = this;
			pjQ.$.get([this.options.folder, "index.php?controller=pjFrontPublic&action=pjActionLogin"].join(""), {
				"locale": this.options.locale,
				"hide": this.options.hide,
				"layout": this.options.layout,
				"theme": this.options.theme,
				"session_id": this.options.session_id
			}).done(function (data) {
				self.$container.html(data);

				if (validate) {
					if (self.options.layout != '3') {
						var $form = self.$container.find(".scSelectorLoginForm");
						$form.validate({
							rules: {
								"email": {
									required: true,
									email: true
								},
								"password": {
									required: true
								}
							},
							messages: {
								"email": {
									required: $form.find("input[name='email']").attr('data-err'),
									email: $form.find("input[name='email']").attr('data-email')
								},
								"password": $form.find("input[name='password']").attr('data-err')
							},
							onkeyup: false,
							onclick: false,
							onfocusout: false,
							errorClass: "scError",
							validClass: "scValid",
							submitHandler: function (form) {
								self.disableButtons.call(self);
								var $form = pjQ.$(form);
								pjQ.$.post([self.options.folder, "index.php?controller=pjFrontPublic&action=pjActionLogin", "&session_id=", self.options.session_id].join(""), $form.serialize()).done(function (data) {
									if (data.status == "OK") {
										var prefix = (self.options.pagePrefix) ? self.options.pagePrefix + "-" : "";
										self.hashBang("/" + prefix + "products/q:" + (self.q && self.q !== undefined ? self.q : "") + "/category:" + (self.category_id && self.category_id !== undefined ? self.category_id : "") + "/page:" + (self.page && self.page !== undefined ? self.page : 1));
									} else if (data.status == "ERR") {
										$form
											.find(".scSelectorNoticeMsg")
											.html(data.text)
											.removeClass("scNoticeSuccess")
											.addClass("scNoticeError")
											.prepend(pjQ.$("<div>").addClass("scNoticeIcon"))
											.show();
										self.enableButtons.call(self);
									}
								}).fail(function () {
									self.enableButtons.call(self);
								});
								return false;
							}
						});
					} else {
						var $form = self.$container.find(".scSelectorLoginForm");
						$form.validate({
							rules: {
								"email": {
									required: true,
									email: true
								},
								"password": {
									required: true
								}
							},
							messages: {
								"email": {
									required: $form.find("input[name='email']").attr('data-err'),
									email: $form.find("input[name='email']").attr('data-email')
								},
								"password": $form.find("input[name='password']").attr('data-err')
							},
							onkeyup: false,
							onclick: false,
							onfocusout: false,
							errorPlacement: function (error, element) {
								var $parent = element.parent(),
									$input_group = $parent.parent();
								error.insertAfter(element.parent());
								$input_group.addClass('has-error');
							},
							success: function (label) {
								var $parent = pjQ.$(label).parent(),
									$sibling = pjQ.$(label).siblings();
								$parent.removeClass('has-error').addClass('has-success');
								pjQ.$(label).remove();
							},
							submitHandler: function (form) {
								self.disableButtons.call(self);
								var $form = pjQ.$(form);
								pjQ.$.post([self.options.folder, "index.php?controller=pjFrontPublic&action=pjActionLogin", "&session_id=", self.options.session_id].join(""), $form.serialize()).done(function (data) {
									if (data.status == "OK") {
										var prefix = (self.options.pagePrefix) ? self.options.pagePrefix + "-" : "";
										self.hashBang("/" + prefix + "products/q:" + (self.q && self.q !== undefined ? self.q : "") + "/category:" + (self.category_id && self.category_id !== undefined ? self.category_id : "") + "/page:" + (self.page && self.page !== undefined ? self.page : 1));
									} else if (data.status == "ERR") {
										$form
											.find(".scSelectorNoticeMsg")
											.html(data.text)
											.removeClass("alert-success")
											.addClass("alert-warning")
											.show();
										self.enableButtons.call(self);
									}
								}).fail(function () {
									self.enableButtons.call(self);
								});
								return false;
							}
						});
					}
				}
			});
		},
		getLogout: function () {
			var self = this;
			pjQ.$.get([this.options.folder, "index.php?controller=pjFront&action=pjActionLogout", "&session_id=", self.options.session_id].join("")).done(function (data) {
				var prefix = (self.options.pagePrefix) ? self.options.pagePrefix + "-" : "";
				var hash = self.getHash();
				if (hash.match("/" + prefix + "products/q:") === null) {
					self.hashBang("/" + prefix + "products/q:" + (self.q && self.q !== undefined ? self.q : "") + "/category:" + (self.category_id && self.category_id !== undefined ? self.category_id : "") + "/page:" + (self.page && self.page !== undefined ? self.page : 1));
				} else {
					self.hashBang("/" + prefix + "products");
				}
			});
		},
		getForgot: function () {
			var self = this;
			pjQ.$.get([this.options.folder, "index.php?controller=pjFrontPublic&action=pjActionForgot"].join(""), {
				"locale": this.options.locale,
				"hide": this.options.hide,
				"layout": this.options.layout,
				"theme": this.options.theme,
				"session_id": this.options.session_id
			}).done(function (data) {
				self.$container.html(data);

				if (validate) {
					if (self.options.layout != '3') {
						var $form = self.$container.find(".scSelectorForgotForm");
						$form.validate({
							rules: {
								"email": {
									required: true,
									email: true
								}
							},
							messages: {
								"email": {
									required: $form.find("input[name='email']").attr('data-err'),
									email: $form.find("input[name='email']").attr('data-email')
								}
							},
							onkeyup: false,
							onclick: false,
							onfocusout: false,
							errorClass: "scError",
							validClass: "scValid",
							submitHandler: function (form) {
								self.disableButtons.call(self);
								var $form = pjQ.$(form);
								pjQ.$.post([self.options.folder, "index.php?controller=pjFrontPublic&action=pjActionForgot", "&session_id=", self.options.session_id].join(""), $form.serialize()).done(function (data) {
									if (data.status == "OK") {
										$form.find(".scSelectorNoticeMsg")
											.html(data.text)
											.removeClass("scNoticeError")
											.addClass("scNoticeSuccess")
											.prepend(pjQ.$("<div>").addClass("scNoticeIcon"))
											.show();
										$form.find(":input").not(":button, :submit, :reset, :hidden").val("").removeAttr("checked").removeAttr("selected");
									} else if (data.status == "ERR") {
										$form.find(".scSelectorNoticeMsg")
											.html(data.text)
											.removeClass("scNoticeSuccess")
											.addClass("scNoticeError")
											.prepend(pjQ.$("<div>").addClass("scNoticeIcon"))
											.show();
									}
									self.enableButtons.call(self);
								}).fail(function () {
									self.enableButtons.call(self);
								});
								return false;
							}
						});
					} else {
						var $form = self.$container.find(".scSelectorForgotForm");
						$form.validate({
							rules: {
								"email": {
									required: true,
									email: true
								}
							},
							messages: {
								"email": {
									required: $form.find("input[name='email']").attr('data-err'),
									email: $form.find("input[name='email']").attr('data-email')
								}
							},
							onkeyup: false,
							onclick: false,
							onfocusout: false,
							errorPlacement: function (error, element) {
								var $parent = element.parent(),
									$input_group = $parent.parent();
								error.insertAfter(element.parent());
								$input_group.addClass('has-error');
							},
							success: function (label) {
								var $parent = pjQ.$(label).parent(),
									$sibling = pjQ.$(label).siblings();
								$parent.removeClass('has-error').addClass('has-success');
								pjQ.$(label).remove();
							},
							submitHandler: function (form) {
								self.disableButtons.call(self);
								var $form = pjQ.$(form);
								pjQ.$.post([self.options.folder, "index.php?controller=pjFrontPublic&action=pjActionForgot", "&session_id=", self.options.session_id].join(""), $form.serialize()).done(function (data) {
									if (data.status == "OK") {
										$form.find(".scSelectorNoticeMsg")
											.html(data.text)
											.removeClass("alert-warning")
											.addClass("alert-success")
											.show();
										$form.find(":input").not(":button, :submit, :reset, :hidden").val("").removeAttr("checked").removeAttr("selected");
									} else if (data.status == "ERR") {
										$form.find(".scSelectorNoticeMsg")
											.html(data.text)
											.removeClass("alert-success")
											.addClass("alert-warning")
											.prepend(pjQ.$("<div>").addClass("scNoticeIcon"))
											.show();
									}
									self.enableButtons.call(self);
								}).fail(function () {
									self.enableButtons.call(self);
								});
								return false;
							}
						});
					}
				}
			});
		},
		getOrdersHistory: function (page) {
			var self = this;
			var params = {
				"locale": this.options.locale,
				"hide": this.options.hide,
				"layout": this.options.layout,
				"theme": this.options.theme,
				"session_id": this.options.session_id
			};
			if (page !== undefined && page !== null && parseInt(page, 10) > 0) {
				params.page = parseInt(page, 10);
			}
			pjQ.$.get([this.options.folder, "index.php?controller=pjFrontPublic&action=pjActionOrdersHistory"].join(""), params).done(function (data) {
				self.$container.html(data);
			});
		},
		getOrderDetails: function (uuid) {
			var self = this;
			pjQ.$.get([this.options.folder, "index.php?controller=pjFrontPublic&action=pjActionOrderDetails"].join(""), {
				"uuid": uuid,
				"locale": this.options.locale,
				"hide": this.options.hide,
				"layout": this.options.layout,
				"theme": this.options.theme,
				"session_id": this.options.session_id
			}).done(function (data) {
				self.$container.html(data);
			});
		},
		getProfile: function () {
			var self = this;
			pjQ.$.get([this.options.folder, "index.php?controller=pjFrontPublic&action=pjActionProfile"].join(""), {
				"locale": this.options.locale,
				"hide": this.options.hide,
				"layout": this.options.layout,
				"theme": this.options.theme,
				"session_id": this.options.session_id
			}).done(function (data) {
				self.$container.html(data);

				if (validate) {
					if (self.options.layout != '3') {
						var $form = self.$container.find(".scSelectorProfileForm");
						$form.validate({
							rules: {
								"email": {
									required: true,
									email: true
								},
								"password": "required"
							},
							messages: {
								"email": {
									required: $form.find("input[name='email']").attr('data-err'),
									email: $form.find("input[name='email']").attr('data-email')
								},
								"password": $form.find("input[name='password']").attr('data-err'),
								"client_name": $form.find("input[name='client_name']").attr('data-err'),
								"phone": $form.find("input[name='phone']").attr('data-err'),
								"url": $form.find("input[name='url']").attr('data-err')
							},
							onkeyup: false,
							onclick: false,
							onfocusout: false,
							errorClass: "scError",
							validClass: "scValid",
							submitHandler: function (form) {
								self.disableButtons.call(self);
								var $form = pjQ.$(form);
								pjQ.$.post([self.options.folder, "index.php?controller=pjFrontPublic&action=pjActionProfile", "&session_id=", self.options.session_id].join(""), $form.serialize()).done(function (data) {
									if (data.status == "OK") {
										$form
											.find(".scSelectorNoticeMsg")
											.html(data.text)
											.removeClass("scNoticeError")
											.addClass("scNoticeSuccess")
											.prepend(pjQ.$("<div>").addClass("scNoticeIcon"))
											.show();
									} else if (data.status == "ERR") {
										$form
											.find(".scSelectorNoticeMsg")
											.html(data.text)
											.removeClass("scNoticeSuccess")
											.addClass("scNoticeError")
											.prepend(pjQ.$("<div>").addClass("scNoticeIcon"))
											.show();
									}
									self.enableButtons.call(self);
								}).fail(function () {
									self.enableButtons.call(self);
								});
								return false;
							}
						});
					} else {
						var $form = self.$container.find(".scSelectorProfileForm");
						$form.validate({
							rules: {
								"email": {
									required: true,
									email: true
								},
								"password": "required"
							},
							messages: {
								"email": {
									required: $form.find("input[name='email']").attr('data-err'),
									email: $form.find("input[name='email']").attr('data-email')
								},
								"password": $form.find("input[name='password']").attr('data-err'),
								"client_name": $form.find("input[name='client_name']").attr('data-err'),
								"phone": $form.find("input[name='phone']").attr('data-err'),
								"url": $form.find("input[name='url']").attr('data-err')
							},
							onkeyup: false,
							onclick: false,
							onfocusout: false,
							errorPlacement: function (error, element) {
								var $parent = element.parent();
								if (element.attr('name') == 'captcha') {
									error.insertAfter(element.parent().parent());
								} else {
									if ($parent.hasClass('input-group')) {
										error.insertAfter(element.parent());
									} else {
										error.insertAfter(element);
									}
								}
								if ($parent.hasClass('input-group')) {
									var $input_group = $parent.parent();
									$input_group.addClass('has-error');
								} else {
									if (element.attr('name') != 'captcha') {
										$parent.addClass('has-error');
									} else {
										$parent.parent().parent().addClass('has-error');
									}
								}
							},
							success: function (label) {
								var $parent = pjQ.$(label).parent(),
									$sibling = pjQ.$(label).siblings();
								if ($sibling.attr('name') == 'captcha') {
									$parent.parent().parent().removeClass('has-error').addClass('has-success');
								} else {
									$parent.removeClass('has-error').addClass('has-success');
								}
								pjQ.$(label).remove();
							},
							submitHandler: function (form) {
								self.disableButtons.call(self);
								var $form = pjQ.$(form);
								pjQ.$.post([self.options.folder, "index.php?controller=pjFrontPublic&action=pjActionProfile", "&session_id=", self.options.session_id].join(""), $form.serialize()).done(function (data) {
									if (data.status == "OK") {
										$form
											.find(".scSelectorNoticeMsg")
											.html(data.text)
											.removeClass("alert-warning")
											.addClass("alert-success")
											.show();
									} else if (data.status == "ERR") {
										$form
											.find(".scSelectorNoticeMsg")
											.html(data.text)
											.removeClass("alert-success")
											.addClass("alert-warning")
											.show();
									}
									self.enableButtons.call(self);
								}).fail(function () {
									self.enableButtons.call(self);
								});
								return false;
							}
						});
					}
				}
			});
		},
		getRegister: function () {
			var self = this;
			pjQ.$.get([this.options.folder, "index.php?controller=pjFrontPublic&action=pjActionRegister"].join(""), {
				"locale": this.options.locale,
				"hide": this.options.hide,
				"layout": this.options.layout,
				"theme": this.options.theme,
				"session_id": this.options.session_id
			}).done(function (data) {
				self.$container.html(data);

				if (validate) {
					if (self.options.layout != '3') {
						var $form = self.$container.find(".scSelectorRegisterForm");
						$form.validate({
							rules: {
								"email": {
									required: true,
									email: true
								},
								"password": "required",
								"captcha": {
									required: true,
									minlength: 6,
									maxlength: 6,
									remote: self.options.folder + "index.php?controller=pjFront&action=pjActionCheckCaptcha&session_id=" + self.options.session_id
								}
							},
							messages: {
								"email": {
									required: $form.find("input[name='email']").attr('data-err'),
									email: $form.find("input[name='email']").attr('data-email')
								},
								"password": $form.find("input[name='password']").attr('data-err'),
								"client_name": $form.find("input[name='client_name']").attr('data-err'),
								"phone": $form.find("input[name='phone']").attr('data-err'),
								"url": $form.find("input[name='url']").attr('data-err'),
								"captcha": {
									required: $form.find("input[name='captcha']").attr('data-err'),
									remote: $form.find("input[name='captcha']").attr('data-captcha')
								}
							},
							onkeyup: false,
							onclick: false,
							onfocusout: false,
							errorClass: "scError",
							validClass: "scValid",
							errorPlacement: function (error, element) {
								error.insertAfter(element);
							},
							submitHandler: function (form) {
								self.disableButtons.call(self);

								var $form = pjQ.$(form);
								pjQ.$.post([self.options.folder, "index.php?controller=pjFrontPublic&action=pjActionRegister", "&session_id=", self.options.session_id].join(""), $form.serialize()).done(function (data) {
									if (data.status == "OK") {
										$form
											.find(".scSelectorNoticeMsg")
											.html(data.text)
											.removeClass("scNoticeError")
											.addClass("scNoticeSuccess")
											.prepend(pjQ.$("<div>").addClass("scNoticeIcon"))
											.show();
										$form.find(":input").not(":button, :submit, :reset, :hidden").val("").removeAttr("checked").removeAttr("selected");
										var $captcha = $form.find(".scSelectorCaptcha").eq(0);
										$captcha.attr("src", $captcha.attr("src").replace(/(&rand=)\d+/g, '\$1' + Math.ceil(Math.random() * 99999)));
									} else if (data.status == "ERR") {
										$form
											.find(".scSelectorNoticeMsg")
											.html(data.text)
											.removeClass("scNoticeSuccess")
											.addClass("scNoticeError")
											.prepend(pjQ.$("<div>").addClass("scNoticeIcon"))
											.show();
									}
									self.enableButtons.call(self);
								}).fail(function () {
									self.enableButtons.call(self);
								});

								return false;
							}
						});
					} else {
						var $form = self.$container.find(".scSelectorRegisterForm");
						$form.validate({
							rules: {
								"email": {
									required: true,
									email: true
								},
								"password": "required",
								"captcha": {
									required: true,
									minlength: 6,
									maxlength: 6,
									remote: self.options.folder + "index.php?controller=pjFront&action=pjActionCheckCaptcha&session_id=" + self.options.session_id
								}
							},
							messages: {
								"email": {
									required: $form.find("input[name='email']").attr('data-err'),
									email: $form.find("input[name='email']").attr('data-email')
								},
								"password": $form.find("input[name='password']").attr('data-err'),
								"client_name": $form.find("input[name='client_name']").attr('data-err'),
								"phone": $form.find("input[name='phone']").attr('data-err'),
								"url": $form.find("input[name='url']").attr('data-err'),
								"captcha": {
									required: $form.find("input[name='captcha']").attr('data-err'),
									remote: $form.find("input[name='captcha']").attr('data-captcha')
								}
							},
							onkeyup: false,
							onclick: false,
							onfocusout: false,
							errorPlacement: function (error, element) {
								var $parent = element.parent();
								if (element.attr('name') == 'captcha') {
									error.insertAfter(element.parent().parent());
								} else {
									if ($parent.hasClass('input-group')) {
										error.insertAfter(element.parent());
									} else {
										error.insertAfter(element);
									}
								}
								if ($parent.hasClass('input-group')) {
									var $input_group = $parent.parent();
									$input_group.addClass('has-error');
								} else {
									if (element.attr('name') != 'captcha') {
										$parent.addClass('has-error');
									} else {
										$parent.parent().parent().addClass('has-error');
									}
								}
							},
							success: function (label) {
								var $parent = pjQ.$(label).parent(),
									$sibling = pjQ.$(label).siblings();
								if ($sibling.attr('name') == 'captcha') {
									$parent.parent().parent().removeClass('has-error').addClass('has-success');
								} else {
									$parent.removeClass('has-error').addClass('has-success');
								}
								pjQ.$(label).remove();
							},
							submitHandler: function (form) {
								self.disableButtons.call(self);

								var $form = pjQ.$(form);
								pjQ.$.post([self.options.folder, "index.php?controller=pjFrontPublic&action=pjActionRegister", "&session_id=", self.options.session_id].join(""), $form.serialize()).done(function (data) {
									if (data.status == "OK") {
										$form
											.find(".scSelectorNoticeMsg")
											.html(data.text)
											.removeClass("alert-warning")
											.addClass("alert-success")
											.show();
										$form.find("input[name='email']").val("");
										$form.find("input[name='password']").val("");
										$form.find("input[name='client_name']").val("");
										$form.find("input[name='phone']").val("");
										$form.find("input[name='url']").val("");
										$form.find("input[name='captcha']").val("").removeData("previousValue");
										var $captcha = $form.find(".scSelectorCaptcha").eq(0);
										$captcha.attr("src", $captcha.attr("src").replace(/(&rand=)\d+/g, '\$1' + Math.ceil(Math.random() * 99999)));
									} else if (data.status == "ERR") {
										$form
											.find(".scSelectorNoticeMsg")
											.html(data.text)
											.removeClass("alert-success")
											.addClass("alert-warning")
											.show();
									}
									self.enableButtons.call(self);
								}).fail(function () {
									self.enableButtons.call(self);
								});

								return false;
							}
						});
					}
				}
			});
		},
		disableButtons: function () {
			this.$container.find(".scSelectorButton").attr("disabled", "disabled");
		},
		enableButtons: function () {
			this.$container.find(".scSelectorButton").removeAttr("disabled");
		},
		addToFavs: function () {
			var self = this,
				qs = this.buildQueryString.call(this);
			if (!qs) {
				log("Stock Id not set");
				pjQ.$("#scTermModal").modal();
				return;
			}
			this.disableButtons.call(this);
			pjQ.$.post([this.options.folder, "index.php?controller=pjFrontFavs&action=pjActionAdd", "&session_id=", self.options.session_id].join(""), qs).done(function (data) {
				var prefix = (self.options.pagePrefix) ? self.options.pagePrefix + "-" : "";
				self.hashBang("/" + prefix + "favorites");
			}).fail(function () {
				self.enableButtons.call(self);
			});
		},
		addFavFromProductsList: function ($form, $pid) {
			var self = this,
				qs = $form.serialize();
			if (!qs) {
				log("Stock Id not set");
				pjQ.$("#scTermModal").modal();
				return;
			}
			self.disableButtons.call(self);
			pjQ.$.post([this.options.folder, "index.php?controller=pjFrontFavs&action=pjActionAdd", "&session_id=", self.options.session_id].join(""), qs).done(function (data) {
				pjQ.$('#scProductGlyphiconFavs_' + $pid).closest('.scButtonAdd2Favs').removeClass('scSelectorAddFavFromProductsList');
				pjQ.$('#scProductGlyphiconFavs_' + $pid).closest('.scButtonAdd2Favs').addClass('scSelectorRemoveFavFromProductsList');
				pjQ.$('#scProductGlyphiconFavs_' + $pid).removeClass('glyphicon-heart-empty');
				pjQ.$('#scProductGlyphiconFavs_' + $pid).addClass('glyphicon-heart');
				self.enableButtons.call(self);
			}).fail(function () {
				self.enableButtons.call(self);
			});
		},
		removeFromFavs: function (hash) {
			var self = this;
			this.disableButtons.call(this);
			pjQ.$.post([this.options.folder, "index.php?controller=pjFrontFavs&action=pjActionRemove", "&session_id=", self.options.session_id].join(""), {
				"hash": hash
			}).done(function (data) {
				self.viewFavs.call(self);
			}).fail(function () {
				self.enableButtons.call(self);
			});
		},
		emptyFavs: function () {
			var self = this;
			this.disableButtons.call(this);
			pjQ.$.post([this.options.folder, "index.php?controller=pjFrontFavs&action=pjActionEmpty", "&session_id=", self.options.session_id].join("")).done(function (data) {
				self.viewFavs.call(self);
			}).fail(function () {
				self.enableButtons.call(self);
			});
		},
		viewFavs: function () {
			var self = this;
			pjQ.$.get([this.options.folder, "index.php?controller=pjFrontPublic&action=pjActionFavs"].join(""), {
				"locale": this.options.locale,
				"hide": this.options.hide,
				"layout": this.options.layout,
				"theme": this.options.theme,
				"session_id": this.options.session_id
			}).done(function (data) {
				self.$container.html(data);
			});
		},
		checkFavs: function () {
			var self = this,
				qs = this.buildQueryString.call(this);

			if (!qs) {
				self.$container.find(".scButtonAdd2Favs").removeClass("scButtonAdd2FavsIn");
			} else {
				pjQ.$.post([this.options.folder, "index.php?controller=pjFrontFavs&action=pjActionCheck"].join(""), qs).done(function (data) {
					switch (data.status) {
						case "OK":
							self.$container.find(".scButtonAdd2Favs").addClass("scButtonAdd2FavsIn");
							break;
						case "ERR":
							self.$container.find(".scButtonAdd2Favs").removeClass("scButtonAdd2FavsIn");
							break;
					}
				});
			}
		},
		removeCode: function () {
			var self = this;
			this.disableButtons.call(this);
			pjQ.$.get([this.options.folder, "index.php?controller=pjFrontCart&action=pjActionRemoveCode", "&session_id=", self.options.session_id].join("")).done(function (data) {
				self.viewCart.call(self);
			}).fail(function () {
				self.enableButtons.call(self);
			});
		},
		buildQueryString: function () {
			var m, $el, qs, i, iCnt, productObj = {},
				$form = this.$container.find(".scSelectorProductForm"),
				attr = $form.find(".scSelectorAttr").serializeArray(),
				qs = $form.serialize();

			for (i = 0, iCnt = attr.length; i < iCnt; i++) {
				m = attr[i].name.match(/attr\[(\d+)\]/);
				productObj[m[1]] = attr[i].value;
			}

			for (i = 0, iCnt = this.stockObj.length; i < iCnt; i++) {
				if (ShoppingCart.compare(productObj, this.stockObj[i])) {
					qs += "&stock_id=" + this.stockIds[i];
					return qs;
					break;
				}
			}

			// Apply only if product have not attributes
			if (this.stockObj.length === 0 && this.stockIds[0]) {
				return [qs, "&stock_id=", this.stockIds[0]].join("");
			}

			return false;
		},
		getAddress: function (el) {
			var self = this,
				$el = pjQ.$(el),
				address_id = $el.find("option:selected").val(),
				elName = $el.attr("name");
			this.disableButtons.call(this);
			pjQ.$.get([this.options.folder, "index.php?controller=pjFrontCart&action=pjActionGetAddress", "&session_id=", self.options.session_id].join(""), {
				"id": address_id
			}).done(function (data) {
				var $form = self.$container.find(".scSelectorCheckoutForm");
				if (data.status === "OK") {
					data = data.result;
					if (elName == 'b_address_id') {
						$form.find("input[name='b_name']").val(data.name).attr("data-original", data.name);
						$form.find("select[name='b_country_id']").val(data.country_id).attr("data-original", data.country_id);
						$form.find("input[name='b_state']").val(data.state).attr("data-original", data.state);
						$form.find("input[name='b_city']").val(data.city).attr("data-original", data.city);
						$form.find("input[name='b_zip']").val(data.zip).attr("data-original", data.zip);
						$form.find("input[name='b_address_1']").val(data.address_1).attr("data-original", data.address_1);
						$form.find("input[name='b_address_2']").val(data.address_2).attr("data-original", data.address_2);

						pjQ.$(window).trigger("reloadAddress", { type: "billing" });
						self.$container.find(".scSelectorSaveB").hide();
					} else if (elName == 's_address_id') {
						$form.find("input[name='s_name']").val(data.name).attr("data-original", data.name);
						$form.find("select[name='s_country_id']").val(data.country_id).attr("data-original", data.country_id);
						$form.find("input[name='s_state']").val(data.state).attr("data-original", data.state);
						$form.find("input[name='s_city']").val(data.city).attr("data-original", data.city);
						$form.find("input[name='s_zip']").val(data.zip).attr("data-original", data.zip);
						$form.find("input[name='s_address_1']").val(data.address_1).attr("data-original", data.address_1);
						$form.find("input[name='s_address_2']").val(data.address_2).attr("data-original", data.address_2);
						$form.find("input[name='same_as']").prop("checked", false);
						$form.find(".scSelectorBoxShipping").show();

						pjQ.$(window).trigger("reloadAddress", { type: "shipping" });
						self.$container.find(".scSelectorSaveS").hide();
					}
				} else {
					if (elName == 'b_address_id') {
						$form.find("input[name='b_name'], select[name='b_country_id'], input[name='b_state'], input[name='b_city'], input[name='b_zip'], input[name='b_address_1'], input[name='b_address_2']").val("");

						pjQ.$(window).trigger("reloadAddress", { type: "billing" });
						self.$container.find(".scSelectorSaveB").show();
					} else if (elName == 's_address_id') {
						$form.find("input[name='s_name'], select[name='s_country_id'], input[name='s_state'], input[name='s_city'], input[name='s_zip'], input[name='s_address_1'], input[name='s_address_2']").val("");
						$form.find("input[name='same_as']").prop("checked", false);
						$form.find(".scSelectorBoxShipping").show();

						pjQ.$(window).trigger("reloadAddress", { type: "shipping" });
						self.$container.find(".scSelectorSaveS").show();
					}
				}
			}).always(function () {
				self.enableButtons.call(self);
			});
		},
		addToCart: function (qs) {
			var self = this;
			this.disableButtons.call(this);
			pjQ.$.post([this.options.folder, "index.php?controller=pjFrontCart&action=pjActionAdd", "&session_id=", self.options.session_id].join(""), qs).done(function (data) {
				var prefix = (self.options.pagePrefix) ? self.options.pagePrefix + "-" : "";
				self.hashBang("/bookings/" + prefix + "cart");
			}).fail(function () {
				self.enableButtons.call(self);
			});
		},
		removeFromCart: function (hash) {
			var self = this;
			this.disableButtons.call(this);
			pjQ.$.post([this.options.folder, "index.php?controller=pjFrontCart&action=pjActionRemove", "&session_id=", self.options.session_id].join(""), {
				"hash": hash
			}).done(function (data) {
				self.viewCart.call(self);
			}).fail(function () {
				self.enableButtons.call(self);
			});
		},
		emptyCart: function () {
			var self = this;
			this.disableButtons.call(this);
			pjQ.$.post([this.options.folder, "index.php?controller=pjFrontCart&action=pjActionEmpty", "&session_id=", self.options.session_id].join("")).done(function (data) {
				self.viewCart.call(self);
			}).fail(function () {
				self.enableButtons.call(self);
			});
		},
		updateCart: function () {
			var self = this;
			this.disableButtons.call(this);
			pjQ.$.post([this.options.folder, "index.php?controller=pjFrontCart&action=pjActionUpdate", "&session_id=", self.options.session_id].join(""), this.$container.find(":input[name^='qty'], select[name='tax_id']").serialize()).done(function (data) {
				self.viewCart.call(self);
			}).fail(function () {
				self.enableButtons.call(self);
			});
		},
		viewCart: function () {
			var self = this;
			pjQ.$.get([this.options.folder, "index.php?controller=pjFrontPublic&action=pjActionCart", "&session_id=", self.options.session_id].join(""), {
				"locale": this.options.locale,
				"hide": this.options.hide,
				"layout": this.options.layout,
				"theme": this.options.theme
			}).done(function (data) {
				self.$container.html(data);
				if (validate) {
					var $form = self.$container.find(".scSelectorVoucherForm");
					$form.validate({
						rules: {
							"code": "required"
						},
						messages: {
							"code": $form.find("input[name='code']").attr('data-err')
						},
						onkeyup: false,
						onclick: false,
						onfocusout: false,
						errorClass: "scError",
						validClass: "scValid",
						submitHandler: function (form) {
							self.disableButtons.call(self);
							var $form = pjQ.$(form);
							pjQ.$.post([self.options.folder, "index.php?controller=pjFrontCart&action=pjActionApplyCode", "&session_id=", self.options.session_id].join(""), $form.serialize()).done(function (data) {
								if (data.status == "OK") {
									self.viewCart.call(self);
								} else if (data.status == "ERR") {
									$form
										.find(".scSelectorNoticeMsg")
										.html(data.text)
										.removeClass("scNoticeSuccess")
										.addClass("scNoticeError")
										.prepend(pjQ.$("<div>").addClass("scNoticeIcon"))
										.show();
									self.enableButtons.call(self);
								}
							}).fail(function () {
								self.enableButtons.call(self);
							});
							return false;
						}
					});
					var $form = self.$container.find(".scSelectorCartForm");
					$form.validate({
						messages: {
							"tax_id": $form.find("select[name='tax_id']").attr('data-err')
						},
						onkeyup: false,
						onclick: false,
						onfocusout: false,
						errorClass: "scError",
						validClass: "scValid",
						submitHandler: function (form) {
							self.disableButtons.call(self);

							var prefix = (self.options.pagePrefix) ? self.options.pagePrefix + "-" : "";
							self.hashBang("/bookings/" + prefix + "checkout");
							return false;
						}
					});
				}
			});
		},
		checkoutCart: function () {
			var self = this;
			this.disableButtons.call(this);
			pjQ.$.get([this.options.folder, "index.php?controller=pjFrontPublic&action=pjActionCheckout"].join(""), {
				"locale": this.options.locale,
				"hide": this.options.hide,
				"layout": this.options.layout,
				"theme": this.options.theme,
				"session_id": this.options.session_id
			}).done(function (data) {
				self.$container.html(data);
				pjQ.$('.modal-dialog').css("z-index", "9999");
				pjQ.$(window).trigger("reloadAddress", { type: "billing" });
				pjQ.$(window).trigger("reloadAddress", { type: "shipping" });

				if (validate) {
					var $reCaptcha = self.$container.find('#g-recaptcha');
					if ($reCaptcha.length > 0) {
						grecaptcha.render($reCaptcha.attr('id'), {
							sitekey: $reCaptcha.data('sitekey'),
							callback: function (response) {
								var elem = pjQ.$("input[name='recaptcha']");
								elem.val(response);
								elem.valid();
							}
						});
					}

					if (self.options.layout != '3') {
						var $form = self.$container.find(".scSelectorCheckoutForm");
						$form.validate({
							rules: {
								"captcha": {
									remote: self.options.folder + "index.php?controller=pjFront&action=pjActionCheckCaptcha&session_id=" + self.options.session_id,
									required: true
								},
								"recaptcha": {
									remote: self.options.folder + "index.php?controller=pjFront&action=pjActionCheckReCaptcha&session_id=" + self.options.session_id
								}
							},
							messages: {
								"b_name": $form.find("input[name='b_name']").attr('data-err'),
								"b_country_id": $form.find("select[name='b_country_id']").attr('data-err'),
								"b_city": $form.find("input[name='b_city']").attr('data-err'),
								"b_state": $form.find("input[name='b_state']").attr('data-err'),
								"b_zip": $form.find("input[name='b_zip']").attr('data-err'),
								"b_address_1": $form.find("input[name='b_address_1']").attr('data-err'),
								"b_address_2": $form.find("input[name='b_address_2']").attr('data-err'),
								"s_name": $form.find("input[name='s_name']").attr('data-err'),
								"s_country_id": $form.find("select[name='s_country_id']").attr('data-err'),
								"s_city": $form.find("input[name='s_city']").attr('data-err'),
								"s_state": $form.find("input[name='s_state']").attr('data-err'),
								"s_zip": $form.find("input[name='s_zip']").attr('data-err'),
								"s_address_1": $form.find("input[name='s_address_1']").attr('data-err'),
								"s_address_2": $form.find("input[name='s_address_2']").attr('data-err'),
								"client_name": $form.find("input[name='client_name']").attr('data-err'),
								"phone": $form.find("input[name='phone']").attr('data-err'),
								"url": $form.find("input[name='url']").attr('data-err'),
								"payment_method": $form.find("select[name='payment_method']").attr('data-err'),
								"notes": $form.find("textarea[name='notes']").attr('data-err'),
								"email": {
									required: $form.find("input[name='email']").attr('data-err'),
									email: $form.find("input[name='email']").attr('data-email')
								},
								"password": $form.find("input[name='password']").attr('data-err'),
								"captcha": {
									remote: $form.find("input[name='captcha']").attr('data-captcha'),
									required: $form.find("input[name='captcha']").attr('data-err'),
								},
								"terms": $form.find("input[name='terms']").attr('data-err')
							},
							ignore: ":hidden",
							onkeyup: false,
							onclick: false,
							onfocusout: false,
							errorClass: "scError",
							validClass: "scValid",
							submitHandler: function (form) {
								self.disableButtons.call(self);
								var $form = pjQ.$(form);
								pjQ.$.post([self.options.folder, "index.php?controller=pjFrontPublic&action=pjActionCheckout", "&session_id=", self.options.session_id].join(""), $form.serialize()).done(function (data) {
									if (data.status == "OK") {
										var prefix = (self.options.pagePrefix) ? self.options.pagePrefix + "-" : "";
										self.hashBang("/bookings/" + prefix + "preview");
									} else if (data.status == "ERR") {
										$form
											.find(".scSelectorNoticeMsg")
											.html(data.text)
											.removeClass("scNoticeSuccess")
											.addClass("scNoticeError")
											.prepend(pjQ.$("<div>").addClass("scNoticeIcon"))
											.show();
										self.enableButtons.call(self);
									}
								}).fail(function () {
									self.enableButtons.call(self);
								});
								return false;
							}
						});
					} else {
						var $form = self.$container.find(".scSelectorCheckoutForm");
						$form.validate({
							rules: {
								"captcha": {
									remote: self.options.folder + "index.php?controller=pjFront&action=pjActionCheckCaptcha&session_id=" + self.options.session_id,
									required: true
								},
								"recaptcha": {
									remote: self.options.folder + "index.php?controller=pjFront&action=pjActionCheckReCaptcha&session_id=" + self.options.session_id
								}
							},
							messages: {
								"b_name": $form.find("input[name='b_name']").attr('data-err'),
								"b_country_id": $form.find("select[name='b_country_id']").attr('data-err'),
								"b_city": $form.find("input[name='b_city']").attr('data-err'),
								"b_state": $form.find("input[name='b_state']").attr('data-err'),
								"b_zip": $form.find("input[name='b_zip']").attr('data-err'),
								"b_address_1": $form.find("input[name='b_address_1']").attr('data-err'),
								"b_address_2": $form.find("input[name='b_address_2']").attr('data-err'),
								"s_name": $form.find("input[name='s_name']").attr('data-err'),
								"s_country_id": $form.find("select[name='s_country_id']").attr('data-err'),
								"s_city": $form.find("input[name='s_city']").attr('data-err'),
								"s_state": $form.find("input[name='s_state']").attr('data-err'),
								"s_zip": $form.find("input[name='s_zip']").attr('data-err'),
								"s_address_1": $form.find("input[name='s_address_1']").attr('data-err'),
								"s_address_2": $form.find("input[name='s_address_2']").attr('data-err'),
								"client_name": $form.find("input[name='client_name']").attr('data-err'),
								"phone": $form.find("input[name='phone']").attr('data-err'),
								"url": $form.find("input[name='url']").attr('data-err'),
								"payment_method": $form.find("select[name='payment_method']").attr('data-err'),
								"notes": $form.find("textarea[name='notes']").attr('data-err'),
								"email": {
									required: $form.find("input[name='email']").attr('data-err'),
									email: $form.find("input[name='email']").attr('data-email')
								},
								"password": $form.find("input[name='password']").attr('data-err'),
								"captcha": {
									remote: $form.find("input[name='captcha']").attr('data-captcha'),
									required: $form.find("input[name='captcha']").attr('data-err'),
								},
								"terms": $form.find("input[name='terms']").attr('data-err')
							},
							ignore: ":hidden",
							onkeyup: false,
							onclick: false,
							onfocusout: false,
							errorPlacement: function (error, element) {
								var $parent = element.parent(),
									$input_group = $parent.parent();
								if (element.attr('name') == 'terms') {
									error.insertAfter(element.parent());
								} else {
									error.insertAfter(element);
								}
								if (element.attr('name') == 'captcha') {
									$input_group.parent().addClass('has-error');
								} else {
									$parent.addClass('has-error');
								}
							},
							success: function (label) {
								var $parent = pjQ.$(label).parent(),
									$sibling = pjQ.$(label).siblings();
								if ($sibling.attr('name') == 'captcha') {
									$parent.parent().parent().removeClass('has-error').addClass('has-success');
								} else {
									$parent.removeClass('has-error').addClass('has-success');
								}
								pjQ.$(label).remove();
							},
							submitHandler: function (form) {
								self.disableButtons.call(self);
								var $form = pjQ.$(form);
								pjQ.$.post([self.options.folder, "index.php?controller=pjFrontPublic&action=pjActionCheckout", "&session_id=", self.options.session_id].join(""), $form.serialize()).done(function (data) {
									if (data.status == "OK") {
										var prefix = (self.options.pagePrefix) ? self.options.pagePrefix + "-" : "";
										self.hashBang("/bookings/" + prefix + "preview");
									} else if (data.status == "ERR") {
										$form
											.find(".scSelectorNoticeMsg")
											.html(data.text)
											.removeClass("alert-success")
											.addClass("alert-warning")
											.show();
										self.enableButtons.call(self);
									}
								}).fail(function () {
									self.enableButtons.call(self);
								});
								return false;
							}
						});
					}
				}

			}).fail(function () {
				self.enableButtons.call(self);
			});
		},
		previewOrder: function () {
			var self = this;
			pjQ.$.get([this.options.folder, "index.php?controller=pjFrontPublic&action=pjActionPreview"].join(""), {
				"locale": this.options.locale,
				"hide": this.options.hide,
				"layout": this.options.layout,
				"theme": this.options.theme,
				"session_id": this.options.session_id
			}).done(function (data) {
				self.$container.html(data);

				if (validate) {
					self.$container.find(".scSelectorPreviewForm").validate({
						rules: {},
						onkeyup: false,
						onclick: false,
						onfocusout: false,
						errorClass: "scError",
						validClass: "scValid",
						submitHandler: function (form) {
							self.disableButtons.call(self);
							pjQ.$('.scRefreshIcon').css('display', 'inline-block');
							var $form = pjQ.$(form);
							pjQ.$.post([self.options.folder, "index.php?controller=pjFrontCart&action=pjActionProcessOrder", "&session_id=", self.options.session_id].join(""), $form.serialize()).done(function (data) {
								if (data.status == "OK") {
									self.getPaymentForm.call(self, data);
								} else if (data.status == "ERR") {
									if (self.options.layout != '3') {
										$form
											.find(".scSelectorNoticeMsg")
											.html(data.text)
											.removeClass("scNoticeSuccess")
											.addClass("scNoticeError")
											.prepend(pjQ.$("<div>").addClass("scNoticeIcon"))
											.show();
										pjQ.$('.scRefreshIcon').css('display', 'none');
									} else {
										$form
											.find(".scSelectorNoticeMsg")
											.html(data.text)
											.removeClass("alert-success")
											.addClass("alert-warning")
											.show();
									}

									self.enableButtons.call(self);
								}
							}).fail(function () {
								self.enableButtons.call(self);
								pjQ.$('.scRefreshIcon').css('display', 'none');
							});
							return false;
						}
					});
				}
			});
		},
		getPaymentForm: function (obj) {
			var self = this;
			pjQ.$.get([this.options.folder, "index.php?controller=pjFrontCart&action=pjActionGetPaymentForm", "&session_id=", self.options.session_id].join(""), {
				"locale": this.options.locale,
				"hide": this.options.hide,
				"layout": this.options.layout,
				"theme": this.options.theme,
				"order_id": obj.order_id,
				"invoice_id": obj.invoice_id,
				"payment_method": obj.payment_method
			}).done(function (data) {
				self.$container.html(data);
				switch (obj.payment_method) {
					case 'paypal':
						self.$container.find("form[name='scPaypal']").trigger('submit');
						break;
					case 'authorize':
						self.$container.find("form[name='scAuthorize']").trigger('submit');
						break;
					case 'creditcard':
					case 'bank':
					case 'cod':
						break;
				}
			}).fail(function () {
				log("Deferred is rejected");
			});
		},
		priceStock: function () {
			var m, $el, qs, i, iCnt, j, productObj = {}, $qty,
				$thumb, src, href,
				$form = this.$container.find(".scSelectorProductForm"),
				attr = $form.find(".scSelectorAttr").serializeArray();

			this.canBuy = true;
			for (i = 0, iCnt = attr.length; i < iCnt; i++) {
				m = attr[i].name.match(/attr\[(\d+)\]/);
				productObj[m[1]] = attr[i].value;
				if (attr[i].value == '') {
					this.canBuy = false;
				}
			}

			for (i = 0, iCnt = this.stockObj.length; i < iCnt; i++) {
				if (ShoppingCart.compare(this.stockObj[i], productObj)) {
					this.priceStocks = parseFloat(this.priceObj[i]);
					this.unitPrice = this.priceStocks;
					// Change pic
					$thumb = this.$container.find(".scSelectorStockThumb[data-stock_id='" + this.stockIds[i] + "']");
					if ($thumb.length > 0) {
						src = $thumb.data("src");
						href = $thumb.data("large");
						if (src !== undefined && src.length > 0) {
							this.$container.find(".scSelectorProductPic").attr("src", src).parent("a.scSelectorFancy").attr("href", href);
						}
					}
					// Set qty attrs
					$qty = this.$container.find(":input[name='qty']");
					if ($qty.length > 0) {
						switch ($qty.get(0).nodeName) {
							case 'INPUT':
								$qty.val(1)
									.data("max", this.qtyObj[i])
									.attr("data-max", this.qtyObj[i])
									.attr("maxlength", this.qtyObj[i].length);
								break;
							case 'SELECT':
								$qty.empty();
								for (j = 1; j <= this.qtyObj[i]; j++) {
									pjQ.$("<option>")
										.attr("value", j)
										.text(j)
										.appendTo($qty);
								}
								break;
						}
					}

					break;
				}
			}
			// Apply only if product have not attributes
			if (this.stockObj.length === 0 && this.priceObj[0]) {
				this.priceStocks = parseFloat(this.priceObj[0]);
				this.unitPrice = this.priceStocks;
			}

			if (this.canBuy == true) {
				this.setPrice.call(this).showPrice.call(this);
			} else {
				this.$container.find(".scSelectorPrice").html(pjQ.$('.scHiddenMinPrice').html());
			}
		},
		priceExtra: function () {
			var $ele, $selected,
				price = 0;
			this.$container.find(".scSelectorExtra").each(function (i, ele) {
				$ele = pjQ.$(ele);
				switch (ele.nodeName) {
					case 'INPUT':
						if ($ele.is(":checked")) {
							price += parseFloat($ele.data("price"));
						}
						break;
					case 'SELECT':
						$selected = pjQ.$("option:selected", $ele);
						if ($selected) {
							price += parseFloat($selected.data("price"));
						}
						break;
				}
			});
			this.priceExtras = price;
			if (this.canBuy == true) {
				this.setPrice.call(this).showPrice.call(this);
			}
		},
		formatCurrencySign: function (price) {
			var self = this,
				format = '---';

			switch (self.options.currency) {
				case 'USD':
					format = self.options.currencysign + price;
					break;
				case 'GBP':
					format = self.options.currencysign + price;
					break;
				case 'EUR':
					format = self.options.currencysign + price;
					break;
				case 'JPY':
					format = self.options.currencysign + price;
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
					format = price + self.options.currencysign;
					break;
				case 'NOK':
				case 'HUF':
				case 'CZK':
				case 'ILS':
				case 'MXN':
					format = self.options.currencysign + price;
					break;
				default:
					format = price + self.options.currencysign;
					break;
			}
			return format;
		},
		setPrice: function () {
			this.price = parseFloat(this.priceStocks + (this.priceExtras * this.qty)).toFixed(2);
			//this.price = parseFloat(this.priceStocks + this.priceExtras).toFixed(2);
			return this;
		},
		showPrice: function () {
			this.$container.find(".scSelectorPrice").html(this.formatCurrencySign(this.price)).parent().show();
			return this;
		},
		// changeQty: function (el, callback) {
		// 	var self = this,
		// 		$this = pjQ.$(el),
		// 		$qty = $this.siblings(".scSelectorSpinValue"),
		// 		current = parseInt($qty.val(), 10),
		// 		min = parseInt($qty.data("min"), 10),
		// 		max = parseInt($qty.data("max"), 10),
		// 		direction = $this.data("direction"),
		// 		qty = 1,
		// 		hasError = false;
		// 	switch (direction) {
		// 		case "up":
		// 			if (current + 1 <= max) {
		// 				qty = current + 1;
		// 				$qty.val(qty);
		// 				pjQ.$('.scMaximumItems').html("").hide();
		// 			} else {
		// 				qty = max;
		// 				var max_text = pjQ.$('.scMaximumItems').attr('data-text');
		// 				max_text = max_text.replace("{MAX}", max);
		// 				pjQ.$('.scMaximumItems').html(max_text).show();
		// 				hasError = true;
		// 			}
		// 			break;
		// 		case "down":
		// 			if (current - 1 >= min) {
		// 				qty = current - 1;
		// 				$qty.val(qty);
		// 				pjQ.$('.scMaximumItems').html("").hide();
		// 			}
		// 			break;
		// 	}
		// 	self.qty = qty;
		// 	self.priceStocks = qty * self.unitPrice;
		// 	self.setPrice.call(self).showPrice.call(self);
		// 	if (callback !== undefined && !hasError) {
		// 		callback();
		// 	}
		// },
		changeQty: function (el, callback) {
			var self = this,
				$this = pjQ.$(el),
				$row = $this.closest("tr"),
				$maxMsg = $row.find(".scMaximumItems"),
				$qty = $this.siblings(".scSelectorSpinValue"),
				current = parseInt($qty.val(), 10),
				min = parseInt($qty.data("min"), 10),
				max = parseInt($qty.data("max"), 10),
				direction = $this.data("direction"),
				qty = 1,
				hasError = false;

			switch (direction) {
				case "up":
					if (current + 1 <= max) {
						qty = current + 1;
						$qty.val(qty);
						$maxMsg.html("").hide();
					} else {
						qty = max;
						var max_text = $maxMsg.attr("data-text") || "";
						max_text = max_text.replace("{MAX}", max);
						$maxMsg.html(max_text).show();
						hasError = true;
					}
					break;
				case "down":
					if (current - 1 >= min) {
						qty = current - 1;
						$qty.val(qty);
						$maxMsg.html("").hide();
					}
					break;
			}
			self.qty = qty;
			self.priceStocks = qty * self.unitPrice;
			self.setPrice.call(self).showPrice.call(self);
			if (callback !== undefined && !hasError) {
				callback();
			}
		},
		init: function (opts) {
			var self = this;
			this.options = opts;
			this.container = document.getElementById("scContainer_" + this.options.index);
			this.$container = pjQ.$(this.container);
			if (self.options.cid > 0) {
				self.category_id = self.options.cid;
			}

			self.onHashChange.call(self);
			this.$container.on("click.sc", ".scSelectorLocale", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var locale = pjQ.$(this).data("id");
				self.options.locale = locale;
				pjQ.$(this).addClass("scLocaleFocus").parent().parent().find("a.scSelectorLocale").not(this).removeClass("scLocaleFocus");

				pjQ.$.get([self.options.folder, "index.php?controller=pjFront&action=pjActionLocale", "&session_id=", self.options.session_id].join(""), {
					"locale_id": locale
				}).done(function (data) {
					var prefix = (self.options.pagePrefix) ? self.options.pagePrefix + "-" : "";
					if (self.hashBang("/" + prefix + "products/q:" + (self.q && self.q !== undefined ? self.q : "") + "/category:" + (self.category_id && self.category_id !== undefined ? self.category_id : "") + "/page:" + (self.page && self.page !== undefined ? self.page : 1))) {
					} else {
						self.loadProducts.call(self);
					}
				}).fail(function () {
					log("Deferred is rejected");
				});
				return false;
			}).on("mouseenter.sc", ".scSelectorProductItem", function () {
				pjQ.$(this).addClass("scProductItemHover");
			}).on("mouseleave.sc", ".scSelectorProductItem", function () {
				pjQ.$(this).removeClass("scProductItemHover");
			}).on("click.sc", ".scSelectorProduct", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $this = pjQ.$(this),
					product_id = $this.data("id"),
					slug = $this.data("slug");
				if (self.options.seoUrl === 1 && slug.length > 0) {
					self.hashBang("/" + slug);
				} else {
					var prefix = (self.options.pagePrefix) ? self.options.pagePrefix + "-" : "";
					self.hashBang("/" + prefix + "product/" + product_id);
				}
				return false;
			}).on("click.sc", ".scSelectorProducts", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var prefix = (self.options.pagePrefix) ? self.options.pagePrefix + "-" : "";
				self.hashBang("/" + prefix + "products/q:" + (self.q && self.q !== undefined ? self.q : "") + "/category:" + (self.category_id && self.category_id !== undefined ? self.category_id : "") + "/page:" + (self.page && self.page !== undefined ? self.page : 1) + "/sort:" + self.sort);
				return false;
			}).on("click.sc", ".scSelectorPage", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var page = pjQ.$(this).data("page");
				var prefix = (self.options.pagePrefix) ? self.options.pagePrefix + "-" : "";
				self.hashBang("/" + prefix + "products/q:" + (self.q && self.q !== undefined ? self.q : "") + "/category:" + (self.category_id && self.category_id !== undefined ? self.category_id : "") + "/page:" + (page && page !== undefined ? page : 1) + "/sort:" + self.sort);
				return false;
			}).on("click.sc", ".scSelectorSort", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $sort = pjQ.$(this).data("sort");
				self.sort = $sort;
				var prefix = (self.options.pagePrefix) ? self.options.pagePrefix + "-" : "";
				self.hashBang("/" + prefix + "products/q:" + (self.q && self.q !== undefined ? self.q : "") + "/category:" + (self.category_id && self.category_id !== undefined ? self.category_id : "") + "/page:" + (self.page && self.page !== undefined ? self.page : 1) + "/sort:" + self.sort);
				return false;
			}).on("change.sc", ".scSelectorCategoryId", function (e) {
				var category_id = pjQ.$("option:selected", this).val();
				var prefix = (self.options.pagePrefix) ? self.options.pagePrefix + "-" : "";
				self.hashBang("/" + prefix + "products/q:/category:" + category_id + "/page:1");
			}).on("change.sc", ".scSelectorExtra", function (e) {
				self.priceExtra.call(self);
			}).on("change.sc", ".scSelectorAttr", function (e) {
				self.loopAttr.call(self, this);
				self.priceStock.call(self);
				self.checkFavs.call(self);
			}).on("click.sc", ".scSelectorSpin", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				if (pjQ.$(this).hasClass("scCallbackUpdate")) {
					self.changeQty.call(self, this, function () {
						self.updateCart.call(self);
					});
				} else {
					self.changeQty.call(self, this);
				}
				return false;
			}).on("change.sc", ".scSelectorQty", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				if (pjQ.$(this).hasClass("scCallbackUpdate")) {
					self.updateCart.call(self);
				}
				return false;
			}).on("click.sc", ".scSelectorAdd2Cart", function (e) {
				var $form = self.$container.find(".scSelectorProductForm");
				if ($form.valid()) {
					var qs = self.buildQueryString.call(self);
					if (!qs) {
						log("Stock Id not set");
						return;
					}
					self.addToCart.call(self, qs);
				}
			}).on("submit.sc", ".scSelectorBuyNowForm", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.addToCart.call(self, pjQ.$(this).serialize());
				return false;
			}).on("submit.sc", ".scSelectorProductForm", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				return false;
			}).on("click.sc", ".scSelectorProductThumb", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $this = pjQ.$(this),
					src = $this.data("src"),
					href = $this.data("large");
				if (src !== undefined && src.length > 0) {
					self.$container.find(".scSelectorProductPic").attr("src", src).parent("a.scSelectorFancy").attr("href", href);
				}
				return false;
			}).on("click.sc", ".scSelectorFancy", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $this = pjQ.$(this),
					href = $this.attr("href");
				self.$container.find("a[rel='fancy_group'][href='" + href + "']").trigger("click");
				return false;
			}).on("click.sc", ".scSelectorAdd2Favs", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.addToFavs.call(self);
				return false;
			}).on("click.sc", ".scSelectorAddFavFromProductsList", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $pid = pjQ.$(this).attr('data-id'),
					$form = pjQ.$('#scSelectorProductForm_' + $pid);
				self.addFavFromProductsList.call(self, $form, $pid);
				return false;
			}).on("click.sc", ".scSelectorRemoveFavFromProductsList", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $this = pjQ.$(this),
					$hash = $this.attr('data-hash');
				self.disableButtons.call(self);
				pjQ.$.post([self.options.folder, "index.php?controller=pjFrontFavs&action=pjActionRemove", "&session_id=", self.options.session_id].join(""), {
					"hash": $hash
				}).done(function (data) {
					if (data.status == 'OK') {
						$this.removeClass('scSelectorRemoveFavFromProductsList');
						$this.addClass('scSelectorAddFavFromProductsList');
						$this.find('.glyphicon').removeClass('glyphicon-heart').addClass('glyphicon-heart-empty');
					}
					self.enableButtons.call(self);
				}).fail(function () {
					self.enableButtons.call(self);
				});
				return false;
			}).on("click.sc", ".scSelectorSend2Friend", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $target = pjQ.$(this).attr('data-pj-target');
				self.$container.find("." + $target).toggle();
				return false;
			}).on("click.sc", ".scSelectorSend2FriendCancel", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $target = pjQ.$(this).attr('data-pj-target');
				self.$container.find("." + $target).hide();
				return false;
			}).on("click.sc", ".pjSelectorFavorites", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var prefix = (self.options.pagePrefix) ? self.options.pagePrefix + "-" : "";
				self.hashBang("/" + prefix + "favorites");
				return false;
			}).on("click.sc", ".scSelectorViewCart", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var prefix = (self.options.pagePrefix) ? self.options.pagePrefix + "-" : "";
				self.hashBang("/bookings/" + prefix + "cart");
				return false;

			}).on("change.sc", ".scSelectorOriginalB", function (e) {
				if (self.$container.find(".scSelectorAddressId").length > 0) {
					pjQ.$(window).trigger("compareAddress", {
						"type": "billing"
					});
				}
			}).on("change.sc", ".scSelectorOriginalS", function (e) {
				if (self.$container.find(".scSelectorAddressId").length > 0) {
					pjQ.$(window).trigger("compareAddress", {
						"type": "shipping"
					});
				}
			}).on("mouseenter.sc", ".scSelectorRemoveFromFavs, .scSelectorRemoveFromCart, .scSelectorEyeProduct", function (e) {
				var $img = pjQ.$(this).children("img");
				if ($img.length > 0) {
					$img.attr("src", $img.data("src"));
				}
			}).on("mouseleave.sc", ".scSelectorRemoveFromFavs, .scSelectorRemoveFromCart, .scSelectorEyeProduct", function (e) {
				var $img = pjQ.$(this).children("img");
				if ($img.length > 0) {
					$img.attr("src", $img.data("original"));
				}

				// Favs
			}).on("click.ac", ".scSelectorRemoveFromFavs", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.removeFromFavs.call(self, pjQ.$(this).data("hash"));
				return false;
			}).on("click.sc", ".scSelectorEmptyFavs", function (e) {
				self.emptyFavs.call(self);
			}).on("click.sc", ".scSelectorFav2Cart", function (e) {
				self.addToCart.call(self, pjQ.$(this).closest("form").serialize());

				// Cart (Basket)
			}).on("click.sc", ".scSelectorContinueShopping", function (e) {
				var prefix = (self.options.pagePrefix) ? self.options.pagePrefix + "-" : "";
				self.hashBang("/" + prefix + "products/q:" + (self.q && self.q !== undefined ? self.q : "") + "/category:" + (self.category_id && self.category_id !== undefined ? self.category_id : "") + "/page:" + (self.page && self.page !== undefined ? self.page : 1));
			}).on("click.sc", ".scSelectorEmptyCart", function (e) {
				self.emptyCart.call(self);
			}).on("click.sc", ".scSelectorUpdateCart", function (e) {
				self.updateCart.call(self);
			}).on("click.sc", ".scSelectorRemoveFromCart", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.removeFromCart.call(self, pjQ.$(this).data("hash"));
				return false;
			}).on("click.sc", ".scSelectorCheckout", function (e) {
				self.$container.find(".scSelectorCartForm").trigger("submit");
				// var prefix = (self.options.pagePrefix) ? self.options.pagePrefix + "-" : "";
				//self.hashBang("/bookings/" + prefix + "checkout");
			}).on("click.sc", ".scSelectorTerms", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				if (dialog) {
					var id = 'scSelectorTerms_' + self.options.index,
						$dialog = pjQ.$("#" + id),
						$window = pjQ.$(window),
						width = $window.width() * 0.8,
						height = $window.height() * 0.8;

					if ($dialog.length === 0) {
						$dialog = pjQ.$('<div id="' + id + '"></div>');
						$dialog.dialog({
							modal: true,
							resizable: false,
							draggable: false,
							autoOpen: false,
							title: pjQ.$(this).data("title"),
							width: width,
							height: height,
							open: function () {
								$dialog.html(self.$container.find(".scSelectorTermsBody").html());
								$dialog.dialog("option", "position", "center");
							},
							buttons: {
								'OK': function () {
									$dialog.dialog("close");
								}
							}
						});
					}
					$dialog.dialog("open");
				}
				return false;
			}).on("click.sc", ".scSelectorRemoveCode", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.removeCode.call(self);
				return false;
			}).on("change.sc", ".scSelectorShipping", function () {
				self.updateCart.call(self);
			}).on("change.sc", ".scSelectorAddressId", function (e) {
				self.getAddress.call(self, this);
			}).on("change.sc", ".scSelectorSameAs", function () {
				if (pjQ.$(this).is(":checked")) {
					self.$container.find(".scSelectorBoxShipping").hide();
				} else {
					self.$container.find(".scSelectorBoxShipping").show();
				}
			}).on("change.sc", "select[name='payment_method']", function () {
				self.$container.find(".scCcWrap").hide();
				self.$container.find(".scBankWrap").hide();
				switch (pjQ.$("option:selected", this).val()) {
					case 'creditcard':
						self.$container.find(".scCcWrap").show();
						break;
					case 'bank':
						self.$container.find(".scBankWrap").show();
						break;
				}
			}).on("click.sc", ".scSelectorEditOrder", function () {
				var prefix = (self.options.pagePrefix) ? self.options.pagePrefix + "-" : "";
				self.hashBang("/bookings/" + prefix + "checkout");

				// Front Accounts
			}).on("click.sc", ".scSelectorLogin", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var prefix = (self.options.pagePrefix) ? self.options.pagePrefix + "-" : "";
				self.hashBang("/" + prefix + "login");
				return false;
			}).on("click.sc", ".scSelectorLogout", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.getLogout.call(self);
				return false;
			}).on("click.sc", ".scSelectorProfile", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var prefix = (self.options.pagePrefix) ? self.options.pagePrefix + "-" : "";
				self.hashBang("/" + prefix + "profile");
				return false;
			}).on("click.sc", ".scSelectorOrdersHistory", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var prefix = (self.options.pagePrefix) ? self.options.pagePrefix + "-" : "";
				self.hashBang("/" + prefix + "orders");
				return false;
			}).on("click.sc", ".scSelectorOrderDetails", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var uuid = pjQ.$(this).data("uuid");
				if (!uuid) {
					var href = pjQ.$(this).attr("href") || "";
					var m = href.match(/\/order\/([^/?#]+)/);
					if (m) {
						uuid = m[1];
					}
				}
				if (uuid) {
					var prefix = (self.options.pagePrefix) ? self.options.pagePrefix + "-" : "";
					self.hashBang("/" + prefix + "order/" + uuid);
				}
				return false;
			}).on("click.sc", ".scSelectorOrdersPage", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var page = pjQ.$(this).data("page");
				var prefix = (self.options.pagePrefix) ? self.options.pagePrefix + "-" : "";
				if (page && parseInt(page, 10) > 1) {
					self.hashBang("/" + prefix + "orders/page:" + parseInt(page, 10));
				} else {
					self.hashBang("/" + prefix + "orders");
				}
				return false;
			}).on("click.sc", ".scSelectorRegister", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var prefix = (self.options.pagePrefix) ? self.options.pagePrefix + "-" : "";
				self.hashBang("/" + prefix + "register");
				return false;
			}).on("click.sc", ".scSelectorForgot", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var prefix = (self.options.pagePrefix) ? self.options.pagePrefix + "-" : "";
				self.hashBang("/" + prefix + "forgot-password");
				return false;
			}).on("click.sc", ".scSelectorAddAddress", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $clone = self.$container.find(".scSelectorCloneAddress").eq(0).clone();
				self.$container.find(".scSelectorAddresses")/*.parent()*/.append($clone.html().replace(/\{INDEX\}/g, 'new_' + Math.ceil(Math.random() * 99999)));
				return false;
			}).on("click.sc", ".scSelectorRemoveAddress, .scSelectorDeleteAddress", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				if (self.options.layout != '3') {
					pjQ.$(this).parent().parent().remove();
				} else {
					pjQ.$(this).parent().parent().parent().parent().remove();
				}
				return false;

				// Menu	
			}).on("mouseover.sc", ".scMenuBarItem", function (e) {
				pjQ.$(this).addClass("scMenuBarItemHover");
			}).on("mouseout.sc", ".scMenuBarItem", function (e) {
				pjQ.$(this).removeClass("scMenuBarItemHover");
			}).on("mouseover.sc", ".scMenuItem", function (e) {
				pjQ.$(this).addClass("scMenuItemHover");
			}).on("mouseout.sc", ".scMenuItem", function (e) {
				pjQ.$(this).removeClass("scMenuItemHover");
			}).on("click.sc", ".scMenuBar a, .scCartMenu a", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.hashBang(pjQ.$(this).attr("href"));
				return false;

			}).on("submit.sc", ".scSelectorSearchForm", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var prefix = (self.options.pagePrefix) ? self.options.pagePrefix + "-" : "";
				self.hashBang("/" + prefix + "products/q:" + encodeURIComponent(pjQ.$(this).find("input[name='q']").val()) + "/category:/page:1" + "/sort:" + self.sort);
				return false;
			}).on("click.sc", ".scGoBack", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var prefix = (self.options.pagePrefix) ? self.options.pagePrefix + "-" : "";
				self.hashBang("/" + prefix + "products");
			}).on("click.sc", ".scDropDownMenu, .scVerticalDropDownMenu", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}

				self.hashBang(pjQ.$(this).data('href'));
			}).on("click.sc", ".scStoreName", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}

				var prefix = (self.options.pagePrefix) ? self.options.pagePrefix + "-" : "";
				if (self.options.cid > 0) {
					self.hashBang("/" + prefix + "products/q:/category:" + self.options.cid + "/page:1");
				} else {
					self.hashBang("/" + prefix + "products");
				}

			}).on("click.sc", ".pjScEyeIcon", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $prevElement = pjQ.$(this).prev();
				if ($prevElement.attr('type') == 'password') {
					$prevElement.attr('type', 'text');
					pjQ.$(this).attr('title', pjQ.$(this).attr('data-hide'));
					pjQ.$(this).addClass('active');
				} else {
					$prevElement.attr('type', 'password');
					pjQ.$(this).attr('title', pjQ.$(this).attr('data-show'));
					pjQ.$(this).removeClass('active');
				}
			}).on("click.sc", ".pjScBtnRemoveCode", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.disableButtons.call(self);

				pjQ.$.get([self.options.folder, "index.php?controller=pjFrontCart&action=pjActionRemoveCode", "&session_id=", self.options.session_id].join("")).done(function (data) {
					if (data.status == "OK") {
						self.viewCart.call(self);
					}
				}).fail(function () {
					self.enableButtons.call(self);
				});
			}).on("click.sc", ".scSelectorCaptcha", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $captcha = pjQ.$(this);
				var $form = $captcha.closest("form");
				$captcha.attr("src", $captcha.attr("src").replace(/(&rand=)\d+/g, '\$1' + Math.ceil(Math.random() * 99999)));
				pjQ.$('#pjScCaptchaField').val("").removeData("previousValue");
			}).on("click.sc", ".scSelectorSend2FriendSubmit", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				if (validate) {
					var $form = pjQ.$(this).closest('form');
					$form.validate({
						rules: {
							"your_email": {
								required: true,
								email: true
							},
							"your_name": "required",
							"friend_email": {
								required: true,
								email: true
							},
							"friend_name": "required",
							"captcha": {
								required: true,
								minlength: 6,
								maxlength: 6,
								remote: self.options.folder + "index.php?controller=pjFront&action=pjActionCheckCaptcha&session_id=" + self.options.session_id
							}
						},
						messages: {
							"your_email": {
								required: $form.find("input[name='your_email']").attr('data-err'),
								email: $form.find("input[name='your_email']").attr('data-email')
							},
							"your_name": $form.find("input[name='your_name']").attr('data-err'),
							"friend_email": {
								required: $form.find("input[name='friend_email']").attr('data-err'),
								email: $form.find("input[name='friend_email']").attr('data-email')
							},
							"friend_name": $form.find("input[name='friend_name']").attr('data-err'),
							"captcha": {
								required: $form.find("input[name='captcha']").attr('data-err'),
								remote: $form.find("input[name='captcha']").attr('data-captcha')
							}
						},
						onkeyup: false,
						onclick: false,
						onfocusout: false,
						errorClass: "scError",
						validClass: "scValid",
						submitHandler: function (form) {
							self.disableButtons.call(self);
							var $form = pjQ.$(form);
							pjQ.$.post([self.options.folder, "index.php?controller=pjFront&action=pjActionSendToFriend", "&session_id=", self.options.session_id].join(""), $form.serialize()).done(function (data) {
								if (data.status == "OK") {
									$form.find(".scSelectorNoticeMsg")
										.html(data.text)
										.removeClass("alert-danger")
										.addClass("alert-success")
										.prepend(pjQ.$("<div>").addClass("scNoticeIcon"))
										.show();
									$form.find("input[name='your_email']").val("");
									$form.find("input[name='your_name']").val("");
									$form.find("input[name='friend_email']").val("");
									$form.find("input[name='friend_name']").val("");
									$form.find("input[name='captcha']").val("").removeData("previousValue");
									var $captcha = $form.find(".scSelectorCaptcha").eq(0);
									$captcha.attr("src", $captcha.attr("src").replace(/(&rand=)\d+/g, '\$1' + Math.ceil(Math.random() * 99999)));
								} else if (data.status == "ERR") {
									$form
										.find(".scSelectorNoticeMsg")
										.html(data.text)
										.removeClass("alert-success")
										.addClass("alert-danger")
										.prepend(pjQ.$("<div>").addClass("scNoticeIcon"))
										.show();
								}
								self.enableButtons.call(self);
							}).fail(function () {
								self.enableButtons.call(self);
							});
							return false;
						}
					});
					$form.submit();
				}
			}).on("click.sc", ".pjScToggleSearch", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				pjQ.$('.pjScSearchFormContainer').toggle('slow');
			}).on("click.sc", ".pjScToggleCategory", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				pjQ.$('.pjScCategoriesContainer').toggle('slow');
			}).on("click.sc", ".pjScBtnToggleProductAttibutes", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				pjQ.$('.pjScProductAttributesContainer').toggle('slow');
			}).on("click.sc", ".scStartOver", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var prefix = (self.options.pagePrefix) ? self.options.pagePrefix + "-" : "";
				if (self.hashBang("/" + prefix + "products/q:" + (self.q && self.q !== undefined ? self.q : "") + "/category:" + (self.category_id && self.category_id !== undefined ? self.category_id : "") + "/page:" + (self.page && self.page !== undefined ? self.page : 1))) {
				} else {
					self.loadProducts.call(self);
				}
			});

			//Custom events
			pjQ.$(window).on("loadCart", this.container, function (e) {
				self.viewCart.call(self);
			}).on("loadFavs", this.container, function (e) {
				self.viewFavs.call(self);
			}).on("loadProduct", this.container, function (e, product_id) {
				self.product_id = product_id;
				self.loadProduct.call(self);
			}).on("loadProducts", this.container, function (e, q, category_id, page, sort) {
				self.q = q;
				if (category_id == undefined) {
					if (self.options.cid > 0) {
						self.category_id = self.options.cid;
					} else {
						self.category_id = category_id;
					}
				} else {
					self.category_id = category_id;
				}
				self.page = page;
				self.sort = sort;
				self.loadProducts.call(self);
			}).on("loadLogin", this.container, function (e) {
				self.getLogin.call(self);
			}).on("loadForgot", this.container, function (e) {
				self.getForgot.call(self);
			}).on("loadProfile", this.container, function (e) {
				self.getProfile.call(self);
			}).on("loadOrdersHistory", this.container, function (e, page) {
				self.getOrdersHistory.call(self, page);
			}).on("loadOrderDetails", this.container, function (e, uuid) {
				self.getOrderDetails.call(self, uuid);
			}).on("loadRegister", this.container, function (e) {
				self.getRegister.call(self);
			}).on("loadCheckout", this.container, function (e) {
				self.checkoutCart.call(self);
			}).on("loadPreview", this.container, function (e) {
				self.previewOrder.call(self);

			}).on("reloadAddress", this.container, function (e, data) {
				switch (data.type) {
					case "billing":
						self.bData = {};
						self.$container.find(".scSelectorOriginalB").each(function (i, el) {
							self.bData[this.getAttribute("name")] = this.getAttribute("data-original");
						});
						break;
					case "shipping":
						self.sData = {};
						self.$container.find(".scSelectorOriginalS").each(function (i, el) {
							self.sData[this.getAttribute("name")] = this.getAttribute("data-original");
						});
						break;
				}
			}).on("compareAddress", this.container, function (e, data) {
				var tmp = {};
				switch (data.type) {
					case "billing":
						self.$container.find(".scSelectorOriginalB").each(function (i, el) {
							tmp[this.getAttribute("name")] = this.nodeName !== "SELECT" ? this.value : this.options[this.selectedIndex].value;
						});
						if (ShoppingCart.compare(self.bData, tmp)) {
							self.$container.find(".scSelectorSaveB").hide().find("input[name='b_save']").removeAttr("checked");
						} else {
							self.$container.find(".scSelectorSaveB").show();
						}
						break;
					case "shipping":
						self.$container.find(".scSelectorOriginalS").each(function (i, el) {
							tmp[this.getAttribute("name")] = this.nodeName !== "SELECT" ? this.value : this.options[this.selectedIndex].value;
						});
						if (ShoppingCart.compare(self.sData, tmp)) {
							self.$container.find(".scSelectorSaveS").hide().find("input[name='s_save']").removeAttr("checked");
						} else {
							self.$container.find(".scSelectorSaveS").show();
						}
						break;
				}
			});

			self.onHashChange.call(self);

			// Browser back/forward updates the URL via history but does not fire click handlers.
			// Sync the cart view with the current URL when the user navigates with back/forward.
			pjQ.$(window).off("popstate.scCart_" + this.options.index).on("popstate.scCart_" + this.options.index, function () {
				self.onHashChange.call(self);
			});

			if (!self.isStandaloneStorefront()) {
				pjQ.$(window).off("hashchange.scCart_" + this.options.index).on("hashchange.scCart_" + self.options.index, function () {
					self.onHashChange.call(self);
				});
			}

			pjQ.$(document).on("click.sc", 'button[data-dismiss="modal"]', function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $modal = pjQ.$(this).closest('.modal');
				if ($modal !== undefined && $modal.length > 0) {
					$modal.modal('hide');
					pjQ.$('body').removeClass('modal-open');
				}
				return false;
			});

			return this;
		},
		initLayout3ProductCards: function () {
			var self = this;

			self.$container.find('.pjScProductImages').each(function () {
				var $images = pjQ.$(this);

				if ($images.find('.owl-carousel').length === 0) {
					return;
				}

				var pid = $images.attr('data-id'),
					$owl = pjQ.$('#owl-carousel-' + pid);

				if ($owl.data('owl.carousel')) {
					$owl.trigger('destroy.owl.carousel');
				}

				$owl.find('img.owl-lazy').each(function () {
					var $img = pjQ.$(this),
						src = $img.attr('data-src');

					if (src && !$img.attr('src')) {
						$img.attr('src', src).removeClass('owl-lazy');
					}
				});

				$owl.owlCarousel({
					items: 1,
					lazyLoad: false,
					loop: true,
					dots: false,
					nav: true,
					navText: ['<i class="glyphicon glyphicon-chevron-left"></i>', '<i class="glyphicon glyphicon-chevron-right"></i>'],
					margin: 0,
					startPosition: 1
				});

				$owl.addClass('owl-carousel-hidden').removeClass('owl-carousel-active');
				$owl.find('.owl-nav').hide();
				$images.find('.pjScProductDefaultGalleryImage').css('opacity', 1);
			});

			self.$container.off('mouseenter.pjScProductHover mouseleave.pjScProductHover', '.pjScProduct .pjScProductImages, .pjScProduct .pjScProductNamePrice, .pjScProduct');

			self.$container.on('mouseenter.pjScProductHover', '.pjScProduct .pjScProductImages', function () {
				var $images = pjQ.$(this),
					$owl = $images.find('.owl-carousel');

				if ($owl.length > 0) {
					$images.find('.pjScProductDefaultGalleryImage').css('opacity', 0);
					$owl.find('.owl-nav').show();
					$owl.removeClass('owl-carousel-hidden').addClass('owl-carousel-active');
				}
			}).on('mouseenter.pjScProductHover', '.pjScProduct .pjScProductNamePrice', function () {
				pjQ.$(this).closest('.pjScProduct').find('.pjScProductAttributes').addClass('show');
			}).on('mouseleave.pjScProductHover', '.pjScProduct', function (e) {
				var $card = pjQ.$(this),
					rel = e.relatedTarget;

				if (rel && pjQ.$(rel).closest('.pjScProduct')[0] === this) {
					return;
				}

				var $images = $card.find('.pjScProductImages'),
					$owl = $images.find('.owl-carousel');

				if ($owl.length > 0) {
					$images.find('.pjScProductDefaultGalleryImage').css('opacity', 1);
					$owl.find('.owl-nav').hide();
					$owl.addClass('owl-carousel-hidden').removeClass('owl-carousel-active');
				}

				$card.find('.pjScProductAttributes').removeClass('show');
			});
		},
		loadProducts: function () {
			var self = this;
			this.resetProduct.call(this);
			pjQ.$.get([this.options.folder, "index.php?controller=pjFrontPublic&action=pjActionProducts", "&session_id=", self.options.session_id].join(""), {
				"q": this.q,
				"category_id": this.category_id,
				"page": this.page,
				"sort": this.sort,
				"locale": this.options.locale,
				"hide": this.options.hide,
				"layout": this.options.layout,
				"theme": this.options.theme
			}).done(function (data) {
				self.$container.html(data);
				if (self.page != null) {
					pjQ.$("html, body").animate({ scrollTop: 0 }, "slow");
				}
				pjQ.$('.modal-dialog').css("z-index", "9999");
				self.initLayout3ProductCards();

				pjQ.$('.scDropDownMenu').bind('touchstart', function (e) {
					e.preventDefault();
					var $this = pjQ.$(this);
					if ($this.siblings().size() > 0) {
						if (!$this.siblings(':first').hasClass('scShown')) {
							$this.siblings(':first').addClass('scShown');
							$this.siblings(':first').css('display', 'block');
						} else {
							self.hashBang($this.data('href'));
						}
					} else {
						self.hashBang($this.data('href'));
					}
					return false;
				});
			});
		},
		loadProduct: function () {
			var self = this;
			pjQ.$.get([this.options.folder, "index.php?controller=pjFront&action=pjActionGetStocks&id=", this.product_id, "&session_id=", self.options.session_id].join("")).done(function (data) {
				self.stockIds = data.stock_ids;
				self.stockObj = data.stocks;
				self.qtyObj = data.qty;
				self.priceObj = data.price;
				self.attrObj = data.attributes;
				pjQ.$.get([self.options.folder, "index.php?controller=pjFrontPublic&action=pjActionProduct&id=", self.product_id, "&session_id=", self.options.session_id].join(""), {
					"locale": self.options.locale,
					"hide": self.options.hide,
					"layout": self.options.layout,
					"theme": self.options.theme
				}).done(function (data) {
					self.$container.html(data);
					self.loopAttr.call(self, self.$container.find(".scSelectorAttr:first").get(0));
					self.priceStocks = parseFloat(pjQ.$('.scInputMinPrice').val());
					self.unitPrice = self.priceStocks;
					self.checkFavs.call(self);
					pjQ.$('.modal-dialog').css("z-index", "9999");
					if (fancybox) {
						self.$container.find("a[rel=fancy_group]").fancybox();
					}

					self.$container.find(".sc-gallery-thumbs").off("click.sc", ".sc-gallery-thumb").on("click.sc", ".sc-gallery-thumb", function () {
						var $thumb = pjQ.$(this),
							displaySrc = $thumb.data("display"),
							largeSrc = $thumb.data("large");
						self.$container.find(".sc-gallery-main-img").attr("src", displaySrc);
						self.$container.find(".sc-gallery-main-link").attr("href", largeSrc);
						self.$container.find(".sc-gallery-thumb").removeClass("active");
						$thumb.addClass("active");
					});

					if (pjQ.$('.swiper-container').length > 0) {
						const swiper = new Swiper('.swiper-container', {
							slidesPerView: 2,
							grid: {
								rows: pjQ.$('.swiper-container').attr('data-count')
							},
							spaceBetween: 0,
							pagination: {
								el: ".swiper-pagination",
								clickable: true
							},
							breakpoints: {
								320: { slidesPerView: 1, direction: "vertical", grid: { rows: 1 }, spaceBetween: 0 },
								480: { slidesPerView: 1, direction: "vertical", grid: { rows: 1 }, spaceBetween: 0 },
								640: { slidesPerView: 1, direction: "vertical", grid: { rows: 1 }, spaceBetween: 0 },
								768: { slidesPerView: 1, direction: "vertical", grid: { rows: 1 }, spaceBetween: 0 },
								992: {
									slidesPerView: 2,
									grid: { rows: pjQ.$('.swiper-container').attr('data-count') },
									spaceBetween: 0
								},
								1024: {
									slidesPerView: 2,
									grid: { rows: pjQ.$('.swiper-container').attr('data-count') },
									spaceBetween: 0
								},
								1280: {
									slidesPerView: 2,
									grid: { rows: pjQ.$('.swiper-container').attr('data-count') },
									spaceBetween: 0
								}
							},
							observer: true,
							observeParents: true,
							loop: true,
							lazyLoading: true,
							updateOnWindowResize: true
						});
						pjQ.$(window).resize(function () {
							swiper.update();
						});
					}

					if (validate) {
						var $form = self.$container.find(".scSelectorProductForm");
						$form.validate({
							onkeyup: false,
							highlight: function (ele, errorClass, validClass) {
								pjQ.$(ele).parent().addClass('has-error');
							},
							unhighlight: function (ele, errorClass, validClass) {
								pjQ.$(ele).parent().removeClass('has-error').addClass('has-success');
							}
						});
					}
				});
			});
		},
		loopAttr: function (el) {
			var oid, valid, k, kCnt, j, jCnt, b, bCnt, pid, $select, $option,
				self = this,
				$el = pjQ.$(el),
				row = $el.data("row"),
				id = $el.find("option:selected").val(),
				stocks = [];

			for (k = 0, kCnt = self.stockObj.length; k < kCnt; k++) {
				if (ShoppingCart.inObject(id, this.stockObj[k])) {
					stocks.push(this.stockObj[k]);
				}
			}

			this.$container.find(".scSelectorAttr").each(function (i, select) {
				if (i > row) {
					$select = pjQ.$(select);
					$select.empty();
					pid = $select.data("id");
					pjQ.$("<option>")
						.attr("value", "")
						.text($select.attr('data-choose'))
						.appendTo($select);
					for (k = 0, kCnt = self.attrObj.length; k < kCnt; k++) {
						if (self.attrObj[k].id != pid) {
							continue;
						}
						for (j = 0, jCnt = self.attrObj[k].child.length; j < jCnt; j++) {
							for (b = 0, bCnt = stocks.length; b < bCnt; b++) {
								if (ShoppingCart.inObject(self.attrObj[k].child[j].id, stocks[b]) || (stocks[b][pid] && stocks[b][pid] == 0)) {
									pjQ.$("<option>")
										.attr("value", self.attrObj[k].child[j].id)
										.text(self.attrObj[k].child[j].name)
										.appendTo($select);
									break;
								}
							}
						}
					}
				}
			});
		},
		resetProduct: function () {
			this.stockIds = {};
			this.stockObj = {};
			this.qtyObj = {};
			this.priceObj = {};
			this.attrObj = {};
			this.price = 0.00;
			this.priceStocks = 0;
			this.priceExtras = 0
			return this;
		}
	};

	// expose
	window.ShoppingCart = ShoppingCart;
})(window);