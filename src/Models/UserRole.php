<?php
namespace App\Models;

use App\Config\Database;

class UserRole extends BaseModel
{
    protected static $table      = 'user_roles';
    protected static $primaryKey = 'user_role_id'; // Votre clé modifiée

    /**
     * Assigne un rôle à un utilisateur avec type (driver/passenger/both)
     */
    public static function assignRole(int $userId, int $roleId, string $userType = 'passenger'): int | false
    {
        return self::create([
            'user_id'   => $userId,
            'role_id'   => $roleId,
            'user_role' => $userType,
        ]);
    }

    /**
     * Récupère les rôles d'un utilisateur avec jointure
     */
    public static function getUserRolesWithNames(int $userId): array
    {
        $db   = Database::getInstance();
        $stmt = $db->prepare("
            SELECT ur.*, r.role_name
            FROM user_roles ur
            JOIN roles r ON ur.role_id = r.role_id
            WHERE ur.user_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Met à jour le type d'utilisateur (driver/passenger/both)
     */
    public static function updateUserType(int $userId, int $roleId, string $userType): bool
    {
        $db   = Database::getInstance();
        $stmt = $db->prepare("
            UPDATE user_roles
            SET user_role = ?
            WHERE user_id = ? AND role_id = ?
        ");
        return $stmt->execute([$userType, $userId, $roleId]);
    }

    /**
     * Vérifie si un utilisateur a un rôle spécifique
     */
    public static function hasRole(int $userId, string $roleName): bool
    {
        $db   = Database::getInstance();
        $stmt = $db->prepare("
            SELECT COUNT(*) as count
            FROM user_roles ur
            JOIN roles r ON ur.role_id = r.role_id
            WHERE ur.user_id = ? AND r.role_name = ?
        ");
        $stmt->execute([$userId, $roleName]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
}
