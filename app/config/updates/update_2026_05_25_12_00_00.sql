START TRANSACTION;

INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'infoModelImageTabGrid', 'backend', 'Help / Model image tab grid', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `fields` WHERE `key` = 'infoModelImageTabGrid');
SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'infoModelImageTabGrid' LIMIT 1);
INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @id, 'pjField', '::LOCALE::', 'title', 'Click an image to set the listing photo used in shop grids. Upload more below; use × to delete an image.', 'script'
FROM DUAL
WHERE @id IS NOT NULL AND NOT EXISTS (
	SELECT 1 FROM `multi_lang` m WHERE m.`foreign_id` = @id AND m.`model` = 'pjField' AND m.`field` = 'title' AND m.`locale` = '::LOCALE::'
);

INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'lblProductModelImageGridEmpty', 'backend', 'Empty / Model image grid', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `fields` WHERE `key` = 'lblProductModelImageGridEmpty');
SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'lblProductModelImageGridEmpty' LIMIT 1);
INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @id, 'pjField', '::LOCALE::', 'title', 'No model images yet. Use Upload model image to add the first one.', 'script'
FROM DUAL
WHERE @id IS NOT NULL AND NOT EXISTS (
	SELECT 1 FROM `multi_lang` m WHERE m.`foreign_id` = @id AND m.`model` = 'pjField' AND m.`field` = 'title' AND m.`locale` = '::LOCALE::'
);

INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'lblProductModelImageActive', 'backend', 'Label / Active model image', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `fields` WHERE `key` = 'lblProductModelImageActive');
SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'lblProductModelImageActive' LIMIT 1);
INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @id, 'pjField', '::LOCALE::', 'title', 'Active', 'script'
FROM DUAL
WHERE @id IS NOT NULL AND NOT EXISTS (
	SELECT 1 FROM `multi_lang` m WHERE m.`foreign_id` = @id AND m.`model` = 'pjField' AND m.`field` = 'title' AND m.`locale` = '::LOCALE::'
);

INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'btnDeleteModelImage', 'backend', 'Button / Delete model image', 'script', NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `fields` WHERE `key` = 'btnDeleteModelImage');
SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'btnDeleteModelImage' LIMIT 1);
INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete image', 'script'
FROM DUAL
WHERE @id IS NOT NULL AND NOT EXISTS (
	SELECT 1 FROM `multi_lang` m WHERE m.`foreign_id` = @id AND m.`model` = 'pjField' AND m.`field` = 'title' AND m.`locale` = '::LOCALE::'
);

COMMIT;
