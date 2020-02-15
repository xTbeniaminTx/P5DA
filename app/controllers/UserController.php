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

        if (Request::has('post')) {
            $request = Request::get('post');


            if (CSRFToken::verifyCSRFToken($request->token, false)) {
                $rules = [
                    'txtLastName' => ['required' => true, 'minLength' => 6],
                    'txtFirstName' => ['required' => true, 'minLength' => 6],
                    'txtEmail' => ['required' => true, 'unique' => true, 'minLength' => 6],
                ];

                $validate = new ValidateRequest();
                $validate->abide($_POST, $rules);


                if ($validate->hasError()) {

                    $errors = $validate->getErrorMessages();

                    Session::view('register.html.twig', [
                        'errors' => $errors
                    ]);
                    die;

                }
                $data = [
                    'last_name' => trim($_POST['txtLastName']),
                    'first_name' => trim($_POST['txtFirstName']),
                    'password' => password_hash($_POST['password'], PASSWORD_BCRYPT),
                    'email' => trim($_POST['txtEmail']),
                    'role' => 'member'
                ];

                if ($this->userModel->addUser($data)) {
                    Request::refresh();
                    Session::view('login.html.twig', [
                        'success' => 'Nouveau user ajouté avec succèss, veuilliez vous connectez avec ',
                    ]);
                }


            } else {
                throw new \Exception('Token incorect');
            }


        } else {
            Session::view('register.html.twig', []);
        }


    }

    public function profile()
    {
        $user = $this->userModel->findByEmail($_SESSION['SESSION_USER_EMAIL']);

        Session::view('profile.html.twig', ['user' => $user]);

    }

}