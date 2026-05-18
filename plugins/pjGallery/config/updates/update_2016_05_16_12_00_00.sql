
START TRANSACTION;

ALTER TABLE `plugin_gallery` ADD `model` VARCHAR(80) NULL AFTER `foreign_id`, ADD INDEX (`model`);

COMMIT;