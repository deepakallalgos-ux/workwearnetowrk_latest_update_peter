<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjGalleryAppController extends pjPlugin
{
	public function __construct()
	{
		$this->setLayout('pjActionAdmin');
	}
	
	public static function getConst($const)
	{
		$registry = pjRegistry::getInstance();
		$store = $registry->get('pjGallery');
		return isset($store[$const]) ? $store[$const] : NULL;
	}
	
	public function isGalleryReady()
	{
		$reflector = new ReflectionClass('pjPlugin');
		try {
			//Try to find out 'isCountryReady' into parent class
			$ReflectionMethod = $reflector->getMethod('isGalleryReady');
			return $ReflectionMethod->invoke(new pjPlugin(), 'isGalleryReady');
		} catch (ReflectionException $e) {
			//echo $e->getMessage();
			//If failed to find it out, denied access, or not :)
			return false;
		}
	}
	
	public function pjActionCheckInstall()
	{
		$this->setLayout('pjActionEmpty');
		
		$result = array('status' => 'OK', 'code' => 200, 'text' => 'Operation succeeded', 'info' => array());
		$folders = array(
			'app/web/upload',
			'app/web/upload/large',
			'app/web/upload/medium',
			'app/web/upload/small',
			'app/web/upload/source'
		);
		foreach ($folders as $dir)
		{
			if (!is_writable($dir))
			{
				$result['status'] = 'ERR';
				$result['code'] = 101;
				$result['text'] = 'Permission requirement';
				$result['info'][] = sprintf('Folder \'<span class="bold">%1$s</span>\' is not writable. You need to set write permissions (chmod 777) to directory located at \'<span class="bold">%1$s</span>\'', $dir);
			}
		}
		
		return $result;
	}
}
?>