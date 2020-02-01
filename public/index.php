<?php

require_once('../app/RouterNew.php');

if($_GET)
{
    $request = $_GET['action'];
}
else
{
    $request = "";
    $_GET['action'] = '';
}


$routeur = new RouterNew($request);
$routeur->renderController();



