<?php
namespace App\Models;

use App\Config\Database;

class Carpool extends BaseModel
{
    protected static $table      = 'carpools';
    protected static $primaryKey = 'carpool_id';

    /**
     * Recherche simple pour l'API JavaScript (US 3)
     */
    public static function searchForAPI(array $filters = []): array
    {
        $db         = Database::getInstance();
        $conditions = [
            'c.seats_available > 0',
            "c.status IN ('scheduled', 'in_progress')",
        ];
        $params = [];

        // Filtres de base (ville, date)
        if (! empty($filters['departure'])) {
            $conditions[] = "c.departure LIKE ?";
            $params[]     = '%' . $filters['departure'] . '%';
        }

        if (! empty($filters['arrival'])) {
            $conditions[] = "c.arrival LIKE ?";
            $params[]     = '%' . $filters['arrival'] . '%';
        }

        if (! empty($filters['date'])) {
            $conditions[] = "DATE(c.departure_time) = ?";
            $params[]     = $filters['date'];
        }

        $whereClause = 'WHERE ' . implode(' AND ', $conditions);

        $sql = "
            SELECT
                c.carpool_id,
                c.departure,
                c.arrival,
                c.departure_time,
                c.arrival_time,
                c.seats_available,
                c.price_per_seat,
                c.status,
                u.user_id as driver_id,
                u.username as driver_username,
                u.rating as driver_rating,
                u.photo as driver_photo,
                v.energy_type,
                v.color as vehicle_color,
                b.name_brand,
                v.model as vehicle_model,
                TIMESTAMPDIFF(HOUR, c.departure_time, c.arrival_time) as duration_hours,
                CASE WHEN v.energy_type = 'electric' THEN 1 ELSE 0 END as is_ecological,
                dp.smoking_allowed,
                dp.animals_allowed
            FROM carpools c
            JOIN users u ON c.driver_id = u.user_id
            JOIN vehicles v ON c.vehicle_id = v.vehicle_id
            JOIN brands b ON v.brand_id = b.brand_id
            LEFT JOIN drivers_preferences dp ON u.user_id = dp.user_id
            {$whereClause}
            ORDER BY c.departure_time ASC
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll();

        // Formater pour JavaScript
        foreach ($results as &$result) {
            $result['departure_time']  = date('Y-m-d H:i', strtotime($result['departure_time']));
            $result['arrival_time']    = date('Y-m-d H:i', strtotime($result['arrival_time']));
            $result['driver_rating']   = (float) $result['driver_rating'];
            $result['price_per_seat']  = (float) $result['price_per_seat'];
            $result['is_ecological']   = (bool) $result['is_ecological'];
            $result['smoking_allowed'] = (bool) $result['smoking_allowed'];
            $result['animals_allowed'] = (bool) $result['animals_allowed'];
        }

        return $results;
    }

    /**
     * Suggestion de date si aucun résultat
     */
    public static function suggestAlternativeDate(string $departure, string $arrival, string $requestedDate): ?string
    {
        $db   = Database::getInstance();
        $stmt = $db->prepare("
            SELECT MIN(DATE(departure_time)) as suggested_date
            FROM carpools c
            WHERE c.departure LIKE ?
            AND c.arrival LIKE ?
            AND DATE(c.departure_time) > ?
            AND c.seats_available > 0
            AND c.status = 'scheduled'
        ");

        $stmt->execute([
            '%' . $departure . '%',
            '%' . $arrival . '%',
            $requestedDate,
        ]);

        $result = $stmt->fetch();
        return $result['suggested_date'] ?? null;
    }

    /**
     * Récupère un covoiturage avec détails
     */
    public static function getWithDetails(int $carpoolId): ?array
    {
        $db   = Database::getInstance();
        $stmt = $db->prepare("
            SELECT c.*, u.username, u.rating, u.phone, u.photo,
            v.energy_type, v.seats_available as total_vehicle_seats,
            b.name_brand, v.model, v.color, v.registration_number,
            dp.smoking_allowed, dp.animals_allowed, dp.personalized_preferences
            FROM carpools c
            JOIN users u ON c.driver_id = u.user_id
            JOIN vehicles v ON c.vehicle_id = v.vehicle_id
            JOIN brands b ON v.brand_id = b.brand_id
            LEFT JOIN drivers_preferences dp ON u.user_id = dp.user_id
            WHERE c.carpool_id = ?
        ");
        $stmt->execute([$carpoolId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Vérifie si un covoiturage est écologique (véhicule électrique)
     */
    public static function isEcological(int $carpoolId): bool
    {
        $carpool = self::getWithDetails($carpoolId);
        return $carpool && $carpool['energy_type'] === 'electric';
    }
}
