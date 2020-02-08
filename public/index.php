<?php

use app\Router;

require_once '../vendor/autoload.php';
require_once '../app/config/config.php';


if($_GET)
{
    $request = $_GET['action'];
}
else
{
    $request = '';
}


$router = new Router($request);
$router->renderController();



