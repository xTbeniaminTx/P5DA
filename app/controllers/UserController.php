<?php

namespace app\controllers;

use app\libraries\CSRFToken;
use app\libraries\Redirect;
use app\libraries\Request;
use app\libraries\Session;
use app\libraries\ValidateRequest;
use app\models\Login;
use app\models\User;


class UserController
{
    /**
     * @var Login
     */
    private $loginModel;

    /**
     * @var User
     */
    private $userModel;

    public function __construct()
    {
        $this->loginModel = new Login();
        $this->userModel = new User();

    }

    public function createSession($login)
    {
        $_SESSION['admin_id'] = $login->id;
        $_SESSION['admin_email'] = $login->email;
        header('Location: index.php?action=adminChapters');
    }

    //------------------------------------------------------------------------------------------------------------------

    public function adminLogin()
    {
        if ($this->isLoggedIn()) {
            Redirect::to('adminChapters');
        } else {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                //process form
                //Sanitaze POST data
                $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

                //init data
                $data = [
                    'email' => trim($_POST['email']),
                    'email_err' => '',
                    'password' => trim($_POST['password']),
                    'password_err' => ''
                ];


                //check if email is entered
                if (empty($data['email'])) {
                    $data['email_err'] = 'Veuillez entre votre email';
                }

                //check if password is entered
                if (empty($data['password'])) {
                    $data['password_err'] = 'Veuillez entre votre mot de passe';
                }

                //check for email
                if ($this->loginModel->findByEmail($data['email'])) {
                    //email found
                } else {
                    $data['email_err'] = 'Aucun utilisateur trouvé';
                }

                //make sure errors are empty
                if (empty($data['email_err']) && empty($data['password_err'])) {
                    //validated
                    //check and set logged user
                    $loggedInAdmin = $this->loginModel->login($data['email'], $data['password']);

                    if ($loggedInAdmin) {
                        //create session
                        $this->createSession($loggedInAdmin);
                    } else {
                        $data['password_err'] = 'Mot de passe invalide';
                        //load view with errors
                        global $twig;
                        $vue = $twig->load('admin.login.html.twig');
                        echo $vue->render($data);
                    }
                } else {
                    //load view with errors
                    global $twig;
                    $vue = $twig->load('admin.login.html.twig');
                    echo $vue->render($data);
                }

            } else {
                //init data
                $data = [
                    'email' => '',
                    'email_err' => '',
                    'password' => '',
                    'password_err' => ''
                ];

                //load view
                global $twig;
                $vue = $twig->load('admin.login.html.twig');
                echo $vue->render($data);
            }
        }

    }

    //------------------------------------------------------------------------------------------------------------------
    public function showRegisterForm()
    {
        //init data
        $data = [];

        //load view
        global $twig;
        $vue = $twig->load('register.html.twig');
        echo $vue->render($data);

    }

    public function showLoginForm()
    {
        //init data
        $data = [
            'email' => '',
            'email_err' => '',
            'password' => '',
            'password_err' => ''
        ];

        //load view
        global $twig;
        $vue = $twig->load('admin.login.html.twig');
        echo $vue->render($data);

    }

    public function registerUser()
    {


        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            //Sanitize the post
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'last_name' => trim($_POST['txtLastName']),
                'first_name' => trim($_POST['txtFirstName']),
                'password' => trim($_POST['password']),
                'email' => trim($_POST['txtEmail']),


            ];


            //make sure errors are empty
            if (!empty($data['last_name']) && !empty($data['first_name'])) {
                //validated
                if ($this->userModel->addUser($data)) {
                    header('Location: index.php?action=registerUser2');
                    flash('user_message', 'Nouveau user ajouté avec succès');
                } else {
                    die('Impossible de traiter cette demande à l\'heure actuelle.');
                }

            } else {
                //load view with errors
                global $twig;
                $vue = $twig->load('register.html.twig');
                echo $vue->render($data);
            }
        } else {
            $data = [

            ];
            global $twig;
            $vue = $twig->load('register.html.twig');
            echo $vue->render($data);
        }

    }

    public function registerUser2()
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
                    'role' => 'member',

                ];

                if ($this->userModel->addUser($data)) {
                    Request::refresh();
                    $data = [
                        'success' => 'Nouveau user ajouté avecdd d succès, veuilliez vous connectez',
                    ];
                    global $twig;
                    $vue = $twig->load('register.html.twig');
                    echo $vue->render($data);
                }
            }
            throw new \Exception('Token incorect');

        }

        $data = [];
        global $twig;
        $vue = $twig->load('register.html.twig');
        echo $vue->render($data);

    }

    public function login()
    {
        if (Request::has('post')) {
            $request = Request::get('post');


            if (CSRFToken::verifyCSRFToken($request->token)) {
                $rules = [
                    'email' => ['required' => true],
                    'password' => ['required' => true]
                ];

                $validate = new ValidateRequest();
                $validate->abide($_POST, $rules);


                if ($validate->hasError()) {

                    $errors = $validate->getErrorMessages();

                    $data = [
                        'errors' => $errors,
                        'email_err' => 'Veuillez entre votre email',

                    ];
                    global $twig;
                    $vue = $twig->load('admin.login.html.twig');
                    echo $vue->render($data);
                    exit;
                }


                $data = [
                    'email' => trim($_POST['email']),
                ];

                $user = $this->userModel->findByEmail($_POST['email']);

                if ($user) {
                    if (!password_verify($request->password, $user->password)) {
                        $data = [
                            'password_err' => 'MDP incorect',
                        ];
                        global $twig;
                        $vue = $twig->load('admin.login.html.twig');
                        echo $vue->render($data);
                        exit;

                    } else {
                        Session::add('SESSION_USER_ID', $user->id);
                        Session::add('SESSION_USER_EMAIL', $user->email);
                        Redirect::to('home');
                    }
                }
            }
            throw new \Exception('Token incorect');

        }

        $data = [];
        global $twig;
        $vue = $twig->load('admin.login.html.twig');
        echo $vue->render($data);


    }

    public function show()
    {
        Session::add('admin', 'You are welcome admin user');

        if (Session::has('admin')) {
            $msg = Session::get('admin');
        } else {
            $msg = 'No session defined';
        }

        $token = CSRFToken::_token();


        $data = [
            'msg' => $msg,
            'token' => $token,
            'password_err' => ''
        ];

        global $twig;
        $vue = $twig->load('register.html.twig');
        echo $vue->render($data);

    }

    public function get()
    {

        $post = \Request::get('post');


        return var_dump($post);

    }

    //------------------------------------------------------------------------------------------------------------------

    public function isLoggedIn()
    {
        if (isset($_SESSION['admin_id'])) {
            return true;
        } else {
            return false;
        }
    }
    //------------------------------------------------------------------------------------------------------------------
}