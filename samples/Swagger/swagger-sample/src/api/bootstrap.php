<?php
/**
* Generated by Flexions for flexions@pereira-da-silva.com on ?
* https://github.com/benoit-pereira-da-silva/Flexions/
*
* DO NOT MODIFY THIS FILE YOUR MODIFICATIONS WOULD BE ERASED ON NEXT GENERATION!
* IF NECESSARY YOU CAN MARK THIS FILE TO BE PRESERVED
* IN THE PREPROCESSOR BY ADDING IN Hypotypose::instance().preservePath
*
* Copyright (c) 2015  COMPANY  All rights reserved.
*/

require_once dirname ( __FILE__ ) . DIRECTORY_SEPARATOR ."v1/Config.php";
require_once dirname ( __FILE__ ) . DIRECTORY_SEPARATOR ."v1/Const.php";
require_once dirname ( __FILE__ ) . DIRECTORY_SEPARATOR ."v1/Api.class.php";

try {
    $API = new API ();
    echo $API->run ();
} catch ( Exception $e ) {
    $status=500;
    $header = "HTTP/1.1 " . $status . " " . $API->requestStatus ( $status );
    header ( $header );
    echo json_encode ( Array (
        "description"=>$API->errorDescription,
        "error" => $e->getMessage ()
    ) );
}
