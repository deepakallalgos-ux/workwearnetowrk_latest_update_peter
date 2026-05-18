<?php
if (!defined("ROOT_PATH")) {
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminVouchers extends pjAdmin
{
	public function pjActionCheckCode()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			$default_company_id = isset($_SESSION[$this->defaultCompany]['id'])
				? (int) $_SESSION[$this->defaultCompany]['id']
				: 1;
			if (!$this->_get->check('code') || $this->_get->toString('code') == '') {
				echo 'false';
				exit;
			}
			$pjVoucherModel = pjVoucherModel::factory()->where('t1.code', $this->_get->toString('code'))->where('t1.company_id', $default_company_id);
			if ($this->_get->check('id') && $this->_get->toInt('id') > 0) {
				$pjVoucherModel->where('t1.id !=', $this->_get->toInt('id'));
			}
			echo $pjVoucherModel->findCount()->getData() == 0 ? 'true' : 'false';
		}
		exit;
	}
	public function pjActionCheckDate()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			if (strpos($this->option_arr['o_time_format'], 'a') > -1 || strpos($this->option_arr['o_time_format'], 'A') > -1) {
				$dt_from = strtotime(pjDateTime::formatDate($this->_post->toString('p_date_from'), $this->option_arr['o_date_format']) . ' ' . $this->_post->toString('p_hour_from') . ':' . $this->_post->toString('p_minute_from') . ' ' . strtoupper($this->_post->toString('p_ampm_from')));
				$dt_to = strtotime(pjDateTime::formatDate($this->_post->toString('p_date_to'), $this->option_arr['o_date_format']) . ' ' . $this->_post->toString('p_hour_to') . ':' . $this->_post->toString('p_minute_to') . ' ' . strtoupper($this->_post->toString('p_ampm_to')));
			} else {
				$dt_from = sprintf("%s %s:%s:00", pjDateTime::formatDate($this->_post->toString('p_date_from'), $this->option_arr['o_date_format']), $this->_post->toString('p_hour_from'), $this->_post->toString('p_minute_from'));
				$dt_to = sprintf("%s %s:%s:00", pjDateTime::formatDate($this->_post->toString('p_date_to'), $this->option_arr['o_date_format']), $this->_post->toString('p_hour_to'), $this->_post->toString('p_minute_to'));
				$dt_from = strtotime($dt_from);
				$dt_to = strtotime($dt_to);
			}
			echo $dt_to > $dt_from ? 'true' : 'false';
		}
		exit;
	}
	public function pjActionCreate()
	{
		$this->checkLogin();
		if (!pjAuth::factory()->hasAccess()) {
			$this->sendForbidden();
			return;
		}

		$default_company_id = isset($_SESSION[$this->defaultCompany]['id'])
			? (int) $_SESSION[$this->defaultCompany]['id']
			: 1;
		if ($this->_post->check('voucher_create')) {
			$data = array();
			$data['company_id'] = $default_company_id;
			$data['code'] = $this->_post->toString('code');
			$data['discount'] = $this->_post->toString('discount');
			$data['type'] = $this->_post->toString('type');
			$data['valid'] = $this->_post->toString('valid');
			$data['apply'] = $this->_post->check('apply') ? 'each' : 'total';
			switch ($this->_post->toString('valid')) {
				case 'fixed':
					$data['date_from'] = pjDateTime::formatDate($this->_post->toString('f_date'), $this->option_arr['o_date_format']);
					$data['date_to'] = $data['date_from'];
					$data['time_from'] = $this->_post->toString('f_hour_from') . ":" . $this->_post->toString('f_minute_from') . ":00";
					$data['time_to'] = $this->_post->toString('f_hour_to') . ":" . $this->_post->toString('f_minute_to') . ":00";
					break;
				case 'period':
					$data['date_from'] = pjDateTime::formatDate($this->_post->toString('p_date_from'), $this->option_arr['o_date_format']);
					$data['date_to'] = pjDateTime::formatDate($this->_post->toString('p_date_to'), $this->option_arr['o_date_format']);
					$data['time_from'] = $this->_post->toString('p_hour_from') . ":" . $this->_post->toString('p_minute_from') . ":00";
					$data['time_to'] = $this->_post->toString('p_hour_to') . ":" . $this->_post->toString('p_minute_to') . ":00";
					break;
				case 'recurring':
					$data['every'] = $this->_post->toString('r_every');
					$data['time_from'] = $this->_post->toString('r_hour_from') . ":" . $this->_post->toString('r_minute_from') . ":00";
					$data['time_to'] = $this->_post->toString('r_hour_to') . ":" . $this->_post->toString('r_minute_to') . ":00";
					break;
			}

			$id = pjVoucherModel::factory()->setAttributes($data)->insert()->getInsertId();
			if ($id !== false && (int) $id > 0) {
				if ($product_id_arr = $this->_post->toArray('product_id')) {
					$pjVoucherProductModel = pjVoucherProductModel::factory();
					$pjVoucherProductModel->begin();
					foreach ($product_id_arr as $product_id) {
						$pjVoucherProductModel->reset()->setAttributes(array(
							'voucher_id' => $id,
							'company_id' => $default_company_id,
							'product_id' => $product_id
						))->insert();
					}
					$pjVoucherProductModel->commit();
				}
				$err = 'AV01';
			} else {
				$err = 'AV02';
			}

			pjUtil::redirect(sprintf("%s?controller=pjAdminVouchers&action=pjActionIndex&err=%s", $_SERVER['PHP_SELF'], $err));
		} else {
			$pjProductModel = pjProductModel::factory()
				->select('t1.*, t2.content AS name')
				->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='" . $this->getLocaleId() . "'", 'left outer')->where('t1.company_id', $default_company_id);

			$product_arr = $pjProductModel->orderBy('`name` ASC')->findAll()->getData();
			$this->set('product_arr', $product_arr);

			$this->appendCss('css/select2.min.css', PJ_THIRD_PARTY_PATH . 'select2/');
			$this->appendJs('js/select2.full.min.js', PJ_THIRD_PARTY_PATH . 'select2/');
			$this->appendJs('moment-with-locales.min.js', PJ_THIRD_PARTY_PATH . 'moment/');
			$this->appendCss('datepicker.css', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/');
			$this->appendJs('bootstrap-datepicker.js', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/');
			$this->appendJs('pjAdminVouchers.js');
		}
	}

	public function pjActionDeleteVoucher()
	{
		$this->setAjax(true);

		if (!$this->isXHR()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		if (!pjAuth::factory()->hasAccess()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Access denied.'));
		}
		if (!($this->_get->toInt('id'))) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Missing, empty or invalid parameters.'));
		}
		$id = $this->_get->toInt('id');
		$pjVoucherModel = pjVoucherModel::factory();
		// $arr = $pjVoucherModel->find($id)->getData();
		// if (!$arr) {
		// 	self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Voucher not found.'));
		// }
		$default_company_id = $_SESSION['admin_selected_company']['id'];

		$arr = $pjVoucherModel
			->where('t1.id', $id)
			->where('t1.company_id', $default_company_id)
			->limit(1)
			->findAll()
			->getData();

		if (empty($arr)) {
			self::jsonResponse([
				'status' => 'ERR',
				'code'   => 103,
				'text'   => 'Voucher not found or not assigned to this company.'
			]);
		}

		$arr = $arr[0];

		if ($pjVoucherModel->reset()->set('id', $id)->erase()->getAffectedRows() == 1) {
			pjVoucherProductModel::factory()->where('voucher_id', $id)->eraseAll();
			self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Voucher has been deleted'));
		} else {
			self::jsonResponse(array('status' => 'ERR', 'code' => 105, 'text' => 'Voucher has not been deleted.'));
		}
		exit;
	}

	public function pjActionDeleteVoucherBulk()
	{
		$this->setAjax(true);

		if (!pjAuth::factory()->hasAccess()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Access denied.'));
		}

		if (!$this->isXHR()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		if (!self::isPost()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.'));
		}

		if (!$this->_post->has('record') || !($record = $this->_post->toArray('record'))) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Missing, empty or invalid data.'));
		}

		pjVoucherModel::factory()->whereIn('id', $record)->eraseAll();
		pjVoucherProductModel::factory()->whereIn('voucher_id', $record)->eraseAll();

		self::jsonResponse(array('status' => 'OK'));
	}

	public function pjActionGetVoucher()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			$default_company_id = isset($_SESSION[$this->defaultCompany]['id'])
				? (int) $_SESSION[$this->defaultCompany]['id']
				: 1;
			$pjVoucherModel = pjVoucherModel::factory();
			$pjVoucherModel->where('t1.company_id', $default_company_id);

			if ($q = $this->_get->toString('q')) {
				$q = str_replace(array('%', '_'), array('\%', '\_'), trim($q));
				$pjVoucherModel->where('t1.code LIKE', "%$q%");
			}
			if ($this->_get->check('valid') && $this->_get->toString('valid') != '') {
				$pjVoucherModel->where('t1.valid', $this->_get->toString('valid'));
			}

			$column = 'code';
			$direction = 'DESC';
			if ($this->_get->check('direction') && $this->_get->check('column') && in_array(strtoupper($this->_get->toString('direction')), array('ASC', 'DESC'))) {
				$column = $this->_get->toString('column');
				$direction = strtoupper($this->_get->toString('direction'));
			}

			$total = $pjVoucherModel->findCount()->getData();
			$rowCount = $this->_get->check('rowCount') && $this->_get->toInt('rowCount') > 0 ? $this->_get->toInt('rowCount') : 10;
			$pages = ceil($total / $rowCount);
			$page = $this->_get->check('page') && $this->_get->toInt('page') > 0 ? $this->_get->toInt('page') : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages) {
				$page = $pages;
			}

			$data = $pjVoucherModel
				->select('t1.*,
				(
					SELECT GROUP_CONCAT(TL.content SEPARATOR "<br/>")
					FROM `' . pjProductModel::factory()->getTable() . '` AS TP 
						LEFT OUTER JOIN `' . pjMultiLangModel::factory()->getTable() . '` AS TL ON TL.model="pjProduct" AND TL.foreign_id=TP.id AND TL.locale="' . $this->getLocaleId() . '" AND TL.field="name"
						WHERE TP.id IN (
			                         	SELECT TVP.product_id
										FROM `' . pjVoucherProductModel::factory()->getTable() . '` AS TVP
										WHERE TVP.voucher_id = t1.id
									)
				) AS products')
				->orderBy("$column $direction")->limit($rowCount, $offset)->findAll()->getData();

			$daynames = __('daynames', true);
			foreach ($data as $k => $v) {
				$data[$k]['discount_f'] = $v['type'] == 'amount' ? pjCurrency::formatPrice($v['discount']) : $v['discount'] . '%';
				$data[$k]['products'] = !empty($v['products']) ? $v['products'] : __('lblAll', true);
				switch ($v['valid']) {
					case 'fixed':
						$data[$k]['valid_f'] = sprintf('%s, %s - %s', pjDateTime::formatDate($v['date_from'], 'Y-m-d', $this->option_arr['o_date_format']), substr($v['time_from'], 0, 5), substr($v['time_to'], 0, 5));
						break;
					case 'period':
						$data[$k]['valid_f'] = sprintf('%s, %s &divide; %s, %s', pjDateTime::formatDate($v['date_from'], 'Y-m-d', $this->option_arr['o_date_format']), substr($v['time_from'], 0, 5), pjDateTime::formatDate($v['date_to'], 'Y-m-d', $this->option_arr['o_date_format']), substr($v['time_to'], 0, 5));
						break;
					case 'recurring':
						$data[$k]['valid_f'] = sprintf('%s, %s - %s', @$daynames[$v['every']], substr($v['time_from'], 0, 5), substr($v['time_to'], 0, 5));
						break;
				}
			}

			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}

	public function pjActionIndex()
	{
		$this->checkLogin();
		if (!pjAuth::factory()->hasAccess()) {
			$this->sendForbidden();
			return;
		}

		$this->set('has_create', pjAuth::factory('pjAdminVouchers', 'pjActionCreate')->hasAccess());
		$this->set('has_update', pjAuth::factory('pjAdminVouchers', 'pjActionUpdate')->hasAccess());
		$this->set('has_delete', pjAuth::factory('pjAdminVouchers', 'pjActionDeleteVoucher')->hasAccess());
		$this->set('has_delete_bulk', pjAuth::factory('pjAdminVouchers', 'pjActionDeleteVoucherBulk')->hasAccess());

		$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
		$this->appendJs('pjAdminVouchers.js');
	}

	public function pjActionSaveVoucher()
	{
		$this->setAjax(true);

		if (!$this->isXHR()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}

		if (!self::isPost()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.'));
		}

		if (!pjAuth::factory($this->_get->toString('controller'), 'pjActionUpdate')->hasAccess()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Access denied.'));
		}
		$pjVoucherModel = pjVoucherModel::factory();
		$arr = $pjVoucherModel->find($this->_get->toInt('id'))->getData();
		if (!$arr) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Voucher not found.'));
		}
		if (!in_array($this->_post->toString('column'), $pjVoucherModel->getI18n())) {
			$pjVoucherModel->reset()->where('id', $this->_get->toInt('id'))->limit(1)->modifyAll(array($this->_post->toString('column') => $this->_post->toString('value')));
		} else {
			pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($this->_post->toString('column') => $this->_post->toString('value'))), $this->_get->toInt('id'), 'pjVoucher', 'data');
		}
		self::jsonResponse(array('status' => 'OK', 'code' => 201, 'text' => 'Voucher has been updated.'));
		exit;
	}

	public function pjActionUpdate()
	{
		$this->checkLogin();
		if (!pjAuth::factory()->hasAccess()) {
			$this->sendForbidden();
			return;
		}

		if ($this->_post->check('voucher_update')) {
			$data = array();
			$data['id'] = $this->_post->toInt('id');
			$data['code'] = $this->_post->toString('code');
			$data['discount'] = $this->_post->toString('discount');
			$data['type'] = $this->_post->toString('type');
			$data['valid'] = $this->_post->toString('valid');
			$data['apply'] = $this->_post->check('apply') ? 'each' : 'total';
			switch ($this->_post->toString('valid')) {
				case 'fixed':
					$data['date_from'] = pjDateTime::formatDate($this->_post->toString('f_date'), $this->option_arr['o_date_format']);
					$data['date_to'] = $data['date_from'];
					$data['time_from'] = $this->_post->toString('f_hour_from') . ":" . $this->_post->toString('f_minute_from') . ":00";
					$data['time_to'] = $this->_post->toString('f_hour_to') . ":" . $this->_post->toString('f_minute_to') . ":00";
					$data['every'] = array('NULL');
					break;
				case 'period':
					$data['date_from'] = pjDateTime::formatDate($this->_post->toString('p_date_from'), $this->option_arr['o_date_format']);
					$data['date_to'] = pjDateTime::formatDate($this->_post->toString('p_date_to'), $this->option_arr['o_date_format']);
					$data['time_from'] = $this->_post->toString('p_hour_from') . ":" . $this->_post->toString('p_minute_from') . ":00";
					$data['time_to'] = $this->_post->toString('p_hour_to') . ":" . $this->_post->toString('p_minute_to') . ":00";
					$data['every'] = array('NULL');
					break;
				case 'recurring':
					$data['date_from'] = array('NULL');
					$data['date_to'] = array('NULL');
					$data['every'] = $this->_post->toString('r_every');
					$data['time_from'] = $this->_post->toString('r_hour_from') . ":" . $this->_post->toString('r_minute_from') . ":00";
					$data['time_to'] = $this->_post->toString('r_hour_to') . ":" . $this->_post->toString('r_minute_to') . ":00";
					break;
			}
			$pjVoucherProductModel = pjVoucherProductModel::factory();
			$pjVoucherProductModel->where('voucher_id', $this->_post->toInt('id'))->eraseAll();

			if ($product_id_arr = $this->_post->toArray('product_id')) {
				$pjVoucherProductModel->begin();
				foreach ($product_id_arr as $product_id) {
					$pjVoucherProductModel->reset()->setAttributes(array(
						'voucher_id' => $this->_post->toInt('id'),
						'product_id' => $product_id
					))->insert();
				}
				$pjVoucherProductModel->commit();
			}

			if (pjVoucherModel::factory()->set('id', $data['id'])->modify($data)->getAffectedRows() == 1) {
				$err = 'AV05';
			} else {
				$err = 'AV06';
			}
			pjUtil::redirect(sprintf("%s?controller=pjAdminVouchers&action=pjActionIndex&err=%s", $_SERVER['PHP_SELF'], $err));
		} else {
			$default_company_id = $_SESSION[$this->defaultCompany]['id'];
			$id = $this->_get->toInt('id');

			$arr = pjVoucherModel::factory()
				->where('t1.company_id', $default_company_id)
				->where('t1.id', $id)
				->findAll()
				->getData();

			if (empty($arr)) {
				pjUtil::redirect(sprintf(
					"%s?controller=pjAdminVouchers&action=pjActionIndex&err=%s",
					$_SERVER['PHP_SELF'],
					'AV08'
				));
			}

			// Single record expected → take first element
			$this->set('arr', $arr[0]);

			$this->set('vp_arr', pjVoucherProductModel::factory()->where('t1.voucher_id', $arr['id'])->findAll()->getDataPair('product_id', 'product_id'));

			$pjProductModel = pjProductModel::factory()
				->select('t1.*, t2.content AS name')
				->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='" . $this->getLocaleId() . "'", 'left outer');

			$product_arr = $pjProductModel->orderBy('`name` ASC')->findAll()->getData();
			$this->set('product_arr', $product_arr);

			$this->appendCss('css/select2.min.css', PJ_THIRD_PARTY_PATH . 'select2/');
			$this->appendJs('js/select2.full.min.js', PJ_THIRD_PARTY_PATH . 'select2/');
			$this->appendJs('moment-with-locales.min.js', PJ_THIRD_PARTY_PATH . 'moment/');
			$this->appendCss('datepicker.css', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/');
			$this->appendJs('bootstrap-datepicker.js', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/');
			$this->appendJs('pjAdminVouchers.js');
		}
	}
}
