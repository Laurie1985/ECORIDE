<?php
namespace App\Controllers;

// Classe abstraite de base pour les contrôleurs
abstract class BaseController
{
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
}
