START TRANSACTION;

-- Invoice plugin tables (pjInvoice).
-- Table names WITHOUT shopping_cart_ prefix: the installer adds PJ_SCRIPT_PREFIX automatically
-- (e.g. plugin_invoice -> shopping_cart_plugin_invoice in the database).

CREATE TABLE IF NOT EXISTS `plugin_invoice` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) DEFAULT NULL,
  `order_id` varchar(12) DEFAULT NULL,
  `foreign_id` int(10) unsigned DEFAULT NULL,
  `locale_id` int(10) unsigned DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `status` enum('not_paid','paid','cancelled') DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `cc_type` blob DEFAULT NULL,
  `cc_num` blob DEFAULT NULL,
  `cc_exp_month` blob DEFAULT NULL,
  `cc_exp_year` blob DEFAULT NULL,
  `cc_code` blob DEFAULT NULL,
  `txn_id` varchar(255) DEFAULT NULL,
  `processed_on` datetime DEFAULT NULL,
  `subtotal` decimal(9,2) unsigned DEFAULT NULL,
  `discount` decimal(9,2) unsigned DEFAULT NULL,
  `tax` decimal(9,2) unsigned DEFAULT NULL,
  `shipping` decimal(9,2) unsigned DEFAULT NULL,
  `total` decimal(9,2) unsigned DEFAULT NULL,
  `paid_deposit` decimal(9,2) unsigned DEFAULT NULL,
  `amount_due` decimal(9,2) unsigned DEFAULT NULL,
  `currency` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `y_logo` varchar(255) DEFAULT NULL,
  `y_company` varchar(255) DEFAULT NULL,
  `y_name` varchar(255) DEFAULT NULL,
  `y_street_address` varchar(255) DEFAULT NULL,
  `y_country` int(10) DEFAULT NULL,
  `y_city` varchar(255) DEFAULT NULL,
  `y_state` varchar(255) DEFAULT NULL,
  `y_zip` varchar(255) DEFAULT NULL,
  `y_phone` varchar(255) DEFAULT NULL,
  `y_fax` varchar(255) DEFAULT NULL,
  `y_email` varchar(255) DEFAULT NULL,
  `y_url` varchar(255) DEFAULT NULL,
  `b_billing_address` varchar(255) DEFAULT NULL,
  `b_company` varchar(255) DEFAULT NULL,
  `b_name` varchar(255) DEFAULT NULL,
  `b_address` varchar(255) DEFAULT NULL,
  `b_street_address` varchar(255) DEFAULT NULL,
  `b_country` int(10) DEFAULT NULL,
  `b_city` varchar(255) DEFAULT NULL,
  `b_state` varchar(255) DEFAULT NULL,
  `b_zip` varchar(255) DEFAULT NULL,
  `b_phone` varchar(255) DEFAULT NULL,
  `b_fax` varchar(255) DEFAULT NULL,
  `b_email` varchar(255) DEFAULT NULL,
  `b_url` varchar(255) DEFAULT NULL,
  `s_shipping_address` varchar(255) DEFAULT NULL,
  `s_company` varchar(255) DEFAULT NULL,
  `s_name` varchar(255) DEFAULT NULL,
  `s_address` varchar(255) DEFAULT NULL,
  `s_street_address` varchar(255) DEFAULT NULL,
  `s_country` int(10) DEFAULT NULL,
  `s_city` varchar(255) DEFAULT NULL,
  `s_state` varchar(255) DEFAULT NULL,
  `s_zip` varchar(255) DEFAULT NULL,
  `s_phone` varchar(255) DEFAULT NULL,
  `s_fax` varchar(255) DEFAULT NULL,
  `s_email` varchar(255) DEFAULT NULL,
  `s_url` varchar(255) DEFAULT NULL,
  `s_date` date DEFAULT NULL,
  `s_terms` text DEFAULT NULL,
  `s_is_shipped` tinyint(1) unsigned DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `order_id` (`order_id`),
  KEY `foreign_id` (`foreign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_invoice_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(255) DEFAULT NULL,
  `invoice_number_digits` tinyint(2) unsigned NOT NULL DEFAULT 5,
  `invoice_number_reset_yearly` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `y_logo` varchar(255) DEFAULT NULL,
  `y_email_logo` varchar(255) DEFAULT NULL,
  `y_country` int(10) DEFAULT NULL,
  `y_zip` varchar(255) DEFAULT NULL,
  `y_phone` varchar(255) DEFAULT NULL,
  `y_fax` varchar(255) DEFAULT NULL,
  `y_email` varchar(255) DEFAULT NULL,
  `y_url` varchar(255) DEFAULT NULL,
  `y_vat_number` varchar(50) DEFAULT NULL,
  `y_coc_number` varchar(20) DEFAULT NULL,
  `y_bank_iban` varchar(50) DEFAULT NULL,
  `y_bank_name` varchar(100) DEFAULT NULL,
  `p_accept_payments` tinyint(1) unsigned DEFAULT 0,
  `p_accept_paypal` tinyint(1) unsigned DEFAULT 0,
  `p_accept_authorize` tinyint(1) unsigned DEFAULT 0,
  `p_accept_creditcard` tinyint(1) unsigned DEFAULT 0,
  `p_accept_mollie` tinyint(1) unsigned DEFAULT 1,
  `p_accept_cash` tinyint(1) unsigned DEFAULT 0,
  `p_accept_bank` tinyint(1) unsigned DEFAULT 0,
  `p_mollie_address` varchar(255) DEFAULT NULL,
  `p_authorize_tz` varchar(255) DEFAULT NULL,
  `p_authorize_key` varchar(255) DEFAULT NULL,
  `p_authorize_mid` varchar(255) DEFAULT NULL,
  `p_authorize_hash` varchar(255) DEFAULT NULL,
  `si_include` tinyint(1) unsigned DEFAULT 0,
  `si_shipping_address` tinyint(1) unsigned DEFAULT 0,
  `si_company` tinyint(1) unsigned DEFAULT 0,
  `si_name` tinyint(1) unsigned DEFAULT 0,
  `si_address` tinyint(1) unsigned DEFAULT 0,
  `si_street_address` tinyint(1) unsigned DEFAULT 0,
  `si_city` tinyint(1) unsigned DEFAULT 0,
  `si_state` tinyint(1) unsigned DEFAULT 0,
  `si_zip` tinyint(1) unsigned DEFAULT 0,
  `si_phone` tinyint(1) unsigned DEFAULT 0,
  `si_fax` tinyint(1) unsigned DEFAULT 0,
  `si_email` tinyint(1) unsigned DEFAULT 0,
  `si_url` tinyint(1) unsigned DEFAULT 0,
  `si_date` tinyint(1) unsigned DEFAULT 0,
  `si_terms` tinyint(1) unsigned DEFAULT 0,
  `si_is_shipped` tinyint(1) unsigned DEFAULT 0,
  `si_shipping` tinyint(1) unsigned DEFAULT 0,
  `o_booking_url` varchar(255) DEFAULT NULL,
  `o_qty_is_int` tinyint(1) unsigned DEFAULT 0,
  `o_use_qty_unit_price` tinyint(1) unsigned DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_invoice_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` int(10) unsigned DEFAULT NULL,
  `tmp` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` tinytext DEFAULT NULL,
  `qty` decimal(9,2) DEFAULT NULL,
  `unit_price` decimal(9,2) unsigned DEFAULT NULL,
  `amount` decimal(9,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `plugin_invoice_config` (`id`, `invoice_number`, `invoice_number_digits`, `invoice_number_reset_yearly`)
SELECT 1, '1', 5, 0 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_invoice_config` WHERE `id` = 1);

-- Default invoice PDF template (Workwear quotes — single tax line, no Peter VAT/pickup fields)
INSERT INTO `plugin_base_multi_lang` (`foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT 1, 'pjInvoiceConfig', ::LOCALE::, 'y_template',
'<table style="width:100%;" border="0"><tbody><tr><td style="width:50%;">{y_logo}</td><td>&nbsp;</td></tr><tr><td><p><strong>{y_company}</strong></p><p>{y_street_address}<br />{y_zip} {y_city}<br />{y_country}<br /><br />Invoice number: {uuid}<br />Invoice date: {issue_date}<br />Quote number: {order_id}<br />Payment status: {status}</p></td><td>&nbsp;</td></tr></tbody></table><p>&nbsp;</p><table style="width:100%;"><tbody><tr><td style="width:50%;"><strong>Billing address</strong></td><td style="width:50%;"><strong>Quote address</strong></td></tr><tr><td><strong>{b_name}</strong><br />{b_company}<br />{b_address}<br />{b_zip} {b_city}<br />{b_country}</td><td><strong>{s_name}</strong><br />{s_company}<br />{s_address}<br />{s_zip} {s_city}<br />{s_country}</td></tr></tbody></table><p>&nbsp;</p>{items}<p>&nbsp;</p><table style="width:100%;"><tbody><tr><td style="text-align:right;">Discount:</td><td style="text-align:right;">{discount}</td></tr><tr><td style="text-align:right;">Shipping:</td><td style="text-align:right;">{shipping}</td></tr><tr><td style="text-align:right;">Tax:</td><td style="text-align:right;">{tax}</td></tr><tr><td style="text-align:right;"><strong>Total:</strong></td><td style="text-align:right;"><strong>{total}</strong></td></tr></tbody></table><p>{notes}</p>',
'script'
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `plugin_base_multi_lang` ml
    WHERE ml.`model` = 'pjInvoiceConfig' AND ml.`foreign_id` = 1
      AND ml.`field` = 'y_template' AND ml.`locale` = ::LOCALE::
      AND ml.`content` IS NOT NULL AND TRIM(ml.`content`) != ''
);

COMMIT;
