<?php

namespace app\services;

class View
{

    public static function renderTemplate($name, $data = [])
    {
        global $twig;
        $view = $twig->load($name);
        echo $view->render($data);

        return true;
    }

}