
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_uploading', 'backend', 'Gallery plugin / Uploading...', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Uploading...', 'plugin');


COMMIT;