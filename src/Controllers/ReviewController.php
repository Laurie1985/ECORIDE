<?php
namespace App\Controllers;

use App\Models\Carpool;
use App\Models\Reservation;
use App\Models\Review;
use App\Models\User;

class ReviewController extends BaseController
{
    private $reviewModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->reviewModel = new Review();
    }

    /**
     * US 11: Afficher le formulaire pour laisser un avis après un trajet
     */
    public function showCreate(int $carpoolId)
    {
        $userId = $_SESSION['user_id'];

        // Vérifier que l'utilisateur a bien participé à ce covoiturage
        $reservation = Reservation::findBy([
            'carpool_id'   => $carpoolId,
            'passenger_id' => $userId,
            'status'       => 'completed',
        ]);

        if (! $reservation) {
            $_SESSION['error'] = 'Vous ne pouvez laisser un avis que pour vos trajets terminés';
            $this->redirect('/history');
        }

        // Récupérer les détails du covoiturage
        $carpool = Carpool::getWithDetails($carpoolId);
        if (! $carpool) {
            $_SESSION['error'] = 'Covoiturage introuvable';
            $this->redirect('/history');
        }

        $this->render('reviews/create', [
            'title'       => 'Laisser un avis - EcoRide',
            'cssFile'     => 'reviews',
            'carpool'     => $carpool,
            'reservation' => $reservation,
            'csrf_token'  => $this->generateCsrfToken(),
        ]);
    }

    private function sanitizeInput($data)
    {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    /**
     * US 11: Créer un avis (sera en attente de validation employé)
     */
    public function create()
    {
        if (! $this->validateCsrfToken()) {
            $_SESSION['error'] = 'Token invalide';
            $this->redirect('/history');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/history');
        }

        $carpoolId = filter_var($_POST['carpool_id'] ?? null, FILTER_VALIDATE_INT);
        $rating    = filter_var($_POST['rating'] ?? null, FILTER_VALIDATE_INT);
        $comment   = $this->sanitizeInput($_POST['comment'] ?? '');
        $userId    = $_SESSION['user_id'];

        // Validation
        if (! $carpoolId || $rating === false || $rating < 1 || $rating > 5) {
            $_SESSION['error'] = 'Données invalides';
            $this->redirect('/history');
        }

        if (empty($comment) || strlen($comment) < 10) {
            $_SESSION['error'] = 'Le commentaire doit contenir au moins 10 caractères';
            $this->redirect("/reviews/create/{$carpoolId}");
        }

        if (strlen($comment) > 1000) {
            $_SESSION['error'] = 'Le commentaire ne peut pas dépasser 1000 caractères';
            $this->redirect("/reviews/create/{$carpoolId}");
        }

        // Vérifier que l'utilisateur peut laisser cet avis
        $reservation = Reservation::findBy([
            'carpool_id'   => $carpoolId,
            'passenger_id' => $userId,
            'status'       => 'completed',
        ]);

        if (! $reservation) {
            $_SESSION['error'] = 'Vous ne pouvez pas laisser d\'avis pour ce trajet';
            $this->redirect('/history');
        }

        // Récupérer l'ID du conducteur
        $carpool = Carpool::find($carpoolId);
        if (! $carpool) {
            $_SESSION['error'] = 'Covoiturage introuvable';
            $this->redirect('/history');
        }

        // Vérifier qu'il n'a pas déjà laissé un avis pour ce trajet
        $existingReviews = $this->reviewModel->getReviewsForCarpoolAndReviewer($carpoolId, $userId);
        if (! empty($existingReviews)) {
            $_SESSION['error'] = 'Vous avez déjà laissé un avis pour ce trajet';
            $this->redirect('/history');
        }

        // Créer l'avis (en attente de validation)
        $success = $this->reviewModel->createPendingReview(
            $carpoolId,
            $userId,
            $carpool['driver_id'],
            $rating,
            $comment
        );

        if ($success) {
            $_SESSION['success'] = 'Votre avis a été soumis et sera visible après validation par nos équipes';
        } else {
            $_SESSION['error'] = 'Erreur lors de l\'enregistrement de votre avis';
        }

        $this->redirect('/history');
    }

    /**
     * US 5: Afficher les avis approuvés d'un conducteur
     */
    public function showDriverReviews(int $driverId)
    {
        // Récupérer les informations du conducteur
        $driver = User::find($driverId);
        if (! $driver) {
            $_SESSION['error'] = 'Conducteur introuvable';
            $this->redirect('/carpools');
        }

        // Récupérer les avis approuvés
        $reviews = $this->reviewModel->getApprovedReviewsForDriver($driverId);

        // Calculer les statistiques
        $totalReviews  = count($reviews);
        $averageRating = $driver['rating'];

        $ratingStats = [
            5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0,
        ];

        foreach ($reviews as $review) {
            $ratingStats[$review['rating']]++;
        }

        $this->render('reviews/driver', [
            'title'         => "Avis sur {$driver['username']} - EcoRide",
            'cssFile'       => 'reviews',
            'driver'        => $driver,
            'reviews'       => $reviews,
            'totalReviews'  => $totalReviews,
            'averageRating' => $averageRating,
            'ratingStats'   => $ratingStats,
        ]);
    }

    /**
     * API pour récupérer les avis d'un conducteur (pour AJAX)
     */
    public function apiDriverReviews(int $driverId)
    {
        header('Content-Type: application/json');

        try {
            $reviews = $this->reviewModel->getApprovedReviewsForDriver($driverId);
            echo json_encode([
                'success' => true,
                'reviews' => $reviews,
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'error'   => 'Erreur lors de la récupération des avis',
            ]);
        }
    }
}
