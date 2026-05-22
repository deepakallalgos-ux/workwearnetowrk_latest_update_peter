START TRANSACTION;

-- Client ↔ Companies mapping (multiple companies per customer)
-- IMPORTANT: table names are WITHOUT the "shopping_cart_" prefix.
-- The updater/installer automatically prepends PJ_SCRIPT_PREFIX (shopping_cart_) when executing.

CREATE TABLE IF NOT EXISTS `client_companies` (
  `client_id` int(10) unsigned NOT NULL,
  `company_id` int(10) unsigned NOT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`client_id`,`company_id`),
  KEY `company_id` (`company_id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Frontend labels (used by __('...') in views)
-- Insert into fields + plugin_base_multi_lang (same pattern as existing script).

INSERT IGNORE INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
VALUES (NULL, 'front_companies', 'frontend', 'Front / Companies', 'script', NOW());
SET @id := (SELECT `id` FROM `fields` WHERE `key`='front_companies' LIMIT 1);
INSERT IGNORE INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Companies', 'script');

INSERT IGNORE INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
VALUES (NULL, 'front_select_companies', 'frontend', 'Front / Select companies', 'script', NOW());
SET @id := (SELECT `id` FROM `fields` WHERE `key`='front_select_companies' LIMIT 1);
INSERT IGNORE INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Select companies', 'script');

INSERT IGNORE INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
VALUES (NULL, 'front_company_selection_required', 'frontend', 'Front / Company selection required', 'script', NOW());
SET @id := (SELECT `id` FROM `fields` WHERE `key`='front_company_selection_required' LIMIT 1);
INSERT IGNORE INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please select at least one company.', 'script');

INSERT IGNORE INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
VALUES (NULL, 'front_company_not_selected_error', 'frontend', 'Front / Company not selected error', 'script', NOW());
SET @id := (SELECT `id` FROM `fields` WHERE `key`='front_company_not_selected_error' LIMIT 1);
INSERT IGNORE INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can’t quote for these products because they belong to a company you didn’t select.', 'script');

COMMIT;

