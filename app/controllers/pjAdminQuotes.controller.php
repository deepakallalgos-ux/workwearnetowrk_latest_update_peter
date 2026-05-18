<?php
if (!defined("ROOT_PATH")) {
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminQuotes extends pjAdmin
{
	public function pjActionCheckUID()
	{
		$company_id = $_SESSION[$this->defaultCompany]['id'];

		$this->setAjax(true);

		if (!$this->isXHR()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}

		if (!$this->_get->check('uuid') || $this->_get->toString('uuid') == '') {
			echo 'false';
			exit;
		}
		$pjQuoteModel = pjQuoteModel::factory()->where('t1.uuid', $this->_get->toString('uuid'));
		if ($this->_get->check('id') && $this->_get->toInt('id') > 0) {
			$pjQuoteModel->where('t1.id !=', $this->_get->toInt('id'));
		}
		if (isset($company_id) && (int)$company_id > 0) {
			// $pjQuoteModel->where('t1.company_id', $company_id);
		}
		echo $pjQuoteModel->findCount()->getData() == 0 ? 'true' : 'false';
		exit;
	}

	public function pjActionDeleteQuote()
	{
		$this->setAjax(true);
		$company_id = $_SESSION[$this->defaultCompany]['id'];

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
		$pjQuoteModel = pjQuoteModel::factory();

		$arr = $pjQuoteModel->find($id)->getData();
		if (!$arr) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Quote not found.'));
		}
		if ($pjQuoteModel->reset()->set('id', $id)->erase()->getAffectedRows() == 1) {
			$pjQuoteStockModel = pjQuoteStockModel::factory();
			$os_arr = $pjQuoteStockModel->where('quote_id', $id)->findAll()->getDataPair('stock_id', 'qty');
			if (!empty($os_arr)) {
				$pjQuoteStockModel->reset()->where('quote_id', $id)->eraseAll();
				$pjStockModel = pjStockModel::factory();
				foreach ($os_arr as $stock_id => $qty) {
					$pjStockModel->reset()->set('id', $stock_id)->modify(array('qty' => ":qty + " . (int) $qty));
				}
			}
			pjQuoteExtraModel::factory()->where('quote_id', $id)->eraseAll();
			self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Quote has been deleted'));
		} else {
			self::jsonResponse(array('status' => 'ERR', 'code' => 105, 'text' => 'Quote has not been deleted.'));
		}
		exit;
	}

	public function pjActionDeleteQuoteBulk()
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

		pjQuoteModel::factory()->whereIn('id', $record)->eraseAll();
		$pjQuoteStockModel = pjQuoteStockModel::factory();
		$os_arr = $pjQuoteStockModel->whereIn('quote_id', $record)->findAll()->getData();
		if (!empty($os_arr)) {
			$pjQuoteStockModel->reset()->whereIn('quote_id', $record)->eraseAll();
			$pjStockModel = pjStockModel::factory();
			foreach ($os_arr as $item) {
				$pjStockModel->reset()->set('id', $item['stock_id'])->modify(array('qty' => ":qty + " . (int) $item['qty']));
			}
		}
		pjQuoteExtraModel::factory()->whereIn('quote_id', $record)->eraseAll();

		self::jsonResponse(array('status' => 'OK'));
	}

	public function pjActionExportQuote()
	{
		$this->checkLogin();

		if ($record = $this->_post->toArray('record')) {
			$export_fields = array(
				'id',
				'uuid',
				'client_id',
				'address_id',
				'locale_id',
				'tax_id',
				'status',
				'payment_method',
				'txn_id',
				'processed_on',
				'price',
				'discount',
				'insurance',
				'shipping',
				'tax',
				'total',
				'voucher',
				'notes',
				'cc_type',
				'cc_num',
				'cc_exp_month',
				'cc_exp_year',
				'cc_code',
				'created',
				'ip',
				'same_as',
				's_name',
				's_country_id',
				's_state',
				's_city',
				's_zip',
				's_address_1',
				's_address_2',
				'b_name',
				'b_country_id',
				'b_state',
				'b_city',
				'b_zip',
				'b_address_1',
				'b_address_2'
			);

			$pjQuoteModel = pjQuoteModel::factory();
			$pjQuoteStockModel = pjQuoteStockModel::factory();
			$pjQuoteExtraModel = pjQuoteExtraModel::factory();
			$pjProductModel = pjProductModel::factory();
			$pjExtraModel = pjExtraModel::factory();
			$pjExtraItemModel = pjExtraItemModel::factory();

			$separator = '~:~';
			$sep = '|';

			$arr = $pjQuoteModel->reset()->select(sprintf(
				"%1\$s, 
					(SELECT GROUP_CONCAT(CONCAT(COALESCE(`id`, '-1'), '$sep', COALESCE(`product_id`, '-1'), '$sep', COALESCE(`qty`, '0'), '$sep', COALESCE(`price`, '0')) SEPARATOR '$separator') FROM `%2\$s` WHERE `quote_id` = `t1`.`id` LIMIT 1) AS `product_ids`,
					(SELECT GROUP_CONCAT(CONCAT(COALESCE(`id`, '-1'), '$sep', COALESCE(`quote_stock_id`, '-1'), '$sep', COALESCE(`extra_id`, '-1'), '$sep', COALESCE(`extra_item_id`, '-1')) SEPARATOR '$separator') FROM `%3\$s` WHERE `quote_id` = `t1`.`id` LIMIT 1) AS `extra_ids` 
					",
				join(", ", $export_fields),
				$pjQuoteStockModel->getTable(),
				$pjQuoteExtraModel->getTable()
			))
				->whereIn('id', $record)
				->findAll()->getData();

			$product_arr = $pjProductModel->reset()
				->select("t1.*, t2.content AS name")
				->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.id AND t2.locale='" . $this->getLocaleId() . "' AND t2.field='name'", 'left outer')
				->findAll()
				->getDataPair('id', null);

			$extra_arr = $pjExtraModel->reset()
				->select('t1.*, t2.content AS name, t3.content AS title')
				->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.locale='" . $this->getLocaleId() . "' AND t2.field='extra_name'", 'left outer')
				->join('pjMultiLang', "t3.model='pjExtra' AND t3.foreign_id=t1.id AND t3.locale='" . $this->getLocaleId() . "' AND t3.field='extra_title'", 'left outer')
				->orderBy('`title` ASC, `name` ASC')
				->findAll()
				->getDataPair('id', null);

			$extra_item_arr = $pjExtraItemModel->reset()
				->select('t1.*, t2.content AS name')
				->join('pjMultiLang', "t2.model='pjExtraItem' AND t2.foreign_id=t1.id AND t2.locale='" . $this->getLocaleId() . "' AND t2.field='extra_name'", 'left outer')
				->orderBy('t1.price ASC')
				->findAll()
				->getDataPair('id', null);
			$record = $this->_post->toArray('record');
			$_os_arr = pjQuoteStockModel::factory()
				->select("t1.*, t2.sku, t3.content AS name,
					(SELECT GROUP_CONCAT(CONCAT_WS('_', `attribute_id`, `attribute_parent_id`))
						FROM `" . pjStockAttributeModel::factory()->getTable() . "`
						WHERE `stock_id` = `t1`.`stock_id`
						LIMIT 1) AS `attr`")
				->join('pjProduct', 't2.id=t1.product_id', 'left outer')
				->join('pjMultiLang', "t3.model='pjProduct' AND t3.foreign_id=t2.id AND t3.field='name' AND t3.locale='" . $this->getLocaleId() . "'", 'left outer')
				->whereIn('t1.quote_id', $record)
				->findAll()
				->getData();
			$new_os_arr = array();
			$att_product_id = array();
			foreach ($_os_arr as $item) {
				$att_product_id[] = $item['product_id'];
				$new_os_arr[$item['quote_id']][] = $item;
			}
			$attr_arr = $a_arr = array();
			if (!empty($att_product_id)) {
				$a_arr = pjAttributeModel::factory()
					->select('t1.id, t1.product_id, t1.parent_id, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjAttribute' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='" . $this->getLocaleId() . "'", 'left outer')
					->whereIn('t1.product_id', $att_product_id)
					->orderBy('t1.parent_id ASC, `name` ASC')
					->findAll()
					->getData();
			}
			foreach ($a_arr as $attr) {
				if ((int) $attr['parent_id'] === 0) {
					$attr_arr[$attr['id']] = $attr;
				} else {
					if (!isset($attr_arr[$attr['parent_id']]['child'])) {
						$attr_arr[$attr['parent_id']]['child'] = array();
					}
					$attr_arr[$attr['parent_id']]['child'][] = $attr;
				}
			}
			$attr_arr = array_values($attr_arr);
			$export_attr_arr = array();
			foreach ($new_os_arr as $quote_id => $os_arr) {
				foreach ($os_arr as $item) {
					$ex_attr_arr = array();
					if (isset($item['attr']) && !empty($item['attr'])) {
						$at = array();
						$a = explode(",", $item['attr']);
						foreach ($a as $v) {
							$t = explode("_", $v);
							$at[$t[1]] = $t[0];
						}
						foreach ($at as $attr_parent_id => $attr_id) {
							foreach ($attr_arr as $attr) {
								if ($attr['id'] == $attr_parent_id) {
									foreach ($attr['child'] as $child) {
										if ($child['id'] == $attr_id) {
											$ex_attr_arr[] = pjSanitize::html($attr['name']) . ': ' . pjSanitize::html($child['name']);
											break;
										}
									}
								}
							}
						}
					}
					$export_attr_arr[$quote_id][$item['product_id']][$item['id']] = $ex_attr_arr;
				}
			}

			$data = array();

			foreach ($arr as $k => $v) {
				$product_ids = $v['product_ids'];
				$extra_ids = $v['extra_ids'];

				$quote_product_arr = array();
				$product_ids_arr = explode($separator, $product_ids);
				foreach ($product_ids_arr as $str) {
					list($quote_stock_id, $product_id, $qty, $price) = explode($sep, $str);
					if (!isset($quote_product_arr[$quote_stock_id])) {
						$quote_product_arr[$quote_stock_id] = array();
					}
					$quote_product_arr[$quote_stock_id]['product_id'] = $product_id;
					$quote_product_arr[$quote_stock_id]['qty'] = $qty;
					$quote_product_arr[$quote_stock_id]['price'] = $price;
				}

				$quote_extra_arr = array();
				$extra_ids_arr = explode($separator, $extra_ids);
				foreach ($extra_ids_arr as $str) {
					if (strlen($str) == '') continue;
					list($quote_extra_id, $quote_stock_id, $extra_id, $extra_item_id) = explode($sep, $str);
					if (intval($quote_stock_id) > 0) {
						if (!isset($quote_extra_arr[$quote_stock_id])) {
							$quote_extra_arr[$quote_stock_id] = array();
						}
						$quote_extra_arr[$quote_stock_id][] = array('extra_id' => $extra_id, 'extra_item_id' => $extra_item_id);
					}
				}
				$product_list_arr = array();
				foreach ($quote_product_arr as $quote_stock_id => $_quote_product) {
					$product_id = $_quote_product['product_id'];
					$qty = $_quote_product['qty'];
					$price = $_quote_product['price'];

					$extra_str = NULL;
					if (isset($quote_extra_arr[$quote_stock_id])) {
						$extra_list_arr = array();
						foreach ($quote_extra_arr[$quote_stock_id] as $osindex => $osarr) {
							if (intval($osarr['extra_id']) > 0 && intval($osarr['extra_item_id']) > 0) {
								$extra_list_arr[] = $extra_arr[$osarr['extra_id']]['title'] . '(' . $extra_item_arr[$osarr['extra_item_id']]['name'] . ')';
							} else if (intval($osarr['extra_id']) > 0) {
								$extra_list_arr[] = $extra_arr[$osarr['extra_id']]['name'];
							}
						}
						$extra_str = join("; ", $extra_list_arr);
					}
					$_attr_str = '';
					if (isset($export_attr_arr[$v['id']][$product_id][$quote_stock_id])) {
						$_attr_str =  join(' / ', $export_attr_arr[$v['id']][$product_id][$quote_stock_id]);
					}
					$product_list_arr[] = $product_arr[$product_id]['name'] . ' | ' . $_attr_str . ' x ' . $qty . (!empty($extra_str) ? ' (' . $extra_str . ')' : NULL);
				}

				$arr[$k]['Product x Qty'] = join("; \n", $product_list_arr);
				unset($arr[$k]['product_ids']);
				unset($arr[$k]['extra_ids']);
			}

			$csv = new pjCSV();
			$csv
				->setHeader(true)
				->setName("Quotes-" . time() . ".csv")
				->process($arr)
				->download();
		}
		exit;
	}

	public function pjActionGetClient()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			if ($this->_get->check('client_id') && $this->_get->toInt('client_id') > 0) {
				$client_arr = pjClientModel::factory()->find($this->_get->toInt('client_id'))->getData();
				$this->set('client_arr', $client_arr);
			}
		}
	}

	public function pjActionGetAddress()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			if ($this->_get->check('id') && $this->_get->toInt('id') > 0) {
				$address_arr = pjAddressModel::factory()
					->select('t1.*, t2.content AS country_name')
					->join('pjMultiLang', "t2.model='pjBaseCountry' AND t2.foreign_id=t1.country_id AND t2.field='name' AND t2.locale='" . $this->getLocaleId() . "'", 'left outer')
					->find($this->_get->toInt('id'))
					->getData();
				if (!$this->_get->check('json')) {
					$this->set('address_arr', $address_arr);
				} else {
					pjAppController::jsonResponse($address_arr);
				}
			}
		}
	}

	public function pjActionGetAddressBook()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			if ($this->_get->check('client_id') && $this->_get->toInt('client_id') > 0) {
				$this->set('quote_arr', pjQuoteModel::factory()->find($this->_get->toInt('quote_id'))->getData());
				$this->set('address_arr', pjAddressModel::factory()->where('t1.client_id', $this->_get->toInt('client_id'))->orderBy('t1.address_1 ASC')->findAll()->getData());
			}
		}
	}

	public function pjActionGetQuote()
	{
		$this->setAjax(true);
		$company_id = $_SESSION[$this->defaultCompany]['id'];

		if ($this->isXHR()) {
			// echo "<pre>"; print_r($company_id); die;
			$pjQuoteModel = pjQuoteModel::factory()
				->join('pjClient', 't2.id=t1.client_id', 'left outer');
			if (isset($company_id) && (int)$company_id > 0) {
				// $pjQuoteModel->where('t1.company_id', $company_id);
			}
			if ($q = $this->_get->toString('q')) {
				$q = str_replace(array('%', '_'), array('\%', '\_'), trim($q));
				$pjQuoteModel->where('(t1.uuid LIKE "%' . $q . '%" 
				OR t2.client_name LIKE "%' . $q . '%"
				 OR t2.email LIKE "%' . $q . '%"
				  OR t1.s_name LIKE "%' . $q . '%"
				   OR t1.s_address_1 LIKE "%' . $q . '%"
				    OR t1.s_city LIKE "%' . $q . '%"
				     OR t1.b_name LIKE "%' . $q . '%"
				      OR t1.b_address_1 LIKE "%' . $q . '%"
				       OR t1.b_city LIKE "%' . $q . '%")');
			}

			# Update quote (other quotes list)
			if ($this->_get->check('client_id') && ($this->_get->toInt('client_id') > 0 || $this->_get->check('is_client_quotes'))) {
				$pjQuoteModel->where('t1.client_id', $this->_get->toInt('client_id'));
			}
			if ($this->_get->check('quote_id') && $this->_get->toInt('quote_id') > 0) {
				$pjQuoteModel->where('t1.id !=', $this->_get->toInt('quote_id'));
			}
			if ($this->_get->check('product_id') && $this->_get->toInt('product_id') > 0) {
				$pjQuoteModel->where(sprintf("t1.id IN (SELECT `quote_id` FROM `%s` WHERE `product_id` = '%u')", pjQuoteStockModel::factory()->getTable(), $this->_get->toInt('product_id')));
			}
			if ($this->_get->check('status') && in_array($this->_get->toString('status'), array('new', 'pending', 'cancelled', 'completed'))) {
				$pjQuoteModel->where('t1.status', $this->_get->toString('status'));
			}
			if ($this->_get->check('payment_method') && in_array($this->_get->toString('payment_method'), array('paypal', 'authorize', 'creditcard', 'bank', 'cod'))) {
				$pjQuoteModel->where('t1.payment_method', $this->_get->toString('payment_method'));
			}
			if ($this->_get->check('total_from') && $this->_get->toInt('total_from') > 0) {
				$pjQuoteModel->where('t1.total >=', $this->_get->toInt('total_from'));
			}
			if ($this->_get->check('total_to') && $this->_get->toInt('total_to') > 0) {
				$pjQuoteModel->where('t1.total <=', $this->_get->toInt('total_to'));
			}
			if ($this->_get->check('date_from') && $this->_get->toString('date_from') != '') {
				$pjQuoteModel->where('DATE(t1.created) >=', pjDateTime::formatDate($this->_get->toString('date_from'), $this->option_arr['o_date_format']));
			}
			if ($this->_get->check('date_to') && $this->_get->toString('date_to') != '') {
				$pjQuoteModel->where('DATE(t1.created) <=', pjDateTime::formatDate($this->_get->toString('date_to'), $this->option_arr['o_date_format']));
			}

			$column = 't1.id';
			$direction = 'DESC';
			if ($this->_get->check('direction') && $this->_get->check('column') && in_array(strtoupper($this->_get->toString('direction')), array('ASC', 'DESC'))) {
				$column = $this->_get->toString('column');
				$direction = strtoupper($this->_get->toString('direction'));
			}

			$total = $pjQuoteModel->findCount()->getData();
			$rowCount = $this->_get->check('rowCount') && $this->_get->toInt('rowCount') > 0 ? $this->_get->toInt('rowCount') : 10;
			$pages = ceil($total / $rowCount);
			$page = $this->_get->check('page') && $this->_get->toInt('page') > 0 ? $this->_get->toInt('page') : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages) {
				$page = $pages;
			}
			$data = $pjQuoteModel
				->select('t1.id, t1.uuid, t1.total, t1.status, t1.created, t1.client_id, t2.client_name')
				->orderBy("$column $direction")
				->limit($rowCount, $offset)
				->findAll()
				->getData();
			$quote_statuses = __('quote_statuses', true);
			foreach ($data as $k => $v) {
				$data[$k]['total_formated'] = pjCurrency::formatPrice($v['total']);
				$data[$k]['created'] = date($this->option_arr['o_date_format'] . ', ' . $this->option_arr['o_time_format'], strtotime($v['created']));
				$data[$k]['status_formated'] = @$quote_statuses[$v['status']];
			}
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
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
			// $pjProductModel->where('t1.company_id', $company_id);
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

		$this->set('has_update', pjAuth::factory('pjAdminQuotes', 'pjActionUpdate')->hasAccess());
		$this->set('has_delete', pjAuth::factory('pjAdminQuotes', 'pjActionDeleteQuote')->hasAccess());
		$this->set('has_delete_bulk', pjAuth::factory('pjAdminQuotes', 'pjActionDeleteQuoteBulk')->hasAccess());

		$this->appendCss('css/select2.min.css', PJ_THIRD_PARTY_PATH . 'select2/');
		$this->appendJs('js/select2.full.min.js', PJ_THIRD_PARTY_PATH . 'select2/');
		$this->appendJs('moment-with-locales.min.js', PJ_THIRD_PARTY_PATH . 'moment/');
		$this->appendCss('datepicker.css', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/');
		$this->appendJs('bootstrap-datepicker.js', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/');
		$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
		$this->appendJs('pjAdminQuotes.js');
	}

	public function pjActionGetPrice()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			$price = $discount = $tax = $shipping = $insurance = 0;

			$pjQuoteStockModel = pjQuoteStockModel::factory()->where('t1.quote_id', $this->_post->toInt('id'))->findAll();

			$os_arr = $pjQuoteStockModel->getData();
			$oe_arr = pjQuoteExtraModel::factory()->where('t1.quote_id', $this->_post->toInt('id'))->findAll()->getData();

			if ($this->_post->toString('voucher') != '') {
				$product_ids = $pjQuoteStockModel->getDataPair(null, 'product_id');
				$product_ids = array_unique($product_ids);

				$pre = array();
				$pre['code'] = $this->_post->toString('voucher');
				list($pre['date'], $pre['hour'], $pre['minute']) = explode(",", date("Y-m-d,H,i"));

				$response = pjAppController::getDiscount($pre, $this->option_arr);
				if ($response['status'] == 'OK') {
					$intersect = array_intersect($response['voucher_products'], $product_ids);
					if (empty($response['voucher_products'][0]) || !empty($intersect)) {
						$voucher = array(
							'voucher_code' => $response['voucher_code'],
							'voucher_type' => $response['voucher_type'],
							'voucher_apply' => $response['voucher_apply'],
							'voucher_discount' => $response['voucher_discount'],
							'voucher_products' => empty($response['voucher_products'][0]) ? 'all' : $response['voucher_products']
						);
					}
				}
			}

			$calc_price = pjAppController::pjActionCalcPrices($this->_post->toInt('id'), array(), $os_arr, null, @$voucher, $this->option_arr, $this->_post->check('tax_id') ? $this->_post->toInt('tax_id')  : null, 'back', 'quote');

			$data['price'] = $calc_price['price'];
			$data['discount'] = $calc_price['discount'];
			$data['insurance'] = $calc_price['insurance'];
			$data['shipping'] = $calc_price['shipping'];
			$data['tax'] = $calc_price['tax'];
			$data['total'] = $calc_price['total'];
			$data['total'] = $data['total'] > 0 ? $data['total'] : 0;

			$data = array_map('floatval', $data);

			pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => '', 'data' => $data));
		}
		exit;
	}

	public function pjActionSaveQuote()
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
		$pjQuoteModel = pjQuoteModel::factory();
		$arr = $pjQuoteModel->find($this->_get->toInt('id'))->getData();
		if (!$arr) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Quote not found.'));
		}
		if (!in_array($this->_post->toString('column'), $pjQuoteModel->getI18n())) {
			$data = array($this->_post->toString('column') => $this->_post->toString('value'));
			if ($this->_post->toString('column') == 'status' && $this->_post->toString('value') == 'completed') {
				$before = $pjQuoteModel->find($this->_get->toInt('id'))->getData();
				if ($before['status'] != 'completed') {
					$data['processed_on'] = ':NOW()';
				}
			}
			$pjQuoteModel->reset()->set('id', $this->_get->toInt('id'))->modify($data);
		} else {
			pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($this->_post->toString('column') => $this->_post->toString('value'))), $this->_get->toInt('id'), 'pjQuote', 'data');
		}
		self::jsonResponse(array('status' => 'OK', 'code' => 201, 'text' => 'Quote has been updated.'));
		exit;
	}

	public function pjActionStockDelete()
	{
		$this->setAjax(true);

		if ($this->isXHR() && $this->isLoged()) {
			if ($this->_post->check('id') && $this->_post->toInt('id') > 0) {
				$pjQuoteStockModel = pjQuoteStockModel::factory();
				$arr = $pjQuoteStockModel->find($this->_post->toInt('id'))->getData();
				if (empty($arr)) {
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Stock not found.'));
				}
				if (1 == $pjQuoteStockModel->set('id', $this->_post->toInt('id'))->erase()->getAffectedRows()) {
					pjQuoteExtraModel::factory()->where('quote_stock_id', $this->_post->toInt('id'))->eraseAll();
					pjStockModel::factory()->set('id', $arr['stock_id'])->modify(array('qty' => ":qty + " . (int) $arr['qty']));
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Stock has been deleted.'));
				}
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Stock has not been deleted.'));
			}
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Missing parameters.'));
		}
		pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Access denied.'));
		exit;
	}

	public function pjActionStockGet()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			$stack = pjAppController::pjActionGetQuoteStock($this->_get->toInt('quote_id'), $this->getLocaleId());

			$this
				->set('os_arr', $stack['os_arr'])
				->set('extra_arr', $stack['extra_arr'])
				->set('attr_arr', $stack['attr_arr'])
			;
		}
	}

	public function pjActionStockAdd()
	{
		$this->setAjax(true);
		// $company_id = $_SESSION[$this->defaultCompany]['id'];
		$company_id = '0';

		if ($this->isXHR()) {
			if ($this->_post->check('stock_add')) {
				if ($this->_post->check('qty') && $this->_post->toInt('qty') > 0) {
					$pjQuoteStockModel = pjQuoteStockModel::factory();
					$pjQuoteExtraModel = pjQuoteExtraModel::factory();
					$pjStockModel = pjStockModel::factory();

					$stock_id = $this->_post->toInt('stock_id');
					$quote_stock_id = $pjQuoteStockModel->reset()->setAttributes(array(
						'quote_id' => $this->_post->toInt('quote_id'),
						'product_id' => $this->_post->toInt('product_id'),
						'company_id' =>  $company_id,
						'stock_id' =>  $stock_id,
						'price' => $this->_post->toFloat('price'),
						'qty' => $this->_post->toInt('qty')
					))->insert()->getInsertId();

					if ($quote_stock_id !== FALSE && (int) $quote_stock_id > 0) {
						$pjStockModel->reset()->set('id', $stock_id)->modify(array('qty' => ":qty - " . $this->_post->toInt('qty')));

						if ($extra_id_arr = $this->_post->toArray('extra_id')) {
							$oe_data = array(
								'quote_id' => $this->_post->toInt('quote_id'),
								'company_id' => $company_id,
								'quote_stock_id' => $quote_stock_id
							);
							foreach ($extra_id_arr as $extra_id => $value) {
								if (!empty($value) && strpos($value, "|") !== false) {
									$e_arr = array();
									$e_arr = explode("|", $value);
									switch ($e_arr[0]) {
										case 'single':
											$oe_data['extra_item_id'] = NULL;
											break;
										case 'multi':
											$oe_data['extra_item_id'] = $e_arr[2];
											break;
									}
									$oe_data['extra_id'] = $extra_id;
									$oe_data['price'] = $e_arr[1];

									$pjQuoteExtraModel->reset()->setAttributes($oe_data)->insert();
								}
							}
						}
					}

					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Stock has been added.'));
				}
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Stock couldn\'t be empty.'));
			}
			$pjProductModel = pjProductModel::factory();
			$pjProductModel->select('t1.*, t2.content AS name')
				->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='" . $this->getLocaleId() . "'", 'left outer')
				->where('t1.status != 2')
				->where("t1.id IN (SELECT TS.`product_id` FROM `" . pjStockModel::factory()->getTable() . "` AS TS WHERE TS.product_id = t1.id AND (qty > 0 OR t1.is_digital='1'))");
			if (isset($company_id) && (int)$company_id > 0) {
				// $pjProductModel->where('t1.company_id', $company_id);
			}
			$product_arr = $pjProductModel->orderBy("`name` ASC")
				->findAll()->getData();
			$this->set('product_arr', $product_arr);
		}
	}

	public function pjActionStockGetByProduct()
	{
		$this->setAjax(true);
		$company_id = $_SESSION[$this->defaultCompany]['id'];

		if ($this->isXHR()) {
			if ($this->_get->check('product_id') && $this->_get->toInt('product_id') > 0) {
				$pjStockAttributeModel = pjStockAttributeModel::factory();
				$pjExtraItemModel = pjExtraItemModel::factory();

				$arr = pjProductModel::factory()
					->select(sprintf("t1.*, t2.content AS name, t3.content AS full_desc,
						(SELECT MIN(`price`) FROM `%2\$s`
							WHERE `product_id` = `t1`.`id`
							LIMIT 1) AS `price`,
						(SELECT `id` FROM `%2\$s`
							WHERE `product_id` = `t1`.`id`
							ORDER BY `price` ASC
							LIMIT 1) AS `stockId`
						", pjGalleryModel::factory()->getTable(), pjStockModel::factory()->getTable(), pjProductCategoryModel::factory()->getTable()))
					->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.id AND t2.locale='" . $this->getLocaleId() . "' AND t2.field='name'", 'left outer')
					->join('pjMultiLang', "t3.model='pjProduct' AND t3.foreign_id=t1.id AND t3.locale='" . $this->getLocaleId() . "' AND t3.field='full_desc'", 'left outer')
					->find($this->_get->toInt('product_id'))
					->getData();
				$this->set('product_arr', $arr);

				$pjExtraModel = pjExtraModel::factory();
				$pjExtraModel->select('t1.*, t2.content AS name, t3.content AS title')
					->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.locale='" . $this->getLocaleId() . "' AND t2.field='extra_name'", 'left outer')
					->join('pjMultiLang', "t3.model='pjExtra' AND t3.foreign_id=t1.id AND t3.locale='" . $this->getLocaleId() . "' AND t3.field='extra_title'", 'left outer')
					->where('t1.product_id', $this->_get->toInt('product_id'));
				if (isset($company_id) && (int)$company_id > 0) {
					// $pjExtraModel->where('t1.company_id', $company_id);
				}
				$extra_arr = $pjExtraModel->orderBy('`title` ASC, `name` ASC')
					->findAll()
					->getData();

				foreach ($extra_arr as $k => $extra) {
					$extra_arr[$k]['extra_items'] = $pjExtraItemModel
						->reset()
						->select('t1.*, t2.content AS name')
						->join('pjMultiLang', "t2.model='pjExtraItem' AND t2.foreign_id=t1.id AND t2.locale='" . $this->getLocaleId() . "' AND t2.field='extra_name'", 'left outer')
						->where('t1.extra_id', $extra['id'])
						->orderBy('t1.price ASC')
						->findAll()
						->getData();
				}

				$attr_arr = array();
				// Do not change col_name, direction
				$pjAttributeModel = pjAttributeModel::factory()
					->select('t1.id, t1.product_id, t1.parent_id, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjAttribute' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='" . $this->getLocaleId() . "'", 'left outer')
					->where('t1.product_id', $this->_get->toInt('product_id'))
					->where(sprintf("(CONCAT_WS('_', t1.id, t1.parent_id) IN (
							SELECT CONCAT_WS('_', TSA.attribute_id, TSA.attribute_parent_id)
							FROM `%s` AS `TSA`
							INNER JOIN `%s` AS `TS` ON TS.id = TSA.stock_id AND TS.qty > 0
							WHERE TSA.product_id = t1.product_id
						) OR t1.parent_id IS NULL OR t1.parent_id = '0')", $pjStockAttributeModel->getTable(), pjStockModel::factory()->getTable()));
				if (isset($company_id) && (int)$company_id > 0) {
					// $pjAttributeModel->where('t1.company_id', $company_id);
				}
				$a_arr = $pjAttributeModel->orderBy('t1.`quote_group` ASC, `quote_item` ASC')
					->findAll()
					->getData();
				foreach ($a_arr as $attr) {
					if ((int) $attr['parent_id'] === 0) {
						$attr_arr[$attr['id']] = $attr;
					} else {
						if (!isset($attr_arr[$attr['parent_id']]['child'])) {
							$attr_arr[$attr['parent_id']]['child'] = array();
						}
						$attr_arr[$attr['parent_id']]['child'][] = $attr;
					}
				}

				$pjStockModel = pjStockModel::factory()
					->join('pjProduct', 't1.product_id=t2.id', 'left')
					->where('t1.product_id', $this->_get->toInt('product_id'))
					->where("(t1.qty > 0 OR t2.is_digital='1')");
				if (isset($company_id) && (int)$company_id > 0) {
					// $pjStockModel->where('t1.company_id', $company_id);
				}
				$stock_arr = $pjStockModel->findAll()
					->getData();

				$_arr = array();
				foreach ($stock_arr as $k => $stock) {
					$_qty = $stock['qty'];
					if (isset($quote_arr[$stock['id']])) {
						$_qty -= $quote_arr[$stock['id']];
						if ($_qty < 1) {
							unset($stock_arr[$k]);
							continue;
						}
					}
					$stock_arr[$k]['qty'] = $_qty;
					$_arr[$stock['id']] = $pjStockAttributeModel
						->reset()
						->where('t1.stock_id', $stock['id'])
						->orderBy('t1.attribute_id ASC')
						->findAll()
						->getDataPair('attribute_parent_id', 'attribute_id');
				}

				$this
					->set('stock_attr_arr', $_arr)
					->set('extra_arr', $extra_arr)
					->set('attr_arr', array_values($attr_arr))
					->set('stock_arr', array_values($stock_arr))
				;
			}
		}
	}

	public function pjActionStockEdit()
	{
		$this->setAjax(true);
		// $company_id = $_SESSION[$this->defaultCompany]['id'];
		$company_id = '0';

		if ($this->isXHR() && $this->isLoged()) {
			if ($this->_post->check('stock_edit')) {
				$pjQuoteStockModel = pjQuoteStockModel::factory();
				$arr = $pjQuoteStockModel->find($this->_post->toInt('quote_stock_id'))->getData();
				if (empty($arr)) {
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Quote/stock not found'));
				}
				$qty = $this->_post->toInt('qty');
				$pjQuoteStockModel->modify(array('qty' => $qty));
				if ($arr['qty'] > $qty) {
					$diff = $arr['qty'] - $qty;
					pjStockModel::factory()->set('id', $arr['stock_id'])->modify(array('qty' => ":qty + $diff"));
				} elseif ($arr['qty'] < $qty) {
					$diff = $qty - $arr['qty'];
					pjStockModel::factory()->set('id', $arr['stock_id'])->modify(array('qty' => ":qty - $diff"));
				}

				$pjQuoteExtraModel = pjQuoteExtraModel::factory();

				$pjQuoteExtraModel->reset()->where('quote_stock_id', $this->_post->toInt('quote_stock_id'));
				if ($extra_id_arr = $this->_post->toArray('extra_id')) {
					$extra_id_arr = $this->_post->toArray('extra_id');
					$pjQuoteExtraModel->whereNotIn('extra_id', array_keys($extra_id_arr));
				}
				$pjQuoteExtraModel->eraseAll();

				if ($extra_id_arr = $this->_post->toArray('extra_id')) {
					$empty_id = $exist_id = array();
					foreach ($extra_id_arr as $extra_id => $value) {
						if (empty($value)) {
							$empty_id[] = $extra_id;
							continue;
						}

						$stack = explode("|", $value);
						switch ($stack[0]) {
							case 'single':
								if (
									0 == $pjQuoteExtraModel->reset()
									->where('quote_stock_id', $this->_post->toInt('quote_stock_id'))
									->where('extra_id', $extra_id)
									->where('extra_item_id IS NULL')
									->findCount()->getData()
								) {
									$pjQuoteExtraModel->reset()->setAttributes(array(
										'quote_id' => $this->_post->toInt('quote_id'),
										'quote_stock_id' => $this->_post->toInt('quote_stock_id'),
										'extra_id' => $extra_id,
										'company_id' => $company_id,
										'price' => $stack[1]
									))->insert();
								} else {
									//do nothing
									$exist_id[] = $extra_id;
								}
								break;
							case 'multi':
								if (
									0 == $pjQuoteExtraModel->reset()
									->where('quote_stock_id', $this->_post->toInt('quote_stock_id'))
									->where('extra_id', $extra_id)
									->findCount()->getData()
								) {
									$pjQuoteExtraModel->reset()->setAttributes(array(
										'quote_id' => $this->_post->toInt('quote_id'),
										'quote_stock_id' => $this->_post->toInt('quote_stock_id'),
										'company_id' => $company_id,
										'extra_id' => $extra_id,
										'extra_item_id' => $stack[2],
										'price' => $stack[1]
									))->insert();
								} else {
									$pjQuoteExtraModel->reset()
										->where('quote_id', $this->_post->toInt('quote_id'))
										->where('quote_stock_id', $this->_post->toInt('quote_stock_id'))
										->where('extra_id', $extra_id)
										->limit(1)
										->modifyAll(
											array(
												'extra_item_id' => $stack[2],
												'price' => $stack[1]
											)
										);
									$exist_id[] = $extra_id;
								}
								break;
						}
					}

					$pjQuoteExtraModel->reset();
					if (!empty($empty_id)) {
						$pjQuoteExtraModel
							->where('quote_stock_id', $this->_post->toInt('quote_stock_id'))
							->whereIn('extra_id', $empty_id);

						if (!empty($exist_id)) {
							$pjQuoteExtraModel->whereNotIn('extra_id', $exist_id);
						}
						$pjQuoteExtraModel->eraseAll();
					}
				} else {
					pjQuoteExtraModel::factory()->where('quote_stock_id', $this->_post->toInt('quote_stock_id'))->eraseAll();
				}

				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
			}

			$os_arr = pjQuoteStockModel::factory()->find($this->_get->toInt('quote_stock_id'))->getData();
			$oe_arr = pjQuoteExtraModel::factory()->where('t1.quote_stock_id', $this->_get->toInt('quote_stock_id'))->findAll()->getDataPair('extra_id', 'extra_item_id');
			$stock_arr = pjStockModel::factory()->find($os_arr['stock_id'])->getData();

			$stock_arr['attrs'] = pjStockAttributeModel::factory()
				->where('t1.stock_id', $os_arr['stock_id'])
				->orderBy('t1.attribute_id ASC')
				->findAll()
				->getDataPair('attribute_parent_id', 'attribute_id');

			$pjExtraItemModel = pjExtraItemModel::factory();

			$extra_arr = pjExtraModel::factory()
				->select('t1.*, t2.content AS name, t3.content AS title')
				->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.locale='" . $this->getLocaleId() . "' AND t2.field='extra_name'", 'left outer')
				->join('pjMultiLang', "t3.model='pjExtra' AND t3.foreign_id=t1.id AND t3.locale='" . $this->getLocaleId() . "' AND t3.field='extra_title'", 'left outer')
				->where('t1.product_id', $os_arr['product_id'])
				->orderBy('`title` ASC, `name` ASC')
				->findAll()
				->getData();

			foreach ($extra_arr as $k => $extra) {
				$extra_arr[$k]['extra_items'] = $pjExtraItemModel
					->reset()
					->select('t1.*, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjExtraItem' AND t2.foreign_id=t1.id AND t2.locale='" . $this->getLocaleId() . "' AND t2.field='extra_name'", 'left outer')
					->where('t1.extra_id', $extra['id'])
					->orderBy('t1.price ASC')
					->findAll()
					->getData();
			}

			$attr_arr = array();
			// Do not change col_name, direction
			$a_arr = pjAttributeModel::factory()
				->select('t1.id, t1.product_id, t1.parent_id, t2.content AS name')
				->join('pjMultiLang', "t2.model='pjAttribute' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='" . $this->getLocaleId() . "'", 'left outer')
				->where('t1.product_id', $os_arr['product_id'])
				->orderBy('t1.quote_group ASC, `quote_item` ASC')
				->findAll()
				->getData();

			foreach ($a_arr as $attr) {
				if ((int) $attr['parent_id'] === 0) {
					$attr_arr[$attr['id']] = $attr;
				} else {
					if (!isset($attr_arr[$attr['parent_id']]['child'])) {
						$attr_arr[$attr['parent_id']]['child'] = array();
					}
					$attr_arr[$attr['parent_id']]['child'][] = $attr;
				}
			}

			$this
				->set('os_arr', $os_arr)
				->set('oe_arr', $oe_arr)
				->set('attr_arr', $attr_arr)
				->set('stock_arr', $stock_arr)
				->set('extra_arr', $extra_arr)
			;
		}
	}

	public function pjActionUpdate()
	{
		$this->checkLogin();
		if (!pjAuth::factory()->hasAccess()) {
			$this->sendForbidden();
			return;
		}
		$company_id = $_SESSION[$this->defaultCompany]['id'];

		$pjQuoteModel = pjQuoteModel::factory();
		if (isset($_REQUEST['id']) && (int) $_REQUEST['id'] > 0) {
			$pjQuoteModel->where('t1.id', $_REQUEST['id']);
		} elseif ($this->_get->check('uuid') && $this->_get->toString('uuid') != '') {
			$pjQuoteModel->where('t1.uuid', $this->_get->toString('uuid'));
		}
		if (isset($company_id) && (int)$company_id > 0) {
			// $pjQuoteModel->where('t1.company_id', $company_id);
		}
		$arr = $pjQuoteModel
			->select(sprintf("t1.*,
					AES_DECRYPT(t1.cc_type, '%1\$s') AS `cc_type`,
					AES_DECRYPT(t1.cc_num, '%1\$s') AS `cc_num`,
					AES_DECRYPT(t1.cc_exp_month, '%1\$s') AS `cc_exp_month`,
					AES_DECRYPT(t1.cc_exp_year, '%1\$s') AS `cc_exp_year`,
					AES_DECRYPT(t1.cc_code, '%1\$s') AS `cc_code`,
					t2.content AS `b_country`, t3.content AS `s_country`,
					t4.email as client_email, t4.client_name, t4.phone as client_phone, t4.url as client_url, AES_DECRYPT(t4.password, '%1\$s') AS `password`", PJ_SALT))
			->join('pjMultiLang', "t2.model='pjBaseCountry' AND t2.foreign_id=t1.b_country_id AND t2.locale=t1.locale_id AND t2.field='name'", 'left outer')
			->join('pjMultiLang', "t3.model='pjBaseCountry' AND t3.foreign_id=t1.s_country_id AND t3.locale=t1.locale_id AND t3.field='name'", 'left outer')
			->join('pjClient', "t4.id=t1.client_id", 'left outer')
			->limit(1)
			->findAll()
			->getData();

		if (empty($arr)) {
			pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminQuotes&action=pjActionIndex&err=AOR08");
		}
		$arr = $arr[0];

		if ($this->_post->check('update_form')) {
			if (0 != $pjQuoteModel->reset()->where('t1.uuid', $this->_post->toString('uuid'))->where('t1.id !=', $this->_post->toInt('id'))->findCount()->getData()) {
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminQuotes&action=pjActionIndex&err=AOR02");
			}

			$data = array();
			if ($this->_post->check('same_as')) {
				$data['s_name'] = $this->_post->toString('b_name');
				$data['s_country_id'] = $this->_post->toInt('b_country_id');
				$data['s_state'] = $this->_post->toString('b_state');
				$data['s_city'] = $this->_post->toString('b_city');
				$data['s_zip'] = $this->_post->toString('b_zip');
				$data['s_address_1'] = $this->_post->toString('b_address_1');
				$data['s_address_2'] = $this->_post->toString('b_address_2');
			} else {
				$data['same_as'] = 0;
			}

			if ($arr['status'] != 'completed' && $this->_post->toString('status') == 'completed') {
				$data['processed_on'] = ':NOW()';
			}

			$pjQuoteModel->reset()->set('id', $this->_post->toInt('id'))->modify(array_merge($this->_post->raw(), $data));
			pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminQuotes&action=pjActionIndex&err=AOR05");
		} else {
			$arr['products'] = pjAppController::pjActionGetProductsString($arr['id'], $arr['locale_id']);
			$arr['has_digital'] = pjAppController::pjActionCheckDigital($arr['id']);
			$stack = pjAppController::pjActionGetQuoteStock($arr['id'], $arr['locale_id']);

			$this
				->set('os_arr', $stack['os_arr'])
				->set('extra_arr', $stack['extra_arr'])
				->set('attr_arr', $stack['attr_arr'])
			;

			if (pjObject::getPlugin('pjPayments') !== NULL) {
				$this->set('payment_option_arr', pjPaymentOptionModel::factory()->getOptions($this->getForeignId()));
				$this->set('payment_titles', pjPayments::getPaymentTitles($this->getForeignId(), $this->getLocaleId()));
			} else {
				$this->set('payment_titles', __('payment_methods', true));
			}

			$client_arr = pjClientModel::factory()
				->where('t1.status', 'T')
				->orderBy('t1.client_name ASC')
				->findAll()
				->getData();
			$this->set('client_arr', $client_arr);

			$this
				->set('arr', $arr)
				->set(
					'country_arr',
					pjBaseCountryModel::factory()
						->select('t1.id, t2.content AS country_title')
						->join('pjBaseMultiLang', "t2.model='pjBaseCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='" . $this->getLocaleId() . "'", 'left outer')
						->orderBy('`country_title` ASC')->findAll()->getData()
				)
				->set(
					'address_arr',
					pjAddressModel::factory()
						->select('t1.*, t2.content AS country_name')
						->join('pjMultiLang', "t2.model='pjBaseCountry' AND t2.foreign_id=t1.country_id AND t2.field='name' AND t2.locale='" . $this->getLocaleId() . "'", 'left outer')
						->where('t1.client_id', $arr['client_id'])
						->orderBy('t1.address_1 ASC')->findAll()->getData()
				)
				->set(
					'tax_arr',
					pjTaxModel::factory()
						->select('t1.*, t2.content AS location')
						->join('pjMultiLang', "t2.model='pjTax' AND t2.foreign_id=t1.id AND t2.field='location' AND t2.locale='" . $this->getLocaleId() . "'", 'left outer')
						->orderBy('`location` ASC')
						->findAll()
						->getData()
				)
				->appendCss('css/select2.min.css', PJ_THIRD_PARTY_PATH . 'select2/')
				->appendJs('js/select2.full.min.js', PJ_THIRD_PARTY_PATH . 'select2/')
				->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/')
				->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/')
				->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/')
				->appendJs('pjAdminQuotes.js');

			$this->set('has_delete', pjAuth::factory('pjAdminQuotes', 'pjActionDeleteQuote')->hasAccess());
			$this->set('has_delete_bulk', pjAuth::factory('pjAdminQuotes', 'pjActionDeleteQuoteBulk')->hasAccess());
		}
	}

	public function pjActionGetStocks()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			if ($this->_get->check('id') && $this->_get->toInt('id') > 0) {
				# Find out what qty is in current shopping cart for each stock
				$quote_arr = array();

				$pjStockModel = pjStockModel::factory();
				$pjStockAttributeModel = pjStockAttributeModel::factory();
				$pjAttributeModel = pjAttributeModel::factory();

				$stock_arr = $pjStockModel
					->join('pjProduct', 't1.product_id=t2.id', 'left')
					->where('t1.product_id', $this->_get->toInt('id'))
					->where("(t1.qty > 0 OR t2.is_digital='1')")
					->findAll()->getData();

				$stocks = $stock_ids = $qty = $price = array();
				foreach ($stock_arr as $k => $stock) {
					$_qty = $stock['qty'];
					if (isset($quote_arr[$stock['id']])) {
						$_qty -= $quote_arr[$stock['id']];
						if ($_qty < 1) {
							continue;
						}
					}
					$stock_ids[] = $stock['id'];
					$stocks[] = $pjStockAttributeModel
						->reset()
						->where('t1.stock_id', $stock['id'])
						->where("t1.attribute_id IN (SELECT TA.id FROM `" . $pjAttributeModel->getTable() . "` AS `TA` WHERE `TA`.product_id='" . $this->_get->toInt('id') . "')")
						->orderBy('t1.attribute_id ASC')
						->findAll()
						->getDataPair('attribute_parent_id', 'attribute_id');

					$qty[] = $_qty;
					$price[] = $stock['price'];
				}

				# -- Fix for empty values in stocks
				$attr_arr = $pjAttributeModel
					->where('t1.product_id', $this->_get->toInt('id'))
					->where(sprintf("(CONCAT_WS('_', t1.id, t1.parent_id) IN (
							SELECT CONCAT_WS('_', TSA.attribute_id, TSA.attribute_parent_id)
							FROM `%s` AS `TSA`
							INNER JOIN `%s` AS `TS` ON TS.id = TSA.stock_id AND TS.qty > 0
							WHERE TSA.product_id = t1.product_id
						) OR t1.parent_id IS NULL OR t1.parent_id = '0')", $pjStockAttributeModel->getTable(), $pjStockModel->getTable()))
					->findAll()
					->getDataPair('id', 'parent_id');

				foreach ($stocks as $k => $stock) {
					foreach ($stock as $_k => $_v) {
						if ((int) $_v === 0) {
							$stokkk = $stock;
							pjUtil::reArrange($stocks, $qty, $price, $stokkk, $attr_arr, $_k, $k);
						}
					}
				}
				# -- End fix

				# Attributes --
				$attr_arr = array();
				// Do not change col_name, direction
				$a_arr = $pjAttributeModel
					->reset()
					->select('t1.id, t1.product_id, t1.parent_id, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjAttribute' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='" . $this->getLocaleId() . "'", 'left outer')
					->where('t1.product_id', $this->_get->toInt('id'))
					->where(sprintf("(CONCAT_WS('_', t1.id, t1.parent_id) IN (
							SELECT CONCAT_WS('_', TSA.attribute_id, TSA.attribute_parent_id)
							FROM `%s` AS `TSA`
							INNER JOIN `%s` AS `TS` ON TS.id = TSA.stock_id AND TS.qty > 0
							WHERE TSA.product_id = t1.product_id
						) OR t1.parent_id IS NULL OR t1.parent_id = '0')", $pjStockAttributeModel->getTable(), $pjStockModel->getTable()))
					->orderBy('t1.parent_id ASC, `name` ASC')
					->findAll()
					->getData();

				foreach ($a_arr as $attr) {
					if ((int) $attr['parent_id'] === 0) {
						$attr_arr[$attr['id']] = $attr;
					} else {
						if (!isset($attr_arr[$attr['parent_id']]['child'])) {
							$attr_arr[$attr['parent_id']]['child'] = array();
						}
						$attr_arr[$attr['parent_id']]['child'][] = $attr;
					}
				}
				$attributes = array_values($attr_arr);
				# Attributes --

				# Fix for no-stock
				if (isset($stocks[0]) && empty($stocks[0])) {
					$stocks = array();
				}
				pjAppController::jsonResponse(compact('stocks', 'qty', 'price', 'stock_ids', 'attributes'));
			}
		}
		exit;
	}

	public function pjActionQuoteEmail()
	{
		$this->setAjax(true);

		if (self::isPost()) {
			if ($this->_post->toInt('send_email') && $this->_post->toString('to') && $this->_post->toString('subject') && $this->_post->toString('message') && $this->_post->toInt('id')) {
				$Email = self::getMailer($this->option_arr);

				$message = pjUtil::textToHtml($this->_post->toString('message'));

				if ($this->option_arr['o_send_email'] == 'flexmail') {

					$r = $this->sendFlexMail($this->_post->toString('to'), $this->_post->toString('subject'), $message, $this->option_arr);
				} else {

					$r = $Email

						->setTo($this->_post->toString('to'))

						->setSubject($this->_post->toString('subject'))

						->send($message);
				}


				if (isset($r) && $r) {
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => __('lblEmailSent', true, false)));
				}
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => __('lblFailedToSend', true, false)));
			}
		}
		if (self::isGet()) {
			if ($id = $this->_get->toInt('id')) {
				$pjQuoteModel = pjQuoteModel::factory();

				$arr = $pjQuoteModel
					->select(sprintf("t1.*,
						AES_DECRYPT(t1.cc_type, '%1\$s') AS `cc_type`,
						AES_DECRYPT(t1.cc_num, '%1\$s') AS `cc_num`,
						AES_DECRYPT(t1.cc_exp_month, '%1\$s') AS `cc_exp_month`,
						AES_DECRYPT(t1.cc_exp_year, '%1\$s') AS `cc_exp_year`,
						AES_DECRYPT(t1.cc_code, '%1\$s') AS `cc_code`,
						t2.content AS `b_country`, t3.content AS `s_country`,
						t4.email as client_email, t4.client_name, t4.phone as client_phone, t4.url as client_url, AES_DECRYPT(t4.password, '%1\$s') AS `password`", PJ_SALT))
					->join('pjMultiLang', "t2.model='pjBaseCountry' AND t2.foreign_id=t1.b_country_id AND t2.locale=t1.locale_id AND t2.field='name'", 'left outer')
					->join('pjMultiLang', "t3.model='pjBaseCountry' AND t3.foreign_id=t1.s_country_id AND t3.locale=t1.locale_id AND t3.field='name'", 'left outer')
					->join('pjClient', "t4.id=t1.client_id", 'left outer')
					->find($id)
					->getData();
				if (!empty($arr)) {
					$locale_id = $this->getLocaleId();
					if (isset($arr['locale_id']) && (int) $arr['locale_id'] > 0) {
						$locale_id = $arr['locale_id'];
					}
					$arr['products'] = pjAppController::pjActionGetProductsString($arr['id'], $locale_id);
					$arr['has_digital'] = pjAppController::pjActionCheckDigital($arr['id']);
					$tokens = pjAppController::getTokens($arr, $this->option_arr);
					$notification = pjNotificationModel::factory()->where('recipient', 'client')->where('transport', 'email')->where('variant', 'quote')->findAll()->getDataIndex(0);
					if ((int) $notification['id'] > 0 && $notification['is_active'] == 1) {
						$resp = pjAppController::pjActionGetSubjectMessage($notification, $locale_id);
						$lang_message = $resp['lang_message'];
						$lang_subject = $resp['lang_subject'];
						if (count($lang_message) === 1 && count($lang_subject) === 1) {
							$subject_client = str_replace($tokens['search'], $tokens['replace'], $lang_subject[0]['content']);
							$message_client = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
							$this->set('arr', array(
								'id' => $id,
								'client_email' => $arr['client_email'],
								'message' => $message_client,
								'subject' => $subject_client
							));
						}
					}
				} else {
					exit;
				}
			} else {
				exit;
			}
		}
	}

	public function pjActionPaymentEmail()
	{
		$this->setAjax(true);

		if (self::isPost()) {
			if ($this->_post->toInt('send_email') && $this->_post->toString('to') && $this->_post->toString('subject') && $this->_post->toString('message') && $this->_post->toInt('id')) {
				$Email = self::getMailer($this->option_arr);
				$message = pjUtil::textToHtml($this->_post->toString('message'));

				if ($this->option_arr['o_send_email'] == 'flexmail') {

					$r = $this->sendFlexMail($this->_post->toString('to'), $this->_post->toString('subject'), $message, $this->option_arr);
				} else {

					$r = $Email

						->setTo($this->_post->toString('to'))

						->setSubject($this->_post->toString('subject'))

						->send($message);
				}

				if (isset($r) && $r) {
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => __('lblEmailSent', true, false)));
				}
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => __('lblFailedToSend', true, false)));
			}
		}
		if (self::isGet()) {
			if ($id = $this->_get->toInt('id')) {
				$pjQuoteModel = pjQuoteModel::factory();

				$arr = $pjQuoteModel
					->select(sprintf("t1.*,
						AES_DECRYPT(t1.cc_type, '%1\$s') AS `cc_type`,
						AES_DECRYPT(t1.cc_num, '%1\$s') AS `cc_num`,
						AES_DECRYPT(t1.cc_exp_month, '%1\$s') AS `cc_exp_month`,
						AES_DECRYPT(t1.cc_exp_year, '%1\$s') AS `cc_exp_year`,
						AES_DECRYPT(t1.cc_code, '%1\$s') AS `cc_code`,
						t2.content AS `b_country`, t3.content AS `s_country`,
						t4.email as client_email, t4.client_name, t4.phone as client_phone, t4.url as client_url, AES_DECRYPT(t4.password, '%1\$s') AS `password`", PJ_SALT))
					->join('pjMultiLang', "t2.model='pjBaseCountry' AND t2.foreign_id=t1.b_country_id AND t2.locale=t1.locale_id AND t2.field='name'", 'left outer')
					->join('pjMultiLang', "t3.model='pjBaseCountry' AND t3.foreign_id=t1.s_country_id AND t3.locale=t1.locale_id AND t3.field='name'", 'left outer')
					->join('pjClient', "t4.id=t1.client_id", 'left outer')
					->find($id)
					->getData();
				if (!empty($arr)) {
					$locale_id = $this->getLocaleId();
					if (isset($arr['locale_id']) && (int) $arr['locale_id'] > 0) {
						$locale_id = $arr['locale_id'];
					}
					$arr['products'] = pjAppController::pjActionGetProductsString($arr['id'], $locale_id);
					$arr['has_digital'] = pjAppController::pjActionCheckDigital($arr['id']);
					$tokens = pjAppController::getTokens($arr, $this->option_arr);
					$notification = pjNotificationModel::factory()->where('recipient', 'client')->where('transport', 'email')->where('variant', 'payment')->findAll()->getDataIndex(0);
					if ((int) $notification['id'] > 0 && $notification['is_active'] == 1) {
						$resp = pjAppController::pjActionGetSubjectMessage($notification, $locale_id);
						$lang_message = $resp['lang_message'];
						$lang_subject = $resp['lang_subject'];
						if (count($lang_message) === 1 && count($lang_subject) === 1) {
							$subject_client = str_replace($tokens['search'], $tokens['replace'], $lang_subject[0]['content']);
							$message_client = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
							$this->set('arr', array(
								'id' => $id,
								'client_email' => $arr['client_email'],
								'message' => $message_client,
								'subject' => $subject_client
							));
						}
					}
				} else {
					exit;
				}
			} else {
				exit;
			}
		}
	}
}
