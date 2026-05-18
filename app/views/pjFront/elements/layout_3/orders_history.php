<?php
// Initialize default values for option_arr if not set
$option_arr = isset($tpl['option_arr']) ? $tpl['option_arr'] : array();
$datetime_format = isset($option_arr['o_datetime_format']) && !empty($option_arr['o_datetime_format'])
    ? $option_arr['o_datetime_format']
    : 'd-m-Y H:i';
$install_url = isset($option_arr['o_install_url']) ? $option_arr['o_install_url'] : '';
$page_prefix_segment = (!empty($option_arr['o_page_prefix'])) ? $option_arr['o_page_prefix'] . '-' : '';
$sc_orders_list_base = rtrim($install_url, '/') . '/' . $page_prefix_segment . 'orders';
$sc_order_detail_base = rtrim($install_url, '/') . '/' . $page_prefix_segment . 'order/';

// Status labels - same as backend
$status_labels = __('order_statuses', true);
if (!is_array($status_labels)) {
    $status_labels = array(
        'new' => 'New',
        'pending' => 'Pending',
        'shipped' => 'Shipped',
        'out_for_delivery' => 'Out for delivery',
        'ready_for_pickup' => 'Ready for pickup',
        'delivered' => 'Delivered',
        'picked_up' => 'Picked up',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled'
    );
}

// Status badge classes — semantic, mapped to Template styling colours via CSS
function getStatusBadgeClass($status) {
    switch ($status) {
        case 'completed':
        case 'delivered':
        case 'picked_up':
            return 'sc-status-success';
        case 'pending':
            return 'sc-status-brand-dark';
        case 'new':
            return 'sc-status-accent';
        case 'cancelled':
        case 'refunded':
            return 'sc-status-error';
        case 'shipped':
        case 'out_for_delivery':
        case 'ready_for_pickup':
            return 'sc-status-brand-dark';
        case 'processing':
            return 'sc-status-processing';
        default:
            return 'sc-status-brand';
    }
}

// Payment status labels & classes
$payment_status_labels = array(
    'pending'   => __('front_payment_status_pending', true) ?: 'Pending',
    'pay_later' => __('front_payment_status_pay_later', true) ?: 'Pay later',
    'paid'      => __('front_payment_status_paid', true) ?: 'Paid',
    'failed'    => __('front_payment_status_failed', true) ?: 'Failed',
    'refunded'  => __('front_payment_status_refunded', true) ?: 'Refunded',
    'unknown'   => __('front_payment_status_unknown', true) ?: 'Unknown',
);
function getPaymentStatusBadgeClass($payment_status) {
    switch ($payment_status) {
        case 'paid':      return 'sc-status-success';
        case 'failed':    return 'sc-status-error';
        case 'refunded':  return 'sc-status-error';
        case 'pay_later': return 'sc-status-brand';
        case 'pending':
        default:          return 'sc-status-brand-dark';
    }
}
?>
<div class="container-fluid pjScOrdersHistory">
    <h2 class="text-uppercase"><strong><?php __('front_orders_history'); ?></strong></h2><br>

    <?php if (isset($tpl['order_arr']) && !empty($tpl['order_arr'])) { ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th><?php echo __('front_order_number', true) ?: 'Order number'; ?></th>
                    <th><?php echo __('front_order_created', true) ?: 'Created'; ?></th>
                    <th><?php echo __('front_quote_address', true) ?: 'Quote address'; ?></th>
                    <th><?php echo __('front_order_tab_billing_details', true) ?: 'Billing details'; ?></th>
                    <th><?php echo __('front_order_total', true) ?: 'Total'; ?></th>
                    <th><?php echo __('front_order_payment_status', true) ?: 'Payment status'; ?></th>
                    <th><?php echo __('front_order_status', true) ?: 'Status'; ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tpl['order_arr'] as $order) { ?>
                <tr>
                    <!-- Bestelnummer - klikbaar naar detail pagina -->
                    <td>
                        <a href="<?php echo pjSanitize::html($sc_order_detail_base . $order['uuid']); ?>" data-uuid="<?php echo pjSanitize::html($order['uuid']); ?>" class="scSelectorOrderDetails" style="font-weight: bold; text-decoration: none;">
                            <?php echo pjSanitize::html($order['uuid']); ?>
                        </a>
                    </td>

                    <!-- Datum -->
                    <td><?php echo date($datetime_format, strtotime($order['created'])); ?></td>

                    <!-- Quote address -->
                    <td>
                        <?php
                        $s_firstname = isset($order['s_firstname']) ? trim((string)$order['s_firstname']) : '';
                        $s_lastname  = isset($order['s_lastname']) ? trim((string)$order['s_lastname']) : (isset($order['s_name']) ? trim((string)$order['s_name']) : '');
                        $s_fullname  = trim($s_firstname . ' ' . $s_lastname);
                        if ($s_fullname === '' && isset($order['s_name'])) {
                            $s_fullname = trim((string)$order['s_name']);
                        }
                        $s_address_1 = isset($order['s_address_1']) ? trim((string)$order['s_address_1']) : '';
                        $s_house_nr  = isset($order['s_house_number']) ? trim((string)$order['s_house_number']) : '';
                        $s_zip       = isset($order['s_zip']) ? trim((string)$order['s_zip']) : '';
                        $s_city      = isset($order['s_city']) ? trim((string)$order['s_city']) : '';
                        $s_country   = isset($order['s_country']) ? trim((string)$order['s_country']) : '';
                        $s_street    = trim($s_address_1 . (!empty($s_house_nr) ? ' ' . $s_house_nr : ''));

                        if (!empty($s_fullname) || !empty($s_street)) {
                            if (!empty($s_fullname)) echo '<strong>' . pjSanitize::html($s_fullname) . '</strong><br>';
                            if (!empty($s_street)) echo pjSanitize::html($s_street) . '<br>';
                            $location = array();
                            if (!empty($s_zip)) $location[] = pjSanitize::html($s_zip);
                            if (!empty($s_city)) $location[] = pjSanitize::html($s_city);
                            if (!empty($location)) echo implode(' ', $location);
                            if (!empty($s_country)) echo '<br><small class="text-muted">' . pjSanitize::html($s_country) . '</small>';
                        } else {
                            echo '<span class="text-muted">-</span>';
                        }
                        ?>
                    </td>

                    <!-- Factuuradres -->
                    <td>
                        <?php
                        $b_firstname = isset($order['b_firstname']) ? trim((string)$order['b_firstname']) : '';
                        $b_lastname  = isset($order['b_lastname']) ? trim((string)$order['b_lastname']) : (isset($order['b_name']) ? trim((string)$order['b_name']) : '');
                        $b_fullname  = trim($b_firstname . ' ' . $b_lastname);
                        if ($b_fullname === '' && isset($order['b_name'])) {
                            $b_fullname = trim((string)$order['b_name']);
                        }
                        $b_address_1 = isset($order['b_address_1']) ? trim((string)$order['b_address_1']) : '';
                        $b_house_nr  = isset($order['b_house_number']) ? trim((string)$order['b_house_number']) : '';
                        $b_zip       = isset($order['b_zip']) ? trim((string)$order['b_zip']) : '';
                        $b_city      = isset($order['b_city']) ? trim((string)$order['b_city']) : '';
                        $b_country   = isset($order['b_country']) ? trim((string)$order['b_country']) : '';
                        $b_street    = trim($b_address_1 . (!empty($b_house_nr) ? ' ' . $b_house_nr : ''));

                        if (!empty($b_fullname) || !empty($b_street)) {
                            if (!empty($b_fullname)) echo '<strong>' . pjSanitize::html($b_fullname) . '</strong><br>';
                            if (!empty($b_street)) echo pjSanitize::html($b_street) . '<br>';
                            $location = array();
                            if (!empty($b_zip)) $location[] = pjSanitize::html($b_zip);
                            if (!empty($b_city)) $location[] = pjSanitize::html($b_city);
                            if (!empty($location)) echo implode(' ', $location);
                            if (!empty($b_country)) echo '<br><small class="text-muted">' . pjSanitize::html($b_country) . '</small>';
                        } else {
                            echo '<span class="text-muted">-</span>';
                        }
                        ?>
                    </td>

                    <!-- Totaal -->
                    <td><strong><?php echo pjCurrency::formatPrice(isset($order['total']) ? $order['total'] : 0); ?></strong></td>

                    <!-- Betaalstatus -->
                    <td>
                        <?php
                        $payment_status = isset($order['payment_status']) ? $order['payment_status'] : 'pending';
                        $payment_status_label = isset($payment_status_labels[$payment_status]) ? $payment_status_labels[$payment_status] : ucfirst(str_replace('_', ' ', $payment_status));
                        $payment_badge_class = getPaymentStatusBadgeClass($payment_status);
                        ?>
                        <span class="sc-status-label <?php echo $payment_badge_class; ?>"><?php echo pjSanitize::html($payment_status_label); ?></span>
                    </td>

                    <!-- Bestelstatus -->
                    <td>
                        <?php
                        $status = isset($order['status']) ? $order['status'] : 'pending';
                        $status_label = isset($status_labels[$status]) ? $status_labels[$status] : ucfirst(str_replace('_', ' ', $status));
                        $badge_class = getStatusBadgeClass($status);
                        ?>
                        <span class="sc-status-label <?php echo $badge_class; ?>"><?php echo pjSanitize::html($status_label); ?></span>
                    </td>

                    <!-- Acties — icoon-only knoppen in dezelfde stijl -->
                    <td class="text-right sc-oh-actions">
                        <?php if (isset($order['pay_link']) && $order['pay_link'] === true && class_exists('pjMollie') && method_exists('pjMollie', 'getRetryPaymentUrl')) {
                            $retry_url = pjMollie::getRetryPaymentUrl($order);
                        ?>
                            <a href="<?php echo pjSanitize::html($retry_url); ?>" class="sc-oh-action-btn" title="<?php echo __('front_order_pay_order', true) ?: 'Pay now'; ?>" aria-label="<?php echo __('front_order_pay_order', true) ?: 'Pay now'; ?>">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                            </a>
                        <?php } ?>
                        <?php if (pjAppController::isInvoicePluginAvailable()) { ?>
                            <a href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFrontPublic&amp;action=pjActionOrderInvoice&amp;uuid=<?php echo pjSanitize::html($order['uuid']); ?>" class="sc-oh-action-btn" target="_blank" title="<?php echo __('front_print_invoice', true) ?: 'Invoice'; ?>" aria-label="<?php echo __('front_print_invoice', true) ?: 'Invoice'; ?>">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                            </a>
                        <?php } ?>
                        <?php if (!empty($order['has_downloads'])) {
                            $dl_label = __('front_digital_downloads_title', true) ?: 'Digital downloads';
                        ?>
                        <a href="<?php echo pjSanitize::html($sc_order_detail_base . $order['uuid']); ?>#digital-downloads" data-uuid="<?php echo pjSanitize::html($order['uuid']); ?>" class="sc-oh-action-btn sc-oh-action-btn--digital scSelectorOrderDetails" title="<?php echo pjSanitize::html($dl_label); ?>" aria-label="<?php echo pjSanitize::html($dl_label); ?>">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                            </a>
                        <?php } ?>
                        <a href="<?php echo pjSanitize::html($sc_order_detail_base . $order['uuid']); ?>" data-uuid="<?php echo pjSanitize::html($order['uuid']); ?>" class="sc-oh-action-btn sc-oh-action-btn--primary scSelectorOrderDetails" title="<?php echo __('front_view_order_details', true) ?: 'View details'; ?>" aria-label="<?php echo __('front_view_order_details', true) ?: 'View details'; ?>">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <?php
    // Pagination
    if (isset($tpl['paginator']) && $tpl['paginator']['pages'] > 1) {
        $paginator = $tpl['paginator'];
    ?>
    <nav aria-label="Page navigation">
        <ul class="pagination">
            <?php if ($paginator['page'] > 1) { ?>
            <li>
                <a href="<?php echo pjSanitize::html($sc_orders_list_base . (($paginator['page'] - 1) > 1 ? '/page:' . (int) ($paginator['page'] - 1) : '')); ?>" class="scSelectorOrdersPage" data-page="<?php echo (int) ($paginator['page'] - 1); ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <?php } ?>

            <?php for ($i = 1; $i <= $paginator['pages']; $i++) { ?>
            <li<?php echo $i == $paginator['page'] ? ' class="active"' : ''; ?>>
                <a href="<?php echo pjSanitize::html($sc_orders_list_base . ($i > 1 ? '/page:' . (int) $i : '')); ?>" class="scSelectorOrdersPage" data-page="<?php echo (int) $i; ?>"><?php echo $i; ?></a>
            </li>
            <?php } ?>

            <?php if ($paginator['page'] < $paginator['pages']) { ?>
            <li>
                <a href="<?php echo pjSanitize::html($sc_orders_list_base . '/page:' . (int) ($paginator['page'] + 1)); ?>" class="scSelectorOrdersPage" data-page="<?php echo (int) ($paginator['page'] + 1); ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
            <?php } ?>
        </ul>
    </nav>
    <?php } ?>

    <?php } else { ?>
    <div class="alert alert-info">
        <?php __('front_orders_not_found'); ?>
    </div>
    <?php } ?>
</div>
