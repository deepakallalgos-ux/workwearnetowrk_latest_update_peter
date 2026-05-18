
START TRANSACTION;

ALTER TABLE `plugin_payment_options` ADD COLUMN `type` enum('online', 'offline') DEFAULT 'online';

INSERT IGNORE INTO `plugin_payment_options` (`foreign_id`, `payment_method`, `is_active`, `type`, `company_id`) VALUES (1, 'cash', 1, 'offline',1);
INSERT IGNORE INTO `plugin_payment_options` (`foreign_id`, `payment_method`, `type`, `company_id`) VALUES (1, 'bank', 'offline',1);

COMMIT;