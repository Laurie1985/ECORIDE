<?php
namespace App\Controllers;

use App\Models\Carpool;
use App\Models\MongoReview;
use App\Models\Reservation;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserRole;

class AdminController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();

        // Vérifier que l'utilisateur est admin
        if (! isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            $_SESSION['error'] = 'Accès non autorisé';
            $this->redirect('/dashboard');
        }
    }

    /**
     * Tableau de bord administrateur avec statistiques
     */
    public function dashboard()
    {
        // Statistiques générales
        $totalUsers        = count(User::getActiveUsers());
        $totalCarpools     = count(Carpool::all());
        $totalReservations = Reservation::getTotalReservations();
        $platformEarnings  = Transaction::getPlatformTotalEarnings();

        // Statistiques des avis
        $reviewModel  = new MongoReview();
        $reviewsStats = $reviewModel->getReviewsStats();

        // Réservations par statut
        $reservationsByStatus = Reservation::getReservationsByStatus();

        $this->render('Admin/dashboard', [
            'title'                => 'Ecoride - Administration',
            'cssFile'              => 'admin',
            'totalUsers'           => $totalUsers,
            'totalCarpools'        => $totalCarpools,
            'totalReservations'    => $totalReservations,
            'platformEarnings'     => $platformEarnings,
            'reviewsStats'         => $reviewsStats,
            'reservationsByStatus' => $reservationsByStatus,
        ]);
    }

    /**
     * API pour graphique nombre de covoiturages par jour
     */
    public function apiDailyCarpools()
    {
        header('Content-Type: application/json');

        try {
            $dailyCarpools = Carpool::getDailyCarpools(30); // 30 derniers jours
            echo json_encode([
                'success' => true,
                'data'    => $dailyCarpools,
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'error'   => 'Erreur lors de la récupération des données',
            ]);
        }
    }

    /**
     * API pour graphique gains de la plateforme par jour
     */
    public function apiDailyEarnings()
    {
        header('Content-Type: application/json');

        try {
            $dailyEarnings = Transaction::getDailyEarnings(30); // 30 derniers jours
            echo json_encode([
                'success' => true,
                'data'    => $dailyEarnings,
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'error'   => 'Erreur lors de la récupération des gains',
            ]);
        }
    }

    /**
     * Gestion des utilisateurs
     */
    public function users()
    {
        $users = User::all();

        $this->render('Admin/users', [
            'title'      => 'Ecoride - Gestion des utilisateurs',
            'cssFile'    => 'admin',
            'jsFile'     => 'admin-users',
            'users'      => $users,
            'csrf_token' => $this->generateCsrfToken(),
        ]);
    }

    /**
     * Suspendre un utilisateur
     */
    public function suspendUser(int $userId)
    {
        if (! $this->validateCsrfToken()) {
            $_SESSION['error'] = 'Token invalide';
            $this->redirect('/admin/users');
        }

        $user = User::find($userId);
        if (! $user) {
            $_SESSION['error'] = 'Utilisateur introuvable';
            $this->redirect('/admin/users');
        }

        // Empêcher la suspension d'autres admins
        $userRoles = User::getUserRoles($userId);
        foreach ($userRoles as $role) {
            if ($role['role_name'] === 'admin') {
                $_SESSION['error'] = 'Impossible de suspendre un administrateur';
                $this->redirect('/admin/users');
            }
        }

        if (User::updateStatus($userId, 'banned')) {
            $_SESSION['success'] = "L'utilisateur {$user['username']} a été suspendu";
        } else {
            $_SESSION['error'] = 'Erreur lors de la suspension';
        }

        $this->redirect('/admin/users');
    }

    /**
     * Réactiver un utilisateur
     */
    public function activateUser(int $userId)
    {
        if (! $this->validateCsrfToken()) {
            $_SESSION['error'] = 'Token invalide';
            $this->redirect('/admin/users');
        }

        $user = User::find($userId);
        if (! $user) {
            $_SESSION['error'] = 'Utilisateur introuvable';
            $this->redirect('/admin/users');
        }

        if (User::updateStatus($userId, 'active')) {
            $_SESSION['success'] = "L'utilisateur {$user['username']} a été réactivé";
        } else {
            $_SESSION['error'] = 'Erreur lors de la réactivation';
        }

        $this->redirect('/admin/users');
    }

    /**
     * Gestion des employés
     */
    public function employees()
    {
        // Récupérer tous les employés
        $employees = User::getUsersByRole('employee');

        $this->render('Admin/employees', [
            'title'      => 'Ecoride - Gestion des employés',
            'cssFile'    => 'admin',
            'jsFile'     => 'admin-users',
            'employees'  => $employees,
            'csrf_token' => $this->generateCsrfToken(),
        ]);
    }

    /**
     * Formulaire de création d'employé
     */
    public function showCreateEmployee()
    {
        $this->render('Admin/create_employee', [
            'title'      => 'Ecoride - Créer un employé',
            'cssFile'    => 'admin',
            'js'         => 'create-employee',
            'csrf_token' => $this->generateCsrfToken(),
        ]);
    }

    /**
     * Créer un compte employé
     */
    public function createEmployee()
    {
        if (! $this->validateCsrfToken()) {
            $_SESSION['error'] = 'Token invalide';
            $this->redirect('/admin/employees');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/employees');
        }

        $name      = trim($_POST['name'] ?? '');
        $firstname = trim($_POST['firstname'] ?? '');
        $username  = trim($_POST['username'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $password  = $_POST['password'] ?? '';

        // Validation
        $errors = [];

        if (empty($name) || empty($firstname) || empty($username) || empty($email) || empty($password)) {
            $errors[] = 'Tous les champs sont obligatoires';
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email invalide';
        }

        if (strlen($password) < 9) {
            $errors[] = 'Le mot de passe doit contenir au moins 9 caractères';
        }

        // Vérifier que l'adresse mail est unique
        if (User::findByEmail($email)) {
            $errors[] = 'Cette adresse email est déjà utilisée';
        }

        if (User::findByUsername($username)) {
            $errors[] = 'Ce nom d\'utilisateur est déjà pris';
        }

        if (! empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $this->redirect('/admin/employees/create');
        }

        try {
            // Créer le compte employé
            $employeeId = User::create([
                'name'          => $name,
                'firstname'     => $firstname,
                'username'      => $username,
                'email'         => $email,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'credits'       => 0, // Pas de crédits pour un employé
                'status'        => 'active',
            ]);

            // Assigner le rôle employé
            $roleId = Role::findByName('employee')['role_id'];
            UserRole::assignRole($employeeId, $roleId);

            $_SESSION['success'] = 'Compte employé créé avec succès';
            $this->redirect('/admin/employees');

        } catch (\Exception $e) {
            error_log("Erreur création employé: " . $e->getMessage());
            $_SESSION['error'] = 'Erreur lors de la création du compte';
            $this->redirect('/admin/employees/create');
        }
    }

    /**
     * Suspendre un employé
     */
    public function suspendEmployee(int $employeeId)
    {
        if (! $this->validateCsrfToken()) {
            $_SESSION['error'] = 'Token invalide';
            $this->redirect('/admin/employees');
        }

        $employee = User::find($employeeId);
        if (! $employee) {
            $_SESSION['error'] = 'Employé introuvable';
            $this->redirect('/admin/employees');
        }

        if (User::updateStatus($employeeId, 'banned')) {
            $_SESSION['success'] = "Le compte de l'employé {$employee['username']} a été suspendu";
        } else {
            $_SESSION['error'] = 'Erreur lors de la suspension';
        }

        $this->redirect('/admin/employees');
    }

    /**
     * Réactiver un employé
     */
    public function activateEmployee(int $employeeId)
    {
        if (! $this->validateCsrfToken()) {
            $_SESSION['error'] = 'Token invalide';
            $this->redirect('/admin/employees');
        }

        $employee = User::find($employeeId);
        if (! $employee) {
            $_SESSION['error'] = 'Employé introuvable';
            $this->redirect('/admin/employees');
        }

        if (User::updateStatus($employeeId, 'active')) {
            $_SESSION['success'] = "Le compte de l'employé {$employee['username']} a été réactivé";
        } else {
            $_SESSION['error'] = 'Erreur lors de la réactivation';
        }

        $this->redirect('/admin/employees');
    }

    /**
     * Page des statistiques avec graphiques
     */
    public function stats()
    {
        $this->render('Admin/stats', [
            'title'   => 'Ecoride - Statistiques',
            'cssFile' => 'admin',
            'jsFile'  => 'admin-charts',
        ]);
    }

    private function sanitizeInput($data)
    {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
}
