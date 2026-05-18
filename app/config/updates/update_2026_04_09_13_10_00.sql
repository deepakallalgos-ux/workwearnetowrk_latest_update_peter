START TRANSACTION;

INSERT INTO `plugin_base_fields` VALUES (NULL, 'menuPhase1Import', 'backend', 'Menu Phase 1 Import', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Phase 1 Import', 'script');



INSERT INTO `plugin_base_fields` VALUES (NULL, 'menuPhase2ImportReview', 'backend', 'Menu Phase 2 Flatfile/review', 'script', '2022-06-20 06:21:35');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Phase 2 Flatfile/review', 'script');



COMMIT;