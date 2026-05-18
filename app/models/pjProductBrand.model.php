<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjProductBrandModel extends pjAppModel
{
	protected $primaryKey = null;
	
	protected $table = 'products_brands';
	
	protected $schema = array(
		array('name' => 'product_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'company_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'brand_id', 'type' => 'int', 'default' => ':NULL')
	);
	
	public static function factory($attr=array())
	{
		return new self($attr);
	}
}
?>