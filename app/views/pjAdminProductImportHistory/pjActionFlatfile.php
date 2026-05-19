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
							<i class="fa fa-download"></i> <?php __('import_btn_download_sample', false, true); ?>
						</a>

					</div>
				</div>

				<div id="grid"></div>


			</div>
		</div>

	</div>
</div>

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
	myLabel.import_sync_modal_title = <?php x__encode('import_sync_modal_title'); ?>;
	myLabel.import_sync_modal_sub = <?php x__encode('import_sync_modal_sub'); ?>;
	myLabel.import_sync_preparing = <?php x__encode('import_sync_preparing'); ?>;
	myLabel.import_sync_progress_row = <?php x__encode('import_sync_progress_row'); ?>;
	myLabel.import_sync_last_batch = <?php x__encode('import_sync_last_batch'); ?>;
	myLabel.import_invalid_response = <?php x__encode('import_invalid_response'); ?>;
	myLabel.import_sync_timeout = <?php x__encode('import_sync_timeout'); ?>;
	myLabel.import_error_generic = <?php x__encode('import_error_generic'); ?>;
	myLabel.import_request_failed = <?php x__encode('import_request_failed'); ?>;
</script>