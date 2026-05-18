START TRANSACTION;

-- Setup wizard option (first-login demo data flow).
-- Table names WITHOUT shopping_cart_ prefix: the installer adds PJ_SCRIPT_PREFIX automatically.
INSERT INTO `options` (`foreign_id`, `company_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`)
SELECT 1, 1, 'o_setup_wizard_completed', 0, '0', 'Setup wizard completed', 'int', 0, 0, NULL FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `options` WHERE `foreign_id` = 1 AND `key` = 'o_setup_wizard_completed'
);

INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`)
SELECT NULL, NULL, 'pjAdminSetup' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_auth_permissions` WHERE `key` = 'pjAdminSetup');

INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`)
SELECT NULL, p.id, 'pjAdminSetup_pjActionWelcome'
FROM `plugin_auth_permissions` p
WHERE p.`key` = 'pjAdminSetup'
  AND NOT EXISTS (SELECT 1 FROM `plugin_auth_permissions` WHERE `key` = 'pjAdminSetup_pjActionWelcome')
LIMIT 1;

INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`)
SELECT NULL, p.id, 'pjAdminSetup_pjActionLoadDemo'
FROM `plugin_auth_permissions` p
WHERE p.`key` = 'pjAdminSetup'
  AND NOT EXISTS (SELECT 1 FROM `plugin_auth_permissions` WHERE `key` = 'pjAdminSetup_pjActionLoadDemo')
LIMIT 1;

INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`)
SELECT NULL, p.id, 'pjAdminSetup_pjActionSkipDemo'
FROM `plugin_auth_permissions` p
WHERE p.`key` = 'pjAdminSetup'
  AND NOT EXISTS (SELECT 1 FROM `plugin_auth_permissions` WHERE `key` = 'pjAdminSetup_pjActionSkipDemo')
LIMIT 1;

INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`)
SELECT NULL, p.id, 'pjAdminSetup_pjActionRemoveDemo'
FROM `plugin_auth_permissions` p
WHERE p.`key` = 'pjAdminSetup'
  AND NOT EXISTS (SELECT 1 FROM `plugin_auth_permissions` WHERE `key` = 'pjAdminSetup_pjActionRemoveDemo')
LIMIT 1;

INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`)
SELECT NULL, p.id, 'pjAdminSetup_pjActionHasDemoData'
FROM `plugin_auth_permissions` p
WHERE p.`key` = 'pjAdminSetup'
  AND NOT EXISTS (SELECT 1 FROM `plugin_auth_permissions` WHERE `key` = 'pjAdminSetup_pjActionHasDemoData')
LIMIT 1;

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'menuSetupWizard', 'backend', 'Menu: Setup wizard', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'menuSetupWizard');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'menuSetupWizard' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Setup wizard', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang`
      WHERE `foreign_id` = @field_id AND `model` = 'pjField' AND `locale` = l.id AND `field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'pjAdminSetup', 'backend', 'pjAdminSetup', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'pjAdminSetup');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'pjAdminSetup' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjBaseField', l.id, 'title', 'Setup wizard', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang`
      WHERE `foreign_id` = @field_id AND `model` = 'pjBaseField' AND `locale` = l.id AND `field` = 'title'
  );

COMMIT;
