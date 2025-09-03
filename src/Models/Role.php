<?php
namespace App\Models;

class Role extends BaseModel
{
    protected static $table      = 'roles';
    protected static $primaryKey = 'role_id';

    /**
     * Trouve un rôle par son nom.
     *
     * @param string $roleName Le nom du rôle à chercher.
     * @return array|false Retourne les données du rôle ou false si non trouvé.
     */
    public static function findByName(string $roleName): array | false
    {
        return self::findBy(['role_name' => $roleName]);
    }
}
