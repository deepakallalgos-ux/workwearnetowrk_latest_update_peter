var jQuery_1_8_2 = jQuery_1_8_2 || jQuery.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var $dialogExecute = $("#dialogExecute"),
			$dialogExecuteAll = $("#dialogExecuteAll"),
			$dialogNotice = $("#dialogNotice"),
			datagrid = ($.fn.datagrid !== undefined),
			dialog = ($.fn.dialog !== undefined),
			executeUrl = "index.php?controller=pjInstaller&action=pjActionSecureSetUpdate",
			reloadUrl = "index.php?controller=pjInstaller&action=pjActionSecureGetUpdate";

		function buildInstallerUrl(url) {
			if (window.location.search.indexOf("admin=1") !== -1 && url.indexOf("admin=1") === -1) {
				url += (url.indexOf("?") === -1 ? "?" : "&") + "admin=1";
			}
			return url;
		}

		executeUrl = buildInstallerUrl(executeUrl);
		reloadUrl = buildInstallerUrl(reloadUrl);

		function escapeHtml(str) {
			return String(str || "")
				.replace(/&/g, "&amp;")
				.replace(/</g, "&lt;")
				.replace(/>/g, "&gt;");
		}

		function formatErrorHtml(message) {
			return escapeHtml(message || "Request failed.").replace(/\r\n|\r|\n/g, "<br>");
		}

		function showExecuteError($dialog, message) {
			var $box = $dialog.find(".update-execute-error, .i-error-clean, label.error").first();
			if (!$box.length) {
				$box = $('<div class="alert alert-danger update-execute-error" style="margin-top:10px"></div>').appendTo($dialog);
			}
			$box.html(formatErrorHtml(message)).show();
		}

		function hideExecuteError($dialog) {
			$dialog.find(".update-execute-error, .i-error-clean, label.error").hide().html("");
		}

		function parseJsonResponse(data) {
			if (typeof data === "string") {
				try {
					return $.parseJSON(data);
				} catch (e) {
					return null;
				}
			}
			return data;
		}

		function stripResponseBody(raw) {
			var trimmed = $.trim(raw || "");
			if (!trimmed) {
				return "";
			}
			var parsed = parseJsonResponse(trimmed);
			if (parsed && parsed.text) {
				return parsed.text;
			}
			if (trimmed.indexOf("<") !== -1) {
				return trimmed
					.replace(/<script[\s\S]*?<\/script>/gi, "")
					.replace(/<style[\s\S]*?<\/style>/gi, "")
					.replace(/<[^>]+>/g, " ")
					.replace(/\s+/g, " ")
					.trim();
			}
			return trimmed;
		}

		function extractErrorMessage(xhr, textStatus, errorThrown) {
			var parts = [];
			if (textStatus) {
				parts.push(textStatus);
			}
			if (errorThrown) {
				parts.push(errorThrown);
			}
			if (xhr && xhr.status) {
				parts.push("HTTP " + xhr.status);
			}

			var body = stripResponseBody(xhr && xhr.responseText ? xhr.responseText : "");
			if (body) {
				if (body.length > 2000) {
					body = body.substring(0, 2000) + "...";
				}
				return parts.join(" — ") + "\n\n" + body;
			}

			if (textStatus === "parsererror") {
				return parts.join(" — ") + "\n\nServer response was not valid JSON. Add ?reporting=1 to this page URL and try again.";
			}

			return parts.join(" — ") + "\n\nNo details from server (possible PHP fatal error, timeout, or connection problem).";
		}

		function postExecute($dialog, postData, onSuccess) {
			$.ajax({
				url: executeUrl,
				type: "POST",
				dataType: "text",
				data: postData
			}).done(function (raw, textStatus, jqXHR) {
				var data = parseJsonResponse($.trim(raw));
				if (data) {
					handleExecuteResponse($dialog, data, onSuccess);
					return;
				}
				var snippet = $.trim(raw);
				if (!snippet) {
					snippet = "Empty response from server (HTTP " + (jqXHR && jqXHR.status ? jqXHR.status : "?") + ").";
				} else if (snippet.length > 2000) {
					snippet = snippet.substring(0, 2000) + "...";
				}
				showExecuteError($dialog, "Server did not return JSON:\n\n" + snippet);
			}).fail(function (xhr, textStatus, errorThrown) {
				showExecuteError($dialog, extractErrorMessage(xhr, textStatus, errorThrown));
			});
		}

		function handleExecuteResponse($dialog, data, onSuccess) {
			data = parseJsonResponse(data);
			if (!data || !data.status) {
				showExecuteError($dialog, "Invalid server response. Try adding ?reporting=1 to the page URL.");
				return;
			}
			if (data.status === "OK") {
				$dialog.dialog("close");
				$dialogNotice.data("content", data.text || "Database update has been applied.").dialog("open");
				if (typeof onSuccess === "function") {
					onSuccess();
				}
			} else {
				showExecuteError($dialog, data.text || "Database update failed.");
			}
		}

		function formatButton(str, obj) {
			return ['<input type="button" value="', myLabel.execute, '" class="btn btn-primary btn-outline btn-execute" data-name="', obj.name,
			        '" data-module="', obj.module,
			        '" data-path="', obj.path, '" />'].join("");
		}

		function formatView (str, obj) {
			return ['<a href="index.php?controller=pjInstaller&action=pjActionSecureView&p=', str, '" class="install-view" target="_blank"></a>',
			        (obj.is_new ? ['<input type="hidden" name="record[]" value="', obj.path ,'"><input type="hidden" name="module[]" value="',obj.module,'">'].join("") : '')].join("");
		}

		var $grid = null;

		if ($("#grid").length > 0 && datagrid) {

			var columns = [{text: "", type: "text", sortable: false, editable: false, width: 23, renderer: formatView},
					       {text: myLabel.name, type: "text", sortable: false, editable: false, width: 470},
					       {text: myLabel.label, type: "text", sortable: false, editable: false, width: 180},
					       {text: myLabel.dt, type: "text", sortable: false, editable: false, width: 220}];
			var fields = ['base', 'name', 'label', 'date'];

			if (window.location.search.match(/&admin=1/)) {
				columns.push({text: "", type: "text", sortable: false, editable: false, width: 100, align: "center", renderer: formatButton});
				fields.push('path');
			}

			$grid = $("#grid").datagrid({
				buttons: [],
				columns: columns,
				dataUrl: reloadUrl,
				dataType: "json",
				fields: fields,
				paginator: false,
				saveUrl: null,
				select: false,
				onRender: function () {
					if ($grid.find('input[name="record[]"]').length > 0) {
						$(".btn-execute-all").show();
					} else {
						$(".btn-execute-all").hide();
					}
				}
			});
		}

		function reloadGrid() {
			if (!$grid || !$grid.length) {
				return;
			}
			var content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", reloadUrl, "", "", content.page, content.rowCount);
		}

		$(document).on("click", ".btn-execute-all", function (e) {
			if ($dialogExecuteAll.length > 0 && dialog) {
				$dialogExecuteAll.dialog("open");
			}
		}).on("click", ".btn-execute", function (e) {
			if ($dialogExecute.length > 0 && dialog) {
				var $this = $(this);
				$dialogExecute
					.data("name", $this.data("name"))
					.data("path", $this.data("path"))
					.data("module", $this.data("module"))
					.dialog("open");
			}
		});

		if ($dialogExecuteAll.length > 0 && dialog) {
			$dialogExecuteAll.dialog({
				modal: true,
				autoOpen: false,
				draggable: false,
				resizable: false,
				close: function () {
					hideExecuteError($dialogExecuteAll);
				},
				buttons: {
					"Execute": function () {
						postExecute(
							$dialogExecuteAll,
							$grid.find('input[name="record[]"], input[name="module[]"]').serialize(),
							reloadGrid
						);
					},
					"Cancel": function () {
						$dialogExecuteAll.dialog("close");
					}
				}
			});
		}

		if ($dialogExecute.length > 0 && dialog) {
			$dialogExecute.dialog({
				modal: true,
				autoOpen: false,
				draggable: false,
				resizable: false,
				close: function () {
					hideExecuteError($dialogExecute);
				},
				buttons: {
					"Execute": function () {
						postExecute($dialogExecute, {
							"name": $dialogExecute.data("name"),
							"path": $dialogExecute.data("path"),
							"module": $dialogExecute.data("module")
						}, reloadGrid);
					},
					"Cancel": function () {
						$dialogExecute.dialog("close");
					}
				}
			});
		}

		if ($dialogNotice.length > 0 && dialog) {
			$dialogNotice.dialog({
				modal: true,
				autoOpen: false,
				draggable: false,
				resizable: false,
				open: function () {
					$dialogNotice.html($dialogNotice.data("content"));
				},
				buttons: {
					"OK": function () {
						$dialogNotice.dialog("close");
					}
				}
			});
		}

	});
})(jQuery_1_8_2);
