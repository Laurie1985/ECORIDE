<?php
namespace App\Models;

use App\Config\Config;
use App\Config\MongoDb;

class Review
{
    private $collection;

    public function __construct()
    {
        // Utilise la classe MongoDb pour obtenir l'instance de la base de données
        $dbName           = Config::get('MONGO_DATABASE', 'ecoride_mongo');
        $db               = MongoDb::getInstance()->getDb($dbName);
        $this->collection = $db->reviews;
    }

    /**
     * Crée un nouvel avis pour un conducteur (en attente de validation par l'employé)
     */
    public function createPendingReview(int $carpoolId, int $reviewerId, int $reviewedUserId, int $rating, string $comment): bool
    {
        try {
            $result = $this->collection->insertOne([
                'carpool_id'       => $carpoolId,
                'reviewer_id'      => $reviewerId,
                'reviewed_user_id' => $reviewedUserId,
                'rating'           => $rating,
                'comment'          => $comment,
                'status'           => 'pending',
                'created_at'       => date('Y-m-d H:i:s'),
                'approved_by'      => null,
                'approved_at'      => null,
                'rejected_by'      => null,
                'rejected_at'      => null,
                'rejection_reason' => null,
            ]);
            return $result->getInsertedCount() === 1;
        } catch (\Exception $e) {
            error_log("Erreur création avis: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer tous les avis en attente pour les employés
     */
    public function getPendingReviews(): array
    {
        try {
            $reviews = $this->collection->find(['status' => 'pending'])->toArray();
            //Transforme en chaine de caractères pour rendre les données MongoDb utilisables
            return array_map(function ($review) {
                $review['_id'] = (string) $review['_id'];
                return $review;
            }, $reviews);
        } catch (\Exception $e) {
            error_log("Erreur récupération avis en attente: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Approuver un avis (employé)
     */
    public function approveReview(string $reviewId, int $employeeId): bool
    {
        try {
            $result = $this->collection->updateOne(
                ['_id' => $reviewId],
                [
                    '$set' => [
                        'status'      => 'approved',
                        'approved_by' => $employeeId,
                        'approved_at' => date('Y-m-d H:i:s'),
                    ],
                ]
            );

            // Mettre à jour la note du conducteur
            if ($result->getModifiedCount() === 1) {
                $this->updateDriverRating($reviewId);
            }

            return $result->getModifiedCount() === 1;
        } catch (\Exception $e) {
            error_log("Erreur approbation avis: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Rejeter un avis (employé)
     */
    public function rejectReview(string $reviewId, int $employeeId, string $reason = ''): bool
    {
        try {
            $result = $this->collection->updateOne(
                ['_id' => $reviewId],
                [
                    '$set' => [
                        'status'           => 'rejected',
                        'rejected_by'      => $employeeId,
                        'rejected_at'      => date('Y-m-d H:i:s'),
                        'rejection_reason' => $reason,
                    ],
                ]
            );
            return $result->getModifiedCount() === 1;
        } catch (\Exception $e) {
            error_log("Erreur rejet avis: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer les avis approuvés d'un conducteur pour affichage
     */
    public function getApprovedReviewsForDriver(int $driverId): array
    {
        try {
            $reviews = $this->collection->find([
                'reviewed_user_id' => $driverId,
                'status'           => 'approved',
            ])->toArray();

            //Transforme en chaine de caractères pour rendre les données MongoDb utilisables
            return array_map(function ($review) {
                $review['_id'] = (string) $review['_id'];
                return $review;
            }, $reviews);
        } catch (\Exception $e) {
            error_log("Erreur récupération avis conducteur: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Vérifier si un utilisateur a déjà laissé un avis pour un covoiturage
     */
    public function getReviewsForCarpoolAndReviewer(int $carpoolId, int $reviewerId): array
    {
        try {
            $reviews = $this->collection->find([
                'carpool_id'  => $carpoolId,
                'reviewer_id' => $reviewerId,
            ])->toArray();

            return $reviews;
        } catch (\Exception $e) {
            error_log("L'avis est déjà existant : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Calculer et mettre à jour la note moyenne d'un conducteur
     */
    private function updateDriverRating(string $reviewId): void
    {
        try {
            // Récupérer l'avis pour connaître le conducteur
            $review = $this->collection->findOne(['_id' => $reviewId]);
            if (! $review) {
                return;
            }

            $driverId = $review['reviewed_user_id'];

            // Récupérer tous les avis approuvés pour ce conducteur
            $approvedReviews = $this->collection->find([
                'reviewed_user_id' => $driverId,
                'status'           => 'approved',
            ])->toArray();

            if (empty($approvedReviews)) {
                return;
            }

            // Calculer la moyenne
            $totalRating = 0;
            foreach ($approvedReviews as $reviewData) {
                $totalRating += $reviewData['rating'];
            }

            $averageRating = round($totalRating / count($approvedReviews), 1);

            // Mettre à jour la note en Database
            User::update($driverId, ['rating' => $averageRating]);

        } catch (\Exception $e) {
            error_log("Erreur mise à jour note conducteur: " . $e->getMessage());
        }
    }

    /**
     * Récupérer un avis par ID avec détails utilisateur
     */
    public function getReviewWithUserDetails(string $reviewId): ?array
    {
        try {
            $review = $this->collection->findOne(['_id' => $reviewId]);
            if (! $review) {
                return null;
            }

            // Récupérer les détails des utilisateurs depuis MySQL
            $reviewer     = User::find($review['reviewer_id']);
            $reviewedUser = User::find($review['reviewed_user_id']);

            return [
                '_id'           => (string) $review['_id'],
                'carpool_id'    => $review['carpool_id'],
                'rating'        => $review['rating'],
                'comment'       => $review['comment'],
                'status'        => $review['status'],
                'created_at'    => $review['created_at'],
                'reviewer'      => $reviewer ? [
                    'username' => $reviewer['username'],
                ] : null,
                'reviewed_user' => $reviewedUser ? [
                    'username' => $reviewedUser['username'],
                ] : null,
            ];

        } catch (\Exception $e) {
            error_log("Erreur récupération détails avis: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Statistiques pour l'admin
     */
    public function getReviewsStats(): array
    {
        try {
            $pending  = $this->collection->countDocuments(['status' => 'pending']);
            $approved = $this->collection->countDocuments(['status' => 'approved']);
            $rejected = $this->collection->countDocuments(['status' => 'rejected']);

            return [
                'pending'  => $pending,
                'approved' => $approved,
                'rejected' => $rejected,
            ];

        } catch (\Exception $e) {
            error_log("Erreur statistiques avis: " . $e->getMessage());
            return ['pending' => 0, 'approved' => 0, 'rejected' => 0];
        }
    }
}
