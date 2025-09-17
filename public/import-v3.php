<?php
require_once '../src/Config/Database.php';

echo "<h1>Import SQL v3 - Requête par requête</h1>";

try {
    $pdo = App\Config\Database::getInstance();
    echo "✓ Connexion réussie<br><br>";

    // Lire le fichier SQL
    $sql = file_get_contents('../.sql/ecoride.sql/ecoride.sql');
    echo "Taille fichier : " . strlen($sql) . " caractères<br>";

    // Nettoyer le SQL
    $sql = str_replace(["\r\n", "\r"], "\n", $sql);
    $sql = preg_replace('/DROP DATABASE.*?;/i', '', $sql);
    $sql = preg_replace('/CREATE DATABASE.*?;/i', '', $sql);
    $sql = preg_replace('/USE `.*?`;/i', '', $sql);

    // Désactiver contraintes
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO'");

    // Diviser manuellement les principales requêtes CREATE TABLE
    $createTables = [
        "CREATE TABLE IF NOT EXISTS `users`"               => "jusqu'à la fin de cette table",
        "CREATE TABLE IF NOT EXISTS `roles`"               => "jusqu'à la fin",
        "CREATE TABLE IF NOT EXISTS `user_roles`"          => "jusqu'à la fin",
        "CREATE TABLE IF NOT EXISTS `brands`"              => "jusqu'à la fin",
        "CREATE TABLE IF NOT EXISTS `vehicles`"            => "jusqu'à la fin",
        "CREATE TABLE IF NOT EXISTS `drivers_preferences`" => "jusqu'à la fin",
        "CREATE TABLE IF NOT EXISTS `carpools`"            => "jusqu'à la fin",
        "CREATE TABLE IF NOT EXISTS `reservation`"         => "jusqu'à la fin",
        "CREATE TABLE IF NOT EXISTS `transactions`"        => "jusqu'à la fin",
    ];

    // Extraction manuelle des CREATE TABLE
    $queries      = [];
    $lines        = explode("\n", $sql);
    $currentQuery = "";
    $inTable      = false;
    $braceCount   = 0;

    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || str_starts_with($line, '--')) {
            continue;
        }

        if (stripos($line, 'CREATE TABLE') === 0) {
            $inTable      = true;
            $currentQuery = $line . "\n";
            $braceCount   = substr_count($line, '(') - substr_count($line, ')');
        } elseif ($inTable) {
            $currentQuery .= $line . "\n";
            $braceCount += substr_count($line, '(') - substr_count($line, ')');

            if ($braceCount <= 0 && str_ends_with($line, ';')) {
                $queries[]    = trim($currentQuery);
                $inTable      = false;
                $currentQuery = "";
            }
        } elseif (stripos($line, 'INSERT INTO') === 0) {
            $currentQuery = $line;
            if (str_ends_with($line, ';')) {
                $queries[]    = $currentQuery;
                $currentQuery = "";
            }
        } elseif (! empty($currentQuery)) {
            $currentQuery .= " " . $line;
            if (str_ends_with($line, ';')) {
                $queries[]    = trim($currentQuery);
                $currentQuery = "";
            }
        }
    }

    echo "Requêtes extraites : " . count($queries) . "<br><br>";

    // Exécuter chaque requête
    $success = 0;
    foreach ($queries as $i => $query) {
        if (empty($query)) {
            continue;
        }

        try {
            $pdo->exec($query);
            $success++;

            if (stripos($query, 'CREATE TABLE') === 0) {
                preg_match('/CREATE TABLE.*?`(\w+)`/i', $query, $matches);
                $tableName = $matches[1] ?? 'unknown';
                echo "✓ Table créée : $tableName<br>";
            }

            if (stripos($query, 'INSERT INTO') === 0) {
                echo "✓ Données insérées (" . ($i + 1) . ")<br>";
            }

        } catch (Exception $e) {
            echo "✗ Erreur requête " . ($i + 1) . ": " . $e->getMessage() . "<br>";
        }
    }

    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    echo "<br><strong>$success requêtes exécutées</strong><br>";

    // Vérification finale
    echo "<h2>Tables créées :</h2>";
    $stmt   = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll();

    foreach ($tables as $table) {
        echo "- " . $table[0] . "<br>";
    }

} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
