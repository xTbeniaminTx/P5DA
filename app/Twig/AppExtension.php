<?php

namespace app\Twig;

use app\libraries\CSRFToken;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('get_token', [$this, 'getToken']),
        ];
    }

    public function getToken()
    {
        return CSRFToken::_token();
    }
}