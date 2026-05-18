
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_crop_btn_save', 'backend', 'Gallery plugin / Save', 'plugin', '2016-05-10 02:57:48');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Save', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_resize_large_title', 'backend', 'Gallery plugin / Edit Image', 'plugin', '2016-05-10 03:46:14');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Edit Image', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_resize_large_body', 'backend', 'Gallery plugin / Resize Notice body', 'plugin', '2016-05-10 03:46:39');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Below you can see the original image. Use rotate buttons to rotate it. Click on Thumb or Preview to view other versions of the image used on your website.', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_resize_small_title', 'backend', 'Gallery plugin / Thumb image', 'plugin', '2016-05-10 03:47:42');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Thumb image - {SIZE} pixels', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_resize_small_body', 'backend', 'Gallery plugin / Thumb image', 'plugin', '2016-05-10 03:53:29');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Move the outer parts of the rectangle and/or position it over the image to change framing, aspect ratio or accentuate an object. Click Crop & Save to save new thumb image. {STAG}Create new thumb{ETAG} using the original image.', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_resize_thumb_title', 'backend', 'Gallery plugin / Thumb image', 'plugin', '2016-05-10 04:00:41');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Create new Thumb', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_resize_thumb_body', 'backend', 'Gallery plugin / Thumb image', 'plugin', '2016-05-10 04:01:08');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Move the outer parts of the rectangle and/or position it over the image to change framing, aspect ratio or accentuate an object. Once done, click Crop & Save to save new thumb image.', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_resize_medium_title', 'backend', 'Gallery plugin / Preview image', 'plugin', '2016-05-10 04:29:24');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Preview image - {SIZE} pixels', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_resize_medium_body', 'backend', 'Gallery plugin / Preview image', 'plugin', '2016-05-10 04:29:04');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Move the outer parts of the rectangle and/or position it over the image to change framing, aspect ratio or accentuate an object. Click Crop & Save to save new preview image. {STAG}Create new preview{ETAG} using the original image.', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_resize_preview_title', 'backend', 'Gallery plugin / Preview image', 'plugin', '2016-05-10 04:31:01');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Create new Preview', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_resize_preview_body', 'backend', 'Gallery plugin / Preview image', 'plugin', '2016-05-10 04:31:35');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Move the outer parts of the rectangle and/or position it over the image to change framing, aspect ratio or accentuate an object. Once done, click Crop & Save to save new preview image.', 'plugin');

COMMIT;