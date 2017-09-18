<?php
    /*
    *   NOTE:   '/projects/CMS_v2' should be repalced
    *           with '/CMS' before integration.
    * */
    $url = str_replace('/projects/CMS_v2', '', $_SERVER['REQUEST_URI']);
    require_once 'app/controllers/routes.php';
    $Router = new Router();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') { $Router->post($url); }
    else if ($_SERVER['REQUEST_METHOD'] === 'GET') { $Router->get($url); }
 ?>
