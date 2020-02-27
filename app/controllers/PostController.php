<?php

namespace app\controllers;


use app\models\Comment;
use app\models\Post;
use app\services\Session;
use app\services\View;

class PostController
{
    private $postModel;
    private $commentModel;

    public function __construct()
    {
        $this->postModel = new Post();
        $this->commentModel = new Comment();
    }


    public function posts()
    {
        $chapters = $this->postModel->getPosts();
        $photoId = rand(10, 50);

       View::renderTemplate('posts.html.twig', [
           'title' => "Admin Chapters",
           'chapters' => $chapters,
           'photoId' => $photoId
       ]);
    }

    public function showPost()
    {

        //comment add
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {


            //Sanitize the post
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            if (isset($_GET['id'])) {
                $chapter = $this->postModel->getPostById($_GET['id']);
            }

            $comments = $this->commentModel->getComments();
            $commentsById = $this->commentModel->getCommentsById($_GET['id']);


            $data = [
                'comment_author' => trim($_POST['comment_author']),
                'comment_email' => trim($_POST['comment_email']),
                'comment_content' => trim($_POST['comment_content']),
                'comment_date' => date('Y-m-d H:i:s'),
                'comment_status' => 'newComment',
                'comment_author_err' => null,
                'comment_email_err' => null,
                'comment_content_err' => null,
                'chapter' => $chapter,
                'comment_chapter_id' => $chapter->id,
                'comments' => $comments,
                'commentsById' => $commentsById,
            ];

            //Validate data
            if (empty($data['comment_author'])) {
                $data['comment_author_err'] = 'Veuillez entre un author';
            }
            if (empty($data['comment_email'])) {
                $data['comment_email_err'] = 'Veuillez entre un mail valid';
            }
            if (empty($data['comment_content'])) {
                $data['comment_content_err'] = 'Veuillez entre un contenu pour votre commentaire';
            }

            //make sure errors are empty
            if (empty($data['comment_author_err']) && empty($data['comment_email_err']) && empty($data['comment_content_err'])) {
                //validated
                if ($this->commentModel->addComment($data)) {
                    header('Location: index.php?action=showPost&id=' . $_GET['id']);
                    Session::addMessage('Nouveau commentaire ajouté avec succès', Session::INFO);
                } else {
                    die('Impossible de traiter cette demande à l\'heure actuelle.');
                }

            } else {
                //load view with errors
                global $twig;
                $vue = $twig->load('post.html.twig');
                echo $vue->render($data);
            }
        } else {
            $chapters = $this->postModel->getPosts();
            $chapter = $this->postModel->getPostById($_GET['id']);
            $comments = $this->commentModel->getComments();
            $commentsById = $this->commentModel->getCommentsById($_GET['id']);
            $photoId = rand(10, 50);
            $adminLogged = isset($_SESSION['admin_id']) ? true : false;

            $data = [
                'adminLogged' => $adminLogged,
                'chapter' => $chapter,
                'chapters' => $chapters,
                'comments' => $comments,
                'id' => 10 + rand(10, 50),
                'photoId' => $photoId,
                'commentsById' => $commentsById,
                'comment_date' => date('Y-m-d H:i:s'),


            ];
            global $twig;
            $vue = $twig->load('chapter.html.twig');
            echo $vue->render($data);
        }
    }




}