<?php

$product_statuses = __('product_statuses', true);
$import_id = 0;
$company_id_for_import = isset($default_company['id']) ? (int) $default_company['id'] : 0;

if (!empty($_REQUEST['id'])) {
	$import_id = (int) $_REQUEST['id'];
	if ($import_id > 0 && $company_id_for_import > 0) {
		$import_exists = pjProductImportHistoryModel::factory()
			->reset()
			->where('t1.id', $import_id)
			->where('t1.company_id', $company_id_for_import)
			->limit(1)
			->findCount()
			->getData();
		if ((int) $import_exists < 1) {
			$import_id = 0;
		}
	}
?>
	<style>
		#grid_product_history table {
			min-width: 1200px;
		}

		#grid_product_history .table-responsive {
			overflow-x: auto;
			overflow-y: hidden;
		}

		#grid_product_history table {
			white-space: nowrap;
		}

		#grid_product_history th,
		#grid_product_history td {
			white-space: nowrap;
		}

		#grid_product_history img {
			width: 100px;
			height: auto;
		}

		#grid_product_history td {
			white-space: normal !important;
			word-break: break-word;
		}
	</style>
	<div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-sm-12">
			<div class="row">
				<div class="col-sm-10">
					<h2><?php __('infoProductsHistoryImportManager', false, true); ?></h2>
				</div>
			</div><!-- /.row -->

			<p class="m-b-none"><i class="fa fa-info-circle"></i> <?php __('infoProductsHistoryImportManagerDesc', false, true); ?></p>
		</div><!-- /.col-md-12 -->
	</div>
	<div class="row wrapper wrapper-content animated fadeInRight">
		<div class="col-lg-12">

			<div class="ibox">
				<div class="ibox-content">
					<?php if (isset($_REQUEST['sync']) && (int) $_REQUEST['sync'] === 1) { ?>
						<div class="m-b-sm">
							<button id="btn-sync-selected" class="btn btn-primary pj-paginator-action">
								<i class="fa fa-sync"></i> <?php __('import_sync_selected', false, true); ?>
							</button>
						</div>
					<?php } ?>
					<div id="grid_product_history" class="datagrid-scroll"></div>


				</div>
			</div>

		</div>
	</div>


<?php } else { ?>

	<div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-sm-12">
			<div class="row">
				<div class="col-sm-10">
					<h2><?php __('infoProductsImportManager', false, true); ?></h2>
				</div>
			</div><!-- /.row -->

			<p class="m-b-none"><i class="fa fa-info-circle"></i> <?php __('infoProductsImportManagerDesc', false, true); ?></p>
		</div><!-- /.col-md-12 -->
	</div>

	<div class="row wrapper wrapper-content animated fadeInRight">
		<div class="col-lg-12">

			<div class="ibox">
				<div class="ibox-content">
					<div id="import_progress_wrapper" style="display:none;margin-top:10px;">

						<div class="progress">
							<div id="import_progress_bar"
								class="progress-bar progress-bar-success"
								role="progressbar"
								style="width:0%">
								0%
							</div>
						</div>

					</div>
					<!-- Upload Section -->
					<div class="row m-b-lg">
						<div class="col-md-6">
							<form id="frmUploadImport" enctype="multipart/form-data">
								<div class="input-group">

									<input type="file" name="file" id="csv_file" class="form-control" accept=".csv">
									<span class="input-group-btn">
										<button type="button" id="btnUploadCsv" class="btn btn-primary">
											<i class="fa fa-upload"></i> <?php __('import_upload_section'); ?>
										</button>

									</span>
								</div>

							</form>

						</div>
						<div class="col-md-6">
							<a href="index.php?controller=pjAdminProductImportHistory&action=pjActionDownloadSampleCsv"
								class="btn btn-success">
								<i class="fa fa-download"></i> Download Sample CSV
							</a>

						</div>
					</div>

					<div id="grid"></div>


				</div>
			</div>

		</div>
	</div>
<?php } ?>

<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.import_table_file = <?php x__encode('import_table_file'); ?>;
	myLabel.import_table_uploaded_at = <?php x__encode('import_table_uploaded_at'); ?>;
	myLabel.import_table_status = <?php x__encode('import_table_status'); ?>;
	myLabel.import_table_rows = <?php x__encode('import_table_rows'); ?>;
	myLabel.import_sync_at = <?php x__encode('import_sync_at'); ?>;
	myLabel.import_sync = <?php x__encode('import_sync'); ?>;
	myLabel.import_status_uploaded = <?php x__encode('import_status_uploaded'); ?>;
	myLabel.import_status_processing = <?php x__encode('import_status_processing'); ?>;
	myLabel.import_status_completed = <?php x__encode('import_status_completed'); ?>;
	myLabel.import_status_failed = <?php x__encode('import_status_failed'); ?>;
	myLabel.import_page_title = <?php x__encode('import_page_title'); ?>;
	myLabel.import_processing_message = <?php x__encode('import_processing_message'); ?>;
	myLabel.import_completed_title = <?php x__encode('import_completed_title'); ?>;
	myLabel.import_completed_message = <?php x__encode('import_completed_message'); ?>;
	myLabel.import_error_title = <?php x__encode('import_error_title'); ?>;
	myLabel.import_no_file_title = <?php x__encode('import_no_file_title'); ?>;
	myLabel.import_no_file_message = <?php x__encode('import_no_file_message'); ?>;
	myLabel.import_invalid_file_title = <?php x__encode('import_invalid_file_title'); ?>;
	myLabel.import_invalid_file_message = <?php x__encode('import_invalid_file_message'); ?>;
	myLabel.import_uploading_title = <?php x__encode('import_uploading_title'); ?>;
	myLabel.import_uploading_message = <?php x__encode('import_uploading_message'); ?>;
	myLabel.import_server_error = <?php x__encode('import_server_error'); ?>;
	myLabel.import_file_upload_failed = <?php x__encode('import_file_upload_failed'); ?>;
	myLabel.import_upload_error_title = <?php x__encode('import_upload_error_title'); ?>;
	myLabel.import_upload_success = <?php x__encode('import_upload_success'); ?>;
	myLabel.import_start_title = <?php x__encode('import_start_title'); ?>;
	myLabel.import_start_message = <?php x__encode('import_start_message'); ?>;
	myLabel.import_start_confirm = <?php x__encode('import_start_confirm'); ?>;
	myLabel.import_cancel = <?php x__encode('import_cancel'); ?>;
	myLabel.import_sync_selected = <?php x__encode('import_sync_selected'); ?>;
	myLabel.import_sync_count = <?php x__encode('import_sync_count'); ?>;
	myLabel.import_view_file = <?php x__encode('import_view_file'); ?>;
	myLabel.import_csv_success = <?php x__encode('import_csv_success'); ?>;
	myLabel.import_invalid_csv = <?php x__encode('import_invalid_csv'); ?>;
	myLabel.import_sync_count = <?php x__encode('import_sync_count'); ?>;
	myLabel.import_view_file = <?php x__encode('import_view_file'); ?>;
	myLabel.import_image = <?php x__encode('import_image'); ?>;
	myLabel.import_model = <?php x__encode('import_model'); ?>;
	myLabel.import_model_name = <?php x__encode('import_model_name'); ?>;
	myLabel.import_sku = <?php x__encode('import_sku'); ?>;
	myLabel.import_article_number = <?php x__encode('import_article_number'); ?>;
	myLabel.import_article_name = <?php x__encode('import_article_name'); ?>;
	myLabel.import_brand = <?php x__encode('import_brand'); ?>;
	myLabel.import_category = <?php x__encode('import_category'); ?>;
	myLabel.import_size = <?php x__encode('import_size'); ?>;
	myLabel.import_color = <?php x__encode('import_color'); ?>;
	myLabel.import_ean = <?php x__encode('import_ean'); ?>;
	myLabel.import_stock = <?php x__encode('import_stock'); ?>;
	myLabel.import_price = <?php x__encode('import_price'); ?>;
	myLabel.import_w_number = <?php x__encode('import_w_number'); ?>;
	myLabel.import_status = <?php x__encode('import_status'); ?>;
	myLabel.import_name_en = <?php x__encode('import_name_en'); ?>;
	myLabel.import_short_desc_en = <?php x__encode('import_short_desc_en'); ?>;
	myLabel.import_full_description_en = <?php x__encode('import_full_description_en'); ?>;
	myLabel.import_row_status = <?php x__encode('import_row_status'); ?>;
	myLabel.import_sync_status = <?php x__encode('import_sync_status'); ?>;
	myLabel.import_created_at = <?php x__encode('import_created_at'); ?>;
	myLabel.import_sync = <?php x__encode('import_sync'); ?>;
	myLabel.status = <?php x__encode('lblStatus'); ?>;
	myLabel.active = "<?php echo $product_statuses[1]; ?>";
	myLabel.inactive = "<?php echo $product_statuses[2]; ?>";
	var import_id = <?php echo (int)$import_id; ?>;
</script>