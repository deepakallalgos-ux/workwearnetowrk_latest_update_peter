<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjOrderStockModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'orders_stocks';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'order_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'company_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'stock_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'product_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'price', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'qty', 'type' => 'int', 'default' => ':NULL')
	);
	
	public static function factory($attr=array())
	{
		return new self($attr);
	}
}
?>