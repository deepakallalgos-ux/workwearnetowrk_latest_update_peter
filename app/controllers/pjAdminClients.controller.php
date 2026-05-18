<?php
if (!defined("ROOT_PATH")) {
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminClients extends pjAdmin
{
	public function pjActionCheckEmail()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			if (!$this->_get->toString('email')) {
				echo 'false';
				exit;
			}
			$default_company = $_SESSION[$this->defaultCompany];

			$pjClientModel = pjClientModel::factory()
				->where('t1.company_id', $default_company['id'])
				->where('t1.email', $this->_get->toString('email'));
			if ($this->_get->toInt('id')) {
				$pjClientModel->where('t1.id !=', $this->_get->toInt('id'));
			}

			echo $pjClientModel->findCount()->getData() == 0 ? 'true' : 'false';
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
		$default_company = $_SESSION[$this->defaultCompany];

		if (self::isPost() && $this->_post->toInt('client_create')) {
			$data = array();
			$data['status'] = $this->_post->check('status') ? 'T' : 'F';
			$data['company_id'] = $default_company['id'];
			$client_id = pjClientModel::factory()->setAttributes(array_merge($this->_post->raw(), $data))->insert()->getInsertId();
			if ($client_id !== false && (int) $client_id > 0) {
				$post = $this->_post->raw();
				if (isset($post['name'])) {
					$pjAddressModel = pjAddressModel::factory();
					$pjAddressModel->begin();
					foreach ($post['name'] as $k => $v) {
						if (!empty($v)) {
							$pjAddressModel->reset()->setAttributes(array(
								'client_id' => $client_id,
								'company_id' => $default_company['id'],
								'country_id' => $post['country_id'][$k],
								'state' => $post['state'][$k],
								'city' => $post['city'][$k],
								'zip' => $post['zip'][$k],
								'address_1' => $post['address_1'][$k],
								'address_2' => $post['address_2'][$k],
								'name' => $post['name'][$k],
								'is_default_shipping' => ($post['is_default_shipping'] == $k ? 1 : 0),
								'is_default_billing' => ($post['is_default_billing'] == $k ? 1 : 0)
							))->insert();
						}
					}
					$pjAddressModel->commit();
				}
				$err = 'AC01';
			} else {
				$err = 'AC02';
			}
			pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminClients&action=pjActionIndex&err=$err");
		}
		if (self::isGet()) {
			$country_arr = pjBaseCountryModel::factory()
				->select('t1.id, t2.content AS country_title')
				->join('pjBaseMultiLang', "t2.model='pjBaseCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='" . $this->getLocaleId() . "'", 'left outer')
				->where('status', 'T')
				->orderBy('`country_title` ASC')->findAll()->getData();
			$this->set('country_arr', $country_arr);

			$this->appendCss('css/select2.min.css', PJ_THIRD_PARTY_PATH . 'select2/');
			$this->appendJs('js/select2.full.min.js', PJ_THIRD_PARTY_PATH . 'select2/');
			$this->appendJs('pjAdminClients.js');
		}
	}

	public function pjActionDeleteClient()
	{
		$this->setAjax(true);

		if (!$this->isXHR()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		if (!self::isPost()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.'));
		}
		if (!pjAuth::factory()->hasAccess()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Access denied.'));
		}
		if (!($this->_get->toInt('id'))) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Missing, empty or invalid parameters.'));
		}
		if (!pjClientModel::factory()->set('id', $this->_get->toInt('id'))->erase()->getAffectedRows()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 105, 'text' => 'Client has not been deleted.'));
		}
		$default_company = $_SESSION[$this->defaultCompany];

		pjAddressModel::factory()
			->where('t1.company_id', $default_company['id'])
			->where('client_id', $this->_get->toInt('id'))->eraseAll();
		self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Client has been deleted'));
		exit;
	}

	public function pjActionDeleteClientBulk()
	{
		$this->setAjax(true);
		if (!$this->isXHR()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		if (!self::isPost()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.'));
		}
		if (!pjAuth::factory()->hasAccess()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Access denied.'));
		}
		if (!$this->_post->has('record')) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Missing, empty or invalid parameters.'));
		}
		$record = $this->_post->toArray('record');
		if (empty($record)) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 104, 'text' => 'Missing, empty or invalid parameters.'));
		}
		$default_company = $_SESSION[$this->defaultCompany];

		pjClientModel::factory()->where('company_id', $default_company['id'])
			->whereIn('id', $record)->eraseAll();
		pjAddressModel::factory()
			->where('company_id', $default_company['id'])
			->whereIn('client_id', $record)->eraseAll();
		self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Client(s) has been deleted.'));
		exit;
	}

	public function pjActionDeleteAddress()
	{
		$this->setAjax(true);

		if (pjAddressModel::factory()->set('id', $this->_post->toInt('id'))->erase()->getAffectedRows() == 1) {
			$resp = array('status' => 'OK');
		} else {
			$resp = array('status' => 'ERR');
		}
		pjAppController::jsonResponse($resp);
	}

	public function pjActionExportClient()
	{
		$this->checkLogin();
		if (!pjAuth::factory()->hasAccess()) {
			$this->sendForbidden();
			return;
		}

		$record = $this->_post->toArray('record');
		if (count($record)) {
			$arr = pjClientModel::factory()->whereIn('id', $record)->findAll()->getData();
			$csv = new pjCSV();
			$csv
				->setHeader(true)
				->setName("Clients-" . time() . ".csv")
				->process($arr)
				->download();
		}
		exit;
	}

	public function pjActionGetAddresses()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			$default_company = $_SESSION[$this->defaultCompany];

			$this->set(
				'address_arr',
				pjAddressModel::factory()
					->where('t1.client_id', $this->_get->toInt('id'))
					->where('t1.company_id', $default_company['id'])
					->orderBy('FIELD(`is_default_shipping`, 1, 0), FIELD(`is_default_billing`, 1, 0), t1.id ASC')
					->findAll()
					->getData()
			);
			$country_arr = pjBaseCountryModel::factory()
				->select('t1.id, t2.content AS country_title')
				->join('pjBaseMultiLang', "t2.model='pjBaseCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='" . $this->getLocaleId() . "'", 'left outer')
				->where('status', 'T')
				->orderBy('`country_title` ASC')->findAll()->getData();
			$this->set('country_arr', $country_arr);
		}
	}

	public function pjActionGetClient()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			$default_company = $_SESSION[$this->defaultCompany];

			$pjClientModel = pjClientModel::factory()->where('t1.company_id', $default_company['id']);

			if ($q = $this->_get->toString('q')) {
				$pjClientModel->where("(t1.client_name LIKE '%$q%' OR t1.email LIKE '%$q%' OR t1.phone LIKE '%$q%')");
			}
			if ($this->_get->toString('status')) {
				$status = $this->_get->toString('status');
				if (in_array($status, array('T', 'F'))) {
					$pjClientModel->where('t1.status', $status);
				}
			}
			$column = 'client_name';
			$direction = 'ASC';
			if ($this->_get->toString('column') && in_array(strtoupper($this->_get->toString('direction')), array('ASC', 'DESC'))) {
				$column = $this->_get->toString('column');
				$direction = strtoupper($this->_get->toString('direction'));
			}

			$total = $pjClientModel->findCount()->getData();
			$rowCount = $this->_get->toInt('rowCount') ?: 50;
			$pages = ceil($total / $rowCount);
			$page = $this->_get->toInt('page') ?: 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages) {
				$page = $pages;
			}

			$data = $pjClientModel
				->select("t1.id, t1.client_name, t1.email, t1.phone, t1.status, 
				(SELECT COUNT(TO.client_id) FROM `" . pjOrderModel::factory()->getTable() . "` AS `TO` WHERE `TO`.client_id=t1.id) AS cnt_orders,
				(SELECT `TO`.`created` FROM `" . pjOrderModel::factory()->getTable() . "` AS `TO` WHERE `TO`.client_id=t1.id ORDER BY `TO`.`created` DESC LIMIT 1) AS last_order")
				->orderBy("$column $direction")
				->limit($rowCount, $offset)
				->findAll()
				->getData();
			foreach ($data as $k => $v) {
				$v['client_name'] = pjSanitize::stripScripts($v['client_name']);
				$v['email'] = pjSanitize::stripScripts($v['email']);
				$data[$k] = $v;
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

		$this->set('has_update', pjAuth::factory('pjAdminClients', 'pjActionUpdate')->hasAccess());
		$this->set('has_delete', pjAuth::factory('pjAdminClients', 'pjActionDeleteClient')->hasAccess());
		$this->set('has_delete_bulk', pjAuth::factory('pjAdminClients', 'pjActionDeleteClientBulk')->hasAccess());
		$this->set('has_export', pjAuth::factory('pjAdminClients', 'pjActionExportClient')->hasAccess());

		$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
		$this->appendJs('pjAdminClients.js');
	}

	public function pjActionSaveClient()
	{
		$this->setAjax(true);

		if (!$this->isXHR()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		if (!self::isPost()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.'));
		}
		$params = array(
			'id' => $this->_get->toInt('id'),
			'column' => $this->_post->toString('column'),
			'value' => $this->_post->toString('value'),
		);
		if (!(isset($params['id'], $params['column'], $params['value'])
			&& pjValidation::pjActionNumeric($params['id'])
			&& pjValidation::pjActionNotEmpty($params['column']))) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Missing, empty or invalid parameters.'));
		}
		pjClientModel::factory()->where('id', $params['id'])->limit(1)->modifyAll(array($params['column'] => $params['value']));
		self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Client has been updated!'));
		exit;
	}


	public function pjActionUpdate()
	{
		$this->checkLogin();
		if (!pjAuth::factory()->hasAccess()) {
			$this->sendForbidden();
			return;
		}
		if (self::isPost() && $this->_post->toInt('client_update') && $this->_post->toInt('id')) {
			$pjClientModel = pjClientModel::factory();
			$id = $this->_post->toInt('id');
			$post = $this->_post->raw();
			$data = array();
			$data['status'] = $this->_post->check('status') ? 'T' : 'F';
			pjClientModel::factory()->set('id', $id)->modify(array_merge($post, $data));
			if (isset($post['name'])) {
				$pjAddressModel = pjAddressModel::factory();
				$pjAddressModel->begin();
				foreach ($post['name'] as $k => $v) {
					if (empty($v)) {
						continue;
					}
					if (strpos($k, 'new_') === 0) {
						# Add new
						$pjAddressModel->reset()->setAttributes(array(
							'client_id' => $post['id'],
							'country_id' => $post['country_id'][$k],
							'state' => $post['state'][$k],
							'city' => $post['city'][$k],
							'zip' => $post['zip'][$k],
							'address_1' => $post['address_1'][$k],
							'address_2' => $post['address_2'][$k],
							'name' => $post['name'][$k],
							'is_default_shipping' => ($post['is_default_shipping'] == $k ? 1 : 0),
							'is_default_billing' => ($post['is_default_billing'] == $k ? 1 : 0)
						))->insert();
					} else {
						# Update existing
						$pjAddressModel->reset()->set('id', $k)->modify(array(
							'country_id' => $post['country_id'][$k],
							'state' => $post['state'][$k],
							'city' => $post['city'][$k],
							'zip' => $post['zip'][$k],
							'address_1' => $post['address_1'][$k],
							'address_2' => $post['address_2'][$k],
							'name' => $post['name'][$k],
							'is_default_shipping' => ($post['is_default_shipping'] == $k ? 1 : 0),
							'is_default_billing' => ($post['is_default_billing'] == $k ? 1 : 0)
						));
					}
				}
				$pjAddressModel->commit();
			}

			pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminClients&action=pjActionIndex&err=AC01");
		}
		if (self::isGet() && $this->_get->toInt('id')) {
			$id = $this->_get->toInt('id');
			$arr = pjClientModel::factory()
				->select(sprintf("t1.*, AES_DECRYPT(`password`, '%s') AS `password`, (SELECT COUNT(*) FROM `%s` WHERE `client_id` = `t1`.`id` LIMIT 1) AS `orders`", PJ_SALT, pjOrderModel::factory()->getTable()))
				->find($id)->getData();
				// echo "<pre>"; print_r($arr); die;
			if (count($arr) === 0) {
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminClients&action=pjActionIndex&err=AC08");
			}
			$this->set('arr', $arr);

			$this->set('address_arr', pjAddressModel::factory()
				->where('t1.client_id', $arr['id'])
				->orderBy('FIELD(`is_default_shipping`,1,0), FIELD(`is_default_billing`,1,0), t1.id ASC')
				->findAll()
				->getData());

			$country_arr = pjBaseCountryModel::factory()
				->select('t1.id, t2.content AS country_title')
				->join('pjBaseMultiLang', "t2.model='pjBaseCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='" . $this->getLocaleId() . "'", 'left outer')
				->where('status', 'T')
				->orderBy('`country_title` ASC')->findAll()->getData();

			$this->set('country_arr', $country_arr);

			$this->appendCss('css/select2.min.css', PJ_THIRD_PARTY_PATH . 'select2/');
			$this->appendJs('js/select2.full.min.js', PJ_THIRD_PARTY_PATH . 'select2/');
			$this->appendJs('pjAdminClients.js');
		}
	}
}
