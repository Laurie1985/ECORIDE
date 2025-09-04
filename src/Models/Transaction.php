<?php
namespace App\Models;

use App\Config\Database;

class Transaction extends BaseModel
{
    protected static $table      = 'transactions';
    protected static $primaryKey = 'transaction_id';

    /**
     * Transaction quand un passager paie sa réservation
     */
    public static function createPassengerPayment(int $passengerId, int $reservationId, int $amount): int
    {
        return self::create([
            'user_id'          => $passengerId,
            'reservation_id'   => $reservationId,
            'amount'           => -$amount, // Négatif = débit
            'transaction_type' => 'debit',
            'description'      => "Paiement réservation #{$reservationId}",
        ]);
    }

    /**
     * Commission de 2 crédits pour la plateforme
     */
    public static function createPlatformCommission(int $reservationId): int
    {
        return self::create([
            'user_id'          => null, // Plateforme
            'reservation_id'   => $reservationId,
            'amount'           => 2, // Commission fixe
            'transaction_type' => 'credit',
            'description'      => "Commission plateforme - Réservation #{$reservationId}",
        ]);
    }

    /**
     * Paiement du conducteur (montant - commission)
     */
    public static function createDriverPayment(int $driverId, int $reservationId, int $totalAmount): int
    {
        $driverAmount = $totalAmount - 2; // Total - commission

        return self::create([
            'user_id'          => $driverId,
            'reservation_id'   => $reservationId,
            'amount'           => $driverAmount,
            'transaction_type' => 'credit',
            'description'      => "Paiement covoiturage #{$reservationId} (après commission)",
        ]);
    }

    /**
     * Remboursement en cas d'annulation
     */
    public static function createRefund(int $passengerId, int $reservationId, int $amount): int
    {
        return self::create([
            'user_id'          => $passengerId,
            'reservation_id'   => $reservationId,
            'amount'           => $amount, // Positif = crédit
            'transaction_type' => 'credit',
            'description'      => "Remboursement annulation #{$reservationId}",
        ]);
    }

    /**
     * Historique des transactions d'un utilisateur
     */
    public static function getUserHistory(int $userId): array
    {
        return self::findAllBy(['user_id' => $userId]);
    }

    /**
     * Gains totaux de la plateforme
     */
    public static function getPlatformTotalEarnings(): float
    {
        $db   = Database::getInstance();
        $stmt = $db->prepare("
            SELECT SUM(amount) as total
            FROM transactions
            WHERE user_id IS NULL
            AND transaction_type = 'credit'
        ");
        $stmt->execute();
        $result = $stmt->fetch();
        return (float) ($result['total'] ?? 0);
    }

    /**
     * Gains par jour pour les graphiques admin
     */
    public static function getDailyEarnings(int $days = 30): array
    {
        $db   = Database::getInstance();
        $stmt = $db->prepare("
            SELECT
                DATE(created_at) as date,
                SUM(amount) as daily_total
            FROM transactions
            WHERE user_id IS NULL
            AND transaction_type = 'credit'
            AND created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
            GROUP BY DATE(created_at)
            ORDER BY date DESC
        ");
        $stmt->execute([$days]);
        return $stmt->fetchAll();
    }
}
