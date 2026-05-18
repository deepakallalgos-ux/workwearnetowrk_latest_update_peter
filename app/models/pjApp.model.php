<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAppModel extends pjModel
{
	public $defaultCompany = 'admin_selected_company';

	public static function factory($attr=array())
	{
		return new pjAppModel($attr);
	}
	
	public function addRule($field)
	{
		if (!isset($this->validate['rules']))
		{
			$this->validate['rules'] = array();
		}
		
		if (!isset($this->validate['rules'][$field])
			&& isset($this->rules[$field]))
		{
			$this->validate['rules'][$field] = $this->rules[$field];
			return TRUE;
		}
		
		return FALSE;
	}
	
	public function removeRule($field, $rule=NULL)
	{
		if (isset($this->validate['rules'], $this->validate['rules'][$field]))
		{
			if (is_null($rule))
			{
				unset($this->validate['rules'][$field]);
				return TRUE;
			}
			
			if (is_array($this->validate['rules'][$field])
				&& is_string($rule)
				&& isset($this->validate['rules'][$field][$rule]))
			{
				unset($this->validate['rules'][$field][$rule]);
				return TRUE;
			}
		}
		
		return FALSE;
	}
}
?>