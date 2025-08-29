<?php
namespace App\lib;

use Exception;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

class Router
{
    private $dispatcher;

    public function __construct()
    {
        // Création du dispatcher FastRoute
        $this->dispatcher = \FastRoute\simpleDispatcher(function (RouteCollector $r) {
            $this->defineRoutes($r);
        });
    }

    private function defineRoutes(RouteCollector $r)
    {
        // Routes principales de l'application

        // Page d'accueil
        $r->addRoute('GET', '/', [\App\Controllers\HomeController::class, 'index']);

        // Authentification
        $r->addRoute('GET', '/login', [\App\Controllers\AuthController::class, 'showLogin']);
        $r->addRoute('POST', '/login', [\App\Controllers\AuthController::class, 'login']);
        $r->addRoute('GET', '/register', [\App\Controllers\AuthController::class, 'showRegister']);
        $r->addRoute('POST', '/register', [\App\Controllers\AuthController::class, 'register']);
        $r->addRoute('POST', '/logout', [\App\Controllers\AuthController::class, 'logout']);

        // Covoiturages
        $r->addRoute('GET', '/carpools', [\App\Controllers\CarpoolController::class, 'index']);
        $r->addRoute('GET', '/carpools/search', [\App\Controllers\CarpoolController::class, 'search']);
        $r->addRoute('GET', '/carpools/create', [\App\Controllers\CarpoolController::class, 'showCreate']);
        $r->addRoute('POST', '/carpools/create', [\App\Controllers\CarpoolController::class, 'create']);
        $r->addRoute('GET', '/carpools/{id:\d+}', [\App\Controllers\CarpoolController::class, 'show']);
        $r->addRoute('POST', '/carpools/{id:\d+}/book', [\App\Controllers\CarpoolController::class, 'book']);

        // Réservations
        $r->addRoute('GET', '/reservations', [\App\Controllers\ReservationController::class, 'index']);
        $r->addRoute('GET', '/reservations/{id:\d+}', [\App\Controllers\ReservationController::class, 'show']);
        $r->addRoute('POST', '/reservations/{id:\d+}/cancel', [\App\Controllers\ReservationController::class, 'cancel']);
        $r->addRoute('POST', '/reservations/{id:\d+}/confirm', [\App\Controllers\ReservationController::class, 'confirm']);

        // Profil utilisateur
        $r->addRoute('GET', '/profile', [\App\Controllers\UserController::class, 'profile']);
        $r->addRoute('POST', '/profile/update', [\App\Controllers\UserController::class, 'updateProfile']);
        $r->addRoute('GET', '/dashboard', [\App\Controllers\UserController::class, 'dashboard']);

        // Véhicules
        $r->addRoute('GET', '/vehicles', [\App\Controllers\VehicleController::class, 'index']);
        $r->addRoute('GET', '/vehicles/add', [\App\Controllers\VehicleController::class, 'showAdd']);
        $r->addRoute('POST', '/vehicles/add', [\App\Controllers\VehicleController::class, 'add']);
        $r->addRoute('DELETE', '/vehicles/{id:\d+}', [\App\Controllers\VehicleController::class, 'delete']);
    }

    public function dispatch()
    {
        // Récupération de l'URL depuis .htaccess
        $url = $_GET['url'] ?? '/';

        // Nettoyage de l'URL
        $url = '/' . trim($url, '/');
        if ($url !== '/') {
            $url = rtrim($url, '/');
        }

        $method = $_SERVER['REQUEST_METHOD'];

        // Dispatch avec FastRoute
        $routeInfo = $this->dispatcher->dispatch($method, $url);

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND: //Si la route n'est pas trouvée
                $this->handleNotFound();    // Affichage d'une page 404
                break;

            case Dispatcher::METHOD_NOT_ALLOWED:          //Si la méthode HTTP n'est pas autorisée pour cette route
                $this->handleMethodNotAllowed($routeInfo[1]); // Affichage d'une erreur 405 avec les méthodes autorisées
                break;

            case Dispatcher::FOUND:                   //Si la route est trouvée
                $handler = $routeInfo[1];                 // Récupération du contrôleur et de la méthode
                $params  = $routeInfo[2];                 // Récupération des paramètres URL de la route
                $this->callController($handler, $params); // Appel du contrôleur avec les paramètres
                break;
        }
    }

    private function callController($handler, $params)
    {
        list($controllerClass, $method) = $handler; // Extraction du contrôleur et de la méthode

        // Vérification que la classe du contrôleur existe
        if (! class_exists($controllerClass)) {
            throw new Exception("Controller class not found: {$controllerClass}");
        }

        // Instanciation du contrôleur
        $controller = new $controllerClass();

        // Vérification que la méthode existe dans le contrôleur
        if (! method_exists($controller, $method)) {
            throw new Exception("Method {$method} not found in {$controllerClass}");
        }

        // Appel de la méthode avec les paramètres
        call_user_func_array([$controller, $method], $params);
    }

    // Gestion des erreurs
    private function handleNotFound()
    {
        http_response_code(404);
        echo "404 - Page non trouvée";
        // Si tempss, créer une vue 404 personnalisée
        // include __DIR__ . '/../views/404.php';
    }

    private function handleMethodNotAllowed($allowedMethods)
    {
        http_response_code(405);
        echo "405 - Méthode non autorisée. Méthodes autorisées : " . implode(', ', $allowedMethods);
    }
}
