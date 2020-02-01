<?php

namespace app;

use app\controllers\AdminController;
use app\controllers\BaseController;

session_start();

//Load helpers, librairies and controllers etc
require_once 'helpers/session_helper.php';
require_once('libraries/Database.php');


class Router
{

    //------------------------------------------------------------------------------------------------------------------
    private $request;
    private $error;
    private $action;

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
        return $_GET['action'];
    }
    //------------------------------------------------------------------------------------------------------------------
}