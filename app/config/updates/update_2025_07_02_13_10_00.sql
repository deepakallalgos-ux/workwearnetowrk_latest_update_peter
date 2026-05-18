START TRANSACTION;


DROP TABLE IF EXISTS `companies`;
CREATE TABLE IF NOT EXISTS `companies` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) DEFAULT NULL,
    `email` varchar(255) DEFAULT NULL,
    `phone` varchar(255) DEFAULT NULL,
    `country` int(10) unsigned DEFAULT NULL,
    `city` varchar(255) DEFAULT NULL,
    `state` varchar(255) DEFAULT NULL,
    `zip` varchar(255) DEFAULT NULL,
    `address` varchar(255) DEFAULT NULL,
    `is_deleted` int(11) NOT NULL DEFAULT 0,
	`status` enum('T','F') NOT NULL DEFAULT 'T',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `fields` VALUES (NULL, 'menuCompanies', 'backend', 'Menu / Companies', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Companies', 'script');



INSERT INTO `fields` VALUES (NULL, 'lblAddCompany', 'backend', 'Add company', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Add company', 'script');



INSERT INTO `fields` VALUES (NULL, 'lblCompany', 'backend', 'Label Company', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Company', 'script');



INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AEC01', 'arrays', 'error_titles_ARRAY_AEC01', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Company updated!', 'script');



INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AEC03', 'arrays', 'error_titles_ARRAY_AEC03', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Company added!', 'script');



INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AEC04', 'arrays', 'error_titles_ARRAY_AEC04', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Company failed to add.', 'script');



INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AEC08', 'arrays', 'error_titles_ARRAY_AEC08', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Company not found.', 'script');



INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AEC01', 'arrays', 'error_bodies_ARRAY_AEC01', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'All the changes made to the company have been saved.', 'script');



INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AEC03', 'arrays', 'error_bodies_ARRAY_AEC03', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'All the changes made to the company have been saved.', 'script');



INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AEC04', 'arrays', 'error_bodies_ARRAY_AEC04', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'We are sorry, but the company has not been added.', 'script');



INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AEC08', 'arrays', 'error_bodies_ARRAY_AEC08', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Company your looking for is missing.', 'script');



INSERT INTO `fields` VALUES (NULL, 'lblUpdateCompany', 'backend', 'Label Update company', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Update company', 'script');


INSERT INTO `fields` VALUES (NULL, 'lblSameCompany', 'backend', 'Label / Same company', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Company name was alaredy used.', 'script');


INSERT INTO `fields` VALUES (NULL, 'infoCompaniesTitle', 'backend', 'Infobox / Company list title', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Company list', 'script');



INSERT INTO `fields` VALUES (NULL, 'infoCompaniesDesc', 'backend', 'Infobox / Company list desc', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Below is the list of companies. Let click on the Add Company tab to define new company. You can also Edit or Delete a specific company by clicking on the corresponding row.', 'script');



INSERT INTO `fields` VALUES (NULL, 'infoAddCompanyTitle', 'backend', 'Infobox / Add company title', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Add new company', 'script');



INSERT INTO `fields` VALUES (NULL, 'infoAddCompanyDesc', 'backend', 'Infobox / Add company desc', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Please fill out the form below and click Save button to add new company. Or click on the Cancel button the go back to the company list.', 'script');



INSERT INTO `fields` VALUES (NULL, 'infoEditCompanyTitle', 'backend', 'Infobox / Edit company title', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Edit company', 'script');



INSERT INTO `fields` VALUES (NULL, 'infoEditCompanyDesc', 'backend', 'Infobox / Edit company desc', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'You can make any change you want on the form below and click Save button to update company name', 'script');



INSERT INTO `fields` VALUES (NULL, 'btnAddCompany', 'backend', 'Button / Add company', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Add company', 'script');


INSERT INTO `fields` VALUES (NULL, 'menuCurrentCompany', 'backend', 'Menu / Company', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Current company', 'script');


INSERT INTO `fields` VALUES (NULL, 'menuCompany', 'backend', 'Menu / Company', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Set company', 'script');



INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, NULL, 'pjAdminCompanies');

SET @level_1_id := (SELECT LAST_INSERT_ID());



  INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_1_id, 'pjAdminCompanies_pjActionIndex');

  SET @level_2_id := (SELECT LAST_INSERT_ID());

  

    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminCompanies_pjActionCreateForm');

    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminCompanies_pjActionUpdateForm');

    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminCompanies_pjActionDeleteCompany');

    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminCompanies_pjActionDeleteCompanyBulk');

	



INSERT INTO `fields` VALUES (NULL, 'pjAdminCompanies', 'backend', 'pjAdminCompanies', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Companies Menu', 'script');



INSERT INTO `fields` VALUES (NULL, 'pjAdminCompanies_pjActionIndex', 'backend', 'pjAdminCompanies_pjActionIndex', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Companies List', 'script');



INSERT INTO `fields` VALUES (NULL, 'pjAdminCompanies_pjActionCreateForm', 'backend', 'pjAdminCompanies_pjActionCreateForm', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Add companies', 'script');



INSERT INTO `fields` VALUES (NULL, 'pjAdminCompanies_pjActionUpdateForm', 'backend', 'pjAdminCompanies_pjActionUpdateForm', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Edit companies', 'script');



INSERT INTO `fields` VALUES (NULL, 'pjAdminCompanies_pjActionDeleteCompany', 'backend', 'pjAdminCompanies_pjActionDeleteCompany', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Delete single product', 'script');



INSERT INTO `fields` VALUES (NULL, 'pjAdminCompanies_pjActionDeleteCompanyBulk', 'backend', 'pjAdminCompanies_pjActionDeleteCompanyBulk', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Delete multiple companies', 'script');






INSERT INTO `fields` VALUES (NULL, 'lblCompanyName', 'backend', 'Label / Name', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Name', 'script');




INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_ACN001', 'arrays', 'error_titles_ARRAY_ACN001', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Please check!', 'script');



INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_ACN001', 'arrays', 'error_bodies_ARRAY_ACN001', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Please check if the company is selected from the left menu or if you have created the company.', 'script');


COMMIT;