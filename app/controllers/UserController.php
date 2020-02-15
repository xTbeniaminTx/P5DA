<?php

namespace app\controllers;

use app\models\User;
use app\services\CSRFToken;
use app\services\Request;
use app\services\Session;
use app\services\ValidateRequest;


class UserController
{

    /**
     * @var User
     */
    private $userModel;


    public function __construct()
    {
        $this->userModel = new User();
    }

    public function registerUser()
    {
        if (false === Request::has('post')) {
            return Session::view('register.html.twig', []);
        }

        $request = Request::get('post');

        if (false === CSRFToken::verifyCSRFToken($request->token, false)) {
            throw new \Exception('Token incorect');
        }

        $rules = [
            'txtLastName' => ['required' => true, 'minLength' => 6],
            'txtFirstName' => ['required' => true, 'minLength' => 6],
            'txtEmail' => ['required' => true, 'uniqueEmail' => true, 'minLength' => 6],
        ];

        $validate = new ValidateRequest();
        $validate->abide($_POST, $rules);

        if ($validate->hasError()) {
            $errors = $validate->getErrorMessages();

            return Session::view('register.html.twig', [
                'errors' => $errors
            ]);
        }

        $data = [
            'last_name' => trim($_POST['txtLastName']),
            'first_name' => trim($_POST['txtFirstName']),
            'password' => password_hash($_POST['password'], PASSWORD_BCRYPT),
            'email' => trim($_POST['txtEmail']),
            'role' => 'member'
        ];

        $this->userModel->addUser($data);

        return Session::view('login.html.twig', [
            'success' => 'Nouveau user ajouté avec succèss, veuilliez vous connectez avec ',
        ]);
    }

    public function profile()
    {
        $user = $this->userModel->findByEmail($_SESSION['SESSION_USER_EMAIL']);

        Session::view('profile.html.twig', ['user' => $user]);

    }

}