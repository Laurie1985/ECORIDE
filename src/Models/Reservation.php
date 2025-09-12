<?php
namespace App\Models;

use App\Config\Database;

class Reservation extends BaseModel
{
    protected static $table      = 'reservation';
    protected static $primaryKey = 'reservation_id';

    /**
     * Crée une réservation avec validation des crédits
     */
    public static function createWithPayment(int $carpoolId, int $passengerId, int $seatsBooked, int $totalPrice): array
    {
        try {
            $db = Database::getInstance();
            $db->beginTransaction(); //Désactive le mode de validation automatique

            // Vérifier les crédits du passager
            $user = User::find($passengerId);
            if (! $user || $user['credits'] < $totalPrice) {
                $db->rollBack(); // Annule la transaction
                return ['success' => false, 'message' => 'Crédits insuffisants'];
            }

            // Vérifier les places disponibles
            $carpool = Carpool::find($carpoolId);
            if (! $carpool || $carpool['seats_available'] < $seatsBooked) {
                $db->rollBack();
                return ['success' => false, 'message' => 'Places insuffisantes'];
            }

            // Créer la réservation
            $reservationId = self::create([
                'carpool_id'   => $carpoolId,
                'passenger_id' => $passengerId,
                'seats_booked' => $seatsBooked,
                'amount_paid'  => $totalPrice,
                'status'       => 'confirmed',
            ]);

            // Déduire les crédits du passager
            User::updateCredits($passengerId, -$totalPrice);

            // Transaction du passager (débit)
            Transaction::createPassengerPayment($passengerId, $reservationId, $totalPrice);

            // Commission plateforme (2 crédits)
            Transaction::createPlatformCommission($reservationId);

            // Mettre à jour les places disponibles
            Carpool::update($carpoolId, [
                'seats_available' => $carpool['seats_available'] - $seatsBooked,
            ]);

            $db->commit(); // Valide la transaction et réactive le mode de validation automatique
            return ['success' => true, 'reservation_id' => $reservationId, 'message' => 'Réservation confirmée !'];

        } catch (\Exception $e) {
            $db->rollBack();
            error_log("Erreur création réservation: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la réservation'];
        }
    }

    // le passager confirme que le trajet s'est bien passé
    public static function confirmTripByPassenger(int $reservationId, int $passengerId, bool $tripWentWell, string $comment = ''): bool
    {
        try {
            $db = Database::getInstance();
            $db->beginTransaction();

            $reservation = self::find($reservationId);
            if (! $reservation || $reservation['passenger_id'] != $passengerId) {
                $db->rollBack();
                return false;
            }

            if ($reservation['status'] !== 'awaiting_passenger_confirmation') {
                $db->rollBack();
                return false;
            }

            if ($tripWentWell) {
                // Trajet bien passé
                self::update($reservationId, [
                    'status'                 => 'completed',
                    'confirmation_passenger' => 1,
                ]);

                // Payer le conducteur
                $carpool = Carpool::find($reservation['carpool_id']);
                if ($carpool) {
                    $driverPayment = $reservation['amount_paid'] - 2; // Moins la commission
                    User::updateCredits($carpool['driver_id'], $driverPayment);

                    // Transaction pour le conducteur
                    Transaction::createDriverPayment(
                        $carpool['driver_id'],
                        $reservationId,
                        $reservation['amount_paid']
                    );
                }

            } else {
                // Il y a eu un problème
                self::update($reservationId, [
                    'status'                 => 'disputed',
                    'confirmation_passenger' => 0,
                ]);

                // Créer un "ticket" pour l'employé
                self::createComplaintTicket($reservationId, $passengerId, $comment);
            }

            $db->commit();
            return true;

        } catch (\Exception $e) {
            $db->rollBack();
            error_log("Erreur confirmation passager: " . $e->getMessage());
            return false;
        }
    }

//Annulation de réservation par le passager
    public static function cancelByPassenger(int $reservationId, int $passengerId): bool
    {
        try {
            $db = Database::getInstance();
            $db->beginTransaction();

            $reservation = self::find($reservationId);

            if (! $reservation || $reservation['passenger_id'] != $passengerId) {
                $db->rollBack();
                return false;
            }

            if (! $reservation || $reservation['status'] === 'canceled') {
                $db->rollBack();
                return false;
            }

            // Vérifier le statut
            if (in_array($reservation['status'], ['completed', 'disputed', 'canceled'])) {
                $db->rollBack();
                return false;
            }

            // Rembourser le passager
            User::updateCredits($reservation['passenger_id'], $reservation['amount_paid']);

            //Transaction de remboursement
            Transaction::createRefund($reservation['passenger_id'], $reservationId, $reservation['amount_paid']);

            // Remettre les places disponibles
            $carpool = Carpool::find($reservation['carpool_id']);
            if ($carpool) {
                Carpool::update($reservation['carpool_id'], [
                    'seats_available' => $carpool['seats_available'] + $reservation['seats_booked'],
                ]);
            }

            // Modifier le statut de la réservation
            self::update($reservationId, [
                'status'            => 'canceled',
                'cancellation_date' => date('Y-m-d H:i:s'),
            ]);

            $db->commit();
            return true;

        } catch (\Exception $e) {
            $db->rollBack();
            error_log("Erreur annulation réservation: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupèrer les réservations d'un passager avec détails du covoiturage
     */
    public static function getPassengerReservations(int $passengerId): array
    {
        $db   = Database::getInstance();
        $stmt = $db->prepare("
            SELECT r.*,
            c.departure, c.arrival, c.departure_time, c.arrival_time,
            c.status as carpool_status,
            u.username as driver_username, u.phone as driver_phone
            FROM reservation r
            JOIN carpools c ON r.carpool_id = c.carpool_id
            JOIN users u ON c.driver_id = u.user_id
            WHERE r.passenger_id = ?
            ORDER BY c.departure_time DESC
        ");
        $stmt->execute([$passengerId]);
        return $stmt->fetchAll();
    }

    /**
     * Réservations en attente de confirmation (après fin de trajet)
     */
    public static function getAwaitingConfirmation(int $passengerId): array
    {
        $db   = Database::getInstance();
        $stmt = $db->prepare("
            SELECT r.*, c.departure, c.arrival, c.departure_time
            FROM reservation r
            JOIN carpools c ON r.carpool_id = c.carpool_id
            WHERE r.passenger_id = ?
            AND r.status = 'awaiting_passenger_confirmation'
            ORDER BY c.departure_time DESC
        ");
        $stmt->execute([$passengerId]);
        return $stmt->fetchAll();
    }

    /**
     * Récupérer les passagers d'un covoiturage spécifique
     */
    public static function getPassengersByCarpool($carpoolId)
    {
        $db   = Database::getInstance();
        $stmt = $db->prepare("
            SELECT r.reservation_id, r.seats_booked, r.amount_paid, r.status,
                u.email, u.username
            FROM reservation r
            JOIN users u ON r.passenger_id = u.user_id
            WHERE r.carpool_id = ? AND r.status = 'confirmed'
        ");

        $stmt->execute([$carpoolId]);
        return $stmt->fetchAll();
    }

    /**
     * Créer une plainte pour l'employé
     */
    private static function createComplaintTicket(int $reservationId, int $passengerId, string $comment): void
    {
        // Stocker le commentaire directement dans la réservation
        self::update($reservationId, ['complaint_comment' => $comment]);
    }

    /**
     * Récupérer les plaintes pour l'employé
     */
    public static function getComplaints(): array
    {
        $db   = Database::getInstance();
        $stmt = $db->prepare("
            SELECT r.*,
                c.departure, c.arrival, c.departure_time,
                u.username as passenger_username, u.email as passenger_email,
                d.username as driver_username
            FROM reservation r
            JOIN carpools c ON r.carpool_id = c.carpool_id
            JOIN users u ON r.passenger_id = u.user_id
            JOIN users d ON c.driver_id = d.user_id
            WHERE r.status = 'disputed'
            AND r.complaint_comment IS NOT NULL
            ORDER BY r.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Compter les plaintes non traitées pour l'employé
     */
    public static function getComplaintsCount(): int
    {
        $db   = Database::getInstance();
        $stmt = $db->query("
            SELECT COUNT(*) as count
            FROM reservation
            WHERE status = 'disputed'
            AND complaint_comment IS NOT NULL
        ");
        $result = $stmt->fetch();
        return (int) ($result['count'] ?? 0);
    }

    /**
     * Statistiques pour admin
     */
    public static function getTotalReservations(): int
    {
        $db     = Database::getInstance();
        $stmt   = $db->query("SELECT COUNT(*) as total FROM reservation");
        $result = $stmt->fetch();
        return (int) ($result['total'] ?? 0);
    }

    public static function getReservationsByStatus(): array
    {
        $db   = Database::getInstance();
        $stmt = $db->query("
            SELECT status, COUNT(*) as count
            FROM reservation
            GROUP BY status
        ");
        return $stmt->fetchAll();
    }
}
