<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjOrderExtraModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'orders_extras';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'order_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'order_stock_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'extra_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'extra_item_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'price', 'type' => 'decimal', 'default' => ':NULL')
	);
	
	public static function factory($attr=array())
	{
		return new self($attr);
	}
}
?>