<?php
require_once '../src/Config/Database.php';

echo "<h1>Import des données SQL</h1>";

try {
    $pdo = App\Config\Database::getInstance();
    echo "✓ Connexion à la base réussie<br>";

    // Désactiver les contraintes de clés étrangères
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    echo "✓ Contraintes FK désactivées<br>";

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

    $successCount = 0;
    foreach ($queries as $i => $query) {
        if (! empty($query) && ! str_starts_with($query, '--')) {
            try {
                $pdo->exec($query);
                $successCount++;
                if ($i % 5 == 0) {
                    echo "Requête " . ($i + 1) . " exécutée<br>";
                }

            } catch (Exception $e) {
                echo "Erreur requête $i: " . substr($query, 0, 50) . "... → " . $e->getMessage() . "<br>";
            }
        }
    }

    // Réactiver les contraintes de clés étrangères
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "✓ Contraintes FK réactivées<br>";

    $pdo->commit();
    echo "<strong>✓ Import terminé ! $successCount requêtes exécutées avec succès</strong>";

} catch (Exception $e) {
    if (isset($pdo)) {
        $pdo->rollback();
    }

    echo "✗ Erreur générale : " . $e->getMessage();
}
