<?php

require_once('../app/Router.php');

if($_GET)
{
    $request = $_GET['action'];
}
else
{
    $request = "";
    $_GET['action'] = '';
}


$routeur = new Router($request);
$routeur->renderController();



