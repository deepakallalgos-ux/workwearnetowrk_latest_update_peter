START TRANSACTION;

-- Admin sidebar logo (Visual branding) — backend labels. Safe to re-run.

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'lbl_admin_sidebar_logo', 'backend', 'Admin sidebar logo label', 'script', '2026-05-18 23:59:01' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'lbl_admin_sidebar_logo');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'lbl_admin_sidebar_logo' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Admin sidebar logo', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'lbl_admin_sidebar_logo_alt', 'backend', 'Admin sidebar logo alt text', 'script', '2026-05-18 23:59:01' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'lbl_admin_sidebar_logo_alt');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'lbl_admin_sidebar_logo_alt' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Admin sidebar logo', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'btn_admin_sidebar_logo_upload', 'backend', 'Upload sidebar logo button', 'script', '2026-05-18 23:59:01' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'btn_admin_sidebar_logo_upload');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'btn_admin_sidebar_logo_upload' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Upload logo', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'btn_admin_sidebar_logo_remove', 'backend', 'Remove sidebar logo button', 'script', '2026-05-18 23:59:01' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'btn_admin_sidebar_logo_remove');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'btn_admin_sidebar_logo_remove' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Remove logo', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'info_admin_sidebar_logo_hint', 'backend', 'Sidebar logo upload hint', 'script', '2026-05-18 23:59:01' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'info_admin_sidebar_logo_hint');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'info_admin_sidebar_logo_hint' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Recommended: wide logo on a dark background (PNG or SVG). Max display width 200px in the side menu.', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'lbl_admin_sidebar_logo_remove_title', 'backend', 'Remove sidebar logo dialog title', 'script', '2026-05-18 23:59:01' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'lbl_admin_sidebar_logo_remove_title');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'lbl_admin_sidebar_logo_remove_title' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Remove logo?', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'lbl_admin_sidebar_logo_remove_text', 'backend', 'Remove sidebar logo dialog text', 'script', '2026-05-18 23:59:01' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'lbl_admin_sidebar_logo_remove_text');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'lbl_admin_sidebar_logo_remove_text' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'The custom sidebar logo will be removed from the admin menu.', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'lbl_admin_sidebar_logo_remove_confirm', 'backend', 'Remove sidebar logo confirm button', 'script', '2026-05-18 23:59:01' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'lbl_admin_sidebar_logo_remove_confirm');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'lbl_admin_sidebar_logo_remove_confirm' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Remove', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'lbl_admin_sidebar_logo_remove_fallback', 'backend', 'Remove sidebar logo browser confirm', 'script', '2026-05-18 23:59:01' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'lbl_admin_sidebar_logo_remove_fallback');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'lbl_admin_sidebar_logo_remove_fallback' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Remove the sidebar logo?', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'plugin_base_error_titles_ARRAY_logo_upload', 'arrays', 'Error title: logo upload failed', 'script', '2026-05-18 23:59:01' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'plugin_base_error_titles_ARRAY_logo_upload');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'plugin_base_error_titles_ARRAY_logo_upload' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjBaseField', l.id, 'title', 'Upload failed', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjBaseField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'plugin_base_error_bodies_ARRAY_logo_upload', 'arrays', 'Error body: logo upload failed', 'script', '2026-05-18 23:59:01' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'plugin_base_error_bodies_ARRAY_logo_upload');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'plugin_base_error_bodies_ARRAY_logo_upload' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjBaseField', l.id, 'title', 'Please upload a valid image file (PNG, JPG, GIF, WebP, or SVG).', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjBaseField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

-- Login welcome line: script_name matches production (Shopping Cart v5.0). Safe to re-run.
-- No JOINs: installer only prefixes tables after INSERT/UPDATE/FROM keywords.

UPDATE `plugin_base_fields`
SET `label` = 'Shopping Cart v5.0'
WHERE `key` = 'script_name';

UPDATE `plugin_base_multi_lang`
SET `content` = 'Shopping Cart v5.0'
WHERE `model` = 'pjField'
  AND `field` = 'title'
  AND `foreign_id` = (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'script_name' LIMIT 1);

COMMIT;
