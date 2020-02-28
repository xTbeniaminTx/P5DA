<?php

namespace app\controllers;

use app\models\Post;
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
        $posts = $this->postModel->getPosts();

        View::renderTemplate('home.html.twig', ['posts' => $posts]);
    }

    public function showRegisterForm()
    {
        View::renderTemplate('User/register.html.twig', []);
    }


    public function contact()
    {
        View::renderTemplate('User/contact.html.twig');
    }


}