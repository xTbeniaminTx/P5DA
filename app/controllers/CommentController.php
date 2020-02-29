<?php

namespace app\controllers;


use app\models\Comment;
use app\models\Post;
use app\services\Redirect;
use app\services\Session;

class CommentController
{
    private $postModel;
    private $commentModel;

    public function __construct()
    {
        $this->postModel = new Post();
        $this->commentModel = new Comment();
    }


    public function unapprouve()
    {
        $id = $_GET['comment_id'];
        $idChapter = $_GET['id'];


        if ($this->commentModel->unapprouveStatus($id)) {
            Session::addMessage('Le commentaire a été désapprouvé', Session::WARNING);

            return Redirect::to('showPost&id=' . $idChapter);

        }

        Session::addMessage('Impossible de traiter cette demande à l\'heure actuelle.', Session::WARNING);

        return Redirect::to('showPost&id=' . $idChapter);

    }

}