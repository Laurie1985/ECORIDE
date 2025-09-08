<?php
namespace App\Models;

use App\Config\Database;

class User extends BaseModel
{
    // Le nom de la table
    protected static $table = 'users';
    // La clé primaire de la table
    protected static $primaryKey = 'user_id';

    /**
     * Trouver un utilisateur dans la base de données par son adresse e-mail.
     *
     * @param string $email L'adresse e-mail de l'utilisateur à trouver.
     * @return array|false Retourne un tableau associatif si l'utilisateur est trouvé, sinon false.
     */
    public static function findByEmail(string $email): ?array
    {
        $result = self::findBy(['email' => $email]);
        return $result ?: null;
    }

    /**
     * Trouver un utilisateur par son username
     */
    public static function findByUsername(string $username): ?array
    {
        $result = self::findBy(['username' => $username]);
        return $result ?: null;
    }

    /**
     * Récupérer les utilisateurs par rôle (pour Admin)
     */
    public static function getUsersByRole(string $roleName): array
    {
        $db   = Database::getInstance();
        $stmt = $db->prepare("
            SELECT u.*
            FROM users u
            JOIN user_roles ur ON u.user_id = ur.user_id
            JOIN roles r ON ur.role_id = r.role_id
            WHERE r.role_name = ? AND u.status = 'active'
        ");
        $stmt->execute([$roleName]);
        return $stmt->fetchAll();
    }

    /**
     * Récupère les rôles d'un utilisateur
     */
    public static function getUserRoles(int $userId): array
    {
        $db   = Database::getInstance();
        $stmt = $db->prepare("
            SELECT r.role_name, ur.user_role
            FROM user_roles ur
            JOIN roles r ON ur.role_id = r.role_id
            WHERE ur.user_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Assigne un rôle à un utilisateur
     */
    public static function assignUserRole(int $userId, string $roleName, string $userType = 'passenger'): bool
    {
        try {
            $db = Database::getInstance();

            // Récupérer l'ID du rôle
            $stmt = $db->prepare("SELECT role_id FROM roles WHERE role_name = ?");
            $stmt->execute([$roleName]);
            $role = $stmt->fetch();

            if (! $role) {
                return false;
            }

            // Insérer dans user_roles
            $stmt = $db->prepare("
                INSERT INTO user_roles (user_id, role_id, user_role)
                VALUES (?, ?, ?)
            ");

            return $stmt->execute([$userId, $role['role_id'], $userType]);

        } catch (\Exception $e) {
            error_log("Erreur assignUserRole: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Met à jour les crédits d'un utilisateur
     */
    public static function updateCredits(int $userId, int $amount): bool
    {
        $db   = Database::getInstance();
        $stmt = $db->prepare("UPDATE users SET credits = credits + ? WHERE user_id = ?");
        return $stmt->execute([$amount, $userId]);
    }

    /**
     * Vérifie si un utilisateur a assez de crédits
     */
    public static function hasEnoughCredits(int $userId, int $requiredCredits): bool
    {
        $user = self::find($userId);
        return $user && $user['credits'] >= $requiredCredits;
    }

    /**
     * Met à jour le statut d'un utilisateur
     */
    public static function updateStatus(int $userId, string $status): bool
    {
        return self::update($userId, ['status' => $status]);
    }

    /**
     * Récupère les utilisateurs actifs
     */
    public static function getActiveUsers(): array
    {
        return self::findAllBy(['status' => 'active']);
    }
}
