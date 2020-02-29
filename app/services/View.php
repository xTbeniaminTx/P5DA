<?php

namespace app\services;

use app\models\Manager;
use app\models\Post;
use voku\helper\Paginator;

class View
{
    public static function renderTemplate($name, $data = [])
    {
        echo static::getTemplate($name, $data);

        return true;
    }

    public static function getTemplate($name, $data = [])
    {
        global $twig;
        $view = $twig->load($name);

        return $view->render($data);
    }

}
