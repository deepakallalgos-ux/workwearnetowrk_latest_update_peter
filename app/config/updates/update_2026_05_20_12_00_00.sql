START TRANSACTION;

ALTER TABLE `products`
ADD COLUMN `model_image_id` INT(10) UNSIGNED NULL DEFAULT NULL AFTER `is_digital`,
ADD COLUMN `material` TEXT NULL DEFAULT NULL AFTER `model_image_id`,
ADD COLUMN `safety_standard` TEXT NULL DEFAULT NULL AFTER `material`;

ALTER TABLE `stocks`
ADD COLUMN `buying_price` DECIMAL(10,2) NULL DEFAULT NULL AFTER `price`;

ALTER TABLE `product_import_rows`
ADD COLUMN `model_image` TEXT NULL DEFAULT NULL AFTER `image`,
ADD COLUMN `material` TEXT NULL DEFAULT NULL AFTER `model_image`,
ADD COLUMN `safety_standard` TEXT NULL DEFAULT NULL AFTER `material`,
ADD COLUMN `buying_price` DECIMAL(10,2) NULL DEFAULT NULL AFTER `price`;

INSERT INTO `fields` VALUES (NULL, 'product_model_image', 'backend', 'Model image', 'script', '2026-05-20 12:00:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Model image', 'script');

INSERT INTO `fields` VALUES (NULL, 'product_material', 'backend', 'Material', 'script', '2026-05-20 12:00:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Material', 'script');

INSERT INTO `fields` VALUES (NULL, 'product_safety_standard', 'backend', 'Safety standard', 'script', '2026-05-20 12:00:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Safety standard', 'script');

INSERT INTO `fields` VALUES (NULL, 'product_stock_buying_price', 'backend', 'Buying price', 'script', '2026-05-20 12:00:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Buying price', 'script');

INSERT INTO `fields` VALUES (NULL, 'import_model_image', 'backend', 'Model image', 'script', '2026-05-20 12:00:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Model image', 'script');

INSERT INTO `fields` VALUES (NULL, 'import_material', 'backend', 'Material', 'script', '2026-05-20 12:00:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Material', 'script');

INSERT INTO `fields` VALUES (NULL, 'import_safety_standard', 'backend', 'Safety standard', 'script', '2026-05-20 12:00:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Safety standard', 'script');

INSERT INTO `fields` VALUES (NULL, 'import_buying_price', 'backend', 'Buying price', 'script', '2026-05-20 12:00:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Buying price', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'front_product_material', 'frontend', 'Material', 'script', '2026-05-20 12:00:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Material', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'front_product_safety_standard', 'frontend', 'Safety standard', 'script', '2026-05-20 12:00:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Safety standard', 'script');

COMMIT;
