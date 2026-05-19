<?php
mt_srand();
$index = mt_rand(1, 9999);
$validate = str_replace(array('"', "'"), array('\"', "\'"), __('validate', true, true));
$theme = $controller->_get->check('theme') ? $controller->_get->toString('theme') : ('theme'.$tpl['option_arr']['o_theme']);
?>
<div id="pjWrapperShoppingCart_<?php echo $theme;?>">
	<div id="scContainer_<?php echo $index; ?>" class="scContainer"></div>
</div>
<script type="text/javascript">
var pjQ = pjQ || {},
	ShoppingCart_<?php echo $index; ?>;
(function () {
	"use strict";
	var isSafari = /Safari/.test(navigator.userAgent) && /Apple Computer/.test(navigator.vendor),

	loadCssHack = function(url, callback){
		var link = document.createElement('link');
		link.type = 'text/css';
		link.rel = 'stylesheet';
		link.href = url;

		document.getElementsByTagName('head')[0].appendChild(link);

		var img = document.createElement('img');
		img.onerror = function(){
			if (callback && typeof callback === "function") {
				callback();
			}
		};
		img.src = url;
	},
	loadRemote = function(url, type, callback) {
		if (type === "css" && isSafari) {
			loadCssHack(url, callback);
			return;
		}
		var _element, _type, _attr, scr, s, element;
		
		switch (type) {
		case 'css':
			_element = "link";
			_type = "text/css";
			_attr = "href";
			break;
		case 'js':
			_element = "script";
			_type = "text/javascript";
			_attr = "src";
			break;
		}
		
		scr = document.getElementsByTagName(_element);
		s = scr[scr.length - 1];
		element = document.createElement(_element);
		element.type = _type;
		if (type == "css") {
			element.rel = "stylesheet";
		}
		if (element.readyState) {
			element.onreadystatechange = function () {
				if (element.readyState == "loaded" || element.readyState == "complete") {
					element.onreadystatechange = null;
					if (callback && typeof callback === "function") {
						callback();
					}
				}
			};
		} else {
			element.onload = function () {
				if (callback && typeof callback === "function") {
					callback();
				}
			};
		}
		element[_attr] = url;
		s.parentNode.insertBefore(element, s.nextSibling);
	},
	loadScript = function (url, callback) {
		loadRemote(url, "js", callback);
	},
	loadCss = function (url, callback) {
		loadRemote(url, "css", callback);
	},
	getSessionId = function () {
		return sessionStorage.getItem("session_id") == null ? "" : sessionStorage.getItem("session_id");
	},
	createSessionId = function () {
		if(getSessionId()=="") {
			sessionStorage.setItem("session_id", "<?php echo session_id(); ?>");
		}
	},
	options = {
		server: "<?php echo PJ_INSTALL_URL; ?>",
		folder: "<?php echo PJ_INSTALL_URL; ?>",
		installUrl: "<?php echo pjUtil::getStorefrontBaseUrl($tpl['option_arr']['o_install_url']); ?>",
		pagePrefix: "<?php echo $tpl['option_arr']['o_page_prefix']; ?>",
		index: <?php echo $index; ?>,
		locale: <?php echo $controller->_get->check('locale')? $controller->_get->toInt('locale') : $controller->pjActionGetLocale(); ?>,
		hide: <?php echo $controller->_get->check('hide') ? $controller->_get->toInt('hide') : 0; ?>,
		seoUrl: <?php echo (int) $tpl['option_arr']['o_seo_url']; ?>,
		validate: <?php echo pjAppController::jsonEncode($validate); ?>,
		currency: "<?php echo $tpl['option_arr']['o_currency']; ?>",
		currencysign: "<?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency'], false); ?>",
		layout: <?php echo $controller->_get->check('layout') && in_array($controller->_get->toInt('layout'), $controller->getLayoutRange()) ? $controller->_get->toInt('layout') : (int) $tpl['option_arr']['o_layout']; ?>,
		theme: "<?php echo $theme; ?>",
		cid: <?php echo $controller->_get->check('category_id') && $controller->_get->toInt('category_id') > 0 ? $controller->_get->toInt('category_id') : 0; ?>,
		captchaMode: "<?php echo pjSanitize::html(isset($tpl['option_arr']['o_captcha_mode_front']) ? $tpl['option_arr']['o_captcha_mode_front'] : 'string'); ?>",
		captchaLength: <?php echo isset($tpl['option_arr']['o_captcha_length_front']) ? (int) $tpl['option_arr']['o_captcha_length_front'] : 6; ?>
	};
	<?php
	$dm = new pjDependencyManager(PJ_INSTALL_PATH, PJ_THIRD_PARTY_PATH);
	$dm->load(PJ_CONFIG_PATH . 'dependencies.php')->resolve();
	?>
	loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('storage_polyfill'); ?>storagePolyfill.min.js", function () {
		if (isSafari) {
			createSessionId();
			options.session_id = getSessionId();
		}else{
			options.session_id = "";
		}
		loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_jquery'); ?>pjQuery.min.js", function () {
			loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_validate'); ?>pjQuery.validate.min.js", function () {
				loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_bootstrap'); ?>pjQuery.bootstrap.min.js", function () {
					loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_fancybox'); ?>pjQuery.fancybox.js", function () {
						loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_owlcarousel'); ?>owl.carousel.min.js", function () {
							loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_swiper'); ?>js/swiper-bundle.min.js", function () {
								<?php if($tpl['option_arr']['o_captcha_type_front'] == 'google'): ?>
							    loadScript('https://www.google.com/recaptcha/api.js', function () {
	                            <?php endif; ?>
            						loadScript("<?php echo PJ_INSTALL_URL . PJ_JS_PATH; ?>pjShoppingCart.js?v=<?php echo PJ_SCRIPT_VERSION; ?>", function () {
            							ShoppingCart_<?php echo $index; ?> = new ShoppingCart(options);
            						});
        						<?php if($tpl['option_arr']['o_captcha_type_front'] == 'google'): ?>
	                            });
							    <?php endif; ?>
							});
						});
					});
				});
			});
		});
	});
})();
</script>