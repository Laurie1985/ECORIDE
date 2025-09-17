<?php
require_once '../src/Config/Database.php';

try {
    $pdo = App\Config\Database::getInstance();

    echo "<h1>Debug Final - État de la base</h1>";

    // Test basique
    echo "<h2>1. Test de connexion basique</h2>";
    $stmt = $pdo->query("SELECT 1 as test");
    echo "Connexion OK : " . $stmt->fetch()['test'] . "<br><br>";

    // Informations sur la base
    echo "<h2>2. Informations base de données</h2>";
    $stmt   = $pdo->query("SELECT DATABASE() as db_name");
    $dbName = $stmt->fetch()['db_name'];
    echo "Base actuelle : $dbName<br><br>";

    // Tables dans toutes les bases (au cas où)
    echo "<h2>3. Toutes les tables dans cette base</h2>";
    $stmt   = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_NUM);

    if (empty($tables)) {
        echo "AUCUNE table trouvée<br>";

        // Essayer de créer une table simple pour tester
        echo "<h2>4. Test création table simple</h2>";
        try {
            $pdo->exec("CREATE TABLE test_table (id INT PRIMARY KEY, name VARCHAR(50))");
            echo "✓ Table test créée<br>";

            $pdo->exec("INSERT INTO test_table VALUES (1, 'test')");
            echo "✓ Données test insérées<br>";

            $stmt = $pdo->query("SELECT * FROM test_table");
            $data = $stmt->fetch();
            echo "✓ Données récupérées : " . $data['name'] . "<br>";

            $pdo->exec("DROP TABLE test_table");
            echo "✓ Table test supprimée<br>";

        } catch (Exception $e) {
            echo "✗ Erreur test : " . $e->getMessage() . "<br>";
        }

    } else {
        foreach ($tables as $table) {
            $tableName = $table[0];
            echo "- $tableName<br>";

            // Compter les enregistrements
            try {
                $stmt  = $pdo->query("SELECT COUNT(*) as count FROM `$tableName`");
                $count = $stmt->fetch()['count'];
                echo "  → $count enregistrements<br>";
            } catch (Exception $e) {
                echo "  → Erreur comptage: " . $e->getMessage() . "<br>";
            }
        }
    }

} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
