<?php

namespace app\controllers;

use app\models\Post;
use app\services\Auth;
use app\services\Mail;
use app\services\Redirect;
use app\services\View;


class BaseController
{

    private $postModel;


    public function __construct()
    {

        $this->postModel = new Post();

    }

    public function home()
    {
//        Mail::send('beniamin.tolan@gmail.com', Auth::getUser()->first_name,'Test','This is a test',"<h1>and easy to do anywhere, even with PHP</h1>");

        $posts = $this->postModel->getPosts();

        View::renderTemplate('home.html.twig', ['posts' => $posts]);

    }

    public function showRegisterForm()
    {
        View::renderTemplate('register.html.twig', []);
    }

    public function showLoginForm()
    {
        //init data
        $data = [
            'email' => '',
            'email_err' => '',
            'password' => '',
            'password_err' => ''
        ];

        //load view
        global $twig;
        $vue = $twig->load('admin.login.html.twig');
        echo $vue->render($data);

    }



}