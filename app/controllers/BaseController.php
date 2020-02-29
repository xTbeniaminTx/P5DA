<?php

namespace app\controllers;

use app\models\Post;
use app\services\Auth;
use app\services\Mail;
use app\services\Request;
use app\services\View;


class BaseController
{

    private $postModel;


    public function __construct()
    {

        $this->postModel = new Post();

    }


    public function indexAction()
    {
        Auth::requireLogin();
        View::renderTemplate('index.404.html.twig');
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

    public function sendMail()
    {
        $request = Request::get('post');
        $email = $request->txtEmail;
        $userName = $request->txtName;
        $mobile = $request->txtPhone;
        $message = $request->txtMsg;

        $text = View::getTemplate(
            'User/contact_email.txt', [
            'userName' => $userName,
            'email' => $email,
            'mobile' => $mobile,
            'message' => $message
            ]
        );
        $html = View::getTemplate(
            'User/contact_email.html', [
            'userName' => $userName,
            'email' => $email,
            'mobile' => $mobile,
            'message' => $message
            ]
        );

        Mail::send('beniamin.tolan@gmail.com', 'Admin BT Blog', $email, 'Message Blog TB Contact', $text, $html);

    }

}
