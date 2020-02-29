<?php

namespace app\services;

class Redirect
{

    public static function to($page)
    {
        if ($_SESSION['return_to']) {
            unset($_SESSION['return_to']);

            return header("Location: $page", true, 303);
        }

        return header("Location: index.php?action=$page", true, 303);
    }

}
