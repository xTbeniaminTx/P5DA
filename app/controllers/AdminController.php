<?php

namespace app\controllers;


use app\models\Post;
use app\services\Auth;
use app\services\CSRFToken;
use app\services\Request;
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


    public function adminPosts()
    {

        $posts = $this->postModel->getPosts();



        View::renderTemplate('admin.chapters.html.twig', [
            'title' => "Admin Chapters",
            'chapters' => $posts,
        ]);


    }

    public function addPost()
    {

        if (false === Request::has('post')) {
            View::renderTemplate('register.html.twig', []);
            return false;
        }

        $request = Request::get('post');

        if (false === CSRFToken::verifyCSRFToken($request->token, false)) {
            throw new \Exception('Token incorect');
        }


//            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
//
//            $data = [
//                'title' => trim($_POST['title']),
//                'content' => trim($_POST['content']),
//                'admin_id' => $_SESSION['admin_id'],
//                'content_date' => date('Y-m-d H:i:s'),
//                'title_err' => '',
//                'content_err' => '',
//            ];
//
//            //Validate data
//            if (empty($data['title'])) {
//                $data['title_err'] = 'Veuillez entre un titre';
//            }
//            if (empty($data['content'])) {
//                $data['content_err'] = 'Veuillez entre un contenu pour votre chapitre';
//            }
//
//            //make sure errors are empty
//            if (empty($data['title_err']) && empty($data['content_err'])) {
//                //validated
//                if ($this->chapterModel->addChapter($data)) {
//                    header('Location: index.php?action=adminChapters');
//                    flash('chapter_message', 'Nouveau chapitre ajouté avec succès');
//                } else {
//                    die('Impossible de traiter cette demande à l\'heure actuelle.');
//                }
//
//            } else {
//                //load view with errors
//                global $twig;
//                $vue = $twig->load('admin.edit.chapters.html.twig');
//                echo $vue->render($data);
//            }


    }

}