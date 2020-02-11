<?php

use app\Twig\CSRFTokenExtension;
use app\Twig\RedirectExtension;
use app\Twig\RequestExtension;
use app\Twig\SessionExtension;
use app\Twig\UploadFileExtension;
use HelloNico\Twig\DumpExtension;
use Twig\Environment;
use Twig\Extension\CoreExtension;
use Twig\Loader\FilesystemLoader;

//DB Parameters
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'blogp5da');


//App Root
define('APPROOT', dirname(dirname(__FILE__)));

define('URLROOT', '');

define('SITENAME', 'Blog P5DA');

$loader = new FilesystemLoader(APPROOT . '/views/pages');
$twig = new Environment($loader, [
    'auto_load' => true,
    'debug' => true
]);

$twig->addExtension(new DumpExtension()); //https://github.com/nlemoine/twig-dump-extension
$twig->addExtension(new CSRFTokenExtension());
$twig->addExtension(new RedirectExtension());
$twig->addExtension(new RequestExtension());
$twig->addExtension(new SessionExtension());
$twig->addExtension(new UploadFileExtension());
$twig->getExtension(CoreExtension::class)->setTimezone('Europe/Paris');

/*
git config --global alias.lg "log --color --graph --pretty=format:'%Cred%h%Creset -%C(yellow)%d%Creset %s %Cgreen(%cr) %C(bold blue)<%an>%Creset' --abbrev-commit"
git config --global alias.st "status"
git config --global alias.co "checkout"
*/