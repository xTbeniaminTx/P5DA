<?php

use app\services\Auth;
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

ini_set('session.cookie_lifetime', '864000'); //ten days in seconds


error_reporting(E_ALL);
//set_error_handler(Error::class);
//set_exception_handler(Exception::class);


session_start();


$loader = new FilesystemLoader(APPROOT . '/views/pages');
$twig = new Environment($loader, [
    'auto_load' => true,
    'debug' => true
]);

$twig->addGlobal('current_user', Auth::getUser());
$twig->addGlobal('flash_messages', \app\services\Session::getMessages());
$twig->addExtension(new DumpExtension()); //https://github.com/nlemoine/twig-dump-extension
$twig->addExtension(new CSRFTokenExtension());
$twig->addExtension(new RedirectExtension());
$twig->addExtension(new RequestExtension());
$twig->addExtension(new SessionExtension());
$twig->addExtension(new UploadFileExtension());
$twig->getExtension(CoreExtension::class)->setTimezone('Europe/Paris');

const SENDGRID_API_KEY = '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';

const SECRET_KEY = '0c?>x(A#RNv)i(%"kT<w$UEV>`uK(N';

const MAILGUN_API_KEY = '58c3857dff81fe22fb83c0dfeb969b1f-52b6835e-00ee31b6';
const MAILGUN_API_DOMAIN = 'https://api.mailgun.net/v3/sandbox59b38d41a61d40eea897194f451ff653.mailgun.org';


/*
git config --global alias.lg "log --color --graph --pretty=format:'%Cred%h%Creset -%C(yellow)%d%Creset %s %Cgreen(%cr) %C(bold blue)<%an>%Creset' --abbrev-commit"
git config --global alias.st "status"
git config --global alias.co "checkout"
*/