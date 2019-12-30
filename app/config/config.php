<?php 

//DB Parameters
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'blogp5da');

//define('DB_HOST', 'shareddb-g.hosting.stackcp.net');
//define('DB_USER', 'benjito1983');
//define('DB_PASS', 'rewopi123456');
//define('DB_NAME', 'blog-p4oc-37317b91');


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
$twig->getExtension(\Twig\Extension\CoreExtension::class)->setTimezone('Europe/Paris');

/*
git config --global alias.lg "log --color --graph --pretty=format:'%Cred%h%Creset -%C(yellow)%d%Creset %s %Cgreen(%cr) %C(bold blue)<%an>%Creset' --abbrev-commit"
git config --global alias.st "status"
git config --global alias.co "checkout"
*/