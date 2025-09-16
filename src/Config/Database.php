<?php
namespace App\Config;

use PDO;
use PDOException;

//Création de la classe Database pour la connexion à la base de données
class Database extends PDO// hérite de PDO

{
    // Déclaration d'une variable statique pour stocker l'instance unique de PDO
    private static ?PDO $instance = null;

    // Constructeur privé pour empêcher l'instanciation directe de la classe
    private function __construct()
    {
        //Si Heroku ClearDB existe, on l'utilise
        if (! empty($_ENV['CLEARDB_DATABASE_URL'])) {
            // Mode Heroku : parse l'URL complète
            $url      = parse_url($_ENV['CLEARDB_DATABASE_URL']);
            $dsn      = "mysql:host={$url['host']};dbname=" . ltrim($url['path'], '/');
            $username = $url['user'];
            $password = $url['pass'];
        } else {
                                                                                          // Mode local : utilise vos variables Docker actuelles
            $dsn      = 'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME']; //Connexion à la base de données avec les variables d'environnement
            $username = $_ENV['DB_USER'];
            $password = $_ENV['DB_PASSWORD'];
        }

        parent::__construct($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);   //Appel du constructeur parent de la class pdo avec les informations de connexion
        $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); //Définition du mode de récupération des données par défaut à FETCH_ASSOC (tableau associatif)
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);      //Définition du mode de gestion des erreurs pour lancer des exceptions
    }

    // Méthode statique pour obtenir l'instance unique de PDO en utilisant le pattern Singleton
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            try {
                self::$instance = new self();
            } catch (PDOException $e) {
                die('Erreur de connexion à la base de données: ' . $e->getMessage());
            }
        }
        return self::$instance;
    }

}
