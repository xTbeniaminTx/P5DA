<?php

namespace app\libraries;

class Redirect
{

    public static function to($page)
    {
        header("Location: index.php?action=$page");
    }

}