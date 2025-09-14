<?php
namespace App\Models;

use App\Config\Config;
use App\Config\MongoDb;

class MongoReview
{
    private $collection;

    public function __construct()
    {
        $dbName           = Config::get('MONGO_DATABASE', 'ecoride_mongo');
        $db               = MongoDb::getInstance()->getDb($dbName);
        $this->collection = $db->reviews;
    }

    /**
     * Créer un avis en attente de validation
     */
    public function createPendingReview($carpoolId, $reviewerId, $reviewedUserId, $rating, $comment)
    {
        try {
            $document = [
                'carpool_id'       => (int) $carpoolId,
                'reviewer_id'      => (int) $reviewerId,
                'reviewed_user_id' => (int) $reviewedUserId,
                'rating'           => (int) $rating,
                'comment'          => $comment,
                'status'           => 'pending',
                'created_at'       => new \MongoDB\BSON\UTCDateTime(),
                'moderated_at'     => null,
                'moderated_by'     => null,
                'rejection_reason' => null,
            ];

            $result = $this->collection->insertOne($document);
            return $result->getInsertedId() !== null;
        } catch (\Exception $e) {
            error_log("Erreur création avis MongoDB: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer les avis en attente de validation
     */
    public function getPendingReviews()
    {
        try {
            $reviews = $this->collection->find([
                'status' => 'pending',
            ], [
                'sort' => ['created_at' => -1],
            ])->toArray();

            return $reviews;
        } catch (\Exception $e) {
            error_log("Erreur récupération avis pending: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Approuver un avis
     */
    public function approveReview($reviewId, $employeeId)
    {
        try {
            $result = $this->collection->updateOne(
                ['_id' => new \MongoDB\BSON\ObjectId($reviewId)],
                [
                    '$set' => [
                        'status'       => 'approved',
                        'moderated_at' => new \MongoDB\BSON\UTCDateTime(),
                        'moderated_by' => (int) $employeeId,
                    ],
                ]
            );

            return $result->getModifiedCount() > 0;
        } catch (\Exception $e) {
            error_log("Erreur approbation avis: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Rejeter un avis
     */
    public function rejectReview($reviewId, $employeeId, $reason)
    {
        try {
            $result = $this->collection->updateOne(
                ['_id' => new \MongoDB\BSON\ObjectId($reviewId)],
                [
                    '$set' => [
                        'status'           => 'rejected',
                        'moderated_at'     => new \MongoDB\BSON\UTCDateTime(),
                        'moderated_by'     => (int) $employeeId,
                        'rejection_reason' => $reason,
                    ],
                ]
            );

            return $result->getModifiedCount() > 0;
        } catch (\Exception $e) {
            error_log("Erreur rejet avis: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer les avis approuvés d'un conducteur
     */
    public function getApprovedReviewsForDriver($driverId)
    {
        try {
            $reviews = $this->collection->find([
                'reviewed_user_id' => (int) $driverId,
                'status'           => 'approved',
            ], [
                'sort' => ['created_at' => -1],
            ])->toArray();

            return $reviews;
        } catch (\Exception $e) {
            error_log("Erreur récupération avis conducteur: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Vérifier si un utilisateur a déjà laissé un avis pour un covoiturage
     */
    public function getReviewsForCarpoolAndReviewer($carpoolId, $reviewerId)
    {
        try {
            return $this->collection->find([
                'carpool_id'  => (int) $carpoolId,
                'reviewer_id' => (int) $reviewerId,
            ])->toArray();
        } catch (\Exception $e) {
            error_log("Erreur vérification avis existant: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Statistiques des avis pour l'admin
     */
    public function getReviewsStats()
    {
        try {
            $pipeline = [
                [
                    '$group' => [
                        '_id'   => '$status',
                        'count' => ['$sum' => 1],
                    ],
                ],
            ];

            $stats = $this->collection->aggregate($pipeline)->toArray();

            $result = [
                'pending'  => 0,
                'approved' => 0,
                'rejected' => 0,
                'total'    => 0,
            ];

            foreach ($stats as $stat) {
                $result[$stat['_id']] = $stat['count'];
                $result['total'] += $stat['count'];
            }

            return $result;
        } catch (\Exception $e) {
            error_log("Erreur stats avis: " . $e->getMessage());
            return ['pending' => 0, 'approved' => 0, 'rejected' => 0, 'total' => 0];
        }
    }

    /**
     * Calculer la note moyenne d'un utilisateur
     */
    public function calculateAverageRating($userId)
    {
        try {
            $pipeline = [
                [
                    '$match' => [
                        'reviewed_user_id' => (int) $userId,
                        'status'           => 'approved',
                    ],
                ],
                [
                    '$group' => [
                        '_id'     => null,
                        'average' => ['$avg' => '$rating'],
                        'count'   => ['$sum' => 1],
                    ],
                ],
            ];

            $result = $this->collection->aggregate($pipeline)->toArray();

            if (! empty($result)) {
                return [
                    'average' => round($result[0]['average'], 2),
                    'count'   => $result[0]['count'],
                ];
            }

            return ['average' => 0, 'count' => 0];
        } catch (\Exception $e) {
            error_log("Erreur calcul moyenne: " . $e->getMessage());
            return ['average' => 0, 'count' => 0];
        }
    }
}
