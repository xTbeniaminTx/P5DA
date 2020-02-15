<?php

namespace app\controllers;


use app\services\Auth;
use app\services\Redirect;
use app\services\View;

class AdminController
{
    public function indexAction()
    {
        Auth::requireLogin();
        return View::renderTemplate('index.html.twig', [
            'user' => Auth::getUser()
        ]);
    }

}