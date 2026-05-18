START TRANSACTION;

ALTER TABLE `stocks`
ADD COLUMN `status` ENUM('T','F') NOT NULL DEFAULT 'T' AFTER `id`;

INSERT INTO `plugin_base_fields` VALUES (NULL, 'menuProductListFlatFileView', 'backend', 'Menu Product flat file view', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Product flat file view', 'script');



INSERT INTO `fields` VALUES (NULL, 'lblUploadManageImages', 'backend', 'Label / Upload / Manage Images', 'script', NULL);
SET @id := (
SELECT  LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Upload / Manage Images', 'script');
COMMIT;