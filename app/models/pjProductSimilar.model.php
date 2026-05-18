<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjProductSimilarModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'products_similar';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'product_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'company_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'company_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'similar_id', 'type' => 'int', 'default' => ':NULL')
	);
	
	public static function factory($attr=array())
	{
		return new self($attr);
	}
}
?>