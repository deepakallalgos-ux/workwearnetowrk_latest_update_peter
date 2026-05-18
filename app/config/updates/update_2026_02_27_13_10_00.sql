START TRANSACTION;

DROP TABLE IF EXISTS `brands`;
CREATE TABLE IF NOT EXISTS `brands` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned DEFAULT NULL,
   `company_id` int(10) unsigned DEFAULT NULL,
  `lft` int(10) unsigned DEFAULT NULL,
  `rgt` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `brands`(`id`,`parent_id`,`lft`,`rgt`,`name`) values (1,0,1,2,'Products');



DROP TABLE IF EXISTS `products_brands`;
CREATE TABLE IF NOT EXISTS `products_brands` (
  `product_id` int(10) unsigned NOT NULL DEFAULT '0',
   `company_id` int(10) unsigned DEFAULT NULL,
  `brand_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`product_id`,`brand_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



INSERT INTO `plugin_base_fields` VALUES (NULL, 'menuBrands', 'backend', 'Menu Brands', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Brands', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'menu_brands', 'backend', 'menu_brands', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Brands', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'brand_list', 'backend', 'brand_list', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Brands', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'brand_brands', 'backend', 'brand_brands', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Brands', 'script');
 
INSERT INTO `plugin_base_fields` VALUES (NULL, 'brand_empty', 'backend', 'brand_empty', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No brands found', 'script');


INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblAllBrands', 'backend', 'Label / All brands', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All brands', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'dashboard_brands_in_use', 'backend', 'Dashboard / brands in use', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'brands in use', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblAddBrandText', 'backend', 'Label / No brands found. Add brand here', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No brands found. Add brand {STAG}here{ETAG}.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoBrandsDesc', 'backend', 'infoBrandsDesc', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Your menu is organized in brands. Below is a list of all brands added to the system. Use the tab above to add new brand or edit one by clicking on the pencil icon of each row.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoBrandsTitle', 'backend', 'infoBrandsTitle', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Brand List', 'script');


INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, NULL, 'pjAdminBrands');
SET @level_1_id := (SELECT LAST_INSERT_ID());

  INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_1_id, 'pjAdminBrands_pjActionIndex');
  SET @level_2_id := (SELECT LAST_INSERT_ID());
  
    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminBrands_pjActionCreateForm');
    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminBrands_pjActionUpdateForm');
    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminBrands_pjActionDeleteBrand');
    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminBrands_pjActionDeleteBrandBulk');
    
    

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminBrands', 'backend', 'pjAdminBrands', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Brands Menu', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminBrands_pjActionCreateForm', 'backend', 'pjAdminBrands_pjActionCreateForm', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Add brands', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminBrands_pjActionDeleteBrand', 'backend', 'pjAdminBrands_pjActionDeleteBrand', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Delete single brand', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminBrands_pjActionDeleteBrandBulk', 'backend', 'pjAdminBrands_pjActionDeleteBrandBulk', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Delete multiple brands', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminBrands_pjActionIndex', 'backend', 'pjAdminBrands_pjActionIndex', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Brands List', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminBrands_pjActionUpdateForm', 'backend', 'pjAdminBrands_pjActionUpdateForm', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Edit brands', 'script');
    



INSERT INTO `plugin_base_fields` VALUES (NULL, 'product_f_brand', 'backend', 'product_f_brand', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '-- Brand --', 'script');


INSERT INTO `plugin_base_fields` VALUES (NULL, 'product_brand', 'backend', 'product_brand', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Brand', 'script');


INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_BG01', 'arrays', 'error_titles_ARRAY_BG01', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Brand has been added', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_BG02', 'arrays', 'error_titles_ARRAY_BG02', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Brand has not been added', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_BG05', 'arrays', 'error_titles_ARRAY_BG05', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Brand has been updated', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_BG08', 'arrays', 'error_titles_ARRAY_BG08', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Brand doesn''t exists', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_BG01', 'arrays', 'error_bodies_ARRAY_BG01', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can now add another brand', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_BG02', 'arrays', 'error_bodies_ARRAY_BG02', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Brand has not been added', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_BG05', 'arrays', 'error_bodies_ARRAY_BG05', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Brand has been updated', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_BG08', 'arrays', 'error_bodies_ARRAY_BG08', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Brand doesn''t exists', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AB11', 'arrays', 'error_titles_ARRAY_AB11', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Brand list', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblInstallBrand', 'backend', 'Label / Brand', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Brand', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnAddBrand', 'backend', 'Button / Add brand', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add brand', 'script');
  
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoAddBrandDesc', 'backend', 'infoAddBrandDesc', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Use the form below to add new brand to the system.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoAddBrandTitle', 'backend', 'infoAddBrandTitle', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Add new brand', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoUpdateBrandDesc', 'backend', 'infoUpdateBrandDesc', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Edit brand details and click on the ''Save'' button to update it.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoUpdateBrandTitle', 'backend', 'infoUpdateBrandTitle', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Update Brand', 'script');
 
 
INSERT INTO `plugin_base_fields` VALUES (NULL, 'brand_name', 'backend', 'brand_name', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Name', 'script');


INSERT INTO `plugin_base_fields` VALUES (NULL, 'brand_products', 'backend', 'brand_products', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Products', 'script');

ALTER TABLE `products`  ADD COLUMN `brand_id` INT(10) UNSIGNED NULL AFTER `id`;


INSERT INTO `plugin_base_fields` VALUES (NULL, 'brand_parent', 'backend', 'brand_parent', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Parent brand', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'brand_no_parent', 'backend', 'brand_no_parent', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No parent', 'script');





INSERT INTO `plugin_base_fields` VALUES (NULL, 'brand_update', 'backend', 'brand_update', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update brand', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'brand_create', 'backend', 'brand_create', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add brand', 'script');
   
INSERT INTO `plugin_base_fields` VALUES (NULL, 'brand_choose', 'backend', 'brand_choose', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '-- Choose --', 'script');
  
INSERT INTO `plugin_base_fields` VALUES (NULL, 'brand_del_title', 'backend', 'brand_del_title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete this brand?', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'brand_del_body', 'backend', 'brand_del_body', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All brand'' details will be deleted and will not be possible to restore them.', 'script');

ALTER TABLE `carts` ADD COLUMN `is_cart` enum('1','0') NOT NULL DEFAULT '1' AFTER `id`;

COMMIT;