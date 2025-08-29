<?php

use App\Config\Config;
use App\lib\Router;

session_start();

// Chargement des variables d'environnement
Config::load();

// Remonte d'un niveau depuis public/
$rootDir = dirname(__DIR__);

// Chargement de l'autoloader Composer
require_once $rootDir . '/vendor/autoload.php';

// Configuration (fichier, pas une classe)
require_once $rootDir . '/config/database.php';

// Initialisation du routeur
$router = new Router();

// Dispatch de la requête
$router->dispatch();
