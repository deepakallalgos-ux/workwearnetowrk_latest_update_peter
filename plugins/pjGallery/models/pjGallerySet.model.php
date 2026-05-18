<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjGallerySetModel extends pjGalleryAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'plugin_galleries_set';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'small_width', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'small_height', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'medium_width', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'medium_height', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'modified', 'type' => 'datetime', 'default' => ':NULL'),
		array('name' => 'created', 'type' => 'datetime', 'default' => ':NOW()'),
		array('name' => 'status', 'type' => 'enum', 'default' => 'T')
	);
	
	public $i18n = array('name');
	
	public static function factory($attr=array())
	{
		return new pjGallerySetModel($attr);
	}
	
	public function pjActionSetup()
	{
		
	}
}
?>