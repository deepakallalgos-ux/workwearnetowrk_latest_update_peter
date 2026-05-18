<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjGalleryModel extends pjGalleryAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'plugin_gallery';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'foreign_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'model', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'hash', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'mime_type', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'small_path', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'small_size', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'small_width', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'small_height', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'medium_path', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'medium_size', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'medium_width', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'medium_height', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'large_path', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'large_size', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'large_width', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'large_height', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'source_path', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'source_size', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'source_width', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'source_height', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'name', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'title', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'description', 'type' => 'text', 'default' => ':NULL'),
		array('name' => 'alt', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'watermark', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'sort', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'created', 'type' => 'datetime', 'default' => 'NOW()')
	);
	
	public $i18n = array('title');
	
	public static function factory($attr=array())
	{
		return new pjGalleryModel($attr);
	}
	
	public function pjActionSetup()
	{
		
	}
}
?>