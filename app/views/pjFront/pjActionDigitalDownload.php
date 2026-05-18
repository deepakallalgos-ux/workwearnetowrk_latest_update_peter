<?php
if (isset($tpl['status']))
{
	$status = __('digital_status', true);
	echo @$status[$tpl['status']];
}
?>