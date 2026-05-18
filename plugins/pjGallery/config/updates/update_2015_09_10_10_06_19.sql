
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_watermark_text', 'backend', 'Gallery plugin / Watermark text', 'plugin', '2015-09-10 17:06:06');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Watermark text', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_watermark_image', 'backend', 'Gallery plugin / Watermark text', 'plugin', '2015-09-10 17:06:06');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Watermark image', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_compression_title', 'backend', 'Gallery plugin / Watermark text', 'plugin', '2015-09-10 17:06:06');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Compress image', 'plugin');

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "plugin_gallery_photos");
UPDATE `multi_lang` SET `content` = 'images' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "plugin_gallery_compression_note");
UPDATE `multi_lang` SET `content` = 'Compress all images using the slider to specify percentage. Please note that reducing the size of the images leads to quality losses.' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

COMMIT;