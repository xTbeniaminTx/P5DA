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
            View::renderTemplate('login.html.twig', []);

            return false;
        }

        $request = Request::get('post');

        if (!CSRFToken::verifyCSRFToken($request->token, false)) {
            throw new \Exception('Token incorect');
        }

        $validate = new ValidateRequest();
        $validate->abide($_POST, [
            'email' => ['required' => true],
            'MotDePasse' => ['required' => true]
        ]);

        if ($validate->hasError()) {
            $errors = $validate->getErrorMessages();

            View::renderTemplate('login.html.twig', [
                'errors' => $errors
            ]);

            return false;
        }

        if (!Auth::isUserExist($request->email)) {
            Session::addMessage('Email incorect', Session::WARNING);

            return Redirect::to('login');
        }

        $user = $this->userModel->findByEmail($_POST['Email']);

        if (!password_verify($request->MotDePasse, $user->password)) {
            Session::addMessage('MDP incorect');

            return Redirect::to('login');
        }

        Auth::auth($user);
        Session::addMessage('Succesful login');

        return Redirect::to(Auth::getReturnToPage());
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



    public function requestReset()
    {
        if (false === Request::has('post') || false === isset($_POST['email'])) {
            View::renderTemplate('lost.html.twig');
            return;
        }

        if (false === Auth::isUserExist($_POST['email'])) {
            Session::addMessage('Utilisateur inexistant');
            Redirect::to('requestReset');

            return;
        }

        $this->userModel->sendPasswordReset($_POST['email']);
        View::renderTemplate('resetRequest.html.twig');
    }

    public function resetPass()
    {
        $token = $_GET['token'];
        $email = $_GET['email'];

        $user = $this->getUserOrExit($token);

        if (!$user) {
            return false;
        }
        View::renderTemplate('Password/reset.html.twig', [
            'token' => $token,
            'email' => $email
        ]);

        return true;
    }

    public function resetPassword()
    {
        $token = $_POST['tokenPass'];
        $email = $_POST['emailReset'];
        $password = $_POST['password'];

        $user = $this->getUserOrExit($token);

        $validate = new ValidateRequest();
        $validate->abide($_POST, [
            'password' => ['required' => true, 'minLength' => 5]
        ]);

        if ($validate->hasError()) {

            $errors = $validate->getErrorMessages();

            View::renderTemplate('Password/reset.html.twig', [
                'errors' => $errors,
                'user' => $user,
                'token' => $token,
                'email' => $email
            ]);

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