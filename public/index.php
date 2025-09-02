<?php
session_start();

// Remonte d'un niveau depuis public/
$rootDir = dirname(__DIR__);

// Chargement de l'autoloader Composer
require_once $rootDir . '/vendor/autoload.php';

use App\Config\Config;
use App\lib\Router;

// Chargement des variables d'environnement
Config::load();

// Initialisation du routeur
$router = new Router();

// Dispatch de la requête
$router->dispatch();
