<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjGalleryUtil
{
	public static function isConvertPossible($filename)
	{
		if (!(function_exists('memory_get_usage') && ini_get('memory_limit')))
		{
			return array('status' => 'ERR', 'code' => 100, 'text' => 'Could not get memory stats.');
		}
	
		$info = getimagesize($filename);
		if (!$info)
		{
			return array('status' => 'ERR', 'code' => 101, 'text' => 'Could not get image size.');
		}
	
		$MB = 1024 * 1024;
		$K64 = 64 * 1024;
		$tweak_factor = 1.6;
		$channels = isset($info['channels']) ? $info['channels'] : 3; // 3 for RGB pictures and 4 for CMYK pictures
		$memory_needed = round(($info[0] * $info[1] * $info['bits'] * $channels / 8 + $K64) * $tweak_factor);
		$memory_needed = memory_get_usage() + $memory_needed;
		$memory_limit = ini_get('memory_limit');
	
		if ($memory_limit != '')
		{
			$memory_limit = substr($memory_limit, 0, -1) * $MB;
		}
	
		if ($memory_needed > $memory_limit)
		{
			return array('status' => 'ERR', 'code' => 102, 'text' => 'Not enough memory.', 'memory_needed' => $memory_needed, 'memory_limit' => $memory_limit);
		}
	
		return array('status' => 'OK', 'code' => 200, 'text' => 'Memory just enough.', 'memory_needed' => $memory_needed, 'memory_limit' => $memory_limit);
	}
}
?>