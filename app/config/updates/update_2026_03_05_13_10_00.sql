START TRANSACTION;

DROP TABLE IF EXISTS `quotes`;
CREATE TABLE IF NOT EXISTS `quotes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `company_id` int(10) unsigned DEFAULT NULL,
  `uuid` varchar(12) DEFAULT NULL,
  `client_id` int(10) unsigned DEFAULT NULL,
  `address_id` int(10) unsigned DEFAULT NULL,
  `locale_id` int(10) unsigned DEFAULT NULL,
  `tax_id` int(10) unsigned DEFAULT NULL,
  `status` enum('new','pending','cancelled','completed') DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `txn_id` varchar(255) DEFAULT NULL,
  `processed_on` datetime DEFAULT NULL,
  `price` decimal(9,2) unsigned DEFAULT NULL,
  `discount` decimal(9,2) unsigned DEFAULT NULL,
  `insurance` decimal(9,2) unsigned DEFAULT NULL,
  `shipping` decimal(9,2) unsigned DEFAULT NULL,
  `tax` decimal(9,2) unsigned DEFAULT NULL,
  `total` decimal(9,2) unsigned DEFAULT NULL,
  `voucher` varchar(255) DEFAULT NULL,
  `notes` text,
  `cc_type` blob,
  `cc_num` blob,
  `cc_exp_month` blob,
  `cc_exp_year` blob,
  `cc_code` blob,
  `created` datetime DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `same_as` tinyint(1) unsigned DEFAULT '0',
  `s_name` varchar(255) DEFAULT NULL,
  `s_country_id` int(10) unsigned DEFAULT NULL,
  `s_state` varchar(255) DEFAULT NULL,
  `s_city` varchar(255) DEFAULT NULL,
  `s_zip` varchar(255) DEFAULT NULL,
  `s_address_1` varchar(255) DEFAULT NULL,
  `s_address_2` varchar(255) DEFAULT NULL,
  `b_name` varchar(255) DEFAULT NULL,
  `b_country_id` int(10) unsigned DEFAULT NULL,
  `b_state` varchar(255) DEFAULT NULL,
  `b_city` varchar(255) DEFAULT NULL,
  `b_zip` varchar(255) DEFAULT NULL,
  `b_address_1` varchar(255) DEFAULT NULL,
  `b_address_2` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `client_id` (`client_id`),
  KEY `address_id` (`address_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `quotes_extras`;
CREATE TABLE IF NOT EXISTS `quotes_extras` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `company_id` int(10) unsigned DEFAULT NULL,
  `quote_id` int(10) unsigned DEFAULT NULL,
  `quote_stock_id` int(10) unsigned DEFAULT NULL,
  `extra_id` int(10) unsigned DEFAULT NULL,
  `extra_item_id` int(10) unsigned DEFAULT NULL,
  `price` decimal(9,2) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quote_id` (`quote_id`),
  KEY `quote_stock_id` (`quote_stock_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `quotes_stocks`;
CREATE TABLE IF NOT EXISTS `quotes_stocks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `quote_id` int(10) unsigned DEFAULT NULL,
   `company_id` int(10) unsigned DEFAULT NULL,
  `stock_id` int(10) unsigned DEFAULT NULL,
  `product_id` int(10) unsigned DEFAULT NULL,
  `price` decimal(9,2) unsigned DEFAULT NULL,
  `qty` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quote_id` (`quote_id`),
  KEY `stock_id` (`stock_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoUpdateQuoteTitle', 'backend', 'infoUpdateQuoteTitle', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Update Quote', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoUpdateQuoteDesc', 'backend', 'infoUpdateQuoteDesc', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Edit quote details and click on the ''Save'' button to update it.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AQ10', 'arrays', 'error_titles_ARRAY_AQ10', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Quote details', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AQ11', 'arrays', 'error_titles_ARRAY_AQ11', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Client details', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AQ12', 'arrays', 'error_titles_ARRAY_AQ12', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Shipping and billing details', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AQ10', 'arrays', 'error_titles_ARRAY_AQ10', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can change quote details using the form below. If you add or remove a product click on the \"Recalculate the price\" button to calculate new price based on the new selection. At the bottom of the page you can view the invoice for the quote and create new one if needed.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AQ11', 'arrays', 'error_titles_ARRAY_AQ11', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Find information about your client and their previous purchases.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AQ12', 'arrays', 'error_titles_ARRAY_AQ12', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Below you can see shipping and billing details for the quote. Using your client''s address book you can easily change these details.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AQ24', 'arrays', 'error_titles_ARRAY_AQ24', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Shipping & Tax fee', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AQ24', 'arrays', 'error_titles_ARRAY_AQ24', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can add unlimited amount of locations. For each location you can enter shipping and tax fees. When clients purchase something they will select their location and the shipping and tax fees will be added to the quote total amount.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AOQ05', 'arrays', 'error_titles_ARRAY_AOQ05', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Quote updated', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AOQ05', 'arrays', 'error_bodies_ARRAY_AOQ05', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Quote has been updated', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AOQ08', 'arrays', 'error_titles_ARRAY_AOQ08', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Quote not found.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AOQ08', 'arrays', 'error_bodies_ARRAY_AOQ08', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Quote your''re looking for is missing.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AOQ07', 'arrays', 'error_titles_ARRAY_AOQ07', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add a Stock', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AOQ07', 'arrays', 'error_bodies_ARRAY_AOQ07', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'In the below table are listed all current stocks for selected product. After chosing desired stock(s) click on Add button.', 'script');



INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_send_confirm', 'backend', 'Quote / Send quote', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send quote', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_shipping_location', 'backend', 'Quote / Shipping location', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Shipping location', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'locale_quote', 'backend', 'Locale / Quote', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Quote', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'menuQuotes', 'backend', 'Menu Quotes', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Quotes', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'menu_quotes', 'backend', 'menu_quotes', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Quotes', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'client_quotes', 'backend', 'client_quotes', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Total quotes', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'client_last_quote', 'backend', 'client_last_quote', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Last quote', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_index', 'backend', 'quote_index', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Quotes', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_create', 'backend', 'quote_create', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add quote', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_update', 'backend', 'quote_update', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update quote', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_uuid', 'backend', 'quote_uuid', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Quote number', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_status', 'backend', 'quote_status', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Status', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_price', 'backend', 'quote_price', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Price', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_shipping', 'backend', 'quote_shipping', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Shipping', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_tax', 'backend', 'quote_tax', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Tax', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_discount', 'backend', 'quote_discount', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Discount', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_total', 'backend', 'quote_total', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Total', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_voucher', 'backend', 'quote_voucher', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Voucher', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_payment', 'backend', 'quote_payment', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Payment', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_products', 'backend', 'quote_products', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Products', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_client', 'backend', 'quote_client', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Client', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_address', 'backend', 'quote_address', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Address', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_email', 'backend', 'quote_email', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'E-Mail address', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_phone', 'backend', 'quote_phone', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Phone', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_url', 'backend', 'quote_url', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Website', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_created', 'backend', 'quote_created', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Created', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_country', 'backend', 'quote_country', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Country', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_state', 'backend', 'quote_state', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'State', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_city', 'backend', 'quote_city', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'City', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_zip', 'backend', 'quote_zip', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Zip', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_address_1', 'backend', 'quote_address_1', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Address line 1', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_address_2', 'backend', 'quote_address_2', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Address line 2', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_name', 'backend', 'quote_name', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Name', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_notes', 'backend', 'quote_notes', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Notes', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_empty', 'backend', 'quote_empty', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No quotes found', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_general', 'backend', 'quote_general', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Quote details', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_choose', 'backend', 'quote_choose', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '-- Choose --', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_del_product', 'backend', 'quote_del_product', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'delete', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_del_title', 'backend', 'quote_del_title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Confirm quote delete', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_del_body', 'backend', 'quote_del_body', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Are you sure you want to delete selected quote?', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_customer', 'backend', 'quote_customer', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Customer info', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_shipping_details', 'backend', 'quote_shipping_details', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Shipping details', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_billing_details', 'backend', 'quote_billing_details', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Billing details', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_copy_s', 'backend', 'quote_copy_s', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Use for Shipping address', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_copy_b', 'backend', 'quote_copy_b', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Use for Billing address', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_same', 'backend', 'quote_same', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Same as Billing details', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_p_name', 'backend', 'quote_p_name', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Product', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_p_price', 'backend', 'quote_p_price', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Price', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_p_qty', 'backend', 'quote_p_qty', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Qty', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_p_attr', 'backend', 'quote_p_attr', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Attributes', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_p_sku', 'backend', 'quote_p_sku', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'ID', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_statuses_ARRAY_new', 'arrays', 'quote_statuses_ARRAY_new', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'New', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_statuses_ARRAY_pending', 'arrays', 'quote_statuses_ARRAY_pending', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Pending', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_statuses_ARRAY_cancelled', 'arrays', 'quote_statuses_ARRAY_cancelled', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Cancelled', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_statuses_ARRAY_completed', 'arrays', 'quote_statuses_ARRAY_completed', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Completed', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_payments_ARRAY_paypal', 'arrays', 'quote_payments_ARRAY_paypal', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'PayPal', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_payments_ARRAY_authorize', 'arrays', 'quote_payments_ARRAY_authorize', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Authorize.NET', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_payments_ARRAY_creditcard', 'arrays', 'quote_payments_ARRAY_creditcard', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Credit Card', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AQ01', 'arrays', 'errors_ARRAY_AQ01', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Quote has been added', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AQ02', 'arrays', 'errors_ARRAY_AQ02', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Quote has not been added', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AQ03', 'arrays', 'errors_ARRAY_AQ03', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Quote has been deleted', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AQ04', 'arrays', 'errors_ARRAY_AQ04', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Quote has not been deleted', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AQ05', 'arrays', 'errors_ARRAY_AQ05', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Quote has been updated', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AQ06', 'arrays', 'errors_ARRAY_AQ06', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Quote has not been updated', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AQ08', 'arrays', 'errors_ARRAY_AQ08', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Quote doesn''t exists', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_payment_details', 'backend', 'quote_payment_details', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Payment details', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_other_details', 'backend', 'Checkout / Other details', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Other details', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_insurance', 'backend', 'quote_insurance', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Insurance', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_p_subtotal', 'backend', 'quote_p_subtotal', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'SubTotal', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_invoice_details', 'backend', 'quote_invoice_details', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Invoice details', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_create_invoice', 'backend', 'quote_create_invoice', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Create Invoice', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_find_invoices', 'backend', 'quote_find_invoices', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Find Invoices', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'front_edit_quote', 'frontend', 'Edit quote', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Edit quote', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_tab_quote', 'backend', 'quote_tab_quote', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Quote', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_tab_client', 'backend', 'quote_tab_client', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Client', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_tab_shipping', 'backend', 'quote_tab_shipping', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Shipping & Billing', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoQuotesTitle', 'backend', 'Infobox / List of quotes', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'List of quotes', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoQuotesDesc', 'backend', 'Infobox / List of quotes', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can find below the list of quotes made. Click on the pencil icon on the corresponding entry to view more details of a specific quote.', 'script');

COMMIT;