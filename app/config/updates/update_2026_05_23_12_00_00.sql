START TRANSACTION;

/*
 * Corrective: if update_2026_05_21 ran with `gallery` (wrong), re-tag model images on plugin_gallery.
 * Safe to run multiple times.
 */
UPDATE `plugin_gallery` AS `g`
SET `g`.`model` = 'pjProductModelImage'
WHERE `g`.`model` = 'pjProduct'
AND `g`.`id` IN (
	SELECT `model_image_id` FROM `products` WHERE `model_image_id` IS NOT NULL AND `model_image_id` > 0
);

COMMIT;
