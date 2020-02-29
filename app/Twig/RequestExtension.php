<?php

namespace app\Twig;


use app\services\Request;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RequestExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('all', [$this, 'requestAll']),
            new TwigFunction('get_key', [$this, 'getKey']),
            new TwigFunction('request_old', [$this, 'getOld']),
            new TwigFunction('refresh', [$this, 'refresh'])
        ];
    }

    public function requestAll()
    {
        return Request::all();
    }

    public function getKey($key)
    {
        return Request::get($key);
    }

    public function getOld($key, $value)
    {
        return Request::old($key, $value);
    }

    public function refresh()
    {
        return Request::refresh();
    }

}
