<?php
if (!defined("ROOT_PATH")) {
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjOptionModel extends pjAppModel
{
	protected $primaryKey = NULL;

	protected $table = 'options';

	protected $schema = array(
		array('name' => 'foreign_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'key', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'tab_id', 'type' => 'tinyint', 'default' => ':NULL'),
		array('name' => 'value', 'type' => 'text', 'default' => ':NULL'),
		array('name' => 'label', 'type' => 'text', 'default' => ':NULL'),
		array('name' => 'type', 'type' => 'varchar', 'default' => 'string'),
		array('name' => 'order', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'company_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'is_visible', 'type' => 'tinyint', 'default' => 1),
		array('name' => 'style', 'type' => 'varchar', 'default' => 'string')
	);

	public static function factory($attr = array())
	{
		return new pjOptionModel($attr);
	}

	private function getActiveCompanyId()
	{
		return (
			isset($_SESSION['admin_selected_company']['id']) &&
			(int)$_SESSION['admin_selected_company']['id'] > 0
		) ? (int) $_SESSION['admin_selected_company']['id'] : 1;
	}

	public function getAllPairs($foreign_id)
	{
		$company_id = $this->getActiveCompanyId();

		return $this->where('t1.foreign_id', $company_id)
			->where('t1.company_id', $company_id)
			->findAll()
			->getDataPair('key', 'value');
	}

	public function getPairs($foreign_id)
	{
		$company_id = $this->getActiveCompanyId();

		$_arr = $this->where('t1.foreign_id', $company_id)
			->where('t1.company_id', $company_id)
			->findAll()
			->getData();

		$arr = array();
		foreach ($_arr as $row) {
			switch ($row['type']) {
				case 'enum':
				case 'bool':
					list(, $arr[$row['key']]) = explode("::", $row['value']);
					break;
				default:
					$arr[$row['key']] = $row['value'];
					break;
			}
		}
		return $arr;
	}
}
