<?php

if($_GET)
{
    $request = $_GET['action'];
}
else
{
    $request = "";
}

require_once('../app/RouterNew.php');
//require_once ('../app/libraries/Database.php');

$routeur = new RouterNew($request);
$routeur->renderController();



