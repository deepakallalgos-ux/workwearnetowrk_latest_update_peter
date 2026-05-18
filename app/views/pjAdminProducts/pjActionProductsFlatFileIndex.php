<style>
	#grid_flat_file table {
		min-width: 1200px;
	}

	#grid_flat_file .table-responsive {
		overflow-x: auto;
		overflow-y: hidden;
	}

	#grid_flat_file table {
		white-space: nowrap;
	}

	#grid_flat_file th,
	#grid_flat_file td {
		white-space: nowrap;
	}

	#grid_flat_file img {
		width: 100px;
		height: auto;
	}

	#grid_flat_file td {
		white-space: normal !important;
		word-break: break-word;
	}
</style>
<div class="row wrapper border-bottom white-bg page-heading">
	<div class="col-sm-12">
		<div class="row">
			<div class="col-sm-10">
				<h2><?php __('infoProductsTitle', false, true); ?></h2>
			</div>
		</div><!-- /.row -->

		<p class="m-b-none"><i class="fa fa-info-circle"></i> <?php __('infoProductsDesc', false, true); ?></p>
	</div><!-- /.col-md-12 -->
</div>

<div class="row wrapper wrapper-content animated fadeInRight">
	<div class="col-lg-12">
		<?php
		$product_statuses = __('product_statuses', true);
		$error_code = $controller->_get->toString('err');
		if (!empty($error_code)) {
			$titles = __('error_titles', true);
			$bodies = __('error_bodies', true);
			switch (true) {
				case in_array($error_code, array('AP01', 'AP03')):
		?>
					<div class="alert alert-success">
						<i class="fa fa-check m-r-xs"></i>
						<strong><?php echo @$titles[$error_code]; ?></strong>
						<?php echo @$bodies[$error_code] ?>
					</div>
				<?php
					break;
				case in_array($error_code, array('AP04', 'AP05', 'AP08', 'AP09')):
					$bodies_text = str_replace("{SIZE}", ini_get('post_max_size'), @$bodies[$error_code]);
				?>
					<div class="alert alert-danger">
						<i class="fa fa-exclamation-triangle m-r-xs"></i>
						<strong><?php echo @$titles[$error_code]; ?></strong>
						<?php echo $bodies_text; ?>
					</div>
		<?php
					break;
			}
		}
		?>
		<div class="ibox float-e-margins">
			<div class="ibox-content">
				<div class="row m-b-md">
					<?php if ($tpl['has_create']) { ?>
						<div class="col-lg-2 col-md-3 col-sm-3">
							<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProducts&amp;action=pjActionCreate" class="btn btn-primary"><i class="fa fa-plus"></i> <?php __('btnAddProduct') ?></a>
						</div><!-- /.col-md-6 -->
					<?php } ?>
					<div class="col-lg-3 col-md-3 col-sm-5">
						<form action="" method="get" class="form-horizontal frm-filter-flat-file">
							<div class="input-group">
								<input type="text" name="q" placeholder="<?php __('plugin_base_btn_search', false, true); ?>" class="form-control">
								<div class="input-group-btn">
									<button class="btn btn-primary" type="submit">
										<i class="fa fa-search"></i>
									</button>
								</div>
							</div>
						</form>
					</div><!-- /.col-md-3 -->

				</div><!-- /.row -->

				<div id="collapseOne" class="collapse" style="height: 0;" aria-expanded="false">
					<div class="m-b-lg">
						<ul class="agile-list no-padding">
							<li class="success-element b-r-sm">
								<div class="panel-body">
									<form method="get" class="frm-filter-advanced">

										<div class="row">
											<div class="col-sm-4 col-xs-12">
												<div class="form-group">
													<label class="control-label"><?php __('product_name'); ?></label>
													<input class="form-control" type="text" name="name" id="name" value="<?php echo $controller->_get->check('name') ? $controller->_get->toString('name') : ''; ?>">
												</div>
											</div>
											<div class="col-sm-4 col-xs-12">
												<div class="form-group">
													<label class="control-label"><?php __('product_sku'); ?></label>
													<input class="form-control" type="text" name="sku" id="sku" value="<?php echo $controller->_get->check('sku') ? $controller->_get->toString('sku') : ''; ?>">
												</div>
											</div>
											<div class="col-sm-4 col-xs-12">
												<div class="form-group">
													<label class="control-label"><?php __('product_category'); ?></label>
													<select name="category_id" class="form-control">
														<option value="">-- <?php __('lblChoose'); ?> --</option>
														<?php
														foreach ($tpl['category_arr'] as $category) {
														?><option value="<?php echo $category['data']['id']; ?>" <?php echo $controller->_get->check('category_id') && $controller->_get->toInt('category_id') == $category['data']['id'] ? ' selected="selected"' : NULL; ?>><?php echo str_repeat("-----", $category['deep']) . " " . pjSanitize::html($category['data']['name']); ?></option><?php
																																																																																															}
																																																																																																?>
													</select>
												</div>
											</div>
											<div class="col-sm-4 col-xs-12">
												<div class="form-group">
													<label class="control-label"><?php __('product_status'); ?></label>
													<select name="status" class="form-control">
														<option value="">-- <?php __('lblChoose'); ?> --</option>
														<?php
														foreach ($product_statuses as $k => $v) {
														?><option value="<?php echo $k; ?>" <?php echo $controller->_get->check('status') && $controller->_get->toString('status') == $k ? ' selected="selected"' : NULL; ?>><?php echo pjSanitize::html($v); ?></option><?php
																																																																		}
																																																																			?>
													</select>
												</div>
											</div>
											<div class="col-sm-4 col-xs-12">
												<div class="form-group">
													<label class="control-label"><?php __('product_is_digital'); ?></label>
													<div>
														<input type="checkbox" class="i-checks" id="is_digital" name="is_digital" value="1" <?php echo $controller->_get->check('is_digital') ? ' checked="checked"' : NULL; ?> />
													</div>
												</div>
											</div>
											<div class="col-sm-4 col-xs-12">
												<div class="form-group">
													<label class="control-label"><?php __('product_is_featured'); ?></label>
													<div>
														<input type="checkbox" class="i-checks" id="is_featured" name="is_featured" value="1" <?php echo $controller->_get->check('is_featured') ? ' checked="checked"' : NULL; ?> />
													</div>
												</div>
											</div>
										</div>

										<div class="m-t-sm">
											<button class="btn btn-primary" type="submit"><?php __('btnSearch'); ?></button>
											<button class="btn btn-primary btn-outline" type="reset"><?php __('btnCancel'); ?></button>
										</div>
									</form>
								</div>
								<!-- /.panel-body -->
							</li>
							<!-- /.panel panel-primary -->
						</ul>
					</div>
					<!-- /.m-b-lg -->
				</div>

				<div id="grid_flat_file"></div>
			</div>
		</div>
	</div><!-- /.col-lg-12 -->
</div>

<script type="text/javascript">
	var pjGrid = pjGrid || {};
	pjGrid.queryString = "";
	<?php
	if ($controller->_get->check('is_out')) {
	?>pjGrid.queryString += "&is_out=yes";
	<?php
	}
	if ($controller->_get->check('is_active_out')) {
	?>pjGrid.queryString += "&is_active_out=yes";
	<?php
	}
	?>
	var myLabel = myLabel || {};
	myLabel.image = <?php x__encode('product_image'); ?>;
	myLabel.name = <?php x__encode('lblName'); ?>;
	myLabel.sku = <?php x__encode('product_sku'); ?>;
	myLabel.stock = <?php x__encode('product_stock'); ?>;
	myLabel.price = <?php x__encode('product_stock_price'); ?>;
	myLabel.status = <?php x__encode('lblStatus'); ?>;
	myLabel.active = "<?php echo $product_statuses[1]; ?>";
	myLabel.inactive = "<?php echo $product_statuses[2]; ?>";
	myLabel.delete_selected = <?php x__encode('delete_selected'); ?>;
	myLabel.delete_confirmation = <?php x__encode('delete_confirmation'); ?>;
	myLabel.exported = <?php x__encode('lblExport'); ?>;

	myLabel.has_create = <?php echo (int) $tpl['has_create']; ?>;
	myLabel.has_update = <?php echo (int) $tpl['has_update']; ?>;
	myLabel.has_delete = <?php echo (int) $tpl['has_delete']; ?>;
	myLabel.has_delete_bulk = <?php echo (int) $tpl['has_delete_bulk']; ?>;
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
</script>