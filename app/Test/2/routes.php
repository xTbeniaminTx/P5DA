<?php

$router = new AltoRouter;


$router->map('GET', '/', 'App\Controllers\IndexController@show', 'home');

//for admin reoutes
$router->map('GET', '/admin', 'App\Controllers\Admin\DashboardController@show', 'admin_dashboard');




