<?php
namespace App\Controllers;

use App\Models\MongoReview;
use App\Models\User;

class ReviewController extends BaseController
{
    private $reviewModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->reviewModel = new MongoReview();
    }

    private function sanitizeInput($data)
    {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Afficher les avis approuvés d'un conducteur
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
        $averageRating = 0;

        if ($totalReviews > 0) {
            $totalRatingSum = 0;
            foreach ($reviews as $review) {
                $totalRatingSum += $review['rating'];
            }
            $averageRating = round($totalRatingSum / $totalReviews, 1);
        }

        // Statistiques par note
        $ratingStats = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        foreach ($reviews as $review) {
            $ratingStats[$review['rating']]++;
        }

        // Enrichir les avis avec les noms des passagers
        $enrichedReviews = [];
        foreach ($reviews as $review) {
            $reviewer                = User::find($review['reviewer_id']);
            $review['reviewer_name'] = $reviewer ? $reviewer['username'] : 'Utilisateur inconnu';
            // Convertir la date MongoDB en format lisible
            if (isset($review['created_at']) && $review['created_at'] instanceof \MongoDB\BSON\UTCDateTime) {
                $review['formatted_date'] = $review['created_at']->toDateTime()->format('d/m/Y');
            }
            $enrichedReviews[] = $review;
        }

        $this->render('reviews/driver', [
            'title' => "Ecoride - Avis sur {$driver['username']}",
            'cssFile'       => 'reviews',
            'driver'        => $driver,
            'reviews'       => $enrichedReviews,
            'totalReviews'  => $totalReviews,
            'averageRating' => $averageRating,
            'ratingStats'   => $ratingStats,
        ]);
    }

    /**
     * Avis concernant le conducteur
     */
    public function myReviews()
    {
        $userId  = $_SESSION['user_id'];
        $reviews = $this->reviewModel->getApprovedReviewsForDriver($userId);

        // Enrichir avec les noms des passagers
        $enrichedReviews = [];
        foreach ($reviews as $review) {
            $reviewer                = User::find($review['reviewer_id']);
            $review['reviewer_name'] = $reviewer ? $reviewer['username'] : 'Utilisateur inconnu';
            // Convertir la date MongoDB
            if (isset($review['created_at']) && $review['created_at'] instanceof \MongoDB\BSON\UTCDateTime) {
                $review['formatted_date'] = $review['created_at']->toDateTime()->format('d/m/Y H:i');
            }
            $enrichedReviews[] = $review;
        }

        // Calculer la note moyenne
        $totalReviews  = count($reviews);
        $averageRating = 0;
        if ($totalReviews > 0) {
            $totalRatingSum = array_sum(array_column($reviews, 'rating'));
            $averageRating  = round($totalRatingSum / $totalReviews, 1);
        }

        $this->render('reviews/my_reviews', [
            'title'         => 'Ecoride - Mes avis reçus',
            'cssFile'       => 'reviews',
            'reviews'       => $enrichedReviews,
            'user'          => User::find($userId),
            'totalReviews'  => $totalReviews,
            'averageRating' => $averageRating,
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
            // Enrichir avec les données utilisateur
            $enrichedReviews = [];
            foreach ($reviews as $review) {
                $reviewer                = User::find($review['reviewer_id']);
                $review['reviewer_name'] = $reviewer ? $reviewer['username'] : 'Utilisateur inconnu';
                $enrichedReviews[]       = $review;
            }
            echo json_encode([
                'success' => true,
                'reviews' => $enrichedReviews,
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'error'   => 'Erreur lors de la récupération des avis',
            ]);
        }
    }
}
