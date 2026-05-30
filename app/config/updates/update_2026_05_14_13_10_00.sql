START TRANSACTION;

-- Product edit (pjActionUpdate) — new copy for Peter-style section hints (run once; skip if keys already exist in `fields`).

-- Photos tab — short lead under the section heading (gallery lives below).
INSERT INTO `fields` VALUES (NULL, 'lblProductEditPhotosLead', 'backend', 'Label / Product edit / Photos section lead', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Upload, reorder, and remove images in the gallery below.', 'script');

-- Similar tab — hint under the green info box (search field uses existing btnSearch placeholder).
INSERT INTO `fields` VALUES (NULL, 'lblProductSimilarSearchHelp', 'backend', 'Label / Product edit / Similar products search hint', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Type at least two characters, then choose a product from the list to link it as similar.', 'script');

-- Dashboard (pjAdmin / pjActionIndex) — period filters & compare labels (arrays → __('dashboard_filter', true), __('dashboard_compare', true))

INSERT INTO `fields` VALUES (NULL, 'dashboard_filter_ARRAY_today', 'arrays', 'Dashboard / Filter / Today', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Today', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_filter_ARRAY_week', 'arrays', 'Dashboard / Filter / Last 7 days', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Last 7 days', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_filter_ARRAY_month', 'arrays', 'Dashboard / Filter / This month', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'This month', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_filter_ARRAY_year', 'arrays', 'Dashboard / Filter / 12 months', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', '12 months', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_compare_ARRAY_today', 'arrays', 'Dashboard / Compare / Yesterday', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'yesterday', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_compare_ARRAY_week', 'arrays', 'Dashboard / Compare / Previous 7 days', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'previous 7 days', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_compare_ARRAY_month', 'arrays', 'Dashboard / Compare / Previous month', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'previous month', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_compare_ARRAY_year', 'arrays', 'Dashboard / Compare / Previous 12 months', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'previous 12 months', 'script');

-- Payment status (order list on dashboard)
INSERT INTO `fields` VALUES (NULL, 'dashboard_payment_status_ARRAY_pending', 'arrays', 'Dashboard / Payment status / Pending', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Pending', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_payment_status_ARRAY_pay_later', 'arrays', 'Dashboard / Payment status / Pay later', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Pay later', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_payment_status_ARRAY_paid', 'arrays', 'Dashboard / Payment status / Paid', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Paid', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_payment_status_ARRAY_failed', 'arrays', 'Dashboard / Payment status / Failed', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Failed', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_payment_status_ARRAY_refunded', 'arrays', 'Dashboard / Payment status / Refunded', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Refunded', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_payment_status_ARRAY_unknown', 'arrays', 'Dashboard / Payment status / Unknown', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Unknown', 'script');

-- KPI & chart copy
INSERT INTO `fields` VALUES (NULL, 'dashboard_kpi_revenue', 'backend', 'Dashboard / KPI / Revenue', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Revenue', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_vs_compare', 'backend', 'Dashboard / KPI / vs prefix', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'vs.', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_chart_revenue', 'backend', 'Dashboard / Chart / Revenue series', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Revenue', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_chart_previous_period', 'backend', 'Dashboard / Chart / Previous period series', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Previous period', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_chart_orders', 'backend', 'Dashboard / Chart / Orders series', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Orders', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_chart_orders_by_day', 'backend', 'Dashboard / Chart / Orders by day title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Orders by day', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_chart_orders_by_month', 'backend', 'Dashboard / Chart / Orders by month title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Orders by month', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_chart_revenue_overview', 'backend', 'Dashboard / Chart / Revenue overview title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Revenue overview', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_chart_order_status', 'backend', 'Dashboard / Chart / Order status title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Order status', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_chart_top_products', 'backend', 'Dashboard / Chart / Top products title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Top products', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_link_all_products', 'backend', 'Dashboard / Link / All products', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'All products →', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_link_all_orders', 'backend', 'Dashboard / Link / All orders', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'All orders →', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_empty_products_period', 'backend', 'Dashboard / Empty / No products in period', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'No products ordered in this period', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_empty_orders', 'backend', 'Dashboard / Empty / No orders', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'No orders yet', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_empty_chart_data', 'backend', 'Dashboard / Empty / Chart no data', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'No data', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_total_revenue_all_time', 'backend', 'Dashboard / Quick stat / Total revenue all time', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Total revenue (all time)', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_top_product_fallback', 'backend', 'Dashboard / Top product name fallback', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Product #%s', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_donut_orders_one', 'backend', 'Dashboard / Donut centre / One order', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', '%d order', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_donut_orders_many', 'backend', 'Dashboard / Donut centre / Many orders', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', '%d orders', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_tooltip_order', 'backend', 'Dashboard / Chart tooltip / One order', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', '%d order', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_tooltip_orders', 'backend', 'Dashboard / Chart tooltip / Many orders', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', '%d orders', 'script');

-- Widget panel
INSERT INTO `fields` VALUES (NULL, 'dashboard_widget_toggle_title', 'backend', 'Dashboard / Widgets / Toggle button title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Show or hide widgets', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_widgets_heading', 'backend', 'Dashboard / Widgets / Panel heading', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Widgets', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_widgets_drag_hint', 'backend', 'Dashboard / Widgets / Drag hint', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', '↕ drag to reorder', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_widget_kpi', 'backend', 'Dashboard / Widgets / KPI cards', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'KPI cards', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_widget_charts', 'backend', 'Dashboard / Widgets / Charts', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Charts', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_widget_top_products', 'backend', 'Dashboard / Widgets / Top products', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Top products', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_widget_schedule', 'backend', 'Dashboard / Widgets / Today schedule', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Today''s schedule', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_widget_quick_stats', 'backend', 'Dashboard / Widgets / Quick stats', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Quick stats', 'script');

INSERT INTO `fields` VALUES (NULL, 'dashboard_widget_recent_orders', 'backend', 'Dashboard / Widgets / Recent orders', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Recent orders', 'script');

COMMIT;
