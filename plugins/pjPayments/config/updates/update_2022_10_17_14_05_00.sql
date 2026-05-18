
START TRANSACTION;

INSERT IGNORE INTO `plugin_payment_options` (`id`, `foreign_id`, `payment_method`, `type`, `company_id`) VALUES
(NULL, 1, 'cash', 'offline',1),
(NULL, 1, 'bank', 'offline',1);

COMMIT;