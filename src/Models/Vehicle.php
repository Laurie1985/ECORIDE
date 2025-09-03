<?php
namespace App\Models;

use App\Config\Database;

class Vehicle extends BaseModel
{
    protected static $table      = 'vehicles';
    protected static $primaryKey = 'vehicle_id';

    /**
     * Récupère tous les véhicules d'un utilisateur
     */
    public static function getByUser(int $userId): array
    {
        return self::findAllBy(['user_id' => $userId]);
    }

    /**
     * Récupère les véhicules d'un utilisateur avec les informations de marque
     */
    public static function getByUserWithBrand(int $userId): array
    {
        $db   = Database::getInstance();
        $stmt = $db->prepare("
            SELECT v.*, b.name_brand
            FROM vehicles v
            JOIN brands b ON v.brand_id = b.brand_id
            WHERE v.user_id = ?
            ORDER BY v.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Vérifie si un véhicule est écologique (uniquement électrique)
     */
    public static function isEcological(int $vehicleId): bool
    {
        $vehicle = self::find($vehicleId);
        return $vehicle && $vehicle['energy_type'] === 'electric';
    }

    /**
     * Récupère un véhicule avec sa marque
     */
    public static function findWithBrand(int $vehicleId): ?array
    {
        $db   = Database::getInstance();
        $stmt = $db->prepare("
            SELECT v.*, b.name_brand
            FROM vehicles v
            JOIN brands b ON v.brand_id = b.brand_id
            WHERE v.vehicle_id = ?
        ");
        $stmt->execute([$vehicleId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Vérifie si une plaque d'immatriculation est déjà utilisée
     */
    public static function isRegistrationNumberTaken(string $registrationNumber, ?int $excludeVehicleId = null): bool
    {
        $db = Database::getInstance();

        if ($excludeVehicleId) {
            $stmt = $db->prepare("SELECT vehicle_id FROM vehicles WHERE registration_number = ? AND vehicle_id != ?");
            $stmt->execute([$registrationNumber, $excludeVehicleId]);
        } else {
            $stmt = $db->prepare("SELECT vehicle_id FROM vehicles WHERE registration_number = ?");
            $stmt->execute([$registrationNumber]);
        }

        return $stmt->fetch() !== false;
    }

    /**
     * Valide les données d'un véhicule avant insertion/modification
     */
    public static function validateVehicleData(array $data): array
    {
        $errors = [];

        // Validation marque
        if (empty($data['brand_id']) || ! is_numeric($data['brand_id'])) {
            $errors[] = 'La marque est obligatoire';
        }

        // Validation modèle
        if (empty($data['model']) || strlen($data['model']) > 50) {
            $errors[] = 'Le modèle est obligatoire et ne peut dépasser 50 caractères';
        }

        // Validation plaque d'immatriculation
        if (empty($data['registration_number']) || strlen($data['registration_number']) > 20) {
            $errors[] = 'La plaque d\'immatriculation est obligatoire et ne peut dépasser 20 caractères';
        }

        // Vérification unicité plaque
        $excludeId = $data['vehicle_id'] ?? null;
        if (! empty($data['registration_number']) && self::isRegistrationNumberTaken($data['registration_number'], $excludeId)) {
            $errors[] = 'Cette plaque d\'immatriculation est déjà enregistrée';
        }

        // Validation date première immatriculation
        if (empty($data['first_registration_date'])) {
            $errors[] = 'La date de première immatriculation est obligatoire';
        } else {
            $date = \DateTime::createFromFormat('Y-m-d', $data['first_registration_date']);
            if (! $date || $date > new \DateTime()) {
                $errors[] = 'La date de première immatriculation est invalide';
            }
        }

        // Validation nombre de places
        $seats = intval($data['seats_available'] ?? 0);
        if ($seats < 1 || $seats > 6) {
            $errors[] = 'Le nombre de places disponibles doit être entre 1 et 6';
        }

        // Validation type d'énergie
        $allowedEnergyTypes = ['electric', 'hybrid', 'diesel', 'essence'];
        if (empty($data['energy_type']) || ! in_array($data['energy_type'], $allowedEnergyTypes)) {
            $errors[] = 'Le type d\'énergie doit être : électrique, hybride, diesel ou essence';
        }

        return $errors;
    }

    /**
     * Crée un véhicule avec validation
     */
    public static function createWithValidation(array $data): array
    {
        $errors = self::validateVehicleData($data);

        if (! empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        try {
            $vehicleId = self::create($data);
            return ['success' => true, 'vehicle_id' => $vehicleId];
        } catch (\Exception $e) {
            error_log("Erreur création véhicule: " . $e->getMessage());
            return ['success' => false, 'errors' => ['Erreur lors de la création du véhicule']];
        }
    }

    /**
     * Met à jour un véhicule avec validation
     */
    public static function updateWithValidation(int $vehicleId, array $data): array
    {
        $data['vehicle_id'] = $vehicleId;
        $errors             = self::validateVehicleData($data);

        if (! empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        try {
            unset($data['vehicle_id']); // Retire id pour ne pas le mettre à jour
            $success = self::update($vehicleId, $data);
            return ['success' => $success];
        } catch (\Exception $e) {
            error_log("Erreur mise à jour véhicule: " . $e->getMessage());
            return ['success' => false, 'errors' => ['Erreur lors de la mise à jour du véhicule']];
        }
    }

    /**
     * Supprime un véhicule si aucun covoiturage actif ne l'utilise
     */
    public static function safeDelete(int $vehicleId): bool
    {
        try {
            $db = Database::getInstance();

            // Vérifier si le véhicule est utilisé dans des covoiturages actifs
            $stmt = $db->prepare("
                SELECT COUNT(*) as count
                FROM carpools
                WHERE vehicle_id = ? AND status IN ('scheduled', 'in progress')
            ");
            $stmt->execute([$vehicleId]);
            $result = $stmt->fetch();

            if ($result['count'] > 0) {
                return false; // Ne peut pas supprimer, véhicule utilisé
            }

            return self::delete($vehicleId);

        } catch (\Exception $e) {
            error_log("Erreur suppression véhicule: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère les véhicules électriques pour les statistiques
     */
    public static function getElectricVehicles(): array
    {
        return self::findAllBy(['energy_type' => 'electric']);
    }
}
