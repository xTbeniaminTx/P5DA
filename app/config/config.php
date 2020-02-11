<?php 

//DB Parameters
use app\Twig\AppExtension;

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'blogp5da');



//App Root
define('APPROOT', dirname(dirname(__FILE__)));

define('URLROOT', '');

define('SITENAME', 'Blog P5DA');

$loader = new \Twig\Loader\FilesystemLoader(APPROOT.'/views/pages');
$twig = new \Twig\Environment($loader, [
    'auto_load' => true,
    'debug' => true
]);
//https://github.com/nlemoine/twig-dump-extension
$twig->addExtension(new \HelloNico\Twig\DumpExtension());
$twig->addExtension(new AppExtension());
$twig->getExtension(\Twig\Extension\CoreExtension::class)->setTimezone('Europe/Paris');

/*
git config --global alias.lg "log --color --graph --pretty=format:'%Cred%h%Creset -%C(yellow)%d%Creset %s %Cgreen(%cr) %C(bold blue)<%an>%Creset' --abbrev-commit"
git config --global alias.st "status"
git config --global alias.co "checkout"
*/