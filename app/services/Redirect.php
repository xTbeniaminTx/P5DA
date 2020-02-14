<?php

namespace app\services;

class Redirect
{

    public static function to($page)
    {
        header("Location: index.php?action=$page", true, 303);
    }

}