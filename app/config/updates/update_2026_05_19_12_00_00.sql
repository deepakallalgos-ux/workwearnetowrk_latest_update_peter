START TRANSACTION;

-- Skip post-install demo data wizard (client request). Safe to re-run.
-- No JOINs: installer only prefixes tables after INSERT/UPDATE/FROM keywords.

UPDATE `options`
SET `value` = '1'
WHERE `foreign_id` = 1 AND `key` = 'o_setup_wizard_completed';

COMMIT;
