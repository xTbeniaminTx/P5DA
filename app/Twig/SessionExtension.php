<?php

namespace app\Twig;


use app\services\Session;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SessionExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('has_error', [$this, 'hasError'])
        ];
    }

    public function hasError()
    {
        return Session::has('error');
    }

}
