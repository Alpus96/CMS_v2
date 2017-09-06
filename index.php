<?php
    //echo $_REQUEST['url'];
    print_r($_SERVER['REQUEST_METHOD']);

    require 'app/controllers/Router.php';
    $Router = new Router();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') { $Router->post(); }
    else { $Router->get(); }
 ?>