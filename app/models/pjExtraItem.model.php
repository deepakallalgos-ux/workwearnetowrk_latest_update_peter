<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjExtraItemModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'extras_items';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'company_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'extra_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'price', 'type' => 'decimal', 'default' => ':NULL')
	);
	
	protected $i18n = array('extra_name');
	
	public static function factory($attr=array())
	{
		return new self($attr);
	}
}
?>