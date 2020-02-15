<?php

namespace app\controllers;


use app\models\User;
use app\services\CSRFToken;
use app\services\Redirect;
use app\services\Request;
use app\services\Session;
use app\services\ValidateRequest;

class SecurityController
{
    private $userModel;


    public function __construct()
    {

        $this->userModel = new User();

    }


    public function login()
    {
        if (Request::has('post')) {
            $request = Request::get('post');

            if (CSRFToken::verifyCSRFToken($request->token)) {

                $validate = new ValidateRequest();
                $validate->abide($_POST, [
                    'email' => ['required' => true],
                    'password' => ['required' => true]
                ]);

                if ($validate->hasError()) {

                    $errors = $validate->getErrorMessages();

                    Session::view('login.html.twig', [
                        'errors' => $errors,
                        'email_err' => 'Veuillez entre votre email'
                    ]);
                    exit;
                }

                $user = $this->userModel->findByEmail($_POST['email']);

                if ($user) {
                    if (!password_verify($request->password, $user->password)) {
                        $data = [
                            'password_err' => 'MDP incorect',
                        ];
                        Session::view('login.html.twig', $data);
                        exit;

                    } else {
                        Session::add('SESSION_USER_ID', $user->id);
                        Session::add('SESSION_USER_EMAIL', $user->email);
                        Session::add('role', $user->role);
                        Redirect::to('profile');
                    }
                }
            }
            throw new \Exception('Token incorect');

        }

        Session::view('login.html.twig', []);

    }

    public function logout()
    {
        unset($_SESSION['SESSION_USER_ID']);
        unset($_SESSION['SESSION_USER_EMAIL']);
        session_destroy();
        header('Location: index.php?action=home');
    }

}