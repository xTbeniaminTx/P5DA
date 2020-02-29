<?php

namespace app\Twig;

use app\services\UploadFile;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class UploadFileExtension
 *
 * @package app\Twig
 */
class UploadFileExtension extends AbstractExtension
{
    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions()
    {
        return [new TwigFunction('is_image', [$this, 'isImage'])];
    }

    /**
     * @param $file
     *
     * @return bool
     */
    public function isImage($file)
    {
        return UploadFile::isImage($file);
    }
}
