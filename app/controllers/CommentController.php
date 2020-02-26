<?php

namespace app\controllers;


class CommentController
{
    public function unapprouve()
    {
        $id = $_GET['comment_id'];
        $idChapter = $_GET['id'];


        if ($this->commentModel->unapprouveStatus($id)) {
            if ($this->isLoggedIn()) {
                header('Location: index.php?action=adminComments');
                flash('comment_message', 'Le commentaire a été désapprouvé');
            } else {
                header('Location: index.php?action=showChapter&id=' . $idChapter);
                flash('comment_message', 'Le commentaire a été désapprouvé');
            }

        } else {
            die('Impossible de traiter cette demande à l\'heure actuelle.');
        }

    }

}