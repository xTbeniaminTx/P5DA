<?php

namespace app\services;

use app\models\User;

class Auth
{

    public static function auth($user)
    {
        session_regenerate_id(true);
        Session::add('SESSION_USER_ID', $user->id);
        Session::add('SESSION_USER_EMAIL', $user->email);
        Session::add('role', $user->role);
    }

    public static function destroy()
    {
        // Unset all of the session variables.
        $_SESSION = array();

        // If it's desired to kill the session, also delete the session cookie.
        // Note: This will destroy the session, and not just the session data!
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Finally, destroy the session.
        session_destroy();
        Redirect::to('home');
    }


    /**
     * @return mixed
     */
    public static function rememberRequestedPage()
    {
        return $_SESSION['return_to'] = $_SERVER['REQUEST_URI'];
    }

    /**
     * @return mixed|string
     */
    public static function getReturnToPage()
    {
        return $_SESSION['return_to'] ?? 'profile';
    }


    public static function requireLogin()
    {
        if (!Auth::getUser()) {
            Session::addMessage('Please login to acces that page');
            Auth::rememberRequestedPage();
            Redirect::to('index.php?action=login');
        }
    }

    public static function getUser() {
        $user = new User();
        if (isset($_SESSION['SESSION_USER_ID'])) {
            return $user->findById($_SESSION['SESSION_USER_ID']);
        }
        return false;
    }

    public static function isUser() {
        $user = new User();
        if ($user->findByEmail($_POST['email'])) {
            return $user->findByEmail($_POST['email']);
        }
        return false;
    }


}