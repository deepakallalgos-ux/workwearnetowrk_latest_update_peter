<?php
// Dashboard data
$filter = isset($tpl['selected_filter']) ? $tpl['selected_filter'] : 'week';
$info = isset($tpl['info_arr'][0]) ? $tpl['info_arr'][0] : ['orders' => 0, 'amount' => 0, 'products' => 0, 'customers' => 0];
$prev = isset($tpl['prev_info_arr'][0]) ? $tpl['prev_info_arr'][0] : ['orders' => 0, 'amount' => 0, 'products' => 0, 'customers' => 0];

$pctChange = function ($current, $previous) {
	$current = (float) $current;
	$previous = (float) $previous;
	if ($previous == 0) {
		return $current > 0 ? 100 : 0;
	}
	return round((($current - $previous) / $previous) * 100, 1);
};

$pct_orders = $pctChange($info['orders'], $prev['orders']);
$pct_amount = $pctChange($info['amount'], $prev['amount']);
$pct_products = $pctChange($info['products'], $prev['products']);
$pct_customers = $pctChange($info['customers'], $prev['customers']);

$order_statuses = __('order_statuses', true);
$has_access_update_client = pjAuth::factory('pjAdminClients', 'pjActionUpdate')->hasAccess();
$has_access_update_order = pjAuth::factory('pjAdminOrders', 'pjActionUpdate')->hasAccess();

$dashboard_filter = __('dashboard_filter', true);
if (!is_array($dashboard_filter)) {
	$dashboard_filter = [];
}
$filter_labels = [
	'today' => isset($dashboard_filter['today']) ? $dashboard_filter['today'] : '',
	'week' => isset($dashboard_filter['week']) ? $dashboard_filter['week'] : '',
	'month' => isset($dashboard_filter['month']) ? $dashboard_filter['month'] : '',
	'year' => isset($dashboard_filter['year']) ? $dashboard_filter['year'] : '',
];

$dashboard_compare = __('dashboard_compare', true);
if (!is_array($dashboard_compare)) {
	$dashboard_compare = [];
}
$prev_labels = [
	'today' => isset($dashboard_compare['today']) ? $dashboard_compare['today'] : '',
	'week' => isset($dashboard_compare['week']) ? $dashboard_compare['week'] : '',
	'month' => isset($dashboard_compare['month']) ? $dashboard_compare['month'] : '',
	'year' => isset($dashboard_compare['year']) ? $dashboard_compare['year'] : '',
];

// Chart series
$chart_data = isset($tpl['chart_data']) ? $tpl['chart_data'] : [];
$prev_chart_data = isset($tpl['prev_chart_data']) ? $tpl['prev_chart_data'] : [];
$startDate = isset($tpl['start_date']) ? $tpl['start_date'] : date('Y-m-d');
$endDate = isset($tpl['end_date']) ? $tpl['end_date'] : date('Y-m-d');

// Fill chart axis for days/months with no orders
$chart_dates = [];
$chart_amounts = [];
$chart_orders = [];
$groupByMonth = !empty($tpl['group_by_month']);

$chart_lookup = [];
foreach ($chart_data as $row) {
    $chart_lookup[$row['date']] = $row;
}

$short_months = __('short_months', true);
if (!is_array($short_months)) {
	$short_months = [];
}
$month_abbr = function ($monthNum) use ($short_months) {
	$n = (int) $monthNum;
	if (isset($short_months[$n])) {
		return $short_months[$n];
	}
	$s = (string) $n;
	if (isset($short_months[$s])) {
		return $short_months[$s];
	}
	$ts = strtotime(sprintf('2000-%02d-01', $n));

	return $ts ? date('M', $ts) : '';
};

if ($groupByMonth) {
    $current = new DateTime($startDate);
    $current->modify('first day of this month');
    $end = new DateTime($endDate);
    while ($current <= $end) {
        $key = $current->format('Y-m');
        $chart_dates[] = $month_abbr((int) $current->format('n')) . ' ' . $current->format('\'y');
        $chart_amounts[] = isset($chart_lookup[$key]) ? round((float)$chart_lookup[$key]['amount'], 2) : 0;
        $chart_orders[] = isset($chart_lookup[$key]) ? (int)$chart_lookup[$key]['orders'] : 0;
        $current->modify('+1 month');
    }
} else {
    $current = new DateTime($startDate);
    $end = new DateTime($endDate);
    $end->modify('+1 day');
    while ($current < $end) {
        $d = $current->format('Y-m-d');
        $chart_dates[] = (int) $current->format('j') . ' ' . $month_abbr((int) $current->format('n'));
        $chart_amounts[] = isset($chart_lookup[$d]) ? round((float)$chart_lookup[$d]['amount'], 2) : 0;
        $chart_orders[] = isset($chart_lookup[$d]) ? (int)$chart_lookup[$d]['orders'] : 0;
        $current->modify('+1 day');
    }
}

// Previous period series for comparison chart
$prev_lookup = [];
foreach ($prev_chart_data as $row) {
    $prev_lookup[$row['date']] = $row;
}
$prev_amounts = [];

if ($groupByMonth) {
    $prevCurrent = new DateTime(isset($tpl['prev_start_date']) ? $tpl['prev_start_date'] : date('Y-01-01', strtotime('-1 year')));
    $prevCurrent->modify('first day of this month');
    $prevEnd = new DateTime(isset($tpl['prev_end_date']) ? $tpl['prev_end_date'] : date('Y-m-d', strtotime('-1 year')));
    while ($prevCurrent <= $prevEnd) {
        $key = $prevCurrent->format('Y-m');
        $prev_amounts[] = isset($prev_lookup[$key]) ? round((float)$prev_lookup[$key]['amount'], 2) : 0;
        $prevCurrent->modify('+1 month');
    }
} else {
    $prevCurrent = new DateTime(isset($tpl['prev_start_date']) ? $tpl['prev_start_date'] : date('Y-m-d', strtotime('-7 days')));
    $prevEnd = new DateTime(isset($tpl['prev_end_date']) ? $tpl['prev_end_date'] : date('Y-m-d'));
    $prevEnd->modify('+1 day');
    while ($prevCurrent < $prevEnd) {
        $d = $prevCurrent->format('Y-m-d');
        $prev_amounts[] = isset($prev_lookup[$d]) ? round((float)$prev_lookup[$d]['amount'], 2) : 0;
        $prevCurrent->modify('+1 day');
    }
}

$top_products = isset($tpl['top_products']) ? $tpl['top_products'] : [];

$status_counts = isset($tpl['status_counts']) ? $tpl['status_counts'] : [];

$dashboard_payment_status = __('dashboard_payment_status', true);
if (!is_array($dashboard_payment_status)) {
	$dashboard_payment_status = [];
}
$paymentStatusLabels = array_merge([
	'pending' => '',
	'pay_later' => '',
	'paid' => '',
	'failed' => '',
	'refunded' => '',
	'unknown' => '',
], $dashboard_payment_status);

$n_orders_donut = (int) $info['orders'];
$donut_orders_title = $n_orders_donut === 1
	? str_replace('%d', (string) $n_orders_donut, __('dashboard_donut_orders_one', true))
	: str_replace('%d', (string) $n_orders_donut, __('dashboard_donut_orders_many', true));

$dashboard_chart_i18n = [
	'revenue' => __('dashboard_chart_revenue', true),
	'previousPeriod' => __('dashboard_chart_previous_period', true),
	'orders' => __('dashboard_chart_orders', true),
	'ordersByDay' => __('dashboard_chart_orders_by_day', true),
	'ordersByMonth' => __('dashboard_chart_orders_by_month', true),
	'emptyChart' => __('dashboard_empty_chart_data', true),
	'tooltipOrder' => __('dashboard_tooltip_order', true),
	'tooltipOrders' => __('dashboard_tooltip_orders', true),
];

// Workwear: quotes only — no delivery/pickup calendar
$calDeliveryActive = false;
$calPickupActive = false;
?>

<?php /* Dashboard: app/web/css/admin-dashboard.css (loaded from pjAdmin::pjActionIndex) */ ?>

<div class="wrapper wrapper-content animated fadeInRight">

    <!-- Period filter + widget toggle -->
    <div class="db-filter-bar">
        <?php foreach ($filter_labels as $key => $label): ?>
            <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>?controller=pjAdmin&amp;action=pjActionIndex&amp;filter=<?php echo $key; ?>"
               class="db-filter-btn <?php echo $filter === $key ? 'active' : ''; ?>"><?php echo pjSanitize::html($label); ?></a>
        <?php endforeach; ?>

        <div class="db-widget-toggle">
            <button type="button" class="db-widget-toggle-btn" id="dbWidgetToggleBtn" title="<?php echo pjSanitize::html(__('dashboard_widget_toggle_title', true)); ?>"><i class="fa fa-cog"></i></button>
            <div class="db-widget-dropdown" id="dbWidgetDropdown">
                <div class="db-widget-dropdown-title"><?php echo pjSanitize::html(__('dashboard_widgets_heading', true)); ?> <span style="float:right;font-weight:400;color:#666;text-transform:none;"><?php echo pjSanitize::html(__('dashboard_widgets_drag_hint', true)); ?></span></div>
                <label class="db-widget-opt" draggable="true" data-db-order="kpi"><span class="db-widget-grip">&#8942;&#8942;</span><input type="checkbox" data-db-toggle="kpi" checked> <?php echo pjSanitize::html(__('dashboard_widget_kpi', true)); ?></label>
                <label class="db-widget-opt" draggable="true" data-db-order="charts"><span class="db-widget-grip">&#8942;&#8942;</span><input type="checkbox" data-db-toggle="charts" checked> <?php echo pjSanitize::html(__('dashboard_widget_charts', true)); ?></label>
                <label class="db-widget-opt" draggable="true" data-db-order="top-products"><span class="db-widget-grip">&#8942;&#8942;</span><input type="checkbox" data-db-toggle="top-products" checked> <?php echo pjSanitize::html(__('dashboard_widget_top_products', true)); ?></label>
                <?php if ($calDeliveryActive || $calPickupActive): ?>
                <label class="db-widget-opt" draggable="true" data-db-order="agenda"><span class="db-widget-grip">&#8942;&#8942;</span><input type="checkbox" data-db-toggle="agenda" checked> <?php echo pjSanitize::html(__('dashboard_widget_schedule', true)); ?></label>
                <?php endif; ?>
                <label class="db-widget-opt" draggable="true" data-db-order="quick-stats"><span class="db-widget-grip">&#8942;&#8942;</span><input type="checkbox" data-db-toggle="quick-stats" checked> <?php echo pjSanitize::html(__('dashboard_widget_quick_stats', true)); ?></label>
                <label class="db-widget-opt" draggable="true" data-db-order="recent-orders"><span class="db-widget-grip">&#8942;&#8942;</span><input type="checkbox" data-db-toggle="recent-orders" checked> <?php echo pjSanitize::html(__('dashboard_widget_recent_orders', true)); ?></label>
            </div>
        </div>
    </div>

    <!-- KPI cards -->
    <div class="db-kpi-grid" data-db-widget="kpi">
        <div class="db-kpi">
            <div class="db-kpi-icon purple"><i class="fa fa-euro"></i></div>
            <div class="db-kpi-value"><?php echo pjCurrency::formatPrice((float)$info['amount']); ?></div>
            <div class="db-kpi-label"><?php echo pjSanitize::html(__('dashboard_kpi_revenue', true)); ?></div>
            <span class="db-kpi-change <?php echo $pct_amount > 0 ? 'up' : ($pct_amount < 0 ? 'down' : 'neutral'); ?>">
                <?php echo $pct_amount > 0 ? '&#9650;' : ($pct_amount < 0 ? '&#9660;' : '&#8211;'); ?>
                <?php echo abs($pct_amount); ?>%
            </span>
            <span class="db-kpi-change-vs"><?php echo pjSanitize::html(__('dashboard_vs_compare', true)); ?> <?php echo pjSanitize::html($prev_labels[$filter]); ?></span>
        </div>

        <div class="db-kpi">
            <div class="db-kpi-icon blue"><i class="fa fa-shopping-cart"></i></div>
            <div class="db-kpi-value"><?php echo (int)$info['orders']; ?></div>
            <div class="db-kpi-label"><?php echo pjSanitize::html(__('menuOrders', true)); ?></div>
            <span class="db-kpi-change <?php echo $pct_orders > 0 ? 'up' : ($pct_orders < 0 ? 'down' : 'neutral'); ?>">
                <?php echo $pct_orders > 0 ? '&#9650;' : ($pct_orders < 0 ? '&#9660;' : '&#8211;'); ?>
                <?php echo abs($pct_orders); ?>%
            </span>
            <span class="db-kpi-change-vs"><?php echo pjSanitize::html(__('dashboard_vs_compare', true)); ?> <?php echo pjSanitize::html($prev_labels[$filter]); ?></span>
        </div>

        <div class="db-kpi">
            <div class="db-kpi-icon green"><i class="fa fa-cube"></i></div>
            <div class="db-kpi-value"><?php echo (int)$info['products']; ?></div>
            <div class="db-kpi-label"><?php echo pjSanitize::html(__('dashboard_products_ordered', true)); ?></div>
            <span class="db-kpi-change <?php echo $pct_products > 0 ? 'up' : ($pct_products < 0 ? 'down' : 'neutral'); ?>">
                <?php echo $pct_products > 0 ? '&#9650;' : ($pct_products < 0 ? '&#9660;' : '&#8211;'); ?>
                <?php echo abs($pct_products); ?>%
            </span>
            <span class="db-kpi-change-vs"><?php echo pjSanitize::html(__('dashboard_vs_compare', true)); ?> <?php echo pjSanitize::html($prev_labels[$filter]); ?></span>
        </div>

        <div class="db-kpi">
            <div class="db-kpi-icon orange"><i class="fa fa-users"></i></div>
            <div class="db-kpi-value"><?php echo (int)$info['customers']; ?></div>
            <div class="db-kpi-label"><?php echo pjSanitize::html(__('lblUniqueClients', true)); ?></div>
            <span class="db-kpi-change <?php echo $pct_customers > 0 ? 'up' : ($pct_customers < 0 ? 'down' : 'neutral'); ?>">
                <?php echo $pct_customers > 0 ? '&#9650;' : ($pct_customers < 0 ? '&#9660;' : '&#8211;'); ?>
                <?php echo abs($pct_customers); ?>%
            </span>
            <span class="db-kpi-change-vs"><?php echo pjSanitize::html(__('dashboard_vs_compare', true)); ?> <?php echo pjSanitize::html($prev_labels[$filter]); ?></span>
        </div>
    </div>

    <!-- Revenue chart -->
    <div class="row" data-db-widget="charts">
        <div class="col-lg-8">
            <div class="db-chart-card">
                <h4><i class="fa fa-line-chart" style="color: var(--admin-primary, #0a5114); margin-right: 6px;"></i> <?php echo pjSanitize::html(__('dashboard_chart_revenue_overview', true)); ?></h4>
                <div id="chart-revenue"></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="db-chart-card">
                <h4><i class="fa fa-pie-chart" style="color: var(--admin-primary, #0a5114); margin-right: 6px;"></i> <?php echo pjSanitize::html(__('dashboard_chart_order_status', true)); ?></h4>
                <div id="chart-status"></div>
            </div>
        </div>
    </div>

    <div class="row" data-db-widget="top-products">
        <div class="col-lg-5">
            <div class="db-chart-card">
                <h4><i class="fa fa-bar-chart" style="color: var(--admin-info, #3b82f6); margin-right: 6px;"></i> <?php echo pjSanitize::html($groupByMonth ? __('dashboard_chart_orders_by_month', true) : __('dashboard_chart_orders_by_day', true)); ?></h4>
                <div id="chart-orders"></div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="db-chart-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <h4 style="margin: 0;"><i class="fa fa-trophy" style="color: var(--admin-warning, #f59e0b); margin-right: 6px;"></i> <?php echo pjSanitize::html(__('dashboard_chart_top_products', true)); ?></h4>
                    <?php if (pjAuth::factory('pjAdminProducts', 'pjActionIndex')->hasAccess()) { ?>
                        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProducts&amp;action=pjActionIndex" style="font-size: 12px; color: var(--admin-primary, #0a5114);"><?php echo pjSanitize::html(__('dashboard_link_all_products', true)); ?></a>
                    <?php } ?>
                </div>
                <?php if (!empty($top_products)): ?>
                    <?php $max_qty = max(array_column($top_products, 'total_qty')); ?>
                    <ul class="db-top-products">
                        <?php foreach ($top_products as $i => $prod): ?>
                            <li>
                                <span class="db-top-rank"><?php echo $i + 1; ?></span>
                                <div style="flex: 1; min-width: 0;">
                                    <div style="display: flex; align-items: center;">
                                        <span class="db-top-name"><?php echo pjSanitize::html($prod['name'] ?: sprintf(__('dashboard_top_product_fallback', true), $prod['product_id'])); ?></span>
                                        <span class="db-top-qty"><?php echo (int)$prod['total_qty']; ?>x</span>
                                        <span class="db-top-revenue"><?php echo pjCurrency::formatPrice((float)$prod['total_revenue']); ?></span>
                                    </div>
                                    <div class="db-top-bar">
                                        <div class="db-top-bar-fill" style="width: <?php echo $max_qty > 0 ? round(($prod['total_qty'] / $max_qty) * 100) : 0; ?>%;"></div>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p style="color: #555; font-size: 13px; text-align: center; padding: 30px 0;"><?php echo pjSanitize::html(__('dashboard_empty_products_period', true)); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Delivery / pickup today -->
    <div data-db-widget="agenda">
    <?php
    if ($calDeliveryActive || $calPickupActive) {
        $calWidgetId = 'dashboardCalToday';
        include dirname(__FILE__) . '/elements/calendar-today.php';
    }
    ?>
    </div>

    <!-- Quick stats -->
    <div class="db-quick-stats" data-db-widget="quick-stats">
        <div class="db-quick-stat">
            <div class="db-quick-stat-icon" style="background: var(--admin-primary-lighter, rgba(10, 81, 20, 0.1)); color: var(--admin-primary, #0a5114);"><i class="fa fa-archive"></i></div>
            <div>
                <div class="db-quick-stat-value"><?php echo (int)$tpl['cnt_active_products']; ?><span style="font-size: 13px; color: #666; font-weight: 400;"> / <?php echo (int)$tpl['cnt_products']; ?></span></div>
                <div class="db-quick-stat-label"><?php echo pjSanitize::html(__('dashboard_active_products', true)); ?></div>
            </div>
        </div>
        <div class="db-quick-stat">
            <div class="db-quick-stat-icon" style="background: #fef2f2; color: var(--admin-secondary, #dc2626);"><i class="fa fa-exclamation-triangle"></i></div>
            <div>
                <div class="db-quick-stat-value"><?php echo (int)$tpl['cnt_out_stock']; ?></div>
                <div class="db-quick-stat-label"><?php echo pjSanitize::html(__('lblOutOfStock', true)); ?></div>
            </div>
        </div>
        <div class="db-quick-stat">
            <div class="db-quick-stat-icon" style="background: var(--admin-success-light, #f0fdf4); color: var(--admin-success, #22c55e);"><i class="fa fa-euro"></i></div>
            <div>
                <div class="db-quick-stat-value"><?php echo pjCurrency::formatPrice((float)$tpl['total_amount']); ?></div>
                <div class="db-quick-stat-label"><?php echo pjSanitize::html(__('dashboard_total_revenue_all_time', true)); ?></div>
            </div>
        </div>
    </div>

    <div class="db-chart-card" data-db-widget="recent-orders">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <h4 style="margin: 0;"><i class="fa fa-clock-o" style="color: var(--admin-primary, #0a5114); margin-right: 6px;"></i> <?php echo pjSanitize::html(__('dashboard_widget_recent_orders', true)); ?></h4>
            <?php if (pjAuth::factory('pjAdminOrders', 'pjActionIndex')->hasAccess()) { ?>
                <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOrders&amp;action=pjActionIndex" style="font-size: 12px; color: var(--admin-primary, #0a5114);"><?php echo pjSanitize::html(__('dashboard_link_all_orders', true)); ?></a>
            <?php } ?>
        </div>
        <?php if (!empty($tpl['order_arr'])): ?>
            <div class="db-orders-table-wrap table-responsive">
            <table class="table table-hover db-orders-table">
                <thead>
                    <tr>
                        <th><?php echo pjSanitize::html(__('order_uuid', true)); ?></th>
                        <th><?php echo pjSanitize::html(__('order_client', true)); ?></th>
                        <th><?php echo pjSanitize::html(__('order_created', true)); ?></th>
                        <th><?php echo pjSanitize::html(__('order_payment_status', true)); ?></th>
                        <th><?php echo pjSanitize::html(__('order_status', true)); ?></th>
                        <th style="text-align: right;"><?php echo pjSanitize::html(__('order_total', true)); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tpl['order_arr'] as $v):
                        $firstName = !empty($v['b_firstname']) ? $v['b_firstname'] : (!empty($v['s_firstname']) ? $v['s_firstname'] : $v['client_first_name']);
                        $lastName = $v['client_name'];
                        $city = !empty($v['b_city']) ? $v['b_city'] : (!empty($v['s_city']) ? $v['s_city'] : '');
                        $displayParts = [];
                        if (!empty($firstName)) $displayParts[] = $firstName;
                        if (!empty($lastName)) $displayParts[] = $lastName;
                        $displayName = implode(' ', $displayParts);
                        if (!empty($city)) $displayName .= ' - ' . $city;

                        $sLabel = isset($order_statuses[$v['status']]) ? $order_statuses[$v['status']] : $v['status'];

                        $payStatus = !empty($v['payment_status']) ? $v['payment_status'] : 'pending';
                        $payLabel  = !empty($paymentStatusLabels[$payStatus]) ? $paymentStatusLabels[$payStatus] : $payStatus;
                    ?>
                    <tr>
                        <td>
                            <?php if ($has_access_update_order): ?>
                                <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOrders&amp;action=pjActionUpdate&amp;id=<?php echo $v['id']; ?>"><?php echo pjSanitize::html($v['uuid']); ?></a>
                            <?php else: ?>
                                <?php echo pjSanitize::html($v['uuid']); ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($has_access_update_client && !empty($v['client_id'])): ?>
                                <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminClients&amp;action=pjActionUpdate&amp;id=<?php echo $v['client_id']; ?>"><?php echo pjSanitize::html($displayName); ?></a>
                            <?php else: ?>
                                <?php echo pjSanitize::html($displayName); ?>
                            <?php endif; ?>
                        </td>
                        <td style="white-space: nowrap;"><?php echo date($tpl['option_arr']['o_date_format'] . ', ' . $tpl['option_arr']['o_time_format'], strtotime($v['created'])); ?></td>
                        <td><span class="pj-table-cell-label pj-pay-<?php echo pjSanitize::html($payStatus); ?>"><?php echo pjSanitize::html($payLabel); ?></span></td>
                        <td><span class="pj-table-cell-label pj-status-<?php echo pjSanitize::html($v['status']); ?>"><?php echo pjSanitize::html($sLabel); ?></span></td>
                        <td style="text-align: right;"><?php echo pjCurrency::formatPrice($v['total']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php else: ?>
            <p style="color: #555; font-size: 13px; text-align: center; padding: 30px 0;"><?php echo pjSanitize::html(__('dashboard_empty_orders', true)); ?></p>
        <?php endif; ?>
    </div>

</div>

<!-- Dashboard widget visibility and order (localStorage) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var STORAGE_KEY = 'wj_db_widgets';
    var ORDER_KEY = 'wj_db_widget_order';
    var btn = document.getElementById('dbWidgetToggleBtn');
    var dropdown = document.getElementById('dbWidgetDropdown');
    if (!btn || !dropdown) return;

    // --- Visibility ---
    var prefs = {};
    try { prefs = JSON.parse(localStorage.getItem(STORAGE_KEY)) || {}; } catch(e) {}

    var checks = dropdown.querySelectorAll('[data-db-toggle]');
    checks.forEach(function(cb) {
        var key = cb.getAttribute('data-db-toggle');
        if (prefs[key] === false) {
            cb.checked = false;
            toggleWidget(key, false);
        }
    });

    btn.addEventListener('click', function(e) {
        e.stopPropagation();
        dropdown.classList.toggle('open');
    });
    document.addEventListener('click', function(e) {
        if (!dropdown.contains(e.target)) dropdown.classList.remove('open');
    });

    checks.forEach(function(cb) {
        cb.addEventListener('change', function() {
            var key = this.getAttribute('data-db-toggle');
            var visible = this.checked;
            toggleWidget(key, visible);
            prefs[key] = visible;
            localStorage.setItem(STORAGE_KEY, JSON.stringify(prefs));
        });
    });

    function toggleWidget(key, visible) {
        var widgets = document.querySelectorAll('[data-db-widget="' + key + '"]');
        widgets.forEach(function(w) {
            if (visible) w.classList.remove('db-hidden');
            else w.classList.add('db-hidden');
        });
    }

    // --- Drag & drop order ---
    var dragItem = null;
    var dragCheckState = null; // Avoid toggling checkbox when dragging

    var opts = dropdown.querySelectorAll('.db-widget-opt[draggable]');

    opts.forEach(function(opt) {
        opt.addEventListener('dragstart', function(e) {
            dragItem = this;
            // Remember checkbox state to restore after drag
            var cb = this.querySelector('input[type="checkbox"]');
            if (cb) dragCheckState = cb.checked;
            this.classList.add('db-dragging');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', this.getAttribute('data-db-order'));
        });

        opt.addEventListener('dragend', function() {
            this.classList.remove('db-dragging');
            opts.forEach(function(o) { o.classList.remove('db-drag-over'); });
            // Restore checkbox if drag toggled it by mistake
            if (dragCheckState !== null) {
                var cb = this.querySelector('input[type="checkbox"]');
                if (cb && cb.checked !== dragCheckState) cb.checked = dragCheckState;
            }
            dragItem = null;
            dragCheckState = null;
        });

        opt.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            if (this !== dragItem) this.classList.add('db-drag-over');
        });

        opt.addEventListener('dragleave', function() {
            this.classList.remove('db-drag-over');
        });

        opt.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('db-drag-over');
            if (!dragItem || this === dragItem) return;

            // Dropdown: insert before drop target
            var parent = this.parentNode;
            var allOpts = Array.prototype.slice.call(parent.querySelectorAll('.db-widget-opt[draggable]'));
            var dragIdx = allOpts.indexOf(dragItem);
            var dropIdx = allOpts.indexOf(this);

            if (dragIdx < dropIdx) {
                parent.insertBefore(dragItem, this.nextSibling);
            } else {
                parent.insertBefore(dragItem, this);
            }

            // Reorder widgets on the page
            applyWidgetOrder();
            saveOrder();
        });
    });

    function getDropdownOrder() {
        var items = dropdown.querySelectorAll('.db-widget-opt[data-db-order]');
        var order = [];
        items.forEach(function(item) {
            order.push(item.getAttribute('data-db-order'));
        });
        return order;
    }

    function applyWidgetOrder() {
        var order = getDropdownOrder();
        var container = document.querySelector('.wrapper.wrapper-content');
        if (!container) return;

        // Collect widget blocks
        var widgetMap = {};
        order.forEach(function(key) {
            var el = container.querySelector('[data-db-widget="' + key + '"]');
            if (el) widgetMap[key] = el;
        });

        // Append in saved order (after filter bar)
        order.forEach(function(key) {
            if (widgetMap[key]) container.appendChild(widgetMap[key]);
        });
    }

    function saveOrder() {
        localStorage.setItem(ORDER_KEY, JSON.stringify(getDropdownOrder()));
    }

    // --- Apply saved order on load ---
    var savedOrder = null;
    try { savedOrder = JSON.parse(localStorage.getItem(ORDER_KEY)); } catch(e) {}

    if (savedOrder && Array.isArray(savedOrder) && savedOrder.length > 0) {
        // Reorder dropdown options
        savedOrder.forEach(function(key) {
            var opt = dropdown.querySelector('.db-widget-opt[data-db-order="' + key + '"]');
            if (opt) dropdown.appendChild(opt);
        });

        // Reorder page widgets
        applyWidgetOrder();
    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof c3 === 'undefined') return;

    var cs = getComputedStyle(document.documentElement);
    var primaryColor = cs.getPropertyValue('--admin-primary').trim() || '#0a5114';
    var successColor = cs.getPropertyValue('--admin-success').trim() || '#22c55e';
    var warningColor = cs.getPropertyValue('--admin-warning').trim() || '#f59e0b';
    var infoColor    = cs.getPropertyValue('--admin-info').trim() || '#3b82f6';

    var L = <?php echo json_encode($dashboard_chart_i18n, JSON_UNESCAPED_UNICODE); ?>;
    var revKey = L.revenue || 'Revenue';
    var prevKey = L.previousPeriod || 'Previous period';
    var ordKey = L.orders || 'Orders';

    // Revenue vs previous period
    var revTypes = {};
    revTypes[revKey] = 'area-spline';
    revTypes[prevKey] = 'spline';
    var revColors = {};
    revColors[revKey] = primaryColor;
    revColors[prevKey] = '#d1d5db';

    c3.generate({
        bindto: '#chart-revenue',
        data: {
            columns: [
                [revKey].concat(<?php echo json_encode($chart_amounts); ?>),
                [prevKey].concat(<?php echo json_encode($prev_amounts); ?>)
            ],
            types: revTypes,
            colors: revColors
        },
        axis: {
            x: {
                type: 'category',
                categories: <?php echo json_encode($chart_dates); ?>,
                tick: { culling: { max: <?php echo count($chart_dates) > 14 ? 8 : count($chart_dates); ?> } }
            },
            y: {
                tick: { format: function(v) { return '\u20AC ' + v.toLocaleString('en-GB', {minimumFractionDigits: 0, maximumFractionDigits: 0}); } },
                padding: { bottom: 0 }
            }
        },
        point: { r: <?php echo count($chart_dates) <= 14 ? 4 : 2; ?> },
        grid: { y: { show: true } },
        legend: { position: 'inset', inset: { anchor: 'top-right', x: 10, y: -5 } },
        tooltip: {
            format: {
                value: function(v) { return '\u20AC ' + v.toLocaleString('en-GB', {minimumFractionDigits: 2}); }
            }
        },
        padding: { right: 20 }
    });

    var ordTypes = {};
    ordTypes[ordKey] = 'bar';
    var ordColors = {};
    ordColors[ordKey] = infoColor;

    c3.generate({
        bindto: '#chart-orders',
        data: {
            columns: [
                [ordKey].concat(<?php echo json_encode($chart_orders); ?>)
            ],
            types: ordTypes,
            colors: ordColors
        },
        bar: { width: { ratio: <?php echo count($chart_dates) <= 7 ? 0.5 : 0.7; ?> } },
        axis: {
            x: {
                type: 'category',
                categories: <?php echo json_encode($chart_dates); ?>,
                tick: { culling: { max: <?php echo count($chart_dates) > 14 ? 8 : count($chart_dates); ?> } }
            },
            y: {
                tick: { format: function(v) { return Math.round(v); } },
                padding: { bottom: 0 },
                min: 0
            }
        },
        grid: { y: { show: true } },
        legend: { show: false },
        tooltip: {
            format: {
                value: function(v) {
                    var tpl = (v === 1) ? (L.tooltipOrder || '%d order') : (L.tooltipOrders || '%d orders');
                    return tpl.replace('%d', String(v));
                }
            }
        },
        padding: { right: 20 }
    });

    // Status donut (theme colours)
    <?php
    $status_columns = [];
    $status_color_js = [
        'new' => 'infoColor',
        'pending' => 'warningColor',
        'confirmed' => 'successColor',
        'completed' => 'successColor',
        'cancelled' => '"#dc2626"',
        'shipped' => 'primaryColor',
        'out_for_delivery' => 'primaryColor',
        'delivered' => 'successColor',
        'ready_for_pickup' => 'warningColor',
        'picked_up' => 'successColor',
    ];
    $status_labels = [];
    foreach ($status_counts as $sc) {
        $label = isset($order_statuses[$sc['status']]) ? $order_statuses[$sc['status']] : $sc['status'];
        $status_columns[] = [$label, (int)$sc['cnt']];
        $status_labels[$label] = isset($status_color_js[$sc['status']]) ? $status_color_js[$sc['status']] : '"#9ca3af"';
    }
    ?>
    <?php if (!empty($status_columns)): ?>
    var statusColors = {};
    <?php foreach ($status_labels as $label => $jsColor): ?>
    statusColors[<?php echo json_encode($label); ?>] = <?php echo $jsColor; ?>;
    <?php endforeach; ?>
    c3.generate({
        bindto: '#chart-status',
        data: {
            columns: <?php echo json_encode($status_columns); ?>,
            type: 'donut',
            colors: statusColors
        },
        donut: {
            title: <?php echo json_encode($donut_orders_title, JSON_UNESCAPED_UNICODE); ?>,
            width: 35,
            label: { show: false }
        },
        legend: { position: 'right' },
        padding: { right: 10 }
    });
    <?php else: ?>
    document.getElementById('chart-status').innerHTML = '<p style="color: #555; font-size: 13px; text-align: center; padding: 60px 0;">' + String(L.emptyChart || '') + '</p>';
    <?php endif; ?>
});
</script>
