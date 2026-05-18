START TRANSACTION;

DROP TABLE IF EXISTS `product_import_history`;

CREATE TABLE
    IF NOT EXISTS `product_import_history` (
        `id` int (10) unsigned NOT NULL AUTO_INCREMENT,
        `company_id` int (10) unsigned DEFAULT NULL,
        `file_name` varchar(255) DEFAULT NULL,
        `file_path` text,
        `file_size` int (10) unsigned DEFAULT NULL,
        `total_rows` int (10) unsigned DEFAULT NULL,
        `processed_rows` int (10) unsigned DEFAULT '0',
        `failed_rows` int (10) unsigned DEFAULT '0',
        `status` enum ('uploaded', 'processing', 'finished', 'failed') DEFAULT 'uploaded',
        `uploaded_by` int (10) unsigned DEFAULT NULL,
        `uploaded_at` datetime DEFAULT NULL,
        `synced_by` int (10) unsigned DEFAULT NULL,
        `synced_at` datetime DEFAULT NULL,
        `sync_count` int (10) unsigned DEFAULT '0',
        `error_message` text,
        PRIMARY KEY (`id`),
        KEY `company_id` (`company_id`),
        KEY `uploaded_by` (`uploaded_by`),
        KEY `synced_by` (`synced_by`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8;

ALTER TABLE `product_import_history` ADD display_name VARCHAR(255) DEFAULT NULL AFTER file_name;

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoProductsImportManager', 'backend', 'Infobox / Product Import Manager', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Product Import Manager', 'script');


INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoProductsImportManagerDesc', 'backend', 'Infobox / List of import csv ', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Upload CSV files and review the data before syncing. Only the latest import can be synced, while full history is kept for reference.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL,'import_page_title','backend','import_page_title','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Product CSV Import','script');

INSERT INTO `plugin_base_fields` VALUES (NULL,'import_upload_section','backend','import_upload_section','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Upload CSV File','script');

INSERT INTO `plugin_base_fields` VALUES (NULL,'import_history_section','backend','import_history_section','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Import History','script');

INSERT INTO `plugin_base_fields` VALUES (NULL,'import_table_id','backend','import_table_id','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','ID','script');

INSERT INTO `plugin_base_fields` VALUES (NULL,'import_table_file','backend','import_table_file','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','File Name','script');

INSERT INTO `plugin_base_fields` VALUES (NULL,'import_table_rows','backend','import_table_rows','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Total Rows','script');

INSERT INTO `plugin_base_fields` VALUES (NULL,'import_table_size','backend','import_table_size','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','File Size','script');

INSERT INTO `plugin_base_fields` VALUES (NULL,'import_table_status','backend','import_table_status','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Status','script');

INSERT INTO `plugin_base_fields` VALUES (NULL,'import_table_uploaded_by','backend','import_table_uploaded_by','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Uploaded By','script');

INSERT INTO `plugin_base_fields` VALUES (NULL,'import_table_uploaded_at','backend','import_table_uploaded_at','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Uploaded At','script');

INSERT INTO `plugin_base_fields` VALUES (NULL,'import_btn_upload','backend','import_btn_upload','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Upload CSV','script');

INSERT INTO `plugin_base_fields` VALUES (NULL,'import_btn_start_import','backend','import_btn_start_import','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Start Import','script');

INSERT INTO `plugin_base_fields` VALUES (NULL,'import_btn_download_sample','backend','import_btn_download_sample','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Download Sample CSV','script');

INSERT INTO `plugin_base_fields` VALUES (NULL,'import_status_uploaded','backend','import_status_uploaded','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Uploaded','script');

INSERT INTO `plugin_base_fields` VALUES (NULL,'import_status_processing','backend','import_status_processing','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Processing','script');

INSERT INTO `plugin_base_fields` VALUES (NULL,'import_status_completed','backend','import_status_completed','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Completed','script');

INSERT INTO `plugin_base_fields` VALUES (NULL,'import_status_failed','backend','import_status_failed','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Failed','script');

INSERT INTO `plugin_base_fields` VALUES (NULL,'import_upload_success','backend','import_upload_success','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','File uploaded successfully','script');

INSERT INTO `plugin_base_fields` VALUES (NULL,'import_completed_success','backend','import_completed_success','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Products imported successfully','script');

INSERT INTO `plugin_base_fields` VALUES (NULL,'import_invalid_request','backend','import_invalid_request','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Invalid request','script');

INSERT INTO `plugin_base_fields` VALUES (NULL,'import_csv_missing','backend','import_csv_missing','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','CSV file missing','script');

INSERT INTO `plugin_base_fields` VALUES (NULL,'import_file_upload_failed','backend','import_file_upload_failed','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','File upload failed','script');

INSERT INTO `plugin_base_fields` VALUES (NULL,'import_csv_read_error','backend','import_csv_read_error','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Unable to read CSV file','script');

INSERT INTO `plugin_base_fields` VALUES (NULL,'import_header_missing','backend','import_header_missing','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','CSV header missing','script');

INSERT INTO `plugin_base_fields` VALUES (NULL,'import_invalid_header','backend','import_invalid_header','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Invalid CSV header detected','script');

INSERT INTO `plugin_base_fields` VALUES (NULL,'import_mandatory_missing','backend','import_mandatory_missing','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Mandatory field missing','script');

INSERT INTO `plugin_base_fields` VALUES (NULL,'import_sku_conflict','backend','import_sku_conflict','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','SKU conflict detected. Same SKU with different model.','script');

INSERT INTO `plugin_base_fields` VALUES (NULL,'import_article_conflict','backend','import_article_conflict','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Article number conflict detected. Same article number with different model.','script');

INSERT INTO `plugin_base_fields` VALUES (NULL,'import_db_insert_failed','backend','import_db_insert_failed','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Database insert failed','script');




INSERT INTO `plugin_base_fields` VALUES (NULL,'import_sync_at','backend','import_sync_at','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Sync At','script');

INSERT INTO `plugin_base_fields` VALUES (NULL,'import_sync','backend','import_sync','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Sync','script');


-- Import Processing Message
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_processing_message','backend','import_processing_message','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Please wait while products are being synced...','script');

-- Import Completed Title
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_completed_title','backend','import_completed_title','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Import Completed','script');

-- Import Completed Message
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_completed_message','backend','import_completed_message','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','CSV products imported successfully','script');

-- Import Error Title
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_error_title','backend','import_error_title','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Import Error','script');

-- Uploading CSV Title
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_uploading_title','backend','import_uploading_title','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Uploading CSV','script');

-- Uploading CSV Message
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_uploading_message','backend','import_uploading_message','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Please wait while the file is uploading...','script');

-- Upload Success Title
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_upload_success_title','backend','import_upload_success_title','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Upload Successful','script');

-- Upload Error Title
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_upload_error_title','backend','import_upload_error_title','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Upload Error','script');

-- Upload Failed Title
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_upload_failed_title','backend','import_upload_failed_title','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Upload Failed','script');

-- No File Selected Title
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_no_file_title','backend','import_no_file_title','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','No File Selected','script');

-- No File Selected Message
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_no_file_message','backend','import_no_file_message','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Please select a CSV file before uploading.','script');

-- Invalid File Title
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_invalid_file_title','backend','import_invalid_file_title','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Invalid File','script');

-- Invalid File Message
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_invalid_file_message','backend','import_invalid_file_message','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Only CSV files are allowed.','script');

-- Unexpected Server Error
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_server_error','backend','import_server_error','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Unexpected server error occurred.','script');
 
INSERT INTO `plugin_base_fields` VALUES (NULL, 'menuImportProduct', 'backend', 'Infobox / Import Products ', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Import Products ', 'script');





COMMIT;