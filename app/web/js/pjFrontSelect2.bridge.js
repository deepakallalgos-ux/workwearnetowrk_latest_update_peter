/**
 * Select2 registers on window.jQuery; the storefront uses pjQ.$ (isolated pjQuery).
 * This bridge copies the plugin onto pjQ.$ after Select2 loads.
 */
(function (win) {
	"use strict";

	win.pjFrontSelect2Bridge = function () {
		var pj$ = win.pjQ && (win.pjQ.$ || win.pjQ.jQuery);
		if (!pj$ || !pj$.fn) {
			return false;
		}
		if (typeof pj$.fn.select2 === "function") {
			return true;
		}
		if (win.jQuery && win.jQuery.fn && typeof win.jQuery.fn.select2 === "function") {
			pj$.fn.select2 = win.jQuery.fn.select2;
			if (win.pjQ.jQuery && win.pjQ.jQuery !== pj$) {
				win.pjQ.jQuery.fn.select2 = win.jQuery.fn.select2;
			}
			return true;
		}
		return false;
	};
})(window);
