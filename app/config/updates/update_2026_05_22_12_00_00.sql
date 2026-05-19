START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'product_model_image_tab', 'backend', 'Model image', 'script', '2026-05-22 12:00:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Model image', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblProductModelImageTabLead', 'backend', 'Label / Product edit / Model image tab lead', 'script', '2026-05-22 12:00:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Upload one listing/model photo for shop and category grids. It is stored separately from product photos and stock images.', 'script');

INSERT INTO `fields` VALUES (NULL, 'info_ARRAY_product_model_image_title', 'arrays', 'info_ARRAY_product_model_image_title', 'script', '2026-05-22 12:00:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Model image', 'script');

INSERT INTO `fields` VALUES (NULL, 'info_ARRAY_product_model_image_body', 'arrays', 'info_ARRAY_product_model_image_body', 'script', '2026-05-22 12:00:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Use this tab only for the model/listing thumbnail. Product gallery images belong on the Photos tab.', 'script');

COMMIT;
