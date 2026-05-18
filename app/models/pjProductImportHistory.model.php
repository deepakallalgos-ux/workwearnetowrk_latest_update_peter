<?php
if (!defined("ROOT_PATH")) {
	header("HTTP/1.1 403 Forbidden");
	exit;
}

class pjProductImportHistoryModel extends pjAppModel
{
	protected $primaryKey = 'id';

	protected $table = 'product_import_history';

	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),

		array('name' => 'company_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'file_name', 'type' => 'string', 'default' => ':NULL'),
		array('name' => 'file_path', 'type' => 'string', 'default' => ':NULL'),
		array('name' => 'file_size', 'type' => 'int', 'default' => ':NULL'),

		array('name' => 'total_rows', 'type' => 'int', 'default' => '0'),
		array('name' => 'processed_rows', 'type' => 'int', 'default' => '0'),
		array('name' => 'failed_rows', 'type' => 'int', 'default' => '0'),

		array('name' => 'status', 'type' => 'string', 'default' => 'uploaded'),

		array('name' => 'uploaded_by', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'uploaded_at', 'type' => 'string', 'default' => ':NULL'),

		array('name' => 'synced_by', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'synced_at', 'type' => 'string', 'default' => ':NULL'),

		array('name' => 'sync_count', 'type' => 'int', 'default' => '0'),

		array('name' => 'error_message', 'type' => 'string', 'default' => ':NULL'),
		array('name' => 'display_name', 'type' => 'varchar', 'default' => ':NULL'),

	);

	public static function factory($attr = array())
	{
		return new self($attr);
	}
}
