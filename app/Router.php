<?php
session_start();

//Load helpers, librairies and controllers etc
require_once '../vendor/autoload.php';
require_once 'config/config.php';
require_once 'helpers/session_helper.php';
require_once('libraries/Database.php');
require_once('controllers/Controller.php');
require_once('controllers/BaseController.php');
require_once('controllers/AdminController.php');

class Router
{
    //------------------------------------------------------------------------------------------------------------------
    private $request;
    private $error;
    //------------------------------------------------------------------------------------------------------------------
    const ROUTES = [
        [
            '' => [BaseController::class, 'home'],
            "home" => [BaseController::class],
            "contact" => ["controllers" => 'BaseController', "method" => 'contact'],
            "chapters" => ["controllers" => 'BaseController', "method" => 'chapters'],
            "showChapter" => ["controllers" => 'BaseController', "method" => 'showChapter'],
            "editComment" => ["controllers" => 'BaseController', "method" => 'editComment'],
            "bio" => ["controllers" => 'BaseController', "method" => 'bio'],
            "unapprouve" => ["controllers" => 'BaseController', "method" => 'unapprouve'],
            "adminLogin" => ["controllers" => 'BaseController', "method" => 'adminLogin'],
            "sendMail" => ["controllers" => 'BaseController', "method" => 'sendMail'],
        ],
        [

        ],
        [

        ]
    ];
    //------------------------------------------------------------------------------------------------------------------
    private $routesAdmin = [

        "adminView" => ["controllers" => 'AdminController', "method" => 'adminView'],
        "adminComments" => ["controllers" => 'AdminController', "method" => 'adminComments'],
        "approuve" => ["controllers" => 'AdminController', "method" => 'approuve'],
        "adminChapters" => ["controllers" => 'AdminController', "method" => 'adminChapters'],
        "addChapter" => ["controllers" => 'AdminController', "method" => 'addChapter'],
        "editChapter" => ["controllers" => 'AdminController', "method" => 'editChapter'],
        "deleteChapter" => ["controllers" => 'AdminController', "method" => 'deleteChapter'],
        "deleteComment" => ["controllers" => 'AdminController', "method" => 'deleteComment'],
        "logout" => ["controllers" => 'AdminController', "method" => 'logout'],
    ];

    //------------------------------------------------------------------------------------------------------------------
    public function __construct($request)
    {
        $this->request = $request;
    }

    //------------------------------------------------------------------------------------------------------------------
    public function renderController()
    {
        $request = $this->request;

        try {
            if (key_exists($request, $this->routes)) {
                $controller = $this->routes[$request]['controllers'];
                $method = $this->routes[$request]['method'];

                $currentController = new $controller();
                $currentController->$method();
            } elseif (key_exists($request, $this->routesAdmin)) {
                if ($this->isLoggedIn()) {
                    $controller = $this->routesAdmin[$request]['controllers'];
                    $method = $this->routesAdmin[$request]['method'];

                    $currentController = new $controller();
                    $currentController->$method();
                } else {
                    header('Location: index.php?action=adminLogin');
                }
            } else {
                throw new Exception(' 404 aucune page trouvée');
            }
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            echo $this->error;
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
    //------------------------------------------------------------------------------------------------------------------
}