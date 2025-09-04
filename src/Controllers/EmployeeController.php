<?php
namespace App\Controllers;

use App\Models\Carpool;
use App\Models\Reservation;
use App\Models\Review;
use App\Models\User;

class EmployeeController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();

        // Vérifier que l'utilisateur est employé
        if (! isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'employee') {
            $_SESSION['error'] = 'Accès non autorisé';
            $this->redirect('/dashboard');
        }
    }

    /**
     * Tableau de bord employé avec compteur de notifications
     */
    public function dashboard()
    {
        // Compter les avis en attente
        $reviewModel         = new Review();
        $pendingReviewsCount = count($reviewModel->getPendingReviews());

        // Compter les plaintes non traitées
        $complaintsCount = Reservation::getComplaintsCount();

        $this->render('employee/dashboard', [
            'title'               => 'Ecoride - Espace Employé',
            'cssFile'             => 'employee',
            'pendingReviewsCount' => $pendingReviewsCount,
            'complaintsCount'     => $complaintsCount,
        ]);
    }

    /**
     * Liste des avis en attente de validation
     */
    public function pendingReviews()
    {
        $reviewModel    = new Review();
        $pendingReviews = $reviewModel->getPendingReviews();

        $this->render('employee/reviews', [
            'title'      => 'Ecoride - Avis en attente',
            'cssFile'    => 'employee',
            'reviews'    => $pendingReviews,
            'csrf_token' => $this->generateCsrfToken(),
        ]);
    }

    /**
     * Approuver un avis
     */
    public function approveReview(string $reviewId)
    {
        if (! $this->validateCsrfToken()) {
            $_SESSION['error'] = 'Token invalide';
            $this->redirect('/employee/reviews');
        }

        $reviewModel = new Review();
        $employeeId  = $_SESSION['user_id'];

        if ($reviewModel->approveReview($reviewId, $employeeId)) {
            $_SESSION['success'] = 'Avis approuvé avec succès';
        } else {
            $_SESSION['error'] = 'Erreur lors de l\'approbation';
        }

        $this->redirect('/employee/reviews');
    }

    /**
     * Rejeter un avis
     */
    public function rejectReview(string $reviewId)
    {
        if (! $this->validateCsrfToken()) {
            $_SESSION['error'] = 'Token invalide';
            $this->redirect('/employee/reviews');
        }

        $reason      = $_POST['reason'] ?? 'Non conforme aux règles';
        $reviewModel = new Review();
        $employeeId  = $_SESSION['user_id'];

        if ($reviewModel->rejectReview($reviewId, $employeeId, $reason)) {
            $_SESSION['success'] = 'Avis rejeté avec succès';
        } else {
            $_SESSION['error'] = 'Erreur lors du rejet';
        }

        $this->redirect('/employee/reviews');
    }

    /**
     * Liste des plaintes
     */
    public function complaints()
    {
        $complaints = Reservation::getComplaints();

        $this->render('employee/complaints', [
            'title'      => 'EcoRide - Plaintes à traiter',
            'cssFile'    => 'employee',
            'complaints' => $complaints,
            'csrf_token' => $this->generateCsrfToken(),
        ]);
    }

    /**
     * Résoudre une plainte
     */
    public function resolveComplaint(int $reservationId)
    {
        if (! $this->validateCsrfToken()) {
            $_SESSION['error'] = 'Token invalide';
            $this->redirect('/employee/complaints');
        }

        $action = $_POST['action'] ?? ''; // 'refund' ou 'pay_driver'
        $notes  = $_POST['notes'] ?? '';

        try {
            $reservation = Reservation::find($reservationId);
            if (! $reservation) {
                $_SESSION['error'] = 'Réservation introuvable';
                $this->redirect('/employee/complaints');
            }

            if ($action === 'refund') {
                // Rembourser le passager, ne pas payer le conducteur
                User::updateCredits($reservation['passenger_id'], $reservation['amount_paid']);

                $_SESSION['success'] = 'Passager remboursé. Plainte résolue.';

            } elseif ($action === 'pay_driver') {
                // Payer le conducteur normalement
                $carpool = Carpool::find($reservation['carpool_id']);
                if ($carpool) {
                    $driverPayment = $reservation['amount_paid'] - 2;
                    User::updateCredits($carpool['driver_id'], $driverPayment);
                }

                $_SESSION['success'] = 'Conducteur payé. Plainte résolue.';
            }

            // Marquer comme résolu
            Reservation::update($reservationId, [
                'status'            => 'completed',
                'complaint_comment' => $reservation['complaint_comment'] . "\n\nRÉSOLU PAR EMPLOYÉ: " . $notes,
            ]);

        } catch (\Exception $e) {
            error_log("Erreur résolution plainte: " . $e->getMessage());
            $_SESSION['error'] = 'Erreur lors de la résolution';
        }

        $this->redirect('/employee/complaints');
    }
}
