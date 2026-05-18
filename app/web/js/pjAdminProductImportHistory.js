var jQuery = jQuery || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";

		var
			multilang = ($.fn.multilang !== undefined),
			validate = ($.fn.validate !== undefined),
			datagrid = ($.fn.datagrid !== undefined),
			m = window.location.href.match(/&id=(\d+)/),
			product_id, $similar;

		if (m !== null) {
			product_id = m[1];
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

		if ($(".select-item").length) {
			$(".select-item").select2({
				allowClear: true
			});
		};

		if ($('.i-checks').length > 0) {
			$('.i-checks').iCheck({
				checkboxClass: 'icheckbox_square-green',
				radioClass: 'iradio_square-green'
			});
		}
		var $grid = null;

		/* ===============================
		   SYNC PROGRESS MODAL (batch sync from import history)
		   =============================== */
		function ensureSyncProgressModal() {
			if ($("#pjImportSyncProgressModal").length) {
				return;
			}
			var title = (typeof myLabel.import_sync_modal_title !== "undefined" && myLabel.import_sync_modal_title)
				? myLabel.import_sync_modal_title
				: "Sync in progress";
			var sub = (typeof myLabel.import_sync_modal_sub !== "undefined" && myLabel.import_sync_modal_sub)
				? myLabel.import_sync_modal_sub
				: "Rows are processed in batches. You can keep this window open until it finishes.";
			$("body").append(
				'<div class="modal fade" id="pjImportSyncProgressModal" tabindex="-1" role="dialog" data-backdrop="false" data-keyboard="false">' +
					'<div class="modal-dialog" role="document">' +
						'<div class="modal-content">' +
							'<div class="modal-header">' +
								'<h4 class="modal-title">' + $('<div>').text(title).html() + "</h4>" +
							"</div>" +
							'<div class="modal-body">' +
								'<p id="pjImportSyncStatusLine" class="m-b-sm"></p>' +
								'<div class="progress m-b-sm" style="height:26px;">' +
									'<div id="pjImportSyncProgressBar" class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" style="width:0%;line-height:26px;">0%</div>' +
								"</div>" +
								'<p id="pjImportSyncDetailLine" class="text-muted small m-b-none"></p>' +
								'<p class="text-muted small m-t-sm m-b-none"><i class="fa fa-info-circle"></i> ' + $('<div>').text(sub).html() + "</p>" +
							"</div>" +
						"</div>" +
					"</div>" +
				"</div>"
			);
		}

		function pjShowSyncProgressModal() {
			ensureSyncProgressModal();
			pjUpdateSyncProgressModal(0, 0, 0, true);
			$("#pjImportSyncProgressModal").modal("show");
		}

		function pjUpdateSyncProgressModal(processed, total, batchSize, indeterminate) {
			var pct = 0;
			if (total > 0 && !indeterminate) {
				pct = Math.round(Math.min(processed, total) / total * 100);
				if (pct > 100) {
					pct = 100;
				}
			}
			var $bar = $("#pjImportSyncProgressBar");
			$bar.css("width", pct + "%").text(pct + "%");
			var statusLine = indeterminate || !total
				? ((typeof myLabel.import_sync_preparing !== "undefined" && myLabel.import_sync_preparing) ? myLabel.import_sync_preparing : "Preparing…")
				: ((typeof myLabel.import_sync_status !== "undefined" && myLabel.import_sync_status)
					? myLabel.import_sync_status.replace("{processed}", processed).replace("{total}", total)
					: ("Processed " + processed + " of " + total + " rows"));
			$("#pjImportSyncStatusLine").text(statusLine);
			var detail = "";
			if (batchSize > 0) {
				detail = ((typeof myLabel.import_sync_last_batch !== "undefined" && myLabel.import_sync_last_batch)
					? myLabel.import_sync_last_batch.replace("{batch}", batchSize)
					: ("Last batch: " + batchSize + " row(s)"));
			}
			$("#pjImportSyncDetailLine").text(detail);
		}

		function pjHideSyncProgressModal() {
			if (!$("#pjImportSyncProgressModal").length) {
				return;
			}
			$("#pjImportSyncProgressModal").modal("hide");
			setTimeout(function () {
				pjUpdateSyncProgressModal(0, 0, 0, true);
			}, 400);
		}

		/* ===============================
		SYNC BUTTON CLICK
		=============================== */


		function syncImport(syncBaseUrl, offset) {
			if (offset === 0) {
				pjShowSyncProgressModal();
			}

			$.post(
				syncBaseUrl,
				{
					offset: offset
				},
				function (data) {
					console.log(data);
					if (!data || !data.status) {
						console.error("Invalid response");
						pjHideSyncProgressModal();
						swal({
							title: myLabel.import_error_title,
							text: "Invalid response from server.",
							type: "warning",
							confirmButtonColor: "#d9534f"
						});
						return;
					}

					/* refresh datagrid */
					refreshImportGrid();

					var total = parseInt(data.total, 10) || 0;
					var processed = typeof data.processed !== "undefined" && data.processed !== null
						? parseInt(data.processed, 10)
						: Math.min(parseInt(data.offset, 10) || 0, total);
					var batch = parseInt(data.batch, 10) || 0;

					if (total > 0) {
						pjUpdateSyncProgressModal(processed, total, batch, false);
					}

					if (data.status === "OK") {

						syncImport(syncBaseUrl, data.offset);
					}

					else if (data.status === "DONE") {

						pjUpdateSyncProgressModal(total, total, batch, false);

						setTimeout(function () {

							pjHideSyncProgressModal();

							swal({
								title: myLabel.import_completed_title,
								text: myLabel.import_completed_message,
								type: "success",
								confirmButtonColor: "#11511a"
							}, function () {

								window.location.href = "index.php?controller=pjAdminProductImportHistory&action=pjActionIndex";

							});

						}, 400);

					}

					else {

						console.log("-----", data);

						pjHideSyncProgressModal();

						setTimeout(function () {

							swal({
								title: myLabel.import_error_title,
								text: data.text || "Error",
								type: "warning",
								confirmButtonColor: "#d9534f"
							});

						}, 200);

					}

				},
				"json"
			).fail(function (xhr) {
				pjHideSyncProgressModal();
				var msg = myLabel.import_server_error || "Request failed";
				if (xhr.responseJSON && xhr.responseJSON.text) {
					msg = xhr.responseJSON.text;
				} else if (xhr.responseText) {
					msg = xhr.responseText;
				}
				setTimeout(function () {
					swal({
						title: myLabel.import_error_title,
						text: msg,
						type: "error",
						confirmButtonColor: "#d9534f"
					});
				}, 200);
			});
		}
		function syncImportImportFromFile(file_id, offset) {

			if (offset === 0) {
				pjShowSyncProgressModal();
			}

			$.post(
				"index.php?controller=pjAdminProductImportHistory&action=pjActionImportAllProduct",
				{
					file_id: file_id,
					offset: offset
				},
				function (data) {

					if (!data || !data.status) {
						pjHideSyncProgressModal();
						swal({
							title: myLabel.import_error_title,
							text: "Invalid response from server.",
							type: "warning",
							confirmButtonColor: "#d9534f"
						});
						return;
					}

					refreshImportGrid();

					var total = parseInt(data.total, 10) || 0;
					var processed = typeof data.processed !== "undefined" && data.processed !== null
						? parseInt(data.processed, 10)
						: Math.min(parseInt(data.offset, 10) || 0, total);
					var batch = parseInt(data.batch, 10) || 0;

					if (total > 0) {
						pjUpdateSyncProgressModal(processed, total, batch, false);
					}

					if (data.status === "OK") {

						syncImportImportFromFile(file_id, data.offset);
					}

					else if (data.status === "DONE") {

						pjUpdateSyncProgressModal(total, total, batch, false);

						setTimeout(function () {

							pjHideSyncProgressModal();

							swal({
								title: myLabel.import_completed_title,
								text: myLabel.import_completed_message,
								type: "success",
								confirmButtonColor: "#11511a"
							});

						}, 400);
					}

					else {

						pjHideSyncProgressModal();

						swal({
							title: myLabel.import_error_title,
							text: data.text || "Error",
							type: "warning",
							confirmButtonColor: "#d9534f"
						});
					}

				},
				"json"
			).fail(function (xhr) {
				pjHideSyncProgressModal();
				var msg = myLabel.import_server_error || "Request failed";
				if (xhr.responseJSON && xhr.responseJSON.text) {
					msg = xhr.responseJSON.text;
				} else if (xhr.responseText) {
					msg = xhr.responseText;
				}
				setTimeout(function () {
					swal({
						title: myLabel.import_error_title,
						text: msg,
						type: "error",
						confirmButtonColor: "#d9534f"
					});
				}, 200);
			});
		}

		/* ===============================
		GRID REFRESH
		=============================== */

		function refreshImportGrid() {

			if ($("#grid_product_history").length && $("#grid_product_history").data("datagrid")) {
				var $gh = $("#grid_product_history");
				var content = $gh.datagrid("option", "content");
				$gh.datagrid(
					"load",
					"index.php?controller=pjAdminProductImportHistory&action=pjActionGetImportRows&id=" + import_id,
					"id",
					"DESC",
					content.page,
					content.rowCount
				);
				return;
			}

			if (!$grid) return;

			var content = $grid.datagrid("option", "content");

			$grid.datagrid(
				"load",
				"index.php?controller=pjAdminProductImportHistory&action=pjActionGetHistory",
				"id",
				"DESC",
				content.page,
				content.rowCount
			);

		}


		/* ===============================
		DATAGRID
		=============================== */

		if ($("#grid").length > 0 && datagrid) {

			var $buttons = [];

			function formatStatus(val) {
				if (!val) {
					return "";
				}
				var key = String(val).replace(/[^a-z0-9_-]/gi, "_").toLowerCase();
				return '<span class="pj-table-cell-label pj-status-' + key + '">' + val + '</span>';
			}

			function formatRows(val, obj) {

				if (!obj) return "";

				return obj.processed_rows + " / " + obj.total_rows;
			}

			// function formatAction(val, obj) {

			// 	if (!obj) return "";

			// 	var syncBtn =
			// 		'<button class="btn btn-success btn-xs btn-sync m-r-xs" data-id="' + obj.id + '">' +
			// 		'<i class="fa fa-refresh"></i> ' + myLabel.import_sync + '</button>';

			// 	var downloadBtn =
			// 		'<a class="btn btn-primary btn-xs m-r-xs" ' +
			// 		'href="index.php?controller=pjAdminProductImportHistory&action=pjActionIndex&id=' + obj.id + '">' +
			// 		'<i class="fa fa-sync"></i> Sync</a>';

			// 	var deleteBtn =
			// 		'<button class="btn btn-danger btn-xs btn-delete" data-id="' + obj.id + '">' +
			// 		'<i class="fa fa-trash"></i></button>';

			// 	return downloadBtn;
			// }
			function formatAction(val, obj) {

				if (!obj) return "";

				if (obj.is_latest == 1) {

					return '<a class="btn btn-primary btn-xs m-r-xs" ' +
						'href="index.php?controller=pjAdminProductImportHistory&action=pjActionIndex&sync=1&id=' + obj.id + '">' +
						'<i class="fa fa-sync"></i>' + myLabel.import_sync + '</a>';

				}

				return "";
			}
			$buttons.push({ type: "eye", url: "index.php?controller=pjAdminProductImportHistory&action=pjActionIndex&id={:id}" });
			$buttons.push({ type: "download", url: "index.php?controller=pjAdminProductImportHistory&action=pjActionDownloadFile&id={:id}" });
			$buttons.push({ type: "delete", url: "index.php?controller=pjAdminProductImportHistory&action=pjActionDeleteImportFile&id={:id}" });

			$grid = $("#grid").datagrid({
				buttons: $buttons,

				columns: [
					// { text: "ID", type: "text", sortable: true },
					{ text: myLabel.import_table_file, type: "text", sortable: true },
					// { text: "Uploaded By", type: "text", sortable: true },
					{ text: myLabel.import_table_uploaded_at, type: "text", sortable: true },
					// { text: "Synced By", type: "text", sortable: true },
					{ text: myLabel.import_table_status, type: "text", sortable: true, renderer: formatStatus },
					{ text: myLabel.import_table_rows, type: "text", sortable: false, renderer: formatRows },
					{ text: myLabel.import_sync_count, type: "text", sortable: true },
					{ text: myLabel.import_sync, type: "text", sortable: false, renderer: formatAction },
					{ text: myLabel.import_sync_at, type: "text", sortable: true },
				],

				dataUrl: "index.php?controller=pjAdminProductImportHistory&action=pjActionGetHistory",

				dataType: "json",

				fields: [
					// 'id',
					'file_name',
					// 'uploaded_by',
					'uploaded_at',
					// 'synced_by',
					'status',
					'processed_rows',
					'sync_count',
					'id',
					'synced_at',
				],

				paginator: {
					paginate: true,
					total: true,
					rowCount: true
				}

			});


		}
		function formatImage(val) {
			if (!val) return '';
			return '<img src="' + val + '" width="70">';
		}

		var unchecked_rows = [];



		if ($("#grid_product_history").length > 0 && datagrid) {
			function formatLongText(val) {
				if (val == null || val == '') {
					return '';
				}

				// HTML remove
				var text = $('<div>').html(val).text();

				// limit
				var short_text = text.length > 80
					? text.substring(0, 80) + '...'
					: text;

				return '<div title="' + text.replace(/"/g, '&quot;') + '">' + short_text + '</div>';
			}
			var $buttons = [];
			var $actions = [];
			var $select = false;

			/* ACTION BUTTON */


			$actions.push({ text: myLabel.import_sync_selected, url: "index.php?controller=pjAdminProductImportHistory&action=pjActionSyncSelectedRows&id=" + import_id, render: true, confirmation: "Are you sure you want to sync selected products?" });
			/* ENABLE CHECKBOX SELECT */
			if ($actions.length > 0) {
				$select = {
					field: "id",
					name: "record[]",
					cellClass: 'cell-width-2'
				};
			}

			var $grid_product_history = $("#grid_product_history").datagrid({

				buttons: $buttons,

				columns: [

					{ text: myLabel.import_image, type: "text", sortable: false, editable: true, renderer: formatImage },

					// { text: myLabel.import_status, type: "text", sortable: true, editable: true },
					{ text: myLabel.import_status, type: "toggle", sortable: true, editable: true, positiveClass: "pj-toggle-on", negativeClass: "pj-toggle-off", positiveLabel: myLabel.active, positiveValue: "1", negativeLabel: myLabel.inactive, negativeValue: "0" },

					{ text: myLabel.import_model, type: "text", sortable: true, editable: true },

					{ text: myLabel.import_model_name, type: "text", sortable: true, editable: true },

					{ text: myLabel.import_sku, type: "text", sortable: true, editable: true },

					// { text: myLabel.import_status, type: "text", sortable: true, editable: true },

					{ text: myLabel.import_brand, type: "text", sortable: true, editable: true },

					{ text: myLabel.import_category, type: "text", sortable: true, editable: true },

					{ text: myLabel.import_article_number, type: "text", sortable: true, editable: true },

					{ text: myLabel.import_article_name, type: "text", sortable: true, editable: true },

					{ text: myLabel.import_ean, type: "text", sortable: true, editable: true },

					{ text: myLabel.import_size, type: "text", sortable: true, editable: true },

					{ text: myLabel.import_color, type: "text", sortable: true, editable: true },

					{ text: myLabel.import_stock, type: "text", sortable: true, editable: true },

					{ text: myLabel.import_price, type: "text", sortable: true, editable: true },

					{ text: myLabel.import_name_en, type: "text", sortable: true, editable: true },

					{ text: myLabel.import_short_desc_en, type: "text", sortable: false, editable: true , renderer: formatLongText },

					{ text: myLabel.import_full_description_en, type: "text", sortable: false, editable: true, renderer: formatLongText  },

					// { text: myLabel.import_row_status, type: "text", sortable: true, editable: true },

					{ text: myLabel.import_sync_status, type: "text", sortable: true, editable: false },

					{ text: myLabel.import_created_at, type: "text", sortable: true }

				],
				dataUrl: "index.php?controller=pjAdminProductImportHistory&action=pjActionGetImportRows&id=" + import_id,

				dataType: "json",

				fields: [

					'image',
					'status',
					'model',
					'model_name',
					'sku',
					// 'status',
					'brand',
					'category',
					'article_number',
					'article_name',
					'ean',
					'size',
					'color',
					'qty',
					'price',
					'name_en',
					'short_desc_en',
					'full_description_en',
					// 'row_status',
					'sync_status',
					'created_at'

				],
				paginator: {
					// actions: $actions,
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},

				saveUrl: "index.php?controller=pjAdminProductImportHistory&action=pjActionSaveImportRow&id={:id}",

				select: $select,

				onRender: function () {

					var $grid = $("#grid_product_history");

					var total_rows = 0;
					var unchecked_count = 0;

					/* ROW CHECKBOXES */
					$grid.find(".pj-table-select-row").each(function () {

						var val = $(this).val();
						total_rows++;

						if (unchecked_rows.indexOf(val) !== -1) {

							$(this).iCheck('uncheck');
							unchecked_count++;

						} else {

							$(this).iCheck('check');

						}

					});

					/* HEADER CHECKBOX */
					if (unchecked_count === 0) {
						$grid.find(".pj-table-toggle-rows").iCheck('check');
					} else {
						$grid.find(".pj-table-toggle-rows").iCheck('uncheck');
					}

				}

			});

		}



		/* ROW UNCHECK */
		$(document).on('ifUnchecked', '#grid_product_history input[name="record[]"]', function () {

			var id = $(this).val();

			if (unchecked_rows.indexOf(id) === -1) {
				unchecked_rows.push(id);
			}
		});

		/* ROW CHECK AGAIN */
		$(document).on('ifChecked', '#grid_product_history input[name="record[]"]', function () {

			var id = $(this).val();

			unchecked_rows = unchecked_rows.filter(function (row) {
				return row != id;
			});

		});
		$(document).on("click", "#btn-sync-selected", function (e) {

			e.preventDefault();

			/* TOTAL ROWS IN GRID */
			var total_rows = $("#grid_product_history .pj-table-select-row").length;

			/* IF ALL ROWS UNCHECKED */
			if (total_rows === 0 || unchecked_rows.length === total_rows) {

				swal({
					title: "No rows selected",
					text: "Please select at least one product to sync.",
					type: "warning",
					confirmButtonColor: "#d9534f"
				});

				return;
			}

			var url = "index.php?controller=pjAdminProductImportHistory&action=pjActionSyncSelectedRows&id=" + import_id;

			if (unchecked_rows.length > 0) {
				url += "&unchecked_rows=" + unchecked_rows.join(",");
			}

			swal({
				title: myLabel.import_start_title,
				text: myLabel.import_start_message,
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#11511a",
				confirmButtonText: myLabel.import_start_confirm,
				cancelButtonText: myLabel.import_cancel,
				closeOnConfirm: true
			}, function (isConfirm) {

				if (isConfirm) {

					syncImport(url, 0);

				}

			});

		});
		// $(document).on("click", "#btn-sync-selected", function (e) {

		// 	e.preventDefault();

		// 	var url = "index.php?controller=pjAdminProductImportHistory&action=pjActionSyncSelectedRows&id=" + import_id;

		// 	if (unchecked_rows.length > 0) {
		// 		url += "&unchecked_rows=" + unchecked_rows.join(",");
		// 	}

		// 	swal({
		// 		title: myLabel.import_start_title,
		// 		text: myLabel.import_start_message,
		// 		type: "warning",
		// 		showCancelButton: true,
		// 		confirmButtonColor: "#11511a",
		// 		confirmButtonText: myLabel.import_start_confirm,
		// 		cancelButtonText: myLabel.import_cancel,
		// 		closeOnConfirm: true
		// 	}, function (isConfirm) {

		// 		if (isConfirm) {

		// 			showImportProgress();
		// 			syncImport(url, 0);

		// 		}

		// 	});

		// });
		document.addEventListener("click", function (e) {
			var el = e.target.closest("#grid_product_history .pj-paginator-action");

			if (!el) return;

			e.preventDefault();
			e.stopImmediatePropagation();

			var url = el.getAttribute("href");
			if (unchecked_rows.length > 0) {
				url += "&unchecked_rows=" + unchecked_rows.join(",");
			}

			swal({
				title: myLabel.import_start_title,
				text: myLabel.import_start_message,
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#11511a",
				confirmButtonText: myLabel.import_start_confirm,
				cancelButtonText: "Cancel",
				closeOnConfirm: true
			}, function (isConfirm) {

				if (isConfirm) {

					syncImport(url, 0);

				}

			});

			// window.location.href = url;

		}, true);
		/* ===============================
		UPLOAD CSV
		=============================== */

		if ($("#btnUploadCsv").length > 0) {

			$(document).on("click", "#btnUploadCsv", function (e) {

				e.preventDefault();

				var file = $("#csv_file")[0].files[0];

				/* =========================
				   FILE VALIDATION
				========================= */

				if (!file) {

					swal({
						title: myLabel.import_no_file_title,
						text: myLabel.import_no_file_message,
						type: "warning",
						confirmButtonColor: "#11511a"
					});

					return;
				}

				/* =========================
				   CSV EXTENSION VALIDATION
				========================= */

				var fileName = file.name.toLowerCase();

				if (!fileName.endsWith(".csv")) {

					swal({
						title: myLabel.import_invalid_file_title,
						text: myLabel.import_invalid_file_message,
						type: "error",
						confirmButtonColor: "#d9534f"
					});

					return;
				}

				var formData = new FormData();
				formData.append("file", file);

				/* =========================
				   SHOW LOADER
				========================= */

				$("#upload_loader").show();
				$("#btnUploadCsv").prop("disabled", true);

				swal({
					title: myLabel.import_uploading_title,
					text: myLabel.import_uploading_message,
					type: "info",
					showConfirmButton: false,
					showCancelButton: false
				});

				$.ajax({

					url: "index.php?controller=pjAdminProductImportHistory&action=pjActionUploadImportFile",
					type: "POST",
					data: formData,
					processData: false,
					contentType: false,
					dataType: "json",

					success: function (data) {

						$("#upload_loader").hide();
						$("#btnUploadCsv").prop("disabled", false);

						swal.close();

						setTimeout(function () {

							if (data && data.status === "OK") {

								$("#frmUploadImport")[0].reset();

								// if (typeof refreshImportGrid === "function") {
								// 	refreshImportGrid();
								// }

								swal({
									title: myLabel.import_upload_success,
									text: data.text ? data.text : myLabel.import_csv_success,
									type: "success",
									confirmButtonColor: "#11511a"
								}, function () {

									// Redirect after clicking OK
									window.location.href = "index.php?controller=pjAdminProductImportHistory&action=pjActionIndex&sync=1&id=" + data.insert_id;

								});

							} else {

								swal({
									title: myLabel.import_upload_error_title,
									text: (data && data.text) ? data.text : myLabel.import_invalid_csv,
									type: "error",
									confirmButtonColor: "#d9534f"
								});

							}

						}, 300);

					},

					error: function (xhr) {
						$("#upload_loader").hide();
						$("#btnUploadCsv").prop("disabled", false);

						swal.close();

						var message = myLabel.import_server_error;

						if (xhr.responseText) {
							message = xhr.responseText;
						}

						swal({
							title: myLabel.import_file_upload_failed,
							text: message,
							type: "error",
							confirmButtonColor: "#d9534f"
						});

					}

				});

			});

		}
		function showImportProgress() {

			$("#import_progress_wrapper").show();
			updateImportProgress(0);

		}

		function updateImportProgress(percent) {

			$("#import_progress_bar")
				.css("width", percent + "%")
				.text(percent + "%");

		}

		function hideImportProgress() {

			setTimeout(function () {

				$("#import_progress_wrapper").fadeOut();
				updateImportProgress(0);

			}, 800);

		}

		$(document).on("submit", ".frm-filter-stock", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid_stock.datagrid("option", "content"),
				cache = $grid_stock.datagrid("option", "cache");
			$.extend(cache, {
				q: $this.find("input[name='q']").val()
			});
			$grid_stock.datagrid("option", "cache", cache);
			$grid_stock.datagrid("load", "index.php?controller=pjAdminProductImportHistory&action=pjActionGetStock", "name", "ASC", content.page, content.rowCount);
			return false;
		}).on("click", ".btn-all", function (e) {
			$(this).addClass("btn-primary active").removeClass("btn-default")
				.siblings(".btn").removeClass("btn-primary active").addClass("btn-default");
			var content = $grid_product_history.datagrid("option", "content"),
				cache = $grid_product_history.datagrid("option", "cache");
			$.extend(cache, {
				status: "",
				q: "",
				is_out: ""
			});
			$grid_product_history.datagrid("option", "cache", cache);
			$grid_product_history.datagrid("load", "index.php?controller=pjAdminProductImportHistory&action=pjActionGetProduct", "name", "ASC", content.page, content.rowCount);
			return false;
		}).on("click", ".btn-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid_product_history.datagrid("option", "content"),
				cache = $grid_product_history.datagrid("option", "cache"),
				obj = {};
			$this.addClass("btn-primary active").removeClass("btn-default")
				.siblings(".btn").removeClass("btn-primary active").addClass("btn-default");
			obj.status = "";
			if ($this.data("value") == '3' && $this.data("column") == 'status') {
				obj['is_out'] = 'yes';
			} else {
				obj['is_out'] = '';
				obj[$this.data("column")] = $this.data("value");
			}
			$.extend(cache, obj);
			$grid_product_history.datagrid("option", "cache", cache);
			$grid_product_history.datagrid("load", "index.php?controller=pjAdminProductImportHistory&action=pjActionGetProduct", "name", "ASC", content.page, content.rowCount);
			return false;
		}).on("submit", ".frm-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid_product_history.datagrid("option", "content"),
				cache = $grid_product_history.datagrid("option", "cache");
			$.extend(cache, {
				q: $this.find("input[name='q']").val()
			});
			$grid_product_history.datagrid("option", "cache", cache);
			$grid_product_history.datagrid("load", "index.php?controller=pjAdminProductImportHistory&action=pjActionGetProduct", "id", "ASC", content.page, content.rowCount);
			return false;
		}).on("click", ".btn-sync", function (e) {

			e.preventDefault();

			var file_id = $(this).data("id");

			swal({
				title: myLabel.import_start_title,
				text: myLabel.import_start_message,
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#11511a",
				confirmButtonText: myLabel.import_start_confirm,
				cancelButtonText: myLabel.import_cancel,
				closeOnConfirm: true
			}, function (isConfirm) {

				if (isConfirm) {

					syncImportImportFromFile(file_id, 0);

				}

			});

		});

	});
})(jQuery);