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


            if (CSRFToken::verifyCSRFToken($request->token)) {
                $rules = [
                    'txtLastName' => ['required' => true, 'minLength' => 6],
                    'txtFirstName' => ['required' => true, 'minLength' => 6],
                    'txtEmail' => ['required' => true, 'unique' => true, 'minLength' => 6],
                ];

                $validate = new ValidateRequest();
                $validate->abide($_POST, $rules);


                if ($validate->hasError()) {

                    $errors = $validate->getErrorMessages();

                    $data = [
                        'errors' => $errors
                    ];
                    global $twig;
                    $vue = $twig->load('register.html.twig');
                    echo $vue->render($data);
                    exit;
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
                    $data = [
                        'success' => 'Nouveau user ajouté avecdd d succès, veuilliez vous connectez',
                    ];
                    Session::view('register.html.twig', $data);
                }
            }

            throw new \Exception('Token incorect');

        } else {

            Session::view('register.html.twig', $data = []);

        }

        $data = [];
        global $twig;
        $vue = $twig->load('register.html.twig');
        echo $vue->render($data);

    }

}