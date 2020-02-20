<?php

namespace app\services;

class View
{

    public static function renderTemplate($name, $data = [])
    {
        echo static::getTemplate($name, $data);
    }

    public static function getTemplate($name, $data = [])
    {
        global $twig;
        $view = $twig->load($name);
        return $view->render($data);

    }

}