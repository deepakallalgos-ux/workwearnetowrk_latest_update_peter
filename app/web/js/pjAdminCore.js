var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();

/**
 * Shared product/stock thumbnail markup for admin datagrids (Peter s-Img style).
 */
window.pjGridImage = {
	hasPath: function (path) {
		return path !== null && path !== undefined && String(path).length > 0;
	},
	placeholder: function () {
		if (typeof myLabel !== 'undefined' && myLabel.placeholderImage) {
			return myLabel.placeholderImage;
		}
		var base = (typeof myLabel !== 'undefined' && myLabel.installUrl) ? myLabel.installUrl : '';
		return base + 'app/web/img/frontend/80x106.png';
	},
	resolveUrl: function (path) {
		if (!this.hasPath(path)) {
			return this.placeholder();
		}
		var url = String(path);
		if (url.indexOf('http://') === 0 || url.indexOf('https://') === 0) {
			return url;
		}
		var base = (typeof myLabel !== 'undefined' && myLabel.installUrl) ? myLabel.installUrl : '';
		return base + url.replace(/^\//, '');
	},
	thumb: function (path, showEmpty) {
		var src = this.hasPath(path) ? this.resolveUrl(path) : (showEmpty !== false ? this.placeholder() : '');
		if (!src) {
			return '';
		}
		return '<span class="s-Pic"><img src="' + src + '" alt="" class="s-Img" /></span>';
	},
	link: function (path, href, showEmpty) {
		var src = this.hasPath(path) ? this.resolveUrl(path) : (showEmpty !== false ? this.placeholder() : '');
		if (!src) {
			return showEmpty !== false ? this.thumb('', true) : '';
		}
		return '<a href="' + href + '" class="s-Pic"><img src="' + src + '" alt="" class="s-Img" /></a>';
	}
};

(function ($, undefined) {
	$(function () {
		"use strict";
		
		$(".pj-table tbody tr").hover(
			function () {
				$(this).addClass("pj-table-row-hover");
			}, 
			function () {
				$(this).removeClass("pj-table-row-hover");
			}
		);
		$(".pj-button").hover(
			function () {
				$(this).addClass("pj-button-hover");
			}, 
			function () {
				$(this).removeClass("pj-button-hover");
			}
		);
		$(".pj-checkbox").hover(
				function () {
					$(this).addClass("pj-checkbox-hover");
				}, 
				function () {
					$(this).removeClass("pj-checkbox-hover");
				}
			);
		$("#content").on("click", ".notice-close", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(this).closest(".notice-box").fadeOut();
			return false;
		});

		$(document).on("click", ".pj-form-field-icon-date", function (e) {
			var $dp = $(this).parent().siblings("input[type='text']");
			if ($dp.hasClass("hasDatepicker")) {
				$dp.datepicker("show");
			} else {
				$dp.trigger("focusin").datepicker("show");
			}
		});
	});
})(jQuery_1_8_2);
