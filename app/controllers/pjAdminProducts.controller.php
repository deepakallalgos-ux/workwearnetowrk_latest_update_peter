<?php
if (!defined("ROOT_PATH")) {
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminProducts extends pjAdmin
{
	public function pjActionOpenDigital()
	{
		$this->checkLogin();
		if (!pjAuth::factory()->hasAccess()) {
			$this->sendForbidden();
			return;
		}

		if ($this->_get->check('id') && $this->_get->toInt('id') > 0) {
			$arr = pjProductModel::factory()->find($this->_get->toInt('id'))->getData();
			if (empty($arr)) {
				exit;
			}
			if ((int) $arr['is_digital'] !== 1) {
				exit;
			}
			if (empty($arr['digital_file']) || !is_file($arr['digital_file'])) {
				exit;
			}
			$zip = new pjZipStream();

			$handle = @fopen($arr['digital_file'], "rb");
			if ($handle) {
				$zip->addLargeFile($handle, $arr['digital_name']);
				fclose($handle);
			}
			$zip->finalize();
			$zip->sendZip(sprintf("%s.zip", $arr['digital_name']));
			exit;
		} else {
			exit;
		}
	}

	public function pjActionAttrGroupDelete()
	{
		$this->setAjax(true);

		if ($this->isXHR() && $this->isLoged()) {
			if ($this->_post->check('id') && (int) $this->_post->toInt('id') > 0) {
				$default_company = $_SESSION[$this->defaultCompany];

				$pjAttributeModel = pjAttributeModel::factory();
				$ids = $pjAttributeModel->where('t1.company_id', $default_company['id'])
					->where('t1.parent_id', $this->_post->toInt('id'))->findAll()->getDataPair(null, 'id');
				if ($pjAttributeModel->reset()->set('id', $this->_post->toInt('id'))->erase()->getAffectedRows() == 1) {
					$pjMultiLangModel = pjMultiLangModel::factory();
					$pjMultiLangModel->where('model', 'pjAttribute')->where('foreign_id', $this->_post->toInt('id'))->eraseAll();

					if (!empty($ids)) {
						$pjAttributeModel->reset()->whereIn('id', $ids)->eraseAll();
						$pjMultiLangModel->reset()->where('model', 'pjAttribute')->whereIn('foreign_id', $ids)->eraseAll();
					}

					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
				}
			}
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
		}
		exit;
	}

	public function pjActionAttrDelete()
	{
		$this->setAjax(true);

		if ($this->isXHR() && $this->isLoged()) {
			if ($this->_post->check('id') && (int) $this->_post->toInt('id') > 0) {
				if (pjAttributeModel::factory()->set('id', $this->_post->toInt('id'))->erase()->getAffectedRows() == 1) {
					pjMultiLangModel::factory()->where('model', 'pjAttribute')->where('foreign_id', $this->_post->toInt('id'))->eraseAll();
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
				}
			}
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
		}
		exit;
	}

	public function pjActionAttrCopy()
	{
		$this->setAjax(true);

		if ($this->isXHR() && $this->isLoged()) {
			$default_company = $_SESSION[$this->defaultCompany];

			if ($this->_post->check('from_product_id') && $this->_post->toInt('from_product_id') > 0) {
				if ($this->_post->check('product_id') && $this->_post->toInt('product_id') > 0) {
					$ak = 'product_id';
					$av = $this->_post->toInt('product_id');
				} elseif ($this->_post->check('hash') && $this->_post->toString('hash') != '') {
					$ak = 'hash';
					$av = $this->_post->toString('hash');
				}

				$pjAttributeModel = pjAttributeModel::factory();
				$pjMultiLangModel = pjMultiLangModel::factory();

				$attr = $pjAttributeModel->where('t1.company_id', $default_company['id'])
					->where('t1.product_id', $this->_post->toInt('from_product_id'))->orderBy('t1.`order_group`, `order_item` ASC')->findAll()->getDataPair('id', 'parent_id');
				$arr = array();
				foreach ($attr as $id => $parent_id) {
					if (empty($parent_id)) {
						$arr[$id] = array();
					} else {
						$arr[$parent_id][] = $id;
					}
				}

				$multi = $pjMultiLangModel
					->where('t1.model', 'pjAttribute')
					->whereIn('t1.foreign_id', array_keys($attr))
					->where("t1.field='name'")
					->findAll()
					->getData();

				$stack = array();
				foreach ($multi as $item) {
					if (!isset($stack[$item['foreign_id']])) {
						$stack[$item['foreign_id']] = array();
					}
					$stack[$item['foreign_id']][] = $item;
				}

				$last_order = $pjAttributeModel->getLastOrder($this->_post->toInt('product_id'));

				foreach ($arr as $parent_id => $items) {
					$insert_id = $pjAttributeModel->reset()->setAttributes(array($ak => $av, 'order_group' => $last_order, 'company_id' => $default_company['id']))->insert()->getInsertId();
					if ($insert_id !== false && (int) $insert_id > 0) {
						if (isset($stack[$parent_id])) {
							foreach ($stack[$parent_id] as $locale) {
								$pjMultiLangModel->reset()->setAttributes(array(
									'model' => $locale['model'],
									'foreign_id' => $insert_id,
									'field' => $locale['field'],
									'locale' => $locale['locale'],
									'content' => $locale['content'],
									'source' => 'data'
								))->insert();
							}
						}

						$item_order = 0;
						foreach ($items as $id) {
							$attr_id = $pjAttributeModel->reset()->setAttributes(array($ak => $av, 'parent_id' => $insert_id, 'order_group' => $last_order, 'order_item' => $item_order, 'company_id' => $default_company['id']))->insert()->getInsertId();
							if ($attr_id !== false && (int) $attr_id > 0) {
								if (isset($stack[$id])) {
									foreach ($stack[$id] as $locale) {
										$pjMultiLangModel->reset()->setAttributes(array(
											'model' => $locale['model'],
											'foreign_id' => $attr_id,
											'field' => $locale['field'],
											'locale' => $locale['locale'],
											'content' => $locale['content'],
											'source' => 'data'
										))->insert();
									}
								}
								$item_order++;
							}
						}
					}
					$last_order++;
				}
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
			}
		}
		exit;
	}

	private function pjActionAttrHandle($product_id)
	{
		if ($attr_arr = $this->_post->toArray('attr')) {
			$pjAttributeModel = pjAttributeModel::factory();
			$pjMultiLangModel = pjMultiLangModel::factory();

			$i18n_arr = $this->_post->toArray('i18n');
			$keys = array_keys($i18n_arr);
			$fkey = $keys[0];
			$default_company = $_SESSION[$this->defaultCompany];

			$group_arr = $this->_post->check('orderAttributes') && $this->_post->toString('orderAttributes') != '' ? explode("|", $this->_post->toString('orderAttributes')) : array();
			$order_group_arr = array();
			foreach ($group_arr as $k => $v) {
				$order_group_arr[$v] = $k;
			}
			foreach ($attr_arr as $group_id => $whatever) {
				if (strpos($group_id, 'x_') === 0) {
					// insert new group attr
					$attr_group_id = $pjAttributeModel->reset()
						->setAttributes(array(
							'product_id' => $product_id,
							'company_id' => $default_company['id'],
							'order_group' => $order_group_arr['attrBox_' . $group_id]
						))
						->insert()->getInsertId();

					if ($attr_group_id !== false && (int) $attr_group_id > 0) {
						$tmp = $this->pjActionTurnI18n($i18n_arr, 'attr_group', $group_id, NULL, 'name');
						$pjMultiLangModel->saveMultiLang($tmp, $attr_group_id, 'pjAttribute');

						$item_arr = $this->_post->check('orderItems_' . $group_id) &&  $this->_post->toString('orderItems_' . $group_id) != '' ? explode("|", $this->_post->toString('orderItems_' . $group_id)) : array();
						$order_item_arr = array();
						foreach ($item_arr as $k => $v) {
							$order_item_arr[$v] = $k;
						}
						if (isset($i18n_arr[$fkey]['attr_item'][$group_id]) && count(($i18n_arr[$fkey]['attr_item'][$group_id])) > 0) {
							foreach ($i18n_arr[$fkey]['attr_item'][$group_id] as $index => $value) {
								$attr_item_id = $pjAttributeModel->reset()->setAttributes(array(
									'product_id' => $product_id,
									'parent_id' => $attr_group_id,
									'company_id' => $default_company['id'],
									'order_group' => $order_group_arr['attrBox_' . $group_id],
									'order_item' => $order_item_arr['attrBoxRowItems_' . $index]
								))->insert()->getInsertId();

								if ($attr_item_id !== false && (int) $attr_item_id > 0) {
									$tmp = $this->pjActionTurnI18n($i18n_arr, 'attr_item', $group_id, $index, 'name');
									$pjMultiLangModel->saveMultiLang($tmp, $attr_item_id, 'pjAttribute');
								}
							}
						}
					}
				} else {
					// update group attr
					$tmp = $this->pjActionTurnI18n($i18n_arr, 'attr_group', $group_id, NULL, 'name');
					$pjMultiLangModel->updateMultiLang($tmp, $group_id, 'pjAttribute');

					$pjAttributeModel->reset()->set('id', $group_id)->modify(array(
						'order_group' => $order_group_arr['attrBox_' . $group_id]
					));
					$item_arr = $this->_post->check('orderItems_' . $group_id) && $this->_post->toString('orderItems_' . $group_id) != '' ? explode("|", $this->_post->toString('orderItems_' . $group_id)) : array();
					$order_item_arr = array();
					foreach ($item_arr as $k => $v) {
						$order_item_arr[$v] = $k;
					}
					if (isset($i18n_arr[$fkey]['attr_item'][$group_id]) && is_array($i18n_arr[$fkey]['attr_item'][$group_id])) {
						foreach ($i18n_arr[$fkey]['attr_item'][$group_id] as $index => $value) {
							if (strpos($index, 'y_') === 0) {
								# Add items
								$attr_item_id = $pjAttributeModel->reset()->setAttributes(array(
									'product_id' => $product_id,
									'parent_id' => $group_id,
									'company_id' => $default_company['id'],
									'order_group' => $order_group_arr['attrBox_' . $group_id],
									'order_item' => $order_item_arr['attrBoxRowItems_' . $index]
								))->insert()->getInsertId();

								if ($attr_item_id !== false && (int) $attr_item_id > 0) {
									$tmp = $this->pjActionTurnI18n($i18n_arr, 'attr_item', $group_id, $index, 'name');
									$pjMultiLangModel->saveMultiLang($tmp, $attr_item_id, 'pjAttribute');
								}
							} else {
								# Update items
								$tmp = $this->pjActionTurnI18n($i18n_arr, 'attr_item', $group_id, $index, 'name');
								$pjAttributeModel->reset()->set('id', $index)->modify(array(
									'order_group' => $order_group_arr['attrBox_' . $group_id],
									'order_item' => $order_item_arr['attrBoxRowItems_' . $index]
								));
								$pjMultiLangModel->updateMultiLang($tmp, $index, 'pjAttribute');
							}
						}
					}
				}
			}
			foreach ($i18n_arr as $locale_id => $whatever) {
				if (isset($i18n_arr[$locale_id]['attr_group'])) {
					unset($i18n_arr[$locale_id]['attr_group']);
				}
				if (isset($i18n_arr[$locale_id]['attr_item'])) {
					unset($i18n_arr[$locale_id]['attr_item']);
				}
			}
			return $i18n_arr;
		}
	}

	public function pjActionCheckSku()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			if (!$this->_get->check('sku') || $this->_get->toString('sku') == '') {
				echo 'false';
				exit;
			}
			$default_company = $_SESSION[$this->defaultCompany];

			$pjProductModel = pjProductModel::factory()->where('t1.company_id', $default_company['id'])
				->where('t1.sku', $this->_get->toString('sku'));
			if ($this->_get->check('id') && $this->_get->toInt('id') > 0) {
				$pjProductModel->where('t1.id !=', $this->_get->toInt('id'));
			}
			echo $pjProductModel->findCount()->getData() == 0 ? 'true' : 'false';
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

		if (self::isPost() && $this->_post->toInt('product_create')) {
			$pjProductModel = pjProductModel::factory();

			$data = array();
			$data['is_featured'] = $this->_post->check('is_featured') ? 1 : 0;
			$data['company_id'] = $default_company['id'];
			$id = $pjProductModel->setAttributes(array_merge($this->_post->raw(), $data))->insert()->getInsertId();
			if ($id !== false && (int) $id > 0) {
				$err = 'AP01';
				if ($i18n_arr = $this->_post->toArray('i18n')) {
					pjMultiLangModel::factory()->saveMultiLang($i18n_arr, $id, 'pjProduct', 'data');
				}
				$pjProductCategoryModel = pjProductCategoryModel::factory();
				if ($category_id_arr = $this->_post->toArray('category_id')) {
					$pjProductCategoryModel->begin();
					foreach ($category_id_arr as $category_id) {
						$pjProductCategoryModel
							->reset()
							->set('product_id', $id)
							->set('company_id', $default_company['id'])
							->set('category_id', $category_id)
							->insert();
					}
					$pjProductCategoryModel->commit();
				}
				pjUtil::redirect(sprintf("%s?controller=pjAdminProducts&action=pjActionUpdate&id=%u&err=%s", $_SERVER['PHP_SELF'], $id, $err));
			} else {
				$err = 'AP02';
			}
			pjUtil::redirect(sprintf("%s?controller=pjAdminProducts&action=pjActionIndex&err=%s", $_SERVER['PHP_SELF'], $err));
		}
		if (self::isGet()) {
			$this->setLocalesData();

			$this->set('category_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));

			$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
			$this->appendCss('css/select2.min.css', PJ_THIRD_PARTY_PATH . 'select2/');
			$this->appendJs('js/select2.full.min.js', PJ_THIRD_PARTY_PATH . 'select2/');
			$this->appendJs('pjAdminProducts.js');
		}
	}

	private function pjActionDeleteProductAttr($product_id)
	{
		if (empty($product_id)) {
			return false;
		}
		$default_company_id = isset($_SESSION[$this->defaultCompany]['id'])
			? (int) $_SESSION[$this->defaultCompany]['id']
			: 1;
		$pjAttributeModel = pjAttributeModel::factory();
		if (is_array($product_id)) {
			$pjAttributeModel->whereIn('product_id', $product_id);
		} else {
			$pjAttributeModel->where('product_id', $product_id);
		}
		// $pjAttributeModel->where('company_id', $default_company_id);

		$attr_ids = $pjAttributeModel->findAll()->getDataPair(null, 'id');
		if (!empty($attr_ids)) {
			$pjAttributeModel->eraseAll();
			$pjMultiLangModel = pjMultiLangModel::factory();
			$pjMultiLangModel->reset()->where('model', 'pjAttribute')->whereIn('foreign_id', $attr_ids)->eraseAll();
		}
	}

	private function pjActionDeleteStockAttr($product_id)
	{
		if (empty($product_id)) {
			return false;
		}

		$pjStockAttributeModel = pjStockAttributeModel::factory();
		if (is_array($product_id)) {
			$pjStockAttributeModel->whereIn('product_id', $product_id);
		} else {
			$pjStockAttributeModel->where('product_id', $product_id);
		}
		$pjStockAttributeModel->eraseAll();
	}

	public function pjActionDeactivate()
	{
		$this->setAjax(true);

		if ($this->isXHR() && $this->_get->check('id') && $this->_get->toInt('id') > 0) {
			pjProductModel::factory()->where('id', $this->_get->toInt('id'))->limit(1)->modifyAll(array('status' => 2));

			pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
		}
		exit;
	}

	public function pjActionDeleteProduct()
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
		$default_company_id = isset($_SESSION[$this->defaultCompany]['id'])
			? (int) $_SESSION[$this->defaultCompany]['id']
			: 1;
		$pjProductModel = pjProductModel::factory();
		$id = $this->_get->toInt('id');
		// $default_company_id = $_SESSION['admin_selected_company']['id'];

		$arr = $pjProductModel
			->where('t1.id', $id)
			->where('t1.company_id', $default_company_id)
			->limit(1)
			->findAll()
			->getData();

		if (empty($arr)) {
			self::jsonResponse([
				'status' => 'ERR',
				'code'   => 103,
				'text'   => 'Product not found or not assigned to this company.'
			]);
		}

		$arr = $arr[0];

		if ($pjProductModel->reset()->set('id', $id)->erase()->getAffectedRows() == 1) {
			$pjExtraModel = pjExtraModel::factory();
			$pjMultiLangModel = pjMultiLangModel::factory();

			$pjMultiLangModel->where('model', 'pjProduct')->where('foreign_id', $id)->eraseAll();

			pjStockModel::factory()->where('product_id', $id)->eraseAll();
			pjCartModel::factory()->where('product_id', $id)->eraseAll();
			$this->pjActionDeleteStockAttr($id);
			pjProductSimilarModel::factory()->where('product_id', $id)->orWhere('similar_id', $id)->eraseAll();

			$extra_arr = $pjExtraModel->where('product_id', $id)->findAll()->getDataPair(null, 'id');
			if (!empty($extra_arr)) {
				$pjExtraItemModel = pjExtraItemModel::factory();
				$pjExtraModel->eraseAll();
				$pjMultiLangModel->reset()->where('model', 'pjExtra')->whereIn('foreign_id', $extra_arr)->eraseAll();
				$extra_item_arr = $pjExtraItemModel->whereIn('extra_id', $extra_arr)->findAll()->getDataPair(NULL, 'id');
				if (!empty($extra_item_arr)) {
					$pjExtraItemModel->reset()->whereIn('extra_id', $extra_arr)->eraseAll();
					$pjMultiLangModel->reset()->where('model', 'pjExtraItem')->whereIn('foreign_id', $extra_item_arr)->eraseAll();
				}
			}
			$this->pjActionDeleteProductAttr($id);
			pjProductCategoryModel::factory()->where('product_id', $id)->eraseAll();
			pjProductBrandModel::factory()->where('product_id', $id)->eraseAll();

			$pjGalleryModel = pjGalleryModel::factory();
			$image_arr = $pjGalleryModel->where('foreign_id', $id)->findAll()->getData();
			if (!empty($image_arr)) {
				$pjGalleryModel->eraseAll();
				foreach ($image_arr as $image) {
					@clearstatcache();
					if (!empty($image['small_path']) && is_file($image['small_path'])) {
						@unlink($image['small_path']);
					}
					@clearstatcache();
					if (!empty($image['medium_path']) && is_file($image['medium_path'])) {
						@unlink($image['medium_path']);
					}
					@clearstatcache();
					if (!empty($image['large_path']) && is_file($image['large_path'])) {
						@unlink($image['large_path']);
					}
					@clearstatcache();
					if (!empty($image['source_path']) && is_file($image['source_path'])) {
						@unlink($image['source_path']);
					}
				}
			}

			self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Product has been deleted'));
		} else {
			self::jsonResponse(array('status' => 'ERR', 'code' => 105, 'text' => 'Product has not been deleted.'));
		}
		exit;
	}

	// public function pjActionDeleteProductBulk()
	// {
	// 	$this->setAjax(true);

	// 	if (!pjAuth::factory()->hasAccess()) {
	// 		self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Access denied.'));
	// 	}

	// 	if (!$this->isXHR()) {
	// 		self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
	// 	}
	// 	if (!self::isPost()) {
	// 		self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.'));
	// 	}

	// 	if (!$this->_post->has('record') || !($record = $this->_post->toArray('record'))) {
	// 		self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Missing, empty or invalid data.'));
	// 	}

	// 	$pjExtraModel = pjExtraModel::factory();
	// 	$pjMultiLangModel = pjMultiLangModel::factory();

	// 	pjProductModel::factory()->whereIn('id', $record)->eraseAll();
	// 	$pjMultiLangModel->where('model', 'pjProduct')->whereIn('foreign_id', $record)->eraseAll();

	// 	pjStockModel::factory()->whereIn('product_id', $record)->eraseAll();
	// 	pjCartModel::factory()->whereIn('product_id', $record)->eraseAll();
	// 	$this->pjActionDeleteStockAttr($record);
	// 	pjProductSimilarModel::factory()->whereIn('product_id', $record)->orWhereIn('similar_id', $record)->eraseAll();

	// 	$extra_arr = $pjExtraModel->whereIn('product_id', $record)->findAll()->getDataPair(null, 'id');
	// 	if (!empty($extra_arr)) {
	// 		$pjExtraItemModel = pjExtraItemModel::factory();
	// 		$pjExtraModel->eraseAll();
	// 		$pjMultiLangModel->reset()->where('model', 'pjExtra')->whereIn('foreign_id', $extra_arr)->eraseAll();
	// 		$extra_item_arr = $pjExtraItemModel->whereIn('extra_id', $extra_arr)->findAll()->getDataPair(NULL, 'id');
	// 		if (!empty($extra_item_arr)) {
	// 			$pjExtraItemModel->reset()->whereIn('extra_id', $extra_arr)->eraseAll();
	// 			$pjMultiLangModel->reset()->where('model', 'pjExtraItem')->whereIn('foreign_id', $extra_item_arr)->eraseAll();
	// 		}
	// 	}

	// 	$this->pjActionDeleteProductAttr($record);
	// 	pjProductCategoryModel::factory()->whereIn('product_id', $record)->eraseAll();
	// 	pjProductBrandModel::factory()->whereIn('product_id', $record)->eraseAll();

	// 	$pjGalleryModel = pjGalleryModel::factory();
	// 	$image_arr = $pjGalleryModel->whereIn('foreign_id', $record)->findAll()->getData();
	// 	if (!empty($image_arr)) {
	// 		$pjGalleryModel->eraseAll();
	// 		foreach ($image_arr as $image) {
	// 			@clearstatcache();
	// 			if (!empty($image['small_path']) && is_file($image['small_path'])) {
	// 				@unlink($image['small_path']);
	// 			}
	// 			@clearstatcache();
	// 			if (!empty($image['medium_path']) && is_file($image['medium_path'])) {
	// 				@unlink($image['medium_path']);
	// 			}
	// 			@clearstatcache();
	// 			if (!empty($image['large_path']) && is_file($image['large_path'])) {
	// 				@unlink($image['large_path']);
	// 			}
	// 			@clearstatcache();
	// 			if (!empty($image['source_path']) && is_file($image['source_path'])) {
	// 				@unlink($image['source_path']);
	// 			}
	// 		}
	// 	}

	// 	self::jsonResponse(array('status' => 'OK'));
	// }
	public function pjActionDeleteProductBulk()
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

		/* SAFETY CHECK */
		$record = array_filter($record);

		if (empty($record)) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 104, 'text' => 'No products selected.'));
		}

		$pjExtraModel = pjExtraModel::factory();
		$pjMultiLangModel = pjMultiLangModel::factory();

		/* DELETE PRODUCTS */
		pjProductModel::factory()->reset()->whereIn('id', $record)->eraseAll();

		/* DELETE PRODUCT LANG */
		$pjMultiLangModel->reset()->where('model', 'pjProduct')->whereIn('foreign_id', $record)->eraseAll();

		/* DELETE STOCK */
		pjStockModel::factory()->reset()->whereIn('product_id', $record)->eraseAll();

		/* DELETE CART */
		pjCartModel::factory()->reset()->whereIn('product_id', $record)->eraseAll();

		/* DELETE STOCK ATTR */
		$this->pjActionDeleteStockAttr($record);

		/* DELETE SIMILAR PRODUCTS */
		pjProductSimilarModel::factory()
			->reset()
			->whereIn('product_id', $record)
			->orWhereIn('similar_id', $record)
			->eraseAll();

		/* DELETE EXTRAS */
		$extra_arr = $pjExtraModel->reset()->whereIn('product_id', $record)->findAll()->getDataPair(null, 'id');

		if (!empty($extra_arr)) {

			$pjExtraItemModel = pjExtraItemModel::factory();

			/* FIX: delete only related extras */
			$pjExtraModel->reset()->whereIn('product_id', $record)->eraseAll();

			$pjMultiLangModel->reset()->where('model', 'pjExtra')->whereIn('foreign_id', $extra_arr)->eraseAll();

			$extra_item_arr = $pjExtraItemModel->reset()->whereIn('extra_id', $extra_arr)->findAll()->getDataPair(NULL, 'id');

			if (!empty($extra_item_arr)) {

				$pjExtraItemModel->reset()->whereIn('extra_id', $extra_arr)->eraseAll();

				$pjMultiLangModel->reset()->where('model', 'pjExtraItem')->whereIn('foreign_id', $extra_item_arr)->eraseAll();
			}
		}

		/* DELETE PRODUCT ATTRIBUTES */
		$this->pjActionDeleteProductAttr($record);

		/* DELETE CATEGORY RELATIONS */
		pjProductCategoryModel::factory()->reset()->whereIn('product_id', $record)->eraseAll();

		/* DELETE BRAND RELATIONS */
		pjProductBrandModel::factory()->reset()->whereIn('product_id', $record)->eraseAll();

		/* DELETE GALLERY */
		$pjGalleryModel = pjGalleryModel::factory();
		$image_arr = $pjGalleryModel->reset()->whereIn('foreign_id', $record)->findAll()->getData();

		if (!empty($image_arr)) {

			$pjGalleryModel->reset()->whereIn('foreign_id', $record)->eraseAll();

			foreach ($image_arr as $image) {

				@clearstatcache();

				if (!empty($image['small_path']) && is_file($image['small_path'])) {
					@unlink($image['small_path']);
				}

				if (!empty($image['medium_path']) && is_file($image['medium_path'])) {
					@unlink($image['medium_path']);
				}

				if (!empty($image['large_path']) && is_file($image['large_path'])) {
					@unlink($image['large_path']);
				}

				if (!empty($image['source_path']) && is_file($image['source_path'])) {
					@unlink($image['source_path']);
				}
			}
		}

		self::jsonResponse(array('status' => 'OK'));
	}
	public function pjActionDeleteSimilar()
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
		if (pjProductSimilarModel::factory()->set('id', $id)->erase()->getAffectedRows() == 1) {
			self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Similar product has been deleted.'));
		} else {
			self::jsonResponse(array('status' => 'ERR', 'code' => 105, 'text' => 'Similar product has not been deleted.'));
		}
		exit;
	}

	public function pjActionDeleteSimilarBulk()
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

		if (pjProductSimilarModel::factory()->whereIn('id', $record)->eraseAll()->getAffectedRows() > 0) {
			pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Similar products has been deleted.'));
		}

		self::jsonResponse(array('status' => 'OK'));
	}

	public function pjActionGetProduct()
	{
		$this->setAjax(true);
		$default_company = $_SESSION[$this->defaultCompany];

		if ($this->isXHR()) {
			$pjProductModel = pjProductModel::factory()
				->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.id AND t2.locale='" . $this->getLocaleId() . "' AND t2.field='name'", 'left outer')
				->join('pjMultiLang', "t3.model='pjProduct' AND t3.foreign_id=t1.id AND t3.locale='" . $this->getLocaleId() . "' AND t3.field='short_desc'", 'left outer')
				->join('pjMultiLang', "t4.model='pjProduct' AND t4.foreign_id=t1.id AND t4.locale='" . $this->getLocaleId() . "' AND t4.field='full_desc'", 'left outer')
				->where('t1.company_id', $default_company['id']);

			if ($q = $this->_get->toString('q')) {
				$q = str_replace(array('%', '_'), array('\%', '\_'), trim($q));
				$pjProductModel->where('(t2.content LIKE "%' . $q . '%" OR t3.content LIKE "%' . $q . '%" OR t4.content LIKE "%' . $q . '%" OR t1.sku LIKE "%' . $q . '%")');
			}

			if ($this->_get->check('status') && $this->_get->toString('status') != '' && in_array($this->_get->toString('status'), array(1, 0))) {
				$pjProductModel->where('t1.status', $this->_get->toString('status'));
			}

			if ($this->_get->check('name') && $this->_get->toString('name') != '') {
				$q = str_replace(array('%', '_'), array('\%', '\_'), $this->_get->toString('name'));
				$pjProductModel->where('t2.content LIKE', "%$q%");
			}

			if ($this->_get->check('sku') && $this->_get->toString('sku') != '') {
				$q = str_replace(array('%', '_'), array('\%', '\_'), $this->_get->toString('sku'));
				$pjProductModel->where('t1.sku LIKE', "%$q%");
			}

			if ($this->_get->check('category_id') && $this->_get->toInt('category_id') > 0) {
				$pjProductModel->where(sprintf(
					"t1.id IN (SELECT `product_id` FROM `%s` WHERE `category_id` = '%u')",
					pjProductCategoryModel::factory()->getTable(),
					$this->_get->toInt('category_id')
				));
			}

			if ($this->_get->check('is_digital')) {
				$pjProductModel->where('t1.is_digital', 1);
			}

			if ($this->_get->check('is_featured')) {
				$pjProductModel->where('t1.is_featured', 1);
			}
			if ($this->_get->check('is_out') && $this->_get->toString('is_out') != '') {
				$pjProductModel->where("(t1.id NOT IN(SELECT TS.product_id FROM `" . pjStockModel::factory()->getTable() . "` AS TS GROUP BY TS.product_id HAVING SUM(TS.qty) > 0))");
			}
			if ($this->_get->check('is_active_out') && $this->_get->toString('is_active_out') != '') {
				$pjProductModel->where("(t1.status = 1 AND t1.id NOT IN(SELECT TS.product_id FROM `" . pjStockModel::factory()->getTable() . "` AS TS GROUP BY TS.product_id HAVING SUM(TS.qty) > 0))");
			}

			$column = 'name';
			$direction = 'ASC';
			if ($this->_get->check('direction') && $this->_get->check('column') && in_array(strtoupper($this->_get->toString('direction')), array('ASC', 'DESC'))) {
				$column = $this->_get->toString('column');
				$direction = strtoupper($this->_get->toString('direction'));
			}

			$total = $pjProductModel->findCount()->getData();
			$rowCount = $this->_get->check('rowCount') && $this->_get->toInt('rowCount') > 0 ? $this->_get->toInt('rowCount') : 10;
			$pages = ceil($total / $rowCount);
			$page = $this->_get->check('page') && $this->_get->toInt('page') > 0 ? $this->_get->toInt('page') : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages) {
				$page = $pages;
			}

			$data = $pjProductModel
				->select(sprintf("t1.*, t2.content AS name,
					(SELECT `small_path` FROM `%1\$s`
						WHERE `foreign_id` = `t1`.`id`
						ORDER BY `sort` ASC
						LIMIT 1) AS `pic`,
					(SELECT COALESCE(SUM(`qty`), 0) FROM `%2\$s` WHERE `product_id` = `t1`.`id` LIMIT 1) AS `total_stock`,
					(SELECT MIN(`price`) FROM `%2\$s` WHERE `product_id` = `t1`.`id` LIMIT 1) AS `min_price`,
					(SELECT COUNT(`id`) FROM `%2\$s` WHERE `product_id` = `t1`.`id` LIMIT 1) AS `cnt_stock`,
					(SELECT COUNT(DISTINCT `order_id`) FROM `%3\$s` WHERE `product_id` = `t1`.`id` LIMIT 1) AS `cnt_orders`
				", pjGalleryModel::factory()->getTable(), pjStockModel::factory()->getTable(), pjOrderStockModel::factory()->getTable()))
				->orderBy("$column $direction")->limit($rowCount, $offset)->findAll()->getData();

			foreach ($data as $k => $v) {
				$data[$k]['name'] = pjSanitize::clean($v['name']);
				$data[$k]['min_price_format'] = pjCurrency::formatPrice($v['min_price']);
				if ($v['cnt_stock'] > 1) {
					$data[$k]['min_price_format'] = __('front_price_from', true) . " " . $data[$k]['min_price_format'];
				}
			}

			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}

	public function pjActionGetStock()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			$default_company = $_SESSION[$this->defaultCompany];

			$pjStockModel = pjStockModel::factory()
				->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.product_id AND t2.locale='" . $this->getLocaleId() . "' AND t2.field='name'", 'left outer')
				->join('pjProduct', 't3.id=t1.product_id', 'left outer')
				->where('t1.company_id', $default_company['id']);

			if ($this->_get->check('q') && $this->_get->toString('q') != '') {
				$q = trim($this->_get->toString('q'));
				$q = str_replace(array('%', '_'), array('\%', '\_'), $q);
				$pjStockModel->where('t2.content LIKE', "%$q%");
				$pjStockModel->orWhere('t3.sku LIKE', "%$q%");
			}

			$column = 'name';
			$direction = 'ASC';
			if ($this->_get->check('direction') && $this->_get->check('column') && in_array(strtoupper($this->_get->toString('direction')), array('ASC', 'DESC'))) {
				$column = $this->_get->toString('column');
				$direction = strtoupper($this->_get->toString('direction'));
			}

			$total = $pjStockModel->findCount()->getData();
			$rowCount = $this->_get->check('rowCount') && $this->_get->toInt('rowCount') > 0 ? $this->_get->toInt('rowCount') : 10;
			$pages = ceil($total / $rowCount);
			$page = $this->_get->check('page') && $this->_get->toInt('page') > 0 ? $this->_get->toInt('page') : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages) {
				$page = $pages;
			}

			$data = $pjStockModel
				->select(sprintf("t1.*, t2.content AS name,
					(SELECT `small_path` FROM `%1\$s`
						WHERE `id` = `t1`.`image_id`
						LIMIT 1) AS `pic`,
					(SELECT GROUP_CONCAT(CONCAT_WS('~:~', `tm2`.`content`, `tm1`.`content`) SEPARATOR '~|~')
						FROM `%2\$s` AS `tsa`
						LEFT JOIN `%3\$s` AS `tm1` ON `tm1`.`model` = 'pjAttribute' AND `tm1`.`foreign_id` = `tsa`.`attribute_id` AND `tm1`.`field` = 'name' AND `tm1`.`locale` = '%4\$u'
						LEFT JOIN `%3\$s` AS `tm2` ON `tm2`.`model` = 'pjAttribute' AND `tm2`.`foreign_id` = `tsa`.`attribute_parent_id` AND `tm2`.`field` = 'name' AND `tm2`.`locale` = '%4\$u'
						WHERE `tsa`.`product_id` = `t1`.`product_id`
						AND `tsa`.`stock_id` = `t1`.`id`
						LIMIT 1) AS `stock_attr`
				", pjGalleryModel::factory()->getTable(), pjStockAttributeModel::factory()->getTable(), pjMultiLangModel::factory()->getTable(), $this->getLocaleId()))
				->orderBy("$column $direction")
				->limit($rowCount, $offset)
				->findAll()
				->toArray('stock_attr', '~|~')
				->getData();

			foreach ($data as $k => $v) {
				$data[$k]['price_formated'] = pjCurrency::formatPrice($v['price']);
			}

			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}

	public function pjActionDeleteExtra()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			$resp = array('status' => 'ERR');
			if (pjExtraModel::factory()->set('id', $this->_post->toInt('id'))->erase()->getAffectedRows() == 1) {
				$pjMultiLangModel = pjMultiLangModel::factory();
				$pjExtraItemModel = pjExtraItemModel::factory();

				$pjMultiLangModel->reset()
					->where('model', 'pjExtra')
					->where('foreign_id', $this->_post->toInt('id'))
					->eraseAll();

				$extra_item = $pjExtraItemModel->where('extra_id', $this->_post->toInt('id'))->findAll()->getDataPair(NULL, 'id');
				if (!empty($extra_item)) {
					$pjMultiLangModel->reset()
						->where('model', 'pjExtraItem')
						->whereIn('foreign_id', $extra_item)
						->eraseAll();

					$pjExtraItemModel->reset()->where('extra_id', $this->_post->toInt('id'))->eraseAll();
				}
				$resp['status'] = 'OK';
			}
			pjAppController::jsonResponse($resp);
		}
		exit;
	}
	public function pjActionDeleteStock()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {

			$resp = array('status' => 'ERR');

			/* GET ID FROM POST OR GET */

			$id = $this->_post->toInt('id');

			if ($id <= 0) {
				$id = $this->_get->toInt('id');
			}

			if ($id > 0) {

				if (pjStockModel::factory()->set('id', $id)->erase()->getAffectedRows() == 1) {

					pjStockAttributeModel::factory()
						->where('stock_id', $id)
						->eraseAll();

					$resp['status'] = 'OK';
				}
			}

			pjAppController::jsonResponse($resp);
		}
	}

	public function pjActionDeleteDigital()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			$pjProductModel = pjProductModel::factory();

			$arr = $pjProductModel->find($this->_post->toInt('id'))->getData();
			if (count($arr) > 0) {
				if (
					$pjProductModel
					->reset()
					->set('id', $arr['id'])
					->modify(array(
						'digital_file' => ':NULL',
						'digital_name' => ':NULL'
					))->getAffectedRows() == 1
				) {
					@unlink($arr['digital_file']);
				}
				$this->set('arr', $arr);
			}
		}
	}

	public function pjActionExportProduct()
	{
		$this->checkLogin();

		$record = $this->_post->toArray('record');
		if (!empty($record)) {
			$arr = $this->buildProductExportRows($record);
			if (!empty($arr)) {
				$csv = new pjCSV();
				$csv
					->setHeader(true)
					->setName("Products-" . time() . ".csv")
					->process($arr)
					->download();
			}
		}
		exit;
	}

	/**
	 * One CSV row per stock variant (matches import / flat-file columns).
	 */
	private function buildProductExportRows(array $productIds)
	{
		$productIds = array_filter(array_map('intval', $productIds));
		if (empty($productIds)) {
			return array();
		}

		$locale_id = (int) $this->getLocaleId();
		$company_id = isset($_SESSION[$this->defaultCompany]['id']) ? (int) $_SESSION[$this->defaultCompany]['id'] : 0;

		$rows = pjStockModel::factory()
			->select("
				t1.id AS stock_id,
				t1.product_id,
				t1.article_number,
				t1.article_name,
				t1.ean,
				t1.qty,
				t1.price,
				t1.status AS stock_status,
				t2.sku,
				t2.model,
				t2.model_name,
				t2.status AS product_status,
				t2.is_featured,
				t2.is_digital,
				t3.small_path AS image,
				t4.content AS name_en,
				t5.content AS short_desc_en,
				t6.content AS full_description_en
			")
			->join('pjProduct', 't2.id = t1.product_id', 'inner')
			->join('pjGallery', 't3.id = t1.image_id', 'left')
			->join('pjMultiLang', "t4.model='pjProduct' AND t4.foreign_id=t2.id AND t4.field='name' AND t4.locale='$locale_id'", 'left outer')
			->join('pjMultiLang', "t5.model='pjProduct' AND t5.foreign_id=t2.id AND t5.field='short_desc' AND t5.locale='$locale_id'", 'left outer')
			->join('pjMultiLang', "t6.model='pjProduct' AND t6.foreign_id=t2.id AND t6.field='full_desc' AND t6.locale='$locale_id'", 'left outer')
			->whereIn('t2.id', $productIds)
			->orderBy('t2.id ASC, t1.id ASC');

		if ($company_id > 0) {
			$rows->where('t1.company_id', $company_id);
		}

		$data = $rows->findAll()->getData();
		$export = array();
		$exportedProductIds = array();

		foreach ($data as $row) {
			$exportedProductIds[] = (int) $row['product_id'];
			$product_id = (int) $row['product_id'];
			$stock_id = (int) $row['stock_id'];

			$brand = pjProductBrandModel::factory()
				->select('t3.content AS brand_name')
				->join('pjBrand', 't2.id=t1.brand_id', 'left')
				->join('pjMultiLang', "t3.model='pjBrand' AND t3.foreign_id=t2.id AND t3.field='name' AND t3.locale='$locale_id'", 'left outer')
				->where('t1.product_id', $product_id)
				->limit(1)
				->findAll()
				->getData();

			$category = pjProductCategoryModel::factory()
				->select('t3.content AS category_name')
				->join('pjCategory', 't2.id=t1.category_id', 'left')
				->join('pjMultiLang', "t3.model='pjCategory' AND t3.foreign_id=t2.id AND t3.field='name' AND t3.locale='$locale_id'", 'left outer')
				->where('t1.product_id', $product_id)
				->limit(1)
				->findAll()
				->getData();

			$attrs = pjStockAttributeModel::factory()
				->select('t3.content')
				->join('pjAttribute', 't2.id=t1.attribute_id', 'left')
				->join('pjMultiLang', "t3.model='pjAttribute' AND t3.foreign_id=t2.id AND t3.field='name' AND t3.locale='$locale_id'", 'left outer')
				->where('t1.stock_id', $stock_id)
				->findAll()
				->getData();

			$size = '';
			$color = '';
			foreach ($attrs as $attr) {
				if ($size === '') {
					$size = $attr['content'];
				} else {
					$color = $attr['content'];
				}
			}

			$image = $row['image'];
			if (!empty($image) && strpos($image, 'http') !== 0) {
				$image = PJ_INSTALL_URL . ltrim($image, '/');
			}

			$export[] = array(
				'product_id' => $product_id,
				'stock_id' => $stock_id,
				'model' => $row['model'],
				'model_name' => $row['model_name'],
				'sku' => $row['sku'],
				'status' => (int) $row['product_status'],
				'stock_status' => ($row['stock_status'] === 'T' || $row['stock_status'] === 1 || $row['stock_status'] === '1') ? 1 : 0,
				'is_featured' => (int) $row['is_featured'],
				'is_digital' => (int) $row['is_digital'],
				'brand' => !empty($brand) ? $brand[0]['brand_name'] : '',
				'category' => !empty($category) ? $category[0]['category_name'] : '',
				'article_number' => $row['article_number'],
				'article_name' => $row['article_name'],
				'ean' => $row['ean'],
				'qty' => $row['qty'],
				'price' => $row['price'],
				'size' => $size,
				'color' => $color,
				'name_en' => $row['name_en'],
				'short_desc_en' => $row['short_desc_en'],
				'full_description_en' => $row['full_description_en'],
				'image' => $image,
			);
		}

		$missingProductIds = array_diff($productIds, array_unique($exportedProductIds));
		if (!empty($missingProductIds)) {
			$productsOnly = pjProductModel::factory()
				->select('t1.id AS product_id, t1.sku, t1.model, t1.model_name, t1.status AS product_status, t1.is_featured, t1.is_digital, t2.content AS name_en, t3.content AS short_desc_en, t4.content AS full_description_en')
				->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='$locale_id'", 'left outer')
				->join('pjMultiLang', "t3.model='pjProduct' AND t3.foreign_id=t1.id AND t3.field='short_desc' AND t3.locale='$locale_id'", 'left outer')
				->join('pjMultiLang', "t4.model='pjProduct' AND t4.foreign_id=t1.id AND t4.field='full_desc' AND t4.locale='$locale_id'", 'left outer')
				->whereIn('t1.id', $missingProductIds)
				->findAll()
				->getData();

			foreach ($productsOnly as $product) {
				$product_id = (int) $product['product_id'];

				$brand = pjProductBrandModel::factory()
					->select('t3.content AS brand_name')
					->join('pjBrand', 't2.id=t1.brand_id', 'left')
					->join('pjMultiLang', "t3.model='pjBrand' AND t3.foreign_id=t2.id AND t3.field='name' AND t3.locale='$locale_id'", 'left outer')
					->where('t1.product_id', $product_id)
					->limit(1)
					->findAll()
					->getData();

				$category = pjProductCategoryModel::factory()
					->select('t3.content AS category_name')
					->join('pjCategory', 't2.id=t1.category_id', 'left')
					->join('pjMultiLang', "t3.model='pjCategory' AND t3.foreign_id=t2.id AND t3.field='name' AND t3.locale='$locale_id'", 'left outer')
					->where('t1.product_id', $product_id)
					->limit(1)
					->findAll()
					->getData();

				$export[] = array(
					'product_id' => $product_id,
					'stock_id' => '',
					'model' => $product['model'],
					'model_name' => $product['model_name'],
					'sku' => $product['sku'],
					'status' => (int) $product['product_status'],
					'stock_status' => '',
					'is_featured' => (int) $product['is_featured'],
					'is_digital' => (int) $product['is_digital'],
					'brand' => !empty($brand) ? $brand[0]['brand_name'] : '',
					'category' => !empty($category) ? $category[0]['category_name'] : '',
					'article_number' => '',
					'article_name' => '',
					'ean' => '',
					'qty' => '',
					'price' => '',
					'size' => '',
					'color' => '',
					'name_en' => $product['name_en'],
					'short_desc_en' => $product['short_desc_en'],
					'full_description_en' => $product['full_description_en'],
					'image' => '',
				);
			}
		}

		return $export;
	}

	public function pjActionExtraCopy()
	{
		$this->setAjax(true);

		if ($this->isXHR() && $this->isLoged()) {
			$default_company = $_SESSION[$this->defaultCompany];

			if (
				$this->_post->check('from_product_id') && $this->_post->toInt('from_product_id') > 0 &&
				$this->_post->check('product_id') && $this->_post->toInt('product_id') > 0
			) {
				$pjExtraModel = pjExtraModel::factory();
				$pjExtraItemModel = pjExtraItemModel::factory();
				$pjMultiLangModel = pjMultiLangModel::factory();

				$extras = $pjExtraModel->where('t1.company_id', $default_company['id'])
					->where('t1.product_id', $this->_post->toInt('from_product_id'))->findAll()->getData();
				$extras_items = $pjExtraItemModel
					->where(sprintf("t1.extra_id IN (SELECT `id` FROM `%s` WHERE `product_id` = '%u')", $pjExtraModel->getTable(), $this->_post->toInt('from_product_id')))
					->where('t1.company_id', $default_company['id'])
					->findAll()
					->getData();

				foreach ($extras as $k => $extra) {
					$extras[$k]['items'] = array();
					if ($extra['type'] == 'multi') {
						foreach ($extras_items as $key => $item) {
							if ($item['extra_id'] == $extra['id']) {
								$extras[$k]['items'][] = $item;
							}
						}
					}
				}

				$query = sprintf(
					"INSERT INTO `%1\$s` (`foreign_id`, `model`, `locale`, `field`, `content`, `source`)
						SELECT :foreign_id, `model`, `locale`, `field`, `content`, `source`
						FROM `%1\$s`
						WHERE `foreign_id` = :fid
						AND `model` = :model",
					$pjMultiLangModel->getTable()
				);

				foreach ($extras as $extra) {
					$extra_id = $pjExtraModel->reset()->setAttributes(array(
						'product_id' => $this->_post->toInt('product_id'),
						'type' => $extra['type'],
						'company_id' => $default_company['id'],
						'price' => $extra['price'],
						'is_mandatory' => $extra['is_mandatory']
					))->insert()->getInsertId();
					if ($extra_id !== FALSE && (int) $extra_id > 0) {
						$pjMultiLangModel->reset()->prepare($query)->exec(array(
							'foreign_id' => $extra_id,
							'fid' => $extra['id'],
							'model' => 'pjExtra'
						));

						if ($extra['type'] == 'multi' && isset($extra['items']) && !empty($extra['items'])) {
							foreach ($extra['items'] as $item) {
								$extra_item_id = $pjExtraItemModel->reset()->setAttributes(array(
									'extra_id' => $extra_id,
									'price' => $item['price'],
									'company_id' => $default_company['id']
								))->insert()->getInsertId();
								if ($extra_item_id !== FALSE && (int) $extra_item_id > 0) {
									$pjMultiLangModel->reset()->prepare($query)->exec(array(
										'foreign_id' => $extra_item_id,
										'fid' => $item['id'],
										'model' => 'pjExtraItem'
									));
								}
							}
						}
					}
				}

				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Extras has been copied'));
			}
		}
		exit;
	}

	public function pjActionGetAttributes()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			$default_company = $_SESSION[$this->defaultCompany];

			if ($this->_get->check('product_id') && $this->_get->toInt('product_id') > 0) {
				$wk = 't1.product_id';
				$wv = $this->_get->toInt('product_id');
			} elseif ($this->_get->check('hash') && $this->_get->toString('hash') != '') {
				$wk = 't1.hash';
				$wv = $this->_get->toString('hash');
			}

			$attr_arr = array();
			$a_arr = pjAttributeModel::factory()
				->select('t1.id, t1.product_id, t1.parent_id, t1.hash, t2.content AS name')
				->join('pjMultiLang', "t2.model='pjAttribute' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='" . $this->getLocaleId() . "'", 'left outer')
				->where($wk, $wv)
				->where('t1.company_id', $default_company['id'])
				->orderBy('t1.`order_group` ASC, `order_item` ASC')
				->findAll()
				->getData();

			$pjMultiLangModel = pjMultiLangModel::factory();

			foreach ($a_arr as $attr) {
				$attr['i18n'] = $pjMultiLangModel->reset()->getMultiLang($attr['id'], 'pjAttribute');
				if ((int) $attr['parent_id'] === 0) {
					$attr_arr[$attr['id']] = $attr;
				} else {
					if (!isset($attr_arr[$attr['parent_id']]['child'])) {
						$attr_arr[$attr['parent_id']]['child'] = array();
					}
					$attr_arr[$attr['parent_id']]['child'][] = $attr;
				}
			}
			$this->set('attr_arr', array_values($attr_arr));

			$this->setLocalesData();
		}
	}

	public function pjActionGetExtras()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			if (!$this->_get->check('product_id') || $this->_get->toInt('product_id') <= 0) {
				return;
			}
			$default_company = $_SESSION[$this->defaultCompany];

			$extra_arr = pjExtraModel::factory()->where('t1.company_id', $default_company['id'])
				->where('t1.product_id', $this->_get->toInt('product_id'))->findAll()->getData();
			$pjExtraItemModel = pjExtraItemModel::factory();
			$pjMultiLangModel = pjMultiLangModel::factory();
			foreach ($extra_arr as $k => $extra) {
				$extra_arr[$k]['i18n'] = $pjMultiLangModel->reset()->getMultiLang($extra['id'], 'pjExtra');
				$extra_arr[$k]['extra_items'] = $pjExtraItemModel->reset()->where('t1.extra_id', $extra['id'])->orderBy('t1.price ASC')->findAll()->getData();
				foreach ($extra_arr[$k]['extra_items'] as $key => $val) {
					$extra_arr[$k]['extra_items'][$key]['i18n'] = $pjMultiLangModel->reset()->getMultiLang($val['id'], 'pjExtraItem');
				}
			}
			$this->set('extra_arr', $extra_arr);

			$this->setLocalesData();
		}
	}

	public function pjActionGetHistory()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			$default_company = $_SESSION[$this->defaultCompany];
			$product = pjProductModel::factory()->find($this->_get->toInt('id'))->getData();

			$history_arr = pjHistoryModel::factory()
				->select("t1.*, t2.id AS sid, t3.content AS name,
						  (		SELECT GROUP_CONCAT(CONCAT_WS(': ', TML2.content, TML1.content) SEPARATOR '&nbsp;|&nbsp;') 
								FROM `" . pjStockAttributeModel::factory()->getTable() . "` AS `TSA`
									LEFT OUTER JOIN `" . pjMultiLangModel::factory()->getTable() . "` AS TML1 ON (TML1.model='pjAttribute' AND TML1.foreign_id=TSA.attribute_id AND TML1.field='name' AND TML1.locale='" . $this->getLocaleId() . "') 
									LEFT OUTER JOIN `" . pjMultiLangModel::factory()->getTable() . "` AS TML2 ON (TML2.model='pjAttribute' AND TML2.foreign_id=TSA.attribute_parent_id AND TML2.field='name' AND TML2.locale='" . $this->getLocaleId() . "')
								WHERE t1.record_id=TSA.stock_id) AS attributes	
						 ")
				->join('pjStock', 't2.id=t1.record_id', 'left outer')
				->join('pjMultiLang', "t3.model='pjProduct' AND t3.foreign_id=t2.product_id AND t3.locale='" . $this->getLocaleId() . "' AND t3.field='name'", 'left outer')
				->where('t1.table_name', pjStockModel::factory()->getTable())
				->where('t2.product_id', $this->_get->toInt('id'))
				->where('t1.company_id', $default_company['id'])
				->orderBy('t1.created ASC')
				->findAll()
				->getData();

			$this->set('product', $product);
			$this->set('history_arr', $history_arr);
		}
	}

	public function pjActionGetProducts()
	{
		$this->setAjax(true);

		if ($this->isXHR() && $this->isLoged()) {
			$default_company = $_SESSION[$this->defaultCompany];

			$pjProductModel = pjProductModel::factory();
			if ($this->_get->check('product_id') && $this->_get->toInt('product_id') > 0) {
				$pjProductModel->where('t1.id !=', $this->_get->toInt('product_id'));
			}
			if ($this->_get->check('copy')) {
				switch ($this->_get->toString('copy')) {
					case 'Attr':
						$pjProductModel->where(sprintf("t1.id IN (SELECT `product_id` FROM `%s` WHERE 1)", pjAttributeModel::factory()->getTable()));
						break;
					case 'Extra':
						$pjProductModel->where(sprintf("t1.id IN (SELECT `product_id` FROM `%s` WHERE 1)", pjExtraModel::factory()->getTable()));
						break;
				}
			}
			$this->set('arr', $pjProductModel
				->select('t1.*, t2.content AS name')
				->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.id AND t2.locale='" . $this->getLocaleId() . "' AND t2.field='name'", 'left outer')
				->where('t1.company_id', $default_company['id'])
				->orderBy('name ASC')->findAll()->getData());
		}
	}

	public function pjActionGetSimilar()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			$default_company = $_SESSION[$this->defaultCompany];

			if ($this->_get->check('id') && $this->_get->toInt('id') > 0) {
				$pjProductSimilarModel = pjProductSimilarModel::factory()
					->join('pjProduct', 't2.id=t1.similar_id', 'inner')
					->join('pjMultiLang', "t3.model='pjProduct' AND t3.foreign_id=t2.id AND t3.locale='" . $this->getLocaleId() . "' AND t3.field='name'", 'left outer')
					->where('t1.product_id', $this->_get->toInt('id'))
					->where('t1.company_id', $default_company['id']);

				$column = 'name';
				$direction = 'ASC';
				if ($this->_get->check('direction') && $this->_get->check('column') && in_array(strtoupper($this->_get->toString('direction')), array('ASC', 'DESC'))) {
					$column = $this->_get->toString('column');
					$direction = strtoupper($this->_get->toString('direction'));
				}

				$total = $pjProductSimilarModel->findCount()->getData();
				$rowCount = $this->_get->check('rowCount') && $this->_get->toInt('rowCount') > 0 ? $this->_get->toInt('rowCount') : 10;
				$pages = ceil($total / $rowCount);
				$page = $this->_get->check('page') && $this->_get->toInt('page') > 0 ? $this->_get->toInt('page') : 1;
				$offset = ((int) $page - 1) * $rowCount;
				if ($page > $pages) {
					$page = $pages;
				}

				$data = $pjProductSimilarModel
					->select('t1.id, t1.similar_id, t2.sku, t2.status, t3.content AS name')
					->orderBy("$column $direction")
					->findAll()
					->getData();

				pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
			}
		}
		exit;
	}

	public function pjActionSearchProducts()
	{
		$this->setAjax(true);
		$default_company = $_SESSION[$this->defaultCompany];

		$pjProductModel = pjProductModel::factory();

		if ($this->_get->check('query')) {
			$q = $pjProductModel->escapeStr($this->_get->toString('query'));
			$q = str_replace(array('%', '_'), array('\%', '\_'), $q);
			$pjProductModel->where("(t1.id LIKE '%$q%' OR t1.sku LIKE '%$q%' OR t1.id IN (SELECT `foreign_id` FROM `" . pjMultiLangModel::factory()->getTable() . "`
				WHERE `field` = 'name'
				AND `model` = 'pjProduct'
				AND `content` LIKE '%$q%'))");
		}
		$arr = $pjProductModel
			->select('t1.*, t2.content AS name')
			->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='" . $this->getLocaleId() . "'", 'left outer')
			->where('t1.id !=', $this->_get->toInt('id'))
			->where(sprintf("t1.id NOT IN (SELECT `similar_id` FROM `%s` WHERE `product_id` = '%u')", pjProductSimilarModel::factory()->getTable(), $this->_get->toInt('id')))
			->where('t1.company_id', $default_company['id'])
			->orderBy('`name` ASC')
			->limit($this->_get->toInt('limit'))->findAll()->getData();

		$_arr = array();
		foreach ($arr as $k => $v) {
			$_arr[$k]['id'] = $v['id'];
			$_arr[$k]['label'] = $v['name'];
		}
		pjAppController::jsonResponse($_arr);
	}

	public function pjActionStock()
	{
		$this->checkLogin();
		if (!pjAuth::factory()->hasAccess()) {
			$this->sendForbidden();
			return;
		}

		$this->set('has_update', pjAuth::factory('pjAdminProducts', 'pjActionUpdate')->hasAccess());

		$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
		$this->appendJs('pjAdminProducts.js');
	}

	public function pjActionPrintStock()
	{
		$this->setLayout('pjActionPrint');
		$default_company = $_SESSION[$this->defaultCompany];

		$pjStockModel = pjStockModel::factory();
		if ($record = $this->_post->toArray('record')) {
			$pjStockModel->whereIn('t1.id', $record);
		}

		$arr = $pjStockModel
			->select(sprintf("t1.*, t2.content AS name,
				(SELECT `small_path` FROM `%1\$s`
					WHERE `id` = `t1`.`image_id`
					LIMIT 1) AS `pic`,
				(SELECT GROUP_CONCAT(CONCAT_WS('~:~', `tm2`.`content`, `tm1`.`content`) SEPARATOR '~|~')
					FROM `%2\$s` AS `tsa`
					LEFT JOIN `%3\$s` AS `tm1` ON `tm1`.`model` = 'pjAttribute' AND `tm1`.`foreign_id` = `tsa`.`attribute_id` AND `tm1`.`field` = 'name' AND `tm1`.`locale` = '%4\$u'
					LEFT JOIN `%3\$s` AS `tm2` ON `tm2`.`model` = 'pjAttribute' AND `tm2`.`foreign_id` = `tsa`.`attribute_parent_id` AND `tm2`.`field` = 'name' AND `tm2`.`locale` = '%4\$u'
					WHERE `tsa`.`product_id` = `t1`.`product_id`
					AND `tsa`.`stock_id` = `t1`.`id`
					LIMIT 1) AS `stock_attr`
			", pjGalleryModel::factory()->getTable(), pjStockAttributeModel::factory()->getTable(), pjMultiLangModel::factory()->getTable(), $this->getLocaleId()))
			->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.product_id AND t2.locale='" . $this->getLocaleId() . "' AND t2.field='name'", 'left outer')
			->join('pjProduct', 't3.id=t1.product_id', 'left outer')
			->where('t1.company_id', $default_company['id'])
			->orderBy("`name` ASC")
			->findAll()
			->toArray('stock_attr', '~|~')
			->getData();
		$this->set('arr', $arr);
		$this->resetCss()->appendCss('print.css');
	}

	public function pjActionAddSimilar()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			if ($this->_post->check('product_id') && $this->_post->check('similar_id') && $this->_post->toInt('product_id') > 0 && $this->_post->toInt('similar_id') > 0) {
				// 		$default_company = $_SESSION[$this->defaultCompany];
				$default_company_id = isset($_SESSION[$this->defaultCompany]['id'])
					? (int) $_SESSION[$this->defaultCompany]['id']
					: 1; // fallback to first company (optional)

				$data = $this->_post->raw();
				$data['company_id'] = $default_company_id;

				$insert_id = pjProductSimilarModel::factory()
					->setAttributes($data)
					->insert()
					->getInsertId();

				// $insert_id = pjProductSimilarModel::factory($this->_post->raw())->insert()->getInsertId();
				if ($insert_id !== FALSE && (int) $insert_id > 0) {
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Similar product has been added.'));
				}
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Similar product has not been added.'));
			} else {
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Missing parameters.'));
			}
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

		$this->set('category_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));

		$this->set('has_create', pjAuth::factory('pjAdminProducts', 'pjActionCreate')->hasAccess());
		$this->set('has_update', pjAuth::factory('pjAdminProducts', 'pjActionUpdate')->hasAccess());
		$this->set('has_delete', pjAuth::factory('pjAdminProducts', 'pjActionDeleteProduct')->hasAccess());
		$this->set('has_delete_bulk', pjAuth::factory('pjAdminProducts', 'pjActionDeleteProductBulk')->hasAccess());

		$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
		$this->appendJs('pjAdminProducts.js');
	}
	public function pjActionProductsFlatFileIndex()
	{
		$this->checkLogin();
		if (!pjAuth::factory()->hasAccess()) {
			$this->sendForbidden();
			return;
		}

		$this->set('category_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));

		$this->set('has_create', pjAuth::factory('pjAdminProducts', 'pjActionCreate')->hasAccess());
		$this->set('has_update', pjAuth::factory('pjAdminProducts', 'pjActionUpdate')->hasAccess());
		$this->set('has_delete', pjAuth::factory('pjAdminProducts', 'pjActionDeleteProduct')->hasAccess());
		$this->set('has_delete_bulk', pjAuth::factory('pjAdminProducts', 'pjActionDeleteProductBulk')->hasAccess());

		$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
		$this->appendJs('pjAdminProducts.js');
	}

	public function pjActionGetProductFlatfile()
	{
		$this->setAjax(true);

		if (!$this->isXHR()) {
			exit;
		}
		$this->checkLogin();
		if (!pjAuth::factory('pjAdminProducts', 'pjActionProductsFlatFileIndex')->hasAccess()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Access denied.'));
		}
		$default_company_id = $_SESSION[$this->defaultCompany]; // fallback to first company (optional)
		// echo "<pre>";
		// print_r($default_company_id['id']);
		// echo "</pre>"; die;
		$locale_id = $this->getLocaleId();

		$model = pjStockModel::factory()
			->select("
            t1.id,
            t1.product_id,
            t1.article_number,
            t1.article_name,
            t1.ean,
            t1.qty,
            t1.price,
            t2.model,
            t2.model_name,
            t2.sku,
            t1.status,
            t3.small_path AS image,
            t4.content AS name_en,
            t5.content AS short_desc_en,
            t6.content AS full_description_en
        ")
			->join('pjProduct', 't2.id = t1.product_id', 'inner')
			->join('pjGallery', 't3.id = t1.image_id', 'left')

			->join(
				'pjMultiLang',
				"t4.model='pjProduct' AND t4.foreign_id=t2.id AND t4.field='name' AND t4.locale='$locale_id'",
				'left outer'
			)

			->join(
				'pjMultiLang',
				"t5.model='pjProduct' AND t5.foreign_id=t2.id AND t5.field='short_desc' AND t5.locale='$locale_id'",
				'left outer'
			)

			->join(
				'pjMultiLang',
				"t6.model='pjProduct' AND t6.foreign_id=t2.id AND t6.field='full_desc' AND t6.locale='$locale_id'",
				'left outer'
			)->where('t1.company_id', $default_company_id['id']);

		/* SORTING */
		/* SEARCH */

		if ($this->_get->check('q') && $this->_get->toString('q') !== '') {
			$q = pjStockModel::factory()->escapeStr(trim($this->_get->toString('q')));
			$q = str_replace(array('%', '_'), array('\%', '\_'), $q);
			$model->where("
			(
				t2.model LIKE '%$q%' OR
				t2.model_name LIKE '%$q%' OR
				t2.sku LIKE '%$q%' OR
				t1.article_number LIKE '%$q%' OR
				t1.article_name LIKE '%$q%' OR
				t1.ean LIKE '%$q%' OR
				t4.content LIKE '%$q%' OR
				t5.content LIKE '%$q%' OR
				t6.content LIKE '%$q%'
			)
    		");
		}
		$column = 'id';
		$direction = 'DESC';

		if ($this->_get->check('column') && $this->_get->check('direction')) {
			$column = $this->_get->toString('column');
			$direction = $this->_get->toString('direction');
		}

		/* PAGINATION */

		$rowCount = $this->_get->toInt('rowCount');
		$page = $this->_get->toInt('page');

		if ($rowCount <= 0) {
			$rowCount = 10;
		}

		if ($page <= 0) {
			$page = 1;
		}

		$offset = ($page - 1) * $rowCount;

		/* TOTAL COUNT */

		$count_model = clone $model;
		$total = $count_model->findCount()->getData();

		/* DATA */

		$data = $model
			->orderBy("$column $direction")
			->limit($rowCount, $offset)
			->findAll()
			->getData();

		foreach ($data as $k => $row) {

			/* BRAND */

			$brand = pjProductBrandModel::factory()
				->select("t3.content AS brand_name")
				->join('pjBrand', 't2.id=t1.brand_id', 'left')
				->join(
					'pjMultiLang',
					"t3.model='pjBrand' AND t3.foreign_id=t2.id AND t3.field='name' AND t3.locale='$locale_id'",
					'left outer'
				)
				->where('t1.product_id', $row['product_id'])
				->limit(1)
				->findAll()
				->getData();

			$data[$k]['brand'] = !empty($brand) ? $brand[0]['brand_name'] : '';

			/* CATEGORY */

			$category = pjProductCategoryModel::factory()
				->select("t3.content AS category_name")
				->join('pjCategory', 't2.id=t1.category_id', 'left')
				->join(
					'pjMultiLang',
					"t3.model='pjCategory' AND t3.foreign_id=t2.id AND t3.field='name' AND t3.locale='$locale_id'",
					'left outer'
				)
				->where('t1.product_id', $row['product_id'])
				->limit(1)
				->findAll()
				->getData();

			$data[$k]['category'] = !empty($category) ? $category[0]['category_name'] : '';

			/* SIZE + COLOR */

			$attrs = pjStockAttributeModel::factory()
				->select("t3.content")
				->join('pjAttribute', 't2.id=t1.attribute_id', 'left')
				->join(
					'pjMultiLang',
					"t3.model='pjAttribute' AND t3.foreign_id=t2.id AND t3.field='name' AND t3.locale='$locale_id'",
					'left outer'
				)
				->where('t1.stock_id', $row['id'])
				->findAll()
				->getData();

			$size = '';
			$color = '';

			foreach ($attrs as $attr) {
				if (!$size) {
					$size = $attr['content'];
				} else {
					$color = $attr['content'];
				}
			}

			$data[$k]['size'] = $size;
			$data[$k]['color'] = $color;

			$data[$k]['sync_status'] = 'synced';
			// ✅ CONVERT DB STATUS (T/F) → UI (1/0)
			$data[$k]['status'] = ($row['status'] == 'T') ? '1' : '0';
		}
		$pages = ceil($total / $rowCount);

		pjAppController::jsonResponse(compact(
			'data',
			'total',
			'pages',
			'page',
			'rowCount',
			'column',
			'direction'
		));
	}
	// public function pjActionSaveFlatfileRow()
	// {
	// 	$this->setAjax(true);

	// 	// $this->writeLog("===== NEW SAVE REQUEST =====");

	// 	$id = $this->_get->toInt('id');
	// 	$column = $this->_post->toString('column');
	// 	$value  = $this->_post->toString('value');

	// 	// $this->writeLog("GET id: " . $id);
	// 	// $this->writeLog("POST column: " . $column);
	// 	// $this->writeLog("POST value: " . $value);

	// 	if ($id <= 0) {
	// 		// $this->writeLog("ERROR: Invalid ID");
	// 		self::jsonResponse(['status' => 'ERR', 'code' => 103, 'text' => 'Invalid ID.']);
	// 	}

	// 	$pjStockModel   = pjStockModel::factory();
	// 	$pjProductModel = pjProductModel::factory();

	// 	/* GET STOCK */

	// 	$stock = $pjStockModel
	// 		->reset()
	// 		->find($id)
	// 		->getData();

	// 	// $this->writeLog("Stock Row:");
	// 	// $this->writeLog($stock);

	// 	if (!$stock) {
	// 		// $this->writeLog("ERROR: Stock not found");
	// 		self::jsonResponse(['status' => 'ERR', 'code' => 104, 'text' => 'Stock not found.']);
	// 	}

	// 	$product_id = $stock['product_id'];

	// 	// $this->writeLog("Product ID: " . $product_id);

	// 	/* STOCK FIELDS */

	// 	$stock_fields = [
	// 		'article_number',
	// 		'article_name',
	// 		'ean',
	// 		'qty',
	// 		'price',
	// 		'status',

	// 	];

	// 	/* PRODUCT FIELDS */

	// 	$product_fields = [
	// 		'sku',
	// 		'model',
	// 		'model_name'
	// 	];

	// 	/* MULTILANG FIELDS */

	// 	$multilang_fields = [
	// 		'name_en',
	// 		'short_desc_en',
	// 		'full_description_en'
	// 	];

	// 	/* ================= STOCK UPDATE ================= */

	// 	if (in_array($column, $stock_fields)) {

	// 		// $this->writeLog("Updating STOCK column: " . $column);

	// 		$pjStockModel
	// 			->reset()
	// 			->where('id', $id)
	// 			->limit(1)
	// 			->modifyAll([$column => $value]);

	// 		// $this->writeLog("Stock updated");
	// 	}

	// 	/* ================= PRODUCT UPDATE ================= */ elseif (in_array($column, $product_fields)) {

	// 		// $this->writeLog("Updating PRODUCT column: " . $column);

	// 		$pjProductModel
	// 			->reset()
	// 			->where('id', $product_id)
	// 			->limit(1)
	// 			->modifyAll([$column => $value]);

	// 		// $this->writeLog("Product updated");
	// 	}

	// 	/* ================= MULTILANG UPDATE ================= */ elseif (in_array($column, $multilang_fields)) {

	// 		// $this->writeLog("Updating MULTILANG column: " . $column);

	// 		$field_map = [
	// 			'name_en'             => 'name',
	// 			'short_desc_en'       => 'short_desc',
	// 			'full_description_en' => 'full_desc'
	// 		];

	// 		pjMultiLangModel::factory()->updateMultiLang(
	// 			[
	// 				$this->getLocaleId() => [
	// 					$field_map[$column] => $value
	// 				]
	// 			],
	// 			$product_id,
	// 			'pjProduct',
	// 			'data'
	// 		);

	// 		// $this->writeLog("Multilang updated");
	// 	} elseif ($column == 'size' || $column == 'color') {

	// 		// $this->writeLog("Updating ATTRIBUTE: " . $column);

	// 		/* GET ALL STOCK ATTRIBUTES */

	// 		$attrs = pjStockAttributeModel::factory()
	// 			->where('stock_id', $id)
	// 			->findAll()
	// 			->getData();

	// 		if (!$attrs) {
	// 			// $this->writeLog("Stock attributes not found");
	// 			return;
	// 		}

	// 		foreach ($attrs as $attr) {

	// 			/* FIND ATTRIBUTE GROUP NAME */

	// 			$group = pjMultiLangModel::factory()
	// 				->where('model', 'pjAttribute')
	// 				->where('foreign_id', $attr['attribute_parent_id'])
	// 				->where('field', 'name')
	// 				->where('locale', $this->getLocaleId())
	// 				->limit(1)
	// 				->findAll()
	// 				->getData();

	// 			if (!$group) {
	// 				continue;
	// 			}

	// 			$group_name = strtolower($group[0]['content']);

	// 			if ($group_name == $column) {

	// 				// $this->writeLog("Matched group: " . $group_name);

	// 				$attr_id = $attr['attribute_id'];

	// 				// $this->writeLog("Updating attribute item id: " . $attr_id);

	// 				pjMultiLangModel::factory()
	// 					->reset()
	// 					->where('model', 'pjAttribute')
	// 					->where('foreign_id', $attr_id)
	// 					->where('field', 'name')
	// 					->where('locale', $this->getLocaleId())
	// 					->limit(1)
	// 					->modifyAll([
	// 						'content' => $value
	// 					]);

	// 				// $this->writeLog("Attribute value updated to: " . $value);
	// 			}
	// 		}
	// 	} else {

	// 		// $this->writeLog("ERROR: Invalid column received -> " . $column);

	// 		self::jsonResponse([
	// 			'status' => 'ERR',
	// 			'code' => 105,
	// 			'text' => 'Invalid column.'
	// 		]);
	// 	}

	// 	// $this->writeLog("SUCCESS: Row updated successfully");
	// 	// $this->writeLog("===== END REQUEST =====");

	// 	self::jsonResponse([
	// 		'status' => 'OK',
	// 		'code'   => 200,
	// 		'text'   => 'Row updated successfully.'
	// 	]);
	// }
	public function pjActionSaveFlatfileRow()
	{
		$this->setAjax(true);

		if (!$this->isXHR()) {
			exit;
		}
		$this->checkLogin();
		if (!pjAuth::factory('pjAdminProducts', 'pjActionUpdate')->hasAccess()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Access denied.'));
		}

		$id     = $this->_get->toInt('id');
		$column = $this->_post->toString('column');
		$value  = $this->_post->toString('value');

		// normalize column (handles status[651])
		$column = preg_replace('/\[\d+\]/', '', $column);

		if ($id <= 0) {
			self::jsonResponse(['status' => 'ERR', 'code' => 103, 'text' => 'Invalid ID.']);
		}

		$pjStockModel   = pjStockModel::factory();
		$pjProductModel = pjProductModel::factory();

		/* GET STOCK */
		$stock = $pjStockModel
			->reset()
			->find($id)
			->getData();

		if (!$stock) {
			self::jsonResponse(['status' => 'ERR', 'code' => 104, 'text' => 'Stock not found.']);
		}

		$company_id = isset($_SESSION[$this->defaultCompany]['id']) ? (int) $_SESSION[$this->defaultCompany]['id'] : 0;
		if ($company_id > 0 && (int) $stock['company_id'] !== $company_id) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Access denied.'));
		}

		$product_id = $stock['product_id'];

		/* STOCK FIELDS */
		$stock_fields = [
			'article_number',
			'article_name',
			'ean',
			'qty',
			'price',
			'status'
		];

		/* PRODUCT FIELDS */
		$product_fields = [
			'sku',
			'model',
			'model_name'
		];

		/* MULTILANG FIELDS */
		$multilang_fields = [
			'name_en',
			'short_desc_en',
			'full_description_en'
		];

		/* ================= STOCK UPDATE ================= */
		if (in_array($column, $stock_fields)) {

			// 🔥 HANDLE STATUS MAPPING
			if ($column == 'status') {
				if ($value === '1' || $value === 1) {
					$value = 'T';
				} else {
					$value = 'F';
				}
			}

			$pjStockModel
				->reset()
				->where('id', $id)
				->limit(1)
				->modifyAll([$column => $value]);
		}
		/* ================= PRODUCT UPDATE ================= */ elseif (in_array($column, $product_fields)) {


			$pjProductModel
				->reset()
				->where('id', $product_id)
				->limit(1)
				->modifyAll([
					$column => $value
				]);
		}

		/* ================= MULTILANG UPDATE ================= */ elseif (in_array($column, $multilang_fields)) {

			$field_map = [
				'name_en'             => 'name',
				'short_desc_en'       => 'short_desc',
				'full_description_en' => 'full_desc'
			];

			pjMultiLangModel::factory()->updateMultiLang(
				[
					$this->getLocaleId() => [
						$field_map[$column] => $value
					]
				],
				$product_id,
				'pjProduct',
				'data'
			);
		}

		/* ================= SIZE / COLOR ================= */ elseif ($column == 'size' || $column == 'color') {

			$attrs = pjStockAttributeModel::factory()
				->where('stock_id', $id)
				->findAll()
				->getData();

			if (!empty($attrs)) {

				foreach ($attrs as $attr) {

					$group = pjMultiLangModel::factory()
						->where('model', 'pjAttribute')
						->where('foreign_id', $attr['attribute_parent_id'])
						->where('field', 'name')
						->where('locale', $this->getLocaleId())
						->limit(1)
						->findAll()
						->getData();

					if (!$group) continue;

					$group_name = strtolower(trim($group[0]['content']));

					if ($group_name == $column) {

						pjMultiLangModel::factory()
							->reset()
							->where('model', 'pjAttribute')
							->where('foreign_id', $attr['attribute_id'])
							->where('field', 'name')
							->where('locale', $this->getLocaleId())
							->limit(1)
							->modifyAll([
								'content' => $value
							]);
					}
				}
			}
		}

		/* ================= INVALID ================= */ else {
			self::jsonResponse([
				'status' => 'ERR',
				'code'   => 105,
				'text'   => 'Invalid column.'
			]);
		}

		self::jsonResponse([
			'status' => 'OK',
			'code'   => 200,
			'text'   => 'Row updated successfully.'
		]);
	}
	// public function pjActionUpdateFlatFile()
	// {
	// 	$this->checkLogin();

	// 	if (!pjAuth::factory()->hasAccess()) {
	// 		$this->sendForbidden();
	// 		return;
	// 	}

	// 	$stock_id = $this->_get->toInt('id');

	// 	if ($stock_id <= 0) {
	// 		pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminProducts&action=pjActionProductsFlatFileIndex");
	// 	}

	// 	$pjStockModel = pjStockModel::factory();
	// 	$pjProductModel = pjProductModel::factory();
	// 	$pjMultiLangModel = pjMultiLangModel::factory();
	// 	$pjStockAttributeModel = pjStockAttributeModel::factory();

	// 	/* STOCK */

	// 	$stock = $pjStockModel
	// 		->select('t1.*, t2.small_path')
	// 		->join('pjGallery', 't2.id=t1.image_id', 'left')
	// 		->where('t1.id', $stock_id)
	// 		->limit(1)
	// 		->findAll()
	// 		->getData();

	// 	if (empty($stock)) {
	// 		pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminProducts&action=pjActionProductsFlatFileIndex");
	// 	}

	// 	$stock = $stock[0];

	// 	/* PRODUCT */

	// 	$product = $pjProductModel
	// 		->find($stock['product_id'])
	// 		->getData();

	// 	if (!$product) {
	// 		pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminProducts&action=pjActionProductsFlatFileIndex");
	// 	}

	// 	/* MULTILANG */

	// 	$product['i18n'] = $pjMultiLangModel->getMultiLang($product['id'], 'pjProduct');

	// 	/* STOCK ATTRIBUTES */

	// 	$attrs = $pjStockAttributeModel
	// 		->where('t1.stock_id', $stock_id)
	// 		->findAll()
	// 		->getDataPair('attribute_parent_id', 'attribute_id');

	// 	$stock['attrs'] = $attrs;

	// 	/* IMPORTANT — VIEW EXPECTS stock_arr */

	// 	$stock_arr = array();
	// 	$stock_arr[] = $stock;
	// 	$locale_id = $this->getLocaleId();

	// 	/* BRAND */

	// 	$brand = pjProductBrandModel::factory()
	// 		->select("t3.content AS brand_name")
	// 		->join('pjBrand', 't2.id=t1.brand_id', 'left')
	// 		->join(
	// 			'pjMultiLang',
	// 			"t3.model='pjBrand' AND t3.foreign_id=t2.id AND t3.field='name' AND t3.locale='$locale_id'",
	// 			'left'
	// 		)
	// 		->where('t1.product_id', $product['id'])
	// 		->limit(1)
	// 		->findAll()
	// 		->getData();

	// 	$brand_name = !empty($brand) ? $brand[0]['brand_name'] : '';

	// 	/* CATEGORY */

	// 	$category = pjProductCategoryModel::factory()
	// 		->select("t3.content AS category_name")
	// 		->join('pjCategory', 't2.id=t1.category_id', 'left')
	// 		->join(
	// 			'pjMultiLang',
	// 			"t3.model='pjCategory' AND t3.foreign_id=t2.id AND t3.field='name' AND t3.locale='$locale_id'",
	// 			'left'
	// 		)
	// 		->where('t1.product_id', $product['id'])
	// 		->limit(1)
	// 		->findAll()
	// 		->getData();

	// 	$category_name = !empty($category) ? $category[0]['category_name'] : '';
	// 	/* CATEGORY TREE */

	// 	$this->set(
	// 		'category_arr',
	// 		pjCategoryModel::factory()->getNode($this->getLocaleId(), 1)
	// 	);

	// 	/* BRAND TREE */

	// 	$this->set(
	// 		'brand_arr',
	// 		pjBrandModel::factory()->getNode($this->getLocaleId(), 1)
	// 	);

	// 	/* PRODUCT CATEGORIES */

	// 	$this->set(
	// 		'pc_arr',
	// 		pjProductCategoryModel::factory()
	// 			->where('t1.product_id', $product['id'])
	// 			->orderBy('t1.category_id ASC')
	// 			->findAll()
	// 			->getDataPair('category_id', 'category_id')
	// 	);

	// 	/* PRODUCT BRANDS */

	// 	$this->set(
	// 		'pb_arr',
	// 		pjProductBrandModel::factory()
	// 			->where('t1.product_id', $product['id'])
	// 			->orderBy('t1.brand_id ASC')
	// 			->findAll()
	// 			->getDataPair('brand_id', 'brand_id')
	// 	);
	// 	$this->set('brand_name', $brand_name);
	// 	$this->set('category_name', $category_name);
	// 	/* PASS DATA */

	// 	$this->set('arr', $product);
	// 	$this->set('stock_arr', $stock_arr);

	// 	$this->set('extra_arr', array());
	// 	$this->set('attr_arr', array());

	// 	$this->set('gallery_arr', array());

	// 	$this->setLocalesData();

	// 	$this->set('flatfile_mode', true);

	// 	$this->appendCss('css/select2.min.css', PJ_THIRD_PARTY_PATH . 'select2/');
	// 	$this->appendJs('js/select2.full.min.js', PJ_THIRD_PARTY_PATH . 'select2/');
	// 	$this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
	// 	$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
	// 	$this->appendJs('pjAdminProducts.js');
	// }
	public function pjActionUpdateFlatFile()
	{
		$this->checkLogin();

		if (!pjAuth::factory()->hasAccess()) {
			$this->sendForbidden();
			return;
		}

		$pjProductModel        = pjProductModel::factory();
		$pjStockModel          = pjStockModel::factory();
		$pjStockAttributeModel = pjStockAttributeModel::factory();
		$pjMultiLangModel      = pjMultiLangModel::factory();

		/* ================= SAVE ================= */

		if (self::isPost()) {
			// echo "<pre>";
			// print_r($this->_post);
			// die;
			$product_id = $this->_post->toInt('product_id');
			$stock_id   = $this->_post->toInt('stock_id');

			$stock_article_number = $this->_post->toArray('stock_article_number');
			$stock_article_name   = $this->_post->toArray('stock_article_name');
			$stock_ean            = $this->_post->toArray('stock_ean');
			$stock_qty            = $this->_post->toArray('stock_qty');
			$stock_price          = $this->_post->toArray('stock_price');
			$stock_image_id          = $this->_post->toArray('stock_image_id');
			$status_arr           = $this->_post->toArray('status');

			/* ---------------- PRODUCT UPDATE ---------------- */

			$pjProductModel
				->reset()
				->where('id', $product_id)
				->limit(1)
				->modifyAll([
					'sku'        => $this->_post->toString('sku'),
					'model'      => $this->_post->toString('model'),
					'model_name' => $this->_post->toString('model_name')
				]);

			/* ---------------- MULTILANG UPDATE ---------------- */

			$name       = $this->_post->toString('name');
			$short_desc = $this->_post->toString('short_desc');
			$full_desc  = $this->_post->toString('full_desc');

			if ($name || $short_desc || $full_desc) {

				$i18n = [
					$this->getLocaleId() => [
						'name'       => $name,
						'short_desc' => $short_desc,
						'full_desc'  => $full_desc
					]
				];

				pjMultiLangModel::factory()->updateMultiLang(
					$i18n,
					$product_id,
					'pjProduct',
					'data'
				);
			}
			/* ---------------- STOCK UPDATE ---------------- */

			$pjStockModel
				->reset()
				->where('id', $stock_id)
				->limit(1)
				->modifyAll([
					'article_number' => $stock_article_number[$stock_id] ?? null,
					'article_name'   => $stock_article_name[$stock_id] ?? null,
					'ean'            => $stock_ean[$stock_id] ?? null,
					'qty'            => $stock_qty[$stock_id] ?? 0,
					'price'          => $stock_price[$stock_id] ?? 0,
					'image_id'          => $stock_image_id[$stock_id] ?? 0,
					'status'         => $status_arr[$stock_id] ?? 'T'
				]);

			/* ---------------- BRAND UPDATE ---------------- */

			pjProductBrandModel::factory()
				->where('product_id', $product_id)
				->eraseAll();

			$brand_id = $this->_post->toInt('brand_id');

			if ($brand_id > 0) {

				pjProductBrandModel::factory()
					->reset()
					->set('product_id', $product_id)
					->set('brand_id', $brand_id)
					->insert();
			}

			/* ---------------- CATEGORY UPDATE ---------------- */

			pjProductCategoryModel::factory()
				->where('product_id', $product_id)
				->eraseAll();

			$category_id = $this->_post->toInt('category_id');

			if ($category_id > 0) {

				pjProductCategoryModel::factory()
					->reset()
					->set('product_id', $product_id)
					->set('category_id', $category_id)
					->insert();
			}

			/* ---------------- SIZE + COLOR UPDATE ---------------- */

			$size  = $this->_post->toString('size');
			$color = $this->_post->toString('color');

			$attrs = $pjStockAttributeModel
				->where('stock_id', $stock_id)
				->findAll()
				->getData();

			foreach ($attrs as $attr) {

				$group = pjMultiLangModel::factory()
					->where('model', 'pjAttribute')
					->where('foreign_id', $attr['attribute_parent_id'])
					->where('field', 'name')
					->where('locale', $this->getLocaleId())
					->limit(1)
					->findAll()
					->getData();

				if (!$group) {
					continue;
				}

				$group_name = strtolower($group[0]['content']);

				if ($group_name == 'size') {

					pjMultiLangModel::factory()
						->reset()
						->where('model', 'pjAttribute')
						->where('foreign_id', $attr['attribute_id'])
						->where('field', 'name')
						->where('locale', $this->getLocaleId())
						->limit(1)
						->modifyAll([
							'content' => $size
						]);
				}

				if ($group_name == 'color') {

					pjMultiLangModel::factory()
						->reset()
						->where('model', 'pjAttribute')
						->where('foreign_id', $attr['attribute_id'])
						->where('field', 'name')
						->where('locale', $this->getLocaleId())
						->limit(1)
						->modifyAll([
							'content' => $color
						]);
				}
			}

			pjUtil::redirect(
				PJ_INSTALL_URL .
					"index.php?controller=pjAdminProducts&action=pjActionProductsFlatFileIndex&err=AP01"
			);
		}

		/* ================= LOAD PAGE ================= */

		$stock_id = $this->_get->toInt('id');

		$stock = $pjStockModel
			->select('t1.*, t2.small_path')
			->join('pjGallery', 't2.id=t1.image_id', 'left')
			->where('t1.id', $stock_id)
			->limit(1)
			->findAll()
			->getData();

		if (!$stock) {
			pjUtil::redirect(
				PJ_INSTALL_URL .
					"index.php?controller=pjAdminProducts&action=pjActionProductsFlatFileIndex"
			);
		}

		$stock = $stock[0];

		$product = $pjProductModel
			->find($stock['product_id'])
			->getData();

		$product['i18n'] = $pjMultiLangModel->getMultiLang($product['id'], 'pjProduct');

		/* LOAD SIZE + COLOR */

		$attrs = $pjStockAttributeModel
			->where('stock_id', $stock_id)
			->findAll()
			->getData();

		$size  = '';
		$color = '';

		foreach ($attrs as $attr) {

			$group = pjMultiLangModel::factory()
				->where('model', 'pjAttribute')
				->where('foreign_id', $attr['attribute_parent_id'])
				->where('field', 'name')
				->where('locale', $this->getLocaleId())
				->limit(1)
				->findAll()
				->getData();

			if (!$group) {
				continue;
			}

			$group_name = strtolower($group[0]['content']);

			$value = pjMultiLangModel::factory()
				->where('model', 'pjAttribute')
				->where('foreign_id', $attr['attribute_id'])
				->where('field', 'name')
				->where('locale', $this->getLocaleId())
				->limit(1)
				->findAll()
				->getData();

			if (!$value) {
				continue;
			}

			if ($group_name == 'size') {
				$size = $value[0]['content'];
			}

			if ($group_name == 'color') {
				$color = $value[0]['content'];
			}
		}

		$stock['size']  = $size;
		$stock['color'] = $color;

		$stock_arr = [];
		$stock_arr[] = $stock;

		/* CATEGORY + BRAND */

		$this->set('brand_arr', pjBrandModel::factory()->getNode($this->getLocaleId(), 1));
		$this->set('category_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));

		$this->set(
			'pb_arr',
			pjProductBrandModel::factory()
				->where('product_id', $product['id'])
				->findAll()
				->getDataPair('brand_id', 'brand_id')
		);

		$this->set(
			'pc_arr',
			pjProductCategoryModel::factory()
				->where('product_id', $product['id'])
				->findAll()
				->getDataPair('category_id', 'category_id')
		);

		$this->set('arr', $product);
		$this->set('stock_arr', $stock_arr);
		$this->set('flatfile_mode', true);

		$this->setLocalesData();

		$this->appendCss('css/select2.min.css', PJ_THIRD_PARTY_PATH . 'select2/');
		$this->appendJs('js/select2.full.min.js', PJ_THIRD_PARTY_PATH . 'select2/');
		$this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
		$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
		$this->appendJs('pjAdminProducts.js');
	}
	public function pjActionSaveProduct()
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
		$pjProductModel = pjProductModel::factory();
		$arr = $pjProductModel->find($this->_get->toInt('id'))->getData();
		if (!$arr) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Product not found.'));
		}
		if (!in_array($this->_post->toString('column'), $pjProductModel->getI18n())) {
			$pjProductModel->reset()->where('id', $this->_get->toInt('id'))->limit(1)->modifyAll(array($this->_post->toString('column') => $this->_post->toString('value')));
		} else {
			pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($this->_post->toString('column') => $this->_post->toString('value'))), $this->_get->toInt('id'), 'pjProduct', 'data');
		}
		self::jsonResponse(array('status' => 'OK', 'code' => 201, 'text' => 'Product has been updated.'));
		exit;
	}

	public function pjActionSaveStock()
	{
		$this->setAjax(true);

		if ($this->isXHR() && $this->isLoged()) {
			$pjStockModel = pjStockModel::factory();
			if (!in_array($this->_post->toString('column'), $pjStockModel->getI18n())) {
				if ($this->_post->toString('column') == 'qty') {
					$before = $pjStockModel->reset()->find($this->_get->toInt('id'))->getData();
				}
				$affected_rows = $pjStockModel->reset()->set('id', $this->_get->toInt('id'))->modify(array($this->_post->toString('column') => $this->_post->toString('value')))->getAffectedRows();
				if ($this->_post->toString('column') == 'qty' && $affected_rows == 1) {
					$after = $pjStockModel->reset()->find($this->_get->toInt('id'))->getData();
					pjAppController::addToHistory($this->_get->toInt('id'), $this->getUserId(), $pjStockModel->getTable(), $before, $after);
				}
			} else {
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($this->_post->toString('column') => $this->_post->toString('value'))), $this->_get->toInt('id'), 'pjStock');
			}
		}
		exit;
	}

	public function pjActionUpdate()
	{
		$this->checkLogin();
		if (!pjAuth::factory()->hasAccess()) {
			$this->sendForbidden();
			return;
		}

		$pjExtraModel = pjExtraModel::factory();
		$pjExtraItemModel = pjExtraItemModel::factory();
		$pjAttributeModel = pjAttributeModel::factory();
		$pjStockModel = pjStockModel::factory();
		$pjStockAttributeModel = pjStockAttributeModel::factory();
		$pjProductCategoryModel = pjProductCategoryModel::factory();
		$pjProductBrandModel = pjProductBrandModel::factory();
		$pjMultiLangModel = pjMultiLangModel::factory();
		$pjProductModel = pjProductModel::factory();
		$default_company_id = isset($_SESSION[$this->defaultCompany]['id'])
			? (int) $_SESSION[$this->defaultCompany]['id']
			: 1; // fallback to first company (optional)


		$post_max_size = pjUtil::getPostMaxSize();
		if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SERVER['CONTENT_LENGTH']) && (int) $_SERVER['CONTENT_LENGTH'] > $post_max_size) {
			pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminProducts&action=pjActionIndex&err=AP09");
		}
		$err = 'AP01';

		// if (!$this->_post->check('import_mode')) {
		// 	pjUtil::redirect(sprintf(
		// 		"%s?controller=pjAdminProducts&action=pjActionUpdate&id=%u&tab=%s&err=%s",
		// 		$_SERVER['PHP_SELF'],
		// 		$this->_post->toInt('id'),
		// 		$this->_post->toString('tab'),
		// 		$err
		// 	));
		// }
		if (self::isPost() && $this->_post->toInt('product_update')) {
			// echo "<pre>"; print_r($this->_post); die;

			if (!$this->_post->check('id') || $this->_post->toInt('id') <= 0) {
				pjUtil::redirect(PJ_INSTALL_URL . 'index.php?controller=pjAdminProducts&action=pjActionIndex&err=AP08');
			}
			$product_arr = $pjProductModel->find($this->_post->toInt('id'))->getData();
			if (empty($product_arr)) {
				pjUtil::redirect(PJ_INSTALL_URL . 'index.php?controller=pjAdminProducts&action=pjActionIndex&err=AP08');
			}

			# Stock start ----------------------------------------------
			# Get/prepare

			$sa_arr = $pjStockAttributeModel
				->select("t1.stock_id, GROUP_CONCAT(CONCAT_WS(':', t2.image_id, t1.attribute_parent_id, t1.attribute_id) ORDER BY t1.attribute_parent_id ASC, t1.attribute_id ASC SEPARATOR '|') AS `str`")
				->join('pjStock', 't2.id=t1.stock_id AND t2.product_id=t1.product_id', 'inner')
				->where('t2.product_id', $this->_post->toInt('id'))
				->where('t1.company_id', $default_company_id)
				->groupBy('t1.stock_id')
				->findAll()
				->getDataPair('stock_id', 'str');

			$i_arr = $u_arr = array();

			// if ($stock_qty_arr = $this->_post->toArray('stock_qty')) {
			// 	$stock_price_arr = $this->_post->toArray('stock_price');
			// 	$stock_image_id_arr = $this->_post->toArray('stock_image_id');
			// 	$stock_attribute_arr = $this->_post->toArray('stock_attribute');
			// 	$stock_article_name_arr = $this->_post->toArray('stock_article_name');
			// 	$stock_article_number_arr = $this->_post->toArray('stock_article_number');
			// 	$stock_ean_arr = $this->_post->toArray('stock_ean');
			// 	$stock_status_arr = $this->_post->toArray('status');
			// 	foreach ($stock_qty_arr as $k => $whatever) {
			// 		if (strpos($k, 'x_') === 0) {
			// 			if ((float) $stock_price_arr[$k] > 0 && (int) $stock_image_id_arr[$k] > 0) {
			// 				# Insert new stock
			// 				$tmp = array();
			// 				if (isset($stock_attribute_arr[$k])) {
			// 					foreach ($stock_attribute_arr[$k] as $attr_parent_id => $attr_id) {
			// 						$tmp[] = $stock_image_id_arr[$k] . ":" . $attr_parent_id . ":" . $attr_id;
			// 					}
			// 				}
			// 				asort($tmp);
			// 				$i_arr[$k] = join("|", $tmp);
			// 			}
			// 		} else {
			// 			# Update attr
			// 			if (isset($stock_attribute_arr[$k])) {
			// 				# Add items again
			// 				$tmp = array();
			// 				foreach ($stock_attribute_arr[$k] as $attr_parent_id => $attr_id) {
			// 					$tmp[] = $stock_image_id_arr[$k] . ":" . $attr_parent_id . ":" . $attr_id;
			// 				}
			// 				asort($tmp);
			// 				$u_arr[$k] = join("|", $tmp);
			// 			}
			// 		}
			// 	}

			// 	foreach ($stock_qty_arr as $k => $whatever) {
			// 		if (strpos($k, 'x_') === 0) {
			// 			if ((float) $stock_price_arr[$k] > 0 && (int) $stock_image_id_arr[$k] > 0 && !in_array($i_arr[$k], $sa_arr)) {
			// 				# Insert new stock
			// 				// $stock_id = $pjStockModel
			// 				// 	->reset()
			// 				// 	->set('company_id', $default_company_id)
			// 				// 	->set('product_id', $this->_post->toInt('id'))
			// 				// 	->set('image_id', $stock_image_id_arr[$k])
			// 				// 	->set('qty', $stock_qty_arr[$k])
			// 				// 	->set('price', $stock_price_arr[$k])
			// 				// 	->insert()
			// 				// 	->getInsertId();
			// 				$stock_id = $pjStockModel
			// 					->reset()
			// 					->set('company_id', $default_company_id)
			// 					->set('product_id', $this->_post->toInt('id'))
			// 					->set('image_id', $stock_image_id_arr[$k])
			// 					->set('article_name', isset($stock_article_name_arr[$k]) ? $stock_article_name_arr[$k] : NULL)
			// 					->set('article_number', isset($stock_article_number_arr[$k]) ? $stock_article_number_arr[$k] : NULL)
			// 					->set('ean', isset($stock_ean_arr[$k]) ? $stock_ean_arr[$k] : NULL)
			// 					->set('qty', $stock_qty_arr[$k])
			// 					->set('price', $stock_price_arr[$k])
			// 					->set('status', isset($stock_status_arr[$k]) ? $stock_status_arr[$k] : 'T')
			// 					->insert()
			// 					->getInsertId();
			// 				if ($stock_id !== false && (int) $stock_id > 0) {
			// 					if (isset($stock_attribute_arr[$k])) {
			// 						$pjStockAttributeModel->begin();
			// 						foreach ($stock_attribute_arr[$k] as $attr_parent_id => $attr_id) {
			// 							$pjStockAttributeModel
			// 								->reset()
			// 								->set('company_id', $default_company_id)
			// 								->set('stock_id', $stock_id)
			// 								->set('product_id', $this->_post->toInt('id'))
			// 								->set('attribute_parent_id', $attr_parent_id)
			// 								->set('attribute_id', $attr_id)
			// 								->insert();
			// 						}
			// 						$pjStockAttributeModel->commit();
			// 					}
			// 				}
			// 			}
			// 		} else {
			// 			# Update attr
			// 			$before = $pjStockModel->reset()->find($k)->getData();
			// 			if (
			// 				$pjStockModel
			// 				->reset()
			// 				->set('id', $k)

			// 				->modify(array(
			// 					'image_id' => $stock_image_id_arr[$k],
			// 					'article_name' => isset($stock_article_name_arr[$k]) ? $stock_article_name_arr[$k] : NULL,
			// 					'article_number' => isset($stock_article_number_arr[$k]) ? $stock_article_number_arr[$k] : NULL,
			// 					'ean' => isset($stock_ean_arr[$k]) ? $stock_ean_arr[$k] : NULL,
			// 					'qty' => $stock_qty_arr[$k],
			// 					'price' => $stock_price_arr[$k],
			// 					'status' => isset($stock_status_arr[$k]) ? $stock_status_arr[$k] : 'T'
			// 				))
			// 				->getAffectedRows() == 1
			// 			) {
			// 				$after = $pjStockModel->reset()->find($k)->getData();
			// 				pjAppController::addToHistory($k, $this->getUserId(), $pjStockModel->getTable(), $before, $after);
			// 			}
			// 			//if (isset($sa_arr[$k]) && isset($u_arr[$k]) && $sa_arr[$k] != $u_arr[$k] && !in_array($u_arr[$k], $sa_arr))
			// 			if (
			// 				isset($u_arr[$k]) && !in_array($u_arr[$k], $sa_arr) && (
			// 					!isset($sa_arr[$k]) ||
			// 					(isset($sa_arr[$k]) && $sa_arr[$k] != $u_arr[$k])
			// 				)
			// 			) {
			// 				# Delete items ------------------
			// 				$pjStockAttributeModel->reset()->where('stock_id', $k)->eraseAll();
			// 				if (isset($stock_attribute_arr[$k])) {
			// 					# Add items again
			// 					$pjStockAttributeModel->begin();
			// 					foreach ($stock_attribute_arr[$k] as $attr_parent_id => $attr_id) {
			// 						$pjStockAttributeModel
			// 							->reset()
			// 							->set('stock_id', $k)
			// 							->set('company_id', $default_company_id)
			// 							->set('product_id', $this->_post->toInt('id'))
			// 							->set('attribute_parent_id', $attr_parent_id)
			// 							->set('attribute_id', $attr_id)
			// 							->insert();
			// 					}
			// 					$pjStockAttributeModel->commit();
			// 				}
			// 			}
			// 		}
			// 	}
			// }
			if ($stock_qty_arr = $this->_post->toArray('stock_qty')) {

				$stock_price_arr = $this->_post->toArray('stock_price');
				$stock_image_id_arr = $this->_post->toArray('stock_image_id');
				$stock_attribute_arr = $this->_post->toArray('stock_attribute');
				$stock_article_name_arr = $this->_post->toArray('stock_article_name');
				$stock_article_number_arr = $this->_post->toArray('stock_article_number');
				$stock_ean_arr = $this->_post->toArray('stock_ean');
				$stock_status_arr = $this->_post->toArray('status');
				foreach ($stock_qty_arr as $k => $whatever) {

					if (strpos($k, 'x_') === 0) {

						if ((float)$stock_price_arr[$k] > 0 && (int)$stock_image_id_arr[$k] > 0) {

							$tmp = array();

							if (isset($stock_attribute_arr[$k])) {
								foreach ($stock_attribute_arr[$k] as $attr_parent_id => $attr_id) {
									$tmp[] = $stock_image_id_arr[$k] . ":" . $attr_parent_id . ":" . $attr_id;
								}
							}

							asort($tmp);
							$i_arr[$k] = join("|", $tmp);
						}
					} else {

						if (isset($stock_attribute_arr[$k])) {

							$tmp = array();

							foreach ($stock_attribute_arr[$k] as $attr_parent_id => $attr_id) {
								$tmp[] = $stock_image_id_arr[$k] . ":" . $attr_parent_id . ":" . $attr_id;
							}

							asort($tmp);
							$u_arr[$k] = join("|", $tmp);
						}
					}
				}

				foreach ($stock_qty_arr as $k => $whatever) {

					$status = (!empty($stock_status_arr[$k]) && in_array($stock_status_arr[$k], ['T', 'F']))
						? $stock_status_arr[$k]
						: 'T';

					if (strpos($k, 'x_') === 0) {

						if (
							(float)$stock_price_arr[$k] > 0 &&
							(int)$stock_image_id_arr[$k] > 0 &&
							isset($i_arr[$k]) &&
							!in_array($i_arr[$k], $sa_arr)
						) {

							$stock_id = $pjStockModel
								->reset()
								->set('company_id', $default_company_id)
								->set('product_id', $this->_post->toInt('id'))
								->set('image_id', $stock_image_id_arr[$k])
								->set('article_name', isset($stock_article_name_arr[$k]) ? $stock_article_name_arr[$k] : NULL)
								->set('article_number', isset($stock_article_number_arr[$k]) ? $stock_article_number_arr[$k] : NULL)
								->set('ean', isset($stock_ean_arr[$k]) ? $stock_ean_arr[$k] : NULL)
								->set('qty', $stock_qty_arr[$k])
								->set('price', $stock_price_arr[$k])
								->set('status', $status)
								->insert()
								->getInsertId();

							if ($stock_id !== false && (int)$stock_id > 0) {

								if (isset($stock_attribute_arr[$k])) {

									$pjStockAttributeModel->begin();

									foreach ($stock_attribute_arr[$k] as $attr_parent_id => $attr_id) {

										$pjStockAttributeModel
											->reset()
											->set('company_id', $default_company_id)
											->set('stock_id', $stock_id)
											->set('product_id', $this->_post->toInt('id'))
											->set('attribute_parent_id', $attr_parent_id)
											->set('attribute_id', $attr_id)
											->insert();
									}

									$pjStockAttributeModel->commit();
								}
							}
						}
					} else {

						$before = $pjStockModel->reset()->find($k)->getData();

						if (
							$pjStockModel
							->reset()
							->set('id', $k)
							->modify(array(
								'image_id' => $stock_image_id_arr[$k],
								'article_name' => isset($stock_article_name_arr[$k]) ? $stock_article_name_arr[$k] : NULL,
								'article_number' => isset($stock_article_number_arr[$k]) ? $stock_article_number_arr[$k] : NULL,
								'ean' => isset($stock_ean_arr[$k]) ? $stock_ean_arr[$k] : NULL,
								'qty' => $stock_qty_arr[$k],
								'price' => $stock_price_arr[$k],
								'status' => $status
							))
							->getAffectedRows() == 1
						) {

							$after = $pjStockModel->reset()->find($k)->getData();

							pjAppController::addToHistory(
								$k,
								$this->getUserId(),
								$pjStockModel->getTable(),
								$before,
								$after
							);
						}

						if (
							isset($u_arr[$k]) &&
							!in_array($u_arr[$k], $sa_arr) &&
							(
								!isset($sa_arr[$k]) ||
								(isset($sa_arr[$k]) && $sa_arr[$k] != $u_arr[$k])
							)
						) {

							$pjStockAttributeModel->reset()->where('stock_id', $k)->eraseAll();

							if (isset($stock_attribute_arr[$k])) {

								$pjStockAttributeModel->begin();

								foreach ($stock_attribute_arr[$k] as $attr_parent_id => $attr_id) {

									$pjStockAttributeModel
										->reset()
										->set('stock_id', $k)
										->set('company_id', $default_company_id)
										->set('product_id', $this->_post->toInt('id'))
										->set('attribute_parent_id', $attr_parent_id)
										->set('attribute_id', $attr_id)
										->insert();
								}

								$pjStockAttributeModel->commit();
							}
						}
					}
				}
			}
			# Stock end ----------------------------------------------

			$i18n_arr = $this->pjActionAttrHandle($this->_post->toInt('id'));
			if ($this->_post->check('is_digital')) {
				$this->pjActionDeleteProductAttr($product_arr['id']);
				$this->pjActionDeleteStockAttr($product_arr['id']);

				$statement = sprintf("DELETE FROM `%1\$s`
						WHERE `product_id` = :product_id
						AND `id` NOT IN (
						SELECT `id`
						FROM (
							SELECT `id`
							FROM `%1\$s`
							WHERE `product_id` = :product_id
							ORDER BY `id` ASC
							LIMIT 1
						) `foo`);", $pjStockModel->getTable());

				$pjStockModel->prepare($statement)->exec(array('product_id' => $product_arr['id']));
			}

			$i18n_arr = $this->_post->toArray('i18n');
			# Extras start -----------------------------------------
			# Get/Prepare
			$extra_items = array();
			$extra_arr = $pjExtraModel->where('t1.product_id', $this->_post->toInt('id'))->findAll()->getDataPair('id', 'type');
			foreach ($extra_arr as $id => $type) {
				if ($type == 'multi') {
					$extra_items[$id] = $pjExtraItemModel->reset()->where('t1.extra_id', $id)->findAll()->getDataPair('id', 'id');
				}
			}
			# Update ------------------
			if ($extra_type_arr = $this->_post->toArray('extra_type')) {
				$extra_price_arr = $this->_post->toArray('extra_price');
				$extra_is_mandatory_arr = $this->_post->toArray('extra_is_mandatory');
				foreach ($extra_type_arr as $k => $type) {
					# Insert new extra ------------------
					if (strpos($k, 'x_') === 0) {
						$data = array();
						$data['product_id'] = $this->_post->toInt('id');
						$data['type'] = $type;
						$data['company_id'] = $default_company_id;
						switch ($type) {
							case 'single':
								$data['price'] = $extra_price_arr[$k];
								break;
							case 'multi':
								break;
						}
						$data['is_mandatory'] = isset($extra_is_mandatory_arr[$k]) ? 1 : 0;
						$extra_id = $pjExtraModel->reset()->setAttributes($data)->insert()->getInsertId();
						if ($extra_id !== false && (int) $extra_id > 0) {
							switch ($type) {
								case 'single':
									$tmp = $this->pjActionTurnI18n($i18n_arr, 'extra_name', $k);
									$pjMultiLangModel->saveMultiLang($tmp, $extra_id, 'pjExtra');
									break;
								case 'multi':
									$tmp = $this->pjActionTurnI18n($i18n_arr, 'extra_title', $k);
									$pjMultiLangModel->saveMultiLang($tmp, $extra_id, 'pjExtra');

									$edata = array();
									$edata['extra_id'] = $extra_id;
									$edata['company_id'] = $default_company_id;
									foreach ($extra_price_arr[$k] as $index => $price) {
										$edata['price'] = $price;
										$ei_id = $pjExtraItemModel->reset()->setAttributes($edata)->insert()->getInsertId();
										if ($ei_id !== false && (int) $ei_id > 0) {
											$tmp = $this->pjActionTurnI18n($i18n_arr, 'extra_name', $k, $index);
											$pjMultiLangModel->saveMultiLang($tmp, $ei_id, 'pjExtraItem');
										}
									}
									break;
							}
						}
					} else {
						# Update extra
						$pjExtraModel->reset()->set('id', $k)->modify(array(
							'type' => $type,
							'price' => $type == 'single' ? $extra_price_arr[$k] : ':NULL',
							'is_mandatory' => isset($extra_is_mandatory_arr[$k]) ? 1 : 0
						));
						### ---
						switch ($type) {
							case 'multi':
								# value of column 'type' in DB is single
								$tmp = $this->pjActionTurnI18n($i18n_arr, 'extra_title', $k);
								if ($extra_arr[$k] == 'single') {
									$pjMultiLangModel->reset()->where('model', 'pjExtra')->where('foreign_id', $k)->eraseAll();
									$pjMultiLangModel->saveMultiLang($tmp, $k, 'pjExtra');
								} else {
									$pjMultiLangModel->updateMultiLang($tmp, $k, 'pjExtra');
								}
								# Delete items ------------------
								$diff = array_diff($extra_items[$k], array_keys($extra_price_arr[$k]));
								if (count($diff) > 0) {
									$pjExtraItemModel->reset()->whereIn('id', $diff)->eraseAll();
									$pjMultiLangModel->reset()->where('model', 'pjExtraItem')->whereIn('foreign_id', $diff)->eraseAll();
								}

								foreach ($extra_price_arr[$k] as $index => $price) {
									if (strpos($index, 'y_') === 0) {
										# Add items
										$ei_id = $pjExtraItemModel->reset()->setAttributes(array(
											'extra_id' => $k,
											'price' => $price,
											'company_id' => $default_company_id
										))->insert()->getInsertId();
										if ($ei_id !== false && (int) $ei_id > 0) {
											$tmp = $this->pjActionTurnI18n($i18n_arr, 'extra_name', $k, $index);
											$pjMultiLangModel->saveMultiLang($tmp, $ei_id, 'pjExtraItem');
										}
									} else {
										# Update items
										$pjExtraItemModel->reset()->set('id', $index)->modify(array(
											'price' => $price
										));
										$tmp = $this->pjActionTurnI18n($i18n_arr, 'extra_name', $k, $index);
										$pjMultiLangModel->updateMultiLang($tmp, $index, 'pjExtraItem');
									}
								}
								break;
							case 'single':
								# value of column 'type' in DB is multi
								if ($extra_arr[$k] == 'multi') {
									$ei_ids = $pjExtraItemModel->reset()->where('extra_id', $k)->findAll()->getDataPair(NULL, 'id');
									if (!empty($ei_ids)) {
										$pjExtraItemModel->eraseAll();
										$pjMultiLangModel->reset()->where('model', 'pjExtraItem')->whereIn('foreign_id', $ei_ids)->eraseAll();
									}
									$pjMultiLangModel->reset()->where('model', 'pjExtra')->where('foreign_id', $k)->where('field', 'extra_title')->eraseAll();
								}
								$tmp = $this->pjActionTurnI18n($i18n_arr, 'extra_name', $k);
								$pjMultiLangModel->updateMultiLang($tmp, $k, 'pjExtra');
								break;
						}
						### ---
					}
				}
			}
			# Extras end -----------------------------------------

			# Categories start -----------------------------------
			$pjProductCategoryModel->where('product_id', $this->_post->toInt('id'))->eraseAll();
			if ($category_id_arr = $this->_post->toArray('category_id')) {
				$pjProductCategoryModel->begin();
				foreach ($category_id_arr as $category_id) {
					$pjProductCategoryModel
						->reset()
						->set('product_id', $this->_post->toInt('id'))
						->set('category_id', $category_id)
						->set('company_id', $default_company_id)
						->insert();
				}
				$pjProductCategoryModel->commit();
			}
			# Categories end -------------------------------------

			# Brands start -----------------------------------
			$pjProductBrandModel->where('product_id', $this->_post->toInt('id'))->eraseAll();
			$brand_id = $this->_post->toInt('brand_id');

			if ($brand_id > 0) {

				$pjProductBrandModel
					->reset()
					->set('product_id', $this->_post->toInt('id'))
					->set('brand_id', $brand_id)
					->set('company_id', $default_company_id)
					->insert();
			}
			# Brands end -------------------------------------

			$data = array();
			$data['is_featured'] = $this->_post->check('is_featured') ? 1 : 0;
			$data['is_digital'] = $this->_post->check('is_digital') ? 1 : 0;
			if ($this->_post->check('is_digital')) {
				if ($this->_post->check('digital_choose')) {
					switch ($this->_post->toInt('digital_choose')) {
						case 1:
							if (isset($_FILES['digital_file'])) {
								if ($_FILES['digital_file']['error'] == 0) {
									$pjUpload = new pjUpload();
									$pjUpload->setAllowedTypes(array('*'));
									$pjUpload->setAllowedExt(array('*'));
									if ($pjUpload->load($_FILES['digital_file'])) {
										$name = $pjUpload->getFile('name');
										$file = PJ_UPLOAD_PATH . 'digital/' . md5(uniqid(rand(), true)) . "." . $pjUpload->getExtension();
										if ($pjUpload->save($file)) {
											$data['digital_file'] = $file;
											$data['digital_name'] = $name;
										}
									} else {
										$err = 'AP10';
									}
								} else if ($_FILES['digital_file']['error'] != 4) {
									$err = 'AP10';
									$data['is_digital'] = 0;
								}
							}
							break;
						case 2:
							if (file_exists($this->_post->toString('digital_file'))) {
								$data['digital_file'] = $this->_post->toString('digital_file');
								$data['digital_name'] = basename($this->_post->toString('digital_file'));
							} else {
								$err = 'AP11';
								$data['is_digital'] = 0;
							}
							break;
					}
				}
				if ($err == 'AP01') {
					$data['digital_expire'] = sprintf("%s:%s:00", $this->_post->toString('hour'), $this->_post->toString('minute'));
				} else {
					$data['digital_file'] = ':NULL';
					$data['digital_name'] = ':NULL';
					$data['digital_expire'] = ':NULL';
				}
			} else {
				$data['digital_file'] = ':NULL';
				$data['digital_name'] = ':NULL';
				$data['digital_expire'] = ':NULL';
			}

			$pjProductModel->reset()->set('id', $this->_post->toInt('id'))->modify(array_merge($this->_post->raw(), $data));

			$i18n_arr = $this->_post->toI18n('i18n');
			if ($i18n_arr) {
				foreach ($i18n_arr as $locale_id => $locale_arr) {
					if (isset($i18n_arr[$locale_id]['extra_title'])) {
						unset($i18n_arr[$locale_id]['extra_title']);
					}
					if (isset($i18n_arr[$locale_id]['extra_name'])) {
						unset($i18n_arr[$locale_id]['extra_name']);
					}
					if (isset($i18n_arr[$locale_id]['attr_group'])) {
						unset($i18n_arr[$locale_id]['attr_group']);
					}
					if (isset($i18n_arr[$locale_id]['attr_item'])) {
						unset($i18n_arr[$locale_id]['attr_item']);
					}
				}
				$pjMultiLangModel->updateMultiLang($i18n_arr, $this->_post->toInt('id'), 'pjProduct');
			}
			pjUtil::redirect(sprintf("%s?controller=pjAdminProducts&action=pjActionUpdate&id=%u&tab=%s&err=%s", $_SERVER['PHP_SELF'], $this->_post->toInt('id'), $this->_post->toString('tab'), $err));
		} else {
			$arr = $pjProductModel->where('t1.company_id', $default_company_id)->where('t1.id', $this->_get->toInt('id'))->findAll()->getData();
			if (!empty($arr)) {

				$arr = $arr[0];
			}

			if (count($arr) === 0) {
				pjUtil::redirect(sprintf("%s?controller=pjAdminProducts&action=pjActionIndex&err=%s", $_SERVER['PHP_SELF'], 'AP08'));
			}
			$pjMultiLangModel = pjMultiLangModel::factory();
			$arr['i18n'] = $pjMultiLangModel->getMultiLang($arr['id'], 'pjProduct');
			$this->set('arr', $arr);

			$this->set('category_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));
			$this->set('brand_arr', pjBrandModel::factory()->getNode($this->getLocaleId(), 1));
			$this->set('pc_arr', pjProductCategoryModel::factory()->where('t1.product_id', $arr['id'])->orderBy('t1.category_id ASC')->findAll()->getDataPair('category_id', 'category_id'));
			$this->set('pb_arr', pjProductBrandModel::factory()->where('t1.product_id', $arr['id'])->orderBy('t1.brand_id ASC')->findAll()->getDataPair('brand_id', 'brand_id'));
			$extra_arr = pjExtraModel::factory()->where('t1.product_id', $arr['id'])->findAll()->getData();
			$pjExtraItemModel = pjExtraItemModel::factory();
			foreach ($extra_arr as $k => $extra) {
				$extra_arr[$k]['i18n'] = $pjMultiLangModel->reset()->getMultiLang($extra['id'], 'pjExtra');
				$extra_arr[$k]['extra_items'] = $pjExtraItemModel->reset()->where('t1.extra_id', $extra['id'])->orderBy('t1.price ASC')->findAll()->getData();
				foreach ($extra_arr[$k]['extra_items'] as $key => $val) {
					$extra_arr[$k]['extra_items'][$key]['i18n'] = $pjMultiLangModel->reset()->getMultiLang($val['id'], 'pjExtraItem');
				}
			}
			$this->set('extra_arr', $extra_arr);
			$attr_arr = array();
			// Do not change col_name, direction
			$a_arr = pjAttributeModel::factory()
				->select('t1.id, t1.product_id, t1.parent_id, t1.hash, t2.content AS name')
				->join('pjMultiLang', "t2.model='pjAttribute' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='" . $this->getLocaleId() . "'", 'left outer')
				->where('t1.product_id', $arr['id'])
				->orderBy('t1.`order_group`, `order_item` ASC')->findAll()->getData();

			foreach ($a_arr as $attr) {
				$attr['i18n'] = $pjMultiLangModel->reset()->getMultiLang($attr['id'], 'pjAttribute');
				if ((int) $attr['parent_id'] === 0) {
					$attr_arr[$attr['id']] = $attr;
				} else {
					if (!isset($attr_arr[$attr['parent_id']]['child'])) {
						$attr_arr[$attr['parent_id']]['child'] = array();
					}
					$attr_arr[$attr['parent_id']]['child'][] = $attr;
				}
			}
			$this->set('attr_arr', array_values($attr_arr));

			$stock_arr = pjStockModel::factory()
				->select('t1.*, t2.small_path')
				->join('pjGallery', 't2.id=t1.image_id', 'left outer')
				->where('t1.product_id', $arr['id'])
				->findAll()
				->getData();

			$pjStockAttributeModel = pjStockAttributeModel::factory();
			foreach ($stock_arr as $k => $stock) {
				$stock_arr[$k]['attrs'] = $pjStockAttributeModel->reset()
					->where('t1.stock_id', $stock['id'])
					->orderBy('t1.attribute_id ASC')
					->findAll()
					->getDataPair('attribute_parent_id', 'attribute_id');
			}
			$this->set('stock_arr', $stock_arr);

			$this->setLocalesData();

			$gallery_arr = pjGalleryModel::factory()
				->where('t1.foreign_id', $this->_get->toInt('id'))
				->orderBy('ISNULL(t1.sort), t1.sort ASC, t1.id ASC')
				->findAll()
				->getData();
			$this->set('gallery_arr', $gallery_arr);

			pjGalleryModel::factory()->where('foreign_id', $this->_get->toInt('id'))->modifyAll(array('model' => 'pjProduct'));

			$this->set('has_update', pjAuth::factory('pjAdminProducts', 'pjActionUpdate')->hasAccess());

			# Gallery plugin
			$this->appendCss('pj-gallery.css', pjObject::getConstant('pjGallery', 'PLUGIN_CSS_PATH'));
			$this->appendJs('ajaxupload.js', PJ_THIRD_PARTY_PATH . 'ajaxupload/');
			$this->appendJs('jquery.gallery.js', pjObject::getConstant('pjGallery', 'PLUGIN_JS_PATH'));

			$this->appendCss('css/select2.min.css', PJ_THIRD_PARTY_PATH . 'select2/');
			$this->appendJs('js/select2.full.min.js', PJ_THIRD_PARTY_PATH . 'select2/');
			$this->appendCss('admin-products-update.css', PJ_CSS_PATH);

			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
			$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjQueryAutocompleter.min.js')
				->appendJs('pjAdminProducts.js');
		}
	}

	private function pjActionTurnI18n($data, $key, $id, $index = NULL, $new_key = NULL)
	{
		$arr = array();
		$arr_index = is_null($new_key) ? $key : $new_key;
		foreach ($data as $locale => $locale_arr) {
			$arr[$locale] = array(
				$arr_index => is_null($index) ?
					(isset($locale_arr[$key]) && isset($locale_arr[$key][$id]) ? $locale_arr[$key][$id] : NULL) : (isset($locale_arr[$key]) && isset($locale_arr[$key][$id]) && isset($locale_arr[$key][$id][$index]) ? $locale_arr[$key][$id][$index] : NULL)
			);
		}

		return $arr;
	}

	public function pjActionLoadImages()
	{
		$this->setAjax(true);

		if ($this->isXHR() && $this->isLoged()) {
			$arr = array();
			if ($this->_get->check('product_id') && $this->_get->toInt('product_id') > 0) {
				$arr = pjGalleryModel::factory()->where('t1.foreign_id', $this->_get->toInt('product_id'))->orderBy('ISNULL(t1.sort), t1.sort ASC, t1.id ASC')->findAll()->getData();
			} elseif ($this->_get->check('hash') && $this->_get->toString('hash') != '') {
				$arr = pjGalleryModel::factory()->where('t1.hash', $this->_get->toString('hash'))->orderBy('ISNULL(t1.sort), t1.sort ASC, t1.id ASC')->findAll()->getData();
			}

			$this->set('arr', $arr);
		}
	}

	public function pjActionCheckStockAttributes()
	{
		$this->setAjax(true);

		if ($this->isXHR() && $this->isLoged()) {
			$i_arr = array();
			if ($stock_qty_arr = $this->_post->toArray('stock_qty')) {
				$stock_image_id_arr = $this->_post->toArray('stock_image_id');
				$stock_attribute_arr = $this->_post->toArray('stock_attribute');
				foreach ($stock_qty_arr as $k => $whatever) {
					$tmp = array();
					if (isset($stock_attribute_arr[$k])) {
						foreach ($stock_attribute_arr[$k] as $attr_parent_id => $attr_id) {
							$tmp[] = $stock_image_id_arr[$k] . ":" . $attr_parent_id . ":" . $attr_id;
						}
					}
					asort($tmp);
					$str_key = join("|", $tmp);
					if (in_array($str_key, $i_arr)) {
						pjAppController::jsonResponse(array('status' => 'ERR'));
						break;
					}
					$i_arr[] = $str_key;
				}
			}
			pjAppController::jsonResponse(array('status' => 'OK'));
		}
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

	// public function pjActionImportAllProduct()
	// {
	// 	$this->checkLogin();
	// 	if (!empty($_FILES['file']['tmp_name'])) {

	// 		$file = $_FILES['file']['tmp_name'];
	// 		$handle = fopen($file, "r");

	// 		$header = fgetcsv($handle);
	// 		$company_id = $_SESSION[$this->defaultCompany]['id'];
	// 		$csv_products = [];
	// 		$csv_variants = [];

	// 		// $this->writeLog("IMPORT STARTED");

	// 		while (($row = fgetcsv($handle)) !== false) {

	// 			$data = array_combine($header, $row);
	// 			$model = $data['model'];
	// 			$article = $data['article_number'];

	// 			$csv_products[] = $model;

	// 			if (!isset($csv_variants[$model])) {
	// 				$csv_variants[$model] = [];
	// 			}

	// 			$csv_variants[$model][] = $article;

	// 			// $this->writeLog("CSV ROW------------");
	// 			// $this->writeLog($data);

	// 			$product_id = $this->getOrCreateProduct(
	// 				$data['model'],
	// 				$data['model_name'],
	// 				$data['sku'],
	// 				$data['status'],
	// 				$company_id
	// 			);

	// 			// $this->writeLog("PRODUCT CREATED ID: " . $product_id);

	// 			$this->assignBrand($product_id, $data['brand'], $company_id);
	// 			$this->assignCategory($product_id, $data['category'], $company_id);

	// 			$this->updateProductLang(
	// 				$product_id,
	// 				$data['name_en'],
	// 				$data['short_desc_en'],
	// 				$data['full_description_en']
	// 			);
	// 			$img_id = $this->importGalleryImages($product_id, $data);


	// 			$this->addStockViaUpdate(
	// 				$product_id,
	// 				$data['size'],
	// 				$data['color'],
	// 				$data['article_number'],
	// 				$data['article_name'],
	// 				$data['ean'],
	// 				$data['qty'],
	// 				$data['price'],
	// 				$img_id
	// 			);
	// 		}
	// 		fclose($handle);
	// 		// $this->writeLog("CHECK MISSING VARIANTS");

	// 		$products = pjProductModel::factory()
	// 			->select("id,model")
	// 			->where('company_id', $company_id)
	// 			->findAll()
	// 			->getData();

	// 		foreach ($products as $product) {

	// 			$product_id = $product['id'];
	// 			$model = $product['model'];

	// 			$stocks = pjStockModel::factory()
	// 				->where('product_id', $product_id)
	// 				->findAll()
	// 				->getData();

	// 			foreach ($stocks as $stock) {

	// 				if (!isset($csv_variants[$model]) || !in_array($stock['article_number'], $csv_variants[$model])) {

	// 					// $this->writeLog("DISABLE VARIANT " . $stock['article_number']);

	// 					pjStockModel::factory()
	// 						->reset()
	// 						->set('id', $stock['id'])
	// 						->modify(['qty' => 0]);
	// 				}
	// 			}
	// 		}
	// 		// $this->writeLog("CHECK MISSING PRODUCTS");

	// 		$db_products = pjProductModel::factory()
	// 			->select("id,model")
	// 			->where('company_id', $company_id)
	// 			->findAll()
	// 			->getData();

	// 		foreach ($db_products as $p) {

	// 			if (!in_array($p['model'], $csv_products)) {

	// 				// $this->writeLog("DISABLE PRODUCT " . $p['model']);

	// 				pjProductModel::factory()
	// 					->reset()
	// 					->set('id', $p['id'])
	// 					->modify(['status' => 0]);

	// 				pjStockModel::factory()
	// 					->where('product_id', $p['id'])
	// 					->modify(['qty' => 0]);
	// 			}
	// 		}
	// 		// $this->writeLog("IMPORT FINISHED");

	// 		pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminProducts&action=pjActionIndex&err=AP00");
	// 	}
	// }
	// public function pjActionImportAllProduct()
	// {
	// 	$this->checkLogin();

	// 	$limit = 10;
	// 	$offset = $this->_get->check('offset') ? $this->_get->toInt('offset') : 0;

	// 	// FIRST REQUEST → READ CSV
	// 	if ($offset == 0) {

	// 		if (empty($_FILES['file']['tmp_name'])) {
	// 			return;
	// 		}

	// 		$file = $_FILES['file']['tmp_name'];
	// 		$handle = fopen($file, "r");

	// 		$header = fgetcsv($handle);

	// 		$csv_data = [];

	// 		while (($row = fgetcsv($handle)) !== false) {
	// 			$csv_data[] = array_combine($header, $row);
	// 		}

	// 		fclose($handle);
	// 		// $this->writeLog('------------file-------' . print_r($csv_data, true));

	// 		$_SESSION['import_csv_data'] = $csv_data;
	// 		$_SESSION['csv_products'] = [];
	// 		$_SESSION['csv_variants'] = [];
	// 	}

	// 	if (empty($_SESSION['import_csv_data'])) {
	// 		return;
	// 	}

	// 	$company_id = $_SESSION[$this->defaultCompany]['id'];

	// 	$csv_data = $_SESSION['import_csv_data'];
	// 	$csv_products = &$_SESSION['csv_products'];
	// 	$csv_variants = &$_SESSION['csv_variants'];

	// 	$rows = array_slice($csv_data, $offset, $limit);

	// 	foreach ($rows as $data) {

	// 		$model = $data['model'];
	// 		$article = $data['article_number'];

	// 		$csv_products[] = $model;

	// 		if (!isset($csv_variants[$model])) {
	// 			$csv_variants[$model] = [];
	// 		}

	// 		$csv_variants[$model][] = $article;

	// 		$product_id = $this->getOrCreateProduct(
	// 			$data['model'],
	// 			$data['model_name'],
	// 			$data['sku'],
	// 			$data['status'],
	// 			$company_id
	// 		);

	// 		$this->assignBrand($product_id, $data['brand'], $company_id);
	// 		$this->assignCategory($product_id, $data['category'], $company_id);

	// 		$this->updateProductLang(
	// 			$product_id,
	// 			$data['name_en'],
	// 			$data['short_desc_en'],
	// 			$data['full_description_en']
	// 		);

	// 		$img_id = $this->importGalleryImages($product_id, $data);

	// 		$this->addStockViaUpdate(
	// 			$product_id,
	// 			$data['size'],
	// 			$data['color'],
	// 			$data['article_number'],
	// 			$data['article_name'],
	// 			$data['ean'],
	// 			$data['qty'],
	// 			$data['price'],
	// 			$img_id
	// 		);
	// 	}

	// 	$next_offset = $offset + $limit;

	// 	// PROCESS NEXT BATCH
	// 	if ($next_offset < count($csv_data)) {

	// 		pjUtil::redirect(
	// 			$_SERVER['PHP_SELF'] .
	// 				"?controller=pjAdminProducts&action=pjActionImportAllProduct&offset=" . $next_offset
	// 		);
	// 	}

	// 	// FINAL PROCESSING (only once after import)
	// 	$products = pjProductModel::factory()
	// 		->select("id,model")
	// 		->where('company_id', $company_id)
	// 		->findAll()
	// 		->getData();

	// 	foreach ($products as $product) {

	// 		$product_id = $product['id'];
	// 		$model = $product['model'];

	// 		$stocks = pjStockModel::factory()
	// 			->where('product_id', $product_id)
	// 			->findAll()
	// 			->getData();

	// 		foreach ($stocks as $stock) {

	// 			if (!isset($csv_variants[$model]) || !in_array($stock['article_number'], $csv_variants[$model])) {

	// 				pjStockModel::factory()
	// 					->reset()
	// 					->set('id', $stock['id'])
	// 					->modify(['qty' => 0]);
	// 			}
	// 		}
	// 	}

	// 	$db_products = pjProductModel::factory()
	// 		->select("id,model")
	// 		->where('company_id', $company_id)
	// 		->findAll()
	// 		->getData();

	// 	foreach ($db_products as $p) {

	// 		if (!in_array($p['model'], $csv_products)) {

	// 			pjProductModel::factory()
	// 				->reset()
	// 				->set('id', $p['id'])
	// 				->modify(['status' => 0]);

	// 			pjStockModel::factory()
	// 				->where('product_id', $p['id'])
	// 				->modify(['qty' => 0]);
	// 		}
	// 	}

	// 	// CLEAR SESSION
	// 	unset($_SESSION['import_csv_data']);
	// 	unset($_SESSION['csv_products']);
	// 	unset($_SESSION['csv_variants']);

	// 	pjUtil::redirect(
	// 		$_SERVER['PHP_SELF'] .
	// 			"?controller=pjAdminProducts&action=pjActionIndex&err=AP00"
	// 	);
	// }
	// public function pjActionImportAllProduct_latest()
	// {
	// 	$this->checkLogin();

	// 	$limit = 10;
	// 	$offset = $this->_get->check('offset') ? $this->_get->toInt('offset') : 0;

	// 	// FIRST REQUEST → READ CSV
	// 	if ($offset == 0) {

	// 		if (empty($_FILES['file']['tmp_name'])) {
	// 			return;
	// 		}

	// 		$file = $_FILES['file']['tmp_name'];
	// 		$handle = fopen($file, "r");

	// 		$header = fgetcsv($handle);

	// 		$csv_data = [];

	// 		while (($row = fgetcsv($handle)) !== false) {
	// 			$csv_data[] = array_combine($header, $row);
	// 		}

	// 		fclose($handle);

	// 		// $this->writeLog("CSV FILE LOADED");

	// 		$_SESSION['import_csv_data'] = $csv_data;
	// 		$_SESSION['csv_products'] = [];
	// 		$_SESSION['csv_variants'] = [];

	// 		// validation trackers
	// 		$_SESSION['sku_check'] = [];
	// 		$_SESSION['ean_check'] = [];
	// 	}

	// 	if (empty($_SESSION['import_csv_data'])) {
	// 		return;
	// 	}

	// 	$company_id = $_SESSION[$this->defaultCompany]['id'];

	// 	$csv_data = $_SESSION['import_csv_data'];
	// 	$csv_products = &$_SESSION['csv_products'];
	// 	$csv_variants = &$_SESSION['csv_variants'];

	// 	$rows = array_slice($csv_data, $offset, $limit);

	// 	foreach ($rows as $data) {

	// 		$model = $data['model'];
	// 		$article = $data['article_number'];
	// 		$sku = $data['sku'];
	// 		$ean = $data['ean'];

	// 		// $this->writeLog("------ ROW START ------");
	// 		// $this->writeLog("MODEL: " . $model);
	// 		// $this->writeLog("SKU: " . $sku);
	// 		// $this->writeLog("EAN: " . $ean);

	// 		// $this->writeLog("SESSION SKU CHECK: " . print_r($_SESSION['sku_check'], true));
	// 		// $this->writeLog("SESSION EAN CHECK: " . print_r($_SESSION['ean_check'], true));

	// 		$csv_products[] = $model;

	// 		if (!isset($csv_variants[$model])) {
	// 			$csv_variants[$model] = [];
	// 		}

	// 		$csv_variants[$model][] = $article;

	// 		// =========================
	// 		// SKU VALIDATION
	// 		// =========================

	// 		if (isset($_SESSION['sku_check'][$sku])) {

	// 			// $this->writeLog("SKU FOUND IN SESSION");
	// 			// $this->writeLog("SESSION MODEL: " . $_SESSION['sku_check'][$sku]);
	// 			// $this->writeLog("CURRENT MODEL: " . $model);

	// 			if ($_SESSION['sku_check'][$sku] != $model) {

	// 				// $this->writeLog("ERROR: DUPLICATE SKU DETECTED");

	// 				die("ERROR: Duplicate SKU found for different model. SKU: " . $sku);
	// 			}
	// 		}

	// 		$_SESSION['sku_check'][$sku] = $model;


	// 		// =========================
	// 		// EAN VALIDATION
	// 		// =========================

	// 		if (isset($_SESSION['ean_check'][$ean])) {

	// 			// $this->writeLog("EAN FOUND IN SESSION");
	// 			// $this->writeLog("SESSION MODEL: " . $_SESSION['ean_check'][$ean]);
	// 			// $this->writeLog("CURRENT MODEL: " . $model);

	// 			if ($_SESSION['ean_check'][$ean] != $model) {

	// 				// $this->writeLog("ERROR: DUPLICATE EAN DETECTED");

	// 				die("ERROR: Duplicate EAN found for different model. EAN: " . $ean);
	// 			}
	// 		}

	// 		$_SESSION['ean_check'][$ean] = $model;

	// 		// $this->writeLog("VALIDATION PASSED");

	// 		// =========================
	// 		// PRODUCT CREATE / UPDATE
	// 		// =========================

	// 		$product_id = $this->getOrCreateProduct(
	// 			$data['model'],
	// 			$data['model_name'],
	// 			$data['sku'],
	// 			$data['status'],
	// 			$company_id
	// 		);

	// 		$this->assignBrand($product_id, $data['brand'], $company_id);
	// 		$this->assignCategory($product_id, $data['category'], $company_id);

	// 		$this->updateProductLang(
	// 			$product_id,
	// 			$data['name_en'],
	// 			$data['short_desc_en'],
	// 			$data['full_description_en']
	// 		);

	// 		$img_id = $this->importGalleryImages($product_id, $data);

	// 		$this->addStockViaUpdate(
	// 			$product_id,
	// 			$data['size'],
	// 			$data['color'],
	// 			$data['article_number'],
	// 			$data['article_name'],
	// 			$data['ean'],
	// 			$data['qty'],
	// 			$data['price'],
	// 			$img_id
	// 		);
	// 	}

	// 	$next_offset = $offset + $limit;

	// 	// NEXT BATCH
	// 	if ($next_offset < count($csv_data)) {

	// 		// $this->writeLog("NEXT BATCH OFFSET: " . $next_offset);

	// 		pjUtil::redirect(
	// 			$_SERVER['PHP_SELF'] .
	// 				"?controller=pjAdminProducts&action=pjActionImportAllProduct&offset=" . $next_offset
	// 		);
	// 	}

	// 	// $this->writeLog("FINAL CLEANUP START");

	// 	$products = pjProductModel::factory()
	// 		->select("id,model")
	// 		->where('company_id', $company_id)
	// 		->findAll()
	// 		->getData();

	// 	foreach ($products as $product) {

	// 		$product_id = $product['id'];
	// 		$model = $product['model'];

	// 		$stocks = pjStockModel::factory()
	// 			->where('product_id', $product_id)
	// 			->findAll()
	// 			->getData();

	// 		foreach ($stocks as $stock) {

	// 			if (!isset($csv_variants[$model]) || !in_array($stock['article_number'], $csv_variants[$model])) {

	// 				pjStockModel::factory()
	// 					->reset()
	// 					->set('id', $stock['id'])
	// 					->modify(['qty' => 0]);
	// 			}
	// 		}
	// 	}

	// 	$db_products = pjProductModel::factory()
	// 		->select("id,model")
	// 		->where('company_id', $company_id)
	// 		->findAll()
	// 		->getData();

	// 	foreach ($db_products as $p) {

	// 		if (!in_array($p['model'], $csv_products)) {

	// 			pjProductModel::factory()
	// 				->reset()
	// 				->set('id', $p['id'])
	// 				->modify(['status' => 0]);

	// 			pjStockModel::factory()
	// 				->where('product_id', $p['id'])
	// 				->modify(['qty' => 0]);
	// 		}
	// 	}

	// 	unset($_SESSION['import_csv_data']);
	// 	unset($_SESSION['csv_products']);
	// 	unset($_SESSION['csv_variants']);
	// 	unset($_SESSION['sku_check']);
	// 	unset($_SESSION['ean_check']);

	// 	// $this->writeLog("IMPORT FINISHED");

	// 	pjUtil::redirect(
	// 		$_SERVER['PHP_SELF'] .
	// 			"?controller=pjAdminProducts&action=pjActionIndex&err=AP00"
	// 	);
	// }
	public function pjActionImportAllProduct()
	{
		$this->setAjax(true);

		if (!$this->isXHR()) {
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Invalid request'));
		}

		$this->checkLogin();

		$limit = 10;
		$offset = isset($_POST['offset']) ? (int)$_POST['offset'] : 0;

		// FIRST REQUEST → LOAD CSV
		if ($offset == 0) {

			if (empty($_FILES['file']['tmp_name'])) {
				pjAppController::jsonResponse(array(
					'status' => 'ERR',
					'code' => 100,
					'text' => 'CSV file missing'
				));
			}

			$file = $_FILES['file']['tmp_name'];
			$handle = fopen($file, "r");

			$header = fgetcsv($handle);
			$csv_data = [];

			while (($row = fgetcsv($handle)) !== false) {
				$csv_data[] = array_combine($header, $row);
			}

			fclose($handle);

			$_SESSION['import_csv_data'] = $csv_data;
			$_SESSION['csv_products'] = [];
			$_SESSION['csv_variants'] = [];
			$_SESSION['sku_check'] = [];
			$_SESSION['ean_check'] = [];
		}

		if (empty($_SESSION['import_csv_data'])) {
			pjAppController::jsonResponse(array(
				'status' => 'ERR',
				'code' => 100,
				'text' => 'CSV session expired'
			));
		}

		$company_id = $_SESSION[$this->defaultCompany]['id'];

		$csv_data = $_SESSION['import_csv_data'];
		$csv_products = &$_SESSION['csv_products'];
		$csv_variants = &$_SESSION['csv_variants'];

		$rows = array_slice($csv_data, $offset, $limit);

		foreach ($rows as $data) {

			$model = $data['model'];
			$article = $data['article_number'];
			$sku = $data['sku'];
			$ean = $data['ean'];

			$csv_products[] = $model;

			if (!isset($csv_variants[$model])) {
				$csv_variants[$model] = [];
			}

			$csv_variants[$model][] = $article;

			// =========================
			// SKU VALIDATION
			// =========================
			if (isset($_SESSION['sku_check'][$sku])) {

				if ($_SESSION['sku_check'][$sku] != $model) {

					pjAppController::jsonResponse(array(
						'status' => 'ERR',
						'code' => 100,
						'text' => 'Duplicate SKU found for different model: ' . $sku
					));
				}
			}

			$_SESSION['sku_check'][$sku] = $model;


			// =========================
			// EAN VALIDATION
			// =========================
			if (isset($_SESSION['ean_check'][$ean])) {

				if ($_SESSION['ean_check'][$ean] != $model) {

					pjAppController::jsonResponse(array(
						'status' => 'ERR',
						'code' => 100,
						'text' => 'Duplicate EAN found for different model: ' . $ean
					));
				}
			}

			$_SESSION['ean_check'][$ean] = $model;


			// =========================
			// PRODUCT CREATE / UPDATE
			// =========================

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

			$img_id = $this->importGalleryImages($product_id, $data);

			$this->addStockViaUpdate(
				$product_id,
				$data['size'],
				$data['color'],
				$data['article_number'],
				$data['article_name'],
				$data['ean'],
				$data['qty'],
				$data['price'],
				$img_id
			);
		}

		$next_offset = $offset + $limit;

		// CONTINUE NEXT BATCH
		if ($next_offset < count($csv_data)) {

			pjAppController::jsonResponse(array(
				'status' => 'OK',
				'code' => 200,
				'text' => $next_offset
			));
		}

		// FINAL CLEANUP
		$products = pjProductModel::factory()
			->select("id,model")
			->where('company_id', $company_id)
			->findAll()
			->getData();

		foreach ($products as $product) {

			$product_id = $product['id'];
			$model = $product['model'];

			$stocks = pjStockModel::factory()
				->where('product_id', $product_id)
				->findAll()
				->getData();

			foreach ($stocks as $stock) {

				if (!isset($csv_variants[$model]) || !in_array($stock['article_number'], $csv_variants[$model])) {

					pjStockModel::factory()
						->reset()
						->set('id', $stock['id'])
						->modify(['qty' => 0]);
				}
			}
		}

		unset($_SESSION['import_csv_data']);
		unset($_SESSION['csv_products']);
		unset($_SESSION['csv_variants']);
		unset($_SESSION['sku_check']);
		unset($_SESSION['ean_check']);

		pjAppController::jsonResponse(array(
			'status' => 'OK',
			'code' => 200,
			'text' => 'Import finished'
		));
	}
	private function addStockViaUpdate($product_id, $size, $color, $article_number, $article_name, $ean, $qty, $price, $img_id)
	{
		$company_id = $_SESSION[$this->defaultCompany]['id'];

		$article_number = trim($article_number);

		// $this->writeLog("ADDING STOCK FOR PRODUCT " . $product_id . " ARTICLE " . $article_number);

		$size_attr  = $this->getOrCreateAttribute($product_id, 'Size', $size);
		$color_attr = $this->getOrCreateAttribute($product_id, 'Color', $color);

		if (!$size_attr || !$color_attr) {
			// $this->writeLog("ATTRIBUTE ERROR");
			return;
		}

		// find existing stock
		$existing = pjStockModel::factory()
			->where('product_id', $product_id)
			->where('TRIM(article_number)', $article_number)
			->limit(1)
			->findAll()
			->getData();

		if (!empty($existing)) {

			$stock_id = $existing[0]['id'];

			// $this->writeLog("STOCK EXISTS → UPDATE ID " . $stock_id);

			pjStockModel::factory()
				->reset()
				->set('id', $stock_id)
				->modify([
					'article_name' => $article_name,
					'ean' => $ean,
					'qty' => $qty,
					'price' => $price
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

			// $this->writeLog("CREATE NEW STOCK");

			$stock_id = pjStockModel::factory()
				->set('company_id', $company_id)
				->set('product_id', $product_id)
				->set('image_id', 1)
				->set('article_name', $article_name)
				->set('article_number', $article_number)
				->set('ean', $ean)
				->set('qty', $qty)
				->set('price', $price)
				->insert()
				->getInsertId();

			// $this->writeLog("STOCK CREATED ID " . $stock_id);
		}

		if (!$stock_id) {
			// $this->writeLog("STOCK ERROR");
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

		// $this->writeLog("STOCK ATTRIBUTES LINKED");
	}

	private function downloadImage($url, $type = 'source')
	{
		if (empty($url)) {
			return false;
		}

		$imageData = @file_get_contents($url);

		if ($imageData === false) {
			// $this->writeLog("IMAGE DOWNLOAD FAILED");
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

		// $this->writeLog("IMAGE SAVED: " . $localPath);

		return str_replace(PJ_INSTALL_PATH, '', $localPath);
	}

	private function importGalleryImages($product_id, $data)
	{
		// $this->writeLog("START IMAGE IMPORT FOR PRODUCT " . $product_id);

		if (empty($data['image'])) {
			// $this->writeLog("NO IMAGE FIELD FOUND");
			return null;
		}

		$images = explode(',', $data['image']);

		$GalleryModel = pjGalleryModel::factory();
		$Image = new pjImage();

		foreach ($images as $img) {

			$img = trim($img);
			if (!$img) {
				// $this->writeLog("EMPTY IMAGE URL SKIPPED");
				continue;
			}

			// $this->writeLog("DOWNLOAD IMAGE: " . $img);

			$tmp_file = $this->downloadImage($img, 'source');

			if (!$tmp_file) {
				// $this->writeLog("DOWNLOAD FAILED");
				continue;
			}

			// $this->writeLog("TEMP FILE: " . $tmp_file);

			$full_path = PJ_INSTALL_PATH . $tmp_file;

			// $this->writeLog("FULL PATH: " . $full_path);

			if (!$Image->loadImage($full_path)) {
				// $this->writeLog("IMAGE LOAD FAILED");
				continue;
			}

			// $this->writeLog("IMAGE LOADED SUCCESS");

			$hash = md5(uniqid(rand(), true));

			$source_path = PJ_UPLOAD_PATH . 'source/' . $product_id . '_' . $hash . '.' . $Image->getExtension();

			// $this->writeLog("SOURCE PATH: " . $source_path);

			if (!$Image->saveImage(PJ_INSTALL_PATH . $source_path)) {
				// $this->writeLog("SOURCE SAVE FAILED");
				continue;
			}

			// $this->writeLog("SOURCE IMAGE SAVED");

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

			// $this->writeLog("BUILD THUMBNAILS");

			$data_insert = array_merge(
				$data_insert,
				$this->pjActionBuildFromSource($Image, $data_insert)
			);

			// $this->writeLog("THUMBNAIL DATA BEFORE INSERT:");
			// $this->writeLog($data_insert);

			$insert_id = $GalleryModel
				->reset()
				->setAttributes($data_insert)
				->insert()
				->getInsertId();

			// $this->writeLog("GALLERY INSERT ID: " . $insert_id);

			return $insert_id;
		}

		// $this->writeLog("NO IMAGE INSERTED");

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

		// $this->writeLog("START BUILD THUMBNAILS");

		// SMALL
		$Image->loadImage($source_full);

		if ($Image->resize(80, 106)) {

			if ($Image->saveImage($small_full)) {

				$size = getimagesize($small_full);

				$arr['small_path']   = $small_path;
				$arr['small_size']   = filesize($small_full);
				$arr['small_width']  = $size[0];
				$arr['small_height'] = $size[1];

				// $this->writeLog("SMALL CREATED SIZE: ".$arr['small_size']);
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

				// $this->writeLog("MEDIUM CREATED SIZE: ".$arr['medium_size']);
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

				// $this->writeLog("LARGE CREATED SIZE: ".$arr['large_size']);
			}
		}

		// $this->writeLog("THUMBNAIL DATA GENERATED:");
		// $this->writeLog($arr);

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
			'status' => $status,
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
			// $this->writeLog("BRAND EMPTY - SKIPPED");
			return;
		}

		$brand = trim($brand);

		// $this->writeLog("CHECK BRAND: " . $brand);

		$brand_data = pjBrandModel::factory()
			->join('pjMultiLang', 't2.foreign_id=t1.id AND t2.model="pjBrand"', 'inner')
			->where('t2.field', 'name')
			->where('t2.content', $brand)
			->where('t1.company_id', $company_id)

			->limit(1)
			->findAll()
			->getData();


		// BRAND NOT FOUND → CREATE
		if (empty($brand_data)) {

			// $this->writeLog("BRAND NOT FOUND → CREATING: " . $brand);

			$parent_id = 1;

			$data = [
				'company_id' => $company_id,
				'parent_id' => 1
			];

			$brand_id = pjBrandModel::factory()->saveNode($data, $parent_id);

			if ($brand_id) {

				pjMultiLangModel::factory()->saveMultiLang([
					1 => ['name' => $brand]
				], $brand_id, 'pjBrand', 'data');

				// $this->writeLog("BRAND CREATED ID: " . $brand_id . " NAME: " . $brand);
			} else {

				// $this->writeLog("BRAND CREATE FAILED: " . $brand);
				return;
			}
		} else {

			$brand_id = $brand_data[0]['id'];

			// $this->writeLog("BRAND FOUND ID: " . $brand_id . " NAME: " . $brand);
		}

		// REMOVE OLD BRAND
		pjProductBrandModel::factory()
			->where('product_id', $product_id)
			->eraseAll();

		// ASSIGN BRAND
		pjProductBrandModel::factory()
			->set('product_id', $product_id)
			->set('brand_id', $brand_id)
			->set('company_id', $company_id)
			->insert();

		// $this->writeLog("BRAND ASSIGNED → PRODUCT: " . $product_id . " BRAND: " . $brand);
	}
	private function assignCategory($product_id, $category_name, $company_id)
	{
		if (empty($category_name)) {
			// $this->writeLog("CATEGORY EMPTY - SKIPPED");
			return;
		}

		$category_name = trim($category_name);

		// $this->writeLog("CHECK CATEGORY: " . $category_name);


		$category = pjCategoryModel::factory()
			->join('pjMultiLang', 't2.foreign_id=t1.id AND t2.model="pjCategory"', 'inner')
			->where('t2.field', 'name')
			->where('t2.content', $category_name)
			->limit(1)
			->findAll()
			->getData();
		// CATEGORY NOT FOUND → CREATE
		if (empty($category)) {

			// $this->writeLog("CATEGORY NOT FOUND → CREATING: " . $category_name);

			$parent_id = 1;

			$data = [
				'company_id' => $company_id,
				'parent_id' => 1
			];

			$category_id = pjCategoryModel::factory()->saveNode($data, $parent_id);

			if ($category_id) {

				pjMultiLangModel::factory()->saveMultiLang([
					1 => ['name' => $category_name]
				], $category_id, 'pjCategory', 'data');

				// $this->writeLog("CATEGORY CREATED ID: " . $category_id . " NAME: " . $category_name);
			} else {

				// $this->writeLog("CATEGORY CREATE FAILED: " . $category_name);
				return;
			}
		} else {

			$category_id = $category[0]['id'];

			// $this->writeLog("CATEGORY FOUND ID: " . $category_id . " NAME: " . $category_name);
		}

		// REMOVE OLD CATEGORY
		pjProductCategoryModel::factory()
			->where('product_id', $product_id)
			->eraseAll();

		// ASSIGN CATEGORY
		pjProductCategoryModel::factory()
			->set('product_id', $product_id)
			->set('category_id', $category_id)
			->set('company_id', $company_id)
			->insert();

		// $this->writeLog("CATEGORY ASSIGNED → PRODUCT: " . $product_id . " CATEGORY: " . $category_name);
	}
	private function updateProductLang($product_id, $name, $short, $full)
	{
		// $this->writeLog("UPDATE PRODUCT LANG FOR PRODUCT " . $product_id);

		$data = [
			1 => [
				'name' => $name,
				'short_desc' => $short,
				'full_desc' => $full
			]
		];

		// $this->writeLog("LANG DATA");
		// $this->writeLog($data);

		pjMultiLangModel::factory()->updateMultiLang($data, $product_id, 'pjProduct');
	}
	private function getOrCreateAttribute($product_id, $group_name, $item_name)
	{
		$company_id = $_SESSION[$this->defaultCompany]['id'];

		// $this->writeLog("CHECK GROUP: " . $group_name);

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
			// $this->writeLog("CREATE GROUP " . $group_name);

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

		// $this->writeLog("GROUP ID " . $group_id);

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
			// $this->writeLog("CREATE ITEM " . $item_name);

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

		// $this->writeLog("ITEM ID " . $item_id);

		return [
			'parent_id' => $group_id,
			'id' => $item_id
		];
	}
}
