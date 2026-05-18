<?php
if (!defined("ROOT_PATH")) {
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminCategories extends pjAdmin
{
	public function pjActionDeleteCategory()
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
		$category_ids_arr = array();
		$category_ids_arr[] = $this->_get->toInt('id');
		$node_arr = pjCategoryModel::factory()->getNode($this->getLocaleId(), $this->_get->toInt('id'));
		foreach ($node_arr as $item) {
			$category_ids_arr[] = $item['data']['id'];
		}

		$pjCategoryModel = pjCategoryModel::factory();

		$pjCategoryModel->deleteNode($this->_get->toInt('id'));
		$pjCategoryModel->rebuildTree(1, 1);

		$category_ids_arr = array_unique($category_ids_arr);
		if ($category_ids_arr) {
			pjMultiLangModel::factory()->where('model', 'pjCategory')->whereIn('foreign_id', $category_ids_arr)->eraseAll();
			pjProductCategoryModel::factory()->whereIn('category_id', $category_ids_arr)->eraseAll();
		}

		self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Category has been deleted'));
		exit;
	}

	public function pjActionDeleteCategoryBulk()
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
		$pjCategoryModel = pjCategoryModel::factory();
		$category_ids_arr = $record;
		foreach ($record as $id) {
			$node_arr = pjCategoryModel::factory()->getNode($this->getLocaleId(), $id);
			foreach ($node_arr as $item) {
				$category_ids_arr[] = $item['data']['id'];
			}
		}

		$pjCategoryModel->reset()->whereIn('id', $record)->eraseAll();
		foreach ($record as $id) {
			$pjCategoryModel->deleteNode($id);
			$pjCategoryModel->rebuildTree(1, 1);
		}

		$category_ids_arr = array_unique($category_ids_arr);
		if ($category_ids_arr) {
			pjMultiLangModel::factory()->where('model', 'pjCategory')->whereIn('foreign_id', $category_ids_arr)->eraseAll();
			pjProductCategoryModel::factory()->whereIn('category_id', $category_ids_arr)->eraseAll();
		}

		self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Categories has been deleted.'));
		exit;
	}

	public function pjActionGetCategory()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			$default_company_id = isset($_SESSION[$this->defaultCompany]['id'])
				? (int) $_SESSION[$this->defaultCompany]['id']
				: 1; // fallback to first company (optional)
			$pjCategoryModel = pjCategoryModel::factory();

			$column = 'id';
			$direction = 'ASC';
			$data = $pjCategoryModel->getNode($this->getLocaleId(), 1);

			$total = count($data);
			$rowCount = $this->_get->toInt('rowCount') ?: 50;
			$pages = ceil($total / $rowCount);
			$page = $this->_get->toInt('page') ?: 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages) {
				$page = $pages;
			}

			$c_arr = $pjCategoryModel
				->reset()
				->select(sprintf("t1.id, (SELECT COUNT(*) FROM `%s` WHERE `category_id` = `t1`.`id` LIMIT 1) AS `products`", pjProductCategoryModel::factory()->getTable()))
				->where('t1.company_id', $default_company_id)

				->findAll()
				->getDataPair('id', 'products');

			$data = array_slice($data, $offset, $rowCount);
			$stack = array();
			foreach ($data as $k => $category) {
				$data[$k]['products'] = (int) @$c_arr[$category['data']['id']];
				$data[$k]['up'] = 0;
				$data[$k]['down'] = 0;
				$data[$k]['id'] = (int) $category['data']['id'];
				if (!isset($stack[$category['deep'] . "|" . $category['data']['parent_id']])) {
					$stack[$category['deep'] . "|" . $category['data']['parent_id']] = 0;
				}
				$stack[$category['deep'] . "|" . $category['data']['parent_id']] += 1;
				if ($stack[$category['deep'] . "|" . $category['data']['parent_id']] > 1) {
					$data[$k]['up'] = 1;
				}
				//FIXME
				if (isset($data[$k + 1]) && $data[$k + 1]['deep'] == $category['deep'] || $stack[$category['deep'] . "|" . $category['data']['parent_id']] < $category['siblings']) {
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
		$this->set('node_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));

		$this->set('has_create', pjAuth::factory('pjAdminCategories', 'pjActionCreateForm')->hasAccess());
		$this->set('has_update', pjAuth::factory('pjAdminCategories', 'pjActionUpdateForm')->hasAccess());
		$this->set('has_delete', pjAuth::factory('pjAdminCategories', 'pjActionDeleteCategory')->hasAccess());
		$this->set('has_delete_bulk', pjAuth::factory('pjAdminCategories', 'pjActionDeleteCategoryBulk')->hasAccess());

		$this->appendJs('jquery.multilang.js', $this->getConstant('pjBase', 'PLUGIN_JS_PATH'), false, false);
		$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
		$this->appendJs('pjAdminCategories.js');
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
		if (!$this->_post->toInt('category_create')) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Missing, empty or invalid parameters.'));
		}
		$default_company_id = isset($_SESSION[$this->defaultCompany]['id'])
			? (int) $_SESSION[$this->defaultCompany]['id']
			: 1;
		$data = $this->_post->raw();
		$data['company_id'] = $default_company_id;
		// echo "<pre>"; print_r($data);
		// echo "<pre>"; print_r($this->_post); die;
		$id = pjCategoryModel::factory()->saveNode($data, $this->_post->toInt('parent_id'));
		if ($id !== false && (int) $id > 0) {
			if ($this->_post->toArray('i18n')) {
				pjMultiLangModel::factory()->saveMultiLang($this->_post->toArray('i18n'), $id, 'pjCategory', 'data');
			}
			self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Category has been added!'));
		} else {
			self::jsonResponse(array('status' => 'ERR', 'code' => 104, 'text' => 'Category could not be added!'));
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
		if (!$this->_post->toInt('category_update')) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Missing, empty or invalid parameters.'));
		}
		if (!$this->_post->toInt('id')) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 104, 'text' => 'Missing, empty or invalid parameters.'));
		}
		pjCategoryModel::factory()->updateNode($this->_post->raw());
		if ($this->_post->toArray('i18n')) {
			pjMultiLangModel::factory()->updateMultiLang($this->_post->toArray('i18n'), $this->_post->toInt('id'), 'pjCategory', 'data');
		}
		self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Category has been updated!'));
		exit;
	}

	public function pjActionSaveCategory()
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
		$pjCategoryModel = pjCategoryModel::factory();
		$arr = $pjCategoryModel->find($this->_get->toInt('id'))->getData();
		if (!$arr) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Category not found.'));
		}
		if (!in_array($this->_post->toString('column'), $pjCategoryModel->getI18n())) {
			$pjCategoryModel->reset()->where('id', $this->_get->toInt('id'))->limit(1)->modifyAll(array($this->_post->toString('column') => $this->_post->toString('value')));
		} else {
			pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($this->_post->toString('column') => $this->_post->toString('value'))), $this->_get->toInt('id'), 'pjCategory', 'data');
		}

		self::jsonResponse(array('status' => 'OK', 'code' => 201, 'text' => 'Category has been updated.'));

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

		$this->set('node_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));
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
			$arr = pjCategoryModel::factory()->find($id)->getData();
			if (count($arr) === 0) {
				self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Category is not found.'));
			}
			$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjCategory');
			$this->set('arr', $arr);

			$this->setLocalesData();

			$this->set('node_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));
		} else {
			self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Missing parameters.'));
		}
	}

	public function pjActionSetOrder()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			$pjCategoryModel = pjCategoryModel::factory();

			$node = $pjCategoryModel->find($this->_post->toInt('id'))->getData();
			if (count($node) > 0) {
				$pjCategoryModel->reset();
				$opts = array();
				switch ($this->_post->toString('direction')) {
					case 'up':
						$pjCategoryModel->where('t1.lft <', $node['lft'])->orderBy('t1.lft DESC');
						break;
					case 'down':
						$pjCategoryModel->where('t1.lft >', $node['lft'])->orderBy('t1.lft ASC');
						break;
				}

				$neighbour = $pjCategoryModel
					->where('t1.id !=', $node['id'])
					->where('t1.parent_id', $node['parent_id'])
					->limit(1)->findAll()->getData();
				if (count($neighbour) === 1) {
					$neighbour = $neighbour[0];
					$pjCategoryModel->reset()->set('id', $neighbour['id'])->modify(array('lft' => $node['lft'], 'rgt' => $node['rgt']));
					$pjCategoryModel->reset()->set('id', $node['id'])->modify(array('lft' => $neighbour['lft'], 'rgt' => $neighbour['rgt']));
					$pjCategoryModel->reset()->rebuildTree(1, 1);
				} else {
					//last one
				}
			}
		}
		exit;
	}
}
