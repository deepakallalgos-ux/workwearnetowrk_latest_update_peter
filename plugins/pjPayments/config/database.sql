
DROP TABLE IF EXISTS `plugin_payment_options`;
CREATE TABLE IF NOT EXISTS `plugin_payment_options` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `foreign_id` int(10) unsigned DEFAULT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `merchant_id` VARCHAR(255) DEFAULT NULL,
  `merchant_email` VARCHAR(255) DEFAULT NULL,
  `public_key` VARCHAR(255) DEFAULT NULL,
  `private_key` VARCHAR(255) DEFAULT NULL,
  `tz` int(10) DEFAULT NULL,
  `success_url` VARCHAR(255) DEFAULT NULL,
  `failure_url` VARCHAR(255) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `is_active` BOOLEAN DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


SET @label := 'Plugin Payments / Payment Options';
INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`) VALUES (NULL, 'tabPaymentOptions', 'backend', @label, 'plugin', NULL)
ON DUPLICATE KEY UPDATE `fields`.`type` = 'backend', `label` = @label, `source` = 'plugin', `modified` = NULL;
SET @content := 'Payment Options';
INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, `id`, 'pjCmsField', '::LOCALE::', 'title', @content, 'plugin' FROM `fields` WHERE `key` = 'tabPaymentOptions' ON DUPLICATE KEY UPDATE `multi_lang`.`content` = @content, `source` = 'plugin';

SET @label := 'Plugin Payments / Payment Options';
INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`) VALUES (NULL, 'infoPaymentOptionsTitle', 'backend', @label, 'plugin', NULL)
ON DUPLICATE KEY UPDATE `fields`.`type` = 'backend', `label` = @label, `source` = 'plugin', `modified` = NULL;
SET @content := 'Payment Options';
INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, `id`, 'pjCmsField', '::LOCALE::', 'title', @content, 'plugin' FROM `fields` WHERE `key` = 'infoPaymentOptionsTitle' ON DUPLICATE KEY UPDATE `multi_lang`.`content` = @content, `source` = 'plugin';

SET @label := 'Plugin Payments / Payment Options Body';
INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`) VALUES (NULL, 'infoPaymentOptionsBody', 'backend', @label, 'plugin', NULL)
ON DUPLICATE KEY UPDATE `fields`.`type` = 'backend', `label` = @label, `source` = 'plugin', `modified` = NULL;
SET @content := 'Edit the options for the supported payment gateways and then click Save.';
INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, `id`, 'pjCmsField', '::LOCALE::', 'title', @content, 'plugin' FROM `fields` WHERE `key` = 'infoPaymentOptionsBody' ON DUPLICATE KEY UPDATE `multi_lang`.`content` = @content, `source` = 'plugin';

SET @label := 'Plugin Payments / Yes';
INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`) VALUES (NULL, 'allow_payment_method_ARRAY_1', 'arrays', @label, 'plugin', NULL)
ON DUPLICATE KEY UPDATE `fields`.`type` = 'arrays', `label` = @label, `source` = 'plugin', `modified` = NULL;
SET @content := 'Yes';
INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, `id`, 'pjCmsField', '::LOCALE::', 'title', @content, 'plugin' FROM `fields` WHERE `key` = 'allow_payment_method_ARRAY_1' ON DUPLICATE KEY UPDATE `multi_lang`.`content` = @content, `source` = 'plugin';

SET @label := 'Plugin Payments / No';
INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`) VALUES (NULL, 'allow_payment_method_ARRAY_0', 'arrays', @label, 'plugin', NULL)
ON DUPLICATE KEY UPDATE `fields`.`type` = 'arrays', `label` = @label, `source` = 'plugin', `modified` = NULL;
SET @content := 'No';
INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, `id`, 'pjCmsField', '::LOCALE::', 'title', @content, 'plugin' FROM `fields` WHERE `key` = 'allow_payment_method_ARRAY_0' ON DUPLICATE KEY UPDATE `multi_lang`.`content` = @content, `source` = 'plugin';
