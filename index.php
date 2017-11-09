<?php
    /**
    *   This file handels seting the root path and instansing 
<<<<<<< HEAD
    *   the response handler with the requested url.
    *
    *   @uses           ResponseHandler
=======
    *   the response handler with the requested url and method.
    *   Also shows error page if responder is unable to give resopnse.
    *
    *   @uses           Response
>>>>>>> master
    *
    *   @category       Request handling
    *   @package        Server index
    *   @subpackage     Request reciever
<<<<<<< HEAD
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
=======
    *   @version        1.2.1
    *   @since          1.0
    *   @deprecated     -
    * */

    //  Define a root path for require paths in project.
    define('ROOT_PATH', __DIR__.'/');
    //  Require the responder.
    require_once ROOT_PATH.'sbin/app/proc/base/response.php';
    //  Var for evaluating response.
    $response = null;
    //  Try creating a new response instnace.
    try { $response = new Response($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']); }
    //  If the responder threw an error set response to the error.
    catch (Exception $e) { $response = $e; }

    if (!$response || $response instanceof Exception) {
        //  Give error page on GET else json success false.
        if ($_SERVER['REQUEST_METHOD'] == 'GET')
        { echo file_get_contents(ROOT_PATH.'error/error.html'); }
        else { echo json_encode((object)['success' => false]); }
        //  Require and instance the logger class.
        require ROOT_PATH.'sbin/lib/srv/logger.php';
        $logger = new Logger('ExceptionLog');
        //  Log the caught error.
        $logger->log($response);
    }

 ?>
>>>>>>> master
