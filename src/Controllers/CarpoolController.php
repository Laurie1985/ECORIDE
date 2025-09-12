<?php
namespace App\Controllers;

use App\Models\Carpool;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Vehicle;

class CarpoolController extends BaseController
{
    /**
     * API endpoint pour la recherche de covoiturages
     * Utilisé par JavaScript avec fetch()
     */
    public function apiSearch()
    {
        header('Content-Type: application/json');

        // Récupérer les paramètres de recherche
        $filters = [
            'departure' => $_GET['departure'] ?? '',
            'arrival'   => $_GET['arrival'] ?? '',
            'date'      => $_GET['date'] ?? '',
        ];

        try {
            // Recherche de base (ville + date)
            $carpools = Carpool::searchForAPI($filters);

            $response = [
                'success'  => true,
                'carpools' => $carpools,
                'count'    => count($carpools),
            ];

            // Si aucun résultat, proposition d'une autre date
            if (empty($carpools) && ! empty($filters['departure']) && ! empty($filters['arrival']) && ! empty($filters['date'])) {
                $suggestedDate = Carpool::suggestAlternativeDate($filters['departure'], $filters['arrival'], $filters['date']);
                if ($suggestedDate) {
                    $response['suggested_date']     = $suggestedDate;
                    $response['suggestion_message'] = "Aucun covoiturage disponible pour cette date. Voulez-vous essayer le {$suggestedDate} ?";
                }
            }

        } catch (\Exception $e) {
            error_log("Erreur API search: " . $e->getMessage());
            $response = [
                'success' => false,
                'error'   => 'Erreur lors de la recherche',
            ];
        }

        echo json_encode($response);
        exit;
    }

    /**
     * Page de recherche principale
     */
    public function index()
    {
        $this->render('carpools/search', [
            'title'   => 'Ecoride - Rechercher un covoiturage',
            'cssFile' => 'carpools',
            'jsFile'  => 'carpools-search',
        ]);
    }

    /**
     * Détails d'un covoiturage
     */
    public function show(int $carpoolId)
    {
        $this->requireAuth();

        $carpool = Carpool::getWithDetails($carpoolId);

        if (! $carpool) {
            $_SESSION['error'] = 'Covoiturage non trouvé';
            $this->redirect('/carpools');
        }

        $this->render('carpools/details', [
            'title'      => 'Ecoride - Détails du covoiturage ',
            'cssFile'    => 'carpool-details',
            'jsFile'     => 'carpool-details',
            'carpool'    => $carpool,
            'csrf_token' => $this->generateCsrfToken(),
        ]);
    }

    /**
     * Réserver une place dans un covoiturage
     */
    public function bookCarpool(int $carpoolId)
    {

        $this->requireAuth();

        if (! $this->validateCsrfToken()) {
            $_SESSION['error'] = 'Token invalide';
            $this->redirect("/carpools/{$carpoolId}");
        }

        $seatsBooked = filter_var($_POST['seats_booked'] ?? 1, FILTER_VALIDATE_INT);
        $confirmed   = isset($_POST['confirmed']) && $_POST['confirmed'] === 'true';
        $userId      = $_SESSION['user_id'];

        if (! $seatsBooked || $seatsBooked < 1) {
            $_SESSION['error'] = 'Nombre de places invalide';
            $this->redirect("/carpools/{$carpoolId}");
        }

        // Récupérer le covoiturage
        $carpool = Carpool::find($carpoolId);
        if (! $carpool) {
            $_SESSION['error'] = 'Covoiturage introuvable';
            $this->redirect('/carpools');
        }

        $totalPrice = $carpool['price_per_seat'] * $seatsBooked;

        // Double confirmation requise

        // Vérifier les crédits avant la confirmation
        if (! $confirmed) {
            $user = User::find($userId);
            if (! $user || $user['credits'] < $totalPrice) {
                $_SESSION['error'] = "Crédits insuffisants. Vous avez {$user['credits']} crédits mais il en faut {$totalPrice}.";
                $this->redirect("/carpools/{$carpoolId}");
            }

            // Stocker les données en session pour la confirmation
            $_SESSION['booking_data'] = [
                'carpool_id'   => $carpoolId,
                'seats_booked' => $seatsBooked,
                'total_price'  => $totalPrice,
            ];

            // Rediriger vers une page de confirmation
            $this->redirect("/carpools/{$carpoolId}/confirm");
        }

        // Créer la réservation après double confirmation
        $result = Reservation::createWithPayment($carpoolId, $userId, $seatsBooked, $totalPrice);

        if ($result['success']) {
            unset($_SESSION['booking_data']);
            $_SESSION['success'] = $result['message'];
            $this->redirect('/reservations');
        } else {
            $_SESSION['error'] = $result['message'];
            $this->redirect("/carpools/{$carpoolId}");
        }
    }

    /**
     * Afficher la page de confirmation de réservation
     */
    public function showConfirmation(int $carpoolId)
    {
        $this->requireAuth();

        if (! isset($_SESSION['booking_data'])) {
            $_SESSION['error'] = 'Aucune réservation en cours';
            $this->redirect("/carpools/{$carpoolId}");
        }

        $bookingData = $_SESSION['booking_data'];
        $carpool     = Carpool::getWithDetails($carpoolId);

        $this->render('carpools/confirm', [
            'title'        => 'Confirmer la réservation',
            'cssFile'      => 'reservations',
            'carpool'      => $carpool,
            'booking_data' => $bookingData,
            'csrf_token'   => $this->generateCsrfToken(),
        ]);
    }

    /**
     * Formulaire de création de covoiturage
     */
    public function showCreate()
    {
        $this->requireAuth();

        // Vérifier que l'utilisateur est conducteur
        if (! isset($_SESSION['user_type']) || ! in_array($_SESSION['user_type'], ['driver', 'both'])) {
            $_SESSION['error'] = 'Vous devez être conducteur pour créer un covoiturage';
            $this->redirect('/profile');
        }

        $userId   = $_SESSION['user_id'];
        $vehicles = Vehicle::getByUserWithBrand($userId);

        if (empty($vehicles)) {
            $_SESSION['error'] = 'Vous devez d\'abord ajouter un véhicule';
            $this->redirect('/vehicles');
        }

        $this->render('carpools/create', [
            'title'      => 'Ecoride - Créer un covoiturage',
            'cssFile'    => 'carpools',
            'vehicles'   => $vehicles,
            'csrf_token' => $this->generateCsrfToken(),
        ]);
    }

    /**
     * Créer un covoiturage
     */
    public function create()
    {
        $this->requireAuth();

        if (! $this->validateCsrfToken()) {
            $_SESSION['error'] = 'Token invalide';
            $this->redirect('/carpools/create');
        }

        $userId         = $_SESSION['user_id'];
        $departure      = htmlspecialchars(trim($_POST['departure'] ?? ''), ENT_QUOTES, 'UTF-8');
        $arrival        = htmlspecialchars(trim($_POST['arrival'] ?? ''), ENT_QUOTES, 'UTF-8');
        $departureTime  = $_POST['departure_time'] ?? '';
        $arrivalTime    = $_POST['arrival_time'] ?? '';
        $vehicleId      = filter_var($_POST['vehicle_id'] ?? null, FILTER_VALIDATE_INT);
        $pricePerSeat   = filter_var($_POST['price_per_seat'] ?? null, FILTER_VALIDATE_FLOAT);
        $seatsAvailable = filter_var($_POST['seats_available'] ?? null, FILTER_VALIDATE_INT);

        // Validation
        if (empty($departure) || empty($arrival) || empty($departureTime) || empty($arrivalTime)) {
            $_SESSION['error'] = 'Tous les champs sont obligatoires';
            $this->redirect('/carpools/create');
        }

        if (! $vehicleId || ! $pricePerSeat || ! $seatsAvailable) {
            $_SESSION['error'] = 'Données invalides';
            $this->redirect('/carpools/create');
        }

        if ($pricePerSeat < 3) { // 2 crédits commission + 1 minimum pour conducteur
            $_SESSION['error'] = 'Le prix minimum est de 3 crédits (commission plateforme incluse)';
            $this->redirect('/carpools/create');
        }

        // Vérifier que le véhicule appartient au conducteur
        $vehicle = Vehicle::find($vehicleId);
        if (! $vehicle || $vehicle['user_id'] != $userId) {
            $_SESSION['error'] = 'Véhicule invalide';
            $this->redirect('/carpools/create');
        }

        try {
            $carpoolId = Carpool::createWithEcologicalCheck([
                'driver_id'       => $userId,
                'vehicle_id'      => $vehicleId,
                'departure'       => $departure,
                'arrival'         => $arrival,
                'departure_time'  => $departureTime,
                'arrival_time'    => $arrivalTime,
                'seats_available' => $seatsAvailable,
                'price_per_seat'  => $pricePerSeat,
                'status'          => 'scheduled',
            ]);

            $_SESSION['success'] = 'Covoiturage créé avec succès !';
            $this->redirect("/carpools/{$carpoolId}");

        } catch (\Exception $e) {
            error_log("Erreur création covoiturage: " . $e->getMessage());
            $_SESSION['error'] = 'Erreur lors de la création';
            $this->redirect('/carpools/create');
        }
    }

    /**
     * Mes covoiturages (conducteur)
     */
    public function myCarpools()
    {
        $this->requireAuth();

        $userId   = $_SESSION['user_id'];
        $carpools = Carpool::findAllBy(['driver_id' => $userId]);

        $this->render('carpools/my_carpools', [
            'title'      => 'Ecoride - Mes covoiturages',
            'cssFile'    => 'carpools',
            'jsFile'     => 'my-carpools',
            'carpools'   => $carpools,
            'csrf_token' => $this->generateCsrfToken(),
        ]);
    }

    /**
     * Voir les passagers de mes covoiturages
     */
    public function myPassengers()
    {
        $this->requireAuth();
        $userId = $_SESSION['user_id'];

        // Récupérer les covoiturages du conducteur avec leurs réservations
        $carpoolsWithPassengers = Carpool::getMyTripsWithPassengers($userId);

        $this->render('carpools/my_passengers', [
            'title'                  => 'Ecoride - Mes passagers',
            'cssFile'                => 'dashboard',
            'carpoolsWithPassengers' => $carpoolsWithPassengers,
        ]);
    }

    /**
     * Démarrer un trajet
     */
    public function startTrip(int $carpoolId)
    {
        $this->requireAuth();

        if (! $this->validateCsrfToken()) {
            $_SESSION['error'] = 'Token invalide';
            $this->redirect('/my-carpools');
        }

        $userId = $_SESSION['user_id'];

        if (Carpool::startTrip($carpoolId, $userId)) {
            // Récupérer les passagers pour l'email
            $passengers = Reservation::getPassengersByCarpool($carpoolId);
            $carpool    = Carpool::find($carpoolId);

            // Envoyer les emails
            $emailService = new \App\Services\EmailService();
            $emailService->sendTripStartedNotification($passengers, $carpool);

            $_SESSION['success'] = 'Trajet démarré !';
        } else {
            $_SESSION['error'] = 'Impossible de démarrer ce trajet';
        }

        $this->redirect('/my-carpools');
    }

    /**
     * Terminer un trajet
     */
    public function completeTrip(int $carpoolId)
    {
        $this->requireAuth();

        if (! $this->validateCsrfToken()) {
            $_SESSION['error'] = 'Token invalide';
            $this->redirect('/my-carpools');
        }

        $userId = $_SESSION['user_id'];

        if (Carpool::completeTrip($carpoolId, $userId)) {
            // Récupérer les passagers pour l'email
            $passengers = Reservation::getPassengersByCarpool($carpoolId);
            $carpool    = Carpool::find($carpoolId);

            // Envoyer les emails de confirmation
            $emailService = new \App\Services\EmailService();
            $emailService->sendTripCompletedNotification($passengers, $carpool);

            $_SESSION['success'] = 'Trajet terminé ! Les passagers ont reçu un mail pour confirmer.';
        } else {
            $_SESSION['error'] = 'Impossible de terminer ce trajet';
        }

        $this->redirect('/my-carpools');
    }

    /**
     * Annuler un covoiturage (conducteur)
     */
    public function cancelCarpool(int $carpoolId)
    {
        $this->requireAuth();

        if (! $this->validateCsrfToken()) {
            $_SESSION['error'] = 'Token invalide';
            $this->redirect('/my-carpools');
        }

        $userId = $_SESSION['user_id'];
        $reason = htmlspecialchars(trim($_POST['reason'] ?? ''), ENT_QUOTES, 'UTF-8');

        // Vérifier que c'est bien le conducteur
        $carpool = Carpool::find($carpoolId);
        if (! $carpool || $carpool['driver_id'] != $userId) {
            $_SESSION['error'] = 'Covoiturage introuvable';
            $this->redirect('/my-carpools');
        }

        try {
            if (Carpool::cancelByDriver($carpoolId, $userId)) {
                $_SESSION['success'] = 'Covoiturage annulé. Les passagers ont été remboursés.';
            } else {
                $_SESSION['error'] = 'Impossible d\'annuler ce covoiturage';
            }
        } catch (\Exception $e) {
            error_log("Erreur annulation covoiturage: " . $e->getMessage());
            $_SESSION['error'] = 'Erreur lors de l\'annulation';
        }

        $this->redirect('/my-carpools');
    }

    private function sanitizeInput($data)
    {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
}
