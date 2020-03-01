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
        if (false === Request::has('post')) {

            return View::renderTemplate('User/login.html.twig', []);

        }

        $request = Request::get('post');

        if (!CSRFToken::verifyCSRFToken($request->token, false)) {
            throw new \Exception('Token incorect');
        }

        $validate = new ValidateRequest();
        $validate->abide(
            $_POST, [
                'email' => ['required' => true],
                'MotDePasse' => ['required' => true]
            ]
        );

        if ($validate->hasError()) {
            $errors = $validate->getErrorMessages();

            return View::renderTemplate(
                'User/login.html.twig', [
                    'errors' => $errors
                ]
            );

        }

        if (!Auth::isUserExist($request->email)) {
            Session::addMessage('Email incorect', Session::WARNING);

            return Redirect::to('login');
        }

        $user = $this->userModel->findByEmail($request->email);

        if (!password_verify($request->MotDePasse, $user->password)) {
            Session::addMessage('Mot De Passe Incorrect', Session::WARNING);

            return Redirect::to('login');
        }

        Auth::auth($user);
        Session::addMessage('Succesful login', Session::SUCCESS);

        return Redirect::to(Auth::getReturnToPage());
    }

    public function logout()
    {
        Auth::destroy();
        Redirect::to('showLogoutMessage');
    }

    public function showLogoutMessage()
    {
        Session::addMessage('Déconnexion réussie');
        Redirect::to('home');
    }


    public function requestReset()
    {

        $request = Request::get('post');

        if (false === Request::has('post') || false === isset($request->email)) {
            View::renderTemplate('Password/lost.html.twig');
            return;
        }

        if (false === Auth::isUserExist($request->email)) {
            Session::addMessage('Utilisateur inexistant');
            Redirect::to('requestReset');

            return;
        }

        $this->userModel->sendPasswordReset($request->email);
        View::renderTemplate('resetRequest.html.twig');
    }

    public function resetPass()
    {
        $requestGet = Request::get('get');

        $token = $requestGet->token;
        $email = $requestGet->email;

        $user = $this->getUserOrExit($token);

        if (false === $user) {
            return false;
        }

        View::renderTemplate(
            'Password/reset.html.twig', [
                'token' => $token,
                'email' => $email
            ]
        );

        return true;
    }

    public function resetPassword()
    {
        $request = Request::get('post');

        $token = $request->tokenPass;
        $email = $request->emailReset;
        $password = $request->password;

        $user = $this->getUserOrExit($token);

        $validate = new ValidateRequest();
        $validate->abide(
            $_POST, [
                'password' => ['required' => true, 'minLength' => 5]
            ]
        );

        if ($validate->hasError()) {

            $errors = $validate->getErrorMessages();

            View::renderTemplate(
                'Password/reset.html.twig', [
                    'errors' => $errors,
                    'user' => $user,
                    'token' => $token,
                    'email' => $email
                ]
            );

            return false;
        }

        $this->userModel->resetPassword($password, $user);

        Session::addMessage('Mot de passe initialize avec succès', Session::INFO);
        return Redirect::to('login');
    }

    protected function getUserOrExit($token)
    {
        $user = $this->userModel->findByPasswordReset($token);

        if (!$user) {
            View::renderTemplate('Password/token_expired.html.twig');

            return false;
        }

        return $user;
    }

}
