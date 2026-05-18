<?php
if (!defined("ROOT_PATH")) {
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminBrands extends pjAdmin
{
	public function pjActionDeleteBrand()
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
		$brand_ids_arr = array();
		$brand_ids_arr[] = $this->_get->toInt('id');
		$node_arr = pjBrandModel::factory()->getNode($this->getLocaleId(), $this->_get->toInt('id'));
		foreach ($node_arr as $item) {
			$brand_ids_arr[] = $item['data']['id'];
		}

		$pjBrandModel = pjBrandModel::factory();

		$pjBrandModel->deleteNode($this->_get->toInt('id'));
		$pjBrandModel->rebuildTree(1, 1);

		$brand_ids_arr = array_unique($brand_ids_arr);
		if ($brand_ids_arr) {
			pjMultiLangModel::factory()->where('model', 'pjBrand')->whereIn('foreign_id', $brand_ids_arr)->eraseAll();
			pjProductBrandModel::factory()->whereIn('brand_id', $brand_ids_arr)->eraseAll();
		}

		self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Brand has been deleted'));
		exit;
	}

	public function pjActionDeleteBrandBulk()
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
		$pjBrandModel = pjBrandModel::factory();
		$brand_ids_arr = $record;
		foreach ($record as $id) {
			$node_arr = pjBrandModel::factory()->getNode($this->getLocaleId(), $id);
			foreach ($node_arr as $item) {
				$brand_ids_arr[] = $item['data']['id'];
			}
		}

		$pjBrandModel->reset()->whereIn('id', $record)->eraseAll();
		foreach ($record as $id) {
			$pjBrandModel->deleteNode($id);
			$pjBrandModel->rebuildTree(1, 1);
		}

		$brand_ids_arr = array_unique($brand_ids_arr);
		if ($brand_ids_arr) {
			pjMultiLangModel::factory()->where('model', 'pjBrand')->whereIn('foreign_id', $brand_ids_arr)->eraseAll();
			pjProductBrandModel::factory()->whereIn('brand_id', $brand_ids_arr)->eraseAll();
		}

		self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Brands has been deleted.'));
		exit;
	}

	public function pjActionGetBrand()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			$default_company_id = isset($_SESSION[$this->defaultCompany]['id'])
				? (int) $_SESSION[$this->defaultCompany]['id']
				: 1; // fallback to first company (optional)
			$pjBrandModel = pjBrandModel::factory();

			$column = 'id';
			$direction = 'ASC';
			$data = $pjBrandModel->getNode($this->getLocaleId(), 1);

			$total = count($data);
			$rowCount = $this->_get->toInt('rowCount') ?: 50;
			$pages = ceil($total / $rowCount);
			$page = $this->_get->toInt('page') ?: 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages) {
				$page = $pages;
			}

			$c_arr = $pjBrandModel
				->reset()
				->select(sprintf("t1.id, (SELECT COUNT(*) FROM `%s` WHERE `brand_id` = `t1`.`id` LIMIT 1) AS `products`", pjProductBrandModel::factory()->getTable()))
				->where('t1.company_id', $default_company_id)

				->findAll()
				->getDataPair('id', 'products');

			$data = array_slice($data, $offset, $rowCount);
			$stack = array();
			foreach ($data as $k => $brand) {
				$data[$k]['products'] = (int) @$c_arr[$brand['data']['id']];
				$data[$k]['up'] = 0;
				$data[$k]['down'] = 0;
				$data[$k]['id'] = (int) $brand['data']['id'];
				if (!isset($stack[$brand['deep'] . "|" . $brand['data']['parent_id']])) {
					$stack[$brand['deep'] . "|" . $brand['data']['parent_id']] = 0;
				}
				$stack[$brand['deep'] . "|" . $brand['data']['parent_id']] += 1;
				if ($stack[$brand['deep'] . "|" . $brand['data']['parent_id']] > 1) {
					$data[$k]['up'] = 1;
				}
				//FIXME
				if (isset($data[$k + 1]) && $data[$k + 1]['deep'] == $brand['deep'] || $stack[$brand['deep'] . "|" . $brand['data']['parent_id']] < $brand['siblings']) {
					$data[$k]['down'] = 1;
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
		$this->setLocalesData();
		$this->set('node_arr', pjBrandModel::factory()->getNode($this->getLocaleId(), 1));

		$this->set('has_create', pjAuth::factory('pjAdminBrands', 'pjActionCreateForm')->hasAccess());
		$this->set('has_update', pjAuth::factory('pjAdminBrands', 'pjActionUpdateForm')->hasAccess());
		$this->set('has_delete', pjAuth::factory('pjAdminBrands', 'pjActionDeleteBrand')->hasAccess());
		$this->set('has_delete_bulk', pjAuth::factory('pjAdminBrands', 'pjActionDeleteBrandBulk')->hasAccess());

		$this->appendJs('jquery.multilang.js', $this->getConstant('pjBase', 'PLUGIN_JS_PATH'), false, false);
		$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
		$this->appendJs('pjAdminBrands.js');
	}

	public function pjActionCreate()
	{
		$this->setAjax(true);
		if (!pjAuth::factory()->hasAccess()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Access denied.'));
		}
		if (!$this->isXHR()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		if (!self::isPost()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.'));
		}
		if (!$this->_post->toInt('brand_create')) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Missing, empty or invalid parameters.'));
		}
		$default_company_id = isset($_SESSION[$this->defaultCompany]['id'])
			? (int) $_SESSION[$this->defaultCompany]['id']
			: 1;
		$data = $this->_post->raw();
		$data['company_id'] = $default_company_id;
		$id = pjBrandModel::factory()->saveNode($data, $this->_post->toInt('parent_id'));
		if ($id !== false && (int) $id > 0) {
			if ($this->_post->toArray('i18n')) {
				pjMultiLangModel::factory()->saveMultiLang($this->_post->toArray('i18n'), $id, 'pjBrand', 'data');
			}
			self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Brand has been added!'));
		} else {
			self::jsonResponse(array('status' => 'ERR', 'code' => 104, 'text' => 'Brand could not be added!'));
		}
		exit;
	}

	public function pjActionUpdate()
	{
		$this->setAjax(true);
		if (!pjAuth::factory()->hasAccess()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Access denied.'));
		}
		if (!$this->isXHR()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		if (!self::isPost()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.'));
		}
		if (!$this->_post->toInt('brand_update')) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Missing, empty or invalid parameters.'));
		}
		if (!$this->_post->toInt('id')) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 104, 'text' => 'Missing, empty or invalid parameters.'));
		}
		pjBrandModel::factory()->updateNode($this->_post->raw());
		if ($this->_post->toArray('i18n')) {
			pjMultiLangModel::factory()->updateMultiLang($this->_post->toArray('i18n'), $this->_post->toInt('id'), 'pjBrand', 'data');
		}
		self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Brand has been updated!'));
		exit;
	}

	public function pjActionSaveBrand()
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
		$pjBrandModel = pjBrandModel::factory();
		$arr = $pjBrandModel->find($this->_get->toInt('id'))->getData();
		if (!$arr) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Brand not found.'));
		}
		if (!in_array($this->_post->toString('column'), $pjBrandModel->getI18n())) {
			$pjBrandModel->reset()->where('id', $this->_get->toInt('id'))->limit(1)->modifyAll(array($this->_post->toString('column') => $this->_post->toString('value')));
		} else {
			pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($this->_post->toString('column') => $this->_post->toString('value'))), $this->_get->toInt('id'), 'pjBrand', 'data');
		}

		self::jsonResponse(array('status' => 'OK', 'code' => 201, 'text' => 'Brand has been updated.'));

		exit;
	}

	public function pjActionCreateForm()
	{
		$this->setAjax(true);

		if (!$this->isXHR()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		if (!self::isGet()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.'));
		}
		$this->setLocalesData();

		$this->set('node_arr', pjBrandModel::factory()->getNode($this->getLocaleId(), 1));
	}
	public function pjActionUpdateForm()
	{
		$this->setAjax(true);

		if (!$this->isXHR()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		if (!self::isGet()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.'));
		}
		if ($this->_get->toInt('id')) {
			$id = $this->_get->toInt('id');
			$arr = pjBrandModel::factory()->find($id)->getData();
			if (count($arr) === 0) {
				self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Brand is not found.'));
			}
			$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjBrand');
			$this->set('arr', $arr);

			$this->setLocalesData();

			$this->set('node_arr', pjBrandModel::factory()->getNode($this->getLocaleId(), 1));
		} else {
			self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Missing parameters.'));
		}
	}

	public function pjActionSetOrder()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			$pjBrandModel = pjBrandModel::factory();

			$node = $pjBrandModel->find($this->_post->toInt('id'))->getData();
			if (count($node) > 0) {
				$pjBrandModel->reset();
				$opts = array();
				switch ($this->_post->toString('direction')) {
					case 'up':
						$pjBrandModel->where('t1.lft <', $node['lft'])->orderBy('t1.lft DESC');
						break;
					case 'down':
						$pjBrandModel->where('t1.lft >', $node['lft'])->orderBy('t1.lft ASC');
						break;
				}

				$neighbour = $pjBrandModel
					->where('t1.id !=', $node['id'])
					->where('t1.parent_id', $node['parent_id'])
					->limit(1)->findAll()->getData();
				if (count($neighbour) === 1) {
					$neighbour = $neighbour[0];
					$pjBrandModel->reset()->set('id', $neighbour['id'])->modify(array('lft' => $node['lft'], 'rgt' => $node['rgt']));
					$pjBrandModel->reset()->set('id', $node['id'])->modify(array('lft' => $neighbour['lft'], 'rgt' => $neighbour['rgt']));
					$pjBrandModel->reset()->rebuildTree(1, 1);
				} else {
					//last one
				}
			}
		}
		exit;
	}
}
