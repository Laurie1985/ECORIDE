<?php
namespace App\Controllers;

use App\Models\User;

class AuthController extends BaseController
{
    /**
     * Affiche le formulaire de connexion
     */
    public function showLogin()
    {
        // Si déjà connecté, redirige vers dashboard
        if ($this->isLoggedIn()) {
            $this->redirect('/dashboard');
        }

        $this->render('auth/login', [
            'title'      => 'Ecoride - Connexion',
            'cssFile'    => 'auth',
            'csrf_token' => $this->generateCsrfToken(),
        ]);
    }

    /**
     * Traite le connexion
     */
    public function login()
    {
        if (! $this->validateCsrfToken()) {
            $this->redirect('/login');
        }

        // Vérifie que la methode est POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }

        $login    = trim($_POST['login'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validation des champs
        if (empty($login) || empty($password)) {
            $_SESSION['error'] = 'Veuillez remplir tous les champs';
            $this->redirect('/login');
        }

        // Vérification des identifiants : essaye email puis username
        try {
            $user = User::findByEmail($login);
            if (! $user) {
                $user = User::findByUsername($login);
            }

            if ($user && password_verify($password, $user['password_hash'])) {
                if ($user['status'] === 'banned') {
                    $_SESSION['error'] = 'Votre compte a été suspendu';
                    $this->redirect('/login');
                }

                // Connexion réussie
                $_SESSION['user_id']    = $user['user_id'];
                $_SESSION['username']   = $user['username'];
                $_SESSION['user_email'] = $user['email'];

                // Récupérer le rôle de l'utilisateur
                $this->setUserRole($user['user_id']);

                $_SESSION['success'] = 'Connexion réussie !';
                // Redirection selon le rôle
                $this->redirectByRole();
            } else {
                $_SESSION['error'] = 'Identifiant ou mot de passe incorrect';
                $this->redirect('/login');
            }
        } catch (\Exception $e) {
            error_log("Erreur login: " . $e->getMessage());
            $_SESSION['error'] = 'Erreur lors de la connexion';
            $this->redirect('/login');
        }
    }

    /**
     * Affiche le formulaire d'inscription
     */
    public function showRegister()
    {
        if ($this->isLoggedIn()) {
            $this->redirect('/dashboard');
        }

        $this->render('auth/register', [
            'title'      => 'Ecoride - Inscription',
            'cssFile'    => 'auth',
            'jsFile'     => 'auth',
            'csrf_token' => $this->generateCsrfToken(),
        ]);
    }

    /**
     * Traite l'inscription
     */
    public function register()
    {
        if (! $this->validateCsrfToken()) {
            $this->redirect('/register');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/register');
        }

        $name            = trim($_POST['name'] ?? '');
        $firstname       = trim($_POST['firstname'] ?? '');
        $username        = trim($_POST['username'] ?? '');
        $email           = trim($_POST['email'] ?? '');
        $password        = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validation des champs
        $errors = [];

        if (empty($name) || empty($firstname) || empty($username) || empty($email) || empty($password)) {
            $errors[] = 'Tous les champs sont obligatoires';
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email invalide';
        }

        // Validation mot de passe sécurisé
        if (strlen($password) < 9) {
            $errors[] = 'Le mot de passe doit contenir au moins 9 caractères';
        }

        if (! preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins une majuscule';
        }

        if (! preg_match('/[a-z]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins une minuscule';
        }

        if (! preg_match('/[0-9]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins un chiffre';
        }

        if ($password !== $confirmPassword) {
            $errors[] = 'Les mots de passe ne correspondent pas';
        }

        if (! empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $this->redirect('/register');
        }

        try {
            // Vérifier si email ou username déjà utilisés
            $existingEmail = User::findByEmail($email);
            if ($existingEmail !== null) {
                $_SESSION['error'] = 'Cette adresse email est déjà utilisée';
                $this->redirect('/register');
            }

            $existingUsername = User::findByUsername($username);
            if ($existingUsername !== null) {
                $_SESSION['error'] = 'Ce nom d\'utilisateur est déjà pris';
                $this->redirect('/register');
            }

            // Création du compte avec 20 crédits
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $userId = User::create([
                'name'          => $name,
                'firstname'     => $firstname,
                'username'      => $username,
                'email'         => $email,
                'password_hash' => $passwordHash,
                'credits'       => 20,
                'created_at'    => date('Y-m-d H:i:s'),
            ]);

            // Attribution du rôle 'user' par défaut avec type 'passenger'
            User::assignUserRole($userId, 'user', 'passenger');

            $_SESSION['success'] = 'Compte créé avec succès ! Vous pouvez vous connecter.';
            $this->redirect('/login');

        } catch (\Exception $e) {
            error_log("Erreur inscription: " . $e->getMessage());
            $_SESSION['error'] = 'Erreur lors de la création du compte';
            $this->redirect('/register');
        }
    }

    /**
     * Déconnexion
     */
    public function logout()
    {
        session_destroy();
        $this->redirect('/');
    }

    /**
     * Définit le rôle de l'utilisateur en session
     */
    private function setUserRole($userId)
    {
        try {
            $roles = User::getUserRoles($userId);

                                                  // Définir le rôle principal (admin > employee > user)
            $_SESSION['user_role'] = 'user';      // Par défaut
            $_SESSION['user_type'] = 'passenger'; // Par défaut

            foreach ($roles as $role) {
                if ($role['role_name'] === 'admin') {
                    $_SESSION['user_role'] = 'admin';
                    break;
                } elseif ($role['role_name'] === 'employee') {
                    $_SESSION['user_role'] = 'employee';
                    break;
                }

                // Si c'est un rôle 'user', récupérer le type (driver/passenger/both)
                if ($role['role_name'] === 'user') {
                    $_SESSION['user_type'] = $role['user_role'] ?? 'passenger';
                }
            }
        } catch (\Exception $e) {
            error_log("Erreur récupération rôles: " . $e->getMessage());
            $_SESSION['user_role'] = 'user';
            $_SESSION['user_type'] = 'passenger';
        }
    }

    /**
     * Redirige l'utilisateur selon son rôle
     */
    private function redirectByRole()
    {
        $userRole = $_SESSION['user_role'] ?? 'user';

        switch ($userRole) {
            case 'admin':
                $this->redirect('/admin/dashboard');
                break;

            case 'employee':
                $this->redirect('/employee');
                break;

            case 'user':
            default:
                $this->redirect('/dashboard');
                break;
        }
    }
}
