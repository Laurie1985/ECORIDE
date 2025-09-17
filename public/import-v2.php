<?php
require_once '../src/Config/Database.php';

echo "<h1>Import SQL v2 - Avec debug détaillé</h1>";

try {
    $pdo = App\Config\Database::getInstance();
    echo "✓ Connexion réussie<br><br>";

    // Lire le fichier
    $sqlFile = '../.sql/ecoride.sql/ecoride.sql';
    if (! file_exists($sqlFile)) {
        throw new Exception("Fichier SQL introuvable : $sqlFile");
    }

    $sql = file_get_contents($sqlFile);
    if (! $sql) {
        throw new Exception("Fichier SQL vide");
    }

    echo "Taille fichier : " . strlen($sql) . " caractères<br>";

    // Nettoyer le SQL
    $sql = preg_replace('/DROP DATABASE.*?;/i', '', $sql);
    $sql = preg_replace('/CREATE DATABASE.*?;/i', '', $sql);
    $sql = preg_replace('/USE `.*?`;/i', '', $sql);

    // Désactiver les contraintes
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO'");

    // Diviser en requêtes plus proprement
    $queries = preg_split('/;\s*$/m', $sql);
    $queries = array_filter(array_map('trim', $queries));

    echo "Requêtes à exécuter : " . count($queries) . "<br><br>";

    $success = 0;
    $errors  = 0;

    foreach ($queries as $i => $query) {
        if (empty($query) || str_starts_with($query, '--')) {
            continue;
        }

        try {
            $result = $pdo->exec($query);
            $success++;

            // Afficher les CREATE TABLE
            if (stripos($query, 'CREATE TABLE') === 0) {
                preg_match('/CREATE TABLE\s+`?(\w+)`?/i', $query, $matches);
                echo "✓ Table créée : " . ($matches[1] ?? 'inconnue') . "<br>";
            }

            // Afficher les INSERT
            if (stripos($query, 'INSERT INTO') === 0) {
                echo "✓ Données insérées (requête " . ($i + 1) . ")<br>";
            }

        } catch (Exception $e) {
            $errors++;
            echo "✗ Erreur requête " . ($i + 1) . ": " . $e->getMessage() . "<br>";
            echo "Query: " . substr($query, 0, 100) . "...<br><br>";
        }
    }

    // Réactiver les contraintes
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    echo "<br><strong>Résumé : $success succès, $errors erreurs</strong><br>";

    // Vérifier les tables créées
    echo "<h2>Vérification finale :</h2>";
    $stmt   = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll();

    if (! empty($tables)) {
        foreach ($tables as $table) {
            echo "- " . $table[0] . "<br>";
        }
    } else {
        echo "Aucune table trouvée après import";
    }

} catch (Exception $e) {
    echo "Erreur générale : " . $e->getMessage();
}
