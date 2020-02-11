<?php

namespace app\Twig;

use app\services\CSRFToken;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CSRFTokenExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('get_token', [$this, 'getToken']),
            new TwigFunction('verify_token', [$this, 'verifyToken'])
        ];
    }

    public function getToken()
    {
        return CSRFToken::_token();
    }

    public function verifyToken()
    {
        return CSRFToken::verifyCSRFToken();
    }

}