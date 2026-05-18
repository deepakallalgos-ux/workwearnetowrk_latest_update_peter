
START TRANSACTION;

ALTER TABLE `plugin_gallery` ADD COLUMN `created` datetime DEFAULT NULL AFTER `sort`;

COMMIT;