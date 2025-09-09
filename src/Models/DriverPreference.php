<?php
namespace App\Models;

class DriverPreference extends BaseModel
{
    protected static $table      = 'drivers_preferences';
    protected static $primaryKey = 'preference_id';

    /**
     * Récupère les préférences d'un conducteur
     */
    public static function getByUser(int $userId): ?array
    {
        return self::findBy(['user_id' => $userId]);
    }

    /**
     * Met à jour ou crée les préférences d'un utilisateur
     */
    public static function updateOrCreate(int $userId, array $data): bool
    {
        $existing = self::findBy(['user_id' => $userId]);

        $data['user_id'] = $userId;

        if ($existing) {
            return self::update($existing['preference_id'], $data);
        } else {
            $id = self::create($data);
            return $id !== false;
        }
    }
}
