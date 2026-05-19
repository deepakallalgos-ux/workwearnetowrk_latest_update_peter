<?php
if (!defined("ROOT_PATH")) {
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminProductImportHistory extends pjAdmin
{
	const IMPORT_SYNC_BATCH_SIZE = 15;

	/** @var array product_id|url => gallery id (avoids re-downloading the same image URL) */
	private static $importImageGalleryCache = array();

	/** @var array product_id => true — model image import once per product per sync run */
	private static $importModelImageDone = array();

	private function getImportSyncBatchLimit()
	{
		$limit = (int) self::IMPORT_SYNC_BATCH_SIZE;
		if ($this->_post->check('batch_size')) {
			$requested = (int) $this->_post->toInt('batch_size');
			if ($requested > 0 && $requested <= 150) {
				$limit = $requested;
			}
		}
		return $limit;
	}

	private function importSyncProgressPayload($status, $offset, $next_offset, $total_rows, $batch_count, $extra = array())
	{
		$processed = min($next_offset, $total_rows);
		$payload = array(
			'status'          => $status,
			'offset'          => $next_offset,
			'total'           => $total_rows,
			'processed'       => $processed,
			'processed_from'  => $total_rows > 0 ? ($offset + 1) : 0,
			'processed_to'    => $processed,
			'batch'           => $batch_count,
			'url'             => $_SERVER['REQUEST_URI'],
		);
		return array_merge($payload, $extra);
	}

	private function clearImportCsvSession($import_id)
	{
		$key = 'pj_import_csv_' . (int) $import_id;
		if (isset($_SESSION[$key])) {
			unset($_SESSION[$key]);
		}
	}

	/**
	 * Parse CSV once per import run (stored in session) — avoids re-reading the file every batch.
	 */
	private function loadImportCsvData($import_id, $csv_path)
	{
		$key = 'pj_import_csv_' . (int) $import_id;
		if (isset($_SESSION[$key]) && is_array($_SESSION[$key]['rows'])) {
			return $_SESSION[$key];
		}

		$handle = fopen($csv_path, 'r');
		if (!$handle) {
			return null;
		}

		$delimiter = ';';
		$header = fgetcsv($handle, 0, $delimiter);
		if ($header === false || count($header) < 2) {
			rewind($handle);
			$delimiter = ',';
			$header = fgetcsv($handle, 0, $delimiter);
		}

		$rows = array();
		$models = array();
		while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
			if (count($row) !== count($header)) {
				continue;
			}
			$assoc = array_combine($header, $row);
			$rows[] = $assoc;
			if (!empty($assoc['model'])) {
				$models[] = $assoc['model'];
			}
		}
		fclose($handle);

		$data = array(
			'rows'   => $rows,
			'models' => array_values(array_unique($models)),
		);
		$_SESSION[$key] = $data;
		return $data;
	}

	private function writeLog($message)
	{
		$file = PJ_INSTALL_PATH . 'import_debug.txt';
		$date = date("Y-m-d H:i:s");

		if (is_array($message) || is_object($message)) {
			$message = print_r($message, true);
		}

		file_put_contents($file, "[" . $date . "] " . $message . PHP_EOL, FILE_APPEND);
	}

	public function pjActionIndex()
	{
		$company_id = $_SESSION[$this->defaultCompany]['id'];

		$this->checkLogin();
		if (!pjAuth::factory()->hasAccess()) {
			$this->sendForbidden();
			return;
		}
		$pjProductModel = pjProductModel::factory();
		$pjProductModel->select('t1.id, t2.content AS `name`')
			->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='" . $this->getLocaleId() . "'", 'left outer');
		if (isset($company_id) && (int)$company_id > 0) {
			$pjProductModel->where('t1.company_id', $company_id);
		}
		$product_arr =	$pjProductModel->orderBy('`name` ASC')
			->findAll()->getData();
		$this->set(
			'product_arr',
			$product_arr

		);

		if (pjObject::getPlugin('pjPayments') !== NULL) {
			$this->set('payment_option_arr', pjPaymentOptionModel::factory()->getOptions(NULL));
			$this->set('payment_titles', pjPayments::getPaymentTitles($this->getForeignId(), $this->getLocaleId()));
		} else {
			$this->set('payment_titles', __('payment_methods', true));
		}
		$dm = $this->getDependencyManager();
		$this->set('has_update', pjAuth::factory('pjAdminProductImportHistory', 'pjActionUpdate')->hasAccess());
		$this->set('has_delete', pjAuth::factory('pjAdminProductImportHistory', 'pjActionDeleteOrder')->hasAccess());
		$this->set('has_delete_bulk', pjAuth::factory('pjAdminProductImportHistory', 'pjActionDeleteOrderBulk')->hasAccess());

		$this->appendCss('css/select2.min.css', PJ_THIRD_PARTY_PATH . 'select2/');
		$this->appendJs('js/select2.full.min.js', PJ_THIRD_PARTY_PATH . 'select2/');
		$this->appendJs('moment-with-locales.min.js', PJ_THIRD_PARTY_PATH . 'moment/');
		$this->appendCss('datepicker.css', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/');
		$this->appendJs('bootstrap-datepicker.js', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/');
		$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
		$this->appendCss('sweetalert.css', $dm->getPath('sweetalert'), false, false);
		$this->appendJs('sweetalert.min.js', $dm->getPath('sweetalert'), false, false);

		$this->appendJs('pjAdminProductImportHistory.js');
	}


	public function pjActionGetImportRows()
	{
		$this->setAjax(true);

		if (!$this->isXHR()) {
			exit;
		}
		$this->checkLogin();
		if (!pjAuth::factory('pjAdminProductImportHistory', 'pjActionIndex')->hasAccess()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Access denied.'));
		}

		$model = pjProductImportRowsModel::factory();

		$column = 'id';
		$direction = 'DESC';

		if ($this->_get->check('column') && $this->_get->check('direction')) {
			$column = $this->_get->toString('column');
			$direction = $this->_get->toString('direction');
		}

		$import_id = $this->_get->toInt('id');

		if ($import_id > 0) {
			$model->where('t1.import_id', $import_id);
		}

		$company_id = $_SESSION[$this->defaultCompany]['id'];

		if ($company_id > 0) {
			$model->where('t1.company_id', $company_id);
		}

		$total = $model->findCount()->getData();

		$rowCount = $this->_get->toInt('rowCount');
		$page = $this->_get->toInt('page');

		if ($rowCount <= 0) {
			$rowCount = 20;
		}

		if ($page <= 0) {
			$page = 1;
		}

		$offset = ($page - 1) * $rowCount;

		$data = $model
			->reset()
			->where('t1.import_id', $import_id)
			->where('t1.company_id', $company_id)
			->orderBy("$column $direction")
			->limit($rowCount, $offset)
			->findAll()
			->getData();

		// ⭐ IMPORTANT FOR PAGINATION
		$pages = ceil($total / $rowCount);

		pjAppController::jsonResponse(array(
			'data' => $data,
			'total' => $total,
			'page' => $page,
			'pages' => $pages,
			'rowCount' => $rowCount
		));
	}


	public function pjActionSyncSelectedRows()
	{
		$this->setAjax(true);

		//$this->writeLog("=== TABLE IMPORT START ===");

		if (!$this->isXHR()) {
			//$this->writeLog("ERROR: Not AJAX");

			pjAppController::jsonResponse([
				'status' => 'ERR',
				'text'   => 'Invalid request'
			]);
		}

		$this->checkLogin();

		@set_time_limit(300);
		@ini_set('memory_limit', '512M');
		if (function_exists('session_write_close')) {
			session_write_close();
		}

		$limit  = $this->getImportSyncBatchLimit();
		$offset = $this->_post->check('offset') ? (int)$this->_post->toInt('offset') : 0;
		$import_id = $this->_get->check('id') ? (int)$this->_get->toInt('id') : 0;

		/* READ UNCHECKED ROWS */

		$unchecked = [];

		if ($this->_get->check('unchecked_rows')) {
			$unchecked = array_map('intval', explode(',', $this->_get->toString('unchecked_rows')));
		}
		// echo "<pre>";
		// print_r($unchecked);
		// die;
		//$this->writeLog("OFFSET: " . $offset);
		//$this->writeLog("IMPORT ID: " . $import_id);
		//$this->writeLog("UNCHECKED ROWS: " . json_encode($unchecked));

		if (!$import_id) {
			pjAppController::jsonResponse([
				'status' => 'ERR',
				'text'   => 'Missing import id'
			]);
		}

		$company_id = isset($_SESSION[$this->defaultCompany]['id']) ? (int) $_SESSION[$this->defaultCompany]['id'] : 0;
		if ($company_id <= 0) {
			pjAppController::jsonResponse([
				'status' => 'ERR',
				'text'   => 'No company selected.'
			]);
		}

		$history = pjProductImportHistoryModel::factory()
			->find($import_id)
			->getData();
		$latest_import = pjProductImportHistoryModel::factory()
			->where('company_id', $company_id)
			->orderBy('id DESC')
			->limit(1)
			->findAll()
			->getData();
		$latest_id = !empty($latest_import) ? (int)$latest_import[0]['id'] : 0;
		if ($import_id != $latest_id) {

			pjAppController::jsonResponse([
				'status' => 'ERR',
				'text'   => 'The selected record cannot be synced. Only the latest import can be synced.'
			]);
		}
		if (!$history) {
			pjAppController::jsonResponse([
				'status' => 'ERR',
				'text'   => 'Import history not found'
			]);
		}

		/* TOTAL ROWS */

		$total_query = pjProductImportRowsModel::factory()
			->where('t1.import_id', $import_id)
			->where('t1.company_id', $company_id);

		if (!empty($unchecked)) {
			$total_query->whereNotIn('t1.id', $unchecked);
		}

		$total_rows = (int) $total_query->findCount()->getData();

		if ($total_rows < 1) {
			pjAppController::jsonResponse([
				'status' => 'ERR',
				'text'   => 'Please select records.'
			]);
		}

		//$this->writeLog("TOTAL TABLE ROWS: " . $total_rows);

		/* =========================
       COLLECT IMPORT MODELS
    	========================= */

		$model_query = pjProductImportRowsModel::factory()
			->select('t1.model')
			->where('t1.import_id', $import_id)
			->where('t1.company_id', $company_id);

		if (!empty($unchecked)) {
			$model_query->whereNotIn('t1.id', $unchecked);
		}

		$import_models = $model_query->findAll()->getData();

		$model_list = [];

		foreach ($import_models as $m) {
			if (!empty($m['model'])) {
				$model_list[] = $m['model'];
			}
		}

		//$this->writeLog("IMPORT MODELS: " . implode(",", $model_list));

		/* STOP IF FINISHED */

		if ($offset >= $total_rows) {

			//$this->writeLog("NO MORE ROWS TO PROCESS");

			pjAppController::jsonResponse($this->importSyncProgressPayload('DONE', $offset, $total_rows, $total_rows, 0));
		}

		/* START SYNC */

		if ($offset == 0) {
			self::$importImageGalleryCache = array();
			self::$importModelImageDone = array();

			//$this->writeLog("SYNC STARTED");

			pjProductImportHistoryModel::factory()
				->reset()
				->set('id', $import_id)
				->modify([
					'status'         => 'processing',
					'synced_by'      => $this->getUserId(),
					'synced_at'      => date('Y-m-d H:i:s'),
					'sync_count'     => (int)$history['sync_count'] + 1,
					'processed_rows' => 0,
					'failed_rows'    => 0
				]);
		}

		/* GET BATCH */

		$query = pjProductImportRowsModel::factory()
			->where('t1.import_id', $import_id)
			->where('t1.company_id', $company_id);

		if (!empty($unchecked)) {
			$query->whereNotIn('t1.id', $unchecked);
		}

		$rows = $query
			->limit($limit, $offset)
			->findAll()
			->getData();
		// echo "<pre>";
		// print_r($rows);
		// die;
		foreach ($rows as $data) {

			try {
				$status = $data['status'] == 1 ? 'T' : 'F';

				//$this->writeLog("IMPORT MODEL: " . $data['model']);

				$product_id = $this->getOrCreateProduct(
					$data['model'],
					$data['model_name'],
					$data['sku'],
					1,
					$company_id
				);

				$this->assignBrand($product_id, $data['brand'], $company_id);

				$this->assignCategory($product_id, $data['category'], $company_id);

				$this->updateProductLang(
					$product_id,
					$data['name_en'],
					$data['short_desc_en'],
					$data['full_description_en']
				);

				$this->updateProductMeta($product_id, $data);

				$img_id = $this->importGalleryImages($product_id, $data);

				$buying_price = isset($data['buying_price']) && $data['buying_price'] !== '' ? $data['buying_price'] : null;

				$this->addStockViaUpdate(
					$product_id,
					$data['size'],
					$data['color'],
					$data['article_number'],
					$data['article_name'],
					$data['ean'],
					$data['qty'],
					$data['price'],
					$buying_price,
					$img_id,
					$status
				);

				pjProductImportRowsModel::factory()
					->reset()
					->set('id', $data['id'])
					->modify([
						'sync_status' => 'synced'
					]);
			} catch (Exception $e) {

				//$this->writeLog("ROW FAILED: " . $e->getMessage());

				pjProductImportRowsModel::factory()
					->reset()
					->set('id', $data['id'])
					->modify([
						'sync_status' => 'error'
					]);
			}
		}

		/* UPDATE PROGRESS */

		$next_offset = $offset + $limit;

		if ($next_offset > $total_rows) {
			$next_offset = $total_rows;
		}

		//$this->writeLog("NEXT OFFSET: " . $next_offset);

		pjProductImportHistoryModel::factory()
			->reset()
			->set('id', $import_id)
			->modify([
				'processed_rows' => $next_offset
			]);

		/* CONTINUE BATCH */

		if ($next_offset < $total_rows) {

			pjAppController::jsonResponse($this->importSyncProgressPayload('OK', $offset, $next_offset, $total_rows, count($rows)));
		}

		/* =========================
		BUILD CSV VARIANTS ARRAY
		========================= */

		$csv_variants = [];

		$variant_query = pjProductImportRowsModel::factory()
			->select('model, article_number')
			->where('import_id', $import_id)
			->where('company_id', $company_id);

		if (!empty($unchecked)) {
			$variant_query->whereNotIn('id', $unchecked);
		}

		$variant_rows = $variant_query->findAll()->getData();

		foreach ($variant_rows as $row) {

			$model = $row['model'];
			$article = $row['article_number'];

			if (!isset($csv_variants[$model])) {
				$csv_variants[$model] = [];
			}

			$csv_variants[$model][] = $article;
		}

		//$this->writeLog("CSV VARIANTS BUILT");


		/* =========================
		FINAL CLEANUP
		========================= */

		$products_query = pjProductModel::factory()
			->select("id,model")
			->where('company_id', $company_id);

		if (!empty($model_list)) {
			$products_query->whereIn('t1.model', array_unique($model_list));
		}

		$products = $products_query->findAll()->getData();

		//$this->writeLog("TOTAL PRODUCTS IN SYSTEM: " . count($products));

		foreach ($products as $product) {

			$product_id = $product['id'];
			$model = $product['model'];

			$stocks = pjStockModel::factory()
				->where('status', 'T')
				->where('product_id', $product_id)
				->findAll()
				->getData();

			foreach ($stocks as $stock) {

				if (
					!isset($csv_variants[$model]) ||
					!in_array($stock['article_number'], $csv_variants[$model])
				) {

					//$this->writeLog("SETTING STOCK QTY 0: " . $stock['article_number']);

					pjStockModel::factory()
						->reset()
						->set('id', $stock['id'])
						->modify([
							'qty' => 0
						]);
				}
			}

			/* =========================
			CHECK IF PRODUCT STILL HAS STOCK
			========================= */

			$available_stock = pjStockModel::factory()
				->where('product_id', $product_id)
				// ->where('status', 'T')
				->where('qty >', 0)
				->findCount()
				->getData();

			if ($available_stock == 0) {

				//$this->writeLog("DEACTIVATING PRODUCT: " . $model);

				pjProductModel::factory()
					->reset()
					->set('id', $product_id)
					->modify([
						'status' => 0
					]);
			}
		}

		//$this->writeLog("PRODUCT CLEANUP FINISHED");
		/* UPDATE IMPORT STATUS */

		pjProductImportHistoryModel::factory()
			->reset()
			->set('id', $import_id)
			->modify([
				'status'         => 'finished',
				'processed_rows' => $total_rows,
				'synced_at'      => date('Y-m-d H:i:s')
			]);

		$this->clearTempImages();
		self::$importImageGalleryCache = array();
		self::$importModelImageDone = array();

		pjAppController::jsonResponse($this->importSyncProgressPayload('DONE', $offset, $next_offset, $total_rows, count($rows)));
	}
	public function pjActionDeleteImportRow()
	{
		$this->setAjax(true);

		if (!$this->isXHR()) {
			exit;
		}

		$id = $this->_get->toInt('id');

		if (!$id) {
			pjAppController::jsonResponse(array(
				'status' => 'ERR',
				'text' => 'Invalid ID'
			));
		}

		pjProductImportRowsModel::factory()
			->where('id', $id)
			->eraseAll();

		pjAppController::jsonResponse(array(
			'status' => 'OK'
		));
	}

	private function updateImportCsv($row_id)
	{
		$row = pjProductImportRowsModel::factory()
			->find($row_id)
			->getData();

		if (!$row) {
			return;
		}

		$import_id = $row['import_id'];

		$import = pjProductImportHistoryModel::factory()
			->find($import_id)
			->getData();

		if (!$import) {
			return;
		}

		$csv_path = $import['file_path'];

		$rows = pjProductImportRowsModel::factory()
			->where('import_id', $import_id)
			->findAll()
			->getData();

		if (!$rows) {
			return;
		}

		$csv_delimiter = ';';
		$handle = fopen($csv_path, 'w');

		/* HEADER */
		fputcsv($handle, array(
			'model',
			'model_name',
			'sku',
			'status',
			'brand',
			'category',
			'article_number',
			'article_name',
			'material',
			'ean',
			'safety_standard',
			'qty',
			'buying_price',
			'price',
			'size',
			'color',
			'name_en',
			'short_desc_en',
			'full_description_en',
			'image',
			'model_image'
		), $csv_delimiter);

		foreach ($rows as $r) {

			fputcsv($handle, array(
				$r['model'],
				$r['model_name'],
				$r['sku'],
				$r['status'],
				$r['brand'],
				$r['category'],
				$r['article_number'],
				$r['article_name'],
				$r['material'],
				$r['ean'],
				$r['safety_standard'],
				$r['qty'],
				$r['buying_price'],
				$r['price'],
				$r['size'],
				$r['color'],
				$r['name_en'],
				$r['short_desc_en'],
				$r['full_description_en'],
				$r['image'],
				$r['model_image']
			), $csv_delimiter);
		}

		fclose($handle);
	}

	public function pjActionSaveImportRow()
	{
		$this->setAjax(true);

		if (!$this->isXHR()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}

		if (!self::isPost()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.'));
		}

		if (!pjAuth::factory('pjAdminProducts', 'pjActionUpdate')->hasAccess()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Access denied.'));
		}

		$id = $this->_get->toInt('id');

		if (!$id) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Invalid ID.'));
		}

		$column = $this->_post->toString('column');
		$value  = $this->_post->toString('value');

		$model = pjProductImportRowsModel::factory()->find($id)->getData();

		if (!$model) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 104, 'text' => 'Row not found.'));
		}

		/* REQUIRED FIELD VALIDATION */
		$allow_zero_fields = array('qty', 'price', 'buying_price');
		$optional_import_fields = array('model_image', 'material', 'safety_standard', 'buying_price');

		if (!in_array($column, $allow_zero_fields) && !in_array($column, $optional_import_fields)) {

			if ($value === null || trim($value) === '') {

				self::jsonResponse(array(
					'status' => 'ERR',
					'code'   => 105,
					'text'   => ucfirst(str_replace('_', ' ', $column)) . ' cannot be empty.'
				));
			}
		}

		/* numeric fields */
		if ($column == 'qty') {

			if (!is_numeric($value)) {
				self::jsonResponse(array(
					'status' => 'ERR',
					'code'   => 106,
					'text'   => 'Stock must be a number.'
				));
			}

			$value = (int)$value;
		}

		if ($column == 'price' || $column == 'buying_price') {

			if ($value !== '' && $value !== null && !is_numeric($value)) {
				self::jsonResponse(array(
					'status' => 'ERR',
					'code'   => 107,
					'text'   => ucfirst(str_replace('_', ' ', $column)) . ' must be a number.'
				));
			}

			$value = $value === '' || $value === null ? null : (float)$value;
		}
		/* PRODUCT LEVEL FIELDS */
		$product_level_fields = array(
			'brand',
			'category',
			'model_name',
			'name_en',
			'short_desc_en',
			'full_description_en',
			'material',
			'safety_standard',
			'model_image',
			// 'status'
		);

		if (in_array($column, $product_level_fields)) {

			pjProductImportRowsModel::factory()
				->reset()
				->where('model', $model['model'])
				->where('import_id', $model['import_id'])
				->modifyAll(array($column => $value));
		} else {

			/* VARIANT LEVEL FIELD → UPDATE ONLY THIS ROW */

			pjProductImportRowsModel::factory()
				->reset()
				->where('id', $id)
				->limit(1)
				->modifyAll(array($column => $value));
		}
		pjProductImportRowsModel::factory()
			->reset()
			->where('id', $id)
			->limit(1)
			->modifyAll(array($column => $value));

		$this->updateImportCsv($id);

		self::jsonResponse(array(
			'status' => 'OK',
			'code'   => 201,
			'text'   => 'Row updated successfully.'
		));
		exit;
	}

	public function pjActionUploadImportFile()
	{
		$this->setAjax(true);

		if (!$this->isXHR()) {
			pjAppController::jsonResponse([
				'status' => 'ERR',
				'code' => 100,
				'text' => 'Invalid request'
			]);
			exit;
		}

		if (empty($_FILES['file']['tmp_name'])) {
			pjAppController::jsonResponse([
				'status' => 'ERR',
				'code' => 100,
				'text' => 'CSV file missing'
			]);
			exit;
		}
		$csv_delimiter = ';';
		$company_id = $_SESSION[$this->defaultCompany]['id'];
		$w_number   = $_SESSION[$this->defaultCompany]['w_number'];
		$user_id    = $this->getUserId();

		$file_name = time() . "_" . basename($_FILES['file']['name']);
		$folder = PJ_UPLOAD_PATH . "imports/";

		if (!is_dir($folder)) {
			mkdir($folder, 0777, true);
		}

		$file_path = $folder . $file_name;

		if (!move_uploaded_file($_FILES['file']['tmp_name'], $file_path)) {
			pjAppController::jsonResponse([
				'status' => 'ERR',
				'code' => 100,
				'text' => 'File upload failed'
			]);
			exit;
		}

		$file_size = filesize($file_path);

		/* =========================
	   CSV VALIDATION
		========================= */

		$allowed_headers = [
			'w_number',
			'model',
			'model_name',
			'sku',
			'status',
			'brand',
			'category',
			'article_number',
			'article_name',
			'material',
			'ean',
			'safety_standard',
			'qty',
			'buying_price',
			'price',
			'size',
			'color',
			'name_en',
			'short_desc_en',
			'full_description_en',
			'image',
			'model_image'
		];

		$optional_csv_fields = array('model_image', 'material', 'safety_standard', 'buying_price');

		$handle = fopen($file_path, "r");

		if (!$handle) {
			pjAppController::jsonResponse([
				'status' => 'ERR',
				'text' => 'Unable to read CSV file'
			]);
			exit;
		}

		// $header = fgetcsv($handle);
		$header = fgetcsv($handle, 0, $csv_delimiter);
		if (!$header) {
			pjAppController::jsonResponse([
				'status' => 'ERR',
				'text' => 'CSV header missing'
			]);
			exit;
		}

		$header = array_map('trim', $header);

		foreach ($header as $column) {

			if (!in_array($column, $allowed_headers)) {

				pjAppController::jsonResponse([
					'status' => 'ERR',
					'text' => 'Invalid CSV header detected: ' . $column
				]);
				exit;
			}
		}

		$header_index = array_flip($header);

		$sku_model_map = [];
		$article_model_map = [];

		$csv_rows = [];
		$row_number = 1;

		// while (($row = fgetcsv($handle)) !== false) {
		while (($row = fgetcsv($handle, 0, $csv_delimiter)) !== false) {
			$row_number++;

			$data = array_combine($header, $row);
			foreach ($data as $field => $value) {

				if (in_array($field, $optional_csv_fields, true)) {
					continue;
				}

				if (trim($value) === '') {
					pjAppController::jsonResponse([
						'status' => 'ERR',
						'text' => "Field '{$field}' is empty at row {$row_number}"
					]);
					exit;
				}
			}
			$sku = trim($data['sku']);
			$article_number = trim($data['article_number']);
			$original_model = trim($data['model']);

			/* BUILD SYSTEM MODEL (w_number-model) */
			$model = $w_number . '-' . $original_model;

			/* Replace model in row so later insert uses same */
			$data['model'] = $model;

			if ($sku == '' || $article_number == '' || $original_model == '') {

				pjAppController::jsonResponse([
					'status' => 'ERR',
					'text' => "Mandatory field missing at row {$row_number}"
				]);
				exit;
			}

			/* SKU VALIDATION */
			if (isset($sku_model_map[$sku]) && $sku_model_map[$sku] != $model) {

				pjAppController::jsonResponse([
					'status' => 'ERR',
					'text' => "SKU conflict at row {$row_number}"
				]);
				exit;
			}

			$sku_model_map[$sku] = $model;

			/* ARTICLE VALIDATION */
			if (isset($article_model_map[$article_number]) && $article_model_map[$article_number] != $model) {

				pjAppController::jsonResponse([
					'status' => 'ERR',
					'text' => "Article number conflict at row {$row_number}"
				]);
				exit;
			}

			$article_model_map[$article_number] = $model;

			$csv_rows[] = $data;
		}

		fclose($handle);

		$total_rows = count($csv_rows);

		/* =========================
	   SAVE IMPORT HISTORY
		========================= */

		$display_name = $this->_post->check('display_name') ? $this->_post->toString('display_name') : '';

		$history = pjProductImportHistoryModel::factory();

		$history->set('company_id', $company_id);
		$history->set('file_name', $file_name);
		$history->set('file_path', $file_path);
		$history->set('file_size', $file_size);
		$history->set('total_rows', $total_rows);
		$history->set('display_name', $display_name);
		$history->set('uploaded_by', $user_id);
		$history->set('uploaded_at', date('Y-m-d H:i:s'));
		$history->set('status', 'uploaded');

		$insert_id = $history->insert()->getInsertId();

		if (!$insert_id) {

			pjAppController::jsonResponse([
				'status' => 'ERR',
				'text' => 'Database insert failed'
			]);
			exit;
		}

		/* =========================
	   SAVE FLATFILE ROWS
		========================= */

		foreach ($csv_rows as $row) {

			pjProductImportRowsModel::factory()->setAttributes([

				'import_id' => $insert_id,
				'company_id' => $company_id,

				'w_number' => isset($w_number) ? $w_number : null,

				'model' => $row['model'], // already w_number-model

				'model_name' => $row['model_name'],

				'sku' => $row['sku'],
				'status' => $row['status'],

				'brand' => $row['brand'],
				'category' => $row['category'],

				'article_number' => $row['article_number'],
				'article_name' => $row['article_name'],

				'ean' => $row['ean'],

				'qty' => $row['qty'],
				'price' => $row['price'],

				'size' => $row['size'],
				'color' => $row['color'],

				'name_en' => $row['name_en'],

				'short_desc_en' => $row['short_desc_en'],
				'full_description_en' => $row['full_description_en'],

				'image' => $row['image'],
				'model_image' => isset($row['model_image']) ? $row['model_image'] : null,
				'material' => isset($row['material']) ? $row['material'] : null,
				'safety_standard' => isset($row['safety_standard']) ? $row['safety_standard'] : null,
				'buying_price' => isset($row['buying_price']) && $row['buying_price'] !== '' ? $row['buying_price'] : null,

				'row_status' => 'active',
				'sync_status' => 'pending',

				'created_at' => date('Y-m-d H:i:s')

			])->insert();
		}

		pjAppController::jsonResponse([
			'status' => 'OK',
			'code' => 200,
			'text' => 'File uploaded and flatfile created',
			'insert_id' => $insert_id
		]);
	}
	public function pjActionDownloadSampleCsv()
	{
		$this->setAjax(false);

		$this->checkLogin();

		$sample_file = PJ_UPLOAD_PATH . "samples/product_import_sample.csv";

		if (!file_exists($sample_file)) {

			header("HTTP/1.0 404 Not Found");
			echo "Sample file not found";
			exit;
		}

		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename="product_import_sample.csv"');
		header('Content-Length: ' . filesize($sample_file));

		readfile($sample_file);
		exit;
	}
	public function pjActionFlatfile()
	{
		$this->checkLogin();

		$import_id = $this->_get->toInt('id');

		$rows = pjProductImportRowsModel::factory()
			->where('import_id', $import_id)
			->findAll()
			->getData();
		// echo "<pre>";
		// print_r($rows);
		// die;
		$this->set('rows', $rows);
	}
	public function pjActionDownloadFile()
	{
		$this->checkLogin();

		$id = (int)$this->_get->toInt('id');

		//$this->writeLog("DOWNLOAD FILE ID: " . $id);

		$file = pjProductImportHistoryModel::factory()
			->find($id)
			->getData();

		if (!$file) {
			die("File not found");
		}

		$file_path = $file['file_path'];

		if (!file_exists($file_path)) {
			die("File missing on server");
		}

		//$this->writeLog("Downloading: " . $file_path);

		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename="' . $file['file_name'] . '"');
		header('Content-Length: ' . filesize($file_path));

		readfile($file_path);
		exit;
	}

	public function pjActionDeleteImportFile()
	{
		$this->setAjax(true);

		if (!$this->isXHR()) {
			pjAppController::jsonResponse(array(
				'status' => 'ERR',
				'text' => 'Invalid request'
			));
		}
		$id = $this->_get->toInt('id');


		//$this->writeLog("DELETE IMPORT FILE: " . $id);

		$file = pjProductImportHistoryModel::factory()
			->find($id)
			->getData();

		if (!$file) {

			pjAppController::jsonResponse(array(
				'status' => 'ERR',
				'text' => 'File not found'
			));
		}

		$file_path = $file['file_path'];

		if (file_exists($file_path)) {

			unlink($file_path);

			//$this->writeLog("FILE REMOVED: " . $file_path);
		}

		pjProductImportHistoryModel::factory()
			->set('id', $id)
			->erase();
		pjProductImportRowsModel::factory()
			->whereIn('import_id', $id)
			->eraseAll();
		pjAppController::jsonResponse(array(
			'status' => 'OK',
			'text' => 'File deleted'
		));
	}

	public function pjActionStartImport()
	{
		$this->setAjax(true);

		if (!$this->isXHR()) {
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Invalid request'));
		}

		$history_id = $this->_post->check('history_id') ? (int) $this->_post->toInt('history_id')  : 0;

		$file = pjProductImportHistoryModel::factory()
			->find($history_id)
			->getData();

		if (empty($file)) {
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'File not found'));
		}

		$_SESSION['import_file_path'] = $file['file_path'];
		$_SESSION['import_history_id'] = $history_id;

		pjProductImportHistoryModel::factory()
			->set('id', $history_id)
			->modify(array(
				'status' => 'processing',
				'synced_by' => $this->getUserId(),
				'synced_at' => date('Y-m-d H:i:s')
			));

		pjAppController::jsonResponse(array(
			'status' => 'OK',
			'code' => 200,
			'text' => 'Import started'
		));
	}

	public function pjActionGetHistory()
	{
		$this->setAjax(true);

		if (!$this->isXHR()) {
			exit;
		}

		$model = pjProductImportHistoryModel::factory();

		$column = 'id';
		$direction = 'DESC';

		if ($this->_get->check('column') && $this->_get->check('direction')) {
			$column = $this->_get->toString('column');
			$direction = $this->_get->toString('direction');
		}
		$company_id = $_SESSION[$this->defaultCompany]['id'];
		if (isset($company_id) && $company_id > 0) {
			$model->where('t1.company_id', $company_id);
		}
		$total = $model->findCount()->getData();

		$rowCount = $this->_get->toInt('rowCount');
		$page = $this->_get->toInt('page');

		if ($rowCount <= 0) {
			$rowCount = 10;
		}

		if ($page <= 0) {
			$page = 1;
		}

		$offset = ($page - 1) * $rowCount;

		$data = $model
			->orderBy("$column $direction")
			->limit($rowCount, $offset)
			->findAll()
			->getData();
		$data = $model
			->orderBy("$column $direction")
			->limit($rowCount, $offset)
			->findAll()
			->getData();

		/* MARK LATEST FILE */

		if (!empty($data)) {
			$latest_id = $data[0]['id'];

			foreach ($data as $k => $row) {
				$data[$k]['is_latest'] = ($row['id'] == $latest_id) ? 1 : 0;
			}
		}
		pjAppController::jsonResponse(array(
			'data' => $data,
			'total' => $total,
			'page' => $page,
			'rowCount' => $rowCount
		));
	}

	public function pjActionImportAllProduct()
	{
		$this->setAjax(true);

		//$this->writeLog("=== IMPORT START ===New");

		if (!$this->isXHR()) {
			//$this->writeLog("ERROR: Not AJAX");

			pjAppController::jsonResponse(array(
				'status' => 'ERR',
				'text'   => 'Invalid request'
			));
		}

		$this->checkLogin();

		@set_time_limit(300);
		@ini_set('memory_limit', '512M');
		if (function_exists('session_write_close')) {
			session_write_close();
		}

		$limit  = $this->getImportSyncBatchLimit();
		$offset = $this->_post->check('offset') ? (int)$this->_post->toInt('offset') : 0;
		$file_id = $this->_post->check('file_id') ? (int)$this->_post->toInt('file_id') : 0;

		//$this->writeLog("OFFSET: " . $offset);
		//$this->writeLog("FILE ID: " . $file_id);

		if (!$file_id) {
			pjAppController::jsonResponse(array(
				'status' => 'ERR',
				'text'   => 'Missing file id'
			));
		}

		$file = pjProductImportHistoryModel::factory()
			->find($file_id)
			->getData();

		if (!$file) {
			pjAppController::jsonResponse(array(
				'status' => 'ERR',
				'text'   => 'File not found'
			));
		}

		$csv_path = $file['file_path'];

		//$this->writeLog("CSV PATH: " . $csv_path);

		if (!file_exists($csv_path)) {

			//$this->writeLog("ERROR: CSV file missing");

			pjProductImportHistoryModel::factory()
				->reset()
				->set('id', $file_id)
				->modify(array(
					'status' => 'failed',
					'error_message' => 'CSV file missing'
				));

			pjAppController::jsonResponse(array(
				'status' => 'ERR',
				'text'   => 'CSV file missing'
			));
		}

		/* =========================
       START SYNC (FIRST CALL)
    		========================= */

		if ($offset == 0) {
			self::$importImageGalleryCache = array();
			$this->clearImportCsvSession($file_id);

			//$this->writeLog("SYNC STARTED");

			pjProductImportHistoryModel::factory()
				->reset()
				->set('id', $file_id)
				->modify(array(
					'status'         => 'processing',
					'synced_by'      => $this->getUserId(),
					'synced_at'      => date('Y-m-d H:i:s'),
					'sync_count'     => (int)$file['sync_count'] + 1,
					'processed_rows' => 0,
					'failed_rows'    => 0
				));
		}

		$csv_bundle = $this->loadImportCsvData($file_id, $csv_path);
		if ($csv_bundle === null) {
			pjAppController::jsonResponse(array(
				'status' => 'ERR',
				'text'   => 'Unable to open CSV file'
			));
		}

		$csv_data = $csv_bundle['rows'];
		$csv_models = $csv_bundle['models'];
		$total_rows = count($csv_data);
		$rows = array_slice($csv_data, $offset, $limit);

		$company_id = $_SESSION[$this->defaultCompany]['id'];
		$w_number = $_SESSION[$this->defaultCompany]['w_number'];

		foreach ($rows as $data) {

			try {
				$status = $data['status'] == 1 ? 'T' : 'F';
				$model = $data['model'];
				$model = $w_number . '-' . $data['model'];
				//$this->writeLog("IMPORT MODEL: " . $model);

				$product_id = $this->getOrCreateProduct(
					$data['model'],
					$data['model_name'],
					$data['sku'],
					$data['status'],
					$company_id
				);

				$this->assignBrand($product_id, $data['brand'], $company_id);

				$this->assignCategory($product_id, $data['category'], $company_id);

				$this->updateProductLang(
					$product_id,
					$data['name_en'],
					$data['short_desc_en'],
					$data['full_description_en']
				);

				$this->updateProductMeta($product_id, $data);

				$img_id = $this->importGalleryImages($product_id, $data);

				$buying_price = isset($data['buying_price']) && $data['buying_price'] !== '' ? $data['buying_price'] : null;

				$this->addStockViaUpdate(
					$product_id,
					$data['size'],
					$data['color'],
					$data['article_number'],
					$data['article_name'],
					$data['ean'],
					$data['qty'],
					$data['price'],
					$buying_price,
					$img_id,
					$status
				);
			} catch (Exception $e) {

				//$this->writeLog("ROW FAILED: " . $e->getMessage());

				pjProductImportHistoryModel::factory()
					->reset()
					->set('id', $file_id)
					->modify(array(
						'failed_rows' => (int)$file['failed_rows'] + 1
					));
			}
		}

		/* =========================
       UPDATE PROGRESS
    		========================= */

		$next_offset = $offset + $limit;

		if ($next_offset > $total_rows) {
			$next_offset = $total_rows;
		}

		//$this->writeLog("NEXT OFFSET: " . $next_offset);

		pjProductImportHistoryModel::factory()
			->reset()
			->set('id', $file_id)
			->modify(array(
				'processed_rows' => $next_offset
			));

		/* =========================
       CONTINUE NEXT BATCH
    		========================= */

		if ($next_offset < $total_rows) {

			pjAppController::jsonResponse($this->importSyncProgressPayload('OK', $offset, $next_offset, $total_rows, count($rows)));
		}

		/* =========================
		FINISHED
			========================= */

		//$this->writeLog("IMPORT FINISHED");

		/* =========================
		DEACTIVATE OLD PRODUCTS
			========================= */

		//$this->writeLog("START PRODUCT CLEANUP");

		/*
			If product exists in DB but not in CSV
			it will be set inactive
			*/

		$products = pjProductModel::factory()
			->select("id, model")
			->where('company_id', $company_id)
			->findAll()
			->getData();

		//$this->writeLog("TOTAL PRODUCTS IN SYSTEM: " . count($products));

		foreach ($products as $product) {

			if (!in_array($product['model'], $csv_models)) {

				//$this->writeLog("DEACTIVATING PRODUCT: " . $product['model']);

				pjProductModel::factory()
					->reset()
					->set('id', $product['id'])
					->modify(array(
						'status' => 0
					));
			}
		}

		//$this->writeLog("PRODUCT CLEANUP FINISHED");

		/* =========================
		UPDATE IMPORT STATUS
			========================= */

		pjProductImportHistoryModel::factory()
			->reset()
			->set('id', $file_id)
			->modify(array(
				'status'         => 'finished',
				'processed_rows' => $total_rows,
				'synced_at'      => date('Y-m-d H:i:s')
			));
		/* =========================
		CLEANUP TEMP IMAGES
			========================= */
		$this->clearTempImages();
		$this->clearImportCsvSession($file_id);
		self::$importImageGalleryCache = array();

		pjAppController::jsonResponse($this->importSyncProgressPayload('DONE', $offset, $next_offset, $total_rows, count($rows)));
	}
	private function clearTempImages()
	{
		$tmp_folder = PJ_INSTALL_PATH . 'app/web/upload/csv-images/';

		if (!is_dir($tmp_folder)) {
			return;
		}

		$files = glob($tmp_folder . '*');

		foreach ($files as $file) {
			if (is_file($file)) {
				unlink($file);
			}
		}

		rmdir($tmp_folder);
	}

	private function updateProductMeta($product_id, $data)
	{
		$update = array();

		if (isset($data['material'])) {
			$update['material'] = $data['material'];
		}
		if (isset($data['safety_standard'])) {
			$update['safety_standard'] = $data['safety_standard'];
		}

		if (!empty($update)) {
			pjProductModel::factory()
				->reset()
				->where('id', $product_id)
				->limit(1)
				->modifyAll($update);
		}

		if (!empty($data['model_image'])) {
			$this->importModelImage($product_id, $data['model_image']);
		}
	}

	private function importModelImage($product_id, $url)
	{
		$url = trim($url);
		if ($url === '') {
			return;
		}

		$product_id = (int) $product_id;
		if (isset(self::$importModelImageDone[$product_id])) {
			return;
		}

		$product = pjProductModel::factory()->find($product_id)->getData();
		if (empty($product)) {
			return;
		}

		/* One model image per product — skip if a dedicated model image already exists */
		if (!empty($product['model_image_id'])) {
			$existing = pjGalleryModel::factory()->find($product['model_image_id'])->getData();
			if (!empty($existing) && $existing['model'] === pjAppController::GALLERY_MODEL_MODEL_IMAGE) {
				self::$importModelImageDone[$product_id] = true;
				return;
			}
		}

		self::$importModelImageDone[$product_id] = true;

		$GalleryModel = pjGalleryModel::factory();
		$Image = new pjImage();

		$tmp_file = $this->downloadImage($url, 'csv-model-images');
		if (!$tmp_file) {
			return;
		}

		$full_path = PJ_INSTALL_PATH . $tmp_file;
		if (!$Image->loadImage($full_path)) {
			return;
		}

		$hash = md5(uniqid(rand(), true));
		$source_path = PJ_UPLOAD_PATH . 'source/' . $product_id . '_model_' . $hash . '.' . $Image->getExtension();

		if (!$Image->saveImage(PJ_INSTALL_PATH . $source_path)) {
			return;
		}

		$image_info = getimagesize(PJ_INSTALL_PATH . $source_path);
		$data_insert = array(
			'foreign_id'   => $product_id,
			'model'        => pjAppController::GALLERY_MODEL_MODEL_IMAGE,
			'mime_type'    => $image_info['mime'],
			'source_path'  => $source_path,
			'source_size'  => filesize(PJ_INSTALL_PATH . $source_path),
			'source_width' => $image_info[0],
			'source_height' => $image_info[1],
			'name'         => basename($url),
			'sort'         => 0,
			'created'      => date('Y-m-d H:i:s')
		);

		$data_insert = array_merge(
			$data_insert,
			$this->pjActionBuildFromSource($Image, $data_insert)
		);

		$insert_id = $GalleryModel
			->reset()
			->setAttributes($data_insert)
			->insert()
			->getInsertId();

		if ($insert_id) {
			pjProductModel::factory()
				->reset()
				->where('id', $product_id)
				->limit(1)
				->modifyAll(array('model_image_id' => $insert_id));
		}
	}

	private function addStockViaUpdate($product_id, $size, $color, $article_number, $article_name, $ean, $qty, $price, $buying_price, $img_id, $status)
	{
		$company_id = $_SESSION[$this->defaultCompany]['id'];

		$article_number = trim($article_number);
		// $this->writeLog("STATUS" . $status);

		// $status = $status == 1 ? 'T' : 'F';

		// $this->writeLog("ADDING STOCK FOR PRODUCT " . $product_id . " ARTICLE " . $article_number. " STATUS" . $status);

		$size_attr  = $this->getOrCreateAttribute($product_id, 'Size', $size);
		$color_attr = $this->getOrCreateAttribute($product_id, 'Color', $color);

		if (!$size_attr || !$color_attr) {
			//$this->writeLog("ATTRIBUTE ERROR");
			return;
		}

		// find existing stock
		$existing = pjStockModel::factory()
			->where('product_id', $product_id)
			->where('status', 'T')
			->where('TRIM(article_number)', $article_number)
			->limit(1)
			->findAll()
			->getData();

		if (!empty($existing)) {

			$stock_id = $existing[0]['id'];

			//$this->writeLog("STOCK EXISTS → UPDATE ID " . $stock_id);

			pjStockModel::factory()
				->reset()
				->set('id', $stock_id)
				->modify([
					'article_name' => $article_name,
					'ean' => $ean,
					'qty' => $qty,
					'price' => $price,
					'buying_price' => $buying_price !== null && $buying_price !== '' ? $buying_price : ':NULL',
					'image_id' => $img_id,
					'status' => $status
				]);

			// remove old attributes
			pjStockAttributeModel::factory()
				->where('stock_id', $stock_id)
				->eraseAll();
			// reactivate product if it was disabled
			pjProductModel::factory()
				->reset()
				->set('id', $product_id)
				->modify(['status' => 1]);
		} else {

			//$this->writeLog("CREATE NEW STOCK");

			$stock_id = pjStockModel::factory()
				->set('company_id', $company_id)
				->set('product_id', $product_id)
				->set('status', $status)
				->set('image_id', $img_id)
				->set('article_name', $article_name)
				->set('article_number', $article_number)
				->set('ean', $ean)
				->set('qty', $qty)
				->set('price', $price)
				->set('buying_price', $buying_price !== null && $buying_price !== '' ? $buying_price : null)
				->insert()
				->getInsertId();

			//$this->writeLog("STOCK CREATED ID " . $stock_id);
		}

		if (!$stock_id) {
			//$this->writeLog("STOCK ERROR");
			return;
		}

		// link size attribute
		pjStockAttributeModel::factory()
			->set('stock_id', $stock_id)
			->set('product_id', $product_id)
			->set('company_id', $company_id)
			->set('attribute_parent_id', $size_attr['parent_id'])
			->set('attribute_id', $size_attr['id'])
			->insert();

		// link color attribute
		pjStockAttributeModel::factory()
			->set('stock_id', $stock_id)
			->set('product_id', $product_id)
			->set('company_id', $company_id)
			->set('attribute_parent_id', $color_attr['parent_id'])
			->set('attribute_id', $color_attr['id'])
			->insert();

		//$this->writeLog("STOCK ATTRIBUTES LINKED");
	}

	private function downloadImage($url, $type = 'csv-images')
	{
		if (empty($url)) {
			return false;
		}

		$timeout = 12;
		$context = stream_context_create(array(
			'http' => array(
				'timeout' => $timeout,
				'ignore_errors' => true,
				'user_agent' => 'WorkwearNetwork-ProductImport/1.0',
			),
			'ssl' => array(
				'verify_peer' => true,
				'verify_peer_name' => true,
			),
		));

		$imageData = @file_get_contents($url, false, $context);

		if ($imageData === false || $imageData === '') {
			//$this->writeLog("IMAGE DOWNLOAD FAILED");
			return false;
		}

		// detect mime type
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime = finfo_buffer($finfo, $imageData);
		finfo_close($finfo);

		$ext = 'jpg';

		if ($mime == 'image/png') $ext = 'png';
		if ($mime == 'image/gif') $ext = 'gif';
		if ($mime == 'image/webp') $ext = 'webp';

		$filename = uniqid() . '.' . $ext;

		$folder = PJ_INSTALL_PATH . 'app/web/upload/' . $type . '/';

		if (!is_dir($folder)) {
			mkdir($folder, 0777, true);
		}

		$localPath = $folder . $filename;

		file_put_contents($localPath, $imageData);

		//$this->writeLog("IMAGE SAVED:------ " . $localPath);

		return str_replace(PJ_INSTALL_PATH, '', $localPath);
	}

	/**
	 * Reuse an existing gallery row for this product + image file name (across sync batches).
	 */
	private function findExistingGalleryImageId($product_id, $image_url)
	{
		$image_url = trim($image_url);
		if ($image_url === '') {
			return null;
		}

		$name = basename(parse_url($image_url, PHP_URL_PATH) ?: $image_url);
		if ($name === '') {
			return null;
		}

		$row = pjGalleryModel::factory()
			->reset()
			->select('t1.id')
			->where('t1.foreign_id', (int) $product_id)
			->where('t1.model', pjAppController::GALLERY_MODEL_PRODUCT)
			->where('t1.name', $name)
			->limit(1)
			->findAll()
			->getData();

		return !empty($row[0]['id']) ? (int) $row[0]['id'] : null;
	}

	private function importGalleryImages($product_id, $data)
	{

		if (empty($data['image'])) {
			//$this->writeLog("NO IMAGE FIELD FOUND----" . $product_id);
			return null;
		}

		$product_id = (int) $product_id;
		$images = explode(',', $data['image']);
		$first_url = trim($images[0]);
		if ($first_url !== '') {
			$cache_key = $product_id . '|' . $first_url;
			if (isset(self::$importImageGalleryCache[$cache_key])) {
				return self::$importImageGalleryCache[$cache_key];
			}

			$existing_id = $this->findExistingGalleryImageId($product_id, $first_url);
			if ($existing_id) {
				self::$importImageGalleryCache[$cache_key] = $existing_id;
				return $existing_id;
			}
		}

		$GalleryModel = pjGalleryModel::factory();
		$Image = new pjImage();

		foreach ($images as $img) {

			$img = trim($img);
			if (!$img) {
				//$this->writeLog("EMPTY IMAGE URL SKIPPED");
				continue;
			}

			//$this->writeLog("DOWNLOAD IMAGE: " . $img);

			$tmp_file = $this->downloadImage($img, 'csv-images');

			if (!$tmp_file) {
				//$this->writeLog("DOWNLOAD FAILED");
				continue;
			}

			//$this->writeLog("TEMP FILE: " . $tmp_file);

			$full_path = PJ_INSTALL_PATH . $tmp_file;

			//$this->writeLog("FULL PATH: " . $full_path);

			if (!$Image->loadImage($full_path)) {
				//$this->writeLog("IMAGE LOAD FAILED");
				continue;
			}

			//$this->writeLog("IMAGE LOADED SUCCESS");

			$hash = md5(uniqid(rand(), true));

			$source_path = PJ_UPLOAD_PATH . 'source/' . $product_id . '_' . $hash . '.' . $Image->getExtension();

			//$this->writeLog("SOURCE PATH: " . $source_path);

			if (!$Image->saveImage(PJ_INSTALL_PATH . $source_path)) {
				//$this->writeLog("SOURCE SAVE FAILED");
				continue;
			}

			//$this->writeLog("SOURCE IMAGE SAVED");

			$image_info = getimagesize(PJ_INSTALL_PATH . $source_path);

			$data_insert = [
				'foreign_id'   => $product_id,
				'model'        => 'pjProduct',
				'mime_type'    => $image_info['mime'],
				'source_path'  => $source_path,
				'source_size'  => filesize(PJ_INSTALL_PATH . $source_path),
				'source_width' => $image_info[0],
				'source_height' => $image_info[1],
				'name'         => basename($img),
				'sort'         => 1,
				'created'      => date('Y-m-d H:i:s')
			];

			//$this->writeLog("BUILD THUMBNAILS");

			$data_insert = array_merge(
				$data_insert,
				$this->pjActionBuildFromSource($Image, $data_insert)
			);

			//$this->writeLog("THUMBNAIL DATA BEFORE INSERT:");
			//$this->writeLog($data_insert);

			$insert_id = $GalleryModel
				->reset()
				->setAttributes($data_insert)
				->insert()
				->getInsertId();

			//$this->writeLog("GALLERY INSERT ID: " . $insert_id);

			if ($insert_id && $first_url !== '') {
				self::$importImageGalleryCache[$product_id . '|' . $first_url] = $insert_id;
			}

			return $insert_id;
		}

		//$this->writeLog("NO IMAGE INSERTED");

		return null;
	}

	private function pjActionBuildFromSource($Image, $data)
	{
		$arr = array();

		$source_full = PJ_INSTALL_PATH . $data['source_path'];

		$small_path  = 'app/web/upload/small/' . basename($data['source_path']);
		$medium_path = 'app/web/upload/medium/' . basename($data['source_path']);
		$large_path  = 'app/web/upload/large/' . basename($data['source_path']);

		$small_full  = PJ_INSTALL_PATH . $small_path;
		$medium_full = PJ_INSTALL_PATH . $medium_path;
		$large_full  = PJ_INSTALL_PATH . $large_path;

		//$this->writeLog("START BUILD THUMBNAILS");

		// SMALL
		$Image->loadImage($source_full);

		if ($Image->resize(80, 106)) {

			if ($Image->saveImage($small_full)) {

				$size = getimagesize($small_full);

				$arr['small_path']   = $small_path;
				$arr['small_size']   = filesize($small_full);
				$arr['small_width']  = $size[0];
				$arr['small_height'] = $size[1];

				//$this->writeLog("SMALL CREATED SIZE: ".$arr['small_size']);
			}
		}

		// MEDIUM
		$Image->loadImage($source_full);

		if ($Image->resize(300, 400)) {

			if ($Image->saveImage($medium_full)) {

				$size = getimagesize($medium_full);

				$arr['medium_path']   = $medium_path;
				$arr['medium_size']   = filesize($medium_full);
				$arr['medium_width']  = $size[0];
				$arr['medium_height'] = $size[1];

				//$this->writeLog("MEDIUM CREATED SIZE: ".$arr['medium_size']);
			}
		}

		// LARGE
		$Image->loadImage($source_full);

		if ($Image->resize(320, 240)) {

			if ($Image->saveImage($large_full)) {

				$size = getimagesize($large_full);

				$arr['large_path']   = $large_path;
				$arr['large_size']   = filesize($large_full);
				$arr['large_width']  = $size[0];
				$arr['large_height'] = $size[1];

				//$this->writeLog("LARGE CREATED SIZE: ".$arr['large_size']);
			}
		}

		//$this->writeLog("THUMBNAIL DATA GENERATED:");
		//$this->writeLog($arr);

		return $arr;
	}

	private function getOrCreateProduct($model, $model_name, $sku, $status, $company_id)
	{
		$product = pjProductModel::factory()
			->where('model', $model)
			->limit(1)
			->findAll()
			->getData();

		if (!empty($product)) {
			return $product[0]['id'];
		}

		$data = [
			'model' => $model,
			'model_name' => $model_name,
			'sku' => $sku,
			'status' => 1,
			'company_id' => $company_id,
			'is_featured' => 0,
			'is_digital' => 0,
			'created_by' => $this->getUserId()
		];

		// $this->writeLog("INSERT PRODUCT");
		// $this->writeLog($data);

		$product_id = pjProductModel::factory()
			->setAttributes($data)
			->insert()
			->getInsertId();

		// $this->writeLog("PRODUCT INSERT RESULT: " . $product_id);

		return $product_id;
	}
	private function assignBrand($product_id, $brand, $company_id)
	{
		if (empty($brand)) {
			return;
		}

		$brand = trim($brand);

		/* CHECK IF PRODUCT ALREADY HAS A BRAND */
		$existing_brand = pjProductBrandModel::factory()
			->select('t1.brand_id')
			->where('t1.product_id', $product_id)
			->limit(1)
			->findAll()
			->getData();
		// $this->writeLog("PRODUCT : " . $product_id);
		// $this->writeLog($existing_brand);

		if (!empty($existing_brand)) {

			$brand_id = $existing_brand[0]['brand_id'];

			/* UPDATE BRAND NAME */
			pjMultiLangModel::factory()
				->where('model', 'pjBrand')
				->where('foreign_id', $brand_id)
				->where('field', 'name')
				->modifyAll([
					'content' => $brand
				]);

			return;
		}

		/* FIND BRAND BY NAME */
		$brand_data = pjBrandModel::factory()
			->select('t1.id')
			->join('pjMultiLang', 't2.foreign_id=t1.id AND t2.model="pjBrand"', 'inner')
			->where('t2.field', 'name')
			->where('LOWER(t2.content)', strtolower($brand))
			->where('t1.company_id', $company_id)
			->limit(1)
			->findAll()
			->getData();

		/* CREATE BRAND IF NOT FOUND */
		if (empty($brand_data)) {

			$parent_id = 1;

			$brand_id = pjBrandModel::factory()->saveNode([
				'company_id' => $company_id,
				'parent_id' => $parent_id
			], $parent_id);

			pjMultiLangModel::factory()->saveMultiLang([
				1 => ['name' => $brand]
			], $brand_id, 'pjBrand', 'data');
		} else {

			$brand_id = $brand_data[0]['id'];
		}

		/* ASSIGN BRAND TO PRODUCT */
		pjProductBrandModel::factory()
			->where('product_id', $product_id)
			->eraseAll();

		pjProductBrandModel::factory()
			->set('product_id', $product_id)
			->set('brand_id', $brand_id)
			->set('company_id', $company_id)
			->insert();
	}

	private function assignCategory($product_id, $category_name, $company_id)
	{
		if (empty($category_name)) {
			return;
		}

		$category_name = trim($category_name);

		/* CHECK IF PRODUCT ALREADY HAS CATEGORY */
		$existing_category = pjProductCategoryModel::factory()
			->select('t1.category_id')
			->where('t1.product_id', $product_id)
			->limit(1)
			->findAll()
			->getData();

		if (!empty($existing_category)) {

			$category_id = $existing_category[0]['category_id'];

			/* UPDATE CATEGORY NAME */
			pjMultiLangModel::factory()
				->where('model', 'pjCategory')
				->where('foreign_id', $category_id)
				->where('field', 'name')
				->modifyAll([
					'content' => $category_name
				]);

			return;
		}

		/* FIND CATEGORY BY NAME */
		$category = pjCategoryModel::factory()
			->join('pjMultiLang', 't2.foreign_id=t1.id AND t2.model="pjCategory"', 'inner')
			->where('t2.field', 'name')
			->where('LOWER(t2.content)', strtolower($category_name))
			->where('t1.company_id', $company_id)
			->limit(1)
			->findAll()
			->getData();

		if (empty($category)) {

			$parent_id = 1;

			$category_id = pjCategoryModel::factory()->saveNode([
				'company_id' => $company_id,
				'parent_id' => $parent_id
			], $parent_id);

			pjMultiLangModel::factory()->saveMultiLang([
				1 => ['name' => $category_name]
			], $category_id, 'pjCategory', 'data');
		} else {

			$category_id = $category[0]['id'];
		}

		/* ASSIGN CATEGORY TO PRODUCT */
		pjProductCategoryModel::factory()
			->where('product_id', $product_id)
			->eraseAll();

		pjProductCategoryModel::factory()
			->set('product_id', $product_id)
			->set('category_id', $category_id)
			->set('company_id', $company_id)
			->insert();
	}
	// private function assignBrand($product_id, $brand, $company_id)
	// {
	// 	if (empty($brand)) {
	// 		// //$this->writeLog("BRAND EMPTY - SKIPPED");
	// 		return;
	// 	}

	// 	$brand = trim($brand);

	// 	// //$this->writeLog("CHECK BRAND: " . $brand);

	// 	$brand_data = pjBrandModel::factory()
	// 		->join('pjMultiLang', 't2.foreign_id=t1.id AND t2.model="pjBrand"', 'inner')
	// 		->where('t2.field', 'name')
	// 		->where('t2.content', $brand)
	// 		->where('t1.company_id', $company_id)

	// 		->limit(1)
	// 		->findAll()
	// 		->getData();


	// 	// BRAND NOT FOUND → CREATE
	// 	if (empty($brand_data)) {

	// 		// //$this->writeLog("BRAND NOT FOUND → CREATING: " . $brand);

	// 		$parent_id = 1;

	// 		$data = [
	// 			'company_id' => $company_id,
	// 			'parent_id' => 1
	// 		];

	// 		$brand_id = pjBrandModel::factory()->saveNode($data, $parent_id);

	// 		if ($brand_id) {

	// 			pjMultiLangModel::factory()->saveMultiLang([
	// 				1 => ['name' => $brand]
	// 			], $brand_id, 'pjBrand', 'data');

	// 			// //$this->writeLog("BRAND CREATED ID: " . $brand_id . " NAME: " . $brand);
	// 		} else {

	// 			// //$this->writeLog("BRAND CREATE FAILED: " . $brand);
	// 			return;
	// 		}
	// 	} else {

	// 		$brand_id = $brand_data[0]['id'];

	// 		// //$this->writeLog("BRAND FOUND ID: " . $brand_id . " NAME: " . $brand);
	// 	}

	// 	// REMOVE OLD BRAND
	// 	pjProductBrandModel::factory()
	// 		->where('product_id', $product_id)
	// 		->eraseAll();

	// 	// ASSIGN BRAND
	// 	pjProductBrandModel::factory()
	// 		->set('product_id', $product_id)
	// 		->set('brand_id', $brand_id)
	// 		->set('company_id', $company_id)
	// 		->insert();

	// 	// //$this->writeLog("BRAND ASSIGNED → PRODUCT: " . $product_id . " BRAND: " . $brand);
	// }

	// private function assignCategory($product_id, $category_name, $company_id)
	// {
	// 	if (empty($category_name)) {
	// 		// //$this->writeLog("CATEGORY EMPTY - SKIPPED");
	// 		return;
	// 	}

	// 	$category_name = trim($category_name);
	// 	/* CHECK IF PRODUCT ALREADY HAS A category */
	// 	$existing_category = pjProductCategoryModel::factory()
	// 		->select('t1.category_id')
	// 		->where('t1.product_id', $product_id)
	// 		->limit(1)
	// 		->findAll()
	// 		->getData();
	// 	// $this->writeLog("PRODUCT : " . $product_id);
	// 	// $this->writeLog($existing_category);

	// 	if (!empty($existing_category)) {

	// 		$category_id = $existing_category[0]['category_id'];

	// 		/* UPDATE category NAME */
	// 		pjMultiLangModel::factory()
	// 			->where('model', 'pjCategory')
	// 			->where('foreign_id', $category_id)
	// 			// ->where('field', 'name')
	// 			->modifyAll([
	// 				'content' => $category_name
	// 			]);

	// 		return;
	// 	}

	// 	// //$this->writeLog("CHECK CATEGORY: " . $category_name);


	// 	$category = pjCategoryModel::factory()
	// 		->join('pjMultiLang', 't2.foreign_id=t1.id AND t2.model="pjCategory"', 'inner')
	// 		->where('t2.field', 'name')
	// 		->where('t2.content', $category_name)
	// 		->limit(1)
	// 		->findAll()
	// 		->getData();
	// 	// CATEGORY NOT FOUND → CREATE
	// 	if (empty($category)) {

	// 		// //$this->writeLog("CATEGORY NOT FOUND → CREATING: " . $category_name);

	// 		$parent_id = 1;

	// 		$data = [
	// 			'company_id' => $company_id,
	// 			'parent_id' => 1
	// 		];

	// 		$category_id = pjCategoryModel::factory()->saveNode($data, $parent_id);

	// 		if ($category_id) {

	// 			pjMultiLangModel::factory()->saveMultiLang([
	// 				1 => ['name' => $category_name]
	// 			], $category_id, 'pjCategory', 'data');

	// 			// //$this->writeLog("CATEGORY CREATED ID: " . $category_id . " NAME: " . $category_name);
	// 		} else {

	// 			// //$this->writeLog("CATEGORY CREATE FAILED: " . $category_name);
	// 			return;
	// 		}
	// 	} else {

	// 		$category_id = $category[0]['id'];

	// 		// //$this->writeLog("CATEGORY FOUND ID: " . $category_id . " NAME: " . $category_name);
	// 	}

	// 	// REMOVE OLD CATEGORY
	// 	pjProductCategoryModel::factory()
	// 		->where('product_id', $product_id)
	// 		->eraseAll();

	// 	// ASSIGN CATEGORY
	// 	pjProductCategoryModel::factory()
	// 		->set('product_id', $product_id)
	// 		->set('category_id', $category_id)
	// 		->set('company_id', $company_id)
	// 		->insert();

	// 	// //$this->writeLog("CATEGORY ASSIGNED → PRODUCT: " . $product_id . " CATEGORY: " . $category_name);
	// }

	private function updateProductLang($product_id, $name, $short, $full)
	{
		//$this->writeLog("UPDATE PRODUCT LANG FOR PRODUCT " . $product_id);

		$data = [
			1 => [
				'name' => $name,
				'short_desc' => $short,
				'full_desc' => $full
			]
		];

		//$this->writeLog("LANG DATA");
		//$this->writeLog($data);

		pjMultiLangModel::factory()->updateMultiLang($data, $product_id, 'pjProduct');
	}

	private function getOrCreateAttribute($product_id, $group_name, $item_name)
	{
		$company_id = $_SESSION[$this->defaultCompany]['id'];

		//$this->writeLog("CHECK GROUP: " . $group_name);

		// find group
		$group = pjAttributeModel::factory()
			->select("t1.id")
			->join('pjMultiLang', 't2.foreign_id=t1.id AND t2.model="pjAttribute"', 'inner')
			->where('t2.field', 'name')
			->where('t2.content', $group_name)
			->where('t1.product_id', $product_id)
			->where('t1.parent_id IS NULL')
			->limit(1)
			->findAll()
			->getData();

		if (empty($group)) {
			//$this->writeLog("CREATE GROUP " . $group_name);

			$group_id = pjAttributeModel::factory()
				->setAttributes([
					'product_id' => $product_id,
					'company_id' => $company_id,
					'order_group' => 0
				])
				->insert()
				->getInsertId();

			pjMultiLangModel::factory()->saveMultiLang([
				1 => ['name' => $group_name]
			], $group_id, 'pjAttribute');
		} else {
			$group_id = $group[0]['id'];
		}

		//$this->writeLog("GROUP ID " . $group_id);

		// find item
		$item = pjAttributeModel::factory()
			->select("t1.id")
			->join('pjMultiLang', 't2.foreign_id=t1.id AND t2.model="pjAttribute"', 'inner')
			->where('t2.field', 'name')
			->where('t2.content', $item_name)
			->where('t1.parent_id', $group_id)
			->where('t1.product_id', $product_id)
			->limit(1)
			->findAll()
			->getData();

		if (empty($item)) {
			//$this->writeLog("CREATE ITEM " . $item_name);

			$item_id = pjAttributeModel::factory()
				->setAttributes([
					'product_id' => $product_id,
					'parent_id' => $group_id,
					'company_id' => $company_id,
					'order_group' => 0,
					'order_item' => 0
				])
				->insert()
				->getInsertId();

			pjMultiLangModel::factory()->saveMultiLang([
				1 => ['name' => $item_name]
			], $item_id, 'pjAttribute');
		} else {
			$item_id = $item[0]['id'];
		}

		//$this->writeLog("ITEM ID " . $item_id);

		return [
			'parent_id' => $group_id,
			'id' => $item_id
		];
	}
}
