DROP TABLE IF EXISTS `shopping_cart_addresses`;
CREATE TABLE IF NOT EXISTS `shopping_cart_addresses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(10) unsigned DEFAULT NULL,
  `country_id` int(10) unsigned DEFAULT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `zip` varchar(255) DEFAULT NULL,
  `address_1` varchar(255) DEFAULT NULL,
  `address_2` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `is_default_shipping` tinyint(1) unsigned DEFAULT '0',
  `is_default_billing` tinyint(1) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `country_id` (`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `shopping_cart_attributes`;
CREATE TABLE IF NOT EXISTS `shopping_cart_attributes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `company_id` int(10) unsigned DEFAULT NULL,
  `product_id` int(10) unsigned DEFAULT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `order_group` int(10) DEFAULT NULL,
  `order_item` int(10) DEFAULT NULL,
  `hash` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `parent_id` (`parent_id`),
  KEY `hash` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `shopping_cart_carts`;
CREATE TABLE IF NOT EXISTS `shopping_cart_carts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `company_id` int(10) unsigned DEFAULT NULL,
  `stock_id` int(10) unsigned DEFAULT NULL,
  `product_id` int(10) unsigned DEFAULT NULL,
  `hash` varchar(32) DEFAULT NULL,
  `key_data` text,
  `qty` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_id` (`stock_id`),
  KEY `hash` (`hash`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `shopping_cart_categories`;
CREATE TABLE IF NOT EXISTS `shopping_cart_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned DEFAULT NULL,
   `company_id` int(10) unsigned DEFAULT NULL,
  `lft` int(10) unsigned DEFAULT NULL,
  `rgt` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `shopping_cart_categories`(`id`,`parent_id`,`lft`,`rgt`,`name`) values (1,0,1,2,'Products');

DROP TABLE IF EXISTS `shopping_cart_clients`;
CREATE TABLE IF NOT EXISTS `shopping_cart_clients` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
   `company_id` int(10) unsigned DEFAULT NULL,      
    `email` varchar(255) DEFAULT NULL,                   
    `password` blob,                                     
    `client_name` varchar(255) DEFAULT NULL,             
    `phone` varchar(255) DEFAULT NULL,                   
    `url` varchar(255) DEFAULT NULL,                     
    `created` datetime DEFAULT NULL,                     
    `last_login` datetime DEFAULT NULL,                  
    `status` enum('T','F') NOT NULL DEFAULT 'T',         
    PRIMARY KEY (`id`),                                  
    UNIQUE KEY `email` (`email`) 
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `shopping_cart_extras`;
CREATE TABLE IF NOT EXISTS `shopping_cart_extras` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `company_id` int(10) unsigned DEFAULT NULL,
  `product_id` int(10) unsigned DEFAULT NULL,
  `type` enum('single','multi') DEFAULT 'single',
  `price` decimal(9,2) unsigned DEFAULT NULL,
  `is_mandatory` tinyint(1) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `shopping_cart_extras_items`;
CREATE TABLE IF NOT EXISTS `shopping_cart_extras_items` (
   `company_id` int(10) unsigned DEFAULT NULL,
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `extra_id` int(10) unsigned DEFAULT NULL,
  `price` decimal(9,2) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `extra_id` (`extra_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `shopping_cart_history`;
CREATE TABLE IF NOT EXISTS `shopping_cart_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `company_id` int(10) unsigned DEFAULT NULL,
  `record_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) DEFAULT NULL,
  `table_name` varchar(255) DEFAULT NULL,
  `before` longtext,
  `after` longtext,
  `ip` varchar(15) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `record_id` (`record_id`),
  KEY `user_id` (`user_id`),
  KEY `table_name` (`table_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `shopping_cart_orders`;
CREATE TABLE IF NOT EXISTS `shopping_cart_orders` (
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

DROP TABLE IF EXISTS `shopping_cart_orders_extras`;
CREATE TABLE IF NOT EXISTS `shopping_cart_orders_extras` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `company_id` int(10) unsigned DEFAULT NULL,
  `order_id` int(10) unsigned DEFAULT NULL,
  `order_stock_id` int(10) unsigned DEFAULT NULL,
  `extra_id` int(10) unsigned DEFAULT NULL,
  `extra_item_id` int(10) unsigned DEFAULT NULL,
  `price` decimal(9,2) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `order_stock_id` (`order_stock_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `shopping_cart_orders_stocks`;
CREATE TABLE IF NOT EXISTS `shopping_cart_orders_stocks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned DEFAULT NULL,
   `company_id` int(10) unsigned DEFAULT NULL,
  `stock_id` int(10) unsigned DEFAULT NULL,
  `product_id` int(10) unsigned DEFAULT NULL,
  `price` decimal(9,2) unsigned DEFAULT NULL,
  `qty` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `stock_id` (`stock_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `shopping_cart_products`;
CREATE TABLE IF NOT EXISTS `shopping_cart_products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `company_id` int(10) unsigned DEFAULT NULL,
  `sku` varchar(255) DEFAULT NULL,
  `status` tinyint(3) unsigned DEFAULT NULL,
  `digital_file` varchar(255) DEFAULT NULL,
  `digital_name` varchar(255) DEFAULT NULL,
  `digital_expire` time DEFAULT NULL,
  `is_featured` tinyint(1) unsigned DEFAULT '0',
  `is_digital` tinyint(1) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `shopping_cart_products_categories`;
CREATE TABLE IF NOT EXISTS `shopping_cart_products_categories` (
  `product_id` int(10) unsigned NOT NULL DEFAULT '0',
   `company_id` int(10) unsigned DEFAULT NULL,
  `category_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`product_id`,`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `shopping_cart_products_similar`;
CREATE TABLE IF NOT EXISTS `shopping_cart_products_similar` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `company_id` int(10) unsigned DEFAULT NULL,
  `product_id` int(10) unsigned DEFAULT NULL,
  `similar_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_id` (`product_id`,`similar_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `shopping_cart_stocks`;
CREATE TABLE IF NOT EXISTS `shopping_cart_stocks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned DEFAULT NULL,
  `image_id` int(10) unsigned DEFAULT NULL,
   `company_id` int(10) unsigned DEFAULT NULL,
  `qty` int(10) unsigned DEFAULT NULL,
  `price` decimal(9,2) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `image_id` (`image_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `shopping_cart_stocks_attributes`;
CREATE TABLE IF NOT EXISTS `shopping_cart_stocks_attributes` (
  `stock_id` int(10) unsigned DEFAULT NULL,
   `company_id` int(10) unsigned DEFAULT NULL,
  `product_id` int(10) unsigned DEFAULT NULL,
  `attribute_id` int(10) unsigned DEFAULT NULL,
  `attribute_parent_id` int(10) unsigned DEFAULT NULL,
  KEY `stock_id` (`stock_id`),
  KEY `product_id` (`product_id`),
  KEY `attribute_id` (`attribute_id`),
  KEY `attribute_parent_id` (`attribute_parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `shopping_cart_taxes`;
CREATE TABLE IF NOT EXISTS `shopping_cart_taxes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `company_id` int(10) unsigned DEFAULT NULL,
  `shipping` decimal(9,2) unsigned DEFAULT NULL,
  `free` decimal(9,2) unsigned DEFAULT NULL,
  `tax` decimal(9,2) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `shopping_cart_vouchers`;
CREATE TABLE IF NOT EXISTS `shopping_cart_vouchers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `company_id` int(10) unsigned DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `type` enum('amount','percent') DEFAULT NULL,
  `apply` enum('total','each') DEFAULT 'each',
  `discount` decimal(9,2) unsigned DEFAULT NULL,
  `valid` enum('fixed','period','recurring') DEFAULT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `time_from` time DEFAULT NULL,
  `time_to` time DEFAULT NULL,
  `every` enum('monday','tuesday','wednesday','thursday','friday','saturday','sunday') DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `shopping_cart_vouchers_products`;
CREATE TABLE IF NOT EXISTS `shopping_cart_vouchers_products` (
  `voucher_id` int(10) unsigned NOT NULL,
   `company_id` int(10) unsigned DEFAULT NULL,
  `product_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`voucher_id`,`product_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `shopping_cart_options`;
CREATE TABLE IF NOT EXISTS `shopping_cart_options` (
  `foreign_id` int(10) unsigned NOT NULL DEFAULT '0',
   `company_id` int(10) unsigned DEFAULT NULL,
  `key` varchar(255) NOT NULL DEFAULT '',
  `tab_id` tinyint(3) unsigned DEFAULT NULL,
  `value` text,
  `label` text,
  `type` enum('string','text','int','float','enum','bool') NOT NULL DEFAULT 'string',
  `order` int(10) unsigned DEFAULT NULL,
  `is_visible` tinyint(1) unsigned DEFAULT '1',
  `style` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`foreign_id`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `shopping_cart_notifications`;
CREATE TABLE IF NOT EXISTS `shopping_cart_notifications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `company_id` int(10) unsigned DEFAULT NULL,
  `recipient` enum('client','admin') DEFAULT NULL,
  `transport` enum('email','sms') DEFAULT NULL,
  `variant` varchar(30) DEFAULT NULL,
  `is_active` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`),
  -- UNIQUE KEY `recipient` (`recipient`,`transport`,`variant`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
INSERT IGNORE INTO `shopping_cart_notifications`
(`id`, `company_id`, `recipient`, `transport`, `variant`, `is_active`) VALUES
(1, 1, 'client', 'email', 'confirmation', 1),
(2, 1, 'client', 'email', 'payment', 1),
(3, 1, 'client', 'email', 'cancel', 1),
(4, 1, 'client', 'email', 'account', 1),
(5, 1, 'client', 'email', 'forgot', 1),
(6, 1, 'client', 'email', 'send_to_friend', 1),
(7, 1, 'admin', 'email', 'confirmation', 1),
(8, 1, 'admin', 'email', 'payment', 1),
(9, 1, 'admin', 'email', 'cancel', 1),
(10, 1, 'client', 'sms', 'confirmation', 1),
(11, 1, 'client', 'sms', 'payment', 1),
(12, 1, 'admin', 'sms', 'confirmation', 1),
(13, 1, 'admin', 'sms', 'payment', 1);

INSERT INTO `shopping_cart_plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`) VALUES
(NULL, 1, 'pjNotification', 1, 'confirm_subject_client', 'Order confirmation', 'data'),
(NULL, 1, 'pjNotification', 1, 'confirm_tokens_client', 'Dear {ClientName},<br /><br />thank you for your order. Your order number is: {OrderUUID}<br /><br />Purchased products:<br />{Products}<br /><br />Please, complete your payment and another confirmation email will be sent.<br /><br />Regards,<br />Shop Name', 'data'),
(NULL, 2, 'pjNotification', 1, 'payment_subject_client', 'Payment confirmation', 'data'),
(NULL, 2, 'pjNotification', 1, 'payment_tokens_client', 'Dear {ClientName},<br /><br />thank you for your payment. <br /><br />Amount: {Price}<br /><br />We will ship the products to you in next 24 - 48 hours.<br /><br />Regards,<br />Shop Name', 'data'),
(NULL, 3, 'pjNotification', 1, 'cancel_subject_client', 'Cancel confirmation', 'data'),
(NULL, 3, 'pjNotification', 1, 'cancel_tokens_client', 'You''ve just cancelled the order.<br/><br/>Your order number is: {OrderUUID}<br /><br />Purchased products:<br />{Products}<br/>Thank you, we will contact you ASAP.', 'data'),
(NULL, 4, 'pjNotification', 1, 'account_subject_client', 'Registration completed', 'data'),
(NULL, 4, 'pjNotification', 1, 'account_tokens_client', 'Dear {ClientName},<br /><br />Thank you for registering with us!<br /><br />Regards,<br />Shop Name', 'data'),
(NULL, 5, 'pjNotification', 1, 'forgot_subject_client', 'Password reminder', 'data'),
(NULL, 5, 'pjNotification', 1, 'forgot_tokens_client', 'Dear {ClientName},<br /><br />Your password is: {ClientPassword}<br /><br />Regards,<br />Shop Name', 'data'),
(NULL, 6, 'pjNotification', 1, 'send_to_friend_subject_client', 'Shopping Cart / Product', 'data'),
(NULL, 6, 'pjNotification', 1, 'send_to_friend_tokens_client', 'Dear {FriendName},<br /><br />Your friend {YourName} thinks this may be interested you:<br />{URL}', 'data'),
(NULL, 7, 'pjNotification', 1, 'confirm_subject_admin', 'New order received', 'data'),
(NULL, 7, 'pjNotification', 1, 'confirm_tokens_admin', 'New order has been made. <br /><br />Order number: {OrderUUID}<br /><br />Purchased products:<br />{Products}<br /><br />Order details<br /><br />{ClientName} - customer''s name<br />{ClientEmail} - customer''s e-mail<br />{ClientPassword} - customer''s password<br />{ClientPhone} - customer''s phone number<br />{ClientURL} - customer''s website<br />{BillingName} - customer''s billing name<br />{BillingAddress1} - billing address 1<br />{BillingAddress2} - billing address 2<br />{BillingCity} - billing city<br />{BillingState} - billing state<br />{BillingZip} - billing zip code<br />{BillingCountry} - billing country<br />{ShippingName} - customer''s shipping name<br />{ShippingAddress1} - shipping address 1<br />{ShippingAddress2} - shipping address 2<br />{ShippingCity} - shipping city<br />{ShippingState} - shipping state<br />{ShippingZip} - shipping zip code; <br />{ShippingCountry} - shipping country<br />{Notes} - additional notes<br />{CCType} - CC type<br />{CCNum} - CC number<br />{CCExpMonth} - CC exp.month<br />{CCExpYear} - CC exp.year<br />{CCSec} - CC sec. code<br />{PaymentMethod} - selected payment method<br />{Insurance} - insurance fee<br />{Shipping} - shipping fee<br />{Tax} - tax fee<br />{Total} - total amount<br />{Price} - price<br />{Discount} - discount<br />{Voucher} - promo code', 'data'),
(NULL, 8, 'pjNotification', 1, 'payment_subject_admin', 'New payment received', 'data'),
(NULL, 8, 'pjNotification', 1, 'payment_tokens_admin', 'Payment for Order {OrderUUID} has been made.<br /><br />You can ship the products.<br /><br />{Products}', 'data'),
(NULL, 9, 'pjNotification', 1, 'cancel_subject_admin', 'Order cancelled', 'data'),
(NULL, 9, 'pjNotification', 1, 'cancel_tokens_admin', 'An order has just been cancelled.<br/><br/>Order number: {OrderUUID}<br /><br />Purchased products:<br />{Products}<br /><br />Thank you!', 'data'),
(NULL, 10, 'pjNotification', 1, 'confirm_sms_client', 'You''ve just made an order. Your order number is: {OrderUUID}', 'data'),
(NULL, 11, 'pjNotification', 1, 'payment_sms_client', 'You''ve just made a payment. Your order number is: {OrderUUID}', 'data'),
(NULL, 12, 'pjNotification', 1, 'confirm_sms_admin', 'New order received {OrderUUID}', 'data'),
(NULL, 13, 'pjNotification', 1, 'payment_sms_admin', 'New payment received {OrderUUID}', 'data'),
(NULL, 1, 'pjPayment', 1, 'cash', 'Cash', 'script'),
(NULL, 1, 'pjPayment', 1, 'creditcard', 'Credit Card', 'script'),
(NULL, 1, 'pjPayment', 1, 'bank', 'Bank Account', 'script');

INSERT INTO `shopping_cart_options` 
(`foreign_id`, `company_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 1, 'o_install_url', 1, 'http://localhost/trunk/StivaShoppingCart/preview.php', '', 'string', NULL, 0, NULL),
(1, 1, 'o_layout', 1, '1|2|3::3', 'Layout 1|Layout 2|Bootstrap template', 'enum', 5, 0, NULL),
(1, 1, 'o_page_prefix', 1, 'pj', '', 'string', NULL, 0, NULL),
(1, 1, 'o_theme', 1, '1|2|3|4|5|6|7|8|9|10::1', 'Theme 1|Theme 2|Theme 3|Theme 4|Theme 5|Theme 6|Theme 7|Theme 8|Theme 9|Theme 10', 'enum', 5, 0, NULL),
(1, 1, 'o_setup_wizard_completed', 0, '0', 'Setup wizard completed', 'int', 0, 0, NULL),

(1, 1, 'o_seo_url', 2, 'Yes|No::Yes', 'Yes|No', 'enum', 1, 1, NULL),
(1, 1, 'o_products_per_page', 2, '16', NULL, 'int', 2, 1, NULL),
(1, 1, 'o_disable_orders', 2, 'Yes|No::No', 'Yes|No', 'enum', 3, 1, NULL),
(1, 1, 'o_disable_payments', 2, 'Yes|No::No', 'Yes|No', 'enum', 4, 1, NULL),
(1, 1, 'o_insurance', 2, '12', NULL, 'float', 5, 1, NULL),
(1, 1, 'o_insurance_type', 2, 'amount|percent::amount', 'Amount|Percent', 'enum', 6, 0, NULL),
(1, 1, 'o_thankyou_page', 2, 'http://www.phpjabbers.com/', NULL, 'string', 7, 1, NULL),

(1, 1, 'o_bf_b_name', 3, '1|2|3::3', 'No|Yes|Yes (Required)', 'enum', 1, 1, NULL),
(1, 1, 'o_bf_b_country_id', 3, '1|2|3::3', 'No|Yes|Yes (Required)', 'enum', 2, 1, NULL),
(1, 1, 'o_bf_b_city', 3, '1|2|3::3', 'No|Yes|Yes (Required)', 'enum', 3, 1, NULL),
(1, 1, 'o_bf_b_state', 3, '1|2|3::3', 'No|Yes|Yes (Required)', 'enum', 4, 1, NULL),
(1, 1, 'o_bf_b_zip', 3, '1|2|3::3', 'No|Yes|Yes (Required)', 'enum', 5, 1, NULL),
(1, 1, 'o_bf_b_address_1', 3, '1|2|3::3', 'No|Yes|Yes (Required)', 'enum', 6, 1, NULL),
(1, 1, 'o_bf_b_address_2', 3, '1|2|3::1', 'No|Yes|Yes (Required)', 'enum', 7, 1, NULL),
(1, 1, 'o_bf_s_name', 3, '1|2|3::3', 'No|Yes|Yes (Required)', 'enum', 8, 1, NULL),
(1, 1, 'o_bf_s_country_id', 3, '1|2|3::2', 'No|Yes|Yes (Required)', 'enum', 9, 1, NULL),
(1, 1, 'o_bf_s_city', 3, '1|2|3::2', 'No|Yes|Yes (Required)', 'enum', 10, 1, NULL),
(1, 1, 'o_bf_s_state', 3, '1|2|3::2', 'No|Yes|Yes (Required)', 'enum', 11, 1, NULL),
(1, 1, 'o_bf_s_zip', 3, '1|2|3::2', 'No|Yes|Yes (Required)', 'enum', 12, 1, NULL),
(1, 1, 'o_bf_s_address_1', 3, '1|2|3::3', 'No|Yes|Yes (Required)', 'enum', 13, 1, NULL),
(1, 1, 'o_bf_s_address_2', 3, '1|2|3::1', 'No|Yes|Yes (Required)', 'enum', 14, 1, NULL),
(1, 1, 'o_bf_c_name', 3, '1|2|3::3', 'No|Yes|Yes (Required)', 'enum', 15, 1, NULL),
(1, 1, 'o_bf_c_phone', 3, '1|2|3::2', 'No|Yes|Yes (Required)', 'enum', 16, 1, NULL),
(1, 1, 'o_bf_c_url', 3, '1|2|3::2', 'No|Yes|Yes (Required)', 'enum', 17, 1, NULL),
(1, 1, 'o_bf_notes', 3, '1|2|3::2', 'No|Yes|Yes (Required)', 'enum', 18, 1, NULL),
(1, 1, 'o_bf_captcha', 3, '1|3::3', 'No|Yes (Required)', 'enum', 19, 1, NULL),
(1, 1, 'o_bf_terms', 3, '1|3::3', 'No|Yes (Required)', 'enum', 20, 1, NULL),

(1, 1, 'o_terms_url', 4, '', NULL, 'string', 1, 1, NULL),
(1, 1, 'o_terms', 4, '', NULL, 'text', 2, 1, NULL),

(1, 1, 'o_allow_paypal', 5, '1|0::1', NULL, 'bool', 1, 1, NULL),
(1, 1, 'o_paypal_address', 5, 'paypal_seller@example.com', NULL, 'string', 2, 1, NULL),
(1, 1, 'o_allow_authorize', 5, '1|0::1', NULL, 'bool', 3, 1, NULL),
(1, 1, 'o_authorize_mid', 5, NULL, NULL, 'string', 4, 1, NULL),
(1, 1, 'o_authorize_key', 5, NULL, NULL, 'string', 5, 1, NULL),
(1, 1, 'o_authorize_hash', 5, NULL, NULL, 'string', 6, 1, NULL),
(1, 1, 'o_authorize_tz', 5, '-43200|-39600|-36000|-32400|-28800|-25200|-21600|-18000|-14400|-10800|-7200|-3600|0|3600|7200|10800|14400|18000|21600|25200|28800|32400|36000|39600|43200|46800::0', 'GMT-12:00|GMT-11:00|GMT-10:00|GMT-09:00|GMT-08:00|GMT-07:00|GMT-06:00|GMT-05:00|GMT-04:00|GMT-03:00|GMT-02:00|GMT-01:00|GMT|GMT+01:00|GMT+02:00|GMT+03:00|GMT+04:00|GMT+05:00|GMT+06:00|GMT+07:00|GMT+08:00|GMT+09:00|GMT+10:00|GMT+11:00|GMT+12:00|GMT+13:00', 'enum', 7, 1, NULL),
(1, 1, 'o_allow_bank', 5, '1|0::1', NULL, 'bool', 8, 1, NULL),
(1, 1, 'o_bank_account', 5, 'Bank of America', NULL, 'text', 9, 1, NULL),
(1, 1, 'o_allow_cash', 5, '1|0::1', NULL, 'bool', 10, 1, NULL),
(1, 1, 'o_allow_creditcard', 5, '1|0::1', NULL, 'bool', 11, 1, NULL),

(1, 1, 'o_multi_lang', 99, '1|0::1', NULL, 'enum', NULL, 1, NULL),
(1, 1, 'o_fields_index', 99, 'd874fcc5fe73b90d770a544664a3775d', NULL, 'string', NULL, 0, NULL);


INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'user', 'backend', 'Username', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Username', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pass', 'backend', 'Password', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Password', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'email', 'backend', 'E-Mail', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Email', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'url', 'backend', 'URL', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'URL', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'created', 'backend', 'Created', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'DateTime', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'btnSave', 'backend', 'Save', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Save', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'btnReset', 'backend', 'Reset', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Reset', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'addLocale', 'backend', 'Add language', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add language', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menuLang', 'backend', 'Menu Multi lang', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Multi Lang', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menuPlugins', 'backend', 'Menu Plugins', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Plugins', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menuUsers', 'backend', 'Menu Users', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Users', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menuOptions', 'backend', 'Menu Options', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Options', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menuLogout', 'backend', 'Menu Logout', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Logout', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'btnUpdate', 'backend', 'Update', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblChoose', 'backend', 'Choose', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Choose', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'btnSearch', 'backend', 'Search', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Search', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'backend', 'backend', 'Backend titles', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Back-end titles', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'frontend', 'backend', 'Front-end titles', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Front-end titles', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'locales', 'backend', 'Languages', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Languages', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'adminLogin', 'backend', 'Admin Login', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Admin Login', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'btnLogin', 'backend', 'Login', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Login', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menuDashboard', 'backend', 'Menu Dashboard', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Dashboard', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblOptionList', 'backend', 'Option list', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Option list', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'btnAdd', 'backend', 'Button Add', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblDelete', 'backend', 'Delete', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblType', 'backend', 'Type', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Type', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblName', 'backend', 'Name', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Name', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblRole', 'backend', 'Role', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Role', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblStatus', 'backend', 'Status', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Status', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblIsActive', 'backend', 'Is Active', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Is confirmed', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblUpdateUser', 'backend', 'Update user', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update user', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblAddUser', 'backend', 'Add user', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add user', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblValue', 'backend', 'Value', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Value', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblOption', 'backend', 'Option', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Option', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblDays', 'backend', 'Days', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'days', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menuLocales', 'backend', 'Menu Languages', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Languages', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblYes', 'backend', 'Yes', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Yes', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblNo', 'backend', 'No', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblError', 'backend', 'Error', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Error', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'btnBack', 'backend', 'Button Back', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '&laquo; Back', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'btnCancel', 'backend', 'Button Cancel', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Cancel', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblForgot', 'backend', 'Forgot password', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Forgot password', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'adminForgot', 'backend', 'Forgot password', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Password reminder', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'btnSend', 'backend', 'Button Send', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'emailForgotSubject', 'backend', 'Email / Forgot Subject', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Password reminder', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'emailForgotBody', 'backend', 'Email / Forgot Body', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Dear {Name},Your password: {Password}', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menuProfile', 'backend', 'Menu Profile', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Profile', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoLocalesTitle', 'backend', 'Infobox / Locales Title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Languages Title', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoLocalesBody', 'backend', 'Infobox / Locales Body', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Languages Body', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoLocalesBackendTitle', 'backend', 'Infobox / Locales Backend Title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Languages Backend Title', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoLocalesBackendBody', 'backend', 'Infobox / Locales Backend Body', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Languages Backend Body', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoLocalesFrontendTitle', 'backend', 'Infobox / Locales Frontend Title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Languages Frontend Title', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoLocalesFrontendBody', 'backend', 'Infobox / Locales Frontend Body', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Languages Frontend Body', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoListingPricesTitle', 'backend', 'Infobox / Listing Prices Title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Listing Prices Title', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoListingPricesBody', 'backend', 'Infobox / Listing Prices Body', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Listing Prices Body', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoListingBookingsTitle', 'backend', 'Infobox / Listing Bookings Title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Listing Bookings Title', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoListingBookingsBody', 'backend', 'Infobox / Listing Bookings Body', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Listing Bookings Body', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoListingContactTitle', 'backend', 'Infobox / Listing Contact Title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Listing Contact Title', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoListingContactBody', 'backend', 'Infobox / Listing Contact Body', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Listing Contact Body', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoListingAddressTitle', 'backend', 'Infobox / Listing Address Title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Listing Address Title', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoListingAddressBody', 'backend', 'Infobox / Listing Address Body', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Listing Address Body', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoListingExtendTitle', 'backend', 'Infobox / Extend exp.date Title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Extend exp.date Title', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoListingExtendBody', 'backend', 'Infobox / Extend exp.date Body', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Extend exp.date Body', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menuBackup', 'backend', 'Menu Backup', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Backup', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'btnBackup', 'backend', 'Button Backup', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Backup', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblBackupDatabase', 'backend', 'Backup / Database', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Backup database', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblBackupFiles', 'backend', 'Backup / Files', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Backup files', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'gridChooseAction', 'backend', 'Grid / Choose Action', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Choose Action', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'gridGotoPage', 'backend', 'Grid / Go to page', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Go to page:', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'gridTotalItems', 'backend', 'Grid / Total items', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Total items:', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'gridItemsPerPage', 'backend', 'Grid / Items per page', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Items per page', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'gridPrevPage', 'backend', 'Grid / Prev page', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Prev page', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'gridPrev', 'backend', 'Grid / Prev', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '&laquo; Prev', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'gridNextPage', 'backend', 'Grid / Next page', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Next page', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'gridNext', 'backend', 'Grid / Next', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Next &raquo;', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'gridDeleteConfirmation', 'backend', 'Grid / Delete confirmation', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete confirmation', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'gridConfirmationTitle', 'backend', 'Grid / Confirmation Title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Are you sure you want to delete selected record?', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'gridActionTitle', 'backend', 'Grid / Action Title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Action confirmation', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'gridBtnOk', 'backend', 'Grid / Button OK', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'OK', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'gridBtnCancel', 'backend', 'Grid / Button Cancel', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Cancel', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'gridBtnDelete', 'backend', 'Grid / Button Delete', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'gridEmptyResult', 'backend', 'Grid / Empty resultset', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No records found', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'multilangTooltip', 'backend', 'MultiLang / Tooltip', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Click on the flag icon to choose which language version of the content you wish to edit.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblIp', 'backend', 'IP address', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'IP address', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblUserCreated', 'backend', 'User / Registration Date & Time', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Registration date/time', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_currency', 'backend', 'Options / Currency', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Currency', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_date_format', 'backend', 'Options / Date format', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Date format', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_timezone', 'backend', 'Options / Timezone', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Timezone', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_week_start', 'backend', 'Options / First day of the week', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'First day of the week', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'u_statarr_ARRAY_T', 'arrays', 'u_statarr_ARRAY_T', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Active', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'u_statarr_ARRAY_F', 'arrays', 'u_statarr_ARRAY_F', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Inactive', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'filter_ARRAY_active', 'arrays', 'filter_ARRAY_active', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Active', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'filter_ARRAY_inactive', 'arrays', 'filter_ARRAY_inactive', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Inactive', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, '_yesno_ARRAY_T', 'arrays', '_yesno_ARRAY_T', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Yes', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, '_yesno_ARRAY_F', 'arrays', '_yesno_ARRAY_F', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'personal_titles_ARRAY_mr', 'arrays', 'personal_titles_ARRAY_mr', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Mr.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'personal_titles_ARRAY_mrs', 'arrays', 'personal_titles_ARRAY_mrs', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Mrs.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'personal_titles_ARRAY_miss', 'arrays', 'personal_titles_ARRAY_miss', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Miss', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'personal_titles_ARRAY_ms', 'arrays', 'personal_titles_ARRAY_ms', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Ms.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'personal_titles_ARRAY_dr', 'arrays', 'personal_titles_ARRAY_dr', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Dr.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'personal_titles_ARRAY_prof', 'arrays', 'personal_titles_ARRAY_prof', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Prof.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'personal_titles_ARRAY_rev', 'arrays', 'personal_titles_ARRAY_rev', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Rev.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'personal_titles_ARRAY_other', 'arrays', 'personal_titles_ARRAY_other', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Other', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'timezones_ARRAY_-43200', 'arrays', 'timezones_ARRAY_-43200', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'GMT-12:00', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'timezones_ARRAY_-39600', 'arrays', 'timezones_ARRAY_-39600', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'GMT-11:00', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'timezones_ARRAY_-36000', 'arrays', 'timezones_ARRAY_-36000', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'GMT-10:00', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'timezones_ARRAY_-32400', 'arrays', 'timezones_ARRAY_-32400', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'GMT-09:00', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'timezones_ARRAY_-28800', 'arrays', 'timezones_ARRAY_-28800', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'GMT-08:00', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'timezones_ARRAY_-25200', 'arrays', 'timezones_ARRAY_-25200', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'GMT-07:00', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'timezones_ARRAY_-21600', 'arrays', 'timezones_ARRAY_-21600', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'GMT-06:00', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'timezones_ARRAY_-18000', 'arrays', 'timezones_ARRAY_-18000', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'GMT-05:00', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'timezones_ARRAY_-14400', 'arrays', 'timezones_ARRAY_-14400', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'GMT-04:00', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'timezones_ARRAY_-10800', 'arrays', 'timezones_ARRAY_-10800', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'GMT-03:00', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'timezones_ARRAY_-7200', 'arrays', 'timezones_ARRAY_-7200', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'GMT-02:00', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'timezones_ARRAY_-3600', 'arrays', 'timezones_ARRAY_-3600', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'GMT-01:00', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'timezones_ARRAY_0', 'arrays', 'timezones_ARRAY_0', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'GMT', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'timezones_ARRAY_3600', 'arrays', 'timezones_ARRAY_3600', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'GMT+01:00', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'timezones_ARRAY_7200', 'arrays', 'timezones_ARRAY_7200', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'GMT+02:00', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'timezones_ARRAY_10800', 'arrays', 'timezones_ARRAY_10800', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'GMT+03:00', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'timezones_ARRAY_14400', 'arrays', 'timezones_ARRAY_14400', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'GMT+04:00', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'timezones_ARRAY_18000', 'arrays', 'timezones_ARRAY_18000', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'GMT+05:00', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'timezones_ARRAY_21600', 'arrays', 'timezones_ARRAY_21600', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'GMT+06:00', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'timezones_ARRAY_25200', 'arrays', 'timezones_ARRAY_25200', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'GMT+07:00', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'timezones_ARRAY_28800', 'arrays', 'timezones_ARRAY_28800', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'GMT+08:00', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'timezones_ARRAY_32400', 'arrays', 'timezones_ARRAY_32400', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'GMT+09:00', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'timezones_ARRAY_36000', 'arrays', 'timezones_ARRAY_36000', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'GMT+10:00', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'timezones_ARRAY_39600', 'arrays', 'timezones_ARRAY_39600', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'GMT+11:00', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'timezones_ARRAY_43200', 'arrays', 'timezones_ARRAY_43200', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'GMT+12:00', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'timezones_ARRAY_46800', 'arrays', 'timezones_ARRAY_46800', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'GMT+13:00', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AU01', 'arrays', 'error_titles_ARRAY_AU01', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'User updated!', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AU03', 'arrays', 'error_titles_ARRAY_AU03', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'User added!', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AU04', 'arrays', 'error_titles_ARRAY_AU04', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'User failed to add.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AU08', 'arrays', 'error_titles_ARRAY_AU08', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'User not found.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AO01', 'arrays', 'error_titles_ARRAY_AO01', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Options updated!', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AB01', 'arrays', 'error_titles_ARRAY_AB01', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Backup', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AB02', 'arrays', 'error_titles_ARRAY_AB02', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Backup complete!', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AB03', 'arrays', 'error_titles_ARRAY_AB03', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Backup failed!', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AB04', 'arrays', 'error_titles_ARRAY_AB04', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Backup failed!', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AA10', 'arrays', 'error_titles_ARRAY_AA10', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Account not found!', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AA11', 'arrays', 'error_titles_ARRAY_AA11', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Password send!', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AA12', 'arrays', 'error_titles_ARRAY_AA12', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Password not send!', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AA13', 'arrays', 'error_titles_ARRAY_AA13', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Profile updated!', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AU01', 'arrays', 'error_bodies_ARRAY_AU01', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All the changes made to this user have been saved.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AU03', 'arrays', 'error_bodies_ARRAY_AU03', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All the changes made to this user have been saved.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AU04', 'arrays', 'error_bodies_ARRAY_AU04', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'We are sorry, but the user has not been added.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AU08', 'arrays', 'error_bodies_ARRAY_AU08', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'User your looking for is missing.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AO01', 'arrays', 'error_bodies_ARRAY_AO01', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All the changes made to options have been saved.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_ALC01', 'arrays', 'error_bodies_ARRAY_ALC01', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All the changes made to titles have been saved.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AB01', 'arrays', 'error_bodies_ARRAY_AB01', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'We recommend you to regularly back up your database and files to prevent any loss of information.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AB02', 'arrays', 'error_bodies_ARRAY_AB02', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All backup files have been saved.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AB03', 'arrays', 'error_bodies_ARRAY_AB03', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No option was selected.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AB04', 'arrays', 'error_bodies_ARRAY_AB04', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Backup not performed.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AA10', 'arrays', 'error_bodies_ARRAY_AA10', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Given email address is not associated with any account.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AA11', 'arrays', 'error_bodies_ARRAY_AA11', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'For further instructions please check your mailbox.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AA12', 'arrays', 'error_bodies_ARRAY_AA12', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'W''re sorry, please try again later.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AA13', 'arrays', 'error_bodies_ARRAY_AA13', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All the changes made to your profile have been saved.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'months_ARRAY_1', 'arrays', 'months_ARRAY_1', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'January', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'months_ARRAY_2', 'arrays', 'months_ARRAY_2', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'February', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'months_ARRAY_3', 'arrays', 'months_ARRAY_3', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'March', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'months_ARRAY_4', 'arrays', 'months_ARRAY_4', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'April', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'months_ARRAY_5', 'arrays', 'months_ARRAY_5', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'May', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'months_ARRAY_6', 'arrays', 'months_ARRAY_6', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'June', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'months_ARRAY_7', 'arrays', 'months_ARRAY_7', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'July', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'months_ARRAY_8', 'arrays', 'months_ARRAY_8', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'August', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'months_ARRAY_9', 'arrays', 'months_ARRAY_9', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'September', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'months_ARRAY_10', 'arrays', 'months_ARRAY_10', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'October', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'months_ARRAY_11', 'arrays', 'months_ARRAY_11', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'November', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'months_ARRAY_12', 'arrays', 'months_ARRAY_12', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'December', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'days_ARRAY_0', 'arrays', 'days_ARRAY_0', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Sunday', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'days_ARRAY_1', 'arrays', 'days_ARRAY_1', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Monday', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'days_ARRAY_2', 'arrays', 'days_ARRAY_2', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Tuesday', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'days_ARRAY_3', 'arrays', 'days_ARRAY_3', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Wednesday', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'days_ARRAY_4', 'arrays', 'days_ARRAY_4', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Thursday', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'days_ARRAY_5', 'arrays', 'days_ARRAY_5', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Friday', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'days_ARRAY_6', 'arrays', 'days_ARRAY_6', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Saturday', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'day_names_ARRAY_0', 'arrays', 'day_names_ARRAY_0', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'S', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'day_names_ARRAY_1', 'arrays', 'day_names_ARRAY_1', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'M', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'day_names_ARRAY_2', 'arrays', 'day_names_ARRAY_2', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'T', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'day_names_ARRAY_3', 'arrays', 'day_names_ARRAY_3', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'W', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'day_names_ARRAY_4', 'arrays', 'day_names_ARRAY_4', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'T', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'day_names_ARRAY_5', 'arrays', 'day_names_ARRAY_5', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'F', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'day_names_ARRAY_6', 'arrays', 'day_names_ARRAY_6', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'S', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'short_months_ARRAY_1', 'arrays', 'short_months_ARRAY_1', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Jan', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'short_months_ARRAY_2', 'arrays', 'short_months_ARRAY_2', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Feb', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'short_months_ARRAY_3', 'arrays', 'short_months_ARRAY_3', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Mar', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'short_months_ARRAY_4', 'arrays', 'short_months_ARRAY_4', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Apr', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'short_months_ARRAY_5', 'arrays', 'short_months_ARRAY_5', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'May', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'short_months_ARRAY_6', 'arrays', 'short_months_ARRAY_6', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Jun', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'short_months_ARRAY_7', 'arrays', 'short_months_ARRAY_7', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Jul', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'short_months_ARRAY_8', 'arrays', 'short_months_ARRAY_8', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Aug', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'short_months_ARRAY_9', 'arrays', 'short_months_ARRAY_9', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Sep', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'short_months_ARRAY_10', 'arrays', 'short_months_ARRAY_10', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Oct', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'short_months_ARRAY_11', 'arrays', 'short_months_ARRAY_11', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Nov', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'short_months_ARRAY_12', 'arrays', 'short_months_ARRAY_12', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Dec', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'status_ARRAY_1', 'arrays', 'status_ARRAY_1', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You are not loged in.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'status_ARRAY_2', 'arrays', 'status_ARRAY_2', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Access denied. You have not requisite rights to.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'status_ARRAY_3', 'arrays', 'status_ARRAY_3', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Empty resultset.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'status_ARRAY_7', 'arrays', 'status_ARRAY_7', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'The operation is not allowed in demo mode.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'status_ARRAY_123', 'arrays', 'status_ARRAY_123', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Your hosting account does not allow uploading such a large image.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'status_ARRAY_999', 'arrays', 'status_ARRAY_999', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No permisions to edit the property', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'status_ARRAY_998', 'arrays', 'status_ARRAY_998', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No permisions to edit the reservation', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'status_ARRAY_997', 'arrays', 'status_ARRAY_997', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No reservation found', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'status_ARRAY_996', 'arrays', 'status_ARRAY_996', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No property for the reservation found', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'status_ARRAY_9999', 'arrays', 'status_ARRAY_9999', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Your registration was successfull.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'status_ARRAY_9998', 'arrays', 'status_ARRAY_9998', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Your registration was successfull. Your account needs to be approved.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'status_ARRAY_9997', 'arrays', 'status_ARRAY_9997', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'E-Mail address already exist', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'login_err_ARRAY_1', 'arrays', 'login_err_ARRAY_1', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Wrong username or password', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'login_err_ARRAY_2', 'arrays', 'login_err_ARRAY_2', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Access denied', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'login_err_ARRAY_3', 'arrays', 'login_err_ARRAY_3', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Account is disabled', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'localeArrays', 'backend', 'Locale / Arrays titles', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Arrays titles', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoLocalesArraysTitle', 'backend', 'Locale / Languages Array Title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Languages Arrays Title', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoLocalesArraysBody', 'backend', 'Locale / Languages Array Body', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Languages Array Body', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lnkBack', 'backend', 'Link Back', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Back', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'locale_order', 'backend', 'Locale / Order', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'locale_is_default', 'backend', 'Locale / Is default', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Is default', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'locale_flag', 'backend', 'Locale / Flag', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Flag', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'locale_title', 'backend', 'Locale / Title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Title', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'btnDelete', 'backend', 'Button Delete', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'btnContinue', 'backend', 'Button Continue', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Continue', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'vr_email_taken', 'backend', 'Users / Email already taken', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Email address is already in use', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'revert_status', 'backend', 'Revert status', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Revert status', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblExport', 'backend', 'Export', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Export', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_send_email', 'backend', 'opt_o_send_email', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Select email sending method', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_smtp_host', 'backend', 'opt_o_smtp_host', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'SMTP Host', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_smtp_port', 'backend', 'opt_o_smtp_port', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'SMTP Port', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_smtp_user', 'backend', 'opt_o_smtp_user', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'SMTP Username', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_smtp_pass', 'backend', 'opt_o_smtp_pass', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'SMTP Password', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menuProducts', 'backend', 'Menu Products', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Products', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menuVouchers', 'backend', 'Menu Vouchers', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Vouchers', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menuOrders', 'backend', 'Menu Orders', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Orders', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menuClients', 'backend', 'Menu Clients', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Clients', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menuCategories', 'backend', 'Menu Categories', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Categories', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menuInstall', 'backend', 'Menu Install', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Install', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menuPreview', 'backend', 'Menu Preview', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Preview', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblProductsList', 'backend', 'Products / List', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Products', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblProductsCreate', 'backend', 'Products / Add', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_statuses_ARRAY_1', 'arrays', 'product_statuses_ARRAY_1', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Available', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_statuses_ARRAY_2', 'arrays', 'product_statuses_ARRAY_2', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Hidden', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'login_email', 'backend', 'login_email', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Email', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'login_password', 'backend', 'login_password', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Password', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'login_login', 'backend', 'login_login', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Admin Login', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'login_error', 'backend', 'login_error', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Error', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'home_options', 'backend', 'home_options', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Options and Account Details', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'home_install', 'backend', 'home_install', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Installation instructions', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menu_dashboard', 'backend', 'menu_dashboard', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Dashboard', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menu_options', 'backend', 'menu_options', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Options', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menu_preview', 'backend', 'menu_preview', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Preview', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menu_install', 'backend', 'menu_install', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Install', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menu_logout', 'backend', 'menu_logout', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Logout', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menu_products', 'backend', 'menu_products', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Products', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menu_clients', 'backend', 'menu_clients', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Clients', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menu_orders', 'backend', 'menu_orders', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Orders', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menu_categories', 'backend', 'menu_categories', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Categories', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menu_vouchers', 'backend', 'menu_vouchers', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Vouchers', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_list', 'backend', 'product_list', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Products', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_create', 'backend', 'product_create', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add product', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_empty', 'backend', 'product_empty', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No products found', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_update', 'backend', 'product_update', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Product update', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_f_category', 'backend', 'product_f_category', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '-- Category --', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_details', 'backend', 'product_details', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Details', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_digital', 'backend', 'product_digital', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Digital', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_attr', 'backend', 'product_attr', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Attributes', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_stock', 'backend', 'product_stock', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'In Stock', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_photos', 'backend', 'product_photos', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Photos', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_extras', 'backend', 'product_extras', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Extras', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_history', 'backend', 'product_history', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'History', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_status', 'backend', 'product_status', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Status', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_name', 'backend', 'product_name', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Product name', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_sku', 'backend', 'product_sku', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'ID', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_short_desc', 'backend', 'product_short_desc', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Short description', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_full_desc', 'backend', 'product_full_desc', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Full description', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_is_featured', 'backend', 'product_is_featured', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Is featured', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_category', 'backend', 'product_category', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Category', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_is_digital', 'backend', 'product_is_digital', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'This is a digital product', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_file', 'backend', 'product_file', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'File', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_file_1', 'backend', 'product_file_1', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Browse file', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_file_2', 'backend', 'product_file_2', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'File path', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_digital_expire', 'backend', 'product_digital_expire', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Link expiration', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_digital_delete_title', 'backend', 'product_digital_delete_title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Confirm digital file delete', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_digital_delete_desc', 'backend', 'product_digital_delete_desc', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Are you sure you want to delete selected digital file?', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_del_title', 'backend', 'product_del_title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Confirm product delete', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_del_body', 'backend', 'product_del_body', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Are you sure you want to delete selected product?', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_extra_type', 'backend', 'product_extra_type', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Extra type', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_extra_add', 'backend', 'product_extra_add', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add extra', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_extra_item_add', 'backend', 'product_extra_item_add', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add extra item', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_extra_name', 'backend', 'product_extra_name', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Name', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_extra_price', 'backend', 'product_extra_price', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Price', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_extra_title', 'backend', 'product_extra_title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Title', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_extra_mandatory', 'backend', 'product_extra_mandatory', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Required', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_extra_delete', 'backend', 'product_extra_delete', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_extra_delete_title', 'backend', 'product_extra_delete_title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Confirm extra delete', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_extra_delete_desc', 'backend', 'product_extra_delete_desc', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Are you sure you want to delete selected extra?', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_attr_add', 'backend', 'product_attr_add', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add new attributes set', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_attr_copy', 'backend', 'product_attr_copy', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Copy attributes from similar product', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_attr_add_item', 'backend', 'product_attr_add_item', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add attribute item', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_attr_attr', 'backend', 'product_attr_attr', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Attribute title', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_attr_item', 'backend', 'product_attr_item', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Attribute item', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_attr_delete', 'backend', 'product_attr_delete', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_attr_delete_title', 'backend', 'product_attr_delete_title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Confirm attribute delete', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_attr_delete_desc', 'backend', 'product_attr_delete_desc', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Are you sure you want to delete selected attribute?', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_attr_copy_title', 'backend', 'product_attr_copy_title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Copy attributes from similar product', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_attr_or', 'backend', 'product_attr_or', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'OR', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_attr_copy_btn', 'backend', 'product_attr_copy_btn', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Copy', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_stock_qty', 'backend', 'product_stock_qty', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Qty', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_stock_price', 'backend', 'product_stock_price', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Price', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_stock_image', 'backend', 'product_stock_image', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Image', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_stock_attributes', 'backend', 'product_stock_attributes', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Attributes', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_stock_choose_image', 'backend', 'product_stock_choose_image', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Choose image', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_stock_add', 'backend', 'product_stock_add', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add combination', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_stock_img_title', 'backend', 'product_stock_img_title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Choose an image', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_stock_delete_title', 'backend', 'product_stock_delete_title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Confirm stock delete', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_stock_delete_desc', 'backend', 'product_stock_delete_desc', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Are you sure you want to delete selected stock?', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_history_empty', 'backend', 'product_history_empty', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No history found', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_history_created', 'backend', 'product_history_created', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Date/Time', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_history_product', 'backend', 'product_history_product', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Product', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_history_qty_before', 'backend', 'product_history_qty_before', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Qty before', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_history_qty_after', 'backend', 'product_history_qty_after', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Qty after', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'image_upload_add', 'backend', 'image_upload_add', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Upload', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'image_del_title', 'backend', 'image_del_title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete confirmation', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'image_del_body', 'backend', 'image_del_body', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Are you sure you want to delete this image?', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'image_edit_title', 'backend', 'image_edit_title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Edit image', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'image_title', 'backend', 'image_title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Title', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'image_delete', 'backend', 'image_delete', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'image_edit', 'backend', 'image_edit', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Edit', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'image_back', 'backend', 'image_back', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Back', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'image_empty', 'backend', 'image_empty', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Click on upload images button above to start uploading images', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'home_coming', 'backend', 'home_coming', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'COMING ON', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'home_leaving', 'backend', 'home_leaving', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'LEAVING ON', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'home_name', 'backend', 'home_name', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Name', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'home_regno', 'backend', 'home_regno', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Registration number', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'home_company', 'backend', 'home_company', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Company', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'home_date', 'backend', 'home_date', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'From', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'home_status', 'backend', 'home_status', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Status', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'home_payment', 'backend', 'home_payment', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Payment', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'home_space', 'backend', 'home_space', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Space type', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'home_bookings', 'backend', 'home_bookings', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Bookings', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'home_available', 'backend', 'home_available', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Available spaces', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'home_empty', 'backend', 'home_empty', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No bookings', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'voucher_update', 'backend', 'voucher_update', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update voucher', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'voucher_create', 'backend', 'voucher_create', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add voucher', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'voucher_list', 'backend', 'voucher_list', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Voucher list', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'voucher_code', 'backend', 'voucher_code', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Voucher code', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'voucher_products', 'backend', 'voucher_products', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Products', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'voucher_ch_product', 'backend', 'voucher_ch_product', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Choose a product', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'voucher_discount', 'backend', 'voucher_discount', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Discount', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'voucher_type', 'backend', 'voucher_type', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Type', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'voucher_valid', 'backend', 'voucher_valid', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Valid', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'voucher_date', 'backend', 'voucher_date', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Date', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'voucher_time_from', 'backend', 'voucher_time_from', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Time from', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'voucher_time_to', 'backend', 'voucher_time_to', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Time to', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'voucher_date_from', 'backend', 'voucher_date_from', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'From date/time', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'voucher_date_to', 'backend', 'voucher_date_to', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'To date/time', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'voucher_every', 'backend', 'voucher_every', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Every', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'voucher_vouchers', 'backend', 'voucher_vouchers', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Vouchers', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'voucher_choose', 'backend', 'voucher_choose', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '-- Choose --', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'voucher_empty', 'backend', 'voucher_empty', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No vouchers found', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'voucher_del_title', 'backend', 'voucher_del_title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete this voucher?', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'voucher_del_body', 'backend', 'voucher_del_body', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All voucher'' details will be deleted and will not be possible to restore them.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'voucher_p_del_title', 'backend', 'voucher_p_del_title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete product association confirmation', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'voucher_p_del_body', 'backend', 'voucher_p_del_body', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Are you sure you want to delete product association?', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'client_index', 'backend', 'client_index', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Clients', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'client_create', 'backend', 'client_create', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add client', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'client_update', 'backend', 'client_update', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update client', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'client_filter', 'backend', 'client_filter', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Find', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'client_email', 'backend', 'client_email', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'E-Mail address', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'client_password', 'backend', 'client_password', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Password', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'client_phone', 'backend', 'client_phone', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Phone', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'client_url', 'backend', 'client_url', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Website', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'client_client_name', 'backend', 'client_client_name', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Client Name', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'client_created', 'backend', 'client_created', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Created', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'client_last_login', 'backend', 'client_last_login', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Last login', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'client_country', 'backend', 'client_country', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Country', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'client_state', 'backend', 'client_state', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'State', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'client_city', 'backend', 'client_city', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'City', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'client_zip', 'backend', 'client_zip', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Zip', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'client_address_1', 'backend', 'client_address_1', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Address line 1', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'client_address_2', 'backend', 'client_address_2', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Address line 2', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'client_name', 'backend', 'client_name', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Name', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'client_default_shipping', 'backend', 'client_default_shipping', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Default shipping address', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'client_default_billing', 'backend', 'client_default_billing', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Default billing address', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'client_empty', 'backend', 'client_empty', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No clients found', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'client_address_book', 'backend', 'client_address_book', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Address book', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'client_general', 'backend', 'client_general', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'General info', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'client_choose', 'backend', 'client_choose', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '-- Choose --', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'client_add_address', 'backend', 'client_add_address', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add new address', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'client_del_address', 'backend', 'client_del_address', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'delete', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'client_da_title', 'backend', 'client_da_title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Confirm address delete', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'client_da_body', 'backend', 'client_da_body', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Are you sure you want to delete selected address?', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'client_del_title', 'backend', 'client_del_title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Confirm client delete', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'client_del_body', 'backend', 'client_del_body', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Are you sure you want to delete selected client?', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'client_orders', 'backend', 'client_orders', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Total orders', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'client_last_order', 'backend', 'client_last_order', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Last order', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'category_update', 'backend', 'category_update', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update category', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'category_create', 'backend', 'category_create', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add category', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'category_list', 'backend', 'category_list', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Categories', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'category_name', 'backend', 'category_name', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Name', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'category_parent', 'backend', 'category_parent', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Parent category', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'category_categories', 'backend', 'category_categories', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Categories', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'category_choose', 'backend', 'category_choose', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '-- Choose --', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'category_no_parent', 'backend', 'category_no_parent', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No parent', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'category_empty', 'backend', 'category_empty', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No categories found', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'category_del_title', 'backend', 'category_del_title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete this category?', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'category_del_body', 'backend', 'category_del_body', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All category'' details will be deleted and will not be possible to restore them.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_index', 'backend', 'order_index', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Orders', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_create', 'backend', 'order_create', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add order', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_update', 'backend', 'order_update', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update order', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_uuid', 'backend', 'order_uuid', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order number', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_status', 'backend', 'order_status', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Status', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_price', 'backend', 'order_price', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Price', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_shipping', 'backend', 'order_shipping', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Shipping', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_tax', 'backend', 'order_tax', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Tax', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_discount', 'backend', 'order_discount', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Discount', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_total', 'backend', 'order_total', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Total', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_voucher', 'backend', 'order_voucher', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Voucher', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_payment', 'backend', 'order_payment', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Payment', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_products', 'backend', 'order_products', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Products', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_client', 'backend', 'order_client', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Client', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_address', 'backend', 'order_address', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Address', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_email', 'backend', 'order_email', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'E-Mail address', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_phone', 'backend', 'order_phone', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Phone', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_url', 'backend', 'order_url', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Website', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_created', 'backend', 'order_created', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Created', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_country', 'backend', 'order_country', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Country', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_state', 'backend', 'order_state', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'State', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_city', 'backend', 'order_city', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'City', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_zip', 'backend', 'order_zip', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Zip', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_address_1', 'backend', 'order_address_1', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Address line 1', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_address_2', 'backend', 'order_address_2', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Address line 2', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_name', 'backend', 'order_name', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Name', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_notes', 'backend', 'order_notes', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Notes', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_empty', 'backend', 'order_empty', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No orders found', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_general', 'backend', 'order_general', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order details', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_choose', 'backend', 'order_choose', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '-- Choose --', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_del_product', 'backend', 'order_del_product', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'delete', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_del_title', 'backend', 'order_del_title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Confirm order delete', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_del_body', 'backend', 'order_del_body', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Are you sure you want to delete selected order?', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_customer', 'backend', 'order_customer', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Customer info', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_shipping_details', 'backend', 'order_shipping_details', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Quote address', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_billing_details', 'backend', 'order_billing_details', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Billing details', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_copy_s', 'backend', 'order_copy_s', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Use for Shipping address', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_copy_b', 'backend', 'order_copy_b', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Use for Billing address', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_same', 'backend', 'order_same', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Same as Billing details', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_p_name', 'backend', 'order_p_name', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Product', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_p_price', 'backend', 'order_p_price', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Price', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_p_qty', 'backend', 'order_p_qty', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Qty', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_p_attr', 'backend', 'order_p_attr', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Attributes', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_p_sku', 'backend', 'order_p_sku', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'ID', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'option_install', 'backend', 'option_install', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Install', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'option_list', 'backend', 'option_list', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Option list', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'option_key', 'backend', 'option_key', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Option key', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'option_description', 'backend', 'option_description', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Options', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'option_value', 'backend', 'option_value', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Value', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'option_general', 'backend', 'option_general', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'General', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'option_bookings', 'backend', 'option_bookings', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Bookings', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'option_confirmation', 'backend', 'option_confirmation', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Confirmation', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'option_booking_form', 'backend', 'option_booking_form', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Checkout Form', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'option_terms', 'backend', 'option_terms', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Terms', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'option_email', 'backend', 'option_email', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Email', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'option_password', 'backend', 'option_password', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Password', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'option_hours_before', 'backend', 'option_hours_before', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'hours before', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'option_get_key', 'backend', 'option_get_key', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'get key', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'option_cron', 'backend', 'option_cron', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Cron script', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'option_cron_info', 'backend', 'option_cron_info', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You need to set up a cron job using your hosting account control panel which should execute every hour. Depending on your web server you should use either the URL or script path.
<br /><br/>
Server path:<br /><span class=\"bold\">%1$s</span>
<br /><br />
URL:<br /><span class=\"bold\">%2$s</span>', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'extra_title', 'backend', 'extra_title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Name', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'extra_description', 'backend', 'extra_description', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Description', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'extra_update', 'backend', 'extra_update', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update extra', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'extra_create', 'backend', 'extra_create', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add extra', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'extra_list', 'backend', 'extra_list', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Extra list', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'extra_price', 'backend', 'extra_price', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Price', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'extra_count', 'backend', 'extra_count', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Count', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'extra_type', 'backend', 'extra_type', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Type', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'extra_is_single', 'backend', 'extra_is_single', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Is single', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'extra_empty', 'backend', 'extra_empty', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No extras found', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'extra_del_title', 'backend', 'extra_del_title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete this extra?', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'extra_del_body', 'backend', 'extra_del_body', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All extra'' details will be deleted and will not be possible to restore them.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, '_view_calendar', 'backend', '_view_calendar', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'calendar', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, '_switch', 'backend', '_switch', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Switch On/Off', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, '_search', 'backend', '_search', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Search', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, '_save', 'backend', '_save', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Save changes', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, '_cancel', 'backend', '_cancel', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Cancel', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, '_upload', 'backend', '_upload', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Upload', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, '_edit', 'backend', '_edit', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Edit', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, '_contact', 'backend', '_contact', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Contact', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, '_email', 'backend', '_email', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Email', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, '_bookings', 'backend', '_bookings', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Bookings', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, '_print', 'backend', '_print', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Print', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, '_delete', 'backend', '_delete', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, '_delete_all', 'backend', '_delete_all', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'delete all', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, '_view', 'backend', '_view', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'view', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, '_back', 'backend', '_back', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'back', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, '_never', 'backend', '_never', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'never', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, '_empty', 'backend', '_empty', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No records.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, '_sure', 'backend', '_sure', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Are you sure you want to delete selected record?', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, '_up', 'backend', '_up', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'up', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, '_down', 'backend', '_down', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'down', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'delete_selected', 'backend', 'Grid / Delete selected', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete selected', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_extra_types_ARRAY_single', 'arrays', 'product_extra_types_ARRAY_single', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Single', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_extra_types_ARRAY_multi', 'arrays', 'product_extra_types_ARRAY_multi', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Multi', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'voucher_types_ARRAY_amount', 'arrays', 'voucher_types_ARRAY_amount', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Amount', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'voucher_types_ARRAY_percent', 'arrays', 'voucher_types_ARRAY_percent', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Percent', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'voucher_valids_ARRAY_fixed', 'arrays', 'voucher_valids_ARRAY_fixed', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Fixed date', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'voucher_valids_ARRAY_period', 'arrays', 'voucher_valids_ARRAY_period', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Period', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'voucher_valids_ARRAY_recurring', 'arrays', 'voucher_valids_ARRAY_recurring', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Recurring', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_statuses_ARRAY_new', 'arrays', 'order_statuses_ARRAY_new', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'New', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_statuses_ARRAY_pending', 'arrays', 'order_statuses_ARRAY_pending', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Pending', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_statuses_ARRAY_cancelled', 'arrays', 'order_statuses_ARRAY_cancelled', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Cancelled', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_statuses_ARRAY_completed', 'arrays', 'order_statuses_ARRAY_completed', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Completed', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_payments_ARRAY_paypal', 'arrays', 'order_payments_ARRAY_paypal', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'PayPal', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_payments_ARRAY_authorize', 'arrays', 'order_payments_ARRAY_authorize', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Authorize.NET', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_payments_ARRAY_creditcard', 'arrays', 'order_payments_ARRAY_creditcard', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Credit Card', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'option_err_ARRAY_5', 'arrays', 'option_err_ARRAY_5', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Options has been updated', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'extra_per_ARRAY_booking', 'arrays', 'extra_per_ARRAY_booking', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Per booking', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'extra_per_ARRAY_day', 'arrays', 'extra_per_ARRAY_day', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Per day', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'extra_types_ARRAY_limited', 'arrays', 'extra_types_ARRAY_limited', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Limited', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'extra_types_ARRAY_unlimited', 'arrays', 'extra_types_ARRAY_unlimited', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Unlimited', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'extra_err_ARRAY_1', 'arrays', 'extra_err_ARRAY_1', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Extra has been added', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'extra_err_ARRAY_2', 'arrays', 'extra_err_ARRAY_2', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Extra has not been added', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'extra_err_ARRAY_3', 'arrays', 'extra_err_ARRAY_3', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Extra has been deleted', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'extra_err_ARRAY_4', 'arrays', 'extra_err_ARRAY_4', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Extra has not been deleted', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'extra_err_ARRAY_5', 'arrays', 'extra_err_ARRAY_5', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Extra has been updated', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'extra_err_ARRAY_6', 'arrays', 'extra_err_ARRAY_6', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Extra has not been updated', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'extra_err_ARRAY_7', 'arrays', 'extra_err_ARRAY_7', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'extra_err_ARRAY_8', 'arrays', 'extra_err_ARRAY_8', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Extra doesn''t exists', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, '_bool_ARRAY_1', 'arrays', '_bool_ARRAY_1', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Yes', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, '_bool_ARRAY_0', 'arrays', '_bool_ARRAY_0', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, '_titles_ARRAY_mr', 'arrays', '_titles_ARRAY_mr', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Mr', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, '_titles_ARRAY_mrs', 'arrays', '_titles_ARRAY_mrs', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Mrs', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, '_titles_ARRAY_ms', 'arrays', '_titles_ARRAY_ms', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Ms', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, '_titles_ARRAY_dr', 'arrays', '_titles_ARRAY_dr', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Dr', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, '_titles_ARRAY_prof', 'arrays', '_titles_ARRAY_prof', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Prof', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, '_titles_ARRAY_rev', 'arrays', '_titles_ARRAY_rev', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Rev', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, '_titles_ARRAY_other', 'arrays', '_titles_ARRAY_other', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Other', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, '_payments_ARRAY_paypal', 'arrays', '_payments_ARRAY_paypal', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'PayPal', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, '_payments_ARRAY_authorize', 'arrays', '_payments_ARRAY_authorize', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Authorize.net', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, '_payments_ARRAY_creditcard', 'arrays', '_payments_ARRAY_creditcard', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Credit Card', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, '_payments_ARRAY_cash', 'arrays', '_payments_ARRAY_cash', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Cash', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'status_ARRAY_4', 'arrays', 'status_ARRAY_4', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'The operation has not been successful', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'status_ARRAY_8', 'arrays', 'status_ARRAY_8', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'The operation is allowed only in Multi-calendar mode', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'status_ARRAY_9', 'arrays', 'status_ARRAY_9', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'The operation is allowed only in Multi-user mode', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'status_ARRAY_20', 'arrays', 'status_ARRAY_20', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'The operation has been successful', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'err_ARRAY_1', 'arrays', 'err_ARRAY_1', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Operation has not been successful.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AU01', 'arrays', 'errors_ARRAY_AU01', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'User has been added', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AU02', 'arrays', 'errors_ARRAY_AU02', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'User has not been added', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AU03', 'arrays', 'errors_ARRAY_AU03', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'User has been deleted', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AU04', 'arrays', 'errors_ARRAY_AU04', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'User has not been deleted', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AU05', 'arrays', 'errors_ARRAY_AU05', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'User has been updated', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AU06', 'arrays', 'errors_ARRAY_AU06', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'User has not been updated', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AU08', 'arrays', 'errors_ARRAY_AU08', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'User doesn''t exists', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AU09', 'arrays', 'errors_ARRAY_AU09', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Email already exists', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AU10', 'arrays', 'error_titles_ARRAY_AU10', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Profile has been updated', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AE01', 'arrays', 'errors_ARRAY_AE01', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Extra has been added', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AE02', 'arrays', 'errors_ARRAY_AE02', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Extra has not been added', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AE03', 'arrays', 'errors_ARRAY_AE03', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Extra has been deleted', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AE04', 'arrays', 'errors_ARRAY_AE04', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Extra has not been deleted', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AE05', 'arrays', 'errors_ARRAY_AE05', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Extra has been updated', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AE06', 'arrays', 'errors_ARRAY_AE06', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Extra has not been updated', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AE08', 'arrays', 'errors_ARRAY_AE08', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Extra doesn''t exists', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AP01', 'arrays', 'errors_ARRAY_AP01', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Product has been added', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AP02', 'arrays', 'errors_ARRAY_AP02', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Product has not been added', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AP03', 'arrays', 'errors_ARRAY_AP03', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Product has been deleted', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AP04', 'arrays', 'errors_ARRAY_AP04', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Product has not been deleted', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AP05', 'arrays', 'errors_ARRAY_AP05', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Product has been updated', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AP06', 'arrays', 'errors_ARRAY_AP06', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Product has not been updated', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AP08', 'arrays', 'errors_ARRAY_AP08', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Product doesn''t exists', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AP01', 'backend', 'error_titles_ARRAY_AP01', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Product updated', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AP03', 'backend', 'error_titles_ARRAY_AP03', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Product added', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AP04', 'backend', 'error_titles_ARRAY_AP04', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Product failed to add', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AP05', 'backend', 'error_titles_ARRAY_AP05', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Image size too large', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AP06', 'backend', 'error_titles_ARRAY_AP06', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Image size too large', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AP08', 'backend', 'error_titles_ARRAY_AP08', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Product not found', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AP01', 'backend', 'error_bodies_ARRAY_AP01', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'All changes to the product have been saved.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AP03', 'backend', 'error_bodies_ARRAY_AP03', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'New product has been added to the list.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AP04', 'backend', 'error_bodies_ARRAY_AP04', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'We are sorry that new product could not be added successfully.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AP05', 'backend', 'error_bodies_ARRAY_AP05', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'New product could not be added because image size is too large and your server cannot upload it. Maximum allowed size is {SIZE}. Please, upload smaller image.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AP06', 'backend', 'error_bodies_ARRAY_AP06', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'The product could not be updated successfully because image size is too large and your server cannot upload it. Maximum allowed size is {SIZE}. Please, upload smaller image.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AP08', 'backend', 'error_bodies_ARRAY_AP08', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'We are sorry that product you are looking for is missing.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AP09', 'backend', 'error_titles_ARRAY_AP09', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Upload error', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AP10', 'backend', 'error_titles_ARRAY_AP10', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Upload error', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AP09', 'backend', 'error_bodies_ARRAY_AP09', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'New product has been added, but image could not be uploaded because filesize is too big. Upload max filesize is {SIZE}. Please upload another file!', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AP10', 'backend', 'error_bodies_ARRAY_AP10', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'The product has been updated, but image could not be uploaded successfully.', 'script');


INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AC01', 'arrays', 'error_titles_ARRAY_AC01', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Client has been added', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AC02', 'arrays', 'error_titles_ARRAY_AC02', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Client has not been added', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AC05', 'arrays', 'error_titles_ARRAY_AC05', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Client has been updated', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AC06', 'arrays', 'error_titles_ARRAY_AC06', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Client has not been updated', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AC07', 'arrays', 'error_titles_ARRAY_AC07', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'E-Mail address already in use', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AC08', 'arrays', 'error_titles_ARRAY_AC08', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Client doesn''t exists', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AO01', 'arrays', 'errors_ARRAY_AO01', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order has been added', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AO02', 'arrays', 'errors_ARRAY_AO02', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order has not been added', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AO03', 'arrays', 'errors_ARRAY_AO03', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order has been deleted', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AO04', 'arrays', 'errors_ARRAY_AO04', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order has not been deleted', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AO05', 'arrays', 'errors_ARRAY_AO05', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order has been updated', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AO06', 'arrays', 'errors_ARRAY_AO06', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order has not been updated', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AO08', 'arrays', 'errors_ARRAY_AO08', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order doesn''t exists', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AG01', 'arrays', 'error_titles_ARRAY_AG01', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Category has been added', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AG02', 'arrays', 'error_titles_ARRAY_AG02', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Category has not been added', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AG05', 'arrays', 'error_titles_ARRAY_AG05', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Category has been updated', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AG08', 'arrays', 'error_titles_ARRAY_AG08', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Category doesn''t exists', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AV01', 'arrays', 'error_titles_ARRAY_AV01', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Voucher has been added', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AV02', 'arrays', 'error_titles_ARRAY_AV02', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Voucher has not been added', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AV05', 'arrays', 'error_titles_ARRAY_AV05', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Voucher has been updated', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AV06', 'arrays', 'error_titles_ARRAY_AV06', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Voucher has not been updated', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AV08', 'arrays', 'error_titles_ARRAY_AV08', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Voucher doesn''t exists', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AS01', 'arrays', 'errors_ARRAY_AS01', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Options has been updated', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AA07', 'arrays', 'errors_ARRAY_AA07', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'The operation is not allowed in demo mode.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'info_ARRAY_users_create', 'arrays', 'info_ARRAY_users_create', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can create different users and set which calendars each user can manage. If ''Change options'' is checked user will be able to change the options for the calendar and not its availability status. If ''Install cod'' is checked user will be able to view installation code for his/her calendar.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'info_ARRAY_users_profile', 'arrays', 'info_ARRAY_users_profile', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Administration login details.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'info_ARRAY_options_index', 'arrays', 'info_ARRAY_options_index', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Manage the options for your Parking Booking software.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'info_ARRAY_options_install', 'arrays', 'info_ARRAY_options_install', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Copy the HTML code below and put it on your web page where you want the booking engine to appear. Please note that the booking engine can ONLY be placed on a web page from the same domain where the script is installed.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'info_ARRAY_dashboard', 'arrays', 'info_ARRAY_dashboard', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Using the date picker below select a date to view bookings and availability', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'info_ARRAY_add_product', 'arrays', 'info_ARRAY_add_product', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add space type name and description. You can set different number of available parking spaces for this type for different periods during the year. For example you can create a space type with 100 spaces for 1st Jan - 31 May, and then during the summer season 1st June - 31th Aug you can set different amount of available spaces for this same type.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'info_ARRAY_price', 'arrays', 'info_ARRAY_price', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'For each space type you can set different price for different dates. For example you can add higher rates for the summer season.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'info_ARRAY_discounts', 'arrays', 'info_ARRAY_discounts', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'First select space type and date period for which price is already set under Prices menu. Then you can set number of days for a booking and a new daily price. For example if you set min days 5 and max days 6, then any booking for 5 or 6 days will be priced based on the new added ''Price per day''. ', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'info_ARRAY_add_extra', 'arrays', 'info_ARRAY_add_extra', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add different extras which people can purchase along with the parking space. For each extra you can add name, description and price. Price can be for the whole booking or per day. Each extra also has type - Limited and Unlimited. If extra is limited then there is a fixed number of items that can be booked. Under Count textbox you can set the number of available items. ''Is singl'' option is used to specify if users should select number of extras that can be purchased.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'info_ARRAY_client_index', 'arrays', 'info_ARRAY_client_index', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Lorem ipsum...', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'info_ARRAY_add_category', 'arrays', 'info_ARRAY_add_category', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Lorem ipsum...', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'info_ARRAY_add_voucher', 'arrays', 'info_ARRAY_add_voucher', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Lorem ipsum...', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'months_full_ARRAY_1', 'arrays', 'months_full_ARRAY_1', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'January', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'months_full_ARRAY_2', 'arrays', 'months_full_ARRAY_2', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'February', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'months_full_ARRAY_3', 'arrays', 'months_full_ARRAY_3', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'March', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'months_full_ARRAY_4', 'arrays', 'months_full_ARRAY_4', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'April', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'months_full_ARRAY_5', 'arrays', 'months_full_ARRAY_5', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'May', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'months_full_ARRAY_6', 'arrays', 'months_full_ARRAY_6', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'June', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'months_full_ARRAY_7', 'arrays', 'months_full_ARRAY_7', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'July', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'months_full_ARRAY_8', 'arrays', 'months_full_ARRAY_8', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'August', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'months_full_ARRAY_9', 'arrays', 'months_full_ARRAY_9', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'September', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'months_full_ARRAY_10', 'arrays', 'months_full_ARRAY_10', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'October', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'months_full_ARRAY_11', 'arrays', 'months_full_ARRAY_11', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'November', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'months_full_ARRAY_12', 'arrays', 'months_full_ARRAY_12', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'December', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'day_name_ARRAY_0', 'arrays', 'day_name_ARRAY_0', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Su', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'day_name_ARRAY_1', 'arrays', 'day_name_ARRAY_1', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Mo', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'day_name_ARRAY_2', 'arrays', 'day_name_ARRAY_2', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Tu', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'day_name_ARRAY_3', 'arrays', 'day_name_ARRAY_3', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'We', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'day_name_ARRAY_4', 'arrays', 'day_name_ARRAY_4', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Th', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'day_name_ARRAY_5', 'arrays', 'day_name_ARRAY_5', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Fr', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'day_name_ARRAY_6', 'arrays', 'day_name_ARRAY_6', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Sa', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'daynames_ARRAY_monday', 'arrays', 'daynames_ARRAY_monday', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Monday', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'daynames_ARRAY_tuesday', 'arrays', 'daynames_ARRAY_tuesday', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Tuesday', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'daynames_ARRAY_wednesday', 'arrays', 'daynames_ARRAY_wednesday', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Wednesday', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'daynames_ARRAY_thursday', 'arrays', 'daynames_ARRAY_thursday', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Thursday', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'daynames_ARRAY_friday', 'arrays', 'daynames_ARRAY_friday', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Friday', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'daynames_ARRAY_saturday', 'arrays', 'daynames_ARRAY_saturday', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Saturday', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'daynames_ARRAY_sunday', 'arrays', 'daynames_ARRAY_sunday', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Sunday', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'f_ARRAY_extras', 'arrays', 'f_ARRAY_extras', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Extras', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_datetime_format', 'backend', 'Options / Date & Time format', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Date/Time format', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'delete_confirmation', 'backend', 'Grid / Delete confirmation', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Are you sure that you want to delete selected record(s)?', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_payment_details', 'backend', 'order_payment_details', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Payment details', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menuPayments', 'backend', 'Menu Payments', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Payments', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_allow_authorize', 'backend', 'Options / Allow Authorize.net', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Allow payments with Authorize.net', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_allow_bank', 'backend', 'Options / Allow Bank', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Provide Bank account details for wire transfers', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_allow_creditcard', 'backend', 'Options / Allow Credit Card', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Collect Credit Card details for offline processing', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_allow_paypal', 'backend', 'Options / Allow Paypal', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Allow payments with PayPal', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_authorize_key', 'backend', 'Options / Authorize.net transaction key', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Authorize.net transaction key', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_authorize_mid', 'backend', 'Options / Authorize.net merchant ID', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Authorize.net merchant ID', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_authorize_tz', 'backend', 'Options / Authorize.net Time zone', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Authorize.net time zone', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_bank_account', 'backend', 'Options / Bank account', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Bank account', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_thankyou_page', 'backend', 'Options / "Thank you" page location', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Thank you page', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_thankyou_page_text', 'backend', 'Options / "Thank you" page location', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'URL for the web page where your clients will be redirected after online payment', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_paypal_address', 'backend', 'Options / Paypal address', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Paypal address', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_insurance', 'backend', 'Options / Insurance', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add insurance fee for each order', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_disable_payments', 'backend', 'Options / Disable payments', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Disable payments', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_disable_payments_text', 'backend', 'Options / Disable payments', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Check if you want to disable payments and only collect order details', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'cc_types_ARRAY_Visa', 'arrays', 'cc_types_ARRAY_Visa', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Visa', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'cc_types_ARRAY_MasterCard', 'arrays', 'cc_types_ARRAY_MasterCard', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'MasterCard', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'cc_types_ARRAY_Maestro', 'arrays', 'cc_types_ARRAY_Maestro', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Maestro', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'cc_types_ARRAY_AmericanExpress', 'arrays', 'cc_types_ARRAY_AmericanExpress', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'AmericanExpress', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'bf_cc_num', 'frontend', 'Booking form / CC Number', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'CC Number', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'bf_cc_exp', 'frontend', 'Booking form / CC Exp.date', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'CC Exp.date', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'bf_cc_sec', 'frontend', 'Booking form / CC Sec.code', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'CC Sec.code', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'bf_cc_type', 'frontend', 'Booking form / CC Type', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'CC Type', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'bf_payment', 'frontend', 'Booking form / Payment method', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Payment method', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'choose_address', 'frontend', 'Booking form / Choose address', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '-- Choose address --', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'choose_country', 'backend', 'Booking form / Choose country', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '-- Choose country --', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'choose_payment', 'backend', 'Booking form / Choose payment', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '-- Choose payment --', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_allow_cash', 'backend', 'Options / Allow Cash on delivery', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Allow Cash on delivery payment option', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menuGeneral', 'backend', 'Menu General', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'General', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menuCheckout', 'backend', 'Menu Checkout', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Checkout Form', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_bf_b_name', 'backend', 'Options / Name (Billing details)', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Name (Billing details)', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_bf_b_city', 'backend', 'Options / City (Billing details)', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'City (Billing details)', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_bf_b_state', 'backend', 'Options / State (Billing details)', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'State (Billing details)', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_bf_b_country_id', 'backend', 'Options / Country (Billing details)', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Country (Billing details)', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_bf_b_zip', 'backend', 'Options / Zip (Billing details)', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Zip (Billing details)', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_bf_b_address_2', 'backend', 'Options / Address 2 (Billing details)', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Address 2 (Billing details)', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_bf_b_address_1', 'backend', 'Options / Address 1 (Billing details)', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Address 1 (Billing details)', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_bf_s_address_1', 'backend', 'Options / Address 1 (Shipping details)', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Address 1 (Shipping details)', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_bf_s_address_2', 'backend', 'Options / Address 2 (Shipping details)', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Address 2 (Shipping details)', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_bf_s_zip', 'backend', 'Options / Zip (Shipping details)', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Zip (Shipping details)', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_bf_s_country_id', 'backend', 'Options / Country (Shipping details)', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Country (Shipping details)', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_bf_s_state', 'backend', 'Options / State (Shipping details)', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'State (Shipping details)', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_bf_s_city', 'backend', 'Options / City (Shipping details)', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'City (Shipping details)', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_bf_s_name', 'backend', 'Options / Name (Shipping details)', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Name (Shipping details)', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_bf_captcha', 'backend', 'Options / Captcha', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Captcha', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_bf_terms', 'backend', 'Options / Terms', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Terms', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_notes', 'backend', 'Options / Notes', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Notes', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_bf_notes', 'backend', 'Options / Notes', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Notes', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'bf_captcha', 'frontend', 'Booking form / Captcha', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Captcha', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'bf_notes', 'frontend', 'Booking form / Notes', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Notes', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'bf_terms', 'frontend', 'Booking form / Terms', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'I Agree with terms and conditions', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_other_details', 'backend', 'Checkout / Other details', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Other details', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'bf_bank_account', 'frontend', 'Booking form / Bank account', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Bank account', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_insurance', 'backend', 'order_insurance', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Insurance', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_p_subtotal', 'backend', 'order_p_subtotal', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'SubTotal', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AG01', 'arrays', 'error_bodies_ARRAY_AG01', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can now add another category', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AG02', 'arrays', 'error_bodies_ARRAY_AG02', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Category has not been added', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AG05', 'arrays', 'error_bodies_ARRAY_AG05', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Category has been updated', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AG08', 'arrays', 'error_bodies_ARRAY_AG08', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Category doesn''t exists', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menuShippingTax', 'backend', 'Menu Shipping & Tax', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Shipping & Tax', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'tax_location', 'backend', 'Shipping & Taxes / Location', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Location', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'tax_shipping', 'backend', 'Shipping & Taxes / Shipping', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Shipping', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'tax_tax', 'backend', 'Shipping & Taxes / Tax', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Tax', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'tax_del_title', 'backend', 'Shipping & Taxes / Delete title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete confirmation', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'tax_del_body', 'backend', 'Shipping & Taxes / Delete body', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Are you sure you want to delete selected location?', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_shipping_location', 'backend', 'Order / Shipping location', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Shipping location', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_similar', 'backend', 'Product / Similar products', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Similar products', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_invoice_details', 'backend', 'order_invoice_details', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Invoice details', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_create_invoice', 'backend', 'order_create_invoice', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Create Invoice', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_find_invoices', 'backend', 'order_find_invoices', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Find Invoices', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblInstallJs1_1', 'backend', 'Install / Step 1', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Step 1 (Required)', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblInstallJs1_2', 'backend', 'Install / Step 2', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Step 2 (Optional) - for SEO purposes and better ranking you need to put next meta tag into the HEAD part of your page', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblInstallJs1_3', 'backend', 'Install / Step 3', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Step 3 (Optional) - for SEO purposes and better ranking you need to create a .htaccess file (or update existinig one) with data below. Put the file in the same folder as your webpage.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblInstallJs1_title', 'backend', 'Install / Title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Install instructions', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblInstallJs1_body', 'backend', 'Install / Body', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Copy the code below and put it on the web page where you want shopping cart to appear. If you have a multi language shopping cart you can select the default language and to hide the language selector.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblInstallConfig', 'backend', 'Install / Config', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Language and Categories', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblInstallConfigLocale', 'backend', 'Install / Locale', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Language', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_register', 'frontend', 'Menu Register', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Register', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_login', 'frontend', 'Menu Login', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Login', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_forgot', 'frontend', 'Menu Forgot password', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Forgot password', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_logout', 'frontend', 'Menu Logout', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Logout', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_profile', 'frontend', 'Menu Profile', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Profile', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_favs', 'frontend', 'Menu Favorites', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Favorites', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_search', 'frontend', 'Search products', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'SEARCH PRODUCTS', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_cart', 'frontend', 'Menu My Bag', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'My Cart', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_checkout', 'frontend', 'Menu Checkout', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Checkout', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_item', 'frontend', 'Item (singular)', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Item', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_items', 'frontend', 'Item (plural)', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Items', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_all', 'frontend', 'All', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_select_category', 'frontend', 'Select category', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '[Select category]', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_shopping_cart', 'frontend', 'Shopping Cart', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Shopping Cart', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_empty_shopping_cart', 'frontend', 'Empty Shopping Cart', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Your shopping cart is empty', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_back_products', 'frontend', 'Back to Products', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Back to Products', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_total', 'frontend', 'Total', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Total', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_tax', 'frontend', 'Tax', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Tax', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_shipping', 'frontend', 'Shipping', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Shipping', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_shipping_location', 'frontend', 'Shipping Location', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Shipping Location', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_insurance', 'frontend', 'Insurance', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Insurance', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_choose_location', 'frontend', 'Choose location', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '-- Choose location --', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_promo_code', 'frontend', 'Promo Code', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Promo Code', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_discount', 'frontend', 'Discount', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Discount', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_apply_code', 'frontend', 'Apply code', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Apply code', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_sub_total', 'frontend', 'Sub Total', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Sub Total', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_up', 'frontend', 'Up', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'up', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_down', 'frontend', 'Down', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'down', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_quantity', 'frontend', 'Quantity', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Quantity', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_product', 'frontend', 'Product', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Product', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_price', 'frontend', 'Price', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Price', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_product_price', 'frontend', 'Product/Price', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Product/Price', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_quantity_total', 'frontend', 'Quantity/Total', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Quantity/Total', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_placeholder_email', 'frontend', 'Type your email address', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Type your email address', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_placeholder_password', 'frontend', 'Type your password', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Type your password', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_placeholder_name', 'frontend', 'Type your name', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Type your name', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_placeholder_phone', 'frontend', 'Type your phone', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Type your phone', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_placeholder_url', 'frontend', 'Type your website', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Type your website', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_preview_order', 'frontend', 'Preview order', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Preview order', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_back', 'frontend', 'Back', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Back', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_placeholder_b_name', 'frontend', 'Type your billing name', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Type your billing name', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_placeholder_b_city', 'frontend', 'Type your billing city', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Type your billing city', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_placeholder_b_state', 'frontend', 'Type your billing state', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Type your billing state', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_placeholder_b_zip', 'frontend', 'Type your billing zip', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Type your billing zip', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_placeholder_b_address_1', 'frontend', 'Type your billing address 1', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Type your billing address 1', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_placeholder_b_address_2', 'frontend', 'Type your billing address 2', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Type your billing address 2', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_placeholder_s_name', 'frontend', 'Type your shipping name', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Type your shipping name', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_placeholder_s_city', 'frontend', 'Type your shipping city', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Type your shipping city', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_placeholder_s_state', 'frontend', 'Type your shipping state', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Type your shipping state', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_placeholder_s_zip', 'frontend', 'Type your shipping zip', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Type your shipping zip', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_placeholder_s_address_1', 'frontend', 'Type your shipping address 1', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Type your shipping address 1', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_placeholder_s_address_2', 'frontend', 'Type your shipping address 2', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Type your shipping address 2', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_placeholder_cc_number', 'frontend', 'Type your credit card number', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Type your credit card number', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_placeholder_cc_code', 'frontend', 'Type your credit card security code', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Type your credit card security code', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_placeholder_notes', 'frontend', 'Type any additional notes', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Type any additional notes', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_my_favorites', 'frontend', 'My favorites', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'My favorites', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_not_available', 'frontend', 'Not available', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'N/A', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_out_of_stock', 'frontend', 'Out of stock', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Out of stock', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_empty_favs', 'frontend', 'Empty Favorites', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Empty Favorites', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_favs_not_found', 'frontend', 'Your favorite list is empty', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Your favorite list is empty', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_forgot_password', 'frontend', 'Forgot password', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Forgot password', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_send', 'frontend', 'Send', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_forgot_note', 'frontend', 'Forgot password note', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Morbi dignissim est ut odio aliquam varius. Quisque pellentesque pharetra adipiscing. Mauris tempus, augue vel tristique sollicitudin, neque tellus faucibus nibh, eu scelerisque quam elit non augue. Vestibulum cursus lobortis elementum. Maecenas semper eros molestie euismod sodales.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_login_note', 'frontend', 'Login note', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Sed id urna sed arcu euismod dictum in in lectus. Mauris a felis sit amet nibh ultrices aliquet. Donec imperdiet molestie euismod. Quisque fringilla nunc id justo vehicula, eget accumsan est tincidunt. Aenean tincidunt, purus vitae semper egestas.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_edit_order', 'frontend', 'Edit order', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Edit order', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_confirm_procees', 'frontend', 'Confirm & Process order', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Confirm & Process order', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_select_extra', 'frontend', 'Select extra', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '--- Select ---', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_add_to_favs', 'frontend', 'Add to Favorites', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add to Favorites', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_send_to_friend', 'frontend', 'Send to Friend', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send to Friend', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_s2f_your_email', 'frontend', 'Your Email address', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Your Email address', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_s2f_your_name', 'frontend', 'Your Name', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Your Name', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_s2f_friend_name', 'frontend', 'Friend Name', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Friend Name', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_s2f_friend_email', 'frontend', 'Friend Email address', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Friend Email address', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_cancel', 'frontend', 'Cancel', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Cancel', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_description', 'frontend', 'Description', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Description', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_similar', 'frontend', 'Similar Products', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Similar Products', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_product_not_found', 'frontend', 'Product not found', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Product not found', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_buy_now', 'frontend', 'Buy Now', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Buy Now', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_view_details', 'frontend', 'View Details', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'View Details', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_products_not_found', 'frontend', 'No products found', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No products found', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_prev', 'frontend', 'Previous', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Prev', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_next', 'frontend', 'Next', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Next', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_profile_note', 'frontend', 'Profile note', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Mauris sed massa massa. Aenean diam nisl, iaculis quis pulvinar eget, commodo vitae sem. Quisque et ipsum in ipsum hendrerit vestibulum. Nam ullamcorper pharetra nunc ac convallis. Fusce porta vel nisl sit amet congue. Vestibulum at commodo justo. Morbi pharetra vestibulum ultrices.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_save_changes', 'frontend', 'Save changes', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Save changes', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_register_note', 'frontend', 'Register note', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras imperdiet venenatis dignissim. In tellus orci, condimentum id nulla eget, posuere ornare nisl. Nunc tempor, nibh at euismod tincidunt, arcu odio consectetur magna, at dictum justo libero a arcu. Donec fringilla dui ut enim imperdiet, in euismod neque pulvinar. Aliquam vulputate justo ligula, et euismod massa dictum quis. Proin lacinia urna vitae felis pharetra, id cursus ipsum auctor.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_add_to_cart', 'frontend', 'Add to Cart', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add to Cart', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_100', 'frontend', 'System / Send to Friend / Missing or empty params', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Missing or empty parameters.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_101', 'frontend', 'System / Send to Friend / Email not sent', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Email has not been sent.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_200', 'frontend', 'System / Send to Friend / Email sent', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Email has been sent.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_201', 'frontend', 'System / Account / Successful logout', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You''ve been successfully loged out.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_202', 'frontend', 'System / Favs / Product added', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Product has been added to favorites.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_203', 'frontend', 'System / Favs / Product removed', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Product has been removed from the favorites.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_102', 'frontend', 'System / Favs / Product not removed', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Product has not been removed from the favorites.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_204', 'frontend', 'System / Favs / Favs emptied', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Favorite list has been emptied.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_103', 'frontend', 'System / Favs / Favs not emptied', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Favorite list has not been emptied.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_104', 'frontend', 'System / Cart / Voucher code empty', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Voucher code couldn''t be empty.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_205', 'frontend', 'System / Cart / Voucher removed', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Voucher has been removed.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_206', 'frontend', 'System / Cart / Product added', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Product has been added to the cart.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_105', 'frontend', 'System / Cart / Product not added', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Product has not been added to the cart.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_207', 'frontend', 'System / Cart / Product removed', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Product has been removed from the cart.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_106', 'frontend', 'System / Cart / Product not removed', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Product has not been removed from the cart.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_208', 'frontend', 'System / Cart / Cart emptied', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Shopping cart has been emptied.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_107', 'frontend', 'System / Cart / Cart not emptied', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Shopping cart has not been emptied.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_108', 'frontend', 'System / Cart / Cart not updated', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Shopping cart has not been updated.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_209', 'frontend', 'System / Cart / Cart updated', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Shopping cart has been updated.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_109', 'frontend', 'System / Order / Missing parameters', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Missing parameters.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_110', 'frontend', 'System / Order / Missing or wrong captcha', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Missing or wrong captcha.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_111', 'frontend', 'System / Order / Customer invalid data', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Customer info contains invalid data.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_112', 'frontend', 'System / Order / Email taken or wrong pswd', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Email is already taken (password doesn''t match).', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_113', 'frontend', 'System / Order / Client not added', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Creating client failed.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_114', 'frontend', 'System / Order / Invalid data', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order contains invalid data.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_115', 'frontend', 'System / Order / Empty cart', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Empty cart.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_116', 'frontend', 'System / Order / Stocks in cart not in DB', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Stocks in cart not found into the database.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_117', 'frontend', 'System / Order / Stocks in cart not equal', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Stocks in cart not equal.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_118', 'frontend', 'System / Order / Stock qty not enough', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Stock qty not enough.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_210', 'frontend', 'System / Order / Order stored', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order has been stored.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_119', 'frontend', 'System / Order / Order not stored', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order has not been stored.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_211', 'frontend', 'System / Order / Checkout submitted', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Checkout form has been submitted.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_120', 'frontend', 'System / Login / Invalid data', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Data is not valid.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_121', 'frontend', 'System / Login / Account not found', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Account not found.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_122', 'frontend', 'System / Login / Password doesn''t match', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Password doesn''t match.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_212', 'frontend', 'System / Login / Success', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You''ve been successfully loged in.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_123', 'frontend', 'System / Forgot / Invalid data', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Data is not valid.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_124', 'frontend', 'System / Forgot / Account not found', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Account not found.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_125', 'frontend', 'System / Forgot / Password not sent', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Password has not been sent successful.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_213', 'frontend', 'System / Forgot / Password sent', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Password has been sent successful.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_126', 'frontend', 'System / Profile / Invalid data', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Data is not valid.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_127', 'frontend', 'System / Profile / Email taken', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Email is already taken.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_214', 'frontend', 'System / Profile / Updated', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Profile has been updated.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_128', 'frontend', 'System / Register / Captcha not match', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Captcha is not set or doesn''t match.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_129', 'frontend', 'System / Register / Invalid data', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Data is not valid.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_130', 'frontend', 'System / Register / Email taken', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Email is already taken.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_131', 'frontend', 'System / Register / Proccess failed', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Registration has not been successful.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_215', 'frontend', 'System / Register / Registered without email', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You''ve been registered, but confirmation email has not been sent for some reason.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_216', 'frontend', 'System / Register / Registered with email', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You''ve been registered.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menuCountries', 'backend', 'Menu Countries', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Countries', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblCountry', 'backend', 'Country', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Country', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblDeleteConfirmation', 'backend', 'Delete confirmation', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Are you sure you want to delete selected records?', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblDeleteSelected', 'backend', 'Delete selected', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete selected', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'tabEmails', 'backend', 'Tab Emails', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Emails', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'tabSms', 'backend', 'Tab SMS', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'SMS', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoNotificationsEmailTitle', 'backend', 'Infobox / Email notifications title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Email notifications', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoNotificationsEmailBody', 'backend', 'Infobox / Email notifications body', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Below you can edit the email message which will be sent out.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_email_new_order', 'backend', 'Options / New order received', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'New order received', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_email_password_reminder', 'backend', 'Notifications / Password reminder', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Password reminder', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_subject', 'backend', 'Notifications / Subject', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Subject', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_body_forgot_password', 'backend', 'Notifications / Body', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Body
<br />
<br />Available tokens:
<br />{Name}
<br />{Password}', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoNotificationsSmsBody', 'backend', 'Infobox / Sms notifications body', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Different SMS notifications will be sent when various events happen. You can edit each of the Users and set which SMS messages to receive. Under SMS tab you need to input your API key for our SMS gateway.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoNotificationsSmsTitle', 'backend', 'Infobox / Sms notifications title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Sms notifications', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_attr_group_create', 'backend', 'Add attribute group', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add attribute group', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_attr_group_update', 'backend', 'Update attribute group', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update attribute group', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_attr_update', 'backend', 'Update attribute', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update attribute', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_attr_create', 'backend', 'Add attribute', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add attribute value', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_attr_group_name', 'backend', 'Attributes set name', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Attributes set name', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_attr_name', 'backend', 'Attribute value', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Attribute value', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_attr_group_delete', 'backend', 'Delete attribute group', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete attribute group', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_attr_group_delete_body', 'backend', 'Delete attribute group text', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Are you sure you want to delete selected attribute group?', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_attr_delete_body', 'backend', 'Delete attribute text', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Are you sure you want to delete selected attribute?', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_attr_erase', 'backend', 'Delete attribute', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete attribute', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menuNotifications', 'backend', 'Menu Notifications', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Notifications', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_email_new_registration', 'backend', 'Notifications / New registration', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'New registration', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_body_new_registration', 'backend', 'Notifications / Body', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Body
<br />
<br />Available tokens:
<br />{Name}
<br />{Password}
<br />{Email}
<br />{URL}
<br />{Phone}', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'info_ARRAY_product_details_title', 'arrays', 'info_ARRAY_product_details_title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Details', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'info_ARRAY_product_details_body', 'arrays', 'info_ARRAY_product_details_body', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Enter product details below. You can assign a product to one or more categories (change categories under Options page, Categories tab) and to make it featured.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'info_ARRAY_product_digital_title', 'arrays', 'info_ARRAY_product_digital_title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Digital product', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'info_ARRAY_product_digital_body', 'arrays', 'info_ARRAY_product_digital_body', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'If you sell digital content (videos, mp3, images, files, etc..) you can use this page to upload the file and set various options for it. If file is too large you can upload it via FTP and under \"File path\" set full server path to it. Once an order for that file is made a confirmation email is sent to the customer. You can set when this link to expire so file cannot be downloaded any more.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'info_ARRAY_product_attr_title', 'arrays', 'info_ARRAY_product_attr_title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Attributes', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'info_ARRAY_product_attr_body', 'arrays', 'info_ARRAY_product_attr_body', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Using \"Attributes\" you can set different options for your products. For example, you may sell a t-shirt in 2 different sizes (XXL, M) and 3 colors (red, green, blue). So you need to create attribute set for size and then add the 2 different sizes as different attributes from that set. You can do that for color too. Then under the \"In stock\" tab you can set default image, Quantity and Price for each combination - Green XXL t-shirt, Green M t-shirt, Blue XXL t-shirt, Blue M t-shirt,... and so on.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'info_ARRAY_product_stock_title', 'arrays', 'info_ARRAY_product_stock_title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'In Stock', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'info_ARRAY_product_stock_body', 'arrays', 'info_ARRAY_product_stock_body', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Here you can set default image, available quantity and price for each combination of attributes that your product comes with. Click on the \"Choose image\" button to select one of the uploaded images, select the corresponding attributes and add quantity and price. You can add as many combinations as you need. Following the example from \"Attributes\" tab you need to do that for all possible size and color combination.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'info_ARRAY_product_photos_title', 'arrays', 'info_ARRAY_product_photos_title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Photos', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'info_ARRAY_product_photos_body', 'arrays', 'info_ARRAY_product_photos_body', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Upload as many images as you want for this product. You can resize, crop, rotate, watermark and compress the uploaded images. Drag & drop to change their order.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'info_ARRAY_product_extras_title', 'arrays', 'info_ARRAY_product_extras_title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Extras', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'info_ARRAY_product_extras_body', 'arrays', 'info_ARRAY_product_extras_body', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can add additional extras which can be purchased with this product. For example if you sell a mobile phone you can offer a case or a charger for it. You can create single extras (can be bought using a checkbox) or multiple extras (where you select the extra from a drop down).', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'info_ARRAY_product_similar_title', 'arrays', 'info_ARRAY_product_similar_title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Similar products', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'info_ARRAY_product_similar_body', 'arrays', 'info_ARRAY_product_similar_body', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Select similar products which will be offered to shoppers on the current product details page. Just start typing a product name and after the 3rd character available products will be shown. Select a product and it will appear on the products details page.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'info_ARRAY_product_history_title', 'arrays', 'info_ARRAY_product_history_title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Stock History', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'info_ARRAY_product_history_body', 'arrays', 'info_ARRAY_product_history_body', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Below you can see a history log for every change made to quantity or price under the \"In stock\" tab.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'category_products', 'backend', 'category_products', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Products', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'client_status', 'backend', 'client_status', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Status', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_132', 'frontend', 'System / Login / Access denied', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Access denied', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_tab_order', 'backend', 'order_tab_order', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_tab_client', 'backend', 'order_tab_client', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Client', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_tab_shipping', 'backend', 'order_tab_shipping', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Shipping & Billing', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_stock_tab', 'backend', 'product_stock_tab', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Stock', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_empty_shipping_location', 'frontend', 'Shipping location is not set', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Shipping location is not set', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_empty_checkout_form', 'frontend', 'Checkout form not submitted', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Checkout form not submitted', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AC10', 'arrays', 'error_titles_ARRAY_AC10', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Address book', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AC10', 'arrays', 'error_bodies_ARRAY_AC10', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Strore and organize your client''s addresses.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AC08', 'arrays', 'error_bodies_ARRAY_AC08', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Sorry but the client you''ve been looking for doesn''t exists.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AC07', 'arrays', 'error_bodies_ARRAY_AC07', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Sorry but the email address is already in use.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AC06', 'arrays', 'error_bodies_ARRAY_AC06', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Client has not been updated', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AC05', 'arrays', 'error_bodies_ARRAY_AC05', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Client has been updated', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AC02', 'arrays', 'error_bodies_ARRAY_AC02', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Client has not been added', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AC01', 'arrays', 'error_bodies_ARRAY_AC01', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Client has been added', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'info_ARRAY_product_details_add_body', 'arrays', 'info_ARRAY_product_details_add_body', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Enter product details below. You can assign a product to one or more categories (change categories under Options page, Categories tab) and to make it featured.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'info_ARRAY_product_details_add_title', 'arrays', 'info_ARRAY_product_details_add_title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Details', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AO10', 'arrays', 'error_titles_ARRAY_AO10', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order details', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AO11', 'arrays', 'error_titles_ARRAY_AO11', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Client details', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AO12', 'arrays', 'error_titles_ARRAY_AO12', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Shipping and billing details', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AO10', 'arrays', 'error_titles_ARRAY_AO10', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can change order details using the form below. If you add or remove a product click on the \"Recalculate the price\" button to calculate new price based on the new selection. At the bottom of the page you can view the invoice for the order and create new one if needed.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AO11', 'arrays', 'error_titles_ARRAY_AO11', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Find information about your client and their previous purchases.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AO12', 'arrays', 'error_titles_ARRAY_AO12', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Below you can see shipping and billing details for the order. Using your client''s address book you can easily change these details.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AO24', 'arrays', 'error_titles_ARRAY_AO24', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Shipping & Tax fee', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AO24', 'arrays', 'error_titles_ARRAY_AO24', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can add unlimited amount of locations. For each location you can enter shipping and tax fees. When clients purchase something they will select their location and the shipping and tax fees will be added to the order total amount.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_booking_status_ARRAY_1', 'arrays', 'front_booking_status_ARRAY_1', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Thank you. Your order has been made. [STAG]Start over[ETAG]', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_booking_status_ARRAY_11', 'arrays', 'front_booking_status_ARRAY_11', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Your order is saved. Please wait while redirect to secure payment processor webpage complete...', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AV10', 'arrays', 'error_titles_ARRAY_AV10', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Vouchers', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AV10', 'arrays', 'error_bodies_ARRAY_AV10', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Create vouchers and give discounts to your clients. You can create percent or fixed amount voucher codes. You can set period for each promo offer that you create. If you want to offer a voucher for specific products only, in the Products text box, just start typing a product name and after the 3rd character products will be shown.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AV01', 'arrays', 'error_bodies_ARRAY_AV01', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All the changes made to this voucher have been saved.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AV08', 'arrays', 'error_bodies_ARRAY_AV08', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Sorry but the voucher you''ve been looking for was not found.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AV06', 'arrays', 'error_bodies_ARRAY_AV06', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Sorry but the voucher has not been updated.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AV05', 'arrays', 'error_bodies_ARRAY_AV05', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All the changes made to this voucher has been saved.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AV02', 'arrays', 'error_bodies_ARRAY_AV02', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Sorry, but the voucher has not been added.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'dashboard_last_login', 'backend', 'Dashboard / Last login', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Last login', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'dashboard_last_orders', 'backend', 'Dashboard / Latest orders', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Latest orders', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'dashboard_last_clients', 'backend', 'Dashboard / Latest clients', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Latest clients', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'dashboard_stock', 'backend', 'Dashboard / Stock', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Stock', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'dashboard_orders_today', 'backend', 'Dashboard / Orders today', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'orders today', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'dashboard_order_today', 'backend', 'Dashboard / Order today', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'order today', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'dashboard_clients_today', 'backend', 'Dashboard / Clients today', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'clients today', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'dashboard_client_today', 'backend', 'Dashboard / Client today', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'client today', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblAll', 'backend', 'All', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AO21', 'arrays', 'error_titles_ARRAY_AO21', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'General options', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AO21', 'arrays', 'error_titles_ARRAY_AO21', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Use form below to manage the general options.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AO22', 'arrays', 'error_titles_ARRAY_AO22', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Payment options', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AO22', 'arrays', 'error_titles_ARRAY_AO22', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Use form below to manage the payment options.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AO23', 'arrays', 'error_titles_ARRAY_AO23', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Checkout form options', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AO23', 'arrays', 'error_titles_ARRAY_AO23', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Use form below to manage the checkout form options.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AC11', 'arrays', 'error_titles_ARRAY_AC11', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Category list', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AC11', 'arrays', 'error_bodies_ARRAY_AC11', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Use arrows below to re-order your categories.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AC12', 'arrays', 'error_titles_ARRAY_AC12', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add category', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AC12', 'arrays', 'error_bodies_ARRAY_AC12', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Use form below to add your custom category.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AC13', 'arrays', 'error_titles_ARRAY_AC13', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update category', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AC13', 'arrays', 'error_bodies_ARRAY_AC13', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Use form below to update your custom category.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AP11', 'arrays', 'error_titles_ARRAY_AP11', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Stock list', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AP11', 'arrays', 'error_bodies_ARRAY_AP11', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Use search field below to filter the stock you''re looking for. Price and quantity could be changed by clicked on them.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_select_product', 'backend', 'order_select_product', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Select a product', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AOR05', 'arrays', 'error_titles_ARRAY_AOR05', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order updated', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AOR05', 'arrays', 'error_bodies_ARRAY_AOR05', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order has been updated', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AOR08', 'arrays', 'error_titles_ARRAY_AOR08', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order not found.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AOR08', 'arrays', 'error_bodies_ARRAY_AOR08', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order your''re looking for is missing.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AOR07', 'arrays', 'error_titles_ARRAY_AOR07', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add a Stock', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AOR07', 'arrays', 'error_bodies_ARRAY_AOR07', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'In the below table are listed all current stocks for selected product. After chosing desired stock(s) click on Add button.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_all_list', 'backend', 'order_all_list', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All orders', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'gridEmptyDate', 'backend', 'Grid / Empty date', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '(empty date)', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'gridInvalidDate', 'backend', 'Grid / Invalid date', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '(invalid date)', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'gridInvalidDatetime', 'backend', 'Grid / Invalid datetime', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '(invalid date/time)', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'gridEmptyDatetime', 'backend', 'Grid / Empty datetime', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '(empty date/time)', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'dashboard_products', 'backend', 'Dashboard / Products', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'products', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'dashboard_product', 'backend', 'Dashboard / Product', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'product', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'save_to_address_book', 'frontend', 'Frontend / Save to address book', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Save to address book', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_price_from', 'frontend', 'front_price_from', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'from', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_seo_url', 'backend', 'Options / Seo URLs', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Use Seo-friendly URLs', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_image', 'backend', 'Product / Image', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Image', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'tax_free', 'backend', 'Shipping & Taxes / Free shipping', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Free shipping', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_disable_orders', 'backend', 'Options / Disable orders', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Disable orders', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_disable_orders_text', 'backend', 'Options / Disable orders', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Check if you want to disable placing orders and use the cart in catalogue mode', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'validate_ARRAY_captcha', 'arrays', 'Validate / Captcha', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Captcha is required', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'validate_ARRAY_password', 'arrays', 'Validate / Password', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Password is required', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'validate_ARRAY_name', 'arrays', 'Validate / Name', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Name is required', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'validate_ARRAY_email', 'arrays', 'Validate / Email', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Email is required', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'validate_ARRAY_email_invalid', 'arrays', 'Validate / Email invalid', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Email is invalid', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'validate_ARRAY_captcha_wrong', 'arrays', 'Validate / Captcha is wrong', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Captcha is wrong', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'validate_ARRAY_voucher', 'arrays', 'Validate / Voucher', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Promo code is required', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'validate_ARRAY_tax', 'arrays', 'Validate / Shipping Location', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Shipping location is required', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'validate_ARRAY_country', 'arrays', 'Validate / Country', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Country is required', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'validate_ARRAY_city', 'arrays', 'Validate / City', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'City is required', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'validate_ARRAY_state', 'arrays', 'Validate / State', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'State is required', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'validate_ARRAY_zip', 'arrays', 'Validate / Zip', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Zip is required', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'validate_ARRAY_address_1', 'arrays', 'Validate / Address 1', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Address 1 is required', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'validate_ARRAY_address_2', 'arrays', 'Validate / Address 2', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Address 2 is required', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'validate_ARRAY_notes', 'arrays', 'Validate / Notes', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Notes is required', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'validate_ARRAY_payment', 'arrays', 'Validate / Payment method', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Payment method is required', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'validate_ARRAY_terms', 'arrays', 'Validate / Terms & Conditions', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You have to agree with terms', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_authorize_hash', 'backend', 'Options / Authorize.net hash value', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Authorize.net MD5 hash value', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menuConfirmation', 'backend', 'Menu Confirmation', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Confirmation', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AO25', 'arrays', 'error_titles_ARRAY_AO25', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '<p>Email notifications will be sent to people who make an order after checkout form is completed or/and payment is made. Different messages are sent to the super admin. Using tokens you can customize all the messages.</p><br />
<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
  <tr>
    <td width=\"50%\" valign=\"top\"><p>{ClientName} - customer''s name<br />
 {ClientEmail} - customer''s e-mail<br />
{ClientPassword} - customer''s password<br />
{ClientPhone} -  customer''s phone number<br />
{ClientURL} - customer''s website<br />
{BillingName} - customer''s billing name<br />
 {BillingAddress1} - billing address 1<br />
{BillingAddress2} - billing address 2<br />
{BillingCity} - billing city<br />
{BillingState} - billing state<br />
{BillingZip} - billing zip code<br />
{BillingCountry} - billing country<br />
{ShippingName} - customer''s shipping name<br />
{ShippingAddress1} - shipping address 1<br />
{ShippingAddress2} - shipping address 2<br />
{ShippingCity} - shipping city<br />
{ShippingState} - shipping state<br />
{ShippingZip} - shipping zip code; <br />
    {ShippingCountry} - shipping country</p></td>
    <td width=\"50%\" valign=\"top\"> {Notes} - additional notes<br />
{CCType} - CC type<br />
{CCNum} - CC number<br />
{CCExpMonth} - CC exp.month<br />
{CCExpYear} - CC exp.year<br />
{CCSec} - CC sec. code<br />
{PaymentMethod} - selected payment method<br />
{Insurance} -  insurance fee<br />
{Shipping} - shipping fee<br />
{Tax} - tax fee<br />
{Price} - price<br />
{Total} - total amount<br />
{Discount} - discount<br />
{Voucher} - promo code<br />
{Products} - list with purchased products<br />
{OrderUUID} - Order number<br />
{DigitalDownload} - Digital products download link<br />
{StoreName} - Store name<br/>
{FriendName} - your friend''s name<br/>
{FriendEmail} - your friend''s email<br/>
{YourName} - your name<br/>
{YourEmail} - your email<br/>
{URL} - url to product detail
</td>
  </tr>
</table>
<p> </p>', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AO25', 'arrays', 'error_titles_ARRAY_AO25', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Confirmation', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'confirmation_body', 'backend', 'Confirmation / Email body', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Email body', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'confirmation_subject', 'backend', 'Confirmation / Email subject', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Subject', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'confirmation_admin_payment', 'backend', 'Confirmation / Admin payment title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Admin - payment confirmation email', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'confirmation_admin_confirmation', 'backend', 'Confirmation / Admin confirmation title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Admin - order confirmation email', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'confirmation_client_payment', 'backend', 'Confirmation / Client payment title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Client - payment confirmation email', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'confirmation_client_confirmation', 'backend', 'Confirmation / Client confirmation title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Client - order confirmation email', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menuTerms', 'backend', 'Menu Terms', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Terms', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AO26', 'arrays', 'error_titles_ARRAY_AO26', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Enter order terms and conditions. You can also include a link to external web page where your terms and conditions page is.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AO26', 'arrays', 'error_titles_ARRAY_AO26', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Terms and Conditions', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'terms_content', 'backend', 'Options / Order terms content', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order terms content', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'terms_url', 'backend', 'Options / Order terms URL', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order terms URL', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'digital_status_ARRAY_1', 'arrays', 'Digital / Missing params', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Missing or invalid parameters', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'digital_status_ARRAY_2', 'arrays', 'Digital / Order not found', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order not found', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'digital_status_ARRAY_3', 'arrays', 'Digital / Order not paid', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order not paid', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'digital_status_ARRAY_4', 'arrays', 'Digital / Digital products not found', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order doesn''t contain any digital product', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'digital_status_ARRAY_5', 'arrays', 'Digital / Digital products expired', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All digital products are expired', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menuSeo', 'backend', 'Menu SEO', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'SEO', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AO30', 'arrays', 'error_titles_ARRAY_AO30', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'SEO Optimization', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AO30', 'arrays', 'error_titles_ARRAY_AO30', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'To better optimize your shopping cart please follow the steps below', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblInstallSeo_1', 'backend', 'Install / SEO Step 1', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Step 1. Webpage where your front end shopping cart is', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblInstallSeo_2', 'backend', 'Install / SEO Step 2', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Step 2. Put the meta tag below between &lt;head&gt; and &lt;/head&gt;tags on your web page', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblInstallSeo_3', 'backend', 'Install / SEO Step 3', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Step 3. (SAME DOMAIN INSTALL ONLY) Create .htaccess file (or update existing one) in the folder where your web page is and put the data below in it', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblInstallConfigHide', 'backend', 'Install / Config hide', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Hide language selector', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_send_confirm', 'backend', 'Order / Send order confirmation', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send order confirmation', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_send_payment', 'backend', 'Order / Send payment confirmation', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send payment confirmation', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_payment_title', 'backend', 'Order / Payment confirmation', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Payment confirmation', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_confirm_title', 'backend', 'Order / Order confirmation', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order confirmation', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_send_subject', 'backend', 'Order / Confirmation subject', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Subject', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_send_body', 'backend', 'Order / Confirmation body', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Body', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_unit_price', 'backend', 'Order / Unit price', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Unit price', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_current_stock', 'backend', 'Order / Current stock', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Current stock', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_terms_title', 'frontend', 'Checkout / Terms & Conditions', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Terms & Conditions', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'confirm_sms_admin', 'backend', 'Confirmation / Admin - order confirmation sms', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Admin - order confirmation sms', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'payment_sms_admin', 'backend', 'Confirmation / Admin - payment confirmation sms', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Admin - payment confirmation sms', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'sms_body', 'backend', 'Confirmation / SMS content', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'SMS content', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_extra_copy', 'backend', 'product_extra_copy', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Copy extras from another product', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_extra_copy_title', 'backend', 'product_extra_copy_title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Copy extras from another product', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_layout', 'backend', 'Options / Layout', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Layout', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_v_sku', 'backend', 'product_v_sku', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'There is another product with such ID.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_products_per_page', 'backend', 'Options / Products per page', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Products per page', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_stock_print_all', 'backend', 'product_stock_print_all', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Print all', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_stock_print_selected', 'backend', 'product_stock_print_selected', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Print selected', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_bf_c_name', 'backend', 'Options / Name (Client)', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Name (Client)', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_bf_c_phone', 'backend', 'Options / Phone (Client)', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Phone (Client)', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_bf_c_url', 'backend', 'Options / Website (Client)', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Website (Client)', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'validate_ARRAY_phone', 'arrays', 'Validate / Phone', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Phone is required', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'validate_ARRAY_url', 'arrays', 'Validate / Website', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Website is required', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'plugin_backup_size', 'backend', 'Plugin / Size', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Size', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'plugin_backup_sizeXXXXXX', 'backend', 'Plugin / Size', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'SizeXXXX', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'plugin_invoice_email_invoice', 'backend', 'Plugin / Email invoice', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Email invoice', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'plugin_country_revert_status', 'backend', 'Plugin / Revert status', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Revert status', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblNoImageUploaded', 'backend', 'Label / No image uploaded', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'There are no images uploaded for this product. You need to upload at least one image per product.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_send_content', 'frontend', 'Label / Send to friend content', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Dear %1$s,
			
Your friend %3$s thinks this may be interested you:
%5$s', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblInstallCategory', 'backend', 'Label / Category', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Category', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblAllCatgories', 'backend', 'Label / All categories', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All categories', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblNoAddressBook', 'backend', 'Label / No address book', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'There are no addresses. Manage client''s address book [STAG]here[ETAG].', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'btnRecalcualteThePrice', 'backend', 'Button / Recalculate the price', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Recalculate the price', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'btnAddProduct', 'backend', 'Button / Add product', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add product', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'dashboard_new_clients_today', 'backend', 'Label / new clients today', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'new clients today', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'dashboard_new_client_today', 'backend', 'Label / new client today', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'new client today', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblTotalOrders', 'backend', 'Label / total orders', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'total orders', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblTotalOrder', 'backend', 'Label / total order', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'total order', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblViewAll', 'backend', 'Label / view all', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'view all', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblBrief', 'backend', 'Label / Brief', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Brief', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblNewOrders', 'backend', 'Label / new orders', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'new orders', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblNewOrder', 'backend', 'Label / new order', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'new order', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblPendingOrder', 'backend', 'Label / pending order', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'pending order', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblPendingOrders', 'backend', 'Label / pending orders', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'pending orders', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'dashboard_active_products', 'backend', 'Dashboard / active products', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'active products', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'dashboard_active_product', 'backend', 'Dashboard / active product', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'active product', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'dashboard_product_out_of_stock', 'backend', 'Dashboard / product out of stock', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'product out of stock', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'dashboard_products_out_of_stock', 'backend', 'Dashboard / products out of stock', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'products out of stock', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'dashboard_active_products_out_of_stock', 'backend', 'Dashboard / active products out of stock', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'active products out of stock', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'dashboard_active_product_out_of_stock', 'backend', 'Dashboard / active product out of stock', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'active product out of stock', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'dashboard_active_vouchers', 'backend', 'Dashboard / active voucher', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Active Vouchers at the moment', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'dashboard_categories_in_use', 'backend', 'Dashboard / categories in use', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'categories in use', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'dashboard_category_in_use', 'backend', 'Dashboard / category in use', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'category in use', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'dashboard_languages_in_use', 'backend', 'Dashboard / Languages in use', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Languages in use', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'dashboard_registration_date', 'backend', 'Dashboard / Registration date', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Registration date', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'dashboard_date_time', 'backend', 'Dashboard / Date & time', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Date & time', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'dashboard_status', 'backend', 'Dashboard / Status', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Status', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'dashboard_payment', 'backend', 'Dashboard / Payment', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Payment', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'dashboard_client', 'backend', 'Dashboard / Client', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Client', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'dashboard_total', 'backend', 'Dashboard / Total', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Total', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'dashboard_products_ordered', 'backend', 'Dashboard / Products ordered', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Products ordered', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'dashboard_quantity', 'backend', 'Dashboard / Quantity', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Quantity', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'dashboard_voucher', 'backend', 'Dashboard / Voucher', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Voucher', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'dashboard_none', 'backend', 'Dashboard / none', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'none', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblPositiveNumber', 'backend', 'Label / Please enter positive number', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please enter positive number.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_email_send_to_friend', 'backend', 'Options / Send to friend', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send to friend', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'send_to_friend_tokens', 'backend', 'Label / Send to friend tokens', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '<u>Available tokens:</u><br/>
{FriendName}<br/>
{FriendEmail}<br/>
{YourName}<br/>
{YourEmail}<br/>
{URL}<br/>', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblViewStock', 'backend', 'Label / View Stock', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'View Stock', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'sc_delete_confirmation', 'backend', 'Grid / Delete confirmation', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '[PRODUCT] has been purchased [X] times. Deleting the product will also remove it from all these orders. Are you sure you want to delete it or make it inactive?', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'delete_product_confirmation', 'backend', 'Label / Are you sure you want to delete the product?', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Are you sure you want to delete the product?', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblStoreName', 'frontend', 'Label / My Store', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'My Store', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblInstallJs2_1', 'backend', 'Label / Step 2.1 (Optional)', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Step 2.1 (Optional) Add the following code at the very top of your web page', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblInstallJs2_2', 'backend', 'Label / Step 2.2 (Optional)', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Step 2.2 (Optional) Add the following code in the head tag of your web page for UTF and mobile view support', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_133', 'frontend', 'System / Voucher not found', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Voucher not found', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_na', 'frontend', 'Label / n/a', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'n/a', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'titles_ARRAY_AP09', 'arrays', 'titles_ARRAY_AP09', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Image could not be uploaded', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AP09', 'arrays', 'errors_ARRAY_AP09', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Product image could not be uploaded because the file is too big. Maximum allowed file size is {SIZE}. Please, upload smaller file.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'titles_ARRAY_AP10', 'arrays', 'titles_ARRAY_AP10', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Upload error', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AP10', 'arrays', 'errors_ARRAY_AP10', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'The product has been updated, but file could not be uploaded successfully.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'titles_ARRAY_AP11', 'arrays', 'titles_ARRAY_AP11', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'File not existing', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'errors_ARRAY_AP11', 'arrays', 'errors_ARRAY_AP11', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'The product has been updated, but file given from the file path does not exist.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'btnPlusAddProduct', 'backend', 'Button / Add product', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add product', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'btnAddCategory', 'backend', 'Button / Add category', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add category', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblEmailNotifications', 'backend', 'Label / Email notifications', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Email notifications', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'btnPlusAddClient', 'backend', 'Button / Add client', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add client', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoClientsTitle', 'backend', 'Infobox / List of clients', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'List of clients', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoClientsDesc', 'backend', 'Infobox / List of clients', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can find below the list of clients. Click on the Pencil icon on the corresponding entry to view client details.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_tab_invoices', 'backend', 'Tab / Invoices', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Invoices', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoOrdersTitle', 'backend', 'Infobox / List of orders', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'List of orders', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoOrdersDesc', 'backend', 'Infobox / List of orders', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can find below the list of orders made. Click on the pencil icon on the corresponding entry to view more details of a specific order.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_theme', 'backend', 'Options / Theme', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Theme', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblInstallTheme', 'backend', 'Label / Choose theme', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Choose theme', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblCurrentlyInUse', 'backend', 'Label / Currently in use', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Currently in use', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'btnUseThisTheme', 'backend', 'Label / Use this theme', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Use this theme', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'option_themes_ARRAY_1', 'arrays', 'option_themes_ARRAY_1', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Theme 1', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'option_themes_ARRAY_2', 'arrays', 'option_themes_ARRAY_2', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Theme 2', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'option_themes_ARRAY_3', 'arrays', 'option_themes_ARRAY_3', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Theme 3', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'option_themes_ARRAY_4', 'arrays', 'option_themes_ARRAY_4', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Theme 4', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'option_themes_ARRAY_5', 'arrays', 'option_themes_ARRAY_5', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Theme 5', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'option_themes_ARRAY_6', 'arrays', 'option_themes_ARRAY_6', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Theme 6', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'option_themes_ARRAY_7', 'arrays', 'option_themes_ARRAY_7', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Theme 7', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'option_themes_ARRAY_8', 'arrays', 'option_themes_ARRAY_8', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Theme 8', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'option_themes_ARRAY_9', 'arrays', 'option_themes_ARRAY_9', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Theme 9', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'option_themes_ARRAY_10', 'arrays', 'option_themes_ARRAY_10', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Theme 10', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblInstallCode', 'backend', 'Label / Install code', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Install code', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblInstallCodeDesc', 'backend', 'Label / Copy the code below and put it on your web page. It will show the front end booking engine. ', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Copy the code below and put it on your web page. It will show the front end booking engine. ', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoThemeTitle', 'backend', 'Infobox / Preview front end', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Preview front end', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoThemeDesc', 'backend', 'Infobox / Preview front end', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'There are multiple color schemes available for the front end. Click on each of the thumbnails below to preview it. Click on \"Use this theme\" button for the theme you want to use.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblAddCategoryText', 'backend', 'Label / No categories found. Add category here', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No categories found. Add category {STAG}here{ETAG}.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoProductsTitle', 'backend', 'Infobox / List of products', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'List of products', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoProductsDesc', 'backend', 'Infobox / List of products', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can find below the list of products defined. Click on the pencil icon on the corresponding entry to view more details of a specific product. You can also click on the button \"+ Add product\" to add new product.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblProductsOrderedToday', 'backend', 'Label / products ordered today', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'products ordered today', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblProductOrderedToday', 'backend', 'Label / product ordered today', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'product ordered today', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblDashProductsOrderedToday', 'backend', 'Label / Products ordered today', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Products ordered today', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menuReport', 'backend', 'Menu / Report', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Report', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoReportTitle', 'backend', 'Infobox / Report', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Report', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoReportDesc', 'backend', 'Infobox / Report', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Select date range and view a report for all completed orders during that period.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'btnReport', 'backend', 'Button / Report', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Report', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblFromDate', 'backend', 'Label / From date', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'From date', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblToDate', 'backend', 'Label / To date', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'To date', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblUpTotalOrders', 'backend', 'Label / Total orders', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Total orders', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblTotalAmount', 'backend', 'Label / Total amount', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Total amount', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblUniqueClients', 'backend', 'Label / Unqiue clients', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Unique clients', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblFirstTimeClients', 'backend', 'Label / First time clients', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'First time clients', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblAvgOrderAmount', 'backend', 'Label / Average order amount', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Average order amount', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblMinOrderAmount', 'backend', 'Label / Min order amount', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Min order amount', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblMaxOrderAmount', 'backend', 'Label / Max order amount', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Max order amount', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblAverageProductsPerOrder', 'backend', 'Label / Average products per order', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Average products per order', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblMinProductsPerOrder', 'backend', 'Label / Min products per order', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Min products per order', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblMaxProductsPerOrder', 'backend', 'Label / Max products per order', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Max products per order', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblMostPopularProduct', 'backend', 'Label / Most popular product', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Most popular product', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblSoldTimes', 'backend', 'Label / Sold times', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'sold {NUM} times', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_order_completed', 'frontend', 'Label / Order completed', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order completed', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblSummary', 'backend', 'Lable / Summary', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Summary', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblNoExtrasFound', 'backend', 'Lable / No extras found.', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No extras found.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblNoAttributesFound', 'backend', 'Lable / No attributes found.', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No attributes found.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblProductsPrice', 'backend', 'Label / product(s) price', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'product(s) price', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblDashOrders', 'backend', 'Label / Order(s)', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order(s)', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoUsersTitle', 'backend', 'Infobox / List of users', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'List of users', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoUsersDesc', 'backend', 'Infobox / List of users', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can find below the list of users. To view or edit user information, click on the Pencil icon on the corresponding entry.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoAddUserTitle', 'backend', 'Infobox / Add new user', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add new user', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoAddUserDesc', 'backend', 'Infobox / Add new user', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Fill in the form below and click \"Save\" button to add new user.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoUpdateUserTitle', 'backend', 'Infobox / Update user', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update user', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoUpdateUserDesc', 'backend', 'Infobox / Update user', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can make any change on the form and click \"Save\" button to edit user information.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoCreateClientTitle', 'backend', 'Infobox / Add new client', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add new client', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoCreateClientDesc', 'backend', 'Infobox / Add new client', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please fill in the form below and click on \"Save\" button to add new client. There are two sections including \"General info\" and \"Address book\". For the \"Address book\", you can add as many as address books you want for each client.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoUpdateClientTitle', 'backend', 'Infobox / Update client', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update client', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoUpdateClientDesc', 'backend', 'Infobox / Update client', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can make any change on the form below and click \"Save\" button to edit the information of client.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoVouchersTitle', 'backend', 'Infobox / List of vouchers', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'List of vouchers', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoVouchersDesc', 'backend', 'Infobox / List of vouchers', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can find below the list of vouchers. In order to view more details of a specific voucher, click on the \"Pencil\" icon on the corresponding entry. If you want to add new voucher, click on the button \"+ Add voucher\".', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblInstallSeo_4', 'backend', 'Label / Install SEO', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Step 4. (CROSS-DOMAIN INSTALL ONLY) Create .htaccess file (or update existing one) in the folder where your web page is and put the data below in it', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_choose', 'frontend', 'Label / Choose', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Choose', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblDashNoProductsOrderedToday', 'backend', 'Label / No products ordered today', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No products ordered today', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblDashNoOrdersFound', 'backend', 'Label / No orders found', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No orders found', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblSameVoucherCode', 'backend', 'Label / ', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Voucher code is already in use.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblOutOfStock', 'backend', 'Label / Out of stock', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Out of stock', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_maximum_items', 'frontend', 'Label / Maximum Items', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Maximum {MAX} items can be bought for this product.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_field_required', 'frontend', 'Field ', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Field is required.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_134', 'frontend', 'System / Voucher code cannot be empty.', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Voucher code cannot be empty.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_135', 'frontend', 'System / Date cannot be empty.', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Date cannot be empty.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_136', 'frontend', 'System / Voucher code is out of date.', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Voucher code is out of date.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_137', 'frontend', 'System / Voucher code has been applied.', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Voucher code has been applied.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_history_price_before', 'backend', 'Label / Price before', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Price before', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_history_price_after', 'backend', 'Label / Price after', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Price after', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_history_attribute', 'backend', 'Label / Attribute', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Attribute', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_show_password', 'frontend', 'Label / Show password', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Show password', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_hide_password', 'frontend', 'Label / Hide password', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Hide password', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_btn_remove_discount', 'frontend', 'Button / Remove discount', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Remove discount', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblApplyDiscountFor', 'backend', 'Label / Apply discount for', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Apply discount for', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'apply_arr_ARRAY_total', 'arrays', 'apply_arr_ARRAY_total', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Products total amount', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'apply_arr_ARRAY_each', 'arrays', 'apply_arr_ARRAY_each', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Each product', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_select_attribute', 'frontend', 'Label / You need to select select product attribute first.', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You need to select select product attribute first.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblValidateTime', 'backend', 'Label / Validate time', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'End time must be greater than start time.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblValidateVoucherDateTime', 'backend', 'Label / Validate date time', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'From date/time must be greater than To date/time.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'system_217', 'frontend', 'Voucher code not applied.', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Voucher code not applied.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'gridActionEmptyTitle', 'backend', 'Grid / No records selected', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No records selected', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'gridActionEmptyBody', 'backend', 'Grid / No records selected', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You need to select at least a single record.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_extras', 'frontend', 'Label / Extras', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Extras', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_not_required_extras', 'frontend', 'Label / You can have also', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can have also', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblInstallUrl', 'backend', 'Label / Install URL', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'URL where your front-end shopping cart is installed', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblPagePrefix', 'backend', 'Label / Page prefix', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Page prefix', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblInstallOptions', 'backend', 'Label / Install options', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Options', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblMultiselectNoneSelectedText', 'backend', 'Label / Select options', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Select options', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblMultiselectCheckAll', 'backend', 'Label / Check all', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Check all', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblMultiselectUncheckAll', 'backend', 'Label / Uncheck all', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Uncheck all', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblMultiselectSelectedText', 'backend', 'Label / # selected', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '# selected', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoBookingsTitle', 'backend', 'Infobox / Order Options', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order Options', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoBookingsDesc', 'backend', 'Infobox / Order Options desc', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Use the form below to set your payment and order process options.', 'script');


INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'script_name', 'backend', 'Shopping Cart', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Shopping Cart', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'script_offline_payment', 'backend', 'script_offline_payment', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Offline payments', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'script_offline_payment_methods', 'backend', 'script_offline_payment_methods', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Offline Payment Methods', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'script_online_payment_gateway', 'backend', 'script_online_payment_gateway', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Online payments', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'script_preview_your_website', 'backend', 'script_preview_your_website', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Open in new window', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menuInstallPreview', 'backend', 'Menu / Install & Preview', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Install & Preview', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_sms_payment_message', 'backend', 'Options / SMS confirmation sent after payment', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'SMS confirmation sent after payment', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_sms_payment_message_text', 'backend', 'Options / SMS confirmation sent after payment', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '<u>Available Tokens:</u><br/><br/>{Title}<br/>{FirstName}<br/>{LastName}<br/>{Email}<br/>{Date}<br/>{TicketTypesPrice}<br/>{UniqueID}<br/>{Total}<br/>{Tax}<br/>{Phone}', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_btn_close', 'frontend', 'Label / Close', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Close', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_round_trip_tickets_error', 'frontend', 'Label / Round trip select ticket error', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Number of tickets for round trip cannot be greater than number of tickets for one trip.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_cancel_after_pending_time', 'backend', 'Label / Cancelled After "Seats Pending Time"', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Cancelled After \"Seats Pending Time\"', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'datepicker_tooltips_ARRAY_nextMonth', 'arrays', 'datepicker_tooltips_ARRAY_nextMonth', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Next Month', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'datepicker_tooltips_ARRAY_selectYear', 'arrays', 'datepicker_tooltips_ARRAY_selectYear', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Select Year', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'datepicker_tooltips_ARRAY_prevYear', 'arrays', 'datepicker_tooltips_ARRAY_prevYear', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Previous Year', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'datepicker_tooltips_ARRAY_nextYear', 'arrays', 'datepicker_tooltips_ARRAY_nextYear', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Next Year', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'datepicker_tooltips_ARRAY_selectDecade', 'arrays', 'datepicker_tooltips_ARRAY_selectDecade', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Select Decade', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'datepicker_tooltips_ARRAY_prevDecade', 'arrays', 'datepicker_tooltips_ARRAY_prevDecade', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Previous Decade', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'datepicker_tooltips_ARRAY_nextDecade', 'arrays', 'datepicker_tooltips_ARRAY_nextDecade', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Next Decade', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'datepicker_tooltips_ARRAY_prevCentury', 'arrays', 'datepicker_tooltips_ARRAY_prevCentury', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Previous Century', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'datepicker_tooltips_ARRAY_nextCentury', 'arrays', 'datepicker_tooltips_ARRAY_nextCentury', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Next Century', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'datepicker_tooltips_ARRAY_pickHour', 'arrays', 'datepicker_tooltips_ARRAY_pickHour', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Pick Hour', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'datepicker_tooltips_ARRAY_incrementHour', 'arrays', 'datepicker_tooltips_ARRAY_incrementHour', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Increment Hour', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'datepicker_tooltips_ARRAY_decrementHour', 'arrays', 'datepicker_tooltips_ARRAY_decrementHour', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Decrement Hour', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'datepicker_tooltips_ARRAY_pickMinute', 'arrays', 'datepicker_tooltips_ARRAY_pickMinute', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Pick Minute', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'datepicker_tooltips_ARRAY_incrementMinute', 'arrays', 'datepicker_tooltips_ARRAY_incrementMinute', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Increment Minute', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'datepicker_tooltips_ARRAY_decrementMinute', 'arrays', 'datepicker_tooltips_ARRAY_decrementMinute', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Decrement Minute', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'datepicker_tooltips_ARRAY_pickSecond', 'arrays', 'datepicker_tooltips_ARRAY_pickSecond', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Pick Second', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'datepicker_tooltips_ARRAY_incrementSecond', 'arrays', 'datepicker_tooltips_ARRAY_incrementSecond', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Increment Second', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'datepicker_tooltips_ARRAY_decrementSecond', 'arrays', 'datepicker_tooltips_ARRAY_decrementSecond', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Decrement Second', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'datepicker_tooltips_ARRAY_togglePeriod', 'arrays', 'datepicker_tooltips_ARRAY_togglePeriod', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Toggle Period', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'datepicker_tooltips_ARRAY_selectTime', 'arrays', 'datepicker_tooltips_ARRAY_selectTime', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Select Time', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'datepicker_tooltips_ARRAY_today', 'arrays', 'datepicker_tooltips_ARRAY_today', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Go to today', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'datepicker_tooltips_ARRAY_clear', 'arrays', 'datepicker_tooltips_ARRAY_clear', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Clear selection', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'datepicker_tooltips_ARRAY_close', 'arrays', 'datepicker_tooltips_ARRAY_close', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Close the picker', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'datepicker_tooltips_ARRAY_selectMonth', 'arrays', 'datepicker_tooltips_ARRAY_selectMonth', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Select Month', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'datepicker_tooltips_ARRAY_prevMonth', 'arrays', 'datepicker_tooltips_ARRAY_prevMonth', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Previous Month', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'enum_arr_ARRAY_yes', 'arrays', 'enum_arr_ARRAY_yes', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Yes', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'enum_arr_ARRAY_no', 'arrays', 'enum_arr_ARRAY_no', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'enum_arr_ARRAY_1', 'arrays', 'enum_arr_ARRAY_1', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Yes', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'enum_arr_ARRAY_2', 'arrays', 'enum_arr_ARRAY_2', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'enum_arr_ARRAY_3', 'arrays', 'enum_arr_ARRAY_3', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Yes (Required)', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'enum_arr_ARRAY_amount', 'arrays', 'enum_arr_ARRAY_amount', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Amount', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'enum_arr_ARRAY_percent', 'arrays', 'enum_arr_ARRAY_percent', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Percent', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menuProductsList', 'backend', 'Menu / Products List', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Products List', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menuProductStock', 'backend', 'Menu / Stock', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Stock', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menuReports', 'backend', 'Menu / Reports', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Reports', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menuSettings', 'backend', 'Menu / Settings', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Settings', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'settingsTabOrders', 'backend', 'Tab / Orders', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Orders', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'settingsTabPayments', 'backend', 'Tab / Payments', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Payments', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'settingsTabCheckoutForm', 'backend', 'Tab / Checkout Form', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Checkout Form', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'settingsTabShippingTax', 'backend', 'Tab / Shipping & Tax', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Shipping & Tax', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'settingsTabTerms', 'backend', 'Tab / Terms & Conditions', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Terms & Conditions', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'settingsTabNotifications', 'backend', 'Tab / Notifications', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Notifications', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'payment_methods_ARRAY_bank', 'backend', 'payment_methods_ARRAY_bank', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Bank account', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'payment_methods_ARRAY_cash', 'backend', 'payment_methods_ARRAY_cash', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Cash', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'payment_methods_ARRAY_creditcard', 'backend', 'payment_methods_ARRAY_creditcard', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Credit card', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'payment_methods_ARRAY_paypal', 'backend', 'payment_methods_ARRAY_paypal', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'PayPal', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoBookingFormTitle', 'backend', 'Infobox / Booking Form Options', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Checkout Form', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoBookingFormDesc', 'backend', 'Infobox / Booking form descriptoin', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Below you can enable and disable the checkout form fields that customers will have to complete. If you choose \"Yes (required)\" option then this field becomes mandatory and customers will not be able to proceed further without filling it in.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AOP02', 'arrays', 'error_titles_ARRAY_AOP02', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order options updated', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AOP02', 'arrays', 'error_bodies_ARRAY_AOP02', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All changes made to the order options have been saved successfully.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AOP03', 'arrays', 'error_titles_ARRAY_AOP03', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Checkout form fields updated.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AOP03', 'arrays', 'error_bodies_ARRAY_AOP03', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All changes made to the checkout form have been saved successfully.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AOP04', 'arrays', 'error_titles_ARRAY_AOP04', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Terms and Conditions updated', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AOP04', 'arrays', 'error_bodies_ARRAY_AOP04', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All changes you made on the Terms and Conditions options have been saved.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AOP06', 'arrays', 'error_titles_ARRAY_AOP06', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Shipping and tax updated', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AOP06', 'arrays', 'error_bodies_ARRAY_AOP06', 'script', '2022-05-12 08:47:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All changes you made on the shipping and tax fee have been saved.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_ARRAY_client_email_account', 'arrays', 'Notifications / Client registration confirmation', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send new registration email', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_titles_ARRAY_client_email_account', 'arrays', 'Notifications / Client email registration (title)', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'New registration email sent to Client', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_subtitles_ARRAY_client_email_account', 'arrays', 'Notifications / Client email registration (sub-title)', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'This email is sent to the client when a new registration is made.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_ARRAY_client_email_forgot', 'arrays', 'Notifications / Send forgot password email', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send forgot password email', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_titles_ARRAY_client_email_forgot', 'arrays', 'Notifications / Send forgot password email (title)', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send forgot password email to Client', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_subtitles_ARRAY_client_email_forgot', 'arrays', 'Notifications / Send forgot password email (sub-title)', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'This message is sent to client when he requests for password recovery.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_ARRAY_client_email_send_to_friend', 'arrays', 'Notifications / Send to friend email', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send to friend email', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_titles_ARRAY_client_email_send_to_friend', 'arrays', 'Notifications / Send to friend email (title)', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Share product email sent to Friend', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_subtitles_ARRAY_client_email_send_to_friend', 'arrays', 'Notifications / Send to friend email (sub-title)', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'This message is sent to friend when the customer wants to share product to his friend.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblOptionsTermsURL', 'backend', 'Options / Booking terms URL', 'script', '2021-02-03 08:52:57');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Booking terms URL', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblOptionsTermsURLDesc', 'backend', 'Label / Booking terms URL desc', 'script', '2020-10-30 08:30:59');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Enter booking terms URL', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblOptionsTermsContent', 'backend', 'Options / Booking terms content', 'script', '2021-02-03 08:52:57');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Booking terms content', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblOptionsTermsContentDesc', 'backend', 'Label / Booking terms Content desc', 'script', '2020-10-30 08:30:59');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Enter booking terms content', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pj_field_required', 'backend', 'Label / This field is required.', 'script', '2021-02-03 08:52:57');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'This field is required.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pj_field_number', 'backend', 'Label / Please enter a valid number.', 'script', '2021-02-03 08:52:57');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Please enter a valid number.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'shipping_tax_add', 'backend', 'Label / Add shipping & tax', 'script', '2021-02-03 08:52:57');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Add shipping & tax', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblDeleteShipping', 'backend', 'Label / Delete location title', 'script', '2021-02-03 08:52:57');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Delete location', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblDeleteShippingConfirm', 'backend', 'Label / Delete location desc', 'script', '2021-02-03 08:52:57');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Are you sure you want to delete this location?', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoCategoriesDesc', 'backend', 'infoCategoriesDesc', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Your menu is organized in categories. Below is a list of all categories added to the system. Use the tab above to add new category or edit one by clicking on the pencil icon of each row.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoCategoriesTitle', 'backend', 'infoCategoriesTitle', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Category List', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoAddCategoryDesc', 'backend', 'infoAddCategoryDesc', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Use the form below to add new category to the system.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoAddCategoryTitle', 'backend', 'infoAddCategoryTitle', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Add new category', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoUpdateCategoryDesc', 'backend', 'infoUpdateCategoryDesc', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Edit category details and click on the ''Save'' button to update it.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoUpdateCategoryTitle', 'backend', 'infoUpdateCategoryTitle', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Update Category', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'btnAdvancedSearch', 'backend', 'Button / Advanced search', 'script', '2020-10-30 08:30:59');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Advanced search', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoAddProductDesc', 'backend', 'infoAddProductDesc', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Use the form below to add products to the shopping cart system. You can add name, short description, description, assign a product to one or more categories and to make it featured.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoAddProductTitle', 'backend', 'infoAddProductTitle', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Add new product', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoUpdateProductDesc', 'backend', 'infoUpdateProductDesc', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Edit product details and click on the ''Save'' button to update it.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoUpdateProductTitle', 'backend', 'infoUpdateProductTitle', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Update Product', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'btnClose', 'backend', 'btnClose', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Close', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pj_field_digits', 'backend', 'pj_field_digits', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Please enter only digits.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'voucher_code_exist', 'backend', 'Label / Existing voucher code', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'The voucher code is already used.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'vouchers_types_ARRAY_amount', 'arrays', 'vouchers_types_ARRAY_amount', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Amount', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'vouchers_types_ARRAY_percent', 'arrays', 'vouchers_types_ARRAY_percent', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Percent', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'voucher_validate_datetime', 'backend', 'Plugin Vouchers / Label / Validate date time', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'To date/time must be greater than From date/time.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'voucher_validate_time', 'backend', 'Plugin Vouchers / Label / Validate time', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'End time must be greater than start time.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'vouchers_days_ARRAY_monday', 'arrays', 'vouchers_days_ARRAY_monday', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Monday', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'vouchers_days_ARRAY_tuesday', 'arrays', 'vouchers_days_ARRAY_tuesday', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Tuesday', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'vouchers_days_ARRAY_wednesday', 'arrays', 'vouchers_days_ARRAY_wednesday', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Wednesday', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'vouchers_days_ARRAY_thursday', 'arrays', 'vouchers_days_ARRAY_thursday', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Thursday', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'vouchers_days_ARRAY_friday', 'arrays', 'vouchers_days_ARRAY_friday', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Friday', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'vouchers_days_ARRAY_saturday', 'arrays', 'vouchers_days_ARRAY_saturday', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Saturday', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'vouchers_days_ARRAY_sunday', 'arrays', 'vouchers_days_ARRAY_sunday', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Sunday', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'short_days_ARRAY_0', 'backend', 'short_days_ARRAY_0', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Su', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'short_days_ARRAY_1', 'backend', 'short_days_ARRAY_1', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Mo', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'short_days_ARRAY_2', 'backend', 'short_days_ARRAY_2', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Tu', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'short_days_ARRAY_3', 'backend', 'short_days_ARRAY_3', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'We', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'short_days_ARRAY_4', 'backend', 'short_days_ARRAY_4', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Th', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'short_days_ARRAY_5', 'backend', 'short_days_ARRAY_5', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Fr', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'short_days_ARRAY_6', 'backend', 'short_days_ARRAY_6', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Sa', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'vouchers_infobox_add_voucher_title', 'backend', 'Plugin Vouchers / Infobox / Add Voucher', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Add Voucher', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'vouchers_infobox_add_voucher_desc', 'backend', 'Plugin Vouchers / Infobox / Add Voucher description', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Please fill out the form below to add voucher code and discount. You can add a voucher for specific date, day of the week or date range.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'vouchers_infobox_update_voucher_title', 'backend', 'Plugin Vouchers / Infobox / Update voucher', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Update voucher', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'vouchers_infobox_update_voucher_desc', 'backend', 'Plugin Vouchers / Infobox / Update voucher desc', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Please make any change you want on the form below to update voucher information and click SAVE button.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblEmailSent', 'backend', 'lblEmailSent', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Email has been sent.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblFailedToSend', 'backend', 'lblFailedToSend', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Email failed to send.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblFrom', 'backend', 'Label / From', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'From', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblTo', 'backend', 'Label / To', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'To', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoUpdateOrderDesc', 'backend', 'infoUpdateOrderDesc', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Edit order details and click on the ''Save'' button to update it.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoUpdateOrderTitle', 'backend', 'infoUpdateOrderTitle', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Update Order', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblOrderExtra', 'backend', 'Label / Extra', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Extra', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_stock_add_title', 'backend', 'Info / Add Stock', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Add Stock', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblOrderExtras', 'backend', 'Label / Extras', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Extras', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_stock_edit_title', 'backend', 'Label / Edit stock', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Edit stock', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_stock_delete_title', 'backend', 'Label / Delete stock title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Delete stock', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_stock_delete_desc', 'backend', 'Label / Delete stock body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Are you sure you want to delete selected stock?', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_confirmation_email_title', 'backend', 'Label / Order confirmation email', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Order confirmation email', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'order_payment_email_title', 'backend', 'Label / Payment confirmation email', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Payment confirmation email', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblEmailNotificationNotSet', 'backend', 'lblEmailNotificationNotSet', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Email notification has not been set yet.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblSubject', 'backend', 'Label / Subject', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Subject', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblMessage', 'backend', 'Label / Message', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Message', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblClientEmail', 'backend', 'Label / Client email', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Client email', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AO40', 'arrays', 'error_titles_ARRAY_AO40', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Preview front end', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AO40', 'arrays', 'error_bodies_ARRAY_AO40', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'To put the booking engine on your website go to <a href=\"index.php?controller=pjAdminOptions&action=pjActionInstall\">Install</a> page.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'script_change_labels', 'backend', 'script_change_labels', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Change Labels', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'script_install_your_website', 'backend', 'script_install_your_website', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Install your website', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_ip_address_blocked', 'frontend', 'front_ip_address_blocked', 'script', '2020-10-21 08:17:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Your IP address has been blocked.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_see_more', 'frontend', 'Label / See more', 'script', '2021-02-03 08:52:57');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'See more', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_label_search', 'frontend', 'Label / Search products', 'script', '2021-02-03 08:52:57');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Search products', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_label_select_category', 'frontend', 'Label / Select category', 'script', '2021-02-03 08:52:57');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Select category', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_btn_attributes', 'frontend', 'Button / Attributes', 'script', '2021-02-03 08:52:57');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Attributes', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_product_sort_by_ARRAY_featured', 'arrays', 'front_product_sort_by_ARRAY_featured', 'script', '2021-02-03 08:52:57');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Featured products', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_product_sort_by_ARRAY_newest', 'arrays', 'front_product_sort_by_ARRAY_newest', 'script', '2021-02-03 08:52:57');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'From the newest', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_product_sort_by_ARRAY_price_asc', 'arrays', 'front_product_sort_by_ARRAY_price_asc', 'script', '2021-02-03 08:52:57');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Price: low to high', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_product_sort_by_ARRAY_price_desc', 'arrays', 'front_product_sort_by_ARRAY_price_desc', 'script', '2021-02-03 08:52:57');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Price: high to low', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_product_sort_by_ARRAY_name_asc', 'arrays', 'front_product_sort_by_ARRAY_name_asc', 'script', '2021-02-03 08:52:57');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Alphabetical: A-Z', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_product_sort_by_ARRAY_name_desc', 'arrays', 'front_product_sort_by_ARRAY_name_desc', 'script', '2021-02-03 08:52:57');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Alphabetical: Z-A', 'script');


INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoNotificationsTitle', 'backend', 'Info / Email Confirmations Title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Email Confirmations', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'infoNotificationsDesc', 'backend', 'Info / Email Confirmations Desc', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'There are 3 types of email confirmations - one after booking form is submitted , one after payment is made and one when cancelled the booking. Use the available tokens to personalize the email messages.', 'script');


INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_main_title', 'backend', 'Notifications', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Notifications', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_main_subtitle', 'backend', 'Notifications (sub-title)', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Automated messages are sent both to client and super admin on specific events. Select message type to edit it - enable/disable or just change message text. For SMS notifications you need to enable SMS service. See more <a href=\"https://www.phpjabbers.com/web-sms/\" target=\"_blank\">here</a>.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_recipient', 'backend', 'Notifications / Recipient', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Recipient', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_tokens_note', 'backend', 'Notifications / Tokens (note)', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Personalize the message by including any of the available tokens and it will be replaced with corresponding data.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_tokens', 'backend', 'Notifications / Tokens', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Available tokens:', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'recipients_ARRAY_client', 'arrays', 'Recipients / Client', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Client', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'recipients_ARRAY_admin', 'arrays', 'Recipients / Super Admin', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Super Admin', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_email_body_text', 'backend', 'Options / Email body text', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '<div class=\"col-xs-12\">
<div><small>{ClientName} - customer name;</small></div>
<div><small>{ClientEmail} - customer e-mail;</small></div>
<div><small>{ClientPhone} - customer phone number;</small></div>
<div><small>{BillingName} - customer billing name;</small></div>
<div><small>{BillingAddress1} - billing address 1;</small></div>
<div><small>{BillingAddress2} - billing address 2;</small></div>
<div><small>{BillingCity} - billing city;</small></div>
<div><small>{BillingState} - billing state;</small></div>
<div><small>{BillingZip} - billing zip code;</small></div>
<div><small>{BillingCountry} - billing country;</small></div>
<div><small>{ShippingName} - customer shipping name;</small></div>
<div><small>{ShippingAddress1} - shipping address 1;</small></div>
<div><small>{ShippingAddress2} - shipping address 2;</small></div>
<div><small>{ShippingCity} - shipping city;</small></div>
<div><small>{ShippingState} - shipping state;</small></div>
<div><small>{ShippingZip} - shipping zip code;</small></div>
<div><small>{ShippingCountry} - shipping country;</small></div>
<div><small>{Notes} - additional notes;</small></div>
<div><small>{PaymentMethod} - selected payment method;</small></div>
<div><small>{Insurance} - insurance fee;</small></div>
<div><small>{Shipping} - shipping fee;</small></div>
<div><small>{Tax} - tax fee;</small></div>
<div><small>{Price} - price;</small></div>
<div><small>{Total} - total amount;</small></div>
<div><small>{Discount} - discount;</small></div>
<div><small>{Voucher} - promo code;</small></div>
<div><small>{Products} - purchased products;</small></div>
<div><small>{OrderUUID} - Order number;</small></div>
<div><small>{DigitalDownload} - Digital products download link;</small></div>
<div><small>{StoreName} - Store name;</small></div>
</div>', 'script');
 
 
INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_account_email_body_text', 'backend', 'Options / Email body text', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '<div class=\"col-xs-12\">
<div><small>{ClientName} - customer name;</small></div>
<div><small>{ClientEmail} - customer e-mail;</small></div>
<div><small>{ClientPassword} - customer password;</small></div>
<div><small>{ClientPhone} - customer phone number;</small></div>
<div><small>{ClientURL} - customer website;</small></div>
<div><small>{StoreName} - Store name;</small></div>
 </div>', 'script');
 
 
INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'opt_o_send_to_friend_email_body_text', 'backend', 'Options / Email body text', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '<div class=\"col-xs-12\">
<div><small>{FriendName} - your friend name;</small></div>
<div><small>{FriendEmail} - your friend email;</small></div>
<div><small>{YourName} - your name;</small></div>
<div><small>{YourEmail} - your email;</small></div>
<div><small>{URL} - url to product detail;</small></div>
 </div>', 'script');


INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_ARRAY_client_email_confirmation', 'arrays', 'Notifications / Client email confirmation', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send confirmation email', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_ARRAY_client_email_payment', 'arrays', 'Notifications / Client email payment', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send payment confirmation email', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_ARRAY_client_email_cancel', 'arrays', 'Notifications / Client email cancel', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send cancellation email', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_ARRAY_admin_email_confirmation', 'arrays', 'Notifications / Admin email confirmation', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send confirmation email', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_ARRAY_admin_email_payment', 'arrays', 'Notifications / Admin email payment', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send payment confirmation email', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_ARRAY_admin_email_cancel', 'arrays', 'Notifications / Admin email cancel', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send cancellation email', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_titles_ARRAY_client_email_confirmation', 'arrays', 'Notifications / Client email confirmation (title)', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order Confirmation email sent to Client', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_titles_ARRAY_client_email_payment', 'arrays', 'Notifications / Client email payment (title)', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Payment Confirmation email sent to Client', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_titles_ARRAY_client_email_cancel', 'arrays', 'Notifications / Client email cancel (title)', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order Cancellation email sent to Client', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_titles_ARRAY_admin_email_confirmation', 'arrays', 'Notifications / Admin email confirmation (title)', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'New Order Received email sent to Admin', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_titles_ARRAY_admin_email_payment', 'arrays', 'Notifications / Admin email payment (title)', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Payment Confirmation email sent to Admin', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_titles_ARRAY_admin_email_cancel', 'arrays', 'Notifications / Admin email cancel (title)', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Cancellation email sent to Admin', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_subtitles_ARRAY_client_email_confirmation', 'arrays', 'Notifications / Client email confirmation (sub-title)', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'This email is sent to the client when a new order is made.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_subtitles_ARRAY_client_email_payment', 'arrays', 'Notifications / Client email payment (sub-title)', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'This email is sent to the client when a new payment is made.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_subtitles_ARRAY_client_email_cancel', 'arrays', 'Notifications / Client email cancel (sub-title)', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'This email is sent to client when cancel the order.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_subtitles_ARRAY_admin_email_confirmation', 'arrays', 'Notifications / Admin email confirmation (sub-title)', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'This email is sent to the super admin when a new order is made.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_subtitles_ARRAY_admin_email_payment', 'arrays', 'Notifications / Admin email payment (sub-title)', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'This email is sent to the super admin when a new payment is made.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_subtitles_ARRAY_admin_email_cancel', 'arrays', 'Notifications / Admin email cancel (sub-title)', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'This email is sent to the super admin when client cancel the order.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_subject', 'backend', 'Subject', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Subject', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_message', 'backend', 'Message', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Message', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_is_active', 'backend', 'Send this message', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send this message', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_sms_na', 'backend', 'SMS not available', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'SMS notifications are currently not available for your website. See details', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_sms_na_desc', 'backend', 'Label / To use SMS notification, please add you SMS key', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'To use SMS notification, please add you SMS key', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_sms_na_here', 'backend', 'here', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'here', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_send', 'backend', 'Notifications / Send', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_do_not_send', 'backend', 'Notifications / Do not send', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Do not send', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_status', 'backend', 'Notifications / Status', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Status', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_msg_to_client', 'backend', 'Notifications / Messages sent to Clients', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Messages sent to Clients', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_msg_to_admin', 'backend', 'Notifications / Messages sent to Admin', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Messages sent to Admin', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_msg_to_default', 'backend', 'Notifications / Messages sent to Default', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Messages sent', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_ARRAY_client_sms_confirmation', 'arrays', 'Notifications / Order confirmation SMS', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order confirmation SMS', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_ARRAY_client_sms_payment', 'arrays', 'Notifications / Payment confirmation SMS', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Payment confirmation SMS', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_ARRAY_admin_sms_confirmation', 'arrays', 'Notifications / Order confirmation SMS', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order confirmation SMS', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_ARRAY_admin_sms_payment', 'arrays', 'Notifications / Payment confirmation SMS', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Payment confirmation SMS', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_titles_ARRAY_client_sms_confirmation', 'arrays', 'Notifications / Client sms confirmation (title)', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order Confirmation SMS to Client', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_subtitles_ARRAY_client_sms_confirmation', 'arrays', 'Notifications / Client sms confirmation (sub-title)', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'This SMS is sent to client when a new order is made.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_titles_ARRAY_client_sms_payment', 'arrays', 'Notifications / Client sms payment (title)', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Payment Confirmation SMS to Client', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_subtitles_ARRAY_client_sms_payment', 'arrays', 'Notifications / Client sms payment (sub-title)', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'This SMS is sent to client when a payment is made for a his order.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_titles_ARRAY_admin_sms_confirmation', 'arrays', 'Notifications / Admin sms confirmation (title)', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order Confirmation SMS to Admin', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_subtitles_ARRAY_admin_sms_confirmation', 'arrays', 'Notifications / Client sms confirmation (sub-title)', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'This SMS is sent to super admin when a new order is made.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_titles_ARRAY_admin_sms_payment', 'arrays', 'Notifications / Admin sms confirmation (title)', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Payment Confirmation SMS to Admin', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'notifications_subtitles_ARRAY_admin_sms_payment', 'arrays', 'Notifications / Admin sms confirmation (sub-title)', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'This SMS is sent to super admin when a payment is made for a new order.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'lblDate', 'backend', 'Label / Date', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Date', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'dash_today', 'backend', 'Label / Today', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Today', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'dash_orders', 'backend', 'Label / Orders', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Orders', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'dash_products', 'backend', 'Label / Products', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Products', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'dash_total', 'backend', 'Label / Total', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Total', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'dashboard_order_id', 'backend', 'Label / Order ID', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Order ID', 'script');




INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, NULL, 'pjAdminOptions');
SET @level_1_id := (SELECT LAST_INSERT_ID());

  INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_1_id, 'pjAdminOptions_pjActionBooking'); 
  INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_1_id, 'pjPayments_pjActionIndex');
  INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_1_id, 'pjAdminOptions_pjActionBookingForm');
  INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_1_id, 'pjAdminOptions_pjActionShippingTax');
  INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_1_id, 'pjAdminOptions_pjActionNotifications'); 
  INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_1_id, 'pjAdminOptions_pjActionTerm');
  
INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, NULL, 'pjAdminOptions_pjActionPreview');
INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, NULL, 'pjAdminOptions_pjActionInstall');


INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminOptions', 'backend', 'pjAdminOptions', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Options Menu', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminOptions_pjActionBooking', 'backend', 'pjAdminOptions_pjActionBooking', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Bookings', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjPayments_pjActionIndex', 'backend', 'pjPayments_pjActionIndex', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Payments', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminOptions_pjActionBookingForm', 'backend', 'pjAdminOptions_pjActionBookingForm', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Booking Form', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminOptions_pjActionTicket', 'backend', 'pjAdminOptions_pjActionShippingTax', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Shipping & Tax', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminOptions_pjActionTerm', 'backend', 'pjAdminOptions_pjActionTerm', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Terms', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminOptions_pjActionNotifications', 'backend', 'pjAdminOptions_pjActionNotifications', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Notifications', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminOptions_pjActionPreview', 'backend', 'pjAdminOptions_pjActionPreview', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Preview Menu', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminOptions_pjActionInstall', 'backend', 'pjAdminOptions_pjActionInstall', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Install Menu', 'script');


INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, NULL, 'pjAdminCategories');
SET @level_1_id := (SELECT LAST_INSERT_ID());

  INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_1_id, 'pjAdminCategories_pjActionIndex');
  SET @level_2_id := (SELECT LAST_INSERT_ID());
  
    INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminCategories_pjActionCreateForm');
    INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminCategories_pjActionUpdateForm');
    INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminCategories_pjActionDeleteCategory');
    INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminCategories_pjActionDeleteCategoryBulk');
    
    

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminCategories', 'backend', 'pjAdminCategories', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Categories Menu', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminCategories_pjActionCreateForm', 'backend', 'pjAdminCategories_pjActionCreateForm', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Add categories', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminCategories_pjActionDeleteCategory', 'backend', 'pjAdminCategories_pjActionDeleteCategory', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Delete single category', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminCategories_pjActionDeleteCategoryBulk', 'backend', 'pjAdminCategories_pjActionDeleteCategoryBulk', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Delete multiple categories', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminCategories_pjActionIndex', 'backend', 'pjAdminCategories_pjActionIndex', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Categories List', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminCategories_pjActionUpdateForm', 'backend', 'pjAdminCategories_pjActionUpdateForm', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Edit categories', 'script');
    

INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, NULL, 'pjAdminProducts');
SET @level_1_id := (SELECT LAST_INSERT_ID());

  INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_1_id, 'pjAdminProducts_pjActionIndex');
  SET @level_2_id := (SELECT LAST_INSERT_ID());
  
    INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminProducts_pjActionCreate');
    INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminProducts_pjActionUpdate');
    INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminProducts_pjActionDeleteProduct');
    INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminProducts_pjActionDeleteProductBulk');
  
  INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_1_id, 'pjAdminProducts_pjActionStock');
  INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_1_id, 'pjAdminProducts_pjActionProductsFlatFileIndex');

INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, NULL, 'pjAdminProductImportHistory');
SET @level_1_id := (SELECT LAST_INSERT_ID());
  INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_1_id, 'pjAdminProductImportHistory_pjActionIndex');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminProducts', 'backend', 'pjAdminProducts', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Products Menu', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminProducts_pjActionCreate', 'backend', 'pjAdminProducts_pjActionCreate', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Add products', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminProducts_pjActionDeleteProduct', 'backend', 'pjAdminProducts_pjActionDeleteProduct', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Delete single product', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminProducts_pjActionDeleteProductBulk', 'backend', 'pjAdminProducts_pjActionDeleteProductBulk', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Delete multiple products', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminProducts_pjActionIndex', 'backend', 'pjAdminProducts_pjActionIndex', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Products List', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminProducts_pjActionUpdate', 'backend', 'pjAdminProducts_pjActionUpdate', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Edit products', 'script');
    
INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminProducts_pjActionStock', 'backend', 'pjAdminProducts_pjActionStock', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Stock', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminProducts_pjActionProductsFlatFileIndex', 'backend', 'pjAdminProducts_pjActionProductsFlatFileIndex', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Product flat file view', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminProductImportHistory', 'backend', 'pjAdminProductImportHistory', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Product import', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminProductImportHistory_pjActionIndex', 'backend', 'pjAdminProductImportHistory_pjActionIndex', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Import products', 'script');

INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, NULL, 'pjAdminVouchers');
SET @level_1_id := (SELECT LAST_INSERT_ID());

  INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_1_id, 'pjAdminVouchers_pjActionIndex');
  SET @level_2_id := (SELECT LAST_INSERT_ID());
  
    INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminVouchers_pjActionCreate');
    INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminVouchers_pjActionUpdate');
    INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminVouchers_pjActionDeleteVoucher');
    INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminVouchers_pjActionDeleteVoucherBulk');
    

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminVouchers', 'backend', 'pjAdminVouchers', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Vouchers Menu', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminVouchers_pjActionCreate', 'backend', 'pjAdminVouchers_pjActionCreate', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Add vouchers', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminVouchers_pjActionDeleteVoucher', 'backend', 'pjAdminVouchers_pjActionDeleteVoucher', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Delete single voucher', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminVouchers_pjActionDeleteVoucherBulk', 'backend', 'pjAdminVouchers_pjActionDeleteVoucherBulk', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Delete multiple vouchers', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminVouchers_pjActionIndex', 'backend', 'pjAdminVouchers_pjActionIndex', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Vouchers List', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminVouchers_pjActionUpdate', 'backend', 'pjAdminVouchers_pjActionUpdate', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Edit vouchers', 'script');
    
INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, NULL, 'pjAdminOrders');
SET @level_1_id := (SELECT LAST_INSERT_ID());

  INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_1_id, 'pjAdminOrders_pjActionIndex');
  SET @level_2_id := (SELECT LAST_INSERT_ID());
  
    INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminOrders_pjActionUpdate');
    INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminOrders_pjActionDeleteOrder');
    INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminOrders_pjActionDeleteOrderBulk');
    

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminOrders', 'backend', 'pjAdminOrders', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Orders Menu', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminOrders_pjActionDeleteOrder', 'backend', 'pjAdminOrders_pjActionDeleteOrder', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Delete single order', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminOrders_pjActionDeleteOrderBulk', 'backend', 'pjAdminOrders_pjActionDeleteOrderBulk', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Delete multiple orders', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminOrders_pjActionIndex', 'backend', 'pjAdminOrders_pjActionIndex', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Orders List', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminOrders_pjActionUpdate', 'backend', 'pjAdminOrders_pjActionUpdate', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Edit orders', 'script');
    

INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, NULL, 'pjAdminClients');
SET @level_1_id := (SELECT LAST_INSERT_ID());

  INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_1_id, 'pjAdminClients_pjActionIndex');
  SET @level_2_id := (SELECT LAST_INSERT_ID());

    INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminClients_pjActionCreate');
    INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminClients_pjActionUpdate');
    INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminClients_pjActionDeleteClient');
    INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminClients_pjActionDeleteClientBulk');
    INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminClients_pjActionExportClient');


INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminClients', 'backend', 'pjAdminClients', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Clients Menu', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminClients_pjActionCreate', 'backend', 'pjAdminClients_pjActionCreate', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Add clients', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminClients_pjActionDeleteClient', 'backend', 'pjAdminClients_pjActionDeleteClient', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Delete single client', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminClients_pjActionDeleteClientBulk', 'backend', 'pjAdminClients_pjActionDeleteClientBulk', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Delete multiple clients', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminClients_pjActionExportClient', 'backend', 'pjAdminClients_pjActionExportClient', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Export clients', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminClients_pjActionIndex', 'backend', 'pjAdminClients_pjActionIndex', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Clients List', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminClients_pjActionUpdate', 'backend', 'pjAdminClients_pjActionUpdate', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Edit clients', 'script');
    

INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_1_id, 'pjAdminReports_pjActionIndex');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminReports_pjActionIndex', 'backend', 'pjAdminReports_pjActionIndex', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Reports Menu', 'script');



INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_overlapping_attributes_title', 'backend', 'Error / Overlapping attributes title', 'script', '2018-09-19 07:20:53');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Overlapping attributes', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'product_overlapping_attributes_desc', 'backend', 'Error / Overlapping attributes desc', 'script', '2018-09-19 07:20:53');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'There are products with overlapping attributes. Please check and try again!', 'script');

INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, NULL, 'pjAdminSetup');
SET @level_setup_id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_setup_id, 'pjAdminSetup_pjActionWelcome');
INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_setup_id, 'pjAdminSetup_pjActionLoadDemo');
INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_setup_id, 'pjAdminSetup_pjActionSkipDemo');
INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_setup_id, 'pjAdminSetup_pjActionRemoveDemo');
INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_setup_id, 'pjAdminSetup_pjActionHasDemoData');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'menuSetupWizard', 'backend', 'Menu: Setup wizard', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Setup wizard', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdminSetup', 'backend', 'pjAdminSetup', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Setup wizard', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_welcome_title', 'backend', 'Setup: welcome title', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Welcome to PHPJabbers', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_welcome_subtitle', 'backend', 'Setup: welcome subtitle', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Let''s finish setting up your application. You can load sample data to explore the system, or start with an empty catalogue. Demo data can be removed later.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_badge_recommended', 'backend', 'Setup: recommended badge', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Recommended', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_card_demo_title', 'backend', 'Setup: demo card title', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Start with demo data', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_card_demo_desc', 'backend', 'Setup: demo card description', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Explore products, clients, and sample quotes with realistic workwear examples.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_demo_feat_categories', 'backend', 'Setup: demo feature categories', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '3 categories (Workwear, Uniforms, Accessories)', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_demo_feat_products', 'backend', 'Setup: demo feature products', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '5 sample products with placeholder images', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_demo_feat_clients', 'backend', 'Setup: demo feature clients', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '3 demo clients', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_demo_feat_quotes', 'backend', 'Setup: demo feature quotes', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '3 sample quotes (new, pending, completed)', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_btn_start_demo', 'backend', 'Setup: start with demo button', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Start with demo', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_card_empty_title', 'backend', 'Setup: empty card title', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Start empty', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_card_empty_desc', 'backend', 'Setup: empty card description', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Begin with a clean catalogue and add your own products and clients.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_empty_feat_no_samples', 'backend', 'Setup: empty feature no samples', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No sample products or quotes', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_empty_feat_control', 'backend', 'Setup: empty feature control', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Full control from day one', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_empty_feat_migrate', 'backend', 'Setup: empty feature migrate', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Ideal if you are migrating existing data', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_btn_start_empty', 'backend', 'Setup: start empty button', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Start empty', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_loading_demo', 'backend', 'Setup: loading demo', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Loading demo data...', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_loading_sub', 'backend', 'Setup: loading subtitle', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'This takes a few seconds', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_loading_demo_detail', 'backend', 'Setup: loading demo detail', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Creating categories, products, clients, and quotes', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_success_title', 'backend', 'Setup: success title', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Your platform is ready', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_success_redirect', 'backend', 'Setup: success redirect', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Redirecting to the dashboard...', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_demo_active_title', 'backend', 'Setup: demo active title', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Demo data active', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_demo_active_desc', 'backend', 'Setup: demo active description', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Sample products (SKU: DEMO-*) and demo categories are present. Remove them when you no longer need them.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_btn_remove_demo', 'backend', 'Setup: remove demo button', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Remove demo data', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_load_demo_title', 'backend', 'Setup: load demo title', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Load demo data', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_load_demo_desc', 'backend', 'Setup: load demo description', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Load sample workwear products, clients, and quotes to explore the platform.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_load_demo_feat_summary', 'backend', 'Setup: load demo feature summary', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '3 categories, 5 products, 3 clients', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_load_demo_feat_quotes', 'backend', 'Setup: load demo feature quotes', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '3 sample quotes', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_btn_load_demo', 'backend', 'Setup: load demo button', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Load demo data', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_page_title', 'backend', 'Setup: page title suffix', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Setup', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_swal_loaded_title', 'backend', 'Setup: swal loaded title', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Demo data loaded', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_swal_removed_title', 'backend', 'Setup: swal removed title', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Demo data removed', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_summary_loaded', 'backend', 'Setup: summary loaded', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Demo data loaded.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_summary_removed', 'backend', 'Setup: summary removed', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Demo data removed.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_unit_products', 'backend', 'Setup: unit products', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'products', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_unit_categories', 'backend', 'Setup: unit categories', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'categories', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_unit_images', 'backend', 'Setup: unit images', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'images', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_unit_clients', 'backend', 'Setup: unit clients', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'clients', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_unit_quotes', 'backend', 'Setup: unit quotes', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'quotes', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_error_unknown', 'backend', 'Setup: unknown error', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Unknown error', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_error_generic', 'backend', 'Setup: generic error', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'An error occurred. Please try again.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_error_connection', 'backend', 'Setup: connection error', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Connection error. Please try again.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_error_connection_short', 'backend', 'Setup: connection error short', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Connection error.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_error_short', 'backend', 'Setup: error short', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'An error occurred.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_error_prefix', 'backend', 'Setup: error prefix', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Error:', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_skip_loading', 'backend', 'Setup: skip loading', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please wait...', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_skip_loading_sub', 'backend', 'Setup: skip loading subtitle', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Preparing your empty catalogue', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_remove_confirm_title', 'backend', 'Setup: remove confirm title', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Remove demo data?', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_remove_confirm_text', 'backend', 'Setup: remove confirm text', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All sample products, categories, clients, and quotes will be permanently removed.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_btn_remove_confirm', 'backend', 'Setup: remove confirm button', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Yes, remove', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_remove_confirm_fallback', 'backend', 'Setup: remove confirm fallback', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Remove all demo data? This cannot be undone.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_removing_demo', 'backend', 'Setup: removing demo', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Removing demo data...', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_btn_ok', 'backend', 'Setup: OK button', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'OK', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_unauthorized', 'backend', 'Setup: unauthorized', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Unauthorized.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_api_loaded', 'backend', 'Setup: API loaded message', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Demo data loaded successfully.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_api_removed', 'backend', 'Setup: API removed message', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Demo data removed successfully.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_api_completed', 'backend', 'Setup: API wizard completed', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Setup wizard completed.', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_error_loading_demo', 'backend', 'Setup: error loading demo', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Error loading demo data:', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_error_removing_demo', 'backend', 'Setup: error removing demo', 'script', '2026-05-15 21:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Error removing demo data:', 'script');



INSERT INTO `shopping_cart_plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, NULL, 'pjAdmin_pjActionIndex');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'pjAdmin_pjActionIndex', 'backend', 'pjAdmin_pjActionIndex', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Dashboard', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'front_order_by', 'frontend', 'Label / Order by', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order by', 'script');




INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_step_welcome', 'backend', 'Setup step: Welcome', 'script', '2026-05-15 22:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Welcome', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_step_choose', 'backend', 'Setup step: Choose', 'script', '2026-05-15 22:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Choose', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_step_install', 'backend', 'Setup step: Set up', 'script', '2026-05-15 22:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Set up', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_step_done', 'backend', 'Setup step: Done', 'script', '2026-05-15 22:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Done', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_btn_continue', 'backend', 'Setup: Continue button', 'script', '2026-05-15 22:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Continue', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_btn_back', 'backend', 'Setup: Back button', 'script', '2026-05-15 22:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Back', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_step1_feat_1', 'backend', 'Setup step 1 feature 1', 'script', '2026-05-15 22:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Quote-only platform ready for your workwear catalogue', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_step1_feat_2', 'backend', 'Setup step 1 feature 2', 'script', '2026-05-15 22:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Load sample data to explore, or start with a clean slate', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_step1_feat_3', 'backend', 'Setup step 1 feature 3', 'script', '2026-05-15 22:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can change or remove demo data later from the setup wizard', 'script');

INSERT INTO `shopping_cart_plugin_base_fields` VALUES (NULL, 'setup_choose_hint', 'backend', 'Setup: choose step hint', 'script', '2026-05-15 22:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `shopping_cart_plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Select an option below, then click Continue.', 'script');

