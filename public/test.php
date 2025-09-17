<?php
echo "<h1>Test Heroku</h1>";
echo "<p>PHP fonctionne !</p>";

echo "<h2>Variables d'environnement :</h2>";
echo "JAWSDB_URL: " . (isset($_ENV['JAWSDB_URL']) ? "✓ Définie" : "✗ Manquante") . "<br>";
echo "MONGODB_URI: " . (isset($_ENV['MONGODB_URI']) ? "✓ Définie" : "✗ Manquante") . "<br>";

echo "<h2>Test connexion MySQL :</h2>";
try {
    require_once '../src/Config/Database.php';
    $db = App\Config\Database::getInstance();
    echo "✓ Connexion MySQL réussie<br>";
} catch (Exception $e) {
    echo "✗ Erreur MySQL: " . $e->getMessage() . "<br>";
}

echo "<h2>Structure fichiers public/ :</h2>";
echo "<pre>";
print_r(scandir('.'));
echo "</pre>";
