<?php
if (!defined("ROOT_PATH")) {
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjFront extends pjAppController
{
	public $defaultForm = 'SCart_Form';

	public $defaultCaptcha = 'SCart_Captcha';

	public $defaultUser = 'SCart_Client';

	/**
	 * Stores selected company IDs for the logged client.
	 * Array of integers.
	 */
	public $defaultClientCompanies = 'SCart_ClientCompanies';

	public $defaultVoucher = 'SCart_Voucher';

	public $defaultCookie = 'SCart_Cookie';

	public $defaultTax = 'SCart_Tax';

	public $defaultLocale = 'SCart_LocaleId';

	public $defaultHash = 'SCart_Hash';

	public $defaultLangMenu = 'SCart_LangMenu';

	public $defaultCategoryMenu = 'SCart_CategoryMenu';

	public $cart = NULL;

	public function __construct()
	{
		$this->setLayout('pjActionFront');

		if (!isset($_SESSION[$this->defaultHash])) {
			if ($this->isLoged()) {
				$_SESSION[$this->defaultHash] = md5(PJ_SALT . $this->getUserId());
			} else {
				$_SESSION[$this->defaultHash] = md5(uniqid(rand(), true));
			}
		}

		$this->setModel('Cart', pjCartModel::factory());
		$this->cart = new pjShoppingCart($this->getModel('Cart'), $_SESSION[$this->defaultHash]);
		$this->set('cart_arr', $this->cart->getAll());

		self::allowCORS();
	}

	public function afterFilter()
	{
		if ($this->_get->check('locale') && $this->_get->toInt('locale') > 0) {
			$this->pjActionSetLocale($this->_get->toInt('locale'));
		}
		$company_id = pjUtil::getActiveCompanyId($this->defaultCompany);

		if ($this->pjActionGetLocale() === FALSE) {
			$locale_arr = pjLocaleModel::factory()->where('is_default', 1)->limit(1)->findAll()->getData();
			if (count($locale_arr) === 1) {
				$this->pjActionSetLocale($locale_arr[0]['id']);
			}
		}
		if ($this->_get->check('action') && !in_array($this->_get->toArray('action'), array('pjActionLoadCss'))) {
			$this->loadSetFields(true);
		}

		if (
			!$this->_get->check('hide') || ($this->_get->check('hide') && $this->_get->toInt('hide') !== 1) &&
			in_array($this->_get->toString('action'), array(
				'pjActionLogin',
				'pjActionForgot',
				'pjActionRegister',
				'pjActionProfile',
				'pjActionFavs',
				'pjActionProducts',
				'pjActionProduct',
				'pjActionCart',
				'pjActionCheckout',
				'pjActionPreview',
				'pjActionGetPaymentForm'
			))
		) {
			$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file, t2.title')
				->join('pjBaseLocaleLanguage', 't2.iso=t1.language_iso', 'left')
				->where('t2.file IS NOT NULL')
				->orderBy('t1.sort ASC')->findAll()->getData();

			$this->set('locale_arr', $locale_arr);
		}
		// Guest: do not restrict product visibility by company.
		// Logged client: restrict to current active company (which is enforced to be allowed).
		if ($this->isLoged() && isset($company_id) && (int) $company_id > 0) {
			$hidden_ids_arr = pjProductModel::factory()
				->where('t1.company_id', (int) $company_id)
				->where('t1.status', 2)
				->findAll()
				->getDataPair('id', 'id');
		} else {
			$hidden_ids_arr = pjProductModel::factory()
				->where('t1.status', 2)
				->findAll()
				->getDataPair('id', 'id');
		}
		$this->set('hidden_ids_arr', $hidden_ids_arr);
	}

	public function beforeFilter()
	{
		pjUtil::ensureCompanySession($this->defaultCompany);

		$result = parent::beforeFilter();

		// If client is logged and has selected companies, make sure current company is one of them.
		if ($this->isLoged()) {
			$this->pjActionEnforceSelectedCompanies();
		}

		if (!empty($this->option_arr['o_install_url']))
		{
			$this->option_arr['o_install_url'] = pjUtil::getStorefrontBaseUrl($this->option_arr['o_install_url']);
			$this->set('option_arr', $this->option_arr);
			pjRegistry::getInstance()->set('options', $this->option_arr);
		}

		return $result;
	}

	/**
	 * Returns array of allowed company IDs for current logged client.
	 * Falls back to client.company_id for legacy accounts.
	 *
	 * @return int[]
	 */
	protected function pjActionGetClientCompanyIds($forceReload = false)
	{
		if (!$this->isLoged()) {
			return array();
		}

		if (!$forceReload && isset($_SESSION[$this->defaultClientCompanies]) && is_array($_SESSION[$this->defaultClientCompanies])) {
			return $_SESSION[$this->defaultClientCompanies];
		}

		$client_id = (int) $this->getUserId();
		$ids = pjClientCompanyModel::factory()
			->where('t1.client_id', $client_id)
			->findAll()
			->getDataPair(null, 'company_id');

		$ids = array_values(array_unique(array_filter(array_map('intval', (array) $ids))));

		// Fallback for old data (single company_id in clients table)
		if (empty($ids) && !empty($_SESSION[$this->defaultUser]['company_id'])) {
			$ids = array((int) $_SESSION[$this->defaultUser]['company_id']);
		}

		$_SESSION[$this->defaultClientCompanies] = $ids;
		return $ids;
	}

	/**
	 * Ensure current active company (admin_selected_company) is allowed for the logged client.
	 * If not, automatically switch it to the first allowed company.
	 *
	 * @return void
	 */
	protected function pjActionEnforceSelectedCompanies()
	{
		$allowed = $this->pjActionGetClientCompanyIds();
		if (empty($allowed)) {
			return;
		}

		$current_company_id = pjUtil::getActiveCompanyId($this->defaultCompany);
		$current_company_id = (int) $current_company_id;

		if ($current_company_id > 0 && in_array($current_company_id, $allowed, true)) {
			return;
		}

		// Switch session+cookie to the first allowed company
		$first = (int) $allowed[0];
		if ($first > 0) {
			pjUtil::bootstrapPreviewCompanySession($first, $this->defaultCompany);
		}
	}

	/**
	 * Ensure quote cart only contains products from companies selected on register/profile.
	 *
	 * @return array{ok:bool,code?:int,label?:string}
	 */
	protected function pjActionValidateQuoteCartCompanies()
	{
		if (!$this->isLoged()) {
			return array(
				'ok'    => false,
				'code'  => 903,
				'label' => 'front_login_required',
			);
		}

		return pjAppController::validateClientCartCompanies((int) $this->getUserId(), $this->get('cart_arr'));
	}

	public function beforeRender()
	{
		$this->set('price_arr', $this->pjActionGetPrice());
	}

	protected function pjActionGetPrice()
	{

		if ($this->cart->isEmpty()) {
			return array('status' => 'ERR', 'code' => 105, 'text' => 'Empty cart.');
		}
		$company_id = pjUtil::getActiveCompanyId($this->defaultCompany);

		$data = $stock_id = $stocks = $product_id = array();
		$cart_arr = $this->get('cart_arr');
		foreach ($cart_arr as $cart_item) {
			if ($cart_item['is_cart'] == 1) {

				if (isset($cart_item['stock_id']) && (int) $cart_item['stock_id'] > 0) {
					$stock_id[] = $cart_item['stock_id'];
				}
				$product_id[] = $cart_item['product_id'];
			}
		}
		if (empty($stock_id)) {
			return array('status' => 'ERR', 'code' => 105, 'text' => 'Empty cart.');
		}
		$stocksQuery = pjStockModel::factory()
			->where('t1.status', 'T')
			->whereIn('t1.id', $stock_id);

		if (!is_null($company_id) && $company_id > 0) {
			$stocksQuery->where('t1.company_id', $company_id);
		}

		$stocks = $stocksQuery->findAll()->getDataPair('id');


		if (empty($stocks)) {
			return array('status' => 'ERR', 'code' => 106, 'text' => 'Stocks in cart not found into the database.');
		}

		$pjExtraItemModel = pjExtraItemModel::factory();

		$extraQuery = pjExtraModel::factory()
			->whereIn('t1.product_id', $product_id);

		if (!is_null($company_id) && $company_id > 0) {
			$extraQuery->where('t1.company_id', $company_id); // ADD FILTER
		}

		$extra_arr = $extraQuery->findAll()->getDataPair('id', 'price');

		foreach ($extra_arr as $e_id => $e_price) {

			$extra_items_query = $pjExtraItemModel->reset()
				->join('pjExtra', "t2.id=t1.extra_id AND t2.type='multi'", 'inner')
				->where('t1.extra_id', $e_id);

			if (!is_null($company_id) && $company_id > 0) {
				$extra_items_query->where('t2.company_id', $company_id); // FILTER EXTRA ITEMS BY COMPANY
			}

			$extra_arr[$e_id] = array(
				'price' => $e_price,
				'extra_items' => $extra_items_query->findAll()->getDataPair('id', 'price')
			);
		}


		$calc_price = pjAppController::pjActionCalcPrices($product_id, $extra_arr, $cart_arr, $stocks, @$_SESSION[$this->defaultVoucher], $this->option_arr, isset($_SESSION[$this->defaultTax]) ? $_SESSION[$this->defaultTax] : null,  'front');
		// echo "<pre>"; print_r($calc_price); die('-------');


		if ($calc_price == false) {
			return array('status' => 'ERR', 'code' => 108, 'text' => __('system_118', true));
		}

		$data['price'] = $calc_price['price'];
		$data['discount'] = $calc_price['discount'];
		$data['insurance'] = $calc_price['insurance'];
		$data['shipping'] = $calc_price['shipping'];
		$data['tax'] = $calc_price['tax'];
		$data['total'] = $calc_price['total'];
		$data['total'] = $data['total'] > 0 ? $data['total'] : 0;
		return array('status' => 'OK', 'code' => 200, 'text' => 'Success', 'data' => $data);
	}

	protected function pjActionGetCart()
	{
		$company_id = pjUtil::getActiveCompanyId($this->defaultCompany);

		# Find out what qty is in current shopping cart for each stock
		$order_arr = $product_id = $stock_id = array();
		$cart_arr = $this->get('cart_arr');

		foreach ($cart_arr as $cart_item) {
			if ($cart_item['is_cart'] == 1) {

				if (!isset($order_arr[$cart_item['stock_id']])) {
					$order_arr[$cart_item['stock_id']] = 0;
				}
				$order_arr[$cart_item['stock_id']] += $cart_item['qty'];

				$product_id[] = $cart_item['product_id'];
				if (!empty($cart_item['stock_id'])) {
					$stock_id[] = $cart_item['stock_id'];
				}
			}
		}

		$pjProductModel = pjProductModel::factory();
		$pjProductModel
			->select(sprintf(
				"t1.*, t2.content AS name,
					(SELECT GROUP_CONCAT(`category_id`) FROM `%1\$s` WHERE `product_id` = `t1`.`id` LIMIT 1) AS `category_ids`",
				pjProductCategoryModel::factory()->getTable()
			))
			->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.id AND t2.locale='" . $this->getLocaleId() . "' AND t2.field='name'", 'left outer')
			->join('pjStock', 't3.product_id=t1.id', 'inner')
			->whereIn('t1.id', $product_id);

		if (!is_null($company_id) && $company_id > 0) {
			$pjProductModel->where('t1.company_id', $company_id);
		}

		$arr = $pjProductModel
			->findAll()
			->toArray('category_ids', ',')
			->getData();


		$pjTaxModel = pjTaxModel::factory();
		$pjTaxModel
			->select('t1.*, t2.content AS location')
			->join('pjMultiLang', "t2.model='pjTax' AND t2.foreign_id=t1.id AND t2.field='location' AND t2.locale='" . $this->getLocaleId() . "'", 'left outer')
			->orderBy('`location` ASC');

		// Add company filter only when company_id is present
		if (!is_null($company_id) && $company_id > 0) {
			$pjTaxModel->where('t1.company_id', $company_id);
		}

		$tax_arr = $pjTaxModel->findAll()->getData();

		$pjStockModel = pjStockModel::factory();
		$pjStockModel
			->where('t1.status', 'T')
			->whereIn('t1.id', $stock_id);

		// ⭐ Add company filter only if available
		if (!is_null($company_id) && $company_id > 0) {
			$pjStockModel->where('t1.company_id', $company_id);
		}

		$_stock_arr = $pjStockModel->findAll()->getData();

		$stock_arr = array();
		foreach ($_stock_arr as $stock) {
			$stock_arr[$stock['id']] = $stock;
		}


		$pjStockModel2 = pjStockModel::factory();
		$pjStockModel2
			->select('t1.id, t2.small_path')
			->join('pjGallery', 't2.id=t1.image_id', 'left outer')
			->where('t1.status', 'T')
			->whereIn('t1.id', $stock_id);


		// ⭐ Add company filter only if available
		if (!is_null($company_id) && $company_id > 0) {
			$pjStockModel2->where('t1.company_id', $company_id);
		}

		$image_arr = $pjStockModel2->findAll()->getDataPair('id', 'small_path');

		foreach ($image_arr as $id => $img) {
			if (empty($img)) {
				$gallery_arr = pjGalleryModel::factory()
					->select('t1.*')
					->where("`foreign_id` = (SELECT TS.`product_id` FROM `" . pjStockModel::factory()->getTable() . "` AS TS WHERE TS.id='$id')")
					->limit(1)
					->findAll()
					->getData();
				if (!empty($gallery_arr)) {
					$image_arr[$id] = $gallery_arr[0]['small_path'];
				}
			}
		}

		$extra_arr = pjAppController::pjActionGetExtrasList($product_id, $this->getLocaleId());
		$attr_arr = pjAppController::pjActionGetAttr($product_id, $this->getLocaleId());

		return compact('arr', 'extra_arr', 'order_arr', 'attr_arr', 'stock_arr', 'tax_arr', 'image_arr');
	}

	public function pjActionCaptcha()
	{
		$this->setAjax(true);

		header("Cache-Control: max-age=3600, private");
		$rand = $this->_get->toInt('rand') ? $this->_get->toInt('rand') : rand(1, 9999);
		$patterns = 'app/web/img/button.png';
		if (!empty($this->option_arr['o_captcha_background_front']) && $this->option_arr['o_captcha_background_front'] != 'plain') {
			$patterns = PJ_INSTALL_PATH . $this->getConstant('pjBase', 'PLUGIN_IMG_PATH') . 'captcha_patterns/' . $this->option_arr['o_captcha_background_front'];
		}
		$Captcha = new pjCaptcha(PJ_INSTALL_PATH . $this->getConstant('pjBase', 'PLUGIN_WEB_PATH') . 'obj/arialbd.ttf', $this->defaultCaptcha, (int) $this->option_arr['o_captcha_length_front']);
		$Captcha->setImage($patterns)->setMode($this->option_arr['o_captcha_mode_front'])->init($rand);
		exit;
	}

	public function pjActionCheckCaptcha()
	{
		$this->setAjax(true);
		if (!$this->_get->check('captcha') || !$this->_get->toString('captcha') || !pjCaptcha::validate($this->_get->toString('captcha'), $_SESSION[$this->defaultCaptcha])) {
			echo 'false';
		} else {
			echo 'true';
		}
		exit;
	}

	public function pjActionCheckReCaptcha()
	{
		$this->setAjax(true);
		$verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $this->option_arr['o_captcha_secret_key_front'] . '&response=' . $this->_get->toString('recaptcha'));
		$responseData = json_decode($verifyResponse);
		echo $responseData->success ? 'true' : 'false';
		exit;
	}

	public static function writeLog($message, $file = 'email_log.log')
	{
		$log_path = __DIR__ . '/' . $file;

		$date = date('Y-m-d H:i:s');
		$entry = "[{$date}] {$message}" . PHP_EOL;

		file_put_contents($log_path, $entry, FILE_APPEND);
	}


	public static function pjActionConfirmSend($option_arr, $data, $salt, $opt, $locale)
	{

		$Email = self::getMailer($option_arr);

		$pjMultiLangModel = pjMultiLangModel::factory();

		$admin_email = pjAppController::getAdminEmail();
		$admin_phone = pjAppController::getAdminPhone();
		$locale_id = isset($data['locale_id']) && (int) $data['locale_id'] > 0 ? (int) $data['locale_id'] : $locale;
		$data['products'] = preg_replace('/\r\n|\n/', '<br />', pjAppController::pjActionGetProductsString($data['id'], $locale_id));
		$tokens = pjAppController::getTokens($data, $option_arr);

		$pjNotificationModel = pjNotificationModel::factory();
		/*Confirmation sent to clients*/
		// ✔ Add company filter only if valid
		$company_id = $data['company_id'];


		// $notification = $pjNotificationModel->reset()->where('recipient', 'client')->where('transport', 'email')->where('variant', $opt)->findAll()->getDataIndex(0);

		$pjNotificationModel->reset()
			->where('recipient', 'client')
			->where('transport', 'email')
			->where('variant', $opt);

		if (isset($company_id) && (int)$company_id > 0) {
			$pjNotificationModel->where('company_id', $company_id);
		}

		$notification = $pjNotificationModel
			->findAll()
			->getDataIndex(0);


		if ((int) $notification['id'] > 0 && $notification['is_active'] == 1) {
			$resp = pjFront::pjActionGetSubjectMessage($notification, $locale_id);
			$lang_message = $resp['lang_message'];
			$lang_subject = $resp['lang_subject'];
			if (count($lang_message) === 1 && count($lang_subject) === 1) {
				$subject = str_replace($tokens['search'], $tokens['replace'], $lang_subject[0]['content']);
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
				if (! empty($subject) && ! empty($message)) {
					$message = pjUtil::textToHtml($message);

					if ($option_arr['o_send_email'] == 'flexmail') {
						pjAppController::sendFlexMail($data['email'], $subject, $message, $option_arr);
					} else {

						$Email
							->setTo($data['email'])
							->setSubject($subject)
							->send($message);
					}
				}
				// $Email
				// 	->setTo($data['email'])
				// 	->setSubject($subject)
				// 	->send(pjUtil::textToHtml($message));
			}
		}
		/*Confirmation sent to admin*/
		$notification = $pjNotificationModel->reset()->where('recipient', 'admin')->where('transport', 'email')->where('variant', $opt)->findAll()->getDataIndex(0);
		if ((int) $notification['id'] > 0 && $notification['is_active'] == 1) {
			$resp = pjFront::pjActionGetSubjectMessage($notification, $locale_id);
			$lang_message = $resp['lang_message'];
			$lang_subject = $resp['lang_subject'];
			if (count($lang_message) === 1 && count($lang_subject) === 1) {
				$subject = str_replace($tokens['search'], $tokens['replace'], $lang_subject[0]['content']);
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
				if (! empty($subject) && ! empty($message)) {
					$message = pjUtil::textToHtml($message);
					foreach ($admin_email as $email) {
						if ($option_arr['o_send_email'] == 'flexmail') {
							pjAppController::sendFlexMail($email, $subject, $message, $option_arr);
						} else {
							$Email
								->setTo($email)
								->setSubject($subject)
								->send($message);
						}
					}
				}
			}
		}
		/*SMS sent to client*/
		if (!empty($data['phone'])) {
			$notification = $pjNotificationModel->reset()->where('recipient', 'client')->where('transport', 'sms')->where('variant', $opt)->findAll()->getDataIndex(0);
			if ((int) $notification['id'] > 0 && $notification['is_active'] == 1) {
				$variant = $notification['variant'] == 'confirmation' ? 'confirm' : $notification['variant'];
				$field = $variant . '_sms_tokens_' . $notification['recipient'];
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
				if (count($lang_message) === 1) {
					$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
					$params = array(
						'text' => $message,
						'type' => 'unicode',
						'key' => md5($option_arr['private_key'] . PJ_SALT)
					);
					$params['number'] = $data['phone'];
					pjBaseSms::init($params)->pjActionSend();
				}
			}
		}
		/*SMS sent to admin*/
		if (!empty($admin_phone)) {
			$notification = $pjNotificationModel->reset()->where('recipient', 'admin')->where('transport', 'sms')->where('variant', $opt)->findAll()->getDataIndex(0);
			if ((int) $notification['id'] > 0 && $notification['is_active'] == 1) {
				$variant = $notification['variant'] == 'confirmation' ? 'confirm' : $notification['variant'];
				$field = $variant . '_sms_tokens_' . $notification['recipient'];
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
				if (count($lang_message) === 1) {
					$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
					foreach ($admin_phone as $a_phone) {
						$params = array(
							'text' => $message,
							'type' => 'unicode',
							'key' => md5($option_arr['private_key'] . PJ_SALT)
						);
						$params['number'] = $a_phone;
						pjBaseSms::init($params)->pjActionSend();
					}
				}
			}
		}
	}

	public function pjActionDigitalDownload()
	{
		$this->setLayout('pjActionEmpty');

		if (!$this->_get->check('uuid') || $this->_get->toString('uuid')  == '' || !$this->_get->check('hash') || $this->_get->toString('uuid') == '' || md5($this->_get->toString('uuid') . PJ_SALT) != $this->_get->toString('hash')) {
			$this->set('status', 1);
			return;
		}

		$order = pjOrderModel::factory()->where('t1.uuid', $this->_get->toString('uuid'))->limit(1)->findAll()->getData();
		if (empty($order)) {
			$this->set('status', 2);
			return;
		}

		$order = $order[0];
		if ($order['status'] != 'completed') {
			$this->set('status', 3);
			return;
		}

		$os_arr = pjOrderStockModel::factory()
			->select('t3.digital_file, t3.digital_name, t3.digital_expire, t2.processed_on,
				DATE_ADD(t2.processed_on, INTERVAL t3.digital_expire HOUR_SECOND) AS `expire_at`,
				IF(DATE_ADD(t2.processed_on, INTERVAL t3.digital_expire HOUR_SECOND) < NOW(), 1, 0) AS `is_expired`')
			->join('pjOrder', 't2.id=t1.order_id', 'inner')
			->join('pjProduct', "t3.id=t1.product_id AND t3.is_digital='1'", 'inner')
			->where('t1.order_id', $order['id'])
			->findAll()
			->getData();

		if (empty($os_arr)) {
			$this->set('status', 4);
			return;
		}

		$digitals = $expired = array();
		foreach ($os_arr as $item) {
			if ((int) $item['is_expired'] === 0 || $item['digital_expire'] == '00:00:00') {
				$digitals[] = $item;
			} else {
				$expired[] = $item;
			}
		}

		if (empty($digitals)) {
			$this->set('status', 5);
			return;
		}

		$zip = new pjZipStream();
		foreach ($digitals as $file) {
			if (empty($file['digital_file']) || !is_file($file['digital_file'])) {
				continue;
			}
			$handle = @fopen($file['digital_file'], "rb");
			if ($handle) {
				$zip->addLargeFile($handle, $file['digital_name']);
				fclose($handle);
			}
		}
		$zip->finalize();
		$zip->sendZip(sprintf("%s.zip", $order['uuid']));
		exit;
	}

	public function pjActionGetStocks()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			if ($this->_get->check('id') && $this->_get->toInt('id') > 0) {
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

				$pjStockModel = pjStockModel::factory();
				$pjStockAttributeModel = pjStockAttributeModel::factory();
				$pjAttributeModel = pjAttributeModel::factory();

				$stock_arr = $pjStockModel
					->join('pjProduct', 't1.product_id=t2.id', 'left')
					->where('t1.product_id', $this->_get->toInt('id'))
					->where("(t1.qty > 0 OR t2.is_digital='1')")
					->where('t1.status', 'T')
					->findAll()->getData();

				$stocks = $stock_ids = $qty = $price = array();
				foreach ($stock_arr as $k => $stock) {
					$_qty = $stock['qty'];
					if (isset($order_arr[$stock['id']])) {
						$_qty -= $order_arr[$stock['id']];
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
					->join('pjMultiLang', "t2.model='pjAttribute' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='" . $this->pjActionGetLocale() . "'", 'left outer')
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

	public function pjActionSendToFriend()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			if (
				!$this->_post->check('id') || $this->_post->toInt('id') <= 0 || !$this->_post->check('url') || $this->_post->toString('url') == '' ||
				!$this->_post->check('your_email') || $this->_post->toString('your_email') == '' || !pjValidation::pjActionEmail($this->_post->toString('your_email')) ||
				!$this->_post->check('your_name') || $this->_post->toString('your_name') == '' ||
				!$this->_post->check('friend_email') || $this->_post->toString('friend_email') == '' || !pjValidation::pjActionEmail($this->_post->toString('friend_email')) ||
				!$this->_post->check('friend_name') || $this->_post->toString('friend_name') == '' ||
				!$this->_post->check('captcha') || $this->_post->toString('captcha') == ''
				|| !isset($_SESSION[$this->defaultCaptcha])
				|| !pjCaptcha::validate($this->_post->toString('captcha'), $_SESSION[$this->defaultCaptcha])
			) {
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => __('system_100', true)));
			}

			$_SESSION[$this->defaultCaptcha] = NULL;
			unset($_SESSION[$this->defaultCaptcha]);

			$notification = pjNotificationModel::factory()->where('recipient', 'client')->where('transport', 'email')->where('variant', 'send_to_friend')->findAll()->getDataIndex(0);
			if ((int) $notification['id'] > 0 && $notification['is_active'] == 1) {
				$resp = pjAppController::pjActionGetSubjectMessage($notification, $this->getLocaleId());
				$lang_message = $resp['lang_message'];
				$lang_subject = $resp['lang_subject'];
				if (count($lang_message) === 1 && count($lang_subject) === 1) {
					$search = array(
						'{FriendName}',
						'{FriendEmail}',
						'{YourName}',
						'{YourEmail}',
						'{URL}'
					);
					$replace = array(
						pjSanitize::html($this->_post->toString('friend_name')),
						pjSanitize::html($this->_post->toString('friend_email')),
						pjSanitize::html($this->_post->toString('your_name')),
						pjSanitize::html($this->_post->toString('your_email')),
						pjSanitize::html($this->_post->toString('url'))
					);
					$subject_client = str_replace($search, $replace, $lang_subject[0]['content']);
					$message_client = str_replace($search, $replace, $lang_message[0]['content']);

					$Email = self::getMailer($this->option_arr);
					$message = pjUtil::textToHtml($message_client);

					if ($this->option_arr['o_send_email'] == 'flexmail') {

						$r = $this->sendFlexMail($this->_post->toString('to'), $subject_client, $message, $this->option_arr);
					} else {

						$r = $Email
							->setFrom($this->_post->toString('your_email'))
							->setTo($this->_post->toString('friend_email'))
							->setSubject($subject_client)
							->send(pjUtil::textToHtml($message_client));
					}



					if (isset($r) && $r) {
						pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => __('system_200', true)));
					}
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => __('system_101', true)));
				}
			}
		}
		exit;
	}

	public function pjActionLoad()
	{
		ob_start();
		header("Content-type: text/javascript");
		// ini_set('display_errors', '1');
		// ini_set('display_startup_errors', '1');
		// error_reporting(E_ALL);

		$_SESSION[$this->defaultLangMenu] = 'show';
		if ($this->_get->check('locale') && $this->_get->toInt('locale') > 0) {
			$this->loadSetFields(true);
		}


		if ($this->_get->check('company_id') && $this->_get->toString('company_id') != '') {
			if ($this->_get->toString('data') != '1') {
				pjUtil::bootstrapPreviewCompanyFromRequest($_GET, $this->defaultCompany, false);
			}
		} else {
			pjUtil::ensureCompanySession($this->defaultCompany);
		}

		if ($this->_get->check('category_id') && $this->_get->toInt('category_id') > 0) {
			$_SESSION[$this->defaultCategoryMenu] = $this->_get->toInt('category_id');
		} else {
			$_SESSION[$this->defaultCategoryMenu] = 0;
		}
	}

	public function pjActionLoadCss()
	{
		if (pjUtil::bootstrapPreviewCompanyFromRequest($_GET, $this->defaultCompany, true) > 0)
		{
			$base_option_arr = $this->models['Option']->getPairs($this->getForeignId());
			$script_option_arr = pjOptionModel::factory()->getPairs($this->getForeignId());
			$this->option_arr = array_merge($base_option_arr, $script_option_arr);
		}

		$dm = new pjDependencyManager(PJ_INSTALL_PATH, PJ_THIRD_PARTY_PATH);
		$dm->load(PJ_CONFIG_PATH . 'dependencies.php')->resolve();

		$layout = $this->_get->check('layout') && in_array($this->_get->toInt('layout'), $this->getLayoutRange()) ?
			$this->_get->toInt('layout') : (int) $this->option_arr['o_layout'];
		$theme = 'theme' . $this->option_arr['o_theme'];
		if ($this->_get->check('theme')) {
			if (in_array($this->_get->toString('theme'), array('theme1', 'theme2', 'theme3', 'theme4', 'theme5', 'theme6', 'theme7', 'theme8', 'theme9', 'theme10'))) {
				$theme = $this->_get->toString('theme');
			}
		}

		$arr = array(
			array('file' => 'ShoppingCart' . $layout . '.css', 'path' => PJ_CSS_PATH),
			array('file' => 'pjQuery.fancybox.css', 'path' => $dm->getPath('pj_fancybox')),
			array('file' => 'assets/owl.carousel.min.css', 'path' => $dm->getPath('pj_owlcarousel')),
			array('file' => 'css/swiper-bundle.min.css', 'path' => $dm->getPath('pj_swiper'), 'replace' => false),
			array('file' => 'css/select2.min.css', 'path' => $dm->getPath('select2'), 'replace' => false),
			array('file' => 'storefront-company-select.css', 'path' => PJ_CSS_PATH),
			array('file' => $theme . '.css', 'path' => PJ_CSS_PATH)
		);
		header("Content-Type: text/css; charset=utf-8");
		foreach ($arr as $item) {
			$string = FALSE;
			if ($stream = fopen($item['path'] . $item['file'], 'rb')) {
				$string = stream_get_contents($stream);
				fclose($stream);
			}

			if ($string !== FALSE) {
				if (!isset($item['replace'])) {
					echo str_replace(
						array("url('", "pjWrapper"),
						array(
							"url('" . PJ_INSTALL_URL . $dm->getPath('pj_fancybox'),
							"pjWrapperShoppingCart_" . $theme
						),
						$string
					) . "\n";
				} else {
					echo $string . "\n";
				}
			}
		}
		exit;
	}

	public function pjActionLogout()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			if ($this->isLoged()) {
				$_SESSION[$this->defaultUser] = NULL;
				unset($_SESSION[$this->defaultUser]);

				$_SESSION[$this->defaultHash] = NULL;
				unset($_SESSION[$this->defaultHash]);
			}
			pjAppController::jsonResponse(array('status' => 'OK', 'code' => 201, 'text' => __('system_201', true)));
		}
		exit;
	}

	public function pjActionLocale()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			if ($this->_get->check('locale_id')) {
				$this->pjActionSetLocale($this->_get->toInt('locale_id'));
				$this->loadSetFields(true);
			}
		}
		exit;
	}

	private function pjActionSetLocale($locale)
	{
		if ((int) $locale > 0) {
			$_SESSION[$this->defaultLocale] = (int) $locale;
		}
		return $this;
	}

	public function pjActionGetLocale()
	{
		return isset($_SESSION[$this->defaultLocale]) && (int) $_SESSION[$this->defaultLocale] > 0 ? (int) $_SESSION[$this->defaultLocale] : FALSE;
	}

	public function pjActionShowShipping()
	{
		$cart_arr = $this->get('cart_arr');
		foreach ($cart_arr as $cart_item) {
			if ($cart_item['is_cart'] == 1) {

				$item = unserialize($cart_item['key_data']);
				if ((int) $item['is_digital'] === 0) {
					return true;
					break;
				}
			}
		}

		return false;
	}

	protected function pjActionSaveToAddressBook($client_id, $data, $prefix = 'b_')
	{
		$company_id = pjUtil::getActiveCompanyId($this->defaultCompany);

		return pjAddressModel::factory()->setAttributes(array(
			'client_id' => $client_id,
			'company_id' => $company_id,
			'country_id' => @$data[$prefix . 'country_id'],
			'state' => @$data[$prefix . 'state'],
			'city' => @$data[$prefix . 'city'],
			'zip' => @$data[$prefix . 'zip'],
			'address_1' => @$data[$prefix . 'address_1'],
			'address_2' => @$data[$prefix . 'address_2'],
			'name' => @$data[$prefix . 'name']
		))->insert()->getInsertId();
	}

	public function isXHR()
	{
		return parent::isXHR() || isset($_SERVER['HTTP_ORIGIN']);
	}

	protected static function allowCORS()
	{
		$install_url = parse_url(PJ_INSTALL_URL);
		if ($install_url['scheme'] == 'https') {
			header('Set-Cookie: ' . session_name() . '=' . session_id() . '; SameSite=None; Secure');
		}
		$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';
		header('P3P: CP="ALL DSP COR CUR ADM TAI OUR IND COM NAV INT"');
		header("Access-Control-Allow-Origin: $origin");
		header("Access-Control-Allow-Credentials: true");
		header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
		header("Access-Control-Allow-Headers: Origin, X-Requested-With");
		if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
			exit;
		}
	}

	public function pjActionConfirm()
	{
		$this->setAjax(true);

		if (pjObject::getPlugin('pjPayments') === NULL) {
			$this->log('pjPayments plugin not installed');
			exit;
		}

		$pjPayments = new pjPayments();
		$post = $this->_post->raw();
		$get = $this->_get->raw();
		$request = array();
		if (isset($get['payment_method'])) {
			$request = $get;
		}
		if (isset($post['payment_method'])) {
			$request = $post;
		}
		if ($pjPlugin = $pjPayments->getPaymentPlugin($request)) {
			if ($uuid = $this->requestAction(array('controller' => $pjPlugin, 'action' => 'pjActionGetCustom', 'params' => $request), array('return'))) {
				$pjOrderModel = pjOrderModel::factory();
				$order_arr = $pjOrderModel
					->select(sprintf("t1.*,
						AES_DECRYPT(t1.cc_type, '%1\$s') AS `cc_type`,
						AES_DECRYPT(t1.cc_num, '%1\$s') AS `cc_num`,
						AES_DECRYPT(t1.cc_exp_month, '%1\$s') AS `cc_exp_month`,
						AES_DECRYPT(t1.cc_exp_year, '%1\$s') AS `cc_exp_year`,
						AES_DECRYPT(t1.cc_code, '%1\$s') AS `cc_code`,
						t2.content AS b_country, t3.content AS s_country,
						t4.email, t4.client_name, t4.phone, t4.url, AES_DECRYPT(t4.password, '%1\$s') AS `password`", PJ_SALT))
					->join('pjMultiLang', "t2.model='pjBaseCountry' AND t2.foreign_id=t1.b_country_id AND t2.locale=t1.locale_id AND t2.field='name'", 'left outer')
					->join('pjMultiLang', "t3.model='pjBaseCountry' AND t3.foreign_id=t1.s_country_id AND t3.locale=t1.locale_id AND t3.field='name'", 'left outer')
					->join('pjClient', 't4.id=t1.client_id', 'left outer')
					->where('t1.uuid', $uuid)
					->limit(1)
					->findAll()
					->getDataIndex(0);
				if (!empty($order_arr)) {
					$params = array(
						'request'		=> $request,
						'payment_method' => $request['payment_method'],
						'foreign_id'	 => $this->getForeignId(),
						'amount'		 => $order_arr['total'],
						'txn_id'		 => $order_arr['txn_id'],
						'order_id'	   => $order_arr['id'],
						'cancel_hash'	=> sha1($order_arr['uuid'] . strtotime($order_arr['created']) . PJ_SALT),
						'key'			=> md5($this->option_arr['private_key'] . PJ_SALT)
					);
					$response = $this->requestAction(array('controller' => $pjPlugin, 'action' => 'pjActionConfirm', 'params' => $params), array('return'));
					if ($response['status'] == 'OK') {
						$this->log("Payments | {$pjPlugin} plugin<br>Order was confirmed. UUID: {$uuid}");

						$pjOrderModel->reset()
							->setAttributes(array('id' => $order_arr['id']))
							->modify(array('status' => 'completed', 'txn_id' => @$response['txn_id'], 'processed_on' => ':NOW()'));

						$order_arr['has_digital'] = pjAppController::pjActionCheckDigital($order_arr['id']);
						$locale_id = $this->getLocaleId();
						if ((int) $order_arr['locale_id']) {
							$locale_id = $order_arr['locale_id'];
						}
						pjFront::pjActionConfirmSend($this->option_arr, $order_arr, PJ_SALT, 'payment', $locale_id);

						echo $this->option_arr['o_thankyou_page'];
						exit;
					} elseif ($response['status'] == 'CANCEL') {
						$this->log("Payments | {$pjPlugin} plugin<br>Payment was cancelled. UUID: {$uuid}");

						$pjOrderModel->reset()
							->setAttributes(array('id' => $order_arr['id']))
							->modify(array('status' => 'cancelled', 'processed_on' => ':NOW()'));

						$order_arr['has_digital'] = pjAppController::pjActionCheckDigital($order_arr['id']);
						$locale_id = $this->getLocaleId();
						if ((int) $order_arr['locale_id']) {
							$locale_id = $order_arr['locale_id'];
						}
						pjFront::pjActionConfirmSend($this->option_arr, $order_arr, PJ_SALT, 'cancel', $locale_id);

						echo $this->option_arr['o_thankyou_page'];
						exit;
					} else {
						$this->log("Payments | {$pjPlugin} plugin<br>Order confirmation was failed. UUID: {$uuid}");
					}

					if (isset($response['redirect']) && $response['redirect'] == true) {
						echo $this->option_arr['o_thankyou_page'];
						exit;
					}
				} else {
					$this->log("Payments | {$pjPlugin} plugin<br>Reservation with UUID {$uuid} not found.");
				}
				echo $this->option_arr['o_thankyou_page'];
				exit;
			}
		}

		echo $this->option_arr['o_thank_you_page'];
		exit;
	}
}
