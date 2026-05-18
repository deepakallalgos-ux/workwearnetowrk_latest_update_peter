<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjCartModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'carts';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'stock_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'product_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'hash', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'key_data', 'type' => 'text', 'default' => ':NULL'),
		array('name' => 'qty', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'is_cart', 'type' => 'enum', 'default' => '1')

	);
	
	public static function factory($attr=array())
	{
		return new self($attr);
	}
}
?>