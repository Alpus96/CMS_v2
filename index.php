<?php
    /**
    *   This file handels seting the root path and instansing 
    *   the response handler with the requested url.
    *
    *   @uses           ResponseHandler
    *
    *   @category       Request handling
    *   @package        Server index
    *   @subpackage     Request reciever
    *   @version        1.0
    *   @since          1.0
    *   @deprecated     ---
    * */
    define('ROOT_PATH', __DIR__.'/');
    require_once ROOT_PATH.'app/ResponseHandler.php';

    //  NOTE: Remove qequest rewriting on release.
    $url = str_replace('/projects/CMS_v2', '', $_SERVER['REQUEST_URI']);
    $responseHandler = new ResponseHandler($url);
    //  TODO: Use $responseHandler = new ResponseHandler($_SERVER['REQUEST_URI']);

    //  TODO: Move request type handling to response handler.
    //  TODO: Use $responseHandler = new ResponseHandler($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
    if ($_SERVER['REQUEST_METHOD'] === 'GET') { $responseHandler->get(); }
    else if ($_SERVER['REQUEST_METHOD'] === 'POST') { $responseHandler->post(); }
 ?>
