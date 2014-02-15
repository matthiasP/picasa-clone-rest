<?php
require_once('PicasaAPI.class.php');

/* definition des constantes */
define('NKT_DEPRECATED', 1001);
define('NKT_ERROR', 1002);
define('NKT_USER_ERROR', 1003);


function nkt_log($message, $type = 0){

	switch($type):
		case constant('NKT_DEPRECATED'):
			$upgrade = $message;
			$debug = debug_backtrace();
			$offset = 1;
			if(!isset($debug[1]))
				$offset = 0;
			
			$message = 'DEPRECATED:  function '.$debug[$offset]['function'];
			$message.= ' use in file '.$debug[$offset]['file'].' at line '.$debug[$offset]['line'];
			$message.= '. UPGRADE>>'.$upgrade;
			break;
			
		case constant('NKT_ERROR'):
			$error = $message;
			$debug = debug_backtrace();
			$offset = 1;
			if(!isset($debug[1]))
				$offset = 0;
				
			$message = 'function '.$debug[$offset]['function'];
			$message.= ' use in file '.$debug[$offset]['file'].' at line '.$debug[$offset]['line'];
			$message.= '. ERROR>>'.$error;
			break;
			
		case constant('NKT_USER_ERROR'):
			$error = $message;
			$debug = debug_backtrace();
				
			$message = 'file '.$debug[0]['file'].' at line '.$debug[0]['line'];
			$message.= '. USER_ERROR>>'.$error;
			break;
	endswitch;
	
	return error_log(var_export($message, true));
}

 // Requests from the same server don't have a HTTP_ORIGIN header
if(!array_key_exists('HTTP_ORIGIN', $_SERVER)){
  $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}

try {
    $API = new PicasaAPI($_REQUEST['request'], $_SERVER['HTTP_ORIGIN']);
    echo $API->process();
} catch (Exception $e) {
    echo json_encode(array('error' => $e->getMessage()));
}
?>