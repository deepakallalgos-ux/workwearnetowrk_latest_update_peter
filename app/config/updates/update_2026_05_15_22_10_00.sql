START TRANSACTION;

-- Setup wizard step labels (backend). Safe to re-run after database.sql.

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_step_welcome', 'backend', 'Setup step: Welcome', 'script', '2026-05-15 22:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_step_welcome');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_step_welcome' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Welcome', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_step_choose', 'backend', 'Setup step: Choose', 'script', '2026-05-15 22:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_step_choose');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_step_choose' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Choose', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_step_install', 'backend', 'Setup step: Set up', 'script', '2026-05-15 22:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_step_install');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_step_install' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Set up', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_step_done', 'backend', 'Setup step: Done', 'script', '2026-05-15 22:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_step_done');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_step_done' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Done', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_btn_continue', 'backend', 'Setup: Continue button', 'script', '2026-05-15 22:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_btn_continue');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_btn_continue' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Continue', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_btn_back', 'backend', 'Setup: Back button', 'script', '2026-05-15 22:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_btn_back');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_btn_back' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Back', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_step1_feat_1', 'backend', 'Setup step 1 feature 1', 'script', '2026-05-15 22:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_step1_feat_1');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_step1_feat_1' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Quote-only platform ready for your workwear catalogue', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_step1_feat_2', 'backend', 'Setup step 1 feature 2', 'script', '2026-05-15 22:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_step1_feat_2');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_step1_feat_2' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Load sample data to explore, or start with a clean slate', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_step1_feat_3', 'backend', 'Setup step 1 feature 3', 'script', '2026-05-15 22:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_step1_feat_3');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_step1_feat_3' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'You can change or remove demo data later from the setup wizard', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_choose_hint', 'backend', 'Setup: choose step hint', 'script', '2026-05-15 22:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_choose_hint');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_choose_hint' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Select an option below, then click Continue.', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

COMMIT;
