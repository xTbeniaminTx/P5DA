<?php

namespace app\Twig;

use app\services\UploadFile;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UploadFileExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('is_image', [$this, 'isImage'])
        ];
    }

    public function isImage($file)
    {
        return UploadFile::isImage($file);
    }

}