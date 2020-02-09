<?php

namespace app;

use app\controllers\AdminController;
use app\controllers\BaseController;
use app\controllers\UserController;

session_start();


class Router
{

    //------------------------------------------------------------------------------------------------------------------
    private $request;
    private $error;


    //------------------------------------------------------------------------------------------------------------------
    public function __construct($request)
    {
        $this->request = $request;
        $this->action = $this->getAction();
    }

    //------------------------------------------------------------------------------------------------------------------

    const ROUTES = [
        [
            '' => [BaseController::class, 'home'],
            'contact' => [BaseController::class],
            'chapters' => [BaseController::class],
            'showChapter' => [BaseController::class],
            'editComment' => [BaseController::class],
            'bio' => [BaseController::class],
            'unapprouve' => [BaseController::class],
            'adminLogin' => [BaseController::class],
            'sendMail' => [BaseController::class],

            'showRegisterForm' => [UserController::class],
            'registerUser' => [UserController::class],
            'registerUser2' => [UserController::class],
            'show' => [UserController::class],
            'get' => [UserController::class],
            'login' => [UserController::class],
            'showLoginForm' => [UserController::class]
        ],
        [

            'adminComments' => [AdminController::class],
            'approuve' => [AdminController::class],
            'adminChapters' => [AdminController::class],
            'addChapter' => [AdminController::class],
            'editChapter' => [AdminController::class],
            'deleteChapter' => [AdminController::class],
            'deleteComment' => [AdminController::class],
            'logout' => [AdminController::class],

        ],
        [
            'adminView' => [AdminController::class],
        ]

    ];


    //------------------------------------------------------------------------------------------------------------------
    public function renderController()
    {
        $_SESSION['role'] = 'admin';

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

    //------------------------------------------------------------------------------------------------------------------
    public function isLoggedIn()
    {
        if (isset($_SESSION['admin_id'])) {
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

        $role = $roleByLevel[$_SESSION['role'] ?? 'visitor']; // 0, 1, 2

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
    //------------------------------------------------------------------------------------------------------------------
}