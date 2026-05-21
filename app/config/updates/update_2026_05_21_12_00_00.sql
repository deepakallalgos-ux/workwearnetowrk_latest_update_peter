START TRANSACTION;

/* Separate model/listing images from product photo gallery (previously both used model=pjProduct). */
UPDATE `plugin_gallery` AS `g`
SET `g`.`model` = 'pjProductModelImage'
WHERE `g`.`model` = 'pjProduct'
AND `g`.`id` IN (
	SELECT `model_image_id` FROM `products` WHERE `model_image_id` IS NOT NULL AND `model_image_id` > 0
);

COMMIT;
