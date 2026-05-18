START TRANSACTION;

-- Fix setup wizard welcome copy on existing installs (PHPJabbers branding).
-- No JOINs: installer only prefixes tables after INSERT/UPDATE/FROM keywords.

UPDATE `plugin_base_multi_lang`
SET `content` = 'Welcome to PHPJabbers'
WHERE `model` = 'pjField'
  AND `field` = 'title'
  AND `content` = 'Welcome to your quote platform'
  AND `foreign_id` = (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_welcome_title' LIMIT 1);

UPDATE `plugin_base_multi_lang`
SET `content` = 'Let''s finish setting up your application. You can load sample data to explore the system, or start with an empty catalogue. Demo data can be removed later.'
WHERE `model` = 'pjField'
  AND `field` = 'title'
  AND `content` LIKE 'Choose how to get started%'
  AND `foreign_id` = (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_welcome_subtitle' LIMIT 1);

COMMIT;
