
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_crop_btn_crop', 'backend', 'Gallery plugin / Crop & Save', 'plugin', '2016-04-28 10:38:23');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Crop & Save', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_crop_btn_back', 'backend', 'Plugin gallery / Back', 'plugin', '2016-04-28 10:42:16');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '&laquo; Back', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_image_version', 'backend', 'Plugin gallery / Image version', 'plugin', '2016-04-28 10:43:38');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Image version', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_image_will_cropped', 'backend', 'Plugin gallery / Image will be cropped to', 'plugin', '2016-04-28 10:59:05');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Image will be cropped to', 'plugin');

COMMIT;