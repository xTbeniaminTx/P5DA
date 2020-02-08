<?php

namespace app\Twig;

use app\libraries\CSRFToken;
use app\libraries\Request;
use app\libraries\Session;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('get_token', [$this, 'getToken']),
            new TwigFunction('has_error', [$this, 'hasError']),
            new TwigFunction('request_old', [$this, 'old'])
        ];
    }

    public function getToken()
    {
        return CSRFToken::_token();
    }

    public function hasError()
    {
        return Session::has('error');
    }

    public function old($key, $value)
    {
        return Request::old($key, $value);
    }
}