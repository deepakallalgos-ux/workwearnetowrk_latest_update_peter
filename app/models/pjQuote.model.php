<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjQuoteModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'quotes';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'uuid', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'company_id', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'client_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'address_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'locale_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'tax_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'status', 'type' => 'enum', 'default' => ':NULL'),
		array('name' => 'payment_method', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'txn_id', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'processed_on', 'type' => 'datetime', 'default' => ':NULL'),
		array('name' => 'price', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'discount', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'insurance', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'shipping', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'tax', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'total', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'voucher', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'notes', 'type' => 'text', 'default' => ':NULL'),
		array('name' => 'cc_type', 'type' => 'blob', 'default' => ':NULL', 'encrypt' => 'AES'),
		array('name' => 'cc_num', 'type' => 'blob', 'default' => ':NULL', 'encrypt' => 'AES'),
		array('name' => 'cc_exp_month', 'type' => 'blob', 'default' => ':NULL', 'encrypt' => 'AES'),
		array('name' => 'cc_exp_year', 'type' => 'blob', 'default' => ':NULL', 'encrypt' => 'AES'),
		array('name' => 'cc_code', 'type' => 'blob', 'default' => ':NULL', 'encrypt' => 'AES'),
		array('name' => 'created', 'type' => 'datetime', 'default' => ':NOW()'),
		array('name' => 'ip', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'same_as', 'type' => 'tinyint', 'default' => 0),
		array('name' => 's_name', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 's_country_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 's_state', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 's_city', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 's_zip', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 's_address_1', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 's_address_2', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'b_name', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'b_country_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'b_state', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'b_city', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'b_zip', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'b_address_1', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'b_address_2', 'type' => 'varchar', 'default' => ':NULL')
	);
	
	protected $validate = array(
		'rules' => array(
			'uuid' => array(
				'pjActionAlphaNumeric' => true,
				'pjActionNotEmpty' => true,
				'pjActionRequired' => true
			),
			'ip' => array(
				'pjActionRequired' => true,
				'pjActionNotEmpty' => true
			),
			'status' => array(
				'pjActionRequired' => true,
				'pjActionNotEmpty' => true
			)
		)
	);
	
	public static function factory($attr=array())
	{
		return new self($attr);
	}
}
?>