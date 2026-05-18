
START TRANSACTION;

SET @label := 'Plugin Payments / Add other Payment Gateways';
INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`) VALUES (NULL, 'plugin_payments_add_payment_gateways', 'backend', @label, 'plugin', NULL)
ON DUPLICATE KEY UPDATE `fields`.`type` = 'backend', `label` = @label, `source` = 'plugin', `modified` = NULL;
SET @content := 'Add other Payment Gateways';
INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, `id`, 'pjCmsField', '::LOCALE::', 'title', @content, 'plugin' FROM `fields` WHERE `key` = 'plugin_payments_add_payment_gateways' ON DUPLICATE KEY UPDATE `multi_lang`.`content` = @content, `source` = 'plugin';

SET @label := 'Plugin Payments / Active Payment Gateways';
INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`) VALUES (NULL, 'plugin_payments_active_payment_gateways', 'backend', @label, 'plugin', NULL)
ON DUPLICATE KEY UPDATE `fields`.`type` = 'backend', `label` = @label, `source` = 'plugin', `modified` = NULL;
SET @content := 'Active Payment Gateways';
INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, `id`, 'pjCmsField', '::LOCALE::', 'title', @content, 'plugin' FROM `fields` WHERE `key` = 'plugin_payments_active_payment_gateways' ON DUPLICATE KEY UPDATE `multi_lang`.`content` = @content, `source` = 'plugin';

COMMIT;