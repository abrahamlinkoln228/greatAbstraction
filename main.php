<?php

require __DIR__ . '/vendor/autoload.php';

//var_dump(__FILE__);

//$dotenv = new \Dotenv\Dotenv(dirname(__FILE__));
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, '.env');
$dotenv->load();

var_dump($_ENV);

//Twig_autoloader::register();

$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader);

$template = $twig->loadTemplate('index.html');

echo $template->render(array());
