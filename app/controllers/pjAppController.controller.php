<?php
if (!defined("ROOT_PATH")) {
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAppController extends pjBaseAppController
{
	/** Product Photos tab + stock variant images */
	const GALLERY_MODEL_PRODUCT = 'pjProduct';

	/** Listing/model image only — never shown in product photo gallery */
	const GALLERY_MODEL_MODEL_IMAGE = 'pjProductModelImage';

	public $models = array();

	public function pjActionCheckInstall()
	{
		$this->setLayout('pjActionEmpty');

		$result = array('status' => 'OK', 'code' => 200, 'text' => 'Operation succeeded', 'info' => array());
		$folders = array(
			'app/web/upload/digital',
			'app/web/upload/large',
			'app/web/upload/locale',
			'app/web/upload/medium',
			'app/web/upload/small',
			'app/web/upload/source'
		);
		foreach ($folders as $dir) {
			if (!is_writable($dir)) {
				$result['status'] = 'ERR';
				$result['code'] = 101;
				$result['text'] = 'Permission requirement';
				$result['info'][] = sprintf('Folder \'<span class="bold">%1$s</span>\' is not writable. You need to set write permissions (chmod 777) to directory located at \'<span class="bold">%1$s</span>\'', $dir);
			}
		}

		return $result;
	}

	/**
	 * Sets some predefined role permissions and grants full permissions to Admin.
	 */
	public function pjActionAfterInstall()
	{
		$this->setLayout('pjActionEmpty');

		$result = array('status' => 'OK', 'code' => 200, 'text' => 'Operation succeeded', 'info' => array());

		$pjAuthRolePermissionModel = pjAuthRolePermissionModel::factory();
		$pjAuthUserPermissionModel = pjAuthUserPermissionModel::factory();

		$permissions = pjAuthPermissionModel::factory()->findAll()->getDataPair('key', 'id');

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
							}
						}
					}
				}
			}
		}

		pjOptionModel::factory()
			->where('company_id', 1)
			->where('`key`', 'o_install_url')
			->modifyAll(array(
				'value' => pjUtil::getStorefrontBaseUrl()
			));

		pjOptionModel::factory()
			->where('company_id', 1)
			->where('`key`', 'o_setup_wizard_completed')
			->modifyAll(array('value' => '1'));

		// Grant full permissions to Admin
		$user_id = 1; // Admin ID
		$pjAuthUserPermissionModel->reset()->where('user_id', $user_id)->eraseAll();
		foreach ($permissions as $key => $permission_id) {
			$pjAuthUserPermissionModel->setAttributes(compact('user_id', 'permission_id'))->insert();
		}

		return $result;
	}

	public function isEditor()
	{
		return $this->getRoleId() == 2;
	}

	public function getForeignId()
	{
		return 1;
	}

	public function beforeFilter()
	{
		parent::beforeFilter();

		if (!in_array($this->_get->toString('controller'), array('pjFront', 'pjInstaller'))) {
			pjUtil::ensureAdminSidebarLogoOption();
			$this->appendJs('pjAdminCore.js');
			/* admin.css / admin-datagrid-listings.css / admin-ui-global.css are appended
			   from pjBaseAppController::afterFilter (after plugin CSS) for all backend controllers. */

			if (in_array($this->_get->toString('controller'), array('pjAdminProducts')) && in_array($this->_get->toString('action'), array('pjActionUpdate'))) {
				$this->appendCss('pj-all.css', PJ_FRAMEWORK_LIBS_PATH . 'pj/css/');
				$this->appendJs('jquery-ui.min.js', PJ_THIRD_PARTY_PATH . 'jquery_ui/');
				$this->appendCss('jquery-ui.min.css', PJ_THIRD_PARTY_PATH . 'jquery_ui/');
			}
		}
		return true;
	}

	public static function jsonDecode($str)
	{
		$Services_JSON = new pjServices_JSON();
		return $Services_JSON->decode($str);
	}

	public static function jsonEncode($arr)
	{
		if (function_exists('json_encode'))
		{
			$flags = 0;
			if (defined('JSON_INVALID_UTF8_SUBSTITUTE'))
			{
				$flags |= JSON_INVALID_UTF8_SUBSTITUTE;
			}
			if (defined('JSON_UNESCAPED_UNICODE'))
			{
				$flags |= JSON_UNESCAPED_UNICODE;
			}
			$encoded = json_encode($arr, $flags);
			if ($encoded !== false)
			{
				return $encoded;
			}
		}

		$Services_JSON = new pjServices_JSON();
		return $Services_JSON->encode($arr);
	}

	public static function jsonResponse($arr)
	{
		while (ob_get_level() > 0)
		{
			ob_end_clean();
		}

		if (!headers_sent())
		{
			header("Content-Type: application/json; charset=utf-8");
		}

		$json = pjAppController::jsonEncode($arr);
		if ($json === false || $json === null || $json === '')
		{
			$fallback = array(
				'status' => 'ERR',
				'code' => 500,
				'text' => 'Could not encode server response as JSON.'
			);
			if (isset($arr['text']) && is_string($arr['text']))
			{
				$fallback['text'] = mb_substr(strip_tags($arr['text']), 0, 2000);
			}
			$json = function_exists('json_encode')
				? json_encode($fallback)
				: '{"status":"ERR","code":500,"text":"Could not encode server response as JSON."}';
		}

		echo $json;
		exit;
	}

	public function getLocaleId()
	{
		return isset($_SESSION[$this->defaultLocale]) && (int) $_SESSION[$this->defaultLocale] > 0 ? (int) $_SESSION[$this->defaultLocale] : false;
	}

	public function setLocaleId($locale_id)
	{
		$_SESSION[$this->defaultLocale] = (int) $locale_id;
	}

	static public function getFromEmail()
	{
		$arr = pjAuthUserModel::factory()
			->findAll()
			->orderBy("t1.id ASC")
			->limit(1)
			->getData();
		return !empty($arr) ? $arr[0]['email'] : null;
	}

	static public function getAdminEmail()
	{
		$arr = pjAuthUserModel::factory()
			->where('t1.role_id', '1')
			->where('t1.status', 'T')
			->findAll()
			->getDataPair('id', 'email');
		return $arr;
	}

	static public function getAdminPhone()
	{
		$arr = pjAuthUserModel::factory()
			->where('t1.role_id', '1')
			->where('t1.status', 'T')
			->findAll()
			->getDataPair('id', 'phone');
		return $arr;
	}

	public function friendlyURL($str, $divider = '-')
	{
		$str = mb_strtolower($str, mb_detect_encoding($str));
		$str = trim($str);
		$str = preg_replace('/[_|\s]+/', $divider, $str);
		$str = preg_replace('/\x{00C5}/u', 'AA', $str);
		$str = preg_replace('/\x{00C6}/u', 'AE', $str);
		$str = preg_replace('/\x{00D8}/u', 'OE', $str);
		$str = preg_replace('/\x{00E5}/u', 'aa', $str);
		$str = preg_replace('/\x{00E6}/u', 'ae', $str);
		$str = preg_replace('/\x{00F8}/u', 'oe', $str);
		$str = preg_replace('/[^a-z\x{0400}-\x{04FF}0-9-]+/u', '', $str);
		$str = preg_replace('/[-]+/', $divider, $str);
		$str = preg_replace('/^-+|-+$/', '', $str);
		return $str;
	}

	public static function getSubjectMessage($notification, $locale_id)
	{
		$variant = $notification['variant'] == 'confirmation' ? 'confirm' : $notification['variant'];
		$field = $variant . '_tokens_' . $notification['recipient'];
		$pjMultiLangModel = pjMultiLangModel::factory();
		$lang_message = $pjMultiLangModel
			->reset()
			->select('t1.*')
			->where('t1.foreign_id', $notification['id'])
			->where('t1.model', 'pjNotification')
			->where('t1.locale', $locale_id)
			->where('t1.field', $field)
			->limit(0, 1)
			->findAll()
			->getData();
		$field = $variant . '_subject_' . $notification['recipient'];
		$lang_subject = $pjMultiLangModel
			->reset()
			->select('t1.*')
			->where('t1.foreign_id',  $notification['id'])
			->where('t1.model', 'pjNotification')
			->where('t1.locale', $locale_id)
			->where('t1.field', $field)
			->limit(0, 1)
			->findAll()
			->getData();
		return compact('lang_message', 'lang_subject');
	}

	public static function getSmsMessage($notification, $locale_id)
	{
		$variant = $notification['variant'] == 'confirmation' ? 'confirm' : $notification['variant'];
		$field = $variant . '_sms_' . $notification['recipient'];
		$pjMultiLangModel = pjMultiLangModel::factory();
		$lang_message = $pjMultiLangModel
			->reset()
			->select('t1.*')
			->where('t1.foreign_id', $notification['id'])
			->where('t1.model', 'pjNotification')
			->where('t1.locale', $locale_id)
			->where('t1.field', $field)
			->limit(0, 1)
			->findAll()
			->getData();
		return compact('lang_message');
	}

	public static function getDiscount($data, $option_arr)
	{
		if (!isset($data['code']) || empty($data['code'])) {
			// Missing params
			return array('status' => 'ERR', 'code' => 100, 'text' => __('system_134', true));
		}


		$pjVoucherModel = pjVoucherModel::factory();
		$pjVoucherModel::factory()
			->select(sprintf("t1.*, (SELECT GROUP_CONCAT(`product_id`) FROM `%s` WHERE `voucher_id` = `t1`.`id` LIMIT 1) AS `products`", pjVoucherProductModel::factory()->getTable()))
			->where('t1.code', $data['code']);
		// ✔ Add company filter only if valid
		if (isset($data['company_id']) && $data['company_id'] !== '') {
			$pjVoucherModel->where('t1.company_id', $data['company_id']);
		}
		$arr = $pjVoucherModel->findAll()
			->limit(1)
			->toArray('products', ',')
			->getData();

		if (empty($arr)) {
			// Not found
			return array('status' => 'ERR', 'code' => 101, 'text' => __('system_133', true));
		}
		$arr = $arr[0];

		$date = $data['date'];
		if (isset($data['hour']) && isset($data['minute'])) {
			$time = $data['hour'] . ":" . $data['minute'] . ":00";
		}
		if (!isset($time)) {
			$time = "00:00:00";
		}
		if (empty($date)) {
			// Empty date
			return array('status' => 'ERR', 'code' => 103, 'text' => __('system_135', true));
		}
		$d = strtotime($date);
		$dt = strtotime($date . " " . $time);

		$valid = false;
		switch ($arr['valid']) {
			case 'fixed':
				$time_from = strtotime($arr['date_from'] . " " . $arr['time_from']);
				$time_to = strtotime($arr['date_to'] . " " . $arr['time_to']);
				if ($time_from <= $dt && $time_to >= $dt) {
					// Valid
					$valid = true;
				}
				break;
			case 'period':
				$d_from = strtotime($arr['date_from']);
				$d_to = strtotime($arr['date_to']);
				$t_from = strtotime($arr['date_from'] . " " . $arr['time_from']);
				$t_to = strtotime($arr['date_to'] . " " . $arr['time_to']);
				if ($d_from <= $d && $d_to >= $d && $t_from <= $dt && $t_to >= $dt) {
					// Valid
					$valid = true;
				}
				break;
			case 'recurring':
				$t_from = strtotime($date . " " . $arr['time_from']);
				$t_to = strtotime($date . " " . $arr['time_to']);
				if ($arr['every'] == strtolower(date("l", $dt)) && $t_from <= $dt && $t_to >= $dt) {
					// Valid
					$valid = true;
				}
				break;
		}

		if (!$valid) {
			// Out of date
			return array('status' => 'ERR', 'code' => 102, 'text' => __('system_136', true));
		}

		// Valid
		return array(
			'status' => 'OK',
			'code' => 200,
			'text' => __('system_137', true),
			'voucher_code' => $arr['code'],
			'voucher_type' => $arr['type'],
			'voucher_apply' => $arr['apply'],
			'voucher_discount' => $arr['discount'],
			'voucher_products' => $arr['products']
		);
	}

	public static function addToHistory($record_id, $user_id, $table, $before, $after)
	{
		return pjHistoryModel::factory()->setAttributes(array(
			'record_id' => $record_id,
			'user_id' => $user_id,
			'table_name' => $table,
			'before' => base64_encode(serialize($before)),
			'after' => base64_encode(serialize($after)),
			'ip' => $_SERVER['REMOTE_ADDR']
		))->insert()->getInsertId();
	}

	public static function getTokens($order_arr, $option_arr)
	{
		$search = array(
			'{BillingName}',
			'{BillingCountry}',
			'{BillingCity}',
			'{BillingState}',
			'{BillingZip}',
			'{BillingAddress1}',
			'{BillingAddress2}',
			'{ShippingName}',
			'{ShippingCountry}',
			'{ShippingCity}',
			'{ShippingState}',
			'{ShippingZip}',
			'{ShippingAddress1}',
			'{ShippingAddress2}',
			'{ClientName}',
			'{ClientEmail}',
			'{ClientPassword}',
			'{ClientPhone}',
			'{ClientURL}',
			'{PaymentMethod}',
			'{Price}',
			'{Discount}',
			'{Insurance}',
			'{Shipping}',
			'{Tax}',
			'{Total}',
			'{Voucher}',
			'{Notes}',
			'{OrderID}',
			'{OrderUUID}',
			'{DigitalDownload}',
			'{Products}',
			'{StoreName}'
		);
		$digital_download = __('front_na', true);
		if ($order_arr['has_digital'] == true) {
			$digital_download = sprintf("%sindex.php?controller=pjFront&action=pjActionDigitalDownload&uuid=%s&hash=%s", PJ_INSTALL_URL, $order_arr['uuid'], md5($order_arr['uuid'] . PJ_SALT));
		}
		$email = isset($order_arr['email']) ? $order_arr['email'] : (isset($order_arr['client_email']) ? $order_arr['client_email'] : NULL);
		$phone = isset($order_arr['phone']) ? $order_arr['phone'] : (isset($order_arr['client_phone']) ? $order_arr['client_phone'] : NULL);
		$url = isset($order_arr['url']) ? $order_arr['url'] : (isset($order_arr['client_url']) ? $order_arr['client_url'] : NULL);
		$replace = array(
			$order_arr['b_name'],
			@$order_arr['b_country'],
			$order_arr['b_city'],
			$order_arr['b_state'],
			$order_arr['b_zip'],
			$order_arr['b_address_1'],
			$order_arr['b_address_2'],
			isset($order_arr['same_as']) && $order_arr['same_as'] == 1 ? $order_arr['b_name'] : $order_arr['s_name'],
			isset($order_arr['same_as']) && $order_arr['same_as'] == 1 ? $order_arr['b_country'] : $order_arr['s_country'],
			isset($order_arr['same_as']) && $order_arr['same_as'] == 1 ? $order_arr['b_city'] : $order_arr['s_city'],
			isset($order_arr['same_as']) && $order_arr['same_as'] == 1 ? $order_arr['b_state'] : $order_arr['s_state'],
			isset($order_arr['same_as']) && $order_arr['same_as'] == 1 ? $order_arr['b_zip'] : $order_arr['s_zip'],
			isset($order_arr['same_as']) && $order_arr['same_as'] == 1 ? $order_arr['b_address_1'] : $order_arr['s_address_1'],
			isset($order_arr['same_as']) && $order_arr['same_as'] == 1 ? $order_arr['s_address_2'] : $order_arr['s_address_2'],
			$order_arr['client_name'],
			$email,
			$order_arr['password'],
			$phone,
			$url,
			$order_arr['payment_method'],
			$order_arr['price'] . " " . $option_arr['o_currency'],
			$order_arr['discount'] . " " . $option_arr['o_currency'],
			$order_arr['insurance'] . " " . $option_arr['o_currency'],
			$order_arr['shipping'] . " " . $option_arr['o_currency'],
			$order_arr['tax'] . " " . $option_arr['o_currency'],
			$order_arr['total'] . " " . $option_arr['o_currency'],
			$order_arr['voucher'],
			$order_arr['notes'],
			$order_arr['id'],
			$order_arr['uuid'],
			$digital_download,
			@$order_arr['products'],
			__('lblStoreName', true)
		);
		return compact('search', 'replace');
	}

	public static function pjActionCheckDigital($order_id)
	{
		$has_digital = false;
		$digitals = array();
		$os_arr = pjOrderStockModel::factory()
			->select('t2.digital_file, t2.digital_name, t2.is_digital')
			->join('pjProduct', "t2.id=t1.product_id AND t2.is_digital='1'", 'inner')
			->where('t1.order_id', $order_id)
			->findAll()
			->getData();
		foreach ($os_arr as $item) {
			$digitals[] = $item;
		}
		foreach ($digitals as $file) {
			if (!empty($file['digital_file']) && is_file($file['digital_file'])) {
				$has_digital = true;
				break;
			}
		}
		return $has_digital;
	}

	public static function pjActionGetExtrasList($product_id, $locale_id)
	{
		$pjExtraItemModel = pjExtraItemModel::factory();
		$pjExtraModel = pjExtraModel::factory();
		if (!empty($product_id)) {
			$pjExtraModel->whereIn('t1.product_id', $product_id);
		}
		$extra_arr = $pjExtraModel
			->select('t1.*, t2.content AS name, t3.content AS title')
			->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.locale='$locale_id' AND t2.field='extra_name'", 'left outer')
			->join('pjMultiLang', "t3.model='pjExtra' AND t3.foreign_id=t1.id AND t3.locale='$locale_id' AND t3.field='extra_title'", 'left outer')
			->orderBy('`title` ASC, `name` ASC')
			->findAll()
			->getData();


		foreach ($extra_arr as $k => $extra) {
			$extra_arr[$k]['extra_items'] = $pjExtraItemModel
				->reset()
				->select('t1.*, t2.content AS name')
				->join('pjMultiLang', "t2.model='pjExtraItem' AND t2.foreign_id=t1.id AND t2.locale='$locale_id' AND t2.field='extra_name'", 'left outer')
				->where('t1.extra_id', $extra['id'])
				->orderBy('t1.price ASC')
				->findAll()
				->getData();
		}
		return $extra_arr;
	}

	public static function pjActionGetAttr($product_id, $locale_id)
	{
		$attr_arr = $a_arr = array();
		if (!empty($product_id)) {
			// Do not change col_name, direction
			$a_arr = pjAttributeModel::factory()
				->select('t1.id, t1.product_id, t1.parent_id, t2.content AS name')
				->join('pjMultiLang', "t2.model='pjAttribute' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='$locale_id'", 'left outer')
				->whereIn('t1.product_id', $product_id)
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

		return $attr_arr;
	}
	public static function pjActionGetQuoteStock($quote_id, $locale_id)
	{
		$os_arr = pjQuoteStockModel::factory()
			->select("t1.*, t2.sku, t3.content AS name,
				(SELECT GROUP_CONCAT(CONCAT_WS('_', `attribute_id`, `attribute_parent_id`))
					FROM `" . pjStockAttributeModel::factory()->getTable() . "`
					WHERE `stock_id` = `t1`.`stock_id`
					LIMIT 1) AS `attr`,
				(SELECT GROUP_CONCAT(CONCAT_WS('.', `extra_id`, `extra_item_id`))
					FROM `" . pjQuoteExtraModel::factory()->getTable() . "`
					WHERE `quote_stock_id` = `t1`.`id`
					LIMIT 1) AS `extra`")
			->join('pjProduct', 't2.id=t1.product_id', 'left outer')
			->join('pjMultiLang', "t3.model='pjProduct' AND t3.foreign_id=t2.id AND t3.field='name' AND t3.locale='$locale_id'", 'left outer')
			->where('t1.quote_id', $quote_id)
			->findAll()
			->getData();

		$product_id = array();
		foreach ($os_arr as $item) {
			$product_id[] = $item['product_id'];
		}

		$extra_arr = pjAppController::pjActionGetExtrasList($product_id, $locale_id);
		$attr_arr = pjAppController::pjActionGetAttr($product_id, $locale_id);

		return compact('os_arr', 'extra_arr', 'attr_arr');
	}
	public static function pjActionGetOrderStock($order_id, $locale_id)
	{
		$os_arr = pjOrderStockModel::factory()
			->select("t1.*, t2.sku, t3.content AS name,
				(SELECT GROUP_CONCAT(CONCAT_WS('_', `attribute_id`, `attribute_parent_id`))
					FROM `" . pjStockAttributeModel::factory()->getTable() . "`
					WHERE `stock_id` = `t1`.`stock_id`
					LIMIT 1) AS `attr`,
				(SELECT GROUP_CONCAT(CONCAT_WS('.', `extra_id`, `extra_item_id`))
					FROM `" . pjOrderExtraModel::factory()->getTable() . "`
					WHERE `order_stock_id` = `t1`.`id`
					LIMIT 1) AS `extra`,
				(SELECT `small_path` FROM `" . pjGalleryModel::factory()->getTable() . "` WHERE `foreign_id` = t1.product_id ORDER BY `id` ASC LIMIT 1) AS `product_image`")
			->join('pjProduct', 't2.id=t1.product_id', 'left outer')
			->join('pjMultiLang', "t3.model='pjProduct' AND t3.foreign_id=t2.id AND t3.field='name' AND t3.locale='$locale_id'", 'left outer')
			->where('t1.order_id', $order_id)
			->findAll()
			->getData();

		$product_id = array();
		$stock_ids = array();
		foreach ($os_arr as $item) {
			$product_id[] = $item['product_id'];
			if (!empty($item['stock_id'])) {
				$stock_ids[] = (int) $item['stock_id'];
			}
		}
		$stock_ids = array_values(array_unique($stock_ids));

		$image_arr = array();
		if (!empty($stock_ids)) {
			$image_arr = pjStockModel::factory()
				->select('t1.id, t2.small_path')
				->join('pjGallery', 't2.id=t1.image_id', 'left outer')
				->where('t1.status', 'T')
				->whereIn('t1.id', $stock_ids)
				->findAll()
				->getDataPair('id', 'small_path');
		}

		$extra_arr = pjAppController::pjActionGetExtrasList($product_id, $locale_id);
		$attr_arr = pjAppController::pjActionGetAttr($product_id, $locale_id);

		return compact('os_arr', 'extra_arr', 'attr_arr', 'image_arr');
	}

	public static function pjActionGetProductsString($order_id, $locale_id)
	{
		$result = pjAppController::pjActionGetOrderStock($order_id, $locale_id);
		if (!isset($result['os_arr']) || empty($result['os_arr'])) {
			return '';
		}

		$stack = array();
		foreach ($result['os_arr'] as $item) {
			$stack[] = sprintf("%s x %u", $item['name'], (int) $item['qty']);
			$attrs = array();
			if (isset($item['attr']) && !empty($item['attr'])) {
				$at = array();
				$a = explode(",", $item['attr']);
				foreach ($a as $v) {
					$t = explode("_", $v);
					$at[$t[1]] = $t[0];
				}
				foreach ($at as $attr_parent_id => $attr_id) {
					foreach ($result['attr_arr'] as $attr) {
						if ($attr['id'] == $attr_parent_id) {
							foreach ($attr['child'] as $child) {
								if ($child['id'] == $attr_id) {
									$attrs[] = sprintf('%s: %s', $attr['name'], $child['name']);
									break;
								}
							}
						}
					}
				}
			}
			if (!empty($attrs)) {
				$stack[] = join("; ", $attrs);
			}
			//Extras
			$extras = array();
			if (isset($item['extra']) && !empty($item['extra'])) {
				$a = explode(",", $item['extra']);
				foreach ($a as $eid) {
					if (strpos($eid, ".") === FALSE) {
						//single
						foreach ($result['extra_arr'] as $extra) {
							if ($extra['id'] == $eid) {
								$extras[] = $extra['name'];
								break;
							}
						}
					} else {
						//multi
						list($e_id, $ei_id) = explode(".", $eid);
						foreach ($result['extra_arr'] as $extra) {
							if ($extra['id'] == $e_id && isset($extra['extra_items']) && !empty($extra['extra_items'])) {
								foreach ($extra['extra_items'] as $extra_item) {
									if ($extra_item['id'] == $ei_id) {
										$extras[] = $extra_item['name'];
										break;
									}
								}
								break;
							}
						}
					}
				}
			}
			if (!empty($extras)) {
				$stack[] = join("; ", $extras);
			}
		}

		return join("\n", $stack);
	}

	public static function pjActionCalcPrices($product_id, $extra_arr, $cart_arr, $stocks, $voucher, $option_arr, $tax_id, $from, $back_context = 'order')
	{
		$price = $discount = $tax = $shipping = $insurance = $total = 0;

		// Saved line extras (admin order / quote recalculation). Cart rows use is_cart; DB rows do not.
		$oe_arr = array();
		if ($from === 'back' && (int) $product_id > 0) {
			if ($back_context === 'quote') {
				$oe_arr = pjQuoteExtraModel::factory()
					->where('t1.quote_id', (int) $product_id)
					->findAll()
					->getData();
			} else {
				$oe_arr = pjOrderExtraModel::factory()
					->where('t1.order_id', (int) $product_id)
					->findAll()
					->getData();
			}
		}

		foreach ($cart_arr as $cart_item) {
			$include_line = ($from === 'back')
				|| (!empty($cart_item['is_cart']) && (int) $cart_item['is_cart'] === 1);
			if (!$include_line) {
				continue;
			}

			$qty = isset($cart_item['qty']) ? (int) $cart_item['qty'] : 1;
			$amount = 0;

			if ($from === 'front') {

				$item = !empty($cart_item['key_data'])
					? @unserialize($cart_item['key_data'])
					: [];

				$stock_id = $cart_item['stock_id'] ?? null;

				// 🔒 SAFETY: stock must exist
				if ($stock_id === null || !isset($stocks[$stock_id])) {
					continue;
				}

				$stock = $stocks[$stock_id];

				// 🔒 SAFETY: stock qty check
				if (!empty($item) && empty($item['is_digital'])) {
					if (isset($stock['qty']) && $qty > (int) $stock['qty']) {
						return false;
					}
				}

				$stock_price = isset($stock['price']) ? (float) $stock['price'] : 0;
				$amount = $stock_price * $qty;

				// Extras
				if (isset($item['extra']) && is_array($item['extra'])) {
					foreach ($item['extra'] as $extra) {

						if (strpos($extra, '.') !== false) {
							[$extra_id, $extra_item_id] = explode('.', $extra);

							if (
								isset($extra_arr[$extra_id]['extra_items'][$extra_item_id])
							) {
								$amount += (float) $extra_arr[$extra_id]['extra_items'][$extra_item_id] * $qty;
							}
						} else {
							if (isset($extra_arr[$extra]['price'])) {
								$amount += (float) $extra_arr[$extra]['price'] * $qty;
							}
						}
					}
				}
			} else {

				$amount = $qty * (float) ($cart_item['price'] ?? 0);

				$line_id = isset($cart_item['id']) ? (int) $cart_item['id'] : 0;
				$line_fk = ($back_context === 'quote') ? 'quote_stock_id' : 'order_stock_id';

				foreach ($oe_arr as $oe_item) {
					if (
						$line_id > 0
						&& isset($oe_item[$line_fk])
						&& (int) $oe_item[$line_fk] === $line_id
					) {
						$amount += (float) $oe_item['price'] * $qty;
					}
				}
			}

			$price += $amount;

			if ($voucher && ($voucher['voucher_apply'] ?? '') === 'each') {
				$discount += pjUtil::getDiscount(
					$amount,
					$cart_item['product_id'] ?? null,
					$voucher
				);
			}
		}

		// Voucher total
		if ($voucher && ($voucher['voucher_apply'] ?? '') === 'total') {
			if (($voucher['voucher_type'] ?? '') === 'percent') {
				$discount = ($price * (float)$voucher['voucher_discount']) / 100;
			} elseif (($voucher['voucher_type'] ?? '') === 'amount') {
				$discount = (float)$voucher['voucher_discount'];
			}
		}

		// Tax & shipping
		if ((int)$tax_id > 0) {
			$tax_arr = pjTaxModel::factory()->find($tax_id)->getData();

			if (!empty($tax_arr)) {
				$shipping = (float)($tax_arr['shipping'] ?? 0);

				if (
					(float)($tax_arr['free'] ?? 0) > 0 &&
					$price >= (float)$tax_arr['free']
				) {
					$shipping = 0;
				}

				if ((float)($tax_arr['tax'] ?? 0) > 0) {
					$tax = ($price * (float)$tax_arr['tax']) / 100;
				}
			}
		}

		// Insurance
		switch ($option_arr['o_insurance_type'] ?? '') {
			case 'percent':
				$insurance = ($price * (float)$option_arr['o_insurance']) / 100;
				break;
			case 'amount':
				$insurance = (float)$option_arr['o_insurance'];
				break;
			default:
				$insurance = 0;
		}

		$total = $price + $shipping + $tax + $insurance - $discount;

		return compact('price', 'tax', 'shipping', 'insurance', 'discount', 'total');
	}

	protected function pjActionGenerateInvoice($order_id)
	{
		if (!isset($order_id) || (int) $order_id <= 0) {
			return array('status' => 'ERR', 'code' => 400, 'text' => 'ID is not set ot invalid.');
		}
		$arr = pjOrderModel::factory()
			->select('t1.*, t2.email, t2.phone, t2.url')
			->join('pjClient', 't2.id=t1.client_id', 'left outer')
			->find($order_id)->getData();
		if (empty($arr)) {
			return array('status' => 'ERR', 'code' => 404, 'text' => 'Order not found.');
		}

		$stack = pjAppController::pjActionGetOrderStock($arr['id'], $arr['locale_id']);

		$items = array();
		if (isset($stack['os_arr']) && !empty($stack['os_arr'])) {
			$total = 0;
			foreach ($stack['os_arr'] as $i => $item) {
				$desc = array();
				$extra_price = 0;
				if (isset($item['attr']) && !empty($item['attr'])) {
					$at = array();
					$a = explode(",", $item['attr']);
					foreach ($a as $v) {
						$t = explode("_", $v);
						$at[$t[1]] = $t[0];
					}
					foreach ($at as $attr_parent_id => $attr_id) {
						foreach ($stack['attr_arr'] as $attr) {
							if ($attr['id'] == $attr_parent_id) {
								foreach ($attr['child'] as $child) {
									if ($child['id'] == $attr_id) {
										$desc[] = sprintf('%s: %s', $attr['name'], pjSanitize::html($child['name']));
										break;
									}
								}
							}
						}
					}
				}
				//Extras
				if (isset($item['extra']) && !empty($item['extra'])) {
					$a = explode(",", $item['extra']);
					foreach ($a as $eid) {
						if (strpos($eid, ".") === FALSE) {
							//single
							foreach ($stack['extra_arr'] as $extra) {
								if ($extra['id'] == $eid) {
									$desc[] = sprintf('Extra: %s (%s)', $extra['name'], pjCurrency::formatPrice($extra['price']));
									$extra_price += $extra['price'];
									break;
								}
							}
						} else {
							//multi
							list($e_id, $ei_id) = explode(".", $eid);
							foreach ($stack['extra_arr'] as $extra) {
								if ($extra['id'] == $e_id && isset($extra['extra_items']) && !empty($extra['extra_items'])) {
									foreach ($extra['extra_items'] as $extra_item) {
										if ($extra_item['id'] == $ei_id) {
											$desc[] = sprintf('Extra: %s (%s)', $extra_item['name'], pjCurrency::formatPrice($extra_item['price']));
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
				$price = $item['price'] + $extra_price;
				$subtotal = $price * (int) $item['qty'];
				$total += $subtotal;

				$items[] = array(
					'name' => $item['name'],
					'description' => join("; ", $desc),
					'qty' => (int) $item['qty'],
					'unit_price' => number_format($price, 2, ".", ""),
					'amount' => number_format($subtotal, 2, ".", "")
				);
			}
			$items[] = array(
				'name' => __('order_insurance', true),
				'description' => NULL,
				'qty' => 1,
				'unit_price' => $arr['insurance'],
				'amount' => $arr['insurance']
			);
			$items[] = array(
				'name' => __('order_shipping', true),
				'description' => NULL,
				'qty' => 1,
				'unit_price' => $arr['shipping'],
				'amount' => $arr['shipping']
			);
		} else {
			$items[] = array(
				'name' => 'Order payment',
				'description' => "",
				'qty' => 1,
				'unit_price' => $arr['total'],
				'amount' => $arr['total']
			);
		}

		$map = array(
			'completed' => 'paid',
			'cancelled' => 'cancelled',
			'new' => 'not_paid',
			'pending' => 'not_paid'
		);

		$response = $this->requestAction(
			array(
				'controller' => 'pjInvoice',
				'action' => 'pjActionCreate',
				'params' => array(
					'key' => md5($this->option_arr['private_key'] . PJ_SALT),
					'uuid' => pjUtil::uuid(),
					'order_id' => $arr['uuid'],
					'locale_id' => !empty($arr['locale_id']) ? (int) $arr['locale_id'] : $this->getLocaleId(),
					'foreign_id' => $this->getForeignId(),
					'issue_date' => ':CURDATE()',
					'due_date' => ':CURDATE()',
					'created' => ':NOW()',
					//'modified' => ':NULL',
					'status' => @$map[$arr['status']],
					'subtotal' => $arr['price'] + $arr['insurance'] + $arr['shipping'],
					'discount' => $arr['discount'],
					'tax' => $arr['tax'],
					'shipping' => $arr['shipping'],
					'total' => $arr['total'],
					'paid_deposit' => 0,
					'amount_due' => 0,
					'currency' => $this->option_arr['o_currency'],
					'notes' => $arr['notes'],
					'b_billing_address' => $arr['b_address_1'],
					'b_name' => $arr['b_name'],
					'b_address' => $arr['b_address_1'],
					'b_street_address' => $arr['b_address_2'],
					'b_city' => $arr['b_city'],
					'b_state' => $arr['b_state'],
					'b_zip' => $arr['b_zip'],
					'b_phone' => $arr['phone'],
					'b_email' => $arr['email'],
					'b_url' => $arr['url'],
					's_shipping_address' => (int) $arr['same_as'] === 1 ? $arr['b_address_1'] : $arr['s_address_1'],
					's_name' => (int) $arr['same_as'] === 1 ? $arr['b_name'] : $arr['s_name'],
					's_address' => (int) $arr['same_as'] === 1 ? $arr['b_address_1'] : $arr['s_address_1'],
					's_street_address' => (int) $arr['same_as'] === 1 ? $arr['b_address_2'] : $arr['s_address_2'],
					's_city' => (int) $arr['same_as'] === 1 ? $arr['b_city'] : $arr['s_city'],
					's_state' => (int) $arr['same_as'] === 1 ? $arr['b_state'] : $arr['s_state'],
					's_zip' => (int) $arr['same_as'] === 1 ? $arr['b_zip'] : $arr['s_zip'],
					's_phone' => $arr['phone'],
					's_email' => $arr['email'],
					's_url' => $arr['url'],
					'items' => $items
				)
			),
			array('return')
		);

		return $response;
	}

	public function getModel($key)
	{
		if (array_key_exists($key, $this->models)) {
			return $this->models[$key];
		}

		return false;
	}

	public function setModel($key, $value)
	{
		$this->models[$key] = $value;

		return true;
	}

	public function getLayoutRange()
	{
		return $this->layoutRange;
	}

	public static function pjActionGetSubjectMessage($notification, $locale_id)
	{
		$variant = $notification['variant'] == 'confirmation' ? 'confirm' : $notification['variant'];
		$field = $variant . '_tokens_' . $notification['recipient'];
		$pjMultiLangModel = pjMultiLangModel::factory();
		$lang_message = $pjMultiLangModel
			->reset()
			->select('t1.*')
			->where('t1.foreign_id', $notification['id'])
			->where('t1.model', 'pjNotification')
			->where('t1.locale', $locale_id)
			->where('t1.field', $field)
			->limit(0, 1)
			->findAll()
			->getData();
		$field = $variant . '_subject_' . $notification['recipient'];
		$lang_subject = $pjMultiLangModel
			->reset()
			->select('t1.*')
			->where('t1.foreign_id',  $notification['id'])
			->where('t1.model', 'pjNotification')
			->where('t1.locale', $locale_id)
			->where('t1.field', $field)
			->limit(0, 1)
			->findAll()
			->getData();
		return compact('lang_message', 'lang_subject');
	}

	// public static function sendFlexMail($email, $subject, $message, $option_arr)
	// {

	// 	$curl = curl_init();
	// 	// 		$username = "70706";
	// 	// 		$password = "56f4c52c9e6b9e14443adc66b59416a559237a33c4e17f6b9828eb8cf2602302";
	// 	if (isset($option_arr['o_sender_flex_email_user_id'])) {
	// 		$username = $option_arr['o_sender_flex_email_user_id'];
	// 		$password = $option_arr['o_sender_flex_email_api_key'];
	// 	} else {
	// 		$option_arr_new = [];

	// 		foreach ($option_arr as $option) {
	// 			$option_arr_new[$option['key']] = $option['value'];
	// 		}
	// 		$username = $option_arr_new['o_sender_flex_email_user_id'];
	// 		$password = $option_arr_new['o_sender_flex_email_api_key'];

	// 	}


	// 	curl_setopt($curl, CURLOPT_USERPWD, $username . ":" . $password);
	// 	curl_setopt_array($curl, [
	// 		CURLOPT_URL            => 'https://email-api.flexmail.eu/messages',
	// 		CURLOPT_RETURNTRANSFER => true,
	// 		CURLOPT_ENCODING       => '',
	// 		CURLOPT_MAXREDIRS      => 10,
	// 		CURLOPT_TIMEOUT        => 0,
	// 		CURLOPT_FOLLOWLOCATION => true,
	// 		CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
	// 		CURLOPT_CUSTOMREQUEST  => 'POST',

	// 		CURLOPT_SSL_VERIFYPEER => false,
	// 		CURLOPT_SSL_VERIFYHOST => false,
	// 	]);
	// 	curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode([
	// 		"recipient" => [
	// 			"address" => [
	// 				"email_address" => $email,
	// 				//  "name" => $name
	// 			],
	// 		],
	// 		"from"      => [
	// 			// "email_address" => "info@easyexpo.nl",
	// 			// "name" => "Easyexpo"
	// 			"email_address" => $option_arr['o_flexmail_sender_email'],
	// 			"name"          => $option_arr['o_flexmail_sender_name'],
	// 		],
	// 		"subject"   => $subject,
	// 		"content"   => [
	// 			"html" => $message,
	// 		],
	// 	]));

	// 	$response = curl_exec($curl);
	// 	// echo "<pre>"; print_r($password);
	// 	curl_close($curl);

	// 	return $response;
	// }
	public static function sendFlexMail($email, $subject, $message, $option_arr)
	{
		$curl = curl_init();

		// If array is not in key=>value format, convert it
		if (!isset($option_arr['o_sender_flex_email_user_id']) || !isset($option_arr['o_sender_flex_email_api_key'])) {
			$option_arr_new = [];

			foreach ($option_arr as $option) {
				if (isset($option['key']) && isset($option['value'])) {
					$option_arr_new[$option['key']] = $option['value'];
				}
			}

			$option_arr = $option_arr_new;
		}

		$username = $option_arr['o_sender_flex_email_user_id'];
		$password = $option_arr['o_sender_flex_email_api_key'];
		
		curl_setopt($curl, CURLOPT_USERPWD, $username . ":" . $password);

		curl_setopt_array($curl, [
			CURLOPT_URL            => 'https://email-api.flexmail.eu/messages',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING       => '',
			CURLOPT_MAXREDIRS      => 10,
			CURLOPT_TIMEOUT        => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST  => 'POST',

			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,

			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json'
			]
		]);

		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode([
			"recipient" => [
				"address" => [
					"email_address" => $email
				]
			],
			"from" => [
				"email_address" => $option_arr['o_flexmail_sender_email'],
				"name" => $option_arr['o_flexmail_sender_name'],
			],
			"subject" => $subject,
			"content" => [
				"html" => $message,
			],
		]));

		$response = curl_exec($curl);

		curl_close($curl);

		return $response;
	}

	/**
	 * Payment status for frontend when payment_status column is absent.
	 */
	public static function resolveOrderPaymentStatus($order)
	{
		if (isset($order['payment_status']) && $order['payment_status'] !== '' && $order['payment_status'] !== null) {
			return (string) $order['payment_status'];
		}
		$status = isset($order['status']) ? (string) $order['status'] : 'new';
		$method = isset($order['payment_method']) ? (string) $order['payment_method'] : '';
		$txn_id = isset($order['txn_id']) ? trim((string) $order['txn_id']) : '';
		if ($status === 'completed') {
			return 'paid';
		}
		if ($status === 'cancelled') {
			return 'failed';
		}
		if ($txn_id !== '') {
			return 'paid';
		}
		if (in_array($method, array('cod', 'cash', 'bank'), true)) {
			return 'pay_later';
		}
		return 'pending';
	}

	public static function enrichOrderForFrontend(&$order)
	{
		$order['payment_status'] = self::resolveOrderPaymentStatus($order);
	}

	/**
	 * Load order/quote row for invoice PDF (Workwear schema — no Peter-only columns).
	 */
	public static function fetchOrderForInvoicePdf($order_uuid, $locale_id = null)
	{
		if (empty($order_uuid)) {
			return null;
		}

		$order_row = pjOrderModel::factory()
			->select("t1.id, t1.uuid, t1.locale_id, t1.discount, t1.shipping, t1.tax, t1.total, t1.price,
				t1.b_name, t1.b_address_1, t1.b_address_2, t1.b_country_id, t1.b_city, t1.b_state, t1.b_zip,
				t1.s_name, t1.s_address_1, t1.s_address_2, t1.s_country_id, t1.s_city, t1.s_state, t1.s_zip,
				t1.status, t1.payment_method, t1.txn_id, t1.notes, t1.created,
				t2.content AS b_country_name, t3.content AS s_country_name")
			->join('pjMultiLang', "t2.model='pjBaseCountry' AND t2.foreign_id=t1.b_country_id AND t2.field='name' AND t2.locale=t1.locale_id", 'left outer')
			->join('pjMultiLang', "t3.model='pjBaseCountry' AND t3.foreign_id=t1.s_country_id AND t3.field='name' AND t3.locale=t1.locale_id", 'left outer')
			->where('t1.uuid', $order_uuid)
			->limit(1)
			->findAll()
			->getDataIndex(0);

		if (empty($order_row)) {
			return null;
		}

		self::enrichOrderForFrontend($order_row);

		$order_row['shippinglabel'] = '';
		$order_row['carrier_name'] = '';
		$order_row['b_company'] = '';
		$order_row['s_company'] = '';
		$order_row['b_house_number'] = '';
		$order_row['s_house_number'] = '';

		return $order_row;
	}

	/**
	 * Merge quote/order data into invoice array for PDF template tokens.
	 */
	public static function mergeOrderIntoInvoicePdfData(&$invoice_arr, $order_row)
	{
		if (empty($order_row)) {
			return;
		}

		$invoice_arr['shippinglabel'] = isset($order_row['shippinglabel']) ? $order_row['shippinglabel'] : '';
		$invoice_arr['carrier_name'] = isset($order_row['carrier_name']) ? $order_row['carrier_name'] : '';
		$invoice_arr['b_house_number'] = isset($order_row['b_house_number']) ? $order_row['b_house_number'] : '';
		$invoice_arr['b_address_2'] = isset($order_row['b_address_2']) ? $order_row['b_address_2'] : '';
		$invoice_arr['s_house_number'] = isset($order_row['s_house_number']) ? $order_row['s_house_number'] : '';
		$invoice_arr['s_address_2'] = isset($order_row['s_address_2']) ? $order_row['s_address_2'] : '';

		if (empty($invoice_arr['b_company']) && !empty($order_row['b_company'])) {
			$invoice_arr['b_company'] = $order_row['b_company'];
		}
		if (empty($invoice_arr['s_company']) && !empty($order_row['s_company'])) {
			$invoice_arr['s_company'] = $order_row['s_company'];
		}

		if (empty($invoice_arr['tax']) && isset($order_row['tax'])) {
			$invoice_arr['tax'] = $order_row['tax'];
		}
		if (empty($invoice_arr['shipping']) && isset($order_row['shipping'])) {
			$invoice_arr['shipping'] = $order_row['shipping'];
		}
		if (empty($invoice_arr['discount']) && isset($order_row['discount'])) {
			$invoice_arr['discount'] = $order_row['discount'];
		}
		if (empty($invoice_arr['total']) && isset($order_row['total'])) {
			$invoice_arr['total'] = $order_row['total'];
		}
		if (empty($invoice_arr['b_country_title']) && !empty($order_row['b_country_name'])) {
			$invoice_arr['b_country_title'] = $order_row['b_country_name'];
		}
		if (empty($invoice_arr['s_country_title']) && !empty($order_row['s_country_name'])) {
			$invoice_arr['s_country_title'] = $order_row['s_country_name'];
		}
	}

	/**
	 * Invoice PDF footer from pjInvoice company config.
	 */
	public static function getInvoiceFooterHtml($option_arr = array(), $locale_id = null)
	{
		$config = array();
		try {
			$config = pjInvoiceConfigModel::factory()->getConfigData((int) $locale_id ?: 1);
		} catch (Exception $e) {
		}

		$companyName = !empty($config['y_company']) ? $config['y_company'] : '';
		$companyAddress = !empty($config['y_street_address']) ? $config['y_street_address'] : '';
		$companyZip = !empty($config['y_zip']) ? $config['y_zip'] : '';
		$companyCity = !empty($config['y_city']) ? $config['y_city'] : '';
		$companyPhone = !empty($config['y_phone']) ? $config['y_phone'] : '';
		$companyEmail = !empty($config['y_email']) ? $config['y_email'] : '';
		$companyUrl = !empty($config['y_url']) ? $config['y_url'] : '';
		$companyVat = !empty($config['y_vat_number']) ? $config['y_vat_number'] : '';
		$companyCoc = !empty($config['y_coc_number']) ? $config['y_coc_number'] : '';
		$companyIban = !empty($config['y_bank_iban']) ? $config['y_bank_iban'] : '';
		$zipCity = trim($companyZip . ' ' . $companyCity);

		$line1Parts = array_filter(array(
			$companyName,
			$companyAddress . ($zipCity ? ', ' . $zipCity : ''),
			$companyCoc ? 'CoC ' . $companyCoc : '',
			$companyVat ? 'VAT ' . $companyVat : '',
			$companyIban ? 'IBAN ' . $companyIban : '',
		));
		$line2Parts = array_filter(array($companyPhone, $companyEmail, $companyUrl));

		$html = '<hr style="border:none; border-top:0.5pt solid #e5e7eb; margin:0 0 4pt 0;">';
		$html .= '<div style="font-family: dejavusans, Helvetica, Arial, sans-serif; font-size:8pt; color:#9ca3af; text-align:center; line-height:1.5;">';
		if (!empty($line1Parts)) {
			$html .= htmlspecialchars(implode(' · ', $line1Parts)) . '<br>';
		}
		if (!empty($line2Parts)) {
			$html .= htmlspecialchars(implode(' · ', $line2Parts));
		}
		$html .= '</div>';

		return $html;
	}

	/**
	 * Legacy pjInvoice tokenizer template (used when y_template is not configured in admin).
	 */
	public static function getDefaultInvoiceYTemplate()
	{
		return '<table style="width:100%;" border="0"><tbody>'
			. '<tr><td style="width:50%;">{y_logo}</td><td>&nbsp;</td></tr>'
			. '<tr><td><p><strong>{y_company}</strong></p>'
			. '<p>{y_street_address}<br />{y_zip} {y_city}<br />{y_country}<br /><br />'
			. 'Invoice number: {uuid}<br />Invoice date: {issue_date}<br />'
			. 'Quote number: {order_id}<br />Payment status: {status}</p></td><td>&nbsp;</td></tr>'
			. '</tbody></table><p>&nbsp;</p>'
			. '<table style="width:100%;"><tbody>'
			. '<tr><td style="width:50%;"><strong>Billing address</strong></td>'
			. '<td style="width:50%;"><strong>Quote address</strong></td></tr>'
			. '<tr><td><strong>{b_name}</strong><br />{b_company}<br />{b_address}<br />{b_zip} {b_city}<br />{b_country}</td>'
			. '<td><strong>{s_name}</strong><br />{s_company}<br />{s_address}<br />{s_zip} {s_city}<br />{s_country}</td></tr>'
			. '</tbody></table><p>&nbsp;</p>{items}<p>&nbsp;</p>'
			. '<table style="width:100%;"><tbody>'
			. '<tr><td style="text-align:right;">Discount:</td><td style="text-align:right;">{discount}</td></tr>'
			. '<tr><td style="text-align:right;">Shipping:</td><td style="text-align:right;">{shipping}</td></tr>'
			. '<tr><td style="text-align:right;">Tax:</td><td style="text-align:right;">{tax}</td></tr>'
			. '<tr><td style="text-align:right;"><strong>Total:</strong></td>'
			. '<td style="text-align:right;"><strong>{total}</strong></td></tr>'
			. '</tbody></table>'
			. '<p>{notes}</p>';
	}

	/**
	 * Human-readable PDF filename for frontend/admin downloads.
	 */
	public static function getInvoicePdfFilename($order_id, $invoice_uuid = null)
	{
		$base = !empty($order_id) ? (string) $order_id : (string) $invoice_uuid;
		$safe = preg_replace('/[^A-Za-z0-9_-]/', '', $base);
		if ($safe === '') {
			$safe = 'invoice';
		}
		return 'invoice-' . $safe . '.pdf';
	}

	/**
	 * Peter uses a custom HTML template engine; Workwear falls back to pjInvoice tokenizer.
	 */
	public static function buildInvoiceFromTemplate($invoice_arr, $items, $option_arr = array(), $locale_id = null)
	{
		return '';
	}

	public static function isInvoicePluginAvailable()
	{
		$modelFile = PJ_PLUGINS_PATH . 'pjInvoice/models/pjInvoice.model.php';
		if (!is_file($modelFile)) {
			return false;
		}
		if (!class_exists('pjInvoiceModel', false)) {
			$appModelFile = PJ_PLUGINS_PATH . 'pjInvoice/models/pjInvoiceApp.model.php';
			if (is_file($appModelFile)) {
				require_once $appModelFile;
			}
			require_once $modelFile;
		}
		return class_exists('pjInvoiceModel', false);
	}

	protected function pjActionEnsureInvoice($order_id)
	{
		if (!self::isInvoicePluginAvailable() || (int) $order_id <= 0) {
			return null;
		}
		$arr = pjOrderModel::factory()->find($order_id)->getData();
		if (empty($arr) || empty($arr['uuid'])) {
			return null;
		}
		$existing = pjInvoiceModel::factory()
			->where('order_id', $arr['uuid'])
			->limit(1)
			->findAll()
			->getDataIndex(0);
		if (!empty($existing['uuid'])) {
			return $existing;
		}
		$response = $this->pjActionGenerateInvoice($order_id);
		if (is_array($response) && isset($response['status']) && $response['status'] === 'OK' && !empty($response['data'])) {
			return $response['data'];
		}
		return null;
	}
}
