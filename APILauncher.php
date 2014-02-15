<?php
include_once('nkt/nkt_basics.php');
require_once('PicasaAPI.class.php');

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