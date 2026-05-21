<?php
if (!defined("ROOT_PATH")) {
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdmin extends pjAppController
{
	public $defaultUser = 'admin_user';
	public $defaultCompany = 'admin_selected_company';
	public $requireLogin = true;

	public function __construct($requireLogin = null)
	{
		$this->setLayout('pjActionAdmin');

		if (!is_null($requireLogin) && is_bool($requireLogin)) {
			$this->requireLogin = $requireLogin;
		}

		if ($this->requireLogin) {
			if (!$this->isLoged() && !in_array(@$_REQUEST['action'], array('pjActionLogin', 'pjActionForgot', 'pjActionPreview', 'pjActionExportFeed'))) {
				if (!$this->isXHR()) {
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjBase&action=pjActionLogin");
				} else {
					header('HTTP/1.1 401 Unauthorized');
					exit;
				}
			}
		}

		$ref_inherits_arr = array();
		if ($this->isXHR() && isset($_SERVER['HTTP_REFERER'])) {
			$http_refer_arr = parse_url($_SERVER['HTTP_REFERER']);
			parse_str($http_refer_arr['query'], $arr);
			if (isset($arr['controller']) && isset($arr['action'])) {
				parse_str($_SERVER['QUERY_STRING'], $query_string_arr);
				$key = $query_string_arr['controller'] . '_' . $query_string_arr['action'];
				$cnt = pjAuthPermissionModel::factory()->where('`key`', $key)->findCount()->getData();
				if ($cnt <= 0) {
					$ref_inherits_arr[$query_string_arr['controller'] . '::' . $query_string_arr['action']] = $arr['controller'] . '::' . $arr['action'];
				}
			}
		}
		$inherits_arr = array(
			'pjAdminBranding::pjActionUploadSidebarLogo' => 'pjBaseOptions::pjActionVisual',
			'pjAdminBranding::pjActionRemoveSidebarLogo' => 'pjBaseOptions::pjActionVisual',
			'pjBaseOptions::pjActionUploadSidebarLogo' => 'pjBaseOptions::pjActionVisual',
			'pjBaseOptions::pjActionRemoveSidebarLogo' => 'pjBaseOptions::pjActionVisual',
			'pjAdminOptions::pjActionUpdateTheme' => 'pjAdminOptions::pjActionPreview',
			'pjBasePermissions::pjActionResetPermission' => 'pjBasePermissions::pjActionUserPermission',
			'pjAdminOptions::pjActionDeleteLocation' => 'pjAdminOptions::pjActionShippingTax',
			'pjAdminCategories::pjActionGetCategory' => 'pjAdminCategories::pjActionIndex',
			'pjAdminCategories::pjActionCreate' => 'pjAdminCategories::pjActionCreateForm',
			'pjAdminCategories::pjActionUpdate' => 'pjAdminCategories::pjActionUpdateForm',
			'pjAdminCategories::pjActionSaveCategory' => 'pjAdminCategories::pjActionUpdateForm',
			'pjAdminCategories::pjActionSetOrder' => 'pjAdminCategories::pjActionUpdateForm',
			'pjAdminProducts::pjActionCheckStockAttributes' => 'pjAdminProducts::pjActionUpdate'

		);
		if ($_REQUEST['controller'] == 'pjAdminOptions' && isset($_REQUEST['next_action'])) {
			$inherits_arr['pjAdminOptions::pjActionUpdate'] = 'pjAdminOptions::' . $_REQUEST['next_action'];
		}
		$inherits_arr = array_merge($inherits_arr, $ref_inherits_arr);
		pjRegistry::getInstance()->set('inherits', $inherits_arr);
	}

	public function beforeFilter()
	{
		parent::beforeFilter();

		if (!pjAuth::factory()->hasAccess() && @$_REQUEST['action'] != 'pjActionExportFeed') {
			$this->sendForbidden();
			return false;
		}

		return true;
	}

	public function afterFilter()
	{
		parent::afterFilter();
		$this->appendJs('index.php?controller=pjBase&action=pjActionMessages', PJ_INSTALL_URL, true);
	}

	public function beforeRender() {}


	public function pjActionIndex()
	{
		$this->checkLogin();
		if (!pjAuth::factory()->hasAccess()) {
			$this->sendForbidden();
			return;
		}

		$company_id = isset($_SESSION[$this->defaultCompany]['id'])
			? (int) $_SESSION[$this->defaultCompany]['id']
			: 0;
		if ($company_id <= 0) {
			$this->sendForbidden();
			return;
		}

		$this->appendCss('c3.min.css', PJ_THIRD_PARTY_PATH . 'c3/');
		$this->appendJs('d3.min.js', PJ_THIRD_PARTY_PATH . 'd3/');
		$this->appendJs('c3.min.js', PJ_THIRD_PARTY_PATH . 'c3/');
		$this->appendJs('moment-with-locales.min.js', PJ_THIRD_PARTY_PATH . 'moment/');
		$this->appendCss('admin-dashboard.css', PJ_CSS_PATH);

		$filter = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : 'week';

		switch ($filter) {
			case 'today':
				$startDate = date('Y-m-d');
				$endDate = $startDate;
				$prevStartDate = date('Y-m-d', strtotime('-1 day'));
				$prevEndDate = $prevStartDate;
				break;
			case 'month':
				$startDate = date('Y-m-01');
				$endDate = date('Y-m-d');
				$prevStartDate = date('Y-m-01', strtotime('-1 month'));
				$prevEndDate = date('Y-m-d', strtotime('-1 month'));
				break;
			case 'year':
				$startDate = date('Y-m-01', strtotime('-11 months'));
				$endDate = date('Y-m-d');
				$prevStartDate = date('Y-m-01', strtotime('-23 months'));
				$prevEndDate = date('Y-m-d', strtotime('-12 months'));
				break;
			case 'week':
			default:
				$filter = 'week';
				$startDate = date('Y-m-d', strtotime('-6 days'));
				$endDate = date('Y-m-d');
				$prevStartDate = date('Y-m-d', strtotime('-13 days'));
				$prevEndDate = date('Y-m-d', strtotime('-7 days'));
				break;
		}

		$pjProductModel = pjProductModel::factory();
		$pjOrderModel = pjOrderModel::factory();
		$orderTable = $pjOrderModel->getTable();
		$orderStockTable = pjOrderStockModel::factory()->getTable();

		// clients table has no first_name/company (unlike peter); keep aliases for dashboard view
		$order_arr = pjOrderModel::factory()
			->select("t1.*, t2.client_name, '' AS client_first_name, '' AS client_company")
			->join("pjClient", "t1.client_id=t2.id", "left outer")
			->where('t1.company_id', $company_id)
			->orderBy('t1.created DESC')
			->limit(8)
			->findAll()
			->getData();

		$pdo = new PDO('mysql:host=' . PJ_HOST . ';dbname=' . PJ_DB, PJ_USER, PJ_PASS);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$pdo->exec("SET NAMES utf8");

		$info_arr = [$this->pjDashboardGetKpi($pdo, $orderTable, $orderStockTable, $startDate, $endDate, $company_id)];
		$prev_info_arr = [$this->pjDashboardGetKpi($pdo, $orderTable, $orderStockTable, $prevStartDate, $prevEndDate, $company_id)];

		$groupByMonth = ($filter === 'year');

		if ($groupByMonth) {
			$chartSql = sprintf("SELECT DATE_FORMAT(`created`, '%%Y-%%m') AS `date`,
				COUNT(*) AS `orders`,
				COALESCE(SUM(`total`), 0) AS `amount`
			FROM `%s`
			WHERE `status` <> :cancelled AND `company_id` = :company_id AND DATE(`created`) BETWEEN :start AND :end
			GROUP BY DATE_FORMAT(`created`, '%%Y-%%m')
			ORDER BY `date` ASC", $orderTable);
		} else {
			$chartSql = sprintf("SELECT DATE(`created`) AS `date`,
				COUNT(*) AS `orders`,
				COALESCE(SUM(`total`), 0) AS `amount`
			FROM `%s`
			WHERE `status` <> :cancelled AND `company_id` = :company_id AND DATE(`created`) BETWEEN :start AND :end
			GROUP BY DATE(`created`)
			ORDER BY DATE(`created`) ASC", $orderTable);
		}

		$stmt = $pdo->prepare($chartSql);
		$stmt->execute([
			'cancelled' => 'cancelled',
			'company_id' => $company_id,
			'start' => $startDate,
			'end' => $endDate,
		]);
		$chart_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$stmt = $pdo->prepare($chartSql);
		$stmt->execute([
			'cancelled' => 'cancelled',
			'company_id' => $company_id,
			'start' => $prevStartDate,
			'end' => $prevEndDate,
		]);
		$prev_chart_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$topSql = sprintf("SELECT
			os.product_id,
			ml.content AS name,
			SUM(os.qty) AS total_qty,
			SUM(os.price * os.qty) AS total_revenue
		FROM `%s` os
		INNER JOIN `%s` o ON os.order_id = o.id
		LEFT JOIN `%s` ml ON ml.model = :model AND ml.foreign_id = os.product_id AND ml.locale = :locale AND ml.field = :field
		WHERE o.status <> :cancelled AND o.company_id = :company_id AND os.company_id = :company_id AND DATE(o.created) BETWEEN :start AND :end
		GROUP BY os.product_id
		ORDER BY total_qty DESC
		LIMIT 5", $orderStockTable, $orderTable, pjMultiLangModel::factory()->getTable());

		$stmt = $pdo->prepare($topSql);
		$stmt->execute([
			'model' => 'pjProduct', 'locale' => $this->getLocaleId(), 'field' => 'name',
			'cancelled' => 'cancelled', 'company_id' => $company_id, 'start' => $startDate, 'end' => $endDate,
		]);
		$top_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$stmt = $pdo->prepare(sprintf("SELECT `status`, COUNT(*) AS cnt
			FROM `%s`
			WHERE `company_id` = :company_id AND DATE(`created`) BETWEEN :start AND :end
			GROUP BY `status`
			ORDER BY cnt DESC", $orderTable));
		$stmt->execute(['company_id' => $company_id, 'start' => $startDate, 'end' => $endDate]);
		$status_counts = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$cnt_orders = $pjOrderModel->reset()
			->where('status <>', 'cancelled')
			->where('company_id', $company_id)
			->findCount()
			->getData();
		$cnt_new_orders = $pjOrderModel->reset()
			->where('status', 'new')
			->where('company_id', $company_id)
			->findCount()
			->getData();
		$cnt_pending_orders = $pjOrderModel->reset()
			->where('status', 'pending')
			->where('company_id', $company_id)
			->findCount()
			->getData();
		$cnt_products = $pjProductModel->reset()
			->where('company_id', $company_id)
			->findCount()
			->getData();
		$cnt_active_products = $pjProductModel->reset()
			->where('status', 1)
			->where('company_id', $company_id)
			->findCount()
			->getData();
		$cnt_out_stock = $pjProductModel->reset()
			->where('company_id', $company_id)
			->where("t1.id NOT IN(SELECT TS.product_id FROM `" . pjStockModel::factory()->getTable() . "` AS TS GROUP BY TS.product_id HAVING SUM(TS.qty) > 0)")
			->findCount()
			->getData();

		$total_amount = $pjOrderModel->reset()
			->select("SUM(total) AS amount")
			->where('status <>', 'cancelled')
			->where('company_id', $company_id)
			->findAll()
			->getData();

		$this
			->set('order_arr', $order_arr)
			->set('info_arr', $info_arr)
			->set('prev_info_arr', $prev_info_arr)
			->set('chart_data', $chart_data)
			->set('prev_chart_data', $prev_chart_data)
			->set('top_products', $top_products)
			->set('status_counts', $status_counts)
			->set('cnt_orders', $cnt_orders)
			->set('cnt_new_orders', $cnt_new_orders)
			->set('cnt_pending_orders', $cnt_pending_orders)
			->set('cnt_products', $cnt_products)
			->set('cnt_active_products', $cnt_active_products)
			->set('cnt_out_stock', $cnt_out_stock)
			->set('total_amount', !empty($total_amount) ? $total_amount[0]['amount'] : 0)
			->set('selected_filter', $filter)
			->set('start_date', $startDate)
			->set('end_date', $endDate)
			->set('prev_start_date', $prevStartDate)
			->set('prev_end_date', $prevEndDate)
			->set('group_by_month', $groupByMonth);

		if (pjObject::getPlugin('pjPayments') !== NULL) {
			$this->set('payment_option_arr', pjPaymentOptionModel::factory()->getOptions($company_id));
			$this->set('payment_titles', pjPayments::getPaymentTitles($company_id, $this->getLocaleId()));
		} else {
			$this->set('payment_titles', __('payment_methods', true));
		}

		$delivery_active = false;
		$pickup_active = false;
		if (class_exists('pjWorkingTimeModel')) {
			$pjWorkingTimeModel = pjWorkingTimeModel::factory();
			$delivery_wt = $pjWorkingTimeModel->find(1)->getData();
			$pickup_wt = $pjWorkingTimeModel->reset()->find(2)->getData();
			$delivery_active = isset($delivery_wt['status']) && $delivery_wt['status'] === 'T';
			$pickup_active = isset($pickup_wt['status']) && $pickup_wt['status'] === 'T';
		}
		$this->set('delivery_active', $delivery_active);
		$this->set('pickup_active', $pickup_active);

	}

	/**
	 * Dashboard KPI helper — orders, omzet, producten en klanten voor een periode (per company).
	 */
	private function pjDashboardGetKpi($pdo, $orderTable, $orderStockTable, $start, $end, $company_id)
	{
		$params = [
			'cancelled' => 'cancelled',
			'start' => $start,
			'end' => $end,
			'company_id' => $company_id,
		];

		$stmt = $pdo->prepare("SELECT COUNT(*) AS cnt FROM `$orderTable` WHERE `status` <> :cancelled AND `company_id` = :company_id AND DATE(`created`) BETWEEN :start AND :end");
		$stmt->execute($params);
		$orders = (int) $stmt->fetchColumn();

		$stmt = $pdo->prepare("SELECT COALESCE(SUM(`total`), 0) FROM `$orderTable` WHERE `status` <> :cancelled AND `company_id` = :company_id AND DATE(`created`) BETWEEN :start AND :end");
		$stmt->execute($params);
		$amount = (float) $stmt->fetchColumn();

		$stmt = $pdo->prepare("SELECT COUNT(DISTINCT product_id) FROM `$orderStockTable` WHERE order_id IN (SELECT `id` FROM `$orderTable` WHERE `status` <> :cancelled AND `company_id` = :company_id AND DATE(`created`) BETWEEN :start AND :end)");
		$stmt->execute($params);
		$products = (int) $stmt->fetchColumn();

		$stmt = $pdo->prepare("SELECT COUNT(DISTINCT client_id) FROM `$orderTable` WHERE `status` <> :cancelled AND `company_id` = :company_id AND DATE(`created`) BETWEEN :start AND :end AND client_id > 0");
		$stmt->execute($params);
		$customers = (int) $stmt->fetchColumn();

		return [
			'orders' => $orders,
			'amount' => $amount,
			'products' => $products,
			'customers' => $customers,
		];
	}
}
