<?php

namespace app\controllers;

use app\models\User;
use app\services\Auth;
use app\services\CSRFToken;
use app\services\Request;
use app\services\Session;
use app\services\ValidateRequest;
use app\services\View;
use mysql_xdevapi\Exception;


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

    public function before()
    {
        Auth::requireLogin();
    }

    public function registerUser()
    {
        if (false === Request::has('post')) {
            View::renderTemplate('register.html.twig', []);
            return false;
        }

        $request = Request::get('post');

        if (false === CSRFToken::verifyCSRFToken($request->token, false)) {
            throw new \Exception('Token incorect');
        }

        $validate = new ValidateRequest();
        $validate->abide($_POST, [
            'Nom' => ['required' => true, 'minLength' => 3, 'maxLength' => 20],
            'Prénom' => ['required' => true, 'minLength' => 3],
            'email' => ['required' => true, 'uniqueEmail' => true, 'minLength' => 6],
            'MotDePasse' => ['required' => true]
        ]);

        if ($validate->hasError()) {
            $errors = $validate->getErrorMessages();

            View::renderTemplate('register.html.twig', [
                'errors' => $errors
            ]);
            return false;
        }

        $data = [
            'last_name' => trim($_POST['Nom']),
            'first_name' => trim($_POST['Prénom']),
            'password' => password_hash($_POST['MotDePasse'], PASSWORD_BCRYPT),
            'email' => trim($_POST['email']),
            'role' => 'member'
        ];

        $this->userModel->addUser($data);

        View::renderTemplate('login.html.twig', [
            'success' => 'Inscription faite avec succèss, veuilliez vous connectez',
        ]);

        return true;
    }

    public function profile()
    {
        Auth::requireLogin();

        $user = Auth::getUser();

        View::renderTemplate('profile.html.twig', ['user' => $user]);
        return;

    }

    public function editProfile()
    {
        Auth::requireLogin();

        $user = Auth::getUser();

        if (false === Request::has('post')) {
            View::renderTemplate('edit.profile.html.twig', ['user' => $user]);
            return false;
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

            View::renderTemplate('edit.profile.html.twig', [
                'errors' => $errors,
                'user' => $user
            ]);
            return false;
        }

        if ($this->updateProfile()) {
            View::renderTemplate('profile.html.twig', [
                'success' => 'Informations éditées avec succès',
                'user' => Auth::getUser()
            ]);
            return true;
        }

        throw new \Exception('Un error est sourvenu, veuilez essayer plus tard');


    }

    public function updateProfile()
    {
        $user = Auth::getUser();

        $data = [
            'last_name' => trim($_POST['txtLastName']),
            'first_name' => trim($_POST['txtFirstName']),
            'password' => $_POST['password'] != null ? password_hash($_POST['password'], PASSWORD_BCRYPT) : $user->password,
            'email' => trim($_POST['txtEmail']),
            'role' => $user->role,
            'id' => $user->id
        ];

        return $this->userModel->updateUser($data);

    }

}