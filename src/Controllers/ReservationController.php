<?php
namespace App\Controllers;

use App\Models\Carpool;
use App\Models\Reservation;
use App\Models\Transaction;
use App\Models\User;

class ReservationController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
    }

    /**
     * Liste des réservations du passager connecté
     */
    public function index()
    {
        $userId       = $_SESSION['user_id'];
        $user         = User::find($userId);
        $reservations = Reservation::getPassengerReservations($userId);

        // Séparer les réservations en attente de confirmation
        $awaitingConfirmation = array_filter($reservations, function ($r) {
            return $r['status'] === 'awaiting_passenger_confirmation';
        });

        $otherReservations = array_filter($reservations, function ($r) {
            return $r['status'] !== 'awaiting_passenger_confirmation';
        });

        $this->render('reservations/index', [
            'title'                => 'Ecoride - Mes réservations',
            'cssFile'              => 'reservations',
            'awaitingConfirmation' => $awaitingConfirmation,
            'reservations'         => $otherReservations,
            'user'                 => $user,
            'csrf_token'           => $this->generateCsrfToken(),
        ]);
    }

    /**
     * Annuler une réservation pour le passager
     */
    public function cancel(int $reservationId)
    {
        if (! $this->validateCsrfToken()) {
            $_SESSION['error'] = 'Token invalide';
            $this->redirect('/reservations');
        }

        $userId = $_SESSION['user_id'];

        try {
            if (Reservation::cancelByPassenger($reservationId, $userId)) {
                $_SESSION['success'] = 'Réservation annulée et remboursée avec succès';
            } else {
                $_SESSION['error'] = 'Impossible d\'annuler cette réservation';
            }
        } catch (\Exception $e) {
            error_log("Erreur annulation réservation: " . $e->getMessage());
            $_SESSION['error'] = 'Erreur lors de l\'annulation';
        }

        $this->redirect('/reservations');
    }

    /**
     * Confirmation du trajet par le passager
     */
    public function confirmTrip(int $reservationId)
    {
        if (! $this->validateCsrfToken()) {
            $_SESSION['error'] = 'Token invalide';
            $this->redirect('/reservations');
        }

        $userId       = $_SESSION['user_id'];
        $tripWentWell = isset($_POST['trip_went_well']) && $_POST['trip_went_well'] === '1';
        $comment      = htmlspecialchars(trim($_POST['comment'] ?? ''), ENT_QUOTES, 'UTF-8');

        // Validation du commentaire si problème signalé
        if (! $tripWentWell && empty($comment)) {
            $_SESSION['error'] = 'Veuillez préciser le problème rencontré';
            $this->redirect('/reservations');
        }

        try {
            if (Reservation::confirmTripByPassenger($reservationId, $userId, $tripWentWell, $comment)) {
                if ($tripWentWell) {
                    $_SESSION['success'] = 'Validation confirmée.';
                } else {
                    $_SESSION['success'] = 'Votre signalement a été transmis à nos équipes.';
                }
            } else {
                $_SESSION['error'] = 'Impossible de valider ce trajet';
            }
        } catch (\Exception $e) {
            error_log("Erreur validation trajet: " . $e->getMessage());
            $_SESSION['error'] = 'Erreur lors de la validation';
        }

        $this->redirect('/reservations');
    }

    /**
     * Afficher les détails d'une réservation
     */
    public function show(int $reservationId)
    {
        $userId = $_SESSION['user_id'];

        // Récupérer la réservation avec détails
        $reservation = Reservation::findBy(['reservation_id' => $reservationId, 'passenger_id' => $userId]);

        if (! $reservation) {
            $_SESSION['error'] = 'Réservation introuvable';
            $this->redirect('/reservations');
        }

        // Récupérer les détails du covoiturage
        $carpool = Carpool::getWithDetails($reservation['carpool_id']);

        if (! $carpool) {
            $_SESSION['error'] = 'Covoiturage introuvable';
            $this->redirect('/reservations');
        }

        $this->redirect('/reservations');
    }

    /**
     * Historique complet des réservations
     */
    public function history()
    {
        $userId       = $_SESSION['user_id'];
        $reservations = Reservation::getPassengerReservations($userId);

        // Grouper par statut pour l'affichage
        $grouped = [];
        foreach ($reservations as $reservation) {
            $status = $reservation['status'];
            if (! isset($grouped[$status])) {
                $grouped[$status] = [];
            }
            $grouped[$status][] = $reservation;
        }

        $this->render('reservations/history', [
            'title'               => 'Ecoride - Historique des réservations',
            'cssFile'             => 'reservations',
            'groupedReservations' => $grouped,
        ]);
    }

    /**
     * API pour obtenir le statut d'une réservation (AJAX)
     */
    public function apiStatus(int $reservationId)
    {
        header('Content-Type: application/json');

        $userId      = $_SESSION['user_id'];
        $reservation = Reservation::findBy(['reservation_id' => $reservationId, 'passenger_id' => $userId]);

        if (! $reservation) {
            echo json_encode(['success' => false, 'error' => 'Réservation introuvable']);
            return;
        }

        echo json_encode([
            'success'      => true,
            'status'       => $reservation['status'],
            'amount_paid'  => $reservation['amount_paid'],
            'seats_booked' => $reservation['seats_booked'],
        ]);
    }

    /**
     * Récapitulatif financier du passager
     */
    public function financialSummary()
    {
        $userId = $_SESSION['user_id'];

        // Récupérer l'utilisateur pour voir ses crédits actuels
        $user = User::find($userId);

        // Historique des transactions
        $transactions = Transaction::getUserHistory($userId);

        // Statistiques des réservations
        $reservations      = Reservation::findAllBy(['passenger_id' => $userId]);
        $totalSpent        = array_sum(array_column($reservations, 'amount_paid'));
        $totalReservations = count($reservations);

        $this->redirect('/reservations');
    }

    private function sanitizeInput($data)
    {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
}
