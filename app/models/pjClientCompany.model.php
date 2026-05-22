<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjClientCompanyModel extends pjAppModel
{
	protected $primaryKey = NULL;

	protected $table = 'client_companies';

	protected $schema = array(
		array('name' => 'client_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'company_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'created', 'type' => 'datetime', 'default' => ':NOW()')
	);

	public static function factory($attr=array())
	{
		return new self($attr);
	}
}
?>

