<?php
    //  NOTE:   '/projects/CMS_v2' should be repalced with '/CMS' before integration.
    $url = str_replace('/projects/CMS_v2', '', $_SERVER['REQUEST_URI']);
    require_once 'app/controllers/routes.php';
    $Router = new Router();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') { $Router->post($url); }
    else if ($_SERVER['REQUEST_METHOD'] === 'GET') { $Router->get($url); }

    $tst = (object)['type' => 1, 'username' => 'Alpus96', 'hash' => 'password'];
    //print_r(array_keys((array)$tst));
    print_r(array_keys((array)$tst));
    echo array_keys((array)$tst) === ['type', 'username', 'hash'] ? 'true' : 'false' ;
 ?>
