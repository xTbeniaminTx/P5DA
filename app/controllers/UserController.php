<?php

namespace app\controllers;

use app\models\User;
use app\services\Auth;
use app\services\CSRFToken;
use app\services\Request;
use app\services\Session;
use app\services\ValidateRequest;
use app\services\View;


class UserController extends CoreController
{

    /**
     * @var User
     */
    private $userModel;


    public function __construct()
    {
        $this->userModel = new User();
    }

    public function before() {
        Auth::requireLogin();
    }

    public function registerUser()
    {
        if (false === Request::has('post')) {
            return View::renderTemplate('register.html.twig', []);
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

            return View::renderTemplate('register.html.twig', [
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

        return View::renderTemplate('login.html.twig', [
            'success' => 'Nouveau user ajouté avec succèss, veuilliez vous connectez avec ',
        ]);
    }

    public function profile()
    {
        Auth::requireLogin();

        $user = $this->userModel->findByEmail($_SESSION['SESSION_USER_EMAIL']);

        return View::renderTemplate('profile.html.twig', ['user' => $user]);

    }

}