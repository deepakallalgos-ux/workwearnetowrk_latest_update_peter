<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjStockAttributeModel extends pjAppModel
{
	protected $primaryKey = null;
	
	protected $table = 'stocks_attributes';
	
	protected $schema = array(
		array('name' => 'stock_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'product_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'company_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'attribute_parent_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'attribute_id', 'type' => 'int', 'default' => ':NULL'),
		

	);
	
	public static function factory($attr=array())
	{
		return new self($attr);
	}
}
?>