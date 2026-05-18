
START TRANSACTION;

SET @id := (SELECT `id` FROM `fields` WHERE `key`='payment_plugin_messages_ARRAY_paypal_express');
UPDATE `multi_lang` SET `content`='Your order is saved. Please choose your payment method' WHERE `foreign_id`=@id AND `model`='pjField' AND `field`='title';

COMMIT;