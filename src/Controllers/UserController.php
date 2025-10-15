<?php
namespace App\Controllers;

use App\Models\Brand;
use App\Models\Carpool;
use App\Models\DriverPreference;
use App\Models\MongoReview;
use App\Models\Reservation;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use App\Models\Vehicle;

class UserController extends BaseController
{
    /**
     * Tableau de bord utilisateur
     */
    public function dashboard()
    {
        $this->requireAuth();

        $userId   = $_SESSION['user_id'];
        $user     = User::find($userId);
        $userType = $_SESSION['user_type'] ?? 'passenger';

        $myVehicles       = Vehicle::findAllBy(['user_id' => $userId]);
        $myDriverCarpools = Carpool::findAllBy(['driver_id' => $userId]);
        $myReservations   = Reservation::findAllBy(['passenger_id' => $userId]);

        $reviewModel = new MongoReview();
        $ratingData  = $reviewModel->calculateAverageRating($userId);

        $data = [
            'user'               => $user,
            'vehicleCount'       => count($myVehicles),
            'driverCarpoolCount' => count($myDriverCarpools),
            'reservationCount'   => count($myReservations),
            'cssFile'            => 'dashboard',
            'averageRating'      => $ratingData['average'],
            'totalReviews'       => $ratingData['count'],
        ];

        // Choisir la vue selon le type d'utilisateur
        switch ($userType) {
            case 'driver':
                $view          = 'Users/dashboard-driver';
                $data['title'] = 'Ecoride - Espace Conducteur';
                break;
            case 'both':
                $view          = 'Users/dashboard-both';
                $data['title'] = 'Ecoride - Mon espace';
                break;
            case 'passenger':
            default:
                $view          = 'Users/dashboard-passenger';
                $data['title'] = 'Ecoride - Espace Passager';
                break;
        }

        $this->render($view, $data);
    }

    /**
     * Afficher le profil utilisateur
     */
    public function profile()
    {
        $this->requireAuth();

        $userId    = $_SESSION['user_id'];
        $user      = User::find($userId);
        $userRoles = User::getUserRoles($userId);

        $this->render('Users/profile', [
            'title'      => 'Ecoride - Mon profil',
            'cssFile'    => 'profile',
            'csrf_token' => $this->generateCsrfToken(),
            'user'       => $user,
            'userRoles'  => $userRoles,
        ]);
    }

    /**
     * Mettre à jour le profil
     */
    public function updateProfile()
    {
        $this->requireAuth();

        if (! $this->validateCsrfToken()) {
            $this->redirect('/profile');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/profile');
        }

        $userId    = $_SESSION['user_id'];
        $name      = trim($_POST['name'] ?? '');
        $firstname = trim($_POST['firstname'] ?? '');
        $phone     = trim($_POST['phone'] ?? '');
        $address   = trim($_POST['address'] ?? '');

        // Validation
        if (empty($name) || empty($firstname)) {
            $_SESSION['error'] = 'Les nom et prénom sont obligatoires';
            $this->redirect('/profile');
        }

        try {
            $updateData = [
                'name'      => $name,
                'firstname' => $firstname,
                'phone'     => $phone,
                'address'   => $address,
            ];

            // Gestion de la photo de profil
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
                // Validation du fichier
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (in_array($_FILES['photo']['type'], $allowedTypes)) {
                    $updateData['photo'] = file_get_contents($_FILES['photo']['tmp_name']);
                }
            }

            User::update($userId, $updateData);

            // Mise à jour de la session
            $_SESSION['user_name']      = $name;
            $_SESSION['user_firstname'] = $firstname;

            $_SESSION['success'] = 'Profil mis à jour avec succès';

        } catch (\Exception $e) {
            error_log("Erreur de mise à jour du profil: " . $e->getMessage());
            $_SESSION['error'] = 'Erreur lors de la mise à jour';
        }

        $this->redirect('/profile');
    }

    /**
     * Changer le type d'utilisateur (driver/passenger/both)
     */
    public function updateRole()
    {
        $this->requireAuth();

        if (! $this->validateCsrfToken()) {
            $this->redirect('/profile');
        }

        $userType     = $_POST['user_type'] ?? '';
        $allowedTypes = ['passenger', 'driver', 'both'];

        if (! in_array($userType, $allowedTypes)) {
            $_SESSION['error'] = 'Type d\'utilisateur invalide';
            $this->redirect('/profile');
        }

        try {
            $userId = $_SESSION['user_id'];

            // Mise à jour du type dans user_roles
            $roleId = Role::findByName('user')['role_id'];

            if (UserRole::updateUserType($userId, $roleId, $userType)) {
                $_SESSION['user_type'] = $userType;
                $_SESSION['success']   = 'Type d\'utilisateur mis à jour';
            } else {
                $_SESSION['error'] = 'Erreur lors de la mise à jour';
            }

        } catch (\Exception $e) {
            error_log("Erreur updateRole: " . $e->getMessage());
            $_SESSION['error'] = 'Erreur lors de la mise à jour';
        }

        $this->redirect('/profile');
    }

    /**
     * Gérer les véhicules
     */
    public function vehicles()
    {
        $this->requireAuth();

        $userId   = $_SESSION['user_id'];
        $vehicles = Vehicle::findAllBy(['user_id' => $userId]);
        $brands   = Brand::all();

        $this->render('/vehicles', [
            'title'      => 'Ecoride - Mes véhicules',
            'cssFile'    => 'vehicles',
            'csrf_token' => $this->generateCsrfToken(),
            'vehicles'   => $vehicles,
            'brands'     => $brands,
        ]);
    }

    /**
     * Gérer les préférences de conduite
     */
    public function preferences()
    {
        $this->requireAuth();

        $userId      = $_SESSION['user_id'];
        $preferences = DriverPreference::findBy(['user_id' => $userId]);

        $this->render('Users/preferences', [
            'title'       => 'Ecoride - Mes préférences',
            'cssFile'     => 'preferences',
            'csrf_token'  => $this->generateCsrfToken(),
            'preferences' => $preferences,
        ]);
    }

    /**
     * Mettre à jour les préférences
     */
    public function updatePreferences()
    {
        $this->requireAuth();

        if (! $this->validateCsrfToken()) {
            $this->redirect('/preferences');
        }

        $userId = $_SESSION['user_id'];

        try {
            $data = [
                'user_id'                  => $userId,
                'smoking_allowed'          => isset($_POST['smoking_allowed']) ? 1 : 0,
                'animals_allowed'          => isset($_POST['animals_allowed']) ? 1 : 0,
                'personalized_preferences' => $_POST['personalized_preferences'] ?? null,
            ];

            // Vérifier si des préférences existent déjà
            $existing = DriverPreference::findBy(['user_id' => $userId]);

            if ($existing) {
                DriverPreference::update($existing['preference_id'], $data);
            } else {
                DriverPreference::create($data);
            }

            $_SESSION['success'] = 'Préférences mises à jour';

        } catch (\Exception $e) {
            error_log("Erreur préférences: " . $e->getMessage());
            $_SESSION['error'] = 'Erreur lors de la mise à jour';
        }

        $this->redirect('/preferences');
    }

    /**
     * Historique des covoiturages
     */
    public function history()
    {
        $this->requireAuth();

        $userId = $_SESSION['user_id'];

        // Covoiturages en tant que conducteur
        $driverCarpools = Carpool::findAllBy(['driver_id' => $userId]);

        // Covoiturages en tant que passager
        $passengerReservations = Reservation::getPassengerReservations($userId);

        $this->render('Users/history', [
            'title'                 => 'Ecoride - Mon historique',
            'cssFile'               => 'history',
            'driverCarpools'        => $driverCarpools,
            'passengerReservations' => $passengerReservations,
        ]);
    }
}
