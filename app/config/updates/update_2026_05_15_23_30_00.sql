START TRANSACTION;

-- Setup wizard copy: Back To Basics CSV demo catalogue (no JOINs for installer prefix rules).

UPDATE `plugin_base_multi_lang`
SET `content` = '2 sample products (apron & beanie) with colour variants'
WHERE `model` = 'pjField'
  AND `field` = 'title'
  AND `foreign_id` = (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_demo_feat_products' LIMIT 1);

UPDATE `plugin_base_multi_lang`
SET `content` = '2 products, 3 clients'
WHERE `model` = 'pjField'
  AND `field` = 'title'
  AND `foreign_id` = (SELECT `id` FROM `plugin_base_fields` WHERE `key` = 'setup_load_demo_feat_summary' LIMIT 1);

COMMIT;
