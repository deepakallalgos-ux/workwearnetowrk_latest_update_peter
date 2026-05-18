
START TRANSACTION;

DROP TABLE IF EXISTS `plugin_galleries_set`;
CREATE TABLE IF NOT EXISTS `plugin_galleries_set` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `small_width` smallint(5) unsigned DEFAULT NULL,
  `small_height` smallint(5) unsigned DEFAULT NULL,
  `medium_width` smallint(5) unsigned DEFAULT NULL,
  `medium_height` smallint(5) unsigned DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `status` enum('T','F') NOT NULL DEFAULT 'T',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `plugin_galleries_set` (`id`, `small_width`, `small_height`, `medium_width`, `medium_height`, `modified`, `created`, `status`) VALUES
(NULL, '150', '100', '665', '450', NULL, NOW(), 'T');

SET @set_id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @set_id, 'pjGallerySet', '1', 'name', 'Default gallery', 'plugin');

COMMIT;