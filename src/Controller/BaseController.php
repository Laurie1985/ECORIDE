<?php
namespace App\Controller;

// Classe abstraite de base pour les contrôleurs
abstract class BaseController
{
    // Méthode pour renvoyer une vue
    public function render(string $view, array $data = []): void
    {
        // Extraction du contenu des données dans des variables
        extract($data);
        ob_start(); // Démarre la mise en tampon de sortie

        // Inclusion de la vue
        require_once __DIR__ . '/../Views/' . $view . '.php';

        $content = ob_get_clean();                     // Récupère le contenu du tampon et nettoie le tampon
        require_once __DIR__ . '/../Views/layout.php'; // Inclusion du layout principal
    }
}
