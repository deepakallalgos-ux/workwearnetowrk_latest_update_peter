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



/*
 * Corrective: if update_2026_05_21 ran with `gallery` (wrong), re-tag model images on plugin_gallery.
 * Safe to run multiple times.
 */
UPDATE `plugin_gallery` AS `g`
SET `g`.`model` = 'pjProductModelImage'
WHERE `g`.`model` = 'pjProduct'
AND `g`.`id` IN (
	SELECT `model_image_id` FROM `products` WHERE `model_image_id` IS NOT NULL AND `model_image_id` > 0
);


-- Import sync UI (plugin_base_fields) — idempotent, no duplicate keys
INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'import_sync_modal_title', 'backend', 'import_sync_modal_title', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'import_sync_modal_title');
SET @id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'import_sync_modal_title' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @id, 'pjField', l.id, 'title', 'Sync in progress', 'script'
FROM `plugin_base_locale` l
WHERE @id IS NOT NULL AND NOT EXISTS (
	SELECT 1 FROM `plugin_base_multi_lang` m WHERE m.`foreign_id` = @id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
);

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'import_sync_modal_sub', 'backend', 'import_sync_modal_sub', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'import_sync_modal_sub');
SET @id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'import_sync_modal_sub' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @id, 'pjField', l.id, 'title', 'Rows are processed in batches. You can keep this window open until it finishes.', 'script'
FROM `plugin_base_locale` l
WHERE @id IS NOT NULL AND NOT EXISTS (
	SELECT 1 FROM `plugin_base_multi_lang` m WHERE m.`foreign_id` = @id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
);

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'import_sync_preparing', 'backend', 'import_sync_preparing', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'import_sync_preparing');
SET @id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'import_sync_preparing' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @id, 'pjField', l.id, 'title', 'Preparing…', 'script'
FROM `plugin_base_locale` l
WHERE @id IS NOT NULL AND NOT EXISTS (
	SELECT 1 FROM `plugin_base_multi_lang` m WHERE m.`foreign_id` = @id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
);

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'import_sync_progress_row', 'backend', 'import_sync_progress_row', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'import_sync_progress_row');
SET @id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'import_sync_progress_row' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @id, 'pjField', l.id, 'title', 'Row {processed} of {total}', 'script'
FROM `plugin_base_locale` l
WHERE @id IS NOT NULL AND NOT EXISTS (
	SELECT 1 FROM `plugin_base_multi_lang` m WHERE m.`foreign_id` = @id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
);

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'import_sync_last_batch', 'backend', 'import_sync_last_batch', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'import_sync_last_batch');
SET @id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'import_sync_last_batch' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @id, 'pjField', l.id, 'title', 'Last batch: {batch} row(s)', 'script'
FROM `plugin_base_locale` l
WHERE @id IS NOT NULL AND NOT EXISTS (
	SELECT 1 FROM `plugin_base_multi_lang` m WHERE m.`foreign_id` = @id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
);

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'import_invalid_response', 'backend', 'import_invalid_response', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'import_invalid_response');
SET @id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'import_invalid_response' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @id, 'pjField', l.id, 'title', 'Invalid response from server.', 'script'
FROM `plugin_base_locale` l
WHERE @id IS NOT NULL AND NOT EXISTS (
	SELECT 1 FROM `plugin_base_multi_lang` m WHERE m.`foreign_id` = @id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
);

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'import_sync_timeout', 'backend', 'import_sync_timeout', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'import_sync_timeout');
SET @id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'import_sync_timeout' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @id, 'pjField', l.id, 'title', 'Sync timed out. The server may still be processing — wait a moment and refresh the page.', 'script'
FROM `plugin_base_locale` l
WHERE @id IS NOT NULL AND NOT EXISTS (
	SELECT 1 FROM `plugin_base_multi_lang` m WHERE m.`foreign_id` = @id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
);

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'import_sync_selected_confirm', 'backend', 'import_sync_selected_confirm', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'import_sync_selected_confirm');
SET @id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'import_sync_selected_confirm' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @id, 'pjField', l.id, 'title', 'Are you sure you want to sync selected products?', 'script'
FROM `plugin_base_locale` l
WHERE @id IS NOT NULL AND NOT EXISTS (
	SELECT 1 FROM `plugin_base_multi_lang` m WHERE m.`foreign_id` = @id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
);

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'import_no_rows_selected_title', 'backend', 'import_no_rows_selected_title', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'import_no_rows_selected_title');
SET @id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'import_no_rows_selected_title' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @id, 'pjField', l.id, 'title', 'No rows selected', 'script'
FROM `plugin_base_locale` l
WHERE @id IS NOT NULL AND NOT EXISTS (
	SELECT 1 FROM `plugin_base_multi_lang` m WHERE m.`foreign_id` = @id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
);

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'import_no_rows_selected_text', 'backend', 'import_no_rows_selected_text', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'import_no_rows_selected_text');
SET @id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'import_no_rows_selected_text' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @id, 'pjField', l.id, 'title', 'Please select at least one product to sync.', 'script'
FROM `plugin_base_locale` l
WHERE @id IS NOT NULL AND NOT EXISTS (
	SELECT 1 FROM `plugin_base_multi_lang` m WHERE m.`foreign_id` = @id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
);

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'import_error_generic', 'backend', 'import_error_generic', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'import_error_generic');
SET @id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'import_error_generic' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @id, 'pjField', l.id, 'title', 'Error', 'script'
FROM `plugin_base_locale` l
WHERE @id IS NOT NULL AND NOT EXISTS (
	SELECT 1 FROM `plugin_base_multi_lang` m WHERE m.`foreign_id` = @id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
);

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'import_request_failed', 'backend', 'import_request_failed', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'import_request_failed');
SET @id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'import_request_failed' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @id, 'pjField', l.id, 'title', 'Request failed', 'script'
FROM `plugin_base_locale` l
WHERE @id IS NOT NULL AND NOT EXISTS (
	SELECT 1 FROM `plugin_base_multi_lang` m WHERE m.`foreign_id` = @id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
);

-- Model image UI (fields table)
INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'lblProductModelImageEmpty', 'backend', 'Label / No model image selected', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `fields` WHERE `key` = 'lblProductModelImageEmpty');
SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'lblProductModelImageEmpty' LIMIT 1);
INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @id, 'pjField', '::LOCALE::', 'title', 'No model image selected', 'script'
FROM DUAL
WHERE @id IS NOT NULL AND NOT EXISTS (
	SELECT 1 FROM `multi_lang` m WHERE m.`foreign_id` = @id AND m.`model` = 'pjField' AND m.`field` = 'title' AND m.`locale` = '::LOCALE::'
);

INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'btnUploadModelImage', 'backend', 'Button / Upload model image', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `fields` WHERE `key` = 'btnUploadModelImage');
SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'btnUploadModelImage' LIMIT 1);
INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @id, 'pjField', '::LOCALE::', 'title', 'Upload model image', 'script'
FROM DUAL
WHERE @id IS NOT NULL AND NOT EXISTS (
	SELECT 1 FROM `multi_lang` m WHERE m.`foreign_id` = @id AND m.`model` = 'pjField' AND m.`field` = 'title' AND m.`locale` = '::LOCALE::'
);

INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'btnReplaceModelLibrary', 'backend', 'Button / Replace from model library', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `fields` WHERE `key` = 'btnReplaceModelLibrary');
SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'btnReplaceModelLibrary' LIMIT 1);
INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @id, 'pjField', '::LOCALE::', 'title', 'Replace from model library', 'script'
FROM DUAL
WHERE @id IS NOT NULL AND NOT EXISTS (
	SELECT 1 FROM `multi_lang` m WHERE m.`foreign_id` = @id AND m.`model` = 'pjField' AND m.`field` = 'title' AND m.`locale` = '::LOCALE::'
);

INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'infoModelImageLibraryHint', 'backend', 'Hint / Model image library picker', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `fields` WHERE `key` = 'infoModelImageLibraryHint');
SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'infoModelImageLibraryHint' LIMIT 1);
INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @id, 'pjField', '::LOCALE::', 'title', 'Pick a previously uploaded model image for this product', 'script'
FROM DUAL
WHERE @id IS NOT NULL AND NOT EXISTS (
	SELECT 1 FROM `multi_lang` m WHERE m.`foreign_id` = @id AND m.`model` = 'pjField' AND m.`field` = 'title' AND m.`locale` = '::LOCALE::'
);

INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'productModelImageUploadFailed', 'backend', 'Error / Model image upload failed title', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `fields` WHERE `key` = 'productModelImageUploadFailed');
SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'productModelImageUploadFailed' LIMIT 1);
INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @id, 'pjField', '::LOCALE::', 'title', 'Upload failed', 'script'
FROM DUAL
WHERE @id IS NOT NULL AND NOT EXISTS (
	SELECT 1 FROM `multi_lang` m WHERE m.`foreign_id` = @id AND m.`model` = 'pjField' AND m.`field` = 'title' AND m.`locale` = '::LOCALE::'
);

INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'productModelImageUploadFailedText', 'backend', 'Error / Model image upload failed text', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `fields` WHERE `key` = 'productModelImageUploadFailedText');
SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'productModelImageUploadFailedText' LIMIT 1);
INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @id, 'pjField', '::LOCALE::', 'title', 'Could not upload model image.', 'script'
FROM DUAL
WHERE @id IS NOT NULL AND NOT EXISTS (
	SELECT 1 FROM `multi_lang` m WHERE m.`foreign_id` = @id AND m.`model` = 'pjField' AND m.`field` = 'title' AND m.`locale` = '::LOCALE::'
);

INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'infoModelImagePickerLead', 'backend', 'Help / Model image picker modal', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `fields` WHERE `key` = 'infoModelImagePickerLead');
SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'infoModelImagePickerLead' LIMIT 1);
INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @id, 'pjField', '::LOCALE::', 'title', 'Only <strong>model images</strong> uploaded for listings appear here — not product photos from the Photos tab or stock images.', 'script'
FROM DUAL
WHERE @id IS NOT NULL AND NOT EXISTS (
	SELECT 1 FROM `multi_lang` m WHERE m.`foreign_id` = @id AND m.`model` = 'pjField' AND m.`field` = 'title' AND m.`locale` = '::LOCALE::'
);

INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'infoStockImagePickerLead', 'backend', 'Help / Stock image picker modal', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `fields` WHERE `key` = 'infoStockImagePickerLead');
SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'infoStockImagePickerLead' LIMIT 1);
INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @id, 'pjField', '::LOCALE::', 'title', 'Product and stock photos only (not model/listing images).', 'script'
FROM DUAL
WHERE @id IS NOT NULL AND NOT EXISTS (
	SELECT 1 FROM `multi_lang` m WHERE m.`foreign_id` = @id AND m.`model` = 'pjField' AND m.`field` = 'title' AND m.`locale` = '::LOCALE::'
);

INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'lblProductModelImagePickerEmpty', 'backend', 'Empty / Model image picker', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `fields` WHERE `key` = 'lblProductModelImagePickerEmpty');
SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'lblProductModelImagePickerEmpty' LIMIT 1);
INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @id, 'pjField', '::LOCALE::', 'title', 'No model images yet. Close this window and use %s on the product form.', 'script'
FROM DUAL
WHERE @id IS NOT NULL AND NOT EXISTS (
	SELECT 1 FROM `multi_lang` m WHERE m.`foreign_id` = @id AND m.`model` = 'pjField' AND m.`field` = 'title' AND m.`locale` = '::LOCALE::'
);

INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'btnRemoveModelImage', 'backend', 'Button / Remove model image', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `fields` WHERE `key` = 'btnRemoveModelImage');
SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'btnRemoveModelImage' LIMIT 1);
INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @id, 'pjField', '::LOCALE::', 'title', 'Remove', 'script'
FROM DUAL
WHERE @id IS NOT NULL AND NOT EXISTS (
	SELECT 1 FROM `multi_lang` m WHERE m.`foreign_id` = @id AND m.`model` = 'pjField' AND m.`field` = 'title' AND m.`locale` = '::LOCALE::'
);

INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'product_model_image_access_denied', 'backend', 'API / Model image access denied', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `fields` WHERE `key` = 'product_model_image_access_denied');
SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'product_model_image_access_denied' LIMIT 1);
INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @id, 'pjField', '::LOCALE::', 'title', 'Access denied.', 'script'
FROM DUAL
WHERE @id IS NOT NULL AND NOT EXISTS (
	SELECT 1 FROM `multi_lang` m WHERE m.`foreign_id` = @id AND m.`model` = 'pjField' AND m.`field` = 'title' AND m.`locale` = '::LOCALE::'
);

INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'product_model_image_not_found', 'backend', 'API / Model image product not found', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `fields` WHERE `key` = 'product_model_image_not_found');
SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'product_model_image_not_found' LIMIT 1);
INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @id, 'pjField', '::LOCALE::', 'title', 'Product not found.', 'script'
FROM DUAL
WHERE @id IS NOT NULL AND NOT EXISTS (
	SELECT 1 FROM `multi_lang` m WHERE m.`foreign_id` = @id AND m.`model` = 'pjField' AND m.`field` = 'title' AND m.`locale` = '::LOCALE::'
);

INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'product_model_image_choose_file', 'backend', 'API / Model image choose file', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `fields` WHERE `key` = 'product_model_image_choose_file');
SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'product_model_image_choose_file' LIMIT 1);
INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please choose an image file to upload.', 'script'
FROM DUAL
WHERE @id IS NOT NULL AND NOT EXISTS (
	SELECT 1 FROM `multi_lang` m WHERE m.`foreign_id` = @id AND m.`model` = 'pjField' AND m.`field` = 'title' AND m.`locale` = '::LOCALE::'
);

INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'product_model_image_invalid_file', 'backend', 'API / Model image invalid file', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `fields` WHERE `key` = 'product_model_image_invalid_file');
SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'product_model_image_invalid_file' LIMIT 1);
INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @id, 'pjField', '::LOCALE::', 'title', 'Invalid image file.', 'script'
FROM DUAL
WHERE @id IS NOT NULL AND NOT EXISTS (
	SELECT 1 FROM `multi_lang` m WHERE m.`foreign_id` = @id AND m.`model` = 'pjField' AND m.`field` = 'title' AND m.`locale` = '::LOCALE::'
);

INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'product_model_image_corrupted', 'backend', 'API / Model image corrupted', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `fields` WHERE `key` = 'product_model_image_corrupted');
SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'product_model_image_corrupted' LIMIT 1);
INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @id, 'pjField', '::LOCALE::', 'title', 'Image is corrupted or invalid.', 'script'
FROM DUAL
WHERE @id IS NOT NULL AND NOT EXISTS (
	SELECT 1 FROM `multi_lang` m WHERE m.`foreign_id` = @id AND m.`model` = 'pjField' AND m.`field` = 'title' AND m.`locale` = '::LOCALE::'
);

INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'product_model_image_save_failed', 'backend', 'API / Model image save failed', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `fields` WHERE `key` = 'product_model_image_save_failed');
SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'product_model_image_save_failed' LIMIT 1);
INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @id, 'pjField', '::LOCALE::', 'title', 'Could not save uploaded image.', 'script'
FROM DUAL
WHERE @id IS NOT NULL AND NOT EXISTS (
	SELECT 1 FROM `multi_lang` m WHERE m.`foreign_id` = @id AND m.`model` = 'pjField' AND m.`field` = 'title' AND m.`locale` = '::LOCALE::'
);

INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'product_model_image_gallery_failed', 'backend', 'API / Model image gallery failed', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `fields` WHERE `key` = 'product_model_image_gallery_failed');
SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'product_model_image_gallery_failed' LIMIT 1);
INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @id, 'pjField', '::LOCALE::', 'title', 'Could not create gallery record.', 'script'
FROM DUAL
WHERE @id IS NOT NULL AND NOT EXISTS (
	SELECT 1 FROM `multi_lang` m WHERE m.`foreign_id` = @id AND m.`model` = 'pjField' AND m.`field` = 'title' AND m.`locale` = '::LOCALE::'
);

INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'product_flatfile_model_change_warning', 'backend', 'Warning / Flat file model change', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `fields` WHERE `key` = 'product_flatfile_model_change_warning');
SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'product_flatfile_model_change_warning' LIMIT 1);
INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @id, 'pjField', '::LOCALE::', 'title', 'WARNING: If you change the model you break the product family.', 'script'
FROM DUAL
WHERE @id IS NOT NULL AND NOT EXISTS (
	SELECT 1 FROM `multi_lang` m WHERE m.`foreign_id` = @id AND m.`model` = 'pjField' AND m.`field` = 'title' AND m.`locale` = '::LOCALE::'
);


INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'infoModelImageTabGrid', 'backend', 'Help / Model image tab grid', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `fields` WHERE `key` = 'infoModelImageTabGrid');
SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'infoModelImageTabGrid' LIMIT 1);
INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @id, 'pjField', '::LOCALE::', 'title', 'Click an image to set the listing photo used in shop grids. Upload more below; use × to delete an image.', 'script'
FROM DUAL
WHERE @id IS NOT NULL AND NOT EXISTS (
	SELECT 1 FROM `multi_lang` m WHERE m.`foreign_id` = @id AND m.`model` = 'pjField' AND m.`field` = 'title' AND m.`locale` = '::LOCALE::'
);

INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'lblProductModelImageGridEmpty', 'backend', 'Empty / Model image grid', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `fields` WHERE `key` = 'lblProductModelImageGridEmpty');
SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'lblProductModelImageGridEmpty' LIMIT 1);
INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @id, 'pjField', '::LOCALE::', 'title', 'No model images yet. Use Upload model image to add the first one.', 'script'
FROM DUAL
WHERE @id IS NOT NULL AND NOT EXISTS (
	SELECT 1 FROM `multi_lang` m WHERE m.`foreign_id` = @id AND m.`model` = 'pjField' AND m.`field` = 'title' AND m.`locale` = '::LOCALE::'
);

INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'lblProductModelImageActive', 'backend', 'Label / Active model image', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `fields` WHERE `key` = 'lblProductModelImageActive');
SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'lblProductModelImageActive' LIMIT 1);
INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @id, 'pjField', '::LOCALE::', 'title', 'Active', 'script'
FROM DUAL
WHERE @id IS NOT NULL AND NOT EXISTS (
	SELECT 1 FROM `multi_lang` m WHERE m.`foreign_id` = @id AND m.`model` = 'pjField' AND m.`field` = 'title' AND m.`locale` = '::LOCALE::'
);

INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'btnDeleteModelImage', 'backend', 'Button / Delete model image', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `fields` WHERE `key` = 'btnDeleteModelImage');
SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'btnDeleteModelImage' LIMIT 1);
INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete image', 'script'
FROM DUAL
WHERE @id IS NOT NULL AND NOT EXISTS (
	SELECT 1 FROM `multi_lang` m WHERE m.`foreign_id` = @id AND m.`model` = 'pjField' AND m.`field` = 'title' AND m.`locale` = '::LOCALE::'
);

UPDATE `plugin_base_multi_lang`
SET content = REPLACE(content, 'Orders', 'Quotes');

UPDATE `plugin_base_multi_lang`
SET content = REPLACE(content, 'Order', 'Quote');

UPDATE `plugin_base_multi_lang`
SET content = REPLACE(content, 'orders', 'quotes');

UPDATE `plugin_base_multi_lang`
SET content = REPLACE(content, 'order', 'quote');
COMMIT;
