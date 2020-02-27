<?php

namespace app;

use app\controllers\AdminController;
use app\controllers\BaseController;
use app\controllers\PostController;
use app\controllers\SecurityController;
use app\controllers\UserController;
use app\services\Auth;
use app\services\Redirect;
use app\services\Session;


class Router
{
    private $request;
    private $error;

    public function __construct($request)
    {
        $this->request = $request;
        $this->action = $this->getAction();
    }

    const ROUTES = [
        [
            '' => [BaseController::class, 'home'],
            'showRegisterForm' => [BaseController::class],
            'contact' => [BaseController::class],
            'sendMail' => [BaseController::class],
            'registerUser' => [UserController::class],
            'login' => [SecurityController::class],
            'showLogoutMessage' => [SecurityController::class],
            'forgotPass' => [SecurityController::class],
            'requestReset' => [SecurityController::class],
            'resetPass' => [SecurityController::class],
            'resetPassword' => [SecurityController::class],
            'indexAction' => [AdminController::class],
            'profile' => [UserController::class],
            'posts' => [PostController::class],
            'showPost' => [PostController::class],
            'showLoginForm' => [BaseController::class]
        ],
        [

            'logout' => [SecurityController::class],
            'editProfile' => [UserController::class],


        ],
        [
            'adminPosts' => [AdminController::class],
        ],
        [
            'adminView' => [AdminController::class],
        ]
    ];

    public function renderController()
    {
        foreach ($this->getAllowedRoutes() as $levelRoutes) {
            foreach ($levelRoutes as $method => $controllers) {
                $methodName = $controllers[1] ?? $method;

                if ($this->action !== $methodName) {
                    continue;
                }

                $controller = new $controllers[0]();

                return $controller->$methodName();
            }
        }

        if (Auth::isLogged()) {
            Session::addMessage('Not access that page', 'info');

            return Redirect::to('home');
        }

        return Auth::requireLogin();
    }

    public function getAllowedRoutes(): array
    {
        $roleByLevel = [
            'visitor' => 0,
            'member' => 1,
            'admin' => 2,
            'superuser' => 3
        ];

        $role = $roleByLevel[$_SESSION['role'] ?? $_SESSION['role'] = 'visitor']; // 0, 1, 2

        $allowedRoutes = [];
        foreach (self::ROUTES as $key => $routes) {
            if ($role < $key) {
                continue;
            }

            $allowedRoutes[] = $routes;
        }

        return $allowedRoutes;
    }

    public function getAction()
    {
        if (isset($_GET['action'])) {
            return $_GET['action'];
        }

        return 'home';
    }

}