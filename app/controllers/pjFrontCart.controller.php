<?php
if (!defined("ROOT_PATH")) {
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjFrontCart extends pjFront
{
	public function pjActionApplyCode()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			if (!$this->_post->check('code') || !pjValidation::pjActionNotEmpty($this->_post->toString('code'))) {
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 104, 'text' => __('system_104', true)));
			}
			$company_id = $_SESSION[$this->defaultCompany]['id'];

			$pre = array();
			list($pre['date'], $pre['hour'], $pre['minute']) = explode(",", date("Y-m-d,H,i"));

			$product_ids = pjCartModel::factory()->where('t1.hash', $_SESSION[$this->defaultHash])->findAll()->getDataPair(null, 'product_id');
			$product_ids = array_unique($product_ids);
			$data = $this->_post->raw();
			$data['company_id'] = $company_id;
			$response = pjAppController::getDiscount(array_merge($data, $pre), $this->option_arr);
			if ($response['status'] == 'OK') {
				$intersect = array_intersect($response['voucher_products'], $product_ids);
				if (empty($response['voucher_products'][0]) || !empty($intersect)) {
					$_SESSION[$this->defaultVoucher] = array(
						'voucher_code' => $response['voucher_code'],
						'voucher_type' => $response['voucher_type'],
						'voucher_apply' => $response['voucher_apply'],
						'voucher_discount' => $response['voucher_discount'],
						'voucher_products' => empty($response['voucher_products'][0]) ? 'all' : $response['voucher_products']
					);
				} else {
					$response = array('status' => 'ERR', 'code' => 104, 'text' => __('system_217', true));
				}
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}

	public function pjActionRemoveCode()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			if (isset($_SESSION[$this->defaultVoucher]) && !empty($_SESSION[$this->defaultVoucher])) {
				$_SESSION[$this->defaultVoucher] = NULL;
				unset($_SESSION[$this->defaultVoucher]);
			}
			pjAppController::jsonResponse(array('status' => 'OK', 'code' => 205, 'text' => __('system_205', true)));
		}
		exit;
	}

	public function pjActionAdd()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			if ($this->_post->check('qty')) {
				$post = $this->_post->raw();
				// echo "<pre>"; print_r($post); die;

				$qty = $post['qty'];
				unset($post['qty']);
				if (isset($post['extra']) && (empty($post['extra']))) {
					unset($post['extra']);
				}

				$key = serialize($post);
				$q = $this->cart->get($key);
				if ($q !== FALSE) {
					$this->cart->update($key, $q['qty'] + $qty);
				} else {
					$this->cart->insert($key, $qty);
				}
				$response = array('status' => 'OK', 'code' => 206, 'text' => __('system_206', true));
			} else {
				$response = array('status' => 'ERR', 'code' => 105, 'text' => __('system_105', true));
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}

	public function pjActionRemove()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			if ($this->_post->check('hash') && $this->_post->toString('hash') != '' && !$this->cart->isEmpty()) {
				$response = array('status' => 'OK', 'code' => 207, 'text' => __('system_207', true));
				$this->cart->remove($this->_post->toString('hash'));
			}
			if (!isset($response)) {
				$response = array('status' => 'ERR', 'code' => 106, 'text' => __('system_106', true));
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}

	public function pjActionEmpty()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			if (!$this->cart->isEmpty()) {
				$this->cart->clear();
				$response = array('status' => 'OK', 'code' => 208, 'text' => __('system_208', true));
			} else {
				$response = array('status' => 'ERR', 'code' => 107, 'text' => __('system_107', true));
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}

	public function pjActionUpdate()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			if ($this->_post->check('qty') && !$this->cart->isEmpty()) {
				$qty_arr = $this->_post->toArray('qty');
				$cart_arr = $this->get('cart_arr');
				foreach ($qty_arr as $hash => $qty) {
					foreach ($cart_arr as $item) {
						if ($item['is_cart'] == 1) {

							if ($hash == md5($item['key_data'])) {
								if ((int) $qty > 0) {
									$this->cart->update($item['key_data'], $qty);
								} else {
									$this->cart->remove($hash);
								}
								$response = array('status' => 'OK', 'code' => 209, 'text' => __('system_209', true));
								break;
							}
						}
					}
				}
			}
			if ($this->_post->check('tax_id')) {
				$_SESSION[$this->defaultTax] = $this->_post->toInt('tax_id');
			}
			if (!isset($response)) {
				$response = array('status' => 'ERR', 'code' => 108, 'text' => __('system_108', true));
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}

	public function pjActionGetAddress()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			if ($this->_get->check('id') && $this->_get->toInt('id') > 0) {
				$address_arr = pjAddressModel::factory()
					->select('t1.*, t2.content AS country_name')
					->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.country_id AND t2.field='name' AND t2.locale='" . $this->getLocaleId() . "'", 'left outer')
					->find($this->_get->toInt('id'))
					->getData();

				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => '', 'result' => $address_arr));
			}
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
		}
	}

	public function pjActionGetPaymentForm()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			$arr = pjOrderModel::factory()->select('t1.*, t2.client_name, t2.email, t2.phone')
				->join('pjClient', 't2.id=t1.client_id', 'left outer')
				->find($this->_get->toInt('order_id'))->getData();
			if (pjObject::getPlugin('pjPayments') !== NULL) {
				$pjPlugin = pjPayments::getPluginName($arr['payment_method']);
				if (pjObject::getPlugin($pjPlugin) !== NULL) {
					$this->set('params', $pjPlugin::getFormParams(array('payment_method' => $arr['payment_method']), array(
						'locale_id'	 => $this->getLocaleId(),
						'return_url'	=> $this->option_arr['o_thankyou_page'],
						'id'			=> $arr['id'],
						'foreign_id'	=> $this->getForeignId(),
						'uuid'		  => $arr['uuid'],
						'name'		  => @$client['client_name'],
						'email'		 => @$client['email'],
						'phone'		 => @$client['phone'],
						'amount'		=> $arr['total'],
						'cancel_hash'   => sha1($arr['uuid'] . strtotime($arr['created']) . PJ_SALT),
						'currency_code' => $this->option_arr['o_currency'],
					)));
				}

				if ($arr['payment_method'] == 'bank') {
					$bank_account = pjMultiLangModel::factory()->select('t1.content')
						->where('t1.model', 'pjOption')
						->where('t1.locale', $this->getLocaleId())
						->where('t1.field', 'o_bank_account')
						->limit(1)
						->findAll()->getDataIndex(0);
					$this->set('bank_account', $bank_account['content']);
				}
			}
			$this->set('category_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));
			$this
				->set('arr', $arr)
				->set('get', $this->_get->raw());
		}
	}

	public function pjActionProcessOrder()
	{
		$this->setAjax(true);
		$company_id = $_SESSION[$this->defaultCompany]['id'];
		$_SESSION[$this->defaultForm]['company_id'] =  $company_id;
		if ($this->isXHR()) {
			if (!$this->_post->check('sc_preview') || !isset($_SESSION[$this->defaultForm]) || empty($_SESSION[$this->defaultForm])) {
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 109, 'text' => __('system_109', true)));
			}

			if ((int) $this->option_arr['o_bf_captcha'] === 3 && $this->option_arr['o_captcha_type_front'] == 'system' && (!isset($_SESSION[$this->defaultForm]['captcha']) ||
				!pjCaptcha::validate($_SESSION[$this->defaultForm]['captcha'], $_SESSION[$this->defaultCaptcha]))) {
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 110, 'text' => __('system_110', true)));
			}

			$data = array();
			if ($this->isLoged()) {
				$data['client_id'] = $this->getUserId();

				if (isset($_SESSION[$this->defaultForm]['b_save'])) {
					$this->pjActionSaveToAddressBook($data['client_id'], $_SESSION[$this->defaultForm], 'b_');
				}
				if (isset($_SESSION[$this->defaultForm]['s_save']) && !isset($_SESSION[$this->defaultForm]['same_as'])) {
					$this->pjActionSaveToAddressBook($data['client_id'], $_SESSION[$this->defaultForm], 's_');
				}
			} else {
				$pjClientModel = pjClientModel::factory();
				if (isset($company_id) && (int)$company_id > 0) {
					$pjClientModel->where('t1.company_id', $company_id);
				}
				$pjClientModel->beforeValidate($this->option_arr);

				if (!$pjClientModel->validates($_SESSION[$this->defaultForm])) {
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 111, 'text' => __('system_111', true)));
				}

				$client = $pjClientModel
					->where('t1.email', $_SESSION[$this->defaultForm]['email'])
					->limit(1)
					->findAll()
					->getData();

				if (!empty($client)) {
					$client = $client[0];
					if ($client['password'] != $_SESSION[$this->defaultForm]['password']) {
						pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 112, 'text' => __('system_112', true)));
					} elseif ($client['status'] != 'T') {
						pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 132, 'text' => __('system_132', true)));
					} else {
						// Client data matches (just not loged in)
						$data['client_id'] = $client['id'];
						// Update client data
						$pjClientModel->reset()->set('id', $client['id'])->modify(array(
							'phone' => $_SESSION[$this->defaultForm]['phone'],
							'url' => $_SESSION[$this->defaultForm]['url'],
							'client_name' => $_SESSION[$this->defaultForm]['client_name']
						));

						if (isset($_SESSION[$this->defaultForm]['b_save'])) {
							$this->pjActionSaveToAddressBook($data['client_id'], $_SESSION[$this->defaultForm], 'b_');
						}
						if (isset($_SESSION[$this->defaultForm]['s_save']) && !isset($_SESSION[$this->defaultForm]['same_as'])) {
							$this->pjActionSaveToAddressBook($data['client_id'], $_SESSION[$this->defaultForm], 's_');
						}
					}
				} else {
					// Create
					$client_id = $pjClientModel->setAttributes($_SESSION[$this->defaultForm])->insert()->getInsertId();

					if ($client_id !== false && (int) $client_id > 0) {
						$data['client_id'] = $client_id;
						$this->pjActionSaveToAddressBook($data['client_id'], $_SESSION[$this->defaultForm], 'b_');
					} else {
						pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 113, 'text' => __('system_113', true)));
					}
				}
			}
			if (isset($_SESSION[$this->defaultVoucher]) && isset($_SESSION[$this->defaultVoucher]['voucher_code'])) {
				$data['voucher'] = $_SESSION[$this->defaultVoucher]['voucher_code'];
			}
			$data['status'] = 'new';
			$data['company_id'] = $company_id;
			$data['uuid'] = pjUtil::uuid();
			$data['ip'] = $_SERVER['REMOTE_ADDR'];
			$data['locale_id'] = $this->getLocaleId();

			$data = array_merge($_SESSION[$this->defaultForm], $data);

			if (isset($data['payment_method']) && $data['payment_method'] != 'creditcard') {
				unset($data['cc_type']);
				unset($data['cc_num']);
				unset($data['cc_exp_month']);
				unset($data['cc_exp_year']);
				unset($data['cc_code']);
			}

			$pjOrderModel = pjOrderModel::factory();
			if (!$pjOrderModel->validates($data)) {
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 114, 'text' => __('system_114', true)));
			}

			$stock_id = $stocks = $product_id = array();
			$cart_arr = $this->get('cart_arr');
			foreach ($cart_arr as $cart_item) {
				if ($cart_item['is_cart'] == 1) {

					$stock_id[] = $cart_item['stock_id'];
					$product_id[] = $cart_item['product_id'];
				}
			}
			if (empty($stock_id)) {
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 115, 'text' => __('system_115', true)));
			}
			$pjStockModel = pjStockModel::factory();
				// ->where('t1.status', 'T');
			if (isset($company_id) && (int)$company_id > 0) {
				$pjStockModel->where('t1.company_id', $company_id);
			}
			$stock_arr = $pjStockModel->whereIn('t1.id', $stock_id)->findAll()->getData();
			foreach ($stock_arr as $stock) {
				$stocks[$stock['id']] = $stock;
			}

			if (empty($stocks)) {
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 116, 'text' => __('system_116', true)));
			}

			$pjExtraItemModel = pjExtraItemModel::factory();
			if (isset($company_id) && (int)$company_id > 0) {
				$pjExtraItemModel->where('t1.company_id', $company_id);
			}
			$extra_arr = pjExtraModel::factory()->whereIn('t1.product_id', $product_id)->findAll()->getDataPair('id', 'price');
			foreach ($extra_arr as $e_id => $e_price) {
				$extra_arr[$e_id] = array(
					'price' => $e_price,
					'extra_items' => $pjExtraItemModel->reset()
						->join('pjExtra', "t2.id=t1.extra_id AND t2.type='multi'", 'inner')
						->where('t1.extra_id', $e_id)->findAll()->getDataPair('id', 'price')
				);
			}

			$calc_price = pjAppController::pjActionCalcPrices($product_id, $extra_arr, $cart_arr, $stocks, @$_SESSION[$this->defaultVoucher], $this->option_arr, isset($_SESSION[$this->defaultTax]) ? $_SESSION[$this->defaultTax] : null, 'front');
			if ($calc_price == false) {
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 118, 'text' => __('system_118', true)));
			}

			$data['tax_id'] = isset($_SESSION[$this->defaultTax]) ? (int) $_SESSION[$this->defaultTax] : 'NULL';

			$data['price'] = $calc_price['price'];
			$data['discount'] = $calc_price['discount'];
			$data['insurance'] = $calc_price['insurance'];
			$data['shipping'] = $calc_price['shipping'];
			$data['tax'] = $calc_price['tax'];
			$data['total'] = $calc_price['total'];

			$order_id = $pjOrderModel->setAttributes($data)->insert()->getInsertId();
			if ($order_id !== false && (int) $order_id > 0) {
				$pjOrderStockModel = pjOrderStockModel::factory();
				$pjOrderExtraModel = pjOrderExtraModel::factory();

				foreach ($cart_arr as $cart_item) {
					if ($cart_item['is_cart'] == 1) {

						$item = unserialize($cart_item['key_data']);
						$order_stock_id = $pjOrderStockModel->reset()->setAttributes(array(
							'order_id' => $order_id,
							'company_id' => $company_id,
							'product_id' => $cart_item['product_id'],
							'stock_id' => $cart_item['stock_id'],
							'price' => @$stocks[$cart_item['stock_id']]['price'],
							'qty' => $cart_item['qty']
						))->insert()->getInsertId();

						if ($order_stock_id !== FALSE && (int) $order_stock_id > 0) {
							// Update available stock qty
							$pjStockModel->reset()->set('id', $cart_item['stock_id'])->modify(array('qty' => ":qty - " . (int) $cart_item['qty']));

							if (isset($item['extra']) && is_array($item['extra'])) {
								$extras = array(
									'order_id' => $order_id,
									'company_id' => $company_id,

									'order_stock_id' => $order_stock_id
								);
								foreach ($item['extra'] as $extra) {
									if (strpos($extra, ".") !== FALSE) {
										list($extras['extra_id'], $extras['extra_item_id']) = explode(".", $extra);
										$extras['price'] = @$extra_arr[$extras['extra_id']]['extra_items'][$extras['extra_item_id']];
									} else {
										$extras['extra_id'] = $extra;
										$extras['extra_item_id'] = NULL;
										$extras['price'] = @$extra_arr[$extra]['price'];
									}
									$pjOrderExtraModel->reset()->setAttributes($extras)->insert();
								}
							}
						}
					}
				}

				# Confirmation email(s)/SMS
				$order_arr = $pjOrderModel
					->reset()
					->select(sprintf("t1.*,
						AES_DECRYPT(t1.cc_type, '%1\$s') AS `cc_type`,
						AES_DECRYPT(t1.cc_num, '%1\$s') AS `cc_num`,
						AES_DECRYPT(t1.cc_exp_month, '%1\$s') AS `cc_exp_month`,
						AES_DECRYPT(t1.cc_exp_year, '%1\$s') AS `cc_exp_year`,
						AES_DECRYPT(t1.cc_code, '%1\$s') AS `cc_code`,
						t2.content AS `b_country`, t3.content AS `s_country`,
						t4.email, t4.client_name, t4.phone, t4.url, AES_DECRYPT(t4.password, '%1\$s') AS `password`", PJ_SALT))
					->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.b_country_id AND t2.locale=t1.locale_id AND t2.field='name'", 'left outer')
					->join('pjMultiLang', "t3.model='pjCountry' AND t3.foreign_id=t1.s_country_id AND t3.locale=t1.locale_id AND t3.field='name'", 'left outer')
					->join('pjClient', 't4.id=t1.client_id', 'left outer')
					->find($order_id)
					->getData();

				$order_arr['has_digital'] = pjAppController::pjActionCheckDigital($order_id);

				$invoice_arr = null;
				if (class_exists('pjInvoiceModel')) {
					$invoice_arr = $this->pjActionGenerateInvoice($order_id);
				}

				pjFront::pjActionConfirmSend($this->option_arr, $order_arr, PJ_SALT, 'confirmation', $this->getLocaleId());

				// Reset SESSION vars
				$this->cart->clear();

				$_SESSION[$this->defaultForm] = NULL;
				unset($_SESSION[$this->defaultForm]);

				$_SESSION[$this->defaultVoucher] = NULL;
				unset($_SESSION[$this->defaultVoucher]);

				$_SESSION[$this->defaultTax] = NULL;
				unset($_SESSION[$this->defaultTax]);

				$_SESSION[$this->defaultCaptcha] = NULL;
				unset($_SESSION[$this->defaultCaptcha]);

				pjAppController::jsonResponse(array(
					'status' => 'OK',
					'code' => 210,
					'text' => __('system_210', true),
					'order_id' => $order_id,
					'invoice_id' => @$invoice_arr['data']['id'],
					'payment_method' => $data['payment_method']
				));
			} else {
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 119, 'text' => __('system_119', true)));
			}
		}
		exit;
	}

	static public function pjActionCalcPrice($option_arr, $cart_arr, $stock_arr, $extra_arr, $o_shipping, $o_tax, $o_free, $voucher_sess)
	{
		$total = $tax = $insurance = $shipping = $discount = $amount = 0;
		$voucher_discount = $discount_print = $voucher_code = '';
		$p_arr = array();
		$subtotal_arr = array();

		foreach ($cart_arr as $key => $cart_item) {
			if ($cart_item['is_cart'] == 1) {

				$item = unserialize($cart_item['key_data']);

				$price = (@$stock_arr[$cart_item['stock_id']]['price']);
				$p_arr[$key] = $price;
				$extra_price = 0;

				if (isset($item['extra']) && !empty($item['extra'])) {
					$extras = array();
					foreach ($item['extra'] as $eid) {
						if (strpos($eid, ".") === FALSE) {
							foreach ($extra_arr as $extra) {
								if ($extra['id'] == $eid) {
									$extra_price += $extra['price'];
									break;
								}
							}
						} else {
							list($e_id, $ei_id) = explode(".", $eid);
							foreach ($extra_arr as $extra) {
								if ($extra['id'] == $e_id && isset($extra['extra_items']) && !empty($extra['extra_items'])) {
									foreach ($extra['extra_items'] as $extra_item) {
										if ($extra_item['id'] == $ei_id) {
											$extra_price += $extra_item['price'];
											break;
										}
									}
									break;
								}
							}
						}
					}
				}

				$price += $extra_price;
				$subtotal = $price * $cart_item['qty'];
				$amount += $subtotal;

				$subtotal_arr[$key] = $subtotal;

				if ($voucher_sess && $voucher_sess['voucher_apply'] == 'each') {
					$discount += pjUtil::getDiscount($subtotal, $item['product_id'], $voucher_sess);

					if (isset($voucher_sess) && !empty($voucher_sess)) {
						$voucher_code = $voucher_sess['voucher_code'];
						$voucher_discount = $voucher_sess['voucher_discount'];
						switch ($voucher_sess['voucher_type']) {
							case 'percent':
								$discount_print = $voucher_discount . '%';
								break;
							case 'amount':
								$discount_print = pjCurrency::formatPrice($voucher_discount);
								break;
						}
					}
				}

				$shipping = $o_shipping != null ? (float) $o_shipping : 0;
				if ($o_free != null && (float) $o_free > 0 && (float) $amount >= (float) $o_free) {
					$shipping = 0;
				}

				switch ($option_arr['o_insurance_type']) {
					case 'percent':
						$insurance = (($amount) * (float) $option_arr['o_insurance']) / 100;
						break;
					case 'amount':
						$insurance = (float) $option_arr['o_insurance'];
						break;
					default:
						$insurance = 0;
				}

				if ($o_tax != null && (float) $o_tax > 0) {
					$tax = (($amount) * (float) $o_tax) / 100;
				}
			}
		}

		if ($voucher_sess && $voucher_sess['voucher_apply'] == 'total') {
			if (isset($voucher_sess) && !empty($voucher_sess)) {
				$voucher_code = $voucher_sess['voucher_code'];
				$voucher_discount = $voucher_sess['voucher_discount'];
				switch ($voucher_sess['voucher_type']) {
					case 'percent':
						$discount_print = $voucher_discount . '%';
						$discount = ($amount * $voucher_discount) / 100;
						break;
					case 'amount':
						$discount_print = pjCurrency::formatPrice($voucher_discount);
						$discount = $voucher_discount;
						break;
				}
			}
		}

		$total = $amount - $discount + $tax + $shipping + $insurance;
		$total = $total > 0 ? $total : 0;

		return compact('p_arr', 'total', 'subtotal_arr', 'tax', 'insurance', 'shipping', 'discount', 'amount', 'voucher_code', 'voucher_discount', 'discount_print');
	}
}
