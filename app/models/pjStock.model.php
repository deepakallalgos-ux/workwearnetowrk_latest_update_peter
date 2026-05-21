<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjStockModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'stocks';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'company_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'product_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'image_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'qty', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'price', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'buying_price', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'article_number', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'article_name', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'ean', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'status', 'type' => 'enum', 'default' => 'T')

	);
	
	public static function factory($attr=array())
	{
		return new self($attr);
	}
}
?>