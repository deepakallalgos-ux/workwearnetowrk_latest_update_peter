START TRANSACTION;

INSERT IGNORE INTO `plugin_base_options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`)
VALUES (1, 'o_admin_sidebar_logo', 2, '', '', 'string', 0, 1, NULL);
