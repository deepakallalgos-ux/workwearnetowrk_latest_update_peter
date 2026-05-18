<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjCompanyModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'companies';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'is_deleted', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'name', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'w_number', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'email', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'phone', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'country', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'city', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'state', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'zip', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'address', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'status', 'type' => 'enum', 'default' => 'T')
	);
     
	public $i18n = array('name');
	
	public static function factory($attr=array())
	{
		return new pjCompanyModel($attr);
	}
}
?>