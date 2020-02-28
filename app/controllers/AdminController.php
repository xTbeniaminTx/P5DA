<?php

namespace app\controllers;

use app\models\Comment;
use app\models\Post;
use app\models\User;
use app\services\Auth;
use app\services\CSRFToken;
use app\services\Redirect;
use app\services\Request;
use app\services\Session;
use app\services\View;

class AdminController
{
    private $postModel;
    private $commentModel;
    private $userModel;

    public function __construct()
    {
        $this->postModel = new Post();
        $this->commentModel = new Comment();
        $this->userModel = new User();
    }


    public function indexAction()
    {
        Auth::requireLogin();
        View::renderTemplate('index.404.html.twig');
    }


    public function adminPosts()
    {
        $posts = $this->postModel->getPosts();

        View::renderTemplate('Admin/admin.posts.html.twig', [
            'title' => "Administration",
            'posts' => $posts,
        ]);
    }

    public function superAdminView()
    {
        $users = $this->userModel->getUsers('member');

        View::renderTemplate('Admin/super.admin.html.twig', [
            'title' => "Super Administration",
            'users' => $users,
        ]);
    }

    public function grantRoleAdmin()
    {

        $request = Request::get('get');
        $id = $request->id;
        if (!$this->userModel->updateUserToAdmin($id)) {
            Session::addMessage('Modification error', Session::WARNING);

            return Redirect::to('superAdminView');
        }
        Session::addMessage('Le Role Utilisateur a ete modifier avec succes');

        return Redirect::to('superAdminView');

    }

    public function addPost()
    {
        if (false === Request::has('post')) {
            View::renderTemplate('User/register.html.twig', []);

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

    public function editChapter()
    {

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            //Sanitize the post
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            if (isset($_GET['id'])) {
                $chapter = $this->chapterModel->getChaptersById($_GET['id']);
            }
            $data = [
                'title' => trim($_POST['title']),
                'content' => trim($_POST['content']),
                'admin_id' => $_SESSION['admin_id'],
                'id' => $chapter->id,
                'title_err' => '',
                'content_err' => '',
            ];

            //Validate data
            if (empty($data['title'])) {
                $data['title_err'] = 'Veuillez entre un titre';
            }
            if (empty($data['content'])) {
                $data['content_err'] = 'Veuillez entre un contenu pour votre chapitre';
            }

            //make sure errors are empty
            if (empty($data['title_err']) && empty($data['content_err'])) {
                //validated
                if ($this->chapterModel->updateChapter($data)) {
                    header('Location: index.php?action=adminChapters');
                    flash('chapter_message', 'Le chapitre a été modifié avec succès');
                } else {
                    die('Impossible de traiter cette demande à l\'heure actuelle.');
                }

            } else {
                //load view with errors
                global $twig;
                $vue = $twig->load('admin.edit.chapters.html.twig');
                echo $vue->render($data);
            }
        } else {
            $chapter = $this->chapterModel->getChaptersById($_GET['id']);
            $data = [
                'title' => $chapter->title,
                'content' => $chapter->content,
                'id' => $chapter->id,
                'chapter' => $chapter
            ];
            global $twig;
            $vue = $twig->load('admin.edit.chapters.html.twig');
            echo $vue->render($data);
        }

    }

    public function deleteChapter()
    {

        $id = $_GET['id'];

        if ($this->chapterModel->deleteChapter($id)) {
            header('Location: index.php?action=adminChapters');
            flash('chapter_message', 'Le chapitre a été supprimé');
        } else {
            die('Impossible de traiter cette demande à l\'heure actuelle.');
        }

    }

    public function deleteUser()
    {
        $request = Request::get('get');
        $id = $request->id;
        if (!$this->userModel->deleteUser($id)) {
            Session::addMessage('Impossible de traiter cette demande à l\'heure actuelle.', Session::WARNING);

            return Redirect::to('superAdminView');
        }
        Session::addMessage('Le Utilisateur a été supprimé avec succes');

        return Redirect::to('superAdminView');
    }

    public function adminComments()
    {
        $chapters = $this->chapterModel->getChapters();
        foreach ($chapters as $chapter) {
            $id = $chapter->id;
        }
        $comments = $this->commentModel->getComments();
        $chapterModel = $this->chapterModel;

        $comment_message = flash('comment_message');
        $message_comment = <<<EOD
                    $comment_message
EOD;

        $data = [
            'chapterId' => $id,
            'comments' => $comments,
            'chapters' => $chapters,
            'comment_message' => $message_comment,
            'chapterModel' => $chapterModel

        ];
        global $twig;
        $vue = $twig->load('admin.comments.html.twig');
        echo $vue->render($data);

    }

    public function deleteComment()
    {

        $idComment = $_GET['comment_id'];

        if ($this->commentModel->deleteComment($idComment)) {
            Session::addMessage('Le commentaire a été supprimé');
            if (isset($_GET['id'])) {
                Redirect::to('showChapter&id=' . $_GET['id']);
            } else {
                Redirect::to('adminComments');
            }
        } else {
            die('Impossible de traiter cette demande à l\'heure actuelle.');
        }

    }

    public function approuve()
    {
        $id = $_GET['id'];

        if ($this->commentModel->approuveStatus($id)) {
            Session::addMessage('Le commentaire a été approuvé');
            Redirect::to('adminComments');

        } else {
            die('Impossible de traiter cette demande à l\'heure actuelle.');
        }
    }

    public function superView()
    {

        View::renderTemplate('admin.base.html.twig');

    }

}