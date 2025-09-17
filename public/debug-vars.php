<?php
echo "<h1>Debug Variables Heroku</h1>";

echo "<h2>JAWSDB variables:</h2>";
echo "JAWSDB_URL isset: " . (isset($_ENV['JAWSDB_URL']) ? 'true' : 'false') . "<br>";
echo "JAWSDB_URL empty: " . (empty($_ENV['JAWSDB_URL']) ? 'true' : 'false') . "<br>";
echo "JAWSDB_URL value: " . ($_ENV['JAWSDB_URL'] ?? 'NOT_SET') . "<br><br>";

echo "<h2>Toutes les variables ENV contenant JAWSDB:</h2>";
foreach ($_ENV as $key => $value) {
    if (stripos($key, 'JAWSDB') !== false) {
        echo "$key = " . substr($value, 0, 50) . "...<br>";
    }
}

echo "<h2>Test condition Database.php:</h2>";
if (! empty($_ENV['JAWSDB_URL'])) {
    echo "✓ Condition JAWSDB_URL = TRUE<br>";
    $url = parse_url($_ENV['JAWSDB_URL']);
    echo "Parse réussie: host=" . ($url['host'] ?? 'ERROR');
} else {
    echo "✗ Condition JAWSDB_URL = FALSE - utilise variables locales<br>";
}
