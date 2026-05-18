
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_drag_drop_title', 'backend', 'Gallery plugin / Drag & Drop title', 'plugin', '2016-01-07 15:02:34');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Drag & Drop', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_drag_drop_sub_title', 'backend', 'Gallery plugin / Drag & Drop sub-title', 'plugin', '2016-01-07 15:02:55');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'to upload new image', 'plugin');

COMMIT;