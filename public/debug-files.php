<?php
echo "<h1>Structure fichiers sur Heroku</h1>";

$rootDir = dirname(__DIR__);
echo "Root dir: $rootDir<br><br>";

echo "<h2>Contenu racine:</h2>";
if (is_dir($rootDir)) {
    foreach (scandir($rootDir) as $item) {
        if ($item != '.' && $item != '..') {
            echo "$item<br>";
        }
    }
}

echo "<h2>Dossier Views existe?</h2>";
$viewsDir = $rootDir . '/Views';
echo "Views dir: $viewsDir<br>";
echo "Existe: " . (is_dir($viewsDir) ? 'OUI' : 'NON') . "<br>";

if (is_dir($viewsDir)) {
    echo "<h3>Contenu Views:</h3>";
    foreach (scandir($viewsDir) as $item) {
        if ($item != '.' && $item != '..') {
            echo "$item<br>";
        }
    }
}

echo "<h2>Fichier home/index.php recherché:</h2>";
$targetFile = $rootDir . '/Views/home/index.php';
echo "Chemin: $targetFile<br>";
echo "Existe: " . (file_exists($targetFile) ? 'OUI' : 'NON') . "<br>";
