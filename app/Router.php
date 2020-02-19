<?php

namespace app;

use app\controllers\AdminController;
use app\controllers\BaseController;
use app\controllers\SecurityController;
use app\controllers\UserController;


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
            'registerUser' => [UserController::class],
            'login' => [SecurityController::class],
            'showLogoutMessage' => [SecurityController::class],
            'forgotPass' => [SecurityController::class],
            'requestReset' => [SecurityController::class],

            'indexAction' => [AdminController::class],
            'showLoginForm' => [BaseController::class]
        ],
        [

            'logout' => [SecurityController::class],
            'profile' => [UserController::class],

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
    }


    public function isLoggedIn()
    {
        if (isset($_SESSION['SESSION_USER_ID'])) {
            return true;
        } else {
            return false;
        }
    }

    public function getAllowedRoutes(): array
    {
        $roleByLevel = [
            'visitor' => 0,
            'member' => 1,
            'admin' => 2,
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
            $act = $_GET['action'];
        } else {
            $act = 'home';
        }
        return $act;
    }

}