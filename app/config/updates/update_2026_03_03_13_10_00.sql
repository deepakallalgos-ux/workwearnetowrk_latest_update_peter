START TRANSACTION;

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_titles_ARRAY_admin_email_quote', 'arrays', 'Notifications / Admin email quote (title)', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'New Order Received email sent to Admin', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_subtitles_ARRAY_admin_email_quote', 'arrays', 'Notifications / Admin email quote (sub-title)', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'This email is sent to the super admin when a new order is made.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_titles_ARRAY_client_email_quote', 'arrays', 'Notifications / Client email quote (title)', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order quote email sent to Client', 'script');


INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_subtitles_ARRAY_client_email_quote', 'arrays', 'Notifications / Client email quote (sub-title)', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'This email is sent to the client when a new order is made.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_ARRAY_client_email_quote', 'arrays', 'Notifications / Client email quote', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send quote email', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_ARRAY_admin_email_quote', 'arrays', 'Notifications / Admin email quote', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send quote email', 'script');
 


INSERT IGNORE INTO `notifications`
(`company_id`, `recipient`, `transport`, `variant`, `is_active`)
SELECT 
    c.id,
    'client',
    'email',
    'quote',
    1
FROM shopping_cart_companies c;

INSERT IGNORE INTO `notifications`
(`company_id`, `recipient`, `transport`, `variant`, `is_active`)
SELECT 
    c.id,
    'admin',
    'email',
    'quote',
    1
FROM shopping_cart_companies c;

INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_client_quote', 'backend', 'quote / Client quote title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Client - order quote email', 'script');



INSERT INTO `plugin_base_fields` VALUES (NULL, 'quote_admin_quote', 'backend', 'quote / Admin quote title', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Admin - order quote email', 'script');


INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`) VALUES
(NULL, 1, 'pjNotification', 1, 'quote_subject_client', 'Order quote', 'data'),
(NULL, 7, 'pjNotification', 1, 'quote_subject_admin', 'New quote order received', 'data');
COMMIT;