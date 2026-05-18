
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_reset_search', 'backend', 'Gallery plugin / Reset search', 'plugin', '2016-08-23 06:46:49');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Reset search', 'plugin');

COMMIT;