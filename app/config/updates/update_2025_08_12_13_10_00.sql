START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'menuSuperAdmin', 'backend', 'Menu / Toggle to view products from all companies', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Toggle to view products from all companies', 'script');

COMMIT;