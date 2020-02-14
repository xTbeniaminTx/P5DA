<?php

namespace app\controllers;

use app\models\Post;
use app\services\Session;

class BaseController
{

    private $postModel;


    public function __construct()
    {

        $this->postModel = new Post();

    }

    public function home()
    {
        $posts = $this->postModel->getPosts();

        Session::view('home.html.twig', ['posts' => $posts]);

    }

    public function showRegisterForm()
    {
       Session::view('register.html.twig', []);
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