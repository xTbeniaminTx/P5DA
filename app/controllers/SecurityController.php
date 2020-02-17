<?php

namespace app\controllers;


use app\models\User;
use app\services\Auth;
use app\services\CSRFToken;
use app\services\Redirect;
use app\services\Request;
use app\services\Session;
use app\services\ValidateRequest;
use app\services\View;

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

            if (CSRFToken::verifyCSRFToken($request->token, false)) {

                $validate = new ValidateRequest();
                $validate->abide($_POST, [
                    'email' => ['required' => true],
                    'password' => ['required' => true]
                ]);

                if ($validate->hasError()) {

                    $errors = $validate->getErrorMessages();

                    return View::renderTemplate('login.html.twig', [
                        'errors' => $errors,
                        'email_err' => 'Veuillez entre votre email'
                    ]);

                }

                if (!Auth::isUser()) {
                    Session::addMessage('Email incorect', Session::WARNING);
                    return Redirect::to('login');
                }

                $user = $this->userModel->findByEmail($_POST['email']);

                if ($user) {
                    if (!password_verify($request->password, $user->password)) {
                        $data = [
                            'password_err' => 'MDP incorect',
                        ];
                        Session::addMessage('MDP incorect');
                        return Redirect::to('login');

                    } else {
                        Auth::auth($user);
                        Session::addMessage('Succesful login');
                        return Redirect::to(Auth::getReturnToPage());
                    }
                }
            }
            throw new \Exception('Token incorect');

        }

        View::renderTemplate('login.html.twig', []);

    }

    public function logout()
    {
        Auth::destroy();
        Redirect::to('showLogoutMessage');
    }

    public function showLogoutMessage()
    {
        Session::addMessage('Logout succesfuly');
        Redirect::to('home');
    }

}