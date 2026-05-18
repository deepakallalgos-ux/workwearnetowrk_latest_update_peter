<?php
if (!defined("ROOT_PATH")) {
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjFrontPublic extends pjFront
{
	public function __construct()
	{
		parent::__construct();

		$this->setAjax(true);

		$this->setLayout('pjActionEmpty');
	}

	public function pjActionRouter()
	{
		$this->setAjax(false);
		$this->setLayout('pjActionSeo');
		if ($this->_get->check('_escaped_fragment_')) {
			$templates = array('Cart', 'Checkout', 'Preview', 'Login', 'Forgot', 'Profile', 'Register', 'Product', 'Products', 'Favs');
			preg_match('/^\/(\w+).*/', $this->_get->toString('_escaped_fragment_'), $m);
			preg_match('/^(.*)-(\d+)\.html/', $this->_get->toString('_escaped_fragment_'), $p);
			$detail_page = false;
			if (isset($m[1]) && in_array($m[1], $templates)) {
				$template = 'pjAction' . $m[1];
				if (method_exists($this, $template)) {
					$this->$template();
				}
				$this->setTemplate('pjFrontPublic', $template);
			} elseif ((isset($p[2]) && (int) $p[2] > 0)) {
				$_REQUEST['_escaped_fragment_'] = '/Product/' . $p[2];
				$_REQUEST['layout'] = $this->option_arr['o_layout'];
				$template = 'pjActionProduct';
				if (method_exists($this, $template)) {
					$this->$template();
				}
				$detail_page = true;
				$this->setTemplate('pjFrontPublic', $template);
			} elseif ($_REQUEST['_escaped_fragment_'] == '') {
				$_REQUEST['_escaped_fragment_'] = '/Products/';
				$_REQUEST['layout'] = $this->option_arr['o_layout'];
				$template = 'pjActionProducts';
				if (method_exists($this, $template)) {
					$this->$template();
				}
				$this->setTemplate('pjFrontPublic', $template);
			}
			$this->set('detail_page', $detail_page);
		}
	}

	public function pjActionCart()
	{
		if ($this->isXHR() || $this->_get->check('_escaped_fragment_')) {
			$is_ip_blocked = pjBase::isBlockedIp(pjUtil::getClientIp(), $this->option_arr);
			if ($is_ip_blocked == true) {
				$this->set('status', 'IP_BLOCKED');
			} else {
				if (!$this->cart->isEmpty() && !pjUtil::isOptionEnumYes($this->option_arr, 'o_disable_orders')) {
					$data = $this->pjActionGetCart();

					if (isset($_SESSION[$this->defaultTax]) && (int) $_SESSION[$this->defaultTax] > 0) {
						foreach ($data['tax_arr'] as $item) {
							if ($item['id'] == $_SESSION[$this->defaultTax]) {
								$this->set('o_tax', $item['tax'])
									->set('o_shipping', $item['shipping'])
									->set('o_free', $item['free']);
								break;
							}
						}
					}

					$this
						->set('arr', $data['arr'])
						->set('extra_arr', $data['extra_arr'])
						->set('order_arr', $data['order_arr'])
						->set('attr_arr', $data['attr_arr'])
						->set('stock_arr', $data['stock_arr'])
						->set('tax_arr', $data['tax_arr'])
						->set('image_arr', $data['image_arr'])
					;
				}
				$this->set('category_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));
			}
		}
	}

	public function pjActionCheckout()
	{
		if ($this->isXHR() || $this->_get->check('_escaped_fragment_')) {
			$company_id = pjUtil::getActiveCompanyId($this->defaultCompany);
			if (empty($company_id)) {
				$company_id_term = 1;
			} else {
				$company_id_term = $company_id;
			}

			$is_ip_blocked = pjBase::isBlockedIp(pjUtil::getClientIp(), $this->option_arr);
			if ($is_ip_blocked == true) {
				$this->set('status', 'IP_BLOCKED');
			} else {
				if ($this->_post->check('sc_checkout')) {
					if ((int) $this->option_arr['o_bf_captcha'] === 3 && $this->option_arr['o_captcha_type_front'] == 'system') {
						if (
							!$this->_post->check('captcha') || !pjValidation::pjActionNotEmpty($this->_post->toString('captcha')) ||
							!isset($_SESSION[$this->defaultCaptcha]) || !pjValidation::pjActionNotEmpty($_SESSION[$this->defaultCaptcha]) ||
							!pjCaptcha::validate($this->_post->toString('captcha'), $_SESSION[$this->defaultCaptcha])
						) {
							pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 128, 'text' => __('system_128', true)));
						}
					}

					$_SESSION[$this->defaultForm] = $this->_post->raw();
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 211, 'text' => __('system_211', true)));
				} else {
					if (!$this->cart->isEmpty() && !pjUtil::isOptionEnumYes($this->option_arr, 'o_disable_orders')) {
						if (
							$this->pjActionShowShipping() && (!isset($_SESSION[$this->defaultTax]) || empty($_SESSION[$this->defaultTax])) &&
							0 < pjTaxModel::factory()->findCount()->getData()
						) {
							$this->set('status', 'ERR');
							# Shipping location is not set
							$this->set('code', '100');
						} else {
							$this->set(
								'country_arr',
								pjBaseCountryModel::factory()
									->select('t1.id, t2.content AS name')
									->join('pjBaseMultiLang', "t2.model='pjBaseCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='" . $this->getLocaleId() . "'", 'left outer')
									->orderBy('`name` ASC')->findAll()->getData()
							);

							// $terms = $this->getModel('Option')
							// 	->reset()
							// 	// ->select('t1.*, t2.content AS `terms_url_{$company_id}`, t3.content AS `terms_body_{$company_id}`')

							// 	->select(sprintf(
							// 		't1.*, t2.content AS terms_url_%d, t3.content AS terms_body_%d',
							// 		$company_id,
							// 		$company_id
							// 	))

							// 	->join('pjMultiLang', sprintf("t2.model='pjOption' AND t2.foreign_id='%u' AND t2.locale='%u' AND t2.field='terms_url'", $this->getForeignId(), $this->pjActionGetLocale()), 'left outer')
							// 	->join('pjMultiLang', sprintf("t3.model='pjOption' AND t3.foreign_id='%u' AND t3.locale='%u' AND t3.field='terms_body'", $this->getForeignId(), $this->pjActionGetLocale()), 'left outer')
							// 	->limit(1)
							// 	->findAll()
							// 	->getData();
							$terms = $this->getModel('Option')
								->reset()
								->select(sprintf(
									't1.*, t2.content AS terms_url_%d, t3.content AS terms_body_%d',
									$company_id_term,
									$company_id_term
								))
								->join(
									'pjMultiLang',
									sprintf(
										"t2.model='pjOption' AND t2.foreign_id='%u' AND t2.locale='%u' AND t2.field='terms_url_%d'",
										$this->getForeignId(),
										$this->pjActionGetLocale(),
										$company_id_term
									),
									'left outer'
								)
								->join(
									'pjMultiLang',
									sprintf(
										"t3.model='pjOption' AND t3.foreign_id='%u' AND t3.locale='%u' AND t3.field='terms_body_%d'",
										$this->getForeignId(),
										$this->pjActionGetLocale(),
										$company_id_term
									),
									'left outer'
								)
								->limit(1)
								->findAll()
								->getData();

							$this->set('terms', @$terms[0]);
							$this->set('company_id', $company_id);

							if ($this->isLoged()) {
								$pjAddressModel = pjAddressModel::factory();
								$pjAddressModel->where('t1.client_id', $this->getUserId());

								// ✔ Add company filter only if valid
								if (isset($company_id) && (int)$company_id > 0) {
									$pjAddressModel->where('t1.company_id', $company_id);
								}

								$this->set(
									'address_arr',
									$pjAddressModel
										->findAll()
										->getData()
								);
							}
							$this->set('status', 'OK');

							$data = $this->pjActionGetCart();
							$this
								->set('arr', $data['arr'])
								->set('extra_arr', $data['extra_arr'])
								->set('attr_arr', $data['attr_arr']);
						}
					} else {
						$this->set('status', 'ERR');
						# Empty cart
						$this->set('code', '101');
					}
					$this->set('category_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));

					$bank_account = pjMultiLangModel::factory()
						->select('t1.content')
						->where('t1.model', 'pjOption')
						->where('t1.locale', $this->getLocaleId())
						->where('t1.field', 'o_bank_account')
						->limit(1)
						->findAll()->getDataIndex(0);
					$this->set('bank_account', $bank_account ? $bank_account['content'] : '');

					if (pjObject::getPlugin('pjPayments') !== NULL) {

						$this->set('payment_option_arr', pjPaymentOptionModel::factory()->getOptions($this->getForeignId()));
						$this->set('payment_titles', pjPayments::getPaymentTitles($this->getForeignId(), $this->getLocaleId()));
					} else {
						$this->set('payment_titles', __('payment_methods', true));
					}
				}
			}
		}
	}

	public function pjActionPreview()
	{
		if ($this->isXHR() || $this->_get->check('_escaped_fragment_')) {
			$is_ip_blocked = pjBase::isBlockedIp(pjUtil::getClientIp(), $this->option_arr);
			if ($is_ip_blocked == true) {
				$this->set('status', 'IP_BLOCKED');
			} else {
				if (!$this->cart->isEmpty() && !pjUtil::isOptionEnumYes($this->option_arr, 'o_disable_orders')) {
					if (
						$this->pjActionShowShipping() && (!isset($_SESSION[$this->defaultTax]) || empty($_SESSION[$this->defaultTax])) &&
						0 < pjTaxModel::factory()->findCount()->getData()
					) {
						$this->set('status', 'ERR');
						$this->set('code', '100');
						//Shipping location is not set
					} elseif (!isset($_SESSION[$this->defaultForm]) || empty($_SESSION[$this->defaultForm])) {
						$this->set('status', 'ERR');
						$this->set('code', '102');
						//Checkout form not filled
					} else {
						$this->set(
							'country_arr',
							pjBaseCountryModel::factory()
								->select('t1.id, t2.content AS name')
								->join('pjBaseMultiLang', "t2.model='pjBaseCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='" . $this->getLocaleId() . "'", 'left outer')
								->orderBy('`name` ASC')->findAll()->getData()
						);
						$this->set('status', 'OK');

						$data = $this->pjActionGetCart();
						$this
							->set('arr', $data['arr'])
							->set('extra_arr', $data['extra_arr'])
							->set('attr_arr', $data['attr_arr']);
					}
				} else {
					$this->set('status', 'ERR');
					$this->set('code', '101');
					//Empty cart
				}
				$this->set('category_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));

				$bank_account = pjMultiLangModel::factory()
					->select('t1.content')
					->where('t1.model', 'pjOption')
					->where('t1.locale', $this->getLocaleId())
					->where('t1.field', 'o_bank_account')
					->limit(1)
					->findAll()->getDataIndex(0);
				$this->set('bank_account', $bank_account ? $bank_account['content'] : '');

				if (pjObject::getPlugin('pjPayments') !== NULL) {
					$this->set('payment_titles', pjPayments::getPaymentTitles($this->getForeignId(), $this->getLocaleId()));
				} else {
					$this->set('payment_titles', __('payment_methods', true));
				}
			}
		}
	}

	public function pjActionLogin()
	{
		if ($this->isXHR() || $this->_get->check('_escaped_fragment_')) {
			$company_id = pjUtil::getActiveCompanyId($this->defaultCompany);

			$is_ip_blocked = pjBase::isBlockedIp(pjUtil::getClientIp(), $this->option_arr);
			if ($is_ip_blocked == true) {
				$this->set('status', 'IP_BLOCKED');
			} else {
				if ($this->_post->check('sc_login')) {
					if (
						!$this->_post->check('email') || !pjValidation::pjActionNotEmpty($this->_post->toString('email')) || !pjValidation::pjActionEmail($this->_post->toString('email')) ||
						!$this->_post->check('password') || !pjValidation::pjActionNotEmpty($this->_post->toString('password'))
					) {
						pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 120, 'text' => __('system_120', true)));
					}

					$pjClientModel = pjClientModel::factory();

					if (isset($company_id) && (int)$company_id > 0) {
						$pjClientModel->where('t1.company_id', $company_id);
					}

					$arr = $pjClientModel->where('t1.email', $this->_post->toString('email'))->limit(1)->findAll()->getData();
					if (empty($arr)) {
						pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 121, 'text' => __('system_121', true)));
					}
					$arr = $arr[0];
					if ($arr['password'] != $this->_post->toString('password')) {
						pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 122, 'text' => __('system_122', true)));
					}
					if ($arr['status'] != 'T') {
						pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 132, 'text' => __('system_132', true)));
					}

					$pjClientModel->reset()->set('id', $arr['id'])->modify(array('last_login' => ':NOW()'));

					$_SESSION[$this->defaultUser] = $arr;
					# ---
					$hash = md5(PJ_SALT . $this->getUserId());
					$this->cart->transform($hash);
					$_SESSION[$this->defaultHash] = $hash;
					# ---
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 212, 'text' => __('system_212', true)));
				}
				$this->set('category_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));
			}
		}
	}

	public function pjActionForgot()
	{
		if ($this->isXHR() || $this->_get->check('_escaped_fragment_')) {
			$company_id = pjUtil::getActiveCompanyId($this->defaultCompany);

			$is_ip_blocked = pjBase::isBlockedIp(pjUtil::getClientIp(), $this->option_arr);
			if ($is_ip_blocked == true) {
				$this->set('status', 'IP_BLOCKED');
			} else {
				if ($this->_post->check('sc_forgot')) {
					if (!$this->_post->check('email') || !pjValidation::pjActionNotEmpty($this->_post->toString('email')) || !pjValidation::pjActionEmail($this->_post->toString('email'))) {
						pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 123, 'text' => __('system_123', true)));
					}


					$pjClientModel = pjClientModel::factory()
						->where('t1.email', $this->_post->toString('email'));

					if (isset($company_id) && (int)$company_id > 0) {
						$pjClientModel->where('t1.company_id', $company_id);
					}

					$arr = $pjClientModel
						->limit(1)
						->findAll()
						->getData();

					if (empty($arr)) {
						pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 124, 'text' => __('system_124', true)));
					}
					$arr = $arr[0];

					$notificationModel = pjNotificationModel::factory()
						->where('recipient', 'client')
						->where('transport', 'email')
						->where('variant', 'forgot');

					// ✔ Add company filter only if valid
					if (isset($company_id) && (int)$company_id > 0) {
						$notificationModel->where('t1.company_id', $company_id);
					}

					$notification = $notificationModel
						->findAll()
						->getDataIndex(0);

					if ((int) $notification['id'] > 0 && $notification['is_active'] == 1) {
						$resp = pjAppController::pjActionGetSubjectMessage($notification, $this->getLocaleId());
						$lang_message = $resp['lang_message'];
						$lang_subject = $resp['lang_subject'];
						if (count($lang_message) === 1 && count($lang_subject) === 1) {
							$search = array('{ClientName}', '{ClientPassword}', '{ClientEmail}', '{ClientPhone}', '{ClientURL}', '{StoreName}');
							$replace = array($arr['client_name'], $arr['password'], $arr['email'], $arr['phone'], $arr['url'], __('lblStoreName', true));
							$subject_client = str_replace($search, $replace, $lang_subject[0]['content']);
							$message_client = str_replace($search, $replace, $lang_message[0]['content']);
							$Email = self::getMailer($this->option_arr);
							// $r = $Email
							// 	->setTo($arr['email'])
							// 	->setSubject($subject_client)
							// 	->send(pjUtil::textToHtml($message_client));
							// $message = pjUtil::textToHtml($this->_post->toString('message'));

							if ($this->option_arr['o_send_email'] == 'flexmail') {

								$r = $this->sendFlexMail($arr['email'], $subject_client, $message_client, $this->option_arr);
							} else {

								$r = $Email

									->setTo($arr['email'])

									->setSubject($subject_client)

									->send($message_client);
							}

							if (isset($r) && $r) {
								pjAppController::jsonResponse(array('status' => 'OK', 'code' => 213, 'text' => __('system_213', true)));
							} else {
								pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 125, 'text' => __('system_125', true)));
							}
						}
					}
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 125, 'text' => __('system_125', true)));
				}
				$this->set('category_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));
			}
		}
	}

	public function pjActionProfile()
	{
		if ($this->isXHR() || $this->_get->check('_escaped_fragment_')) {
			$is_ip_blocked = pjBase::isBlockedIp(pjUtil::getClientIp(), $this->option_arr);
			if ($is_ip_blocked == true) {
				$this->set('status', 'IP_BLOCKED');
			} else {
				$company_id = isset($_SESSION[$this->defaultCompany]['id']) ? (int) $_SESSION[$this->defaultCompany]['id'] : null;

				$pjClientModel = pjClientModel::factory();
				if (isset($company_id) && (int)$company_id > 0) {
					$pjClientModel->where('t1.company_id', $company_id);
				}
				if ($this->_post->check('sc_profile')) {
					$pjClientModel->beforeValidate($this->option_arr);

					if (!$pjClientModel->validates($this->_post->raw())) {
						pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 126, 'text' => __('system_126', true)));
					}

					if (0 != $pjClientModel->where('t1.email', $this->_post->toString('email'))->where('t1.id !=', $this->getUserId())->findCount()->getData()) {
						pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 127, 'text' => __('system_127', true)));
					}

					$pjClientModel->reset()->set('id', $this->getUserId())->modify($this->_post->raw());

					$pjAddressModel = pjAddressModel::factory();
					if (isset($company_id) && (int)$company_id > 0) {
						$pjAddressModel->where('company_id', $company_id);
					}
					# Delete existing in DB and not presented in POST
					$pjAddressModel->where('client_id', $this->getUserId());
					if ($this->_post->check('name') && $name_arr = $this->_post->toArray('name')) {
						$pjAddressModel->whereNotIn('id', array_keys($name_arr));
					}
					$pjAddressModel->eraseAll();

					if ($this->_post->check('name') && $name_arr = $this->_post->toArray('name')) {
						$post = $this->_post->raw();
						$client_id = $this->getUserId();
						$pjAddressModel->begin();
						foreach ($name_arr as $k => $v) {
							if (empty($v)) {
								continue;
							}
							if (strpos($k, 'new_') === 0) {
								# Add new
								$pjAddressModel->reset()->setAttributes(array(
									'client_id' => $client_id,
									'country_id' => $post['country_id'][$k],
									'state' => $post['state'][$k],
									'city' => $post['city'][$k],
									'zip' => $post['zip'][$k],
									'address_1' => $post['address_1'][$k],
									'address_2' => $post['address_2'][$k],
									'name' => $post['name'][$k],
									'is_default_shipping' => (@$post['is_default_shipping'] == $k ? 1 : 0),
									'is_default_billing' => (@$post['is_default_billing'] == $k ? 1 : 0)
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
									'is_default_shipping' => (@$post['is_default_shipping'] == $k ? 1 : 0),
									'is_default_billing' => (@$post['is_default_billing'] == $k ? 1 : 0)
								));
							}
						}
						$pjAddressModel->commit();
					}

					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 214, 'text' => __('system_214', true)));
				} else {
					$this->set('arr', $pjClientModel->find($this->getUserId())->getData());

					$this->set('address_arr', pjAddressModel::factory()
						->where('t1.client_id', $this->getUserId())
						->orderBy('FIELD(`is_default_shipping`,1,0), FIELD(`is_default_billing`,1,0), t1.id ASC')
						->findAll()
						->getData());

					$this->set('country_arr', pjBaseCountryModel::factory()
						->select('t1.id, t2.content AS name')
						->join('pjBaseMultiLang', "t2.model='pjBaseCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='" . $this->getLocaleId() . "'", 'left outer')
						->orderBy('`name` ASC')->findAll()->getData());
				}
				$this->set('category_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));
			}
		}
	}

	public function pjActionRegister()
	{
		if ($this->isXHR() || $this->_get->check('_escaped_fragment_')) {
			$is_ip_blocked = pjBase::isBlockedIp(pjUtil::getClientIp(), $this->option_arr);
			$company_id = pjUtil::getActiveCompanyId($this->defaultCompany);

			if ($is_ip_blocked == true) {
				$this->set('status', 'IP_BLOCKED');
			} else {
				if ($this->_post->check('sc_register')) {
					$pjClientModel = pjClientModel::factory();

					if (
						!$this->_post->check('captcha') || !pjValidation::pjActionNotEmpty($this->_post->toString('captcha')) ||
						!isset($_SESSION[$this->defaultCaptcha]) || !pjValidation::pjActionNotEmpty($_SESSION[$this->defaultCaptcha]) ||
						!pjCaptcha::validate($this->_post->toString('captcha'), $_SESSION[$this->defaultCaptcha])
					) {
						pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 128, 'text' => __('system_128', true)));
					}
					$_SESSION[$this->defaultCaptcha] = NULL;
					unset($_SESSION[$this->defaultCaptcha]);

					$pjClientModel->beforeValidate($this->option_arr);

					if (!$pjClientModel->validates($this->_post->raw())) {
						pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 129, 'text' => __('system_129', true)));
					}

					if (0 != $pjClientModel->where('t1.email', $this->_post->toString('email'))->findCount()->getData()) {
						pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 130, 'text' => __('system_130', true)));
					}
					$data = $this->_post->raw();
					$data['company_id'] = $company_id;
					$client_id = $pjClientModel->setAttributes($data)->insert()->getInsertId();
					if ($client_id === FALSE || (int) $client_id <= 0) {
						pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 131, 'text' => __('system_131', true)));
					}

					$notificationModel = pjNotificationModel::factory()
						->where('recipient', 'client')
						->where('transport', 'email')
						->where('variant', 'account');

					// ✔ apply company_id filter only when valid
					if (isset($company_id) && (int)$company_id > 0) {
						$notificationModel->where('t1.company_id', $company_id);
					}

					$notification = $notificationModel
						->findAll()
						->getDataIndex(0);

					if ((int) $notification['id'] > 0 && $notification['is_active'] == 1) {
						$resp = pjAppController::pjActionGetSubjectMessage($notification, $this->getLocaleId());
						$lang_message = $resp['lang_message'];
						$lang_subject = $resp['lang_subject'];
						if (count($lang_message) === 1 && count($lang_subject) === 1) {
							$arr = $pjClientModel->reset()->find($client_id)->getData();
							$search = array('{ClientName}', '{ClientPassword}', '{ClientEmail}', '{ClientPhone}', '{ClientURL}', '{StoreName}');
							$replace = array($arr['client_name'], $arr['password'], $arr['email'], $arr['phone'], $arr['url'], __('lblStoreName', true));
							$subject_client = str_replace($search, $replace, $lang_subject[0]['content']);
							$message_client = str_replace($search, $replace, $lang_message[0]['content']);
							$Email = self::getMailer($this->option_arr);
							if ($this->option_arr['o_send_email'] == 'flexmail') {

								$r = $this->sendFlexMail($arr['email'], $subject_client, $message_client, $this->option_arr);
							} else {

								$r = $Email

									->setTo($arr['email'])

									->setSubject($subject_client)

									->send($message_client);
							}

							// $r = $Email
							// 	->setTo($arr['email'])
							// 	->setSubject($subject_client)
							// 	->send(pjUtil::textToHtml($message_client));
							if (isset($r) && $r) {
								pjAppController::jsonResponse(array('status' => 'OK', 'code' => 216, 'text' => __('system_216', true)));
							} else {
								pjAppController::jsonResponse(array('status' => 'OK', 'code' => 215, 'text' => __('system_215', true)));
							}
						}
					}
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 216, 'text' => __('system_216', true)));
				}
				$this->set('category_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));
			}
		}
	}

	public function pjActionProduct()
	{
		if ($this->isXHR() || $this->_get->check('_escaped_fragment_')) {
			$company_id = pjUtil::getActiveCompanyId($this->defaultCompany);
			// echo "<pre>"; print_r($company_id); die;
			// ini_set('display_errors', '1');
			// ini_set('display_startup_errors', '1');
			// error_reporting(E_ALL);
			$is_ip_blocked = pjBase::isBlockedIp(pjUtil::getClientIp(), $this->option_arr);
			if ($is_ip_blocked == true) {
				$this->set('status', 'IP_BLOCKED');
			} else {
				if ($this->_get->check('id') && $this->_get->toInt('id') > 0) {
					$id = $this->_get->toInt('id');
				} elseif ($this->_get->check('_escaped_fragment_')) {
					preg_match('/\/Product\/(\d+)/', $this->_get->toString('_escaped_fragment_'), $matches);
					if (isset($matches[1])) {
						$id = $matches[1];
					}
				}

				# Find out what qty is in current shopping cart for each stock
				$order_arr = array();
				$cart_arr = $this->get('cart_arr');
				foreach ($cart_arr as $cart_item) {
					if ($cart_item['is_cart'] == 1) {

						if (!isset($order_arr[$cart_item['stock_id']])) {
							$order_arr[$cart_item['stock_id']] = 0;
						}
						$order_arr[$cart_item['stock_id']] += $cart_item['qty'];
					}
				}
				$pjGalleryModel = pjGalleryModel::factory();

				$pjStockModel = pjStockModel::factory();
				$pjProductCategoryModel = pjProductCategoryModel::factory();

				$arr = pjProductModel::factory()
					->select(sprintf("t1.*, t2.content AS name, t3.content AS full_desc, t4.content AS short_desc,
						(SELECT MIN(`price`) FROM `%2\$s`
							WHERE `product_id` = `t1`.`id` AND (`qty` > 0 OR `t1`.is_digital='1')
							LIMIT 1) AS `price`,
						(SELECT MAX(`price`) FROM `%2\$s`
							WHERE `product_id` = `t1`.`id` AND (`qty` > 0 OR `t1`.is_digital='1')
							LIMIT 1) AS `max_price`,
						(SELECT `id` FROM `%2\$s`
							WHERE `product_id` = `t1`.`id` AND (`qty` > 0 OR `t1`.is_digital='1')
							ORDER BY `price` ASC
							LIMIT 1) AS `stockId`,
						(SELECT CONCAT_WS('~:~', `medium_path`, `large_path`) FROM `%1\$s`
							WHERE `foreign_id` = `t1`.`id`
							ORDER BY ISNULL(`sort`), `sort` ASC, `id` ASC
							LIMIT 1) AS `pic`,
						(SELECT GROUP_CONCAT(`category_id`) FROM `%3\$s` WHERE `product_id` = `t1`.`id` LIMIT 1) AS `category_ids`
						", $pjGalleryModel->getTable(), $pjStockModel->getTable(), $pjProductCategoryModel->getTable()))
					->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.id AND t2.locale='" . $this->getLocaleId() . "' AND t2.field='name'", 'left outer')
					->join('pjMultiLang', "t3.model='pjProduct' AND t3.foreign_id=t1.id AND t3.locale='" . $this->getLocaleId() . "' AND t3.field='full_desc'", 'left outer')
					->join('pjMultiLang', "t4.model='pjProduct' AND t4.foreign_id=t1.id AND t4.locale='" . $this->getLocaleId() . "' AND t4.field='short_desc'", 'left outer')
					// ->where('t1.company_id', $company_id)
					->find($id)
					->toArray('category_ids', ',')
					->getData();

				if (!empty($arr)) {
					if ($arr['status'] != 2) {

						$arr['gallery_arr'] = $pjGalleryModel
							->select('t1.small_path, t1.medium_path, t1.large_path, t1.alt')
							->where('t1.foreign_id', $arr['id'])
							// ->where('t1.company_id', $company_id)
							->orderBy('t1.sort ASC')
							->findAll()
							->getData();

						$pjStockAttributeModel = pjStockAttributeModel::factory();
						$pjExtraItemModel = pjExtraItemModel::factory();

						// Stock images
						$pjStockModel = $pjStockModel
							->select('t1.id AS stock_id, t2.medium_path, t2.large_path, t2.alt AS title')
							->join('pjGallery', 't2.id=t1.image_id', 'inner')
							->where('t1.status', 'T')
							->where('t1.product_id', $arr['id']);

						// ✔ apply company filter only if valid
						if (isset($company_id) && (int)$company_id > 0) {
							$pjStockModel->where('t1.company_id', $company_id);
						}

						$arr['image_arr'] = $pjStockModel
							->orderBy('ISNULL(t2.sort), t2.sort ASC, t2.id ASC')
							->findAll()
							->getData();


						$pjExtraModel = pjExtraModel::factory()
							->select('t1.*, t2.content AS name, t3.content AS title')
							->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.locale='" . $this->getLocaleId() . "' AND t2.field='extra_name'", 'left outer')
							->join('pjMultiLang', "t3.model='pjExtra' AND t3.foreign_id=t1.id AND t3.locale='" . $this->getLocaleId() . "' AND t3.field='extra_title'", 'left outer')
							->where('t1.product_id', $arr['id']);

						// apply company filter only if valid
						if (isset($company_id) && (int)$company_id > 0) {
							$pjExtraModel->where('t1.company_id', $company_id);
						}

						$extra_arr = $pjExtraModel
							->orderBy('`title` ASC, `name` ASC')
							->findAll()
							->getData();

						$locale_id = $this->getLocaleId();
						foreach ($extra_arr as $k => $extra) {
							$q = $pjExtraItemModel
								->reset()
								->select('t1.*, t2.content AS name')
								->join(
									'pjMultiLang',
									"t2.model='pjExtraItem' AND t2.foreign_id=t1.id AND t2.locale='$locale_id' AND t2.field='extra_name'",
									'left outer'
								)
								->where('t1.extra_id', $extra['id']);

							// apply company filter only if valid
							if (isset($company_id) && (int)$company_id > 0) {
								$q->where('t1.company_id', $company_id);
							}

							$extra_arr[$k]['extra_items'] = $q
								->orderBy('t1.price ASC')
								->findAll()
								->getData();
						}

						$this->set('extra_arr', $extra_arr);

						$attr_arr = array();
						// Do not change col_name, direction
						$pjAttributeModel = pjAttributeModel::factory()
							->select('t1.id, t1.product_id, t1.parent_id, t2.content AS name')
							->join(
								'pjMultiLang',
								"t2.model='pjAttribute' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='" . $this->pjActionGetLocale() . "'",
								'left outer'
							)
							->where('t1.product_id', $arr['id'])
							->where(sprintf(
								"(CONCAT_WS('_', t1.id, t1.parent_id) IN (
									SELECT CONCAT_WS('_', TSA.attribute_id, TSA.attribute_parent_id)
									FROM `%s` AS `TSA`
									INNER JOIN `%s` AS `TS` ON TS.id = TSA.stock_id AND TS.qty > 0
									WHERE TSA.product_id = t1.product_id
								) OR t1.parent_id IS NULL OR t1.parent_id = '0')",
								$pjStockAttributeModel->getTable(),
								$pjStockModel->getTable()
							));

						// apply company filter only if valid
						if (isset($company_id) && (int)$company_id > 0) {
							$pjAttributeModel->where('t1.company_id', $company_id);
						}

						$a_arr = $pjAttributeModel
							->orderBy('t1.`order_group` ASC, `order_item` ASC')
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
						$this->set('attr_arr', array_values($attr_arr));

						$stockModel = $pjStockModel
							->reset()
							->select('t1.*, t2.small_path')
							->join('pjGallery', 't2.id=t1.image_id', 'left outer')
							->where('t1.product_id', $arr['id'])
							->where('t1.qty > 0');

						// ✔ apply company filter only if valid
						if (isset($company_id) && (int)$company_id > 0) {
							$stockModel->where('t1.company_id', $company_id);
						}

						$stock_arr = $stockModel
							->findAll()
							->getData();

						$_arr = array();
						foreach ($stock_arr as $k => $stock) {
							$_qty = $stock['qty'];
							if (isset($order_arr[$stock['id']])) {
								$_qty -= $order_arr[$stock['id']];
								if ($_qty < 1) {
									unset($stock_arr[$k]);
									continue;
								}
							}
							$stock_arr[$k]['qty'] = $_qty;

							$pjStockAttributeModel = $pjStockAttributeModel
								->reset()
								->where('t1.stock_id', $stock['id']);

							// apply company filter only if valid
							if (isset($company_id) && (int)$company_id > 0) {
								$pjStockAttributeModel->where('t1.company_id', $company_id);
							}

							$_arr[$stock['id']] = $pjStockAttributeModel
								->orderBy('t1.attribute_id ASC')
								->findAll()
								->getDataPair('attribute_parent_id', 'attribute_id');
						}

						$this->set('stock_attr_arr', $_arr);
						$this->set('stock_arr', array_values($stock_arr));
					} else {
						$arr = array();
					}
				} else {
					$arr = [];
				}

				$this
					->set('product_arr', $arr)
					->set('category_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1))
					->set(
						'similar_arr',
						(function () use ($id, $company_id, $pjGalleryModel, $pjStockModel, $pjProductCategoryModel) {
							$pjProductSimilarModel = pjProductSimilarModel::factory()
								->select(sprintf("t2.*, t3.content AS name,
								(SELECT `medium_path` FROM `%1\$s` WHERE `foreign_id` = `t1`.`similar_id` ORDER BY ISNULL(`sort`), `sort` ASC, `id` ASC LIMIT 1) AS `pic`,
								(SELECT MIN(`price`) FROM `%2\$s` WHERE `product_id` = `t1`.`similar_id` LIMIT 1) AS `price`,
								(SELECT GROUP_CONCAT(`category_id`) FROM `%3\$s` WHERE `product_id` = `t1`.`similar_id` LIMIT 1) AS `category_ids`
							", $pjGalleryModel->getTable(), $pjStockModel->getTable(), $pjProductCategoryModel->getTable()))
								->join('pjProduct', 't2.id=t1.similar_id AND t2.status!=2', 'inner')
								->join('pjMultiLang', "t3.model='pjProduct' AND t3.foreign_id=t2.id AND t3.field='name' AND t3.locale='" . $this->getLocaleId() . "'", 'left outer')
								->where('t1.product_id', $id);

							// apply company filter only if valid
							if (isset($company_id) && (int)$company_id > 0) {
								$pjProductSimilarModel->where('t1.company_id', $company_id);
							}

							return $pjProductSimilarModel
								->where('t2.status', 1)
								->orderBy('`name` ASC')
								->limit(5)
								->findAll()
								->toArray('category_ids', ',')
								->getData();
						})()
					)

				;
			}
		}
	}

	// public function pjActionProducts()
	// {

	// 	if ($this->isXHR() || $this->_get->check('_escaped_fragment_')) {

	// 		$company_id = $_SESSION[$this->defaultCompany]['id'];
	// 		// print_r($company_id);
	// 		$is_ip_blocked = pjBase::isBlockedIp(pjUtil::getClientIp(), $this->option_arr);
	// 		if ($is_ip_blocked == true) {
	// 			$this->set('status', 'IP_BLOCKED');
	// 		} else {
	// 			if ($this->_get->check('_escaped_fragment_')) {
	// 				preg_match('/\/Products\/q:(.*)?\/category:(\d+)?\/page:(\d+)?/', $this->_get->toString('_escaped_fragment_'), $matches);
	// 				if (!empty($matches)) {
	// 					$q = $matches[1];
	// 					$category_id = $matches[2];
	// 					$page = $matches[3];
	// 				}
	// 			} else {
	// 				$q = $this->_get->toString('q');
	// 				$category_id = $this->_get->toInt('category_id');
	// 				$page = $this->_get->toInt('page');
	// 			}

	// 			if (isset($_SESSION[$this->defaultCategoryMenu]) && (int) $_SESSION[$this->defaultCategoryMenu] > 0) {
	// 				$category_id = (int)$_SESSION[$this->defaultCategoryMenu];
	// 			}

	// 			# Find out what qty is in current shopping cart for each stock
	// 			$order_arr = array();
	// 			$cart_arr = $this->get('cart_arr');
	// 			foreach ($cart_arr as $cart_item) {
	// 				if (!isset($order_arr[$cart_item['stock_id']])) {
	// 					$order_arr[$cart_item['stock_id']] = 0;
	// 				}
	// 				$order_arr[$cart_item['stock_id']] += $cart_item['qty'];
	// 			}

	// 			$pjProductModel = pjProductModel::factory()
	// 				->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.id AND t2.locale='" . $this->getLocaleId() . "' AND t2.field='name'", 'left outer')
	// 				->join('pjMultiLang', "t3.model='pjProduct' AND t3.foreign_id=t1.id AND t3.locale='" . $this->getLocaleId() . "' AND t3.field='short_desc'", 'left outer')
	// 				->join('pjMultiLang', "t4.model='pjProduct' AND t4.foreign_id=t1.id AND t4.locale='" . $this->getLocaleId() . "' AND t4.field='full_desc'", 'left outer')
	// 				// ->where('t1.company_id', $company_id)
	// 				->where('t1.status !=', 2);
	// 			if (isset($company_id)) {
	// 				$pjProductModel->where('t1.company_id', $company_id);
	// 			}
	// 			if (isset($category_id) && (int) $category_id > 0) {
	// 				if (isset($company_id)) {

	// 					$pjCategoryModel = pjCategoryModel::factory()
	// 						->where('t1.company_id', $company_id);
	// 				}
	// 				$category = $pjCategoryModel->find($category_id)->getData();
	// 				if ($category) {
	// 					$category_ids = $pjCategoryModel->reset()->where('t1.lft BETWEEN ' . (int)$category['lft'] . ' AND ' . (int)$category['rgt'])->findAll()->getDataPair(null, 'id');
	// 				} else {
	// 					$category_ids = array($category_id);
	// 				}
	// 				$pjProductModel->where(sprintf(
	// 					"t1.id IN (SELECT `product_id` FROM `%s` WHERE `category_id` IN (%s))",
	// 					pjProductCategoryModel::factory()->getTable(),
	// 					implode(',', $category_ids)
	// 				));
	// 			}

	// 			if (isset($q) && !empty($q)) {
	// 				$q = str_replace(array('_', '%'), array('\_', '\%'), $pjProductModel->escapeStr(trim(urldecode($q))));
	// 				$pjProductModel->where("(t2.content LIKE '%$q%' OR t3.content LIKE '%$q%' OR t4.content LIKE '%$q%')");
	// 			}

	// 			$page = isset($page) && (int) $page > 0 ? intval($page) : 1;
	// 			$row_count = (int) $this->option_arr['o_products_per_page'] > 0 ? (int) $this->option_arr['o_products_per_page'] : 10;
	// 			$offset = ((int) $page - 1) * $row_count;
	// 			$count = $pjProductModel->findCount()->getData();
	// 			$pages = ceil($count / $row_count);

	// 			$sort_by = $this->_get->check('sort') && in_array($this->_get->toString('sort'), array('featured', 'newest', 'price_asc', 'price_desc', 'name_asc', 'name_desc')) ? $this->_get->toString('sort') : 'featured';
	// 			switch ($sort_by) {
	// 				case 'featured':
	// 					$pjProductModel->orderBy('`is_featured` DESC, `name` ASC');
	// 					break;
	// 				case 'newest':
	// 					$pjProductModel->orderBy('`id` DESC, `name` ASC');
	// 					break;
	// 				case 'price_asc':
	// 					$pjProductModel->orderBy('IF(t1.is_digital=1, `price`, `min_price`) ASC, `name` ASC');
	// 					break;
	// 				case 'price_desc':
	// 					$pjProductModel->orderBy('IF(t1.is_digital=1, `price`, `min_price`) DESC, `name` ASC');
	// 					break;
	// 				case 'name_asc':
	// 					$pjProductModel->orderBy('`name` ASC');
	// 					break;
	// 				case 'name_desc':
	// 					$pjProductModel->orderBy('`name` DESC');
	// 					break;
	// 				default:
	// 					$pjProductModel->orderBy('`is_featured` DESC, `name` ASC');
	// 					break;
	// 			}
	// 			$product_arr = $pjProductModel
	// 				->select(sprintf(
	// 					"t1.*, t2.content AS `name`,
	// 						(SELECT (`price`) FROM `%1\$s`
	// 							WHERE `product_id` = `t1`.`id` AND `t1`.`is_digital`='1'
	// 							LIMIT 1) AS `price`,
	// 						(SELECT MIN(`price`) FROM `%1\$s`
	// 							WHERE `product_id` = `t1`.`id` AND `qty` > 0
	// 							LIMIT 1) AS `min_price`,
	// 						(SELECT MAX(`price`) FROM `%1\$s`
	// 							WHERE `product_id` = `t1`.`id` AND `qty` > 0
	// 							LIMIT 1) AS `max_price`,
	// 						(SELECT `id` FROM `%1\$s`
	// 							WHERE `product_id` = `t1`.`id` AND (`qty` > 0 OR `t1`.`is_digital`='1')
	// 							ORDER BY `price` ASC
	// 							LIMIT 1) AS `stockId`,
	// 						(SELECT `qty` FROM `%1\$s`
	// 							WHERE `id` = `stockId`
	// 							LIMIT 1) AS `stockQty`,
	// 						(SELECT GROUP_CONCAT(CONCAT_WS('_', STA.attribute_id, STA.attribute_parent_id, ST.id, ST.qty))
	// 							FROM `%2\$s` AS `STA` INNER JOIN `%1\$s` AS `ST` ON ST.id=STA.stock_id 
	// 							WHERE `STA`.`product_id` = `t1`.`id`
	// 							LIMIT 1) AS `stockId_attr`,
	// 						(SELECT GROUP_CONCAT(`category_id`) FROM `%3\$s` WHERE `product_id` = `t1`.`id` LIMIT 1) AS `category_ids`,
	// 						(SELECT GROUP_CONCAT(CONCAT_WS('.', `id`, IF(`type`='single',NULL,(SELECT `id` FROM `%5\$s` WHERE `extra_id` = te.id ORDER BY `price` ASC LIMIT 1)))) FROM `%4\$s` AS `te` WHERE `product_id` = t1.id AND `is_mandatory` = '1' LIMIT 1) AS `m_extras`",
	// 					pjStockModel::factory()->getTable(),
	// 					pjStockAttributeModel::factory()->getTable(),
	// 					pjProductCategoryModel::factory()->getTable(),
	// 					pjExtraModel::factory()->getTable(),
	// 					pjExtraItemModel::factory()->getTable(),
	// 					pjAttributeModel::factory()->getTable()
	// 				))
	// 				->limit($row_count, $offset)
	// 				->findAll()
	// 				->toArray('category_ids', ',')
	// 				->toArray('m_extras', ',')
	// 				->getData();
	// 			$this->set('product_arr', $product_arr);
	// 			$product_ids_arr = $product_image_arr = $product_attr_arr = array();
	// 			if ($product_arr) {
	// 				foreach ($product_arr as $val) {
	// 					$product_ids_arr[] = $val['id'];
	// 				}
	// 				$image_arr = pjGalleryModel::factory()
	// 					->whereIn('t1.foreign_id', $product_ids_arr)
	// 					->orderBy('t1.sort ASC')
	// 					->findAll()
	// 					->getData();
	// 				foreach ($image_arr as $val) {
	// 					$product_image_arr[$val['foreign_id']][] = $val;
	// 				}

	// 				// Do not change col_name, direction
	// 				$pjAttributeModel = pjAttributeModel::factory();
	// 				$pjAttributeModel
	// 					->select('t1.id, t1.product_id, t1.parent_id, t2.content AS name')
	// 					->join('pjMultiLang', "t2.model='pjAttribute' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='" . $this->pjActionGetLocale() . "'", 'left outer')
	// 					->whereIn('t1.product_id', $product_ids_arr)
	// 					->where(sprintf("(CONCAT_WS('_', t1.id, t1.parent_id) IN (
	// 					SELECT CONCAT_WS('_', TSA.attribute_id, TSA.attribute_parent_id)
	// 					FROM `%s` AS `TSA`
	// 					INNER JOIN `%s` AS `TS` ON TS.id = TSA.stock_id AND TS.qty > 0
	// 					WHERE TSA.product_id = t1.product_id
	// 				) OR t1.parent_id IS NULL OR t1.parent_id = '0')", pjStockAttributeModel::factory()->getTable(), pjStockModel::factory()->getTable()));
	// 				if (isset($company_id)) {
	// 					$pjAttributeModel->where('t1.company_id', $company_id);
	// 				}

	// 				$a_arr = $pjAttributeModel->orderBy('t1.`order_group` ASC, `order_item` ASC')
	// 					->findAll()
	// 					->getData();

	// 				foreach ($a_arr as $attr) {
	// 					if ((int) $attr['parent_id'] === 0) {
	// 						$product_attr_arr[$attr['product_id']][$attr['id']] = $attr;
	// 					} else {
	// 						if (!isset($product_attr_arr[$attr['product_id']][$attr['parent_id']]['child'])) {
	// 							$product_attr_arr[$attr['product_id']][$attr['parent_id']]['child'] = array();
	// 						}
	// 						$product_attr_arr[$attr['product_id']][$attr['parent_id']]['child'][] = $attr;
	// 					}
	// 				}
	// 			}
	// 			echo "<pre>";
	// 			print_r($product_arr);
	// 			die;

	// 			$this
	// 				->set('order_arr', $product_image_arr)
	// 				->set('product_image_arr', $product_image_arr)
	// 				->set('product_attr_arr', $product_attr_arr)
	// 				->set('paginator', compact('pages', 'page', 'count', 'row_count'))
	// 				->set('category_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));
	// 		}
	// 	}
	// }
	public function pjActionProducts()
	{

		if ($this->isXHR() || $this->_get->check('_escaped_fragment_')) {

			// Ensure company_id is integer or null
			$company_id = isset($_SESSION[$this->defaultCompany]['id']) ? (int) $_SESSION[$this->defaultCompany]['id'] : null;
			// echo "<pre>";
			// print_r($company_id);
			// die;

			$is_ip_blocked = pjBase::isBlockedIp(pjUtil::getClientIp(), $this->option_arr);
			if ($is_ip_blocked == true) {
				$this->set('status', 'IP_BLOCKED');
			} else {
				if ($this->_get->check('_escaped_fragment_')) {
					preg_match('/\/Products\/q:(.*)?\/category:(\d+)?\/page:(\d+)?/', $this->_get->toString('_escaped_fragment_'), $matches);
					if (!empty($matches)) {
						$q = $matches[1];
						$category_id = $matches[2];
						$page = $matches[3];
					}
				} else {
					$q = $this->_get->toString('q');
					$category_id = $this->_get->toInt('category_id');
					$page = $this->_get->toInt('page');
				}

				if (isset($_SESSION[$this->defaultCategoryMenu]) && (int) $_SESSION[$this->defaultCategoryMenu] > 0) {
					$category_id = (int)$_SESSION[$this->defaultCategoryMenu];
				}

				# Find out what qty is in current shopping cart for each stock
				$order_arr = array();
				$cart_arr = $this->get('cart_arr');
				foreach ($cart_arr as $cart_item) {
					if ($cart_item['is_cart'] == 1) {

						if (!isset($order_arr[$cart_item['stock_id']])) {
							$order_arr[$cart_item['stock_id']] = 0;
						}
						$order_arr[$cart_item['stock_id']] += $cart_item['qty'];
					}
				}

				$pjProductModel = pjProductModel::factory()
					->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.id AND t2.locale='" . $this->getLocaleId() . "' AND t2.field='name'", 'left outer')
					->join('pjMultiLang', "t3.model='pjProduct' AND t3.foreign_id=t1.id AND t3.locale='" . $this->getLocaleId() . "' AND t3.field='short_desc'", 'left outer')
					->join('pjMultiLang', "t4.model='pjProduct' AND t4.foreign_id=t1.id AND t4.locale='" . $this->getLocaleId() . "' AND t4.field='full_desc'", 'left outer')
					->where('t1.status !=', 0);

				// apply company filter only when company_id is a positive integer
				if (!is_null($company_id) && $company_id > 0) {
					$pjProductModel->where('t1.company_id', $company_id);
				}

				if (isset($category_id) && (int) $category_id > 0) {
					if (!is_null($company_id) && $company_id > 0) {
						// ensure category queries are scoped to the same company
						$pjCategoryModel = pjCategoryModel::factory()
							->where('t1.company_id', $company_id);
					} else {
						$pjCategoryModel = pjCategoryModel::factory();
					}
					$category = $pjCategoryModel->find($category_id)->getData();
					if ($category) {
						$category_ids = $pjCategoryModel->reset()->where('t1.lft BETWEEN ' . (int)$category['lft'] . ' AND ' . (int)$category['rgt'])->findAll()->getDataPair(null, 'id');
					} else {
						$category_ids = array($category_id);
					}
					$pjProductModel->where(sprintf(
						"t1.id IN (SELECT `product_id` FROM `%s` WHERE `category_id` IN (%s))",
						pjProductCategoryModel::factory()->getTable(),
						implode(',', $category_ids)
					));
				}

				if (isset($q) && !empty($q)) {
					$q = str_replace(array('_', '%'), array('\_', '\%'), $pjProductModel->escapeStr(trim(urldecode($q))));
					$pjProductModel->where("(t2.content LIKE '%$q%' OR t3.content LIKE '%$q%' OR t4.content LIKE '%$q%')");
				}

				$page = isset($page) && (int) $page > 0 ? intval($page) : 1;
				$row_count = (int) $this->option_arr['o_products_per_page'] > 0 ? (int) $this->option_arr['o_products_per_page'] : 10;
				$offset = ((int) $page - 1) * $row_count;
				$count = $pjProductModel->findCount()->getData();
				$pages = ceil($count / $row_count);

				$sort_by = $this->_get->check('sort') && in_array($this->_get->toString('sort'), array('featured', 'newest', 'price_asc', 'price_desc', 'name_asc', 'name_desc')) ? $this->_get->toString('sort') : 'featured';
				switch ($sort_by) {
					case 'featured':
						$pjProductModel->orderBy('`is_featured` DESC, `name` ASC');
						break;
					case 'newest':
						$pjProductModel->orderBy('`id` DESC, `name` ASC');
						break;
					case 'price_asc':
						$pjProductModel->orderBy('IF(t1.is_digital=1, `price`, `min_price`) ASC, `name` ASC');
						break;
					case 'price_desc':
						$pjProductModel->orderBy('IF(t1.is_digital=1, `price`, `min_price`) DESC, `name` ASC');
						break;
					case 'name_asc':
						$pjProductModel->orderBy('`name` ASC');
						break;
					case 'name_desc':
						$pjProductModel->orderBy('`name` DESC');
						break;
					default:
						$pjProductModel->orderBy('`is_featured` DESC, `name` ASC');
						break;
				}
				$product_arr = $pjProductModel
					->select(sprintf(
						"t1.*, t2.content AS `name`,
                        (SELECT (`price`) FROM `%1\$s`
                            WHERE `product_id` = `t1`.`id` AND `t1`.`is_digital`='1'
                            LIMIT 1) AS `price`,
                        (SELECT MIN(`price`) FROM `%1\$s`
                            WHERE `product_id` = `t1`.`id` AND `qty` > 0
                            LIMIT 1) AS `min_price`,
                        (SELECT MAX(`price`) FROM `%1\$s`
                            WHERE `product_id` = `t1`.`id` AND `qty` > 0
                            LIMIT 1) AS `max_price`,
                        (SELECT `id` FROM `%1\$s`
                            WHERE `product_id` = `t1`.`id` AND (`qty` > 0 OR `t1`.`is_digital`='1')
                            ORDER BY `price` ASC
                            LIMIT 1) AS `stockId`,
                        (SELECT `qty` FROM `%1\$s`
                            WHERE `product_id` = `t1`.`id` AND (`qty` > 0 OR `t1`.`is_digital`='1')
                            ORDER BY `price` ASC
                            LIMIT 1) AS `stockQty`,
                        (SELECT GROUP_CONCAT(CONCAT_WS('_', STA.attribute_id, STA.attribute_parent_id, ST.id, ST.qty))
                            FROM `%2\$s` AS `STA` INNER JOIN `%1\$s` AS `ST` ON ST.id=STA.stock_id 
                            WHERE `STA`.`product_id` = `t1`.`id`
                            LIMIT 1) AS `stockId_attr`,
                        (SELECT GROUP_CONCAT(`category_id`) FROM `%3\$s` WHERE `product_id` = `t1`.`id` LIMIT 1) AS `category_ids`,
                        (SELECT GROUP_CONCAT(CONCAT_WS('.', `id`, IF(`type`='single',NULL,(SELECT `id` FROM `%5\$s` WHERE `extra_id` = te.id ORDER BY `price` ASC LIMIT 1)))) FROM `%4\$s` AS `te` WHERE `product_id` = t1.id AND `is_mandatory` = '1' LIMIT 1) AS `m_extras`",
						pjStockModel::factory()->getTable(),
						pjStockAttributeModel::factory()->getTable(),
						pjProductCategoryModel::factory()->getTable(),
						pjExtraModel::factory()->getTable(),
						pjExtraItemModel::factory()->getTable(),
						pjAttributeModel::factory()->getTable()
					))
					->limit($row_count, $offset)
					->findAll()
					->toArray('category_ids', ',')
					->toArray('m_extras', ',')
					->getData();
				$this->set('product_arr', $product_arr);
				$product_ids_arr = $product_image_arr = $product_attr_arr = array();
				if ($product_arr) {
					foreach ($product_arr as $val) {
						$product_ids_arr[] = $val['id'];
					}
					$image_arr = pjGalleryModel::factory()
						->whereIn('t1.foreign_id', $product_ids_arr)
						->orderBy('t1.sort ASC')
						->findAll()
						->getData();
					foreach ($image_arr as $val) {
						$product_image_arr[$val['foreign_id']][] = $val;
					}

					// Do not change col_name, direction
					$pjAttributeModel = pjAttributeModel::factory();
					$pjAttributeModel
						->select('t1.id, t1.product_id, t1.parent_id, t2.content AS name')
						->join('pjMultiLang', "t2.model='pjAttribute' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='" . $this->pjActionGetLocale() . "'", 'left outer')
						->whereIn('t1.product_id', $product_ids_arr)
						->where(sprintf("(CONCAT_WS('_', t1.id, t1.parent_id) IN (
                    SELECT CONCAT_WS('_', TSA.attribute_id, TSA.attribute_parent_id)
                    FROM `%s` AS `TSA`
                    INNER JOIN `%s` AS `TS` ON TS.id = TSA.stock_id AND TS.qty > 0
                    WHERE TSA.product_id = t1.product_id
                ) OR t1.parent_id IS NULL OR t1.parent_id = '0')", pjStockAttributeModel::factory()->getTable(), pjStockModel::factory()->getTable()));
					// apply company filter to attributes as well (if company_id set)
					if (!is_null($company_id) && $company_id > 0) {
						$pjAttributeModel->where('t1.company_id', $company_id);
					}

					$a_arr = $pjAttributeModel->orderBy('t1.`order_group` ASC, `order_item` ASC')
						->findAll()
						->getData();

					foreach ($a_arr as $attr) {
						if ((int) $attr['parent_id'] === 0) {
							$product_attr_arr[$attr['product_id']][$attr['id']] = $attr;
						} else {
							if (!isset($product_attr_arr[$attr['product_id']][$attr['parent_id']]['child'])) {
								$product_attr_arr[$attr['product_id']][$attr['parent_id']]['child'] = array();
							}
							$product_attr_arr[$attr['product_id']][$attr['parent_id']]['child'][] = $attr;
						}
					}
				}


				$this
					->set('order_arr', $order_arr)
					->set('product_image_arr', $product_image_arr)
					->set('product_attr_arr', $product_attr_arr)
					->set('paginator', compact('pages', 'page', 'count', 'row_count'))
					->set('category_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));
			}
		}
	}

	public function pjActionFavs()
	{
		if ($this->isXHR() || $this->_get->check('_escaped_fragment_')) {
			$company_id = pjUtil::getActiveCompanyId($this->defaultCompany);

			$is_ip_blocked = pjBase::isBlockedIp(pjUtil::getClientIp(), $this->option_arr);
			if ($is_ip_blocked == true) {
				$this->set('status', 'IP_BLOCKED');
			} else {
				if (isset($_COOKIE[$this->defaultCookie]) && !empty($_COOKIE[$this->defaultCookie])) {
					$cookie_value = base64_decode(stripslashes($_COOKIE[$this->defaultCookie]));
					$favs = unserialize($cookie_value);
					$arr = $extra_arr = $attr_arr = $stock_arr = $image_arr = $product_id = $stock_id = array();
					foreach ($favs as $fav => $whatever) {
						$item = unserialize($fav);
						$product_id[] = $item['product_id'];
						$stock_id[] = $item['stock_id'];
					}

					if (!empty($product_id)) {
						$pjProductModel = pjProductModel::factory();
						$pjProductModel->select(sprintf(
							"t1.*, t2.content AS name,
								(SELECT GROUP_CONCAT(`category_id`) FROM `%1\$s` WHERE `product_id` = `t1`.`id` LIMIT 1) AS `category_ids`
								",
							pjProductCategoryModel::factory()->getTable()
						))
							->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.id AND t2.locale='" . $this->getLocaleId() . "' AND t2.field='name'", 'left outer')
							->whereIn('t1.id', $product_id)
							->where('t1.status', 1);
						if (isset($company_id)) {
							$pjProductModel->where('t1.company_id', $company_id);
						}
						$arr = 	$pjProductModel->findAll()
							->toArray('category_ids', ',')
							->getData();

						$pjExtraModel = pjExtraModel::factory();
						$pjExtraModel
							->select('t1.*, t2.content AS name, t3.content AS title')
							->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.locale='" . $this->getLocaleId() . "' AND t2.field='extra_name'", 'left outer')
							->join('pjMultiLang', "t3.model='pjExtra' AND t3.foreign_id=t1.id AND t3.locale='" . $this->getLocaleId() . "' AND t3.field='extra_title'", 'left outer')
							->whereIn('t1.product_id', $product_id);
						if (isset($company_id)) {
							$pjExtraModel->where('t1.company_id', $company_id);
						}

						// fetch extras
						$extra_arr = $pjExtraModel
							->orderBy('t1.title ASC, t1.name ASC')
							->findAll()
							->getData();

						if (!empty($extra_arr)) {
							$locale_id = (int) $this->getLocaleId();
							$pjExtraItemModel = pjExtraItemModel::factory();

							foreach ($extra_arr as $k => $extra) {
								// start a fresh query from the model
								$q = $pjExtraItemModel
									->reset()
									->select('t1.*, t2.content AS name')
									->join(
										'pjMultiLang',
										"t2.model='pjExtraItem' AND t2.foreign_id=t1.id AND t2.locale='{$locale_id}' AND t2.field='extra_name'",
										'left outer'
									)
									->where('t1.extra_id', $extra['id']);

								// apply company filter only if provided
								if (isset($company_id) && (int)$company_id > 0) {
									$q = $q->where('t1.company_id', $company_id);
								}

								// execute and store results
								$extra_arr[$k]['extra_items'] = $q
									->orderBy('t1.price ASC')
									->findAll()
									->getData();
							}
						}


						// Do not change col_name, direction
						$pjAttributeModel = pjAttributeModel::factory();

						$pjAttributeModel
							->select('t1.id, t1.product_id, t1.parent_id, t2.content AS name')
							->join(
								'pjMultiLang',
								"t2.model='pjAttribute' 
									AND t2.foreign_id=t1.id 
									AND t2.field='name' 
									AND t2.locale='" . $this->pjActionGetLocale() . "'",
								'left outer'
							)
							->whereIn('t1.product_id', $product_id);

						// ✔ Add company_id filter only if set
						if (isset($company_id) && (int)$company_id > 0) {
							$pjAttributeModel->where('t1.company_id', $company_id);
						}

						$a_arr = $pjAttributeModel
							->orderBy('t1.parent_id ASC, t2.content ASC')  // correct order by translated name
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
					}
					// STOCK PRICE LIST
					$pjStockModel = pjStockModel::factory();
					$pjStockModel->whereIn('t1.id', $stock_id)
						->where('t1.status', 'T');

					// ✔ Add company filter only if valid
					if (isset($company_id) && (int)$company_id > 0) {
						$pjStockModel->where('t1.company_id', $company_id);
					}

					$stock_arr = $pjStockModel
						->findAll()
						->getDataPair('id', 'price');


					// STOCK IMAGE LIST
					$pjStockImgModel = pjStockModel::factory();
					$pjStockImgModel
						->select('t1.id, t2.small_path')
						->join('pjGallery', 't2.id=t1.image_id', 'left outer')
						->where('t1.status', 'T')
						->whereIn('t1.id', $stock_id);

					// ✔ Add company filter only if valid
					if (isset($company_id) && (int)$company_id > 0) {
						$pjStockImgModel->where('t1.company_id', $company_id);
					}

					$image_arr = $pjStockImgModel
						->findAll()
						->getDataPair('id', 'small_path');

					$this->set('arr', $arr);
					$this->set('extra_arr', $extra_arr);
					$this->set('attr_arr', array_values($attr_arr));
					$this->set('stock_arr', $stock_arr);
					$this->set('image_arr', $image_arr);
				}
				$this->set('category_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));
			}
		}
	}

	public function pjActionOrdersHistory()
	{
		if ($this->isXHR() || $this->_get->check('_escaped_fragment_')) {
			$is_ip_blocked = pjBase::isBlockedIp(pjUtil::getClientIp(), $this->option_arr);
			if ($is_ip_blocked == true) {
				$this->set('status', 'IP_BLOCKED');
			} elseif (!$this->isLoged()) {
				$this->set('status', 'LOGIN_REQUIRED');
			} else {
				$company_id = isset($_SESSION[$this->defaultCompany]['id']) ? (int) $_SESSION[$this->defaultCompany]['id'] : null;

				$page = $this->_get->check('page') ? (int) $this->_get->toInt('page') : 1;
				$page = $page > 0 ? $page : 1;
				$row_count = 20;
				$offset = ($page - 1) * $row_count;

				$pjOrderModel = pjOrderModel::factory()
					->where('t1.client_id', $this->getUserId());
				if (isset($company_id) && $company_id > 0) {
					$pjOrderModel->where('t1.company_id', $company_id);
				}

				$count = $pjOrderModel->findCount()->getData();
				$pages = $row_count > 0 ? (int) ceil($count / $row_count) : 0;

				$select = "t1.*, t2.content AS `b_country`, t3.content AS `s_country`";
				if (class_exists('pjInvoiceModel')) {
					$invoiceTable = pjInvoiceModel::factory()->getTable();
					$select .= ", (SELECT MIN(`id`) FROM {$invoiceTable} AS t4 WHERE t4.order_id = t1.uuid) AS invoice_id"
						. ", (SELECT `uuid` FROM {$invoiceTable} AS t5 WHERE t5.order_id = t1.uuid ORDER BY `id` ASC LIMIT 1) AS invoice_uuid";
				}

				$order_arr = $pjOrderModel
					->reset()
					->select($select)
					->join('pjMultiLang', "t2.model='pjBaseCountry' AND t2.foreign_id=t1.b_country_id AND t2.locale=t1.locale_id AND t2.field='name'", 'left outer')
					->join('pjMultiLang', "t3.model='pjBaseCountry' AND t3.foreign_id=t1.s_country_id AND t3.locale=t1.locale_id AND t3.field='name'", 'left outer')
					->where('t1.client_id', $this->getUserId())
					->orderBy('`created` DESC')
					->limit($row_count, $offset);
				if (isset($company_id) && $company_id > 0) {
					$order_arr->where('t1.company_id', $company_id);
				}
				$order_arr = $order_arr->findAll()->getData();

				foreach ($order_arr as $i => $item) {
					pjAppController::enrichOrderForFrontend($order_arr[$i]);

					$stack = pjAppController::pjActionGetOrderStock($item['id'], $this->getLocaleId());
					$order_arr[$i]['os_arr'] = $stack['os_arr'];
					$order_arr[$i]['extra_arr'] = $stack['extra_arr'];
					$order_arr[$i]['attr_arr'] = $stack['attr_arr'];

					if (class_exists('pjInvoiceModel') && empty($order_arr[$i]['invoice_uuid'])) {
						$invoice_row = $this->pjActionEnsureInvoice($item['id']);
						if (!empty($invoice_row['uuid'])) {
							$order_arr[$i]['invoice_uuid'] = $invoice_row['uuid'];
						}
					}

					$order_arr[$i]['has_downloads'] = false;
					if (!empty($item['payment_status']) && $item['payment_status'] === 'paid' && class_exists('pjDigitalDownloadModel')) {
						$dlCount = (int) pjDigitalDownloadModel::factory()
							->where('order_id', $item['id'])
							->findCount()
							->getData();
						$order_arr[$i]['has_downloads'] = $dlCount > 0;
					}

					$paymentStatus = isset($item['payment_status']) ? $item['payment_status'] : '';
					$pay_link = false;
					if ($paymentStatus !== 'paid'
						&& $paymentStatus !== 'refunded'
						&& !in_array($item['status'], array('completed', 'cancelled'))
						&& !in_array($item['payment_method'], array('creditcard', 'bank', 'cod', 'cash'))) {
						$pay_link = true;
					}
					$order_arr[$i]['pay_link'] = $pay_link;
				}

				$this
					->set('order_arr', $order_arr)
					->set('paginator', compact('pages', 'page', 'count', 'row_count'))
					->set('option_arr', $this->option_arr)
					->set('category_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));
			}
		}
	}

	public function pjActionOrderDetails()
	{
		if ($this->isXHR() || $this->_get->check('_escaped_fragment_')) {
			$is_ip_blocked = pjBase::isBlockedIp(pjUtil::getClientIp(), $this->option_arr);
			if ($is_ip_blocked == true) {
				$this->set('status', 'IP_BLOCKED');
			} elseif (!$this->isLoged()) {
				$this->set('status', 'LOGIN_REQUIRED');
			} else {
				$uuid = $this->_get->check('uuid') ? $this->_get->toString('uuid') : null;
				if (empty($uuid)) {
					$this->set('status', 'ERR');
				} else {
					$company_id = isset($_SESSION[$this->defaultCompany]['id']) ? (int) $_SESSION[$this->defaultCompany]['id'] : null;

					$pjOrderModel = pjOrderModel::factory()
						->where('t1.uuid', $uuid)
						->where('t1.client_id', $this->getUserId());
					if (isset($company_id) && $company_id > 0) {
						$pjOrderModel->where('t1.company_id', $company_id);
					}

					$arr = $pjOrderModel
						->select(sprintf("t1.*,
					AES_DECRYPT(t1.cc_type, '%1\$s') AS `cc_type`,
					AES_DECRYPT(t1.cc_num, '%1\$s') AS `cc_num`,
					AES_DECRYPT(t1.cc_exp_month, '%1\$s') AS `cc_exp_month`,
					AES_DECRYPT(t1.cc_exp_year, '%1\$s') AS `cc_exp_year`,
					AES_DECRYPT(t1.cc_code, '%1\$s') AS `cc_code`,
					t2.content AS `b_country`, t3.content AS `s_country`,
					t4.email as client_email, t4.client_name, t4.phone as client_phone, t4.url as client_url", PJ_SALT))
						->join('pjMultiLang', "t2.model='pjBaseCountry' AND t2.foreign_id=t1.b_country_id AND t2.locale=t1.locale_id AND t2.field='name'", 'left outer')
						->join('pjMultiLang', "t3.model='pjBaseCountry' AND t3.foreign_id=t1.s_country_id AND t3.locale=t1.locale_id AND t3.field='name'", 'left outer')
						->join('pjClient', "t4.id=t1.client_id", 'left outer')
						->limit(1)
						->findAll()
						->getData();

					if (!empty($arr)) {
						$arr = $arr[0];
						pjAppController::enrichOrderForFrontend($arr);
						$locale_id = !empty($arr['locale_id']) ? (int) $arr['locale_id'] : (int) $this->getLocaleId();

						$invoice_available = pjAppController::isInvoicePluginAvailable();
						if ($invoice_available) {
							$invoice_row = pjInvoiceModel::factory()
								->where('order_id', $arr['uuid'])
								->limit(1)
								->findAll()
								->getDataIndex(0);
							if (!empty($invoice_row['uuid'])) {
								$arr['invoice_uuid'] = $invoice_row['uuid'];
							} else {
								$invoice_row = $this->pjActionEnsureInvoice($arr['id']);
								$arr['invoice_uuid'] = !empty($invoice_row['uuid']) ? $invoice_row['uuid'] : null;
							}
						}

						$stack = pjAppController::pjActionGetOrderStock($arr['id'], $locale_id);

						$this
							->set('arr', $arr)
							->set('os_arr', $stack['os_arr'])
							->set('extra_arr', $stack['extra_arr'])
							->set('attr_arr', $stack['attr_arr']);

						$digital_downloads = array();
						if (!empty($arr['payment_status']) && $arr['payment_status'] === 'paid'
							&& class_exists('pjDigitalDownloadHelper')) {
							require_once PJ_APP_PATH . 'classes/pjDigitalDownloadHelper.class.php';
							$digital_downloads = pjDigitalDownloadHelper::findByOrderId($arr['id']);
						}
						$this->set('digital_downloads', $digital_downloads);

						$mollie_info = array();
						if (!empty($arr['txn_id']) && class_exists('pjMollieModel')) {
							$mollie_row = pjMollieModel::factory()
								->where('txn_id', $arr['txn_id'])
								->limit(1)
								->findAll()
								->getDataIndex(0);
							if (!empty($mollie_row)) {
								$mollie_info['method'] = isset($mollie_row['method']) ? $mollie_row['method'] : '';
								$mollie_info['status'] = isset($mollie_row['status']) ? $mollie_row['status'] : '';
								if (!empty($mollie_row['payment_log'])) {
									$log_data = json_decode($mollie_row['payment_log'], true);
									if (is_array($log_data) && !empty($log_data['details'])) {
										$mollie_info['details'] = $log_data['details'];
									}
								}
							}
						}
						$this->set('mollie_info', $mollie_info);
						$this->set('option_arr', $this->option_arr);
						$this->set('status', 'OK');
					} else {
						$this->set('status', 'ERR');
					}
					$this->set('category_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));
				}
			}
		}
	}

	/**
	 * Secure invoice PDF download for logged-in customer (owns the order).
	 */
	public function pjActionOrderInvoice()
	{
		if (!$this->isLoged()) {
			pjUtil::redirect($this->option_arr['o_install_url']);
			return;
		}
		$order_uuid = $this->_get->check('uuid') ? $this->_get->toString('uuid') : null;
		if (empty($order_uuid) || !pjAppController::isInvoicePluginAvailable()) {
			pjUtil::redirect($this->option_arr['o_install_url']);
			return;
		}
		$company_id = isset($_SESSION[$this->defaultCompany]['id']) ? (int) $_SESSION[$this->defaultCompany]['id'] : null;
		$pjOrderModel = pjOrderModel::factory()
			->where('t1.uuid', $order_uuid)
			->where('t1.client_id', $this->getUserId());
		if (isset($company_id) && $company_id > 0) {
			$pjOrderModel->where('t1.company_id', $company_id);
		}
		$order = $pjOrderModel->limit(1)->findAll()->getDataIndex(0);
		if (empty($order)) {
			pjUtil::redirect($this->option_arr['o_install_url']);
			return;
		}
		$this->pjActionEnsureInvoice($order['id']);
		$invoice = pjInvoiceModel::factory()
			->where('order_id', $order['uuid'])
			->limit(1)
			->findAll()
			->getDataIndex(0);
		if (empty($invoice['uuid'])) {
			pjUtil::redirect($this->option_arr['o_install_url']);
			return;
		}
		pjUtil::redirect(PJ_INSTALL_URL . 'index.php?controller=pjInvoice&action=generatePdf&id='
			. urlencode($invoice['uuid']) . '&uuid=' . urlencode($order['uuid']));
	}
}
