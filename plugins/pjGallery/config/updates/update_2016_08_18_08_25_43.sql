
START TRANSACTION;

ALTER TABLE `plugin_gallery` ADD COLUMN `title` varchar(255) DEFAULT NULL AFTER `name`;
ALTER TABLE `plugin_gallery` ADD COLUMN `description` text DEFAULT NULL AFTER `title`;

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_title', 'backend', 'Gallery plugin / Title', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Title', 'script');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_description', 'backend', 'Gallery plugin / Description', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Description', 'script');

COMMIT;