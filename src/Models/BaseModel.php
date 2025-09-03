<?php
namespace App\Models;

use App\Config\Database;

abstract class BaseModel
{
    protected static $table;             // Nom de la table (défini dans chaque Model enfant)
    protected static $primaryKey = 'id'; // Clé primaire par défaut

    /**
     * Récupèrer tous les enregistrements
     */
    public static function all()
    {
        $db   = Database::getInstance();
        $stmt = $db->query("SELECT * FROM " . static::$table);
        return $stmt->fetchAll();
    }

    /**
     * Trouver un enregistrement par ID
     */
    public static function find(int $id)
    {
        $db   = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM " . static::$table . " WHERE " . static::$primaryKey . " = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Trouver un enregistrement par critères
     */
    public static function findBy(array $criteres)
    {
        $db         = Database::getInstance();
        $conditions = [];
        $params     = [];

        foreach ($criteres as $column => $value) {
            $conditions[] = "$column = ?";
            $params[]     = $value;
        }

        $stmt = $db->prepare("SELECT * FROM " . static::$table . " WHERE " . implode(' AND ', $conditions));
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Trouver plusieurs enregistrements par critères
     */
    public static function findAllBy(array $criteres)
    {
        $db         = Database::getInstance();
        $conditions = [];
        $params     = [];

        foreach ($criteres as $column => $value) {
            $conditions[] = "$column = ?";
            $params[]     = $value;
        }

        $stmt = $db->prepare("SELECT * FROM " . static::$table . " WHERE " . implode(' AND ', $conditions));
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Créer un nouvel enregistrement
     */
    public static function create(array $data)
    {
        $db           = Database::getInstance();
        $columns      = array_keys($data);                // Extrait les clés du tableau associatif
        $placeholders = array_fill(0, count($data), '?'); // Crée un tableau avec la même valeur

        $stmt = $db->prepare("INSERT INTO " . static::$table . " (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")");
        $stmt->execute(array_values($data));
        return $db->lastInsertId();
    }

    /**
     * Mettre à jour un enregistrement
     */
    public static function update($id, array $data)
    {
        $db      = Database::getInstance();
        $updates = [];
        $params  = [];

        foreach ($data as $column => $value) {
            $updates[] = "$column = ?";
            $params[]  = $value;
        }

        $params[] = $id; // ID à la fin pour le WHERE

        $stmt = $db->prepare("UPDATE " . static::$table . " SET " . implode(', ', $updates) . " WHERE " . static::$primaryKey . " = ?");
        return $stmt->execute($params);
    }

    /**
     * Supprimer un enregistrement
     */
    public static function delete($id)
    {
        $db   = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM " . static::$table . " WHERE " . static::$primaryKey . " = ?");
        return $stmt->execute([$id]);
    }
}
