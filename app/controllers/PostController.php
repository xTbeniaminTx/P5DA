<?php

namespace app\controllers;


class PostController
{

    public function chapters()
    {
        $chapters = $this->chapterModel->getChapters();
        $photoId = rand(10, 50);

        $data = [
            'title' => "Admin Chapters",
            'chapters' => $chapters,
            'photoId' => $photoId
        ];
        global $twig;
        $vue = $twig->load('chapters.html.twig');
        echo $vue->render($data);
    }

    public function showChapter()
    {

        $comment_message = flash('comment_message');
        $message_comment = <<<EOD
                    $comment_message
EOD;

        //comment add
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {


            //Sanitize the post
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            if (isset($_GET['id'])) {
                $chapter = $this->chapterModel->getChaptersById($_GET['id']);
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
                    header('Location: index.php?action=showChapter&id=' . $_GET['id']);
                    flash('comment_message', 'Nouveau commentaire ajouté avec succès');
                } else {
                    die('Impossible de traiter cette demande à l\'heure actuelle.');
                }

            } else {
                //load view with errors
                global $twig;
                $vue = $twig->load('chapter.html.twig');
                echo $vue->render($data);
            }
        } else {
            $chapters = $this->chapterModel->getChapters();
            $chapter = $this->chapterModel->getChaptersById($_GET['id']);
            $comments = $this->commentModel->getComments();
            $commentsById = $this->commentModel->getCommentsById($_GET['id']);
            $photoId = rand(10, 50);
            $adminLogged = isset($_SESSION['admin_id']) ? true : false;

            $data = [
                'adminLogged' => $adminLogged,
                'comment_message' => $message_comment,
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