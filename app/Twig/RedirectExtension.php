<?php

namespace app\Twig;

use app\services\Redirect;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RedirectExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('redirect_to', [$this, 'redirectTo'])
        ];
    }

    public function redirectTo()
    {
        return Redirect::to();
    }

}
