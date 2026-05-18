<?php
if (!defined("ROOT_PATH")) {
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminOptions extends pjAdmin
{
	public function pjActionUpdate()
	{
		$this->checkLogin();

		if (!pjAuth::factory()->hasAccess()) {
			$this->sendForbidden();
			return;
		}
		$default_company = $_SESSION[$this->defaultCompany];

		if (self::isPost() && $this->_post->toInt('options_update')) {
			// echo "<pre>"; print_r($this->_post); die;
			$pjOptionModel = new pjOptionModel();
			$pjOptionModel
				->where('company_id', $default_company['id'])
				->where('foreign_id', $default_company['id'])
				->where('type', 'bool')
				->where('tab_id', $this->_post->toInt('tab'))
				->modifyAll(array('value' => '1|0::0'));
			if ($this->_post->toInt('tab') == 2) {
				$pjOptionModel->reset()
					->where('company_id', $default_company['id'])
					->where('foreign_id', $default_company['id'])
					->whereIn('`key`', array('o_disable_orders', 'o_disable_payments'))
					->modifyAll(array('value' => 'Yes|No::No'));
			} elseif ($this->_post->toInt('tab') == 6) {
				$pjMultiLangModel = pjMultiLangModel::factory();
				$pjTaxModel = pjTaxModel::factory();
				$shipping_arr = $this->_post->toArray('shipping');
				$free_arr = $this->_post->toArray('free');
				$tax_arr = $this->_post->toArray('tax');
				foreach ($shipping_arr as $k => $v) {
					if (strpos($k, "new_") === 0) {
						# Insert
						$insert_id = $pjTaxModel->reset()->setAttributes(array(
							'shipping' => $shipping_arr[$k],
							'free' => $free_arr[$k],
							'tax' => $tax_arr[$k],
							'company_id' => $default_company['id']
						))->insert()->getInsertId();

						if ($insert_id !== false && (int) $insert_id > 0) {
							if ($this->_post->toArray('i18n')) {
								$tmp = $this->pjActionTurnI18n($this->_post->toArray('i18n'), 'location', $k);
								$pjMultiLangModel->reset()->saveMultiLang($tmp, $insert_id, 'pjTax', 'data');
							}
						}
					} else {
						# Update
						$pjTaxModel->reset()->set('id', $k)->modify(array(
							'shipping' => $shipping_arr[$k],
							'free' => $free_arr[$k],
							'tax' => $tax_arr[$k]
						));

						if ($this->_post->toArray('i18n')) {
							$tmp = $this->pjActionTurnI18n($this->_post->toArray('i18n'), 'location', $k);
							$pjMultiLangModel->reset()->updateMultiLang($tmp, $k, 'pjTax', 'data');
						}
					}
				}
			}
			foreach ($this->_post->raw() as $key => $value) {
				if (preg_match('/value-(string|text|int|float|enum|bool|color)-(.*)/', $key) === 1) {
					list(, $type, $k) = explode("-", $key);
					if (!empty($k)) {
						$_value = ':NULL';
						if ($value) {
							// switch ($type) {
							// 	case 'string':
							// 	case 'text':
							// 	case 'enum':
							// 	case 'color':
							// 		$_value = $this->_post->toString($key);
							// 		break;
							// 	case 'int':
							// 	case 'bool':
							// 		$_value = $this->_post->toString($key);
							// 		break;
							// 	case 'float':
							// 		$_value = $this->_post->toString($key);
							// 		break;
							// }
							switch ($type) {
								case 'string':
								case 'text':
								case 'color':
									$_value = $this->_post->toString($key);
									break;

								case 'enum':
									// Normalize enums to canonical "Yes|No::Yes" or "Yes|No::No"
									$raw = $this->_post->toString($key);

									// If checkbox was used and only the checkbox name was posted (rare in your current markup),
									// try fallback to presence of checkbox key in POST (checked => Yes)
									if ($raw === null || $raw === '') {
										// fallback: check for plain checkbox presence (checkbox named as option key)
										if ($this->_post->check($k)) {
											$raw = 'Yes';
										} else {
											$raw = 'No';
										}
									}

									// If payload contains ::, get the part after it
									if (strpos($raw, '::') !== false) {
										$parts = explode('::', $raw);
										$label = end($parts);
									} else {
										$label = $raw;
									}

									// Normalize numeric/boolean forms
									if ($label === '1' || $label === 1 || strtolower($label) === 'true') {
										$label = 'Yes';
									} elseif ($label === '0' || $label === 0 || $label === '' || strtolower($label) === 'false') {
										$label = 'No';
									} else {
										// keep textual values like "Yes" or "No" as-is
										$label = $label;
									}

									$_value = 'Yes|No::' . $label;
									break;

								case 'int':
								case 'bool':
									$_value = $this->_post->toString($key);
									break;

								case 'float':
									$_value = $this->_post->toString($key);
									break;
							}
						}

						$pjOptionModel
							->reset()
							->where('company_id', $default_company['id'])
							->where('foreign_id', $default_company['id'])
							->where('`key`', $k)
							->limit(1)
							->modifyAll(array('value' => $_value));
					}
				}
			}

			$i18n_arr = $this->_post->toI18n('i18n');
			// echo "<pre>"; print_r($i18n_arr); die;
			if (!empty($i18n_arr) && $this->_post->toInt('tab') != 6) {
				pjMultiLangModel::factory()->updateMultiLang($i18n_arr, $this->getForeignId(), 'pjOption', 'data');
			}

			$err = '';
			if ($this->_post->check('next_action')) {
				switch ($this->_post->toString('next_action')) {
					case 'pjActionBooking':
						$err = 'AOP02';
						break;
					case 'pjActionBookingForm':
						$err = 'AOP03';
						break;
					case 'pjActionTerm':
						$err = 'AOP04';
						break;
					case 'pjActionShippingTax':
						$err = 'AOP06';
						break;
				}
			}
			pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminOptions&action=" . $this->_post->toString('next_action') . "&err=$err");
		}
	}

	public function pjActionBooking()
	{
		if (!pjAuth::factory()->hasAccess()) {
			$this->sendForbidden();
			return;
		}
		$default_company = $_SESSION[$this->defaultCompany];

		$arr = pjOptionModel::factory()
			->where('t1.company_id', $default_company['id'])
			->where('t1.foreign_id', $default_company['id'])
			->where('t1.tab_id', 2)
			->orderBy('t1.order ASC')
			->findAll()
			->getData();
		// echo "<pre>";
		// print_r($arr);
		// echo "</pre>";
		// die;
		$this->set('arr', $arr);
		$this->appendJs('pjAdminOptions.js');
	}

	public function pjActionBookingForm()
	{
		if (!pjAuth::factory()->hasAccess()) {
			$this->sendForbidden();
			return;
		}
		$default_company = $_SESSION[$this->defaultCompany];

		$arr = pjOptionModel::factory()
			->where('t1.company_id', $default_company['id'])
			->where('t1.foreign_id', $default_company['id'])
			->where('t1.tab_id', 3)
			->orderBy('t1.order ASC')
			->findAll()
			->getData();

		$this->set('arr', $arr);
		$this->appendJs('pjAdminOptions.js');
	}

	public function pjActionTerm()
	{
		if (!pjAuth::factory()->hasAccess()) {
			$this->sendForbidden();
			return;
		}
		$default_company = $_SESSION[$this->defaultCompany];

		$arr = pjOptionModel::factory()
			->where('t1.company_id', $default_company['id'])
			->where('t1.foreign_id', $default_company['id'])
			->where('t1.tab_id', 4)
			->orderBy('t1.order ASC')
			->findAll()
			->getData();

		$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($this->getForeignId(), 'pjOption');

		$this->set('default_company', $default_company);
		$this->set('arr', $arr);
		// echo "<pre>";
		// print_r($arr);
		// echo "</pre>";


		$this->setLocalesData();

		$this->appendJs('jquery.multilang.js', $this->getConstant('pjBase', 'PLUGIN_JS_PATH'), false, false);
		$this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
		$this->appendJs('pjAdminOptions.js');
	}

	public function pjActionNotifications()
	{
		if (!pjAuth::factory()->hasAccess()) {
			$this->sendForbidden();
			return;
		}
		$default_company = $_SESSION[$this->defaultCompany];


		$arr = pjOptionModel::factory()
			->where('t1.company_id', $default_company['id'])
			->where('t1.foreign_id', $default_company['id'])
			->where('t1.tab_id', 3)
			->orderBy('t1.order ASC')
			->findAll()
			->getData();

		$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($this->getForeignId(), 'pjOption');

		$this->set('arr', $arr);
		$this->setLocalesData();

		$this->appendCss('awesome-bootstrap-checkbox.css', PJ_THIRD_PARTY_PATH . 'awesome_bootstrap_checkbox/');
		$this->appendJs('jquery.multilang.js', $this->getConstant('pjBase', 'PLUGIN_JS_PATH'), false, false);
		$this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
		$this->appendJs('pjAdminOptions.js');
	}

	public function pjActionInstall()
	{
		if (!pjAuth::factory()->hasAccess()) {
			$this->sendForbidden();
			return;
		}
		$company_id = $_SESSION[$this->defaultCompany]['id'];
		// die($company_id);
		if (self::isPost()) {
			foreach ($this->_post->raw() as $key => $value) {

				if ($key === 'o_install_url') {
					$value = pjUtil::getStorefrontBaseUrl($value);
				}

				pjOptionModel::factory()
					->where('company_id', $company_id)
					->where('`key`', $key)
					->modifyAll(array('value' => $value));
			}


			pjUtil::redirect('index.php?controller=pjAdminOptions&action=pjActionInstall&err=AO01');
		}

		$this->set('category_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));

		$this->set('is_flag_ready', $this->requestAction(array('controller' => 'pjBaseLocale', 'action' => 'pjActionIsFlagReady'), array('return')));
		$locale_arr = pjBaseLocaleModel::factory()
			->select('t1.*, t2.file, t2.title')
			->join('pjBaseLocaleLanguage', 't2.iso=t1.language_iso', 'left')
			->where('t2.file IS NOT NULL')
			->orderBy('t1.sort ASC')
			->findAll()
			->getData();
		$this->set('locale_arr', $locale_arr);

		$this->appendJs('pjAdminOptions.js');
	}

	public function pjActionNotificationsGetMetaData()
	{
		$this->setAjax(true);

		if (!$this->isXHR()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}

		if (!self::isGet()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Invalid request.'));
		}

		if (!(isset($this->query['recipient']) && pjValidation::pjActionNotEmpty($this->query['recipient']))) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Missing, empty or invalid parameters.'));
		}
		$default_company = $_SESSION[$this->defaultCompany];

		$this->set('arr', pjNotificationModel::factory()
			->where('t1.company_id', $default_company['id'])

			->where('t1.recipient', $this->query['recipient'])
			->orderBy("id ASC")
			->findAll()
			->getData());
	}

	public function pjActionNotificationsGetContent()
	{
		$this->setAjax(true);

		if (!$this->isXHR()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}

		if (!self::isGet()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Invalid request.'));
		}

		if (
			!($this->_get->check('recipient') && $this->_get->check('variant') && $this->_get->check('transport'))
			&& pjValidation::pjActionNotEmpty($this->_get->toString('recipient'))
			&& pjValidation::pjActionNotEmpty($this->_get->toString('variant'))
			&& pjValidation::pjActionNotEmpty($this->_get->toString('transport'))
			&& in_array($this->_get->toString('transport'), array('email', 'sms'))
		) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Missing, empty or invalid parameters.'));
		}
		$default_company = $_SESSION[$this->defaultCompany];

		$arr = pjNotificationModel::factory()
			->where('t1.company_id', $default_company['id'])

			->where('t1.recipient', $this->_get->toString('recipient'))
			->where('t1.variant', $this->_get->toString('variant'))
			->where('t1.transport', $this->_get->toString('transport'))
			->limit(1)
			->findAll()
			->getDataIndex(0);

		if (!$arr) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Message not found.'));
		}

		$arr['i18n'] = pjBaseMultiLangModel::factory()->getMultiLang($arr['id'], 'pjNotification');
		$this->set('arr', $arr);
		// echo "<pre>"; print_r($arr); die;

		# Check SMS
		$this->set('is_sms_ready', (isset($this->option_arr['plugin_sms_api_key']) && !empty($this->option_arr['plugin_sms_api_key']) ? 1 : 0));

		# Get locales
		$locale_arr = pjBaseLocaleModel::factory()
			->select('t1.*, t2.file, t2.title')
			->join('pjBaseLocaleLanguage', 't2.iso=t1.language_iso', 'left')
			->where('t2.file IS NOT NULL')
			->orderBy('t1.sort ASC')
			->findAll()
			->getData();

		$lp_arr = array();
		foreach ($locale_arr as $item) {
			$lp_arr[$item['id'] . "_"] = array($item['file'], $item['title']);
		}
		$this->set('lp_arr', $locale_arr);
		$this->set('locale_str', self::jsonEncode($lp_arr));
		$this->set('is_flag_ready', $this->requestAction(array('controller' => 'pjBaseLocale', 'action' => 'pjActionIsFlagReady'), array('return')));
	}

	public function pjActionNotificationsSetContent()
	{
		$this->setAjax(true);

		if (!$this->isXHR()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}

		if (!self::isPost()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Invalid request.'));
		}

		if (!(isset($this->body['id']) && pjValidation::pjActionNumeric($this->body['id']))) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Missing, empty or invalid parameters.'));
		}
		$default_company = $_SESSION[$this->defaultCompany];

		$isToggle = $this->_post->check('is_active') && in_array($this->_post->toInt('is_active'), array(1, 0));
		$isFormSubmit = $this->_post->check('i18n') && !$this->_post->isEmpty('i18n');

		if (!($isToggle xor $isFormSubmit)) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Data mismatch.'));
		}

		if ($isToggle) {
			pjNotificationModel::factory()

				->set('id', $this->_post->toInt('id'))
				->modify(array('is_active' => $this->_post->toInt('is_active')));
		} elseif ($isFormSubmit) {
			pjBaseMultiLangModel::factory()->updateMultiLang($this->_post->toArray('i18n'), $this->_post->toInt('id'), 'pjNotification');
		}

		self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Notification has been updated.'));
	}

	public function pjActionPreview()
	{
		$this->appendJs('pjAdminOptions.js');
	}

	public function pjActionUpdateTheme()
	{
		$default_company = $_SESSION[$this->defaultCompany];

		$this->setAjax(true);

		if (!$this->isXHR()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}

		if (!self::isPost()) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.'));
		}

		if (!$this->_post->has('theme')) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Missing, empty or invalid parameters.'));
		}
		$theme = str_replace("theme", "", $this->_post->toString('theme'));
		pjOptionModel::factory()
			->where('t1.company_id', $default_company['id'])
			->where('foreign_id', $default_company['id'])
			->where('`key`', 'o_theme')
			->limit(1)
			->modifyAll(array('value' => '0|1|2|3|4|5|6|7|8|9|10::' . $theme));

		self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Theme has been changed.'));
	}

	public function pjActionShippingTax()
	{
		if (!pjAuth::factory()->hasAccess()) {
			$this->sendForbidden();
			return;
		}
		$company_id = $_SESSION[$this->defaultCompany]['id'];

		$arr = pjTaxModel::factory()->where('t1.company_id', $company_id)->findAll()->getData();
		foreach ($arr as $k => $v) {
			$arr[$k]['i18n'] = pjMultiLangModel::factory()->reset()->getMultiLang($v['id'], 'pjTax');
		}

		$this->set('arr', $arr);

		$this->setLocalesData();

		$this->appendJs('jquery.multilang.js', $this->getConstant('pjBase', 'PLUGIN_JS_PATH'), false, false);
		$this->appendCss('jasny-bootstrap.min.css', PJ_THIRD_PARTY_PATH . 'jasny/');
		$this->appendJs('jasny-bootstrap.min.js',  PJ_THIRD_PARTY_PATH . 'jasny/');
		$this->appendJs('pjAdminOptions.js');
	}

	private function pjActionTurnI18n($data, $key, $id, $index = NULL)
	{
		$arr = array();
		foreach ($data as $locale => $locale_arr) {
			$arr[$locale] = array(
				$key => is_null($index) ?
					(isset($locale_arr[$key]) && isset($locale_arr[$key][$id]) ? $locale_arr[$key][$id] : NULL) : (isset($locale_arr[$key]) && isset($locale_arr[$key][$id]) && isset($locale_arr[$key][$id][$index]) ? $locale_arr[$key][$id][$index] : NULL)
			);
		}

		return $arr;
	}

	public function pjActionDeleteLocation()
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
		if (!($this->_post->toInt('id'))) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Missing, empty or invalid parameters.'));
		}
		$id = $this->_post->toInt('id');
		$pjTaxModel = pjTaxModel::factory();
		$arr = $pjTaxModel->find($id)->getData();
		if (empty($arr)) {
			self::jsonResponse(array('status' => 'ERR', 'code' => 104, 'text' => 'Shipping location not found.'));
		}
		if ($pjTaxModel->reset()->set('id', $id)->erase()->getAffectedRows() == 1) {
			pjMultiLangModel::factory()->where('model', 'pjTax')->where('foreign_id', $id)->eraseAll();
			self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Shipping location has been deleted'));
		} else {
			self::jsonResponse(array('status' => 'ERR', 'code' => 105, 'text' => 'Shipping location has not been deleted.'));
		}
	}

	public function pjActionUpdate9720()
	{
		$pjAuthRolePermissionModel = pjAuthRolePermissionModel::factory();
		$pjAuthUserPermissionModel = pjAuthUserPermissionModel::factory();

		$permissions = pjAuthPermissionModel::factory()->findAll()->getDataPair('key', 'id');

		$user_arr = array();
		$u_arr = pjAuthUserModel::factory()->whereIn('t1.role_id', array(1, 2))->findAll()->getData();
		if ($u_arr) {
			$user_ids = array();
			foreach ($u_arr as $val) {
				$user_arr[$val['role_id']][] = $val['id'];
				$user_ids[] = $val['id'];
			}
			$pjAuthUserPermissionModel->whereIn('user_id', $user_ids)->eraseAll();
		}
		$roles = array(1 => 'admin', 2 => 'editor');
		foreach ($roles as $role_id => $role) {
			if (
				isset($GLOBALS['CONFIG'], $GLOBALS['CONFIG']["role_permissions_{$role}"])
				&& is_array($GLOBALS['CONFIG']["role_permissions_{$role}"])
				&& !empty($GLOBALS['CONFIG']["role_permissions_{$role}"])
			) {
				$pjAuthRolePermissionModel->reset()->where('role_id', $role_id)->eraseAll();

				foreach ($GLOBALS['CONFIG']["role_permissions_{$role}"] as $role_permission) {
					if ($role_permission == '*') {
						// Grant full permissions for the role
						foreach ($permissions as $key => $permission_id) {
							$pjAuthRolePermissionModel->setAttributes(compact('role_id', 'permission_id'))->insert();
							if (isset($user_arr[$role_id])) {
								foreach ($user_arr[$role_id] as $user_id) {
									$pjAuthUserPermissionModel->reset()->setAttributes(array('user_id' => $user_id, 'permission_id' => $permission_id))->insert();
								}
							}
						}
						break;
					} else {
						$hasAsterix = strpos($role_permission, '*') !== false;
						if ($hasAsterix) {
							$role_permission = str_replace('*', '', $role_permission);
						}

						foreach ($permissions as $key => $permission_id) {
							if ($role_permission == $key || ($hasAsterix && strpos($key, $role_permission) !== false)) {
								$pjAuthRolePermissionModel->setAttributes(compact('role_id', 'permission_id'))->insert();
								if (isset($user_arr[$role_id])) {
									foreach ($user_arr[$role_id] as $user_id) {
										$pjAuthUserPermissionModel->reset()->setAttributes(array('user_id' => $user_id, 'permission_id' => $permission_id))->insert();
									}
								}
							}
						}
					}
				}
			}
		}

		echo 'Data updated!';
		exit;
	}
}
