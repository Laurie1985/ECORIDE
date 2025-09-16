<?php
namespace App\lib;

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
        // ==================== PAGES PRINCIPALES ====================

        // Page d'accueil
        $r->addRoute('GET', '/', [\App\Controllers\HomeController::class, 'index']);
        $r->addRoute('GET', '/contact', [\App\Controllers\HomeController::class, 'contact']);
        $r->addRoute('POST', '/contact', [\App\Controllers\HomeController::class, 'sendContact']);

        // ==================== AUTHENTIFICATION ====================

        $r->addRoute('GET', '/login', [\App\Controllers\AuthController::class, 'showLogin']);
        $r->addRoute('POST', '/login', [\App\Controllers\AuthController::class, 'login']);
        $r->addRoute('GET', '/register', [\App\Controllers\AuthController::class, 'showRegister']);
        $r->addRoute('POST', '/register', [\App\Controllers\AuthController::class, 'register']);
        $r->addRoute('POST', '/logout', [\App\Controllers\AuthController::class, 'logout']);

        // ==================== COVOITURAGES ====================

        // Pages principales
        $r->addRoute('GET', '/carpools', [\App\Controllers\CarpoolController::class, 'index']);
        $r->addRoute('GET', '/carpools/{carpoolId:\d+}', [\App\Controllers\CarpoolController::class, 'show']);

        // API pour JavaScript (recherche et filtres)
        $r->addRoute('GET', '/api/carpools/search', [\App\Controllers\CarpoolController::class, 'apiSearch']);

        // Réservation
        $r->addRoute('POST', '/carpools/{carpoolId:\d+}/book', [\App\Controllers\CarpoolController::class, 'bookCarpool']);

        // ==================== ESPACE UTILISATEUR ====================

        // Tableau de bord principal
        $r->addRoute('GET', '/dashboard', [\App\Controllers\UserController::class, 'dashboard']);

        // Profil utilisateur
        $r->addRoute('GET', '/profile', [\App\Controllers\UserController::class, 'profile']);
        $r->addRoute('POST', '/profile/update', [\App\Controllers\UserController::class, 'updateProfile']);
        $r->addRoute('POST', '/profile/role', [\App\Controllers\UserController::class, 'updateRole']);

        // Historique
        $r->addRoute('GET', '/history', [\App\Controllers\UserController::class, 'history']);

        // Préférences conducteur
        $r->addRoute('GET', '/preferences', [\App\Controllers\UserController::class, 'preferences']);
        $r->addRoute('POST', '/preferences/update', [\App\Controllers\UserController::class, 'updatePreferences']);

        // ==================== VÉHICULES ====================

        $r->addRoute('GET', '/vehicles', [\App\Controllers\VehicleController::class, 'index']);
        $r->addRoute('POST', '/vehicles/create', [\App\Controllers\VehicleController::class, 'create']);
        $r->addRoute('GET', '/vehicles/{vehicleId:\d+}/edit', [\App\Controllers\VehicleController::class, 'showEdit']);
        $r->addRoute('POST', '/vehicles/{vehicleId:\d+}/update', [\App\Controllers\VehicleController::class, 'update']);
        $r->addRoute('POST', '/vehicles/{vehicleId:\d+}/delete', [\App\Controllers\VehicleController::class, 'delete']);

        // ==================== COVOITURAGES CONDUCTEUR ====================

        // Créer un covoiturage
        $r->addRoute('GET', '/carpools/create', [\App\Controllers\CarpoolController::class, 'showCreate']);
        $r->addRoute('POST', '/carpools/create', [\App\Controllers\CarpoolController::class, 'create']);

        // Mes covoiturages conducteur
        $r->addRoute('GET', '/my-carpools', [\App\Controllers\CarpoolController::class, 'myCarpools']);
        $r->addRoute('GET', '/my-carpools/passengers', [\App\Controllers\CarpoolController::class, 'myPassengers']);

        // Gestion des trajets
        $r->addRoute('POST', '/carpools/{carpoolId:\d+}/start', [\App\Controllers\CarpoolController::class, 'startTrip']);
        $r->addRoute('POST', '/carpools/{carpoolId:\d+}/complete', [\App\Controllers\CarpoolController::class, 'completeTrip']);

        // Annulation
        $r->addRoute('POST', '/carpools/{carpoolId:\d+}/cancel', [\App\Controllers\CarpoolController::class, 'cancelCarpool']);

        // ==================== RÉSERVATIONS PASSAGER ====================

        // Mes réservations
        $r->addRoute('GET', '/reservations', [\App\Controllers\ReservationController::class, 'index']);

        // Confirmation après trajet
        $r->addRoute('GET', '/reservations/confirm/{reservationId:\d+}', [\App\Controllers\ReservationController::class, 'showConfirmation']);
        $r->addRoute('POST', '/reservations/confirm/{reservationId:\d+}', [\App\Controllers\ReservationController::class, 'confirmTrip']);

        // Annulation par passager
        $r->addRoute('POST', '/reservations/{reservationId:\d+}/cancel', [\App\Controllers\ReservationController::class, 'cancel']);

        $r->addRoute('GET', '/reservations/{reservationId:\d+}', [\App\Controllers\ReservationController::class, 'show']);

        // ==================== AVIS ====================

        // Voir ses avis reçus en tant que conducteur
        $r->addRoute('GET', '/reviews/about-me', [\App\Controllers\ReviewController::class, 'myReviews']);

        // Voir les avis d'un conducteur pour un passager
        $r->addRoute('GET', '/reviews/driver/{driverId:\d+}', [\App\Controllers\ReviewController::class, 'showDriverReviews']);
        $r->addRoute('GET', '/api/reviews/driver/{driverId:\d+}', [\App\Controllers\ReviewController::class, 'apiDriverReviews']);

        // ==================== ESPACE EMPLOYÉ ====================

        $r->addRoute('GET', '/employee', [\App\Controllers\EmployeeController::class, 'dashboard']);
        $r->addRoute('GET', '/employee/reviews', [\App\Controllers\EmployeeController::class, 'pendingReviews']);
        $r->addRoute('POST', '/employee/reviews/{reviewId}/approve', [\App\Controllers\EmployeeController::class, 'approveReview']);
        $r->addRoute('POST', '/employee/reviews/{reviewId}/reject', [\App\Controllers\EmployeeController::class, 'rejectReview']);
        $r->addRoute('GET', '/employee/complaints', [\App\Controllers\EmployeeController::class, 'complaints']);
        $r->addRoute('POST', '/employee/complaints/{reservationId}/resolve', [\App\Controllers\EmployeeController::class, 'resolveComplaint']);

        // ==================== ESPACE ADMINISTRATEUR ====================

        $r->addRoute('GET', '/admin', [\App\Controllers\AdminController::class, 'dashboard']);
        $r->addRoute('GET', '/admin/dashboard', [\App\Controllers\AdminController::class, 'dashboard']);

        // Gestion des utilisateurs
        $r->addRoute('GET', '/admin/users', [\App\Controllers\AdminController::class, 'users']);
        $r->addRoute('POST', '/admin/users/suspend/{userId:\d+}', [\App\Controllers\AdminController::class, 'suspendUser']);
        $r->addRoute('POST', '/admin/users/activate/{userId:\d+}', [\App\Controllers\AdminController::class, 'activateUser']);

        // Gestion des employés
        $r->addRoute('GET', '/admin/employees', [\App\Controllers\AdminController::class, 'employees']);
        $r->addRoute('GET', '/admin/employees/create', [\App\Controllers\AdminController::class, 'showCreateEmployee']);
        $r->addRoute('POST', '/admin/employees/create', [\App\Controllers\AdminController::class, 'createEmployee']);
        $r->addRoute('POST', '/admin/employees/suspend/{employeeId:\d+}', [\App\Controllers\AdminController::class, 'suspendEmployee']);
        $r->addRoute('POST', '/admin/employees/activate/{employeeId:\d+}', [\App\Controllers\AdminController::class, 'activateEmployee']);

        // Statistiques et graphiques
        $r->addRoute('GET', '/admin/stats', [\App\Controllers\AdminController::class, 'stats']);
        $r->addRoute('GET', '/admin/api/daily-carpools', [\App\Controllers\AdminController::class, 'apiDailyCarpools']);
        $r->addRoute('GET', '/admin/api/daily-earnings', [\App\Controllers\AdminController::class, 'apiDailyEarnings']);
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
            throw new \Exception("Classe du controlleur non trouvée : {$controllerClass}");
        }

        // Instanciation du contrôleur
        $controller = new $controllerClass();

        // Vérification que la méthode existe dans le contrôleur
        if (! method_exists($controller, $method)) {
            throw new \Exception("Méthode {$method} non trouvée dans {$controllerClass}");
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
