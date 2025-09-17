<?php
require_once '../src/Config/Database.php';

echo "<h1>Import des données SQL</h1>";

try {
    $pdo = App\Config\Database::getInstance();
    echo "✓ Connexion à la base réussie<br>";

    $sql = file_get_contents('../.sql/ecoride.sql/ecoride.sql');
    if (! $sql) {
        throw new Exception("Fichier SQL introuvable");
    }

    // Supprimer les lignes problématiques
    $sql = preg_replace('/DROP DATABASE.*?;/i', '', $sql);
    $sql = preg_replace('/CREATE DATABASE.*?;/i', '', $sql);
    $sql = preg_replace('/USE `.*?`;/i', '', $sql);

    // Diviser en requêtes
    $queries = array_filter(array_map('trim', explode(';', $sql)));
    echo "Nombre de requêtes trouvées : " . count($queries) . "<br>";

    $pdo->beginTransaction();

    foreach ($queries as $i => $query) {
        if (! empty($query) && ! str_starts_with($query, '--')) {
            try {
                $pdo->exec($query);
                if ($i % 10 == 0) {
                    echo "Requête " . ($i + 1) . " exécutée<br>";
                }

            } catch (Exception $e) {
                echo "Erreur requête $i: " . $e->getMessage() . "<br>";
            }
        }
    }

    $pdo->commit();
    echo "<strong>✓ Import terminé !</strong>";

} catch (Exception $e) {
    if (isset($pdo)) {
        $pdo->rollback();
    }

    echo "✗ Erreur : " . $e->getMessage();
}
