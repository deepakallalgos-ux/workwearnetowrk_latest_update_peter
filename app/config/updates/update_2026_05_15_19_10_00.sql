START TRANSACTION;

-- Flat file + CSV import: admin permissions (for installs that use updates after database.sql).

INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`)
SELECT NULL, p.id, 'pjAdminProducts_pjActionProductsFlatFileIndex'
FROM `plugin_auth_permissions` p
WHERE p.`key` = 'pjAdminProducts'
  AND NOT EXISTS (SELECT 1 FROM `plugin_auth_permissions` WHERE `key` = 'pjAdminProducts_pjActionProductsFlatFileIndex')
LIMIT 1;

INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`)
SELECT NULL, NULL, 'pjAdminProductImportHistory' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_auth_permissions` WHERE `key` = 'pjAdminProductImportHistory');

INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`)
SELECT NULL, p.id, 'pjAdminProductImportHistory_pjActionIndex'
FROM `plugin_auth_permissions` p
WHERE p.`key` = 'pjAdminProductImportHistory'
  AND NOT EXISTS (SELECT 1 FROM `plugin_auth_permissions` WHERE `key` = 'pjAdminProductImportHistory_pjActionIndex')
LIMIT 1;

COMMIT;
