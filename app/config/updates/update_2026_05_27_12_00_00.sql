START TRANSACTION;

INSERT IGNORE INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
VALUES (NULL, 'front_login_required', 'frontend', 'Front / Login required for quote', 'script', NOW());
SET @id := (SELECT `id` FROM `fields` WHERE `key`='front_login_required' LIMIT 1);
INSERT IGNORE INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @id, 'pjField', l.id, 'title', 'Please log in or register before requesting a quote.', 'script'
FROM `plugin_base_locale` l;

COMMIT;
