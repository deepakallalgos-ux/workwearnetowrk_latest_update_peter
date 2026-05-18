<?php
include PJ_VIEWS_PATH . 'pjFront/elements/header.php'; 
$status = __('front_booking_status', true);
?>
<div class="container-fluid">
	<h2 class="text-uppercase text-primary"><strong><?php __('front_order_completed'); ?></strong></h2><br>
	<div class="alert alert-success" role="alert">
		<?php
		if (!empty($tpl['arr']['payment_method']))
		{
		    if(isset($tpl['params']['plugin']) && !empty($tpl['params']['plugin']))
		    {
		        $payment_messages = __('payment_plugin_messages');
		        echo isset($payment_messages[$tpl['arr']['payment_method']]) ? $payment_messages[$tpl['arr']['payment_method']]: $status[11];
		        if (pjObject::getPlugin($tpl['params']['plugin']) !== NULL)
		        {
		            $controller->requestAction(array('controller' => $tpl['params']['plugin'], 'action' => 'pjActionForm', 'params' => $tpl['params']));
		        }
		    }else{
		        switch ($tpl['arr']['payment_method'])
		        {
		            case 'bank':
		            case 'creditcard':
		            case 'cash':
		            default:
		                $system_msg = str_replace("[STAG]", "<a href='#' class='alert-link scStartOver'>", $status[1]);
		                $system_msg = str_replace("[ETAG]", "</a>", $system_msg);
		                echo $system_msg;
		        }
		    }
		}else{
		    $system_msg = str_replace("[STAG]", "<a href='#' class='alert-link scStartOver'>", $status[1]);
		    $system_msg = str_replace("[ETAG]", "</a>", $system_msg);
		    echo $system_msg;
		}
		?>
	</div>
</div>