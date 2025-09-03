<?php
namespace App\Controllers;

use App\Config\Database;
use App\Security\TokenManager;

// Classe abstraite de base pour les contrôleurs
abstract class BaseController
{
    protected $db;
    protected $tokenManager;

    public function __construct()
    {
        $this->db           = Database::getInstance();
        $this->tokenManager = new TokenManager();
    }
    // Méthode pour renvoyer une vue
    public function render(string $view, array $data = []): void
    {
        // Extraction du contenu des données dans des variables
        extract($data);

                                         // Chemin de la vue
        $rootDir  = dirname(__DIR__, 2); // Remonte de deux niveaux depuis src/Controllers/
        $viewFile = $rootDir . '/Views/' . $view . '.php';

        // Vérification que le fichier de la vue existe
        if (! file_exists($viewFile)) {
            throw new \Exception("Vue non trouvée : {$viewFile}");
        }

        ob_start(); // Démarre la mise en tampon de sortie

        // Inclusion de la vue
        require_once $viewFile;

        $content = ob_get_clean(); // Récupère le contenu du tampon et nettoie le tampon de sortie

        // Inclusion du layout principal
        $layoutFile = $rootDir . '/Views/layout.php';
        if (file_exists($layoutFile)) {
            require_once $layoutFile;
        } else {
            echo $content; // Si le layout n'existe pas, affiche juste le contenu
        }
    }
    /**
     * Génère un token CSRF
     */
    protected function generateCsrfToken()
    {
        return $this->tokenManager->generateCsrfToken();
    }
    /**
     * Valide le token CSRF
     */
    protected function validateCsrfToken()
    {
        if (! $this->tokenManager->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de sécurité invalide';
            return false;
        }
        return true;
    }

    /**
     * Redirection vers une URL
     */
    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    /**
     * Vérifie si l'utilisateur est connecté
     */
    protected function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Vérifie si l'utilisateur est admin
     */
    protected function isAdmin(): bool
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    /**
     * Exige que l'utilisateur soit connecté
     */
    protected function requireAuth(): void
    {
        if (! $this->isLoggedIn()) {
            $this->redirect('/login');
        }
    }
}
