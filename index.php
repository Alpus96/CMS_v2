<?php
    /*
    *   NOTE:   '/projects/CMS_v2' should be repalced
    *           with '/CMS' before integration.
    * */
    $url = str_replace('/projects/CMS_v2', '', $_SERVER['REQUEST_URI']);
    require_once 'app/ResponseHandler.php';
    $responseHandler = new ResponseHandler($url);

    if ($_SERVER['REQUEST_METHOD'] === 'GET') { $responseHandler->get(); }
    else if ($_SERVER['REQUEST_METHOD'] === 'POST') { $responseHandler->post(); }
 ?>
