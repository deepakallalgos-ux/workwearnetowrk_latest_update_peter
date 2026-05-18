START TRANSACTION;


INSERT INTO `fields` VALUES (NULL, 'product_v_model', 'backend', 'product_v_model', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'There is another product with such model.', 'script');

ALTER TABLE `products`
ADD COLUMN `model` varchar(255) NULL AFTER `id`;

ALTER TABLE `products`
ADD COLUMN `model_name` varchar(255) NULL AFTER `model`;

ALTER TABLE `stocks`
ADD COLUMN `article_number` varchar(255) NULL AFTER `id`;

ALTER TABLE `stocks`
ADD COLUMN `article_name` varchar(255) NULL AFTER `article_number`;

ALTER TABLE `stocks`
ADD COLUMN `ean` varchar(255) NULL AFTER `article_name`;

INSERT INTO `fields` VALUES (NULL, 'product_model', 'backend', 'Model', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Model', 'script');


INSERT INTO `fields` VALUES (NULL, 'product_model_name', 'backend', 'Model name', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Model name', 'script');


INSERT INTO `fields` VALUES (NULL, 'product_article_number', 'backend', 'Article number', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Article number', 'script');

INSERT INTO `fields` VALUES (NULL, 'product_article_name', 'backend', 'Article name', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Article name', 'script');

INSERT INTO `fields` VALUES (NULL, 'product_ean', 'backend', 'EAN', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'EAN', 'script');

COMMIT;