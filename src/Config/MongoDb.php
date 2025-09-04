<?php
namespace App\Config;

use App\Config\Config;
use MongoDB\Client;

/**
 * Classe pour gérer la connexion à MongoDB.
 * Utilise le modèle de conception Singleton.
 */
class MongoDb
{
    private static $instance = null;
    private $client;

    /**
     * Constructeur privé pour empêcher l'instanciation directe.
     * Initialise la connexion à MongoDB.
     */
    private function __construct()
    {
        $mongoHost = Config::get('MONGO_HOST', 'localhost');
        $mongoPort = Config::get('MONGO_PORT', '27017');
        $mongoUser = Config::get('MONGO_INITDB_ROOT_USERNAME');
        $mongoPass = Config::get('MONGO_INITDB_ROOT_PASSWORD');

        $connectionString = "mongodb://";
        if (! empty($mongoUser) && ! empty($mongoPass)) {
            $connectionString .= "{$mongoUser}:{$mongoPass}@";
        }
        $connectionString .= "{$mongoHost}:{$mongoPort}";

        $this->client = new Client($connectionString);
    }

    /**
     * Retourne l'instance unique de la classe MongoDb.
     *
     * @return MongoDb
     */
    public static function getInstance(): MongoDb
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Retourne une instance de la base de données.
     */
    public function getDb(string $dbName): \MongoDB\Database
    {
        return $this->client->$dbName;
    }
}
