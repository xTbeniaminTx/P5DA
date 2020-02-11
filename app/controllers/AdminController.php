<?php

namespace app\controllers;

use app\libraries\Request;
use app\libraries\Session;
use app\models\Chapter;
use app\models\Comment;
use app\models\Login;

class AdminController
{

    private $loginModel;
    private $chapterModel;
    private $commentModel;

    public function __construct()
    {
        $this->loginModel = new Login();
        $this->chapterModel = new Chapter();
        $this->commentModel = new Comment();
    }


    public function adminChapters()
    {

        $chapters = $this->chapterModel->getChapters();


        $data = [
            'title' => "Admin Chapters",
            'chapters' => $chapters

        ];
        global $twig;
        $vue = $twig->load('admin.chapters.html.twig');
        echo $vue->render($data);

    }


    public function addChapter()
    {

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            //Sanitize the post
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'title' => trim($_POST['title']),
                'content' => trim($_POST['content']),
                'admin_id' => $_SESSION['admin_id'],
                'content_date' => date('Y-m-d H:i:s'),
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
                if ($this->chapterModel->addChapter($data)) {

                    Request::refresh();
                    $chapters = $this->chapterModel->getChapters();
                    $data = [
                        'title' => "Admin Chapters",
                        'chapters' => $chapters,
                        'success' => 'Nouveau chapitre ajouté avec succès',
                    ];
                    global $twig;
                    $vue = $twig->load('admin.chapters.html.twig');
                    echo $vue->render($data);
                }

            } else {
                //load view with errors
                global $twig;
                $vue = $twig->load('admin.edit.chapters.html.twig');
                echo $vue->render($data);
            }
        } else {
            $data = [
                'title' => '',
                'content' => '',
            ];
            global $twig;
            $vue = $twig->load('admin.edit.chapters.html.twig');
            echo $vue->render($data);
        }

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
            if (isset($_GET['id'])) {
                header('Location: index.php?action=showChapter&id=' . $_GET['id']);
            } else {
                header('Location: index.php?action=adminComments');
            }
            flash('comment_message', 'Le commentaire a été supprimé');
        } else {
            die('Impossible de traiter cette demande à l\'heure actuelle.');
        }

    }


    public function approuve()
    {


        $id = $_GET['id'];

        if ($this->commentModel->approuveStatus($id)) {
            header('Location: index.php?action=adminComments');
            flash('comment_message', 'Le commentaire a été approuvé');
        } else {
            die('Impossible de traiter cette demande à l\'heure actuelle.');
        }


    }


    public function adminView()
    {

        global $twig;
        $vue = $twig->load('admin.base.html.twig');
        echo $vue->render([
            'title' => "Admin Dashboard",
            'admin_id' => $_SESSION['admin_id']
        ]);

    }


    public function profile()
    {

        global $twig;
        $vue = $twig->load('profile.html.twig');
        echo $vue->render([
            'title' => "Profile",
            'admin_id' => $_SESSION['admin_id']
        ]);

    }


    public function logout()
    {
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_email']);
        session_destroy();
        header('Location: index.php?action=home');
    }

}