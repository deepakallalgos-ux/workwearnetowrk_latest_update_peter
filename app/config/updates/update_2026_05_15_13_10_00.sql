START TRANSACTION;

-- Workwear quotes: frontend order labels (fresh install — INSERT only, no Peter fulfilment/VAT labels).
-- Uses ::LOCALE:: so the installer inserts one row per active locale.

-- Order list / navigation
INSERT INTO `plugin_base_fields` VALUES (NULL, 'front_orders_history', 'frontend', 'Order history', 'script', '2026-05-15 13:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order history', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'front_back_to_orders', 'frontend', 'Back to orders', 'script', '2026-05-15 13:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Back to orders', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'front_orders_not_found', 'frontend', 'Orders not found', 'script', '2026-05-15 13:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Orders not found', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'front_order_not_found', 'frontend', 'Order not found', 'script', '2026-05-15 13:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order not found!', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'front_order_login_required', 'frontend', 'Login required to view orders', 'script', '2026-05-15 13:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please log in to view your orders.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'front_view_order_details', 'frontend', 'View order details', 'script', '2026-05-15 13:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'View order details', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'front_order_pay_order', 'frontend', 'Pay now', 'script', '2026-05-15 13:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Pay now', 'script');

-- Order table columns
INSERT INTO `plugin_base_fields` VALUES (NULL, 'front_quote_address', 'frontend', 'Quote address', 'script', '2026-05-15 13:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Quote address', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'front_order_payment_status', 'frontend', 'Payment status', 'script', '2026-05-15 13:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Payment status', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'front_payment_status_pending', 'frontend', 'Payment status: Pending', 'script', '2026-05-15 13:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Pending', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'front_payment_status_pay_later', 'frontend', 'Payment status: Pay later', 'script', '2026-05-15 13:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Pay later', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'front_payment_status_paid', 'frontend', 'Payment status: Paid', 'script', '2026-05-15 13:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Paid', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'front_payment_status_failed', 'frontend', 'Payment status: Failed', 'script', '2026-05-15 13:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Failed', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'front_payment_status_refunded', 'frontend', 'Payment status: Refunded', 'script', '2026-05-15 13:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Refunded', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'front_payment_status_unknown', 'frontend', 'Payment status: Unknown', 'script', '2026-05-15 13:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Unknown', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'front_print_invoice', 'frontend', 'Download invoice', 'script', '2026-05-15 13:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Download invoice', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'front_order_number', 'frontend', 'Order number', 'script', '2026-05-15 13:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order number', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'front_order_created', 'frontend', 'Created', 'script', '2026-05-15 13:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Created', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'front_order_tab_shipping_details', 'frontend', 'Quote address', 'script', '2026-05-15 13:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Quote address', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'front_order_tab_billing_details', 'frontend', 'Billing details', 'script', '2026-05-15 13:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Billing details', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'front_order_total', 'frontend', 'Total', 'script', '2026-05-15 13:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Total', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'front_order_status', 'frontend', 'Status', 'script', '2026-05-15 13:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Status', 'script');

-- Order detail extras
INSERT INTO `plugin_base_fields` VALUES (NULL, 'front_order_voucher', 'frontend', 'Discount code', 'script', '2026-05-15 13:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Discount code', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'front_order_notes', 'frontend', 'Notes', 'script', '2026-05-15 13:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Notes', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'front_custom_fields', 'frontend', 'Additional details', 'script', '2026-05-15 13:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Additional details', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'front_digital_copied', 'frontend', 'Copied', 'script', '2026-05-15 13:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Copied!', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'front_digital_label_count', 'frontend', 'Download count', 'script', '2026-05-15 13:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Download count', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'front_digital_label_until', 'frontend', 'Download until', 'script', '2026-05-15 13:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Download until', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'front_digital_label_last', 'frontend', 'Last download', 'script', '2026-05-15 13:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Last download', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'front_badge_digital', 'frontend', 'Digital product', 'script', '2026-05-15 13:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Digital product', 'script');

COMMIT;
