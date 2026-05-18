START TRANSACTION;
-- W Number
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_w_number','backend','import_w_number','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','W Number','script');

-- Status
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_status','backend','import_status','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Status','script');

-- Name EN
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_name_en','backend','import_name_en','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Name (EN)','script');

-- Short Description EN
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_short_desc_en','backend','import_short_desc_en','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Short Description','script');

-- Full Description EN
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_full_description_en','backend','import_full_description_en','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Full Description','script');

-- Row Status
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_row_status','backend','import_row_status','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Row Status','script');

-- Sync Status
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_sync_status','backend','import_sync_status','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Sync Status','script');

-- Created At
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_created_at','backend','import_created_at','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Created At','script');







-- Image
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_image','backend','import_image','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Image','script');

-- Model
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_model','backend','import_model','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Model','script');

-- Model Name
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_model_name','backend','import_model_name','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Model Name','script');

-- SKU
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_sku','backend','import_sku','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','SKU','script');

-- Article Number
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_article_number','backend','import_article_number','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Article Number','script');

-- Article Name
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_article_name','backend','import_article_name','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Article Name','script');

-- Brand
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_brand','backend','import_brand','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Brand','script');

-- Category
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_category','backend','import_category','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Category','script');

-- Size
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_size','backend','import_size','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Size','script');

-- Color
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_color','backend','import_color','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Color','script');

-- EAN
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_ean','backend','import_ean','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','EAN','script');

-- Stock
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_stock','backend','import_stock','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Stock','script');

-- Price
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_price','backend','import_price','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Price','script');


-- Start Import Title
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_start_title','backend','import_start_title','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Start Import?','script');

-- Start Import Message
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_start_message','backend','import_start_message','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','This will start syncing products from the CSV file.','script');

-- Start Import Confirm Button
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_start_confirm','backend','import_start_confirm','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Yes, Start Import','script');

-- Cancel Button
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_cancel','backend','import_cancel','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Cancel','script');

-- Sync Selected Records
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_sync_selected','backend','import_sync_selected','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Sync Selected Records','script');

-- Sync Count
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_sync_count','backend','import_sync_count','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Sync Count','script');

-- View File
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_view_file','backend','import_view_file','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','View File','script');

-- CSV Upload Success
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_csv_success','backend','import_csv_success','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','CSV uploaded successfully.','script');

-- Invalid CSV File
INSERT INTO `plugin_base_fields` VALUES (NULL,'import_invalid_csv','backend','import_invalid_csv','script',NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `plugin_base_multi_lang` VALUES (NULL,@id,'pjField','::LOCALE::','title','Invalid CSV file.','script');

INSERT INTO `fields` VALUES (NULL, 'infoProductsHistoryImportManager', 'backend', 'Infobox / Product Import Manager', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Product Import Manager', 'script');


INSERT INTO `fields` VALUES (NULL, 'infoProductsHistoryImportManagerDesc', 'backend', 'Infobox / List of import csv ', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Upload CSV files, review and edit imported product data directly in the table. All changes are saved automatically, and you can sync the products to the system anytime. Complete import history is stored for tracking and re-syncing.', 'script');


DROP TABLE IF EXISTS `product_import_rows`;

CREATE TABLE IF NOT EXISTS `product_import_rows` (

    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,

    `import_id` INT(10) UNSIGNED DEFAULT NULL,
    `company_id` INT(10) UNSIGNED DEFAULT NULL,

    `w_number` VARCHAR(20) DEFAULT NULL,

    `model` VARCHAR(255) DEFAULT NULL,
    `model_name` VARCHAR(255) DEFAULT NULL,

    `sku` VARCHAR(255) DEFAULT NULL,
    `status` INT(10) UNSIGNED DEFAULT 1,

    `brand` VARCHAR(255) DEFAULT NULL,
    `category` VARCHAR(255) DEFAULT NULL,

    `article_number` VARCHAR(255) DEFAULT NULL,
    `article_name` VARCHAR(255) DEFAULT NULL,

    `ean` VARCHAR(50) DEFAULT NULL,

    `qty` INT(10) UNSIGNED DEFAULT NULL,
    `price` DECIMAL(10,2) DEFAULT NULL,

    `size` VARCHAR(50) DEFAULT NULL,
    `color` VARCHAR(50) DEFAULT NULL,

    `name_en` VARCHAR(255) DEFAULT NULL,

    `short_desc_en` TEXT,
    `full_description_en` TEXT,

    `image` TEXT,

    `row_status` ENUM('active','inactive') DEFAULT 'active',
    `sync_status` ENUM('pending','synced','error') DEFAULT 'pending',

    `created_at` DATETIME DEFAULT NULL,

    PRIMARY KEY (`id`),

    KEY `import_id` (`import_id`),
    KEY `company_id` (`company_id`),
    KEY `model` (`model`),
    KEY `article_number` (`article_number`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `fields` VALUES (NULL, 'lblCompanyWNumber', 'backend', 'Label / W-number', 'script', NULL);
SET @id := (
SELECT  LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'W-number', 'script');
ALTER TABLE `companies` ADD COLUMN `w_number` varchar(20) NULL AFTER `id`; 
-- Insert default company only if no companies exist
INSERT INTO `companies` 
(`id`, `w_number`,`name`, `email`, `phone`, `country`, `city`, `state`, `zip`, `address`, `is_deleted`, `status`)
SELECT 1, 'W-201','Default Company', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'T'
WHERE NOT EXISTS (SELECT 1 FROM `companies` WHERE id = 1);




COMMIT;