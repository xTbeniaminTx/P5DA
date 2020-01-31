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
    $request = "";
}


//require_once('../app/Router.php');
//require_once ('../app/libraries/Database.php');

$routeur = new Router($request);
$routeur->renderController();



