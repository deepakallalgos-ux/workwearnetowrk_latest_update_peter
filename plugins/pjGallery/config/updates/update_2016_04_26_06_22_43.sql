
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_tab_gallery', 'backend', 'Gallery plugin / Tab Gallery', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Gallery', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_info_galleries_title', 'backend', 'Gallery plugin / Galleries list', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Galleries list', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_info_galleries_desc', 'backend', 'Gallery plugin / Galleries list', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Below is the list of galleries. You can click on the Edit icon to update any gallery you want.', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_statuses_ARRAY_T', 'arrays', 'Gallery plugin / Status (active)', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Active', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_statuses_ARRAY_F', 'arrays', 'Gallery plugin / Status (inactive)', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Inactive', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_btn_add', 'backend', 'Gallery plugin / Button + Add gallery', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '+ Add gallery', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_btn_search', 'backend', 'Gallery plugin / Button Search', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Search', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_btn_all', 'backend', 'Gallery plugin / Button All', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_name', 'backend', 'Gallery plugin / Name', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Name', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_status', 'backend', 'Gallery plugin / Status', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Status', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_revert_status', 'backend', 'Gallery plugin / Revert status', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Revert status', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_delete_set_confirmation', 'backend', 'Gallery plugin / Delete confirmation', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Are you sure you want to delete selected gallery?', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_delete_selected', 'backend', 'Gallery plugin / Delete selected', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete selected', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_PGS01', 'arrays', 'error_titles_ARRAY_PGS01', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Options updated', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_PGS03', 'arrays', 'error_titles_ARRAY_PGS03', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Gallery added', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_PGS04', 'arrays', 'error_titles_ARRAY_PGS04', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Gallery failed to add', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_PGS08', 'arrays', 'error_titles_ARRAY_PGS08', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Gallery not found', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_PGS01', 'arrays', 'error_bodies_ARRAY_PGS01', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Options of gallery has been updated successfully.', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_PGS03', 'arrays', 'error_bodies_ARRAY_PGS03', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Gallery has been added successfully.', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_PGS04', 'arrays', 'error_bodies_ARRAY_PGS04', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Gallery has not been added successfully.', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_PGS08', 'arrays', 'error_bodies_ARRAY_PGS08', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Gallery you are looking for has not been found.', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_info_add_gallery_title', 'backend', 'Gallery plugin / Add gallery', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add gallery', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_info_add_gallery_desc', 'backend', 'Gallery plugin / Add gallery', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Fill in the form below and click Save button to create new gallery.', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_medium_width', 'backend', 'Gallery plugin / Medium width', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Medium width', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_medium_height', 'backend', 'Gallery plugin / Medium height', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Medium height', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_thumb_width', 'backend', 'Gallery plugin / Thumb width', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Thumb width', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_thumb_height', 'backend', 'Gallery plugin / Thumb height', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Thumb height', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_tab_images', 'backend', 'Gallery plugin / Images', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Images', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_tab_options', 'backend', 'Gallery plugin / Options', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Options', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_info_images_title', 'backend', 'Gallery plugin / Images', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Images', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_info_images_desc', 'backend', 'Gallery plugin / Images', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Upload as many as images you want for this gallery. You can resize, crop, rotate, watermark and compress the uploaded images. Drag & drop to change their orders.', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_info_edit_gallery_title', 'backend', 'Gallery plugin / Galler options', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Galler options', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_gallery_info_edit_gallery_desc', 'backend', 'Gallery plugin / Galler options', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please make any change as you want on the below form to set the option for the gallery.', 'plugin');

COMMIT;