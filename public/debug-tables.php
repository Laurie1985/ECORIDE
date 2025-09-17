<?php
require_once '../src/Config/Database.php';

try {
    $pdo = App\Config\Database::getInstance();

    echo "<h2>Tables existantes dans la base :</h2>";
    $stmt   = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll();

    if (empty($tables)) {
        echo "Aucune table trouvée - l'import n'a pas fonctionné<br>";
    } else {
        foreach ($tables as $table) {
            echo "- " . $table[0] . "<br>";
        }
    }

} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
