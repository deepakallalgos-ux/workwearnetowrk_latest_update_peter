START TRANSACTION;

-- Setup wizard UI labels (backend). Safe to re-run after database.sql.

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_welcome_title', 'backend', 'Setup: welcome title', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_welcome_title');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_welcome_title' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Welcome to PHPJabbers', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_welcome_subtitle', 'backend', 'Setup: welcome subtitle', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_welcome_subtitle');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_welcome_subtitle' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Let''s finish setting up your application. You can load sample data to explore the system, or start with an empty catalogue. Demo data can be removed later.', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_badge_recommended', 'backend', 'Setup: recommended badge', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_badge_recommended');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_badge_recommended' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Recommended', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_card_demo_title', 'backend', 'Setup: demo card title', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_card_demo_title');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_card_demo_title' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Start with demo data', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_card_demo_desc', 'backend', 'Setup: demo card description', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_card_demo_desc');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_card_demo_desc' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Explore products, clients, and sample quotes with realistic workwear examples.', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_demo_feat_categories', 'backend', 'Setup: demo feature categories', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_demo_feat_categories');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_demo_feat_categories' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', '3 categories (Workwear, Uniforms, Accessories)', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_demo_feat_products', 'backend', 'Setup: demo feature products', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_demo_feat_products');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_demo_feat_products' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', '5 sample products with placeholder images', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_demo_feat_clients', 'backend', 'Setup: demo feature clients', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_demo_feat_clients');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_demo_feat_clients' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', '3 demo clients', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_demo_feat_quotes', 'backend', 'Setup: demo feature quotes', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_demo_feat_quotes');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_demo_feat_quotes' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', '3 sample quotes (new, pending, completed)', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_btn_start_demo', 'backend', 'Setup: start with demo button', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_btn_start_demo');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_btn_start_demo' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Start with demo', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_card_empty_title', 'backend', 'Setup: empty card title', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_card_empty_title');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_card_empty_title' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Start empty', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_card_empty_desc', 'backend', 'Setup: empty card description', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_card_empty_desc');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_card_empty_desc' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Begin with a clean catalogue and add your own products and clients.', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_empty_feat_no_samples', 'backend', 'Setup: empty feature no samples', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_empty_feat_no_samples');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_empty_feat_no_samples' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'No sample products or quotes', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_empty_feat_control', 'backend', 'Setup: empty feature control', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_empty_feat_control');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_empty_feat_control' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Full control from day one', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_empty_feat_migrate', 'backend', 'Setup: empty feature migrate', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_empty_feat_migrate');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_empty_feat_migrate' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Ideal if you are migrating existing data', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_btn_start_empty', 'backend', 'Setup: start empty button', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_btn_start_empty');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_btn_start_empty' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Start empty', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_loading_demo', 'backend', 'Setup: loading demo', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_loading_demo');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_loading_demo' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Loading demo data...', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_loading_sub', 'backend', 'Setup: loading subtitle', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_loading_sub');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_loading_sub' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'This takes a few seconds', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_loading_demo_detail', 'backend', 'Setup: loading demo detail', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_loading_demo_detail');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_loading_demo_detail' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Creating categories, products, clients, and quotes', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_success_title', 'backend', 'Setup: success title', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_success_title');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_success_title' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Your platform is ready', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_success_redirect', 'backend', 'Setup: success redirect', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_success_redirect');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_success_redirect' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Redirecting to the dashboard...', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_demo_active_title', 'backend', 'Setup: demo active title', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_demo_active_title');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_demo_active_title' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Demo data active', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_demo_active_desc', 'backend', 'Setup: demo active description', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_demo_active_desc');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_demo_active_desc' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Sample products (SKU: DEMO-*) and demo categories are present. Remove them when you no longer need them.', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_btn_remove_demo', 'backend', 'Setup: remove demo button', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_btn_remove_demo');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_btn_remove_demo' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Remove demo data', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_load_demo_title', 'backend', 'Setup: load demo title', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_load_demo_title');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_load_demo_title' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Load demo data', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_load_demo_desc', 'backend', 'Setup: load demo description', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_load_demo_desc');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_load_demo_desc' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Load sample workwear products, clients, and quotes to explore the platform.', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_load_demo_feat_summary', 'backend', 'Setup: load demo feature summary', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_load_demo_feat_summary');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_load_demo_feat_summary' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', '3 categories, 5 products, 3 clients', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_load_demo_feat_quotes', 'backend', 'Setup: load demo feature quotes', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_load_demo_feat_quotes');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_load_demo_feat_quotes' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', '3 sample quotes', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_btn_load_demo', 'backend', 'Setup: load demo button', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_btn_load_demo');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_btn_load_demo' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Load demo data', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_page_title', 'backend', 'Setup: page title suffix', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_page_title');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_page_title' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Setup', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_swal_loaded_title', 'backend', 'Setup: swal loaded title', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_swal_loaded_title');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_swal_loaded_title' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Demo data loaded', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_swal_removed_title', 'backend', 'Setup: swal removed title', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_swal_removed_title');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_swal_removed_title' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Demo data removed', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_summary_loaded', 'backend', 'Setup: summary loaded', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_summary_loaded');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_summary_loaded' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Demo data loaded.', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_summary_removed', 'backend', 'Setup: summary removed', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_summary_removed');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_summary_removed' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Demo data removed.', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_unit_products', 'backend', 'Setup: unit products', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_unit_products');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_unit_products' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'products', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_unit_categories', 'backend', 'Setup: unit categories', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_unit_categories');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_unit_categories' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'categories', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_unit_images', 'backend', 'Setup: unit images', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_unit_images');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_unit_images' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'images', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_unit_clients', 'backend', 'Setup: unit clients', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_unit_clients');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_unit_clients' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'clients', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_unit_quotes', 'backend', 'Setup: unit quotes', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_unit_quotes');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_unit_quotes' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'quotes', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_error_unknown', 'backend', 'Setup: unknown error', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_error_unknown');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_error_unknown' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Unknown error', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_error_generic', 'backend', 'Setup: generic error', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_error_generic');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_error_generic' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'An error occurred. Please try again.', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_error_connection', 'backend', 'Setup: connection error', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_error_connection');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_error_connection' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Connection error. Please try again.', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_error_connection_short', 'backend', 'Setup: connection error short', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_error_connection_short');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_error_connection_short' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Connection error.', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_error_short', 'backend', 'Setup: error short', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_error_short');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_error_short' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'An error occurred.', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_error_prefix', 'backend', 'Setup: error prefix', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_error_prefix');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_error_prefix' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Error:', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_skip_loading', 'backend', 'Setup: skip loading', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_skip_loading');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_skip_loading' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Please wait...', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_skip_loading_sub', 'backend', 'Setup: skip loading subtitle', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_skip_loading_sub');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_skip_loading_sub' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Preparing your empty catalogue', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_remove_confirm_title', 'backend', 'Setup: remove confirm title', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_remove_confirm_title');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_remove_confirm_title' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Remove demo data?', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_remove_confirm_text', 'backend', 'Setup: remove confirm text', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_remove_confirm_text');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_remove_confirm_text' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'All sample products, categories, clients, and quotes will be permanently removed.', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_btn_remove_confirm', 'backend', 'Setup: remove confirm button', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_btn_remove_confirm');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_btn_remove_confirm' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Yes, remove', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_remove_confirm_fallback', 'backend', 'Setup: remove confirm fallback', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_remove_confirm_fallback');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_remove_confirm_fallback' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Remove all demo data? This cannot be undone.', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_removing_demo', 'backend', 'Setup: removing demo', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_removing_demo');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_removing_demo' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Removing demo data...', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_btn_ok', 'backend', 'Setup: OK button', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_btn_ok');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_btn_ok' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'OK', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_unauthorized', 'backend', 'Setup: unauthorized', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_unauthorized');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_unauthorized' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Unauthorized.', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_api_loaded', 'backend', 'Setup: API loaded message', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_api_loaded');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_api_loaded' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Demo data loaded successfully.', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_api_removed', 'backend', 'Setup: API removed message', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_api_removed');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_api_removed' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Demo data removed successfully.', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_api_completed', 'backend', 'Setup: API wizard completed', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_api_completed');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_api_completed' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Setup wizard completed.', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_error_loading_demo', 'backend', 'Setup: error loading demo', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_error_loading_demo');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_error_loading_demo' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Error loading demo data:', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

INSERT INTO `plugin_base_fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'setup_error_removing_demo', 'backend', 'Setup: error removing demo', 'script', '2026-05-15 21:10:00' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `plugin_base_fields` WHERE `key` = 'setup_error_removing_demo');

SET @field_id := (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_error_removing_demo' LIMIT 1);
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @field_id, 'pjField', l.id, 'title', 'Error removing demo data:', 'script'
FROM `plugin_base_locale` l
WHERE @field_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `plugin_base_multi_lang` m
      WHERE m.`foreign_id` = @field_id AND m.`model` = 'pjField' AND m.`locale` = l.id AND m.`field` = 'title'
  );

COMMIT;
