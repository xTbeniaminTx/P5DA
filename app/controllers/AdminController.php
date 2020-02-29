<?php

namespace app\controllers;

use app\models\Comment;
use app\models\Post;
use app\models\User;
use app\services\Auth;
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

            View::renderTemplate('Admin/admin.edit.posts.html.twig', []);

            return false;
        }

        $request = Request::get('post');


        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $data = [
            'id' => Auth::getUser()->id,
            'title' => trim($request->title),
            'content' => trim($request->content),
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
            if ($this->postModel->addPost($data)) {
                Session::addMessage('Nouveau chapitre ajouté avec succès');

                return Redirect::to('adminPosts');
            }

        }

        return View::renderTemplate('Admin/admin.edit.posts.html.twig', $data);

    }

    public function editPost()
    {
        if (false === Request::has('post')) {

            $chapter = $this->postModel->getPostById($_GET['id']);
            $data = [
                'title' => $chapter->title,
                'content' => $chapter->content,
                'id' => $chapter->id,
                'chapter' => $chapter
            ];

            return View::renderTemplate('Admin/admin.edit.posts.html.twig', $data);

        }

        $request = Request::get('post');

        //Sanitize the post
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        if (isset($_GET['id'])) {
            $chapter = $this->postModel->getPostById($_GET['id']);
        }
        $data = [
            'title' => trim($request->title),
            'content' => trim($request->content),
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
            if ($this->postModel->updatePost($data)) {
                Session::addMessage('Le chapitre a été modifié avec succès');

                return Redirect::to('adminPosts');
            }

        }

        return View::renderTemplate('Admin/admin.edit.posts.html.twig', $data);

    }

    public function deletePost()
    {

        $request = Request::get('get');
        $id = $request->id;

        if (!$this->postModel->deletePost($id)) {
            Session::addMessage('Impossible de traiter cette demande à l\'heure actuelle.', Session::WARNING);
            return Redirect::to('adminPosts');
        }

        Session::addMessage('L\'article a été supprimé', Session::SUCCESS);
        return Redirect::to('adminPosts');

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
        $chapters = $this->postModel->getPosts();
        foreach ($chapters as $chapter) {
            $id = $chapter->id;
        }
        $comments = $this->commentModel->getComments();
        $chapterModel = $this->postModel;

        $data = [
            'chapterId' => $id,
            'comments' => $comments,
            'chapters' => $chapters,
            'chapterModel' => $chapterModel

        ];

        return View::renderTemplate('Admin/admin.comments.html.twig', $data);
    }

    public function deleteComment()
    {

        $idComment = $_GET['comment_id'];

        if ($this->commentModel->deleteComment($idComment)) {
            Session::addMessage('Le commentaire a été supprimé');
            if (isset($_GET['id'])) {
                return Redirect::to('showChapter&id=' . $_GET['id']);
            }

            return Redirect::to('adminComments');

        }

        Session::addMessage('Impossible de traiter cette demande à l\'heure actuelle.');
        return Redirect::to('adminComments');

    }

    public function approuve()
    {
        $id = $_GET['id'];

        if (!$this->commentModel->approuveStatus($id)) {
            Session::addMessage('Impossible de traiter cette demande à l\'heure actuelle.');
            return Redirect::to('adminComments');

        }

        Session::addMessage('Le commentaire a été approuvé');

        return Redirect::to('adminComments');
    }

    public function superView()
    {

        View::renderTemplate('admin.base.html.twig');

    }

}