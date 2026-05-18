

START TRANSACTION;

INSERT INTO `plugin_base_options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES

(1, 'o_sender_flex_email_user_id', 3, NULL, NULL, 'string', 15, 1, NULL),

(1, 'o_sender_flex_email_api_key', 3, NULL, NULL, 'string', 16, 1, NULL),

(1, 'o_flexmail_sender_name', 3, NULL, NULL, 'string', 15, 1, NULL),

(1, 'o_flexmail_sender_email', 3, NULL, NULL, 'string', 15, 1, NULL);







INSERT INTO `plugin_base_fields` VALUES (NULL,'plugin_base_opt_o_sender_flex_email_user_id', 'backend', 'Plugin Base / Options /Flexmail User Id', 'plugin' ,'2024-10-01 05:14:10');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Flexmail User Id', 'script');



INSERT INTO `plugin_base_fields` VALUES (NULL,'plugin_base_opt_o_sender_flex_email_api_key', 'backend', 'Plugin Base / Options /Flexmail API Key', 'plugin' ,'2024-10-01 05:14:10');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Flexmail API Key', 'script');


INSERT INTO `plugin_base_fields` VALUES (NULL,'plugin_base_opt_o_flexmail_sender_name', 'backend', 'Plugin Base / Options / Name ("From" header)', 'plugin' ,'2024-10-01 05:14:10');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Name ("From" header)', 'script');


INSERT INTO `plugin_base_fields` VALUES (NULL,'plugin_base_opt_o_flexmail_sender_email', 'backend', 'Plugin Base / Options / Email address ("From" header)', 'plugin' ,'2024-10-01 05:14:10');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Email address ("From" header)', 'script');





UPDATE `plugin_base_options` SET `value`='mail|smtp|flexmail::mail' , `label`='PHP mail()|SMTP|Flexmail' WHERE `foreign_id`='::LOCALE::'  AND `key`='o_send_email';







COMMIT;



