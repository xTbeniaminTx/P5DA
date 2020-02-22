<?php

namespace app\controllers;


use app\models\Post;
use app\services\Auth;
use app\services\View;

class AdminController
{
    private $postModel;

    public function __construct()
    {
        $this->postModel = new Post();
    }


    public function indexAction()
    {
        Auth::requireLogin();
        View::renderTemplate('index.html.twig');
    }


    public function adminChapters()
    {

        $posts = $this->postModel->getPosts();



        View::renderTemplate('admin.chapters.html.twig', [
            'title' => "Admin Chapters",
            'chapters' => $posts,
        ]);


    }

}