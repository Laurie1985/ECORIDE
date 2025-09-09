<?php
namespace App\Controllers;

use App\Models\Brand;
use App\Models\Vehicle;
use App\Security\TokenManager;

class VehicleController extends BaseController
{
    protected $tokenManager;

    public function __construct()
    {
        parent::__construct();
        $this->tokenManager = new TokenManager;
    }

    /**
     * Affiche la liste des véhicules de l'utilisateur connecté
     * et le formulaire d'ajout.
     */
    public function index()
    {
        // Vérifie si l'utilisateur est connecté
        if (! isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }

        $userId = $_SESSION['user_id'];

        // Récupère les véhicules de l'utilisateur avec leur marque
        $vehicles = Vehicle::getByUserWithBrand($userId);

        // Récupère toutes les marques pour le formulaire d'ajout/édition
        $brands = Brand::all();

        $this->render('users/vehicles', [
            'title'      => 'Mes véhicules',
            'cssFile'    => 'vehicles',
            'vehicles'   => $vehicles,
            'brands'     => $brands,
            'csrf_token' => $this->tokenManager->generateCsrfToken(),
        ]);
    }

    /**
     * Traite la création d'un nouveau véhicule.
     */
    /*public function create()
    {
        if (! isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ! $this->validateCsrfToken()) {
            $_SESSION['error'] = 'Requête invalide.';
            $this->redirect('/vehicles');
        }

        $data            = $_POST;
        $data['user_id'] = $_SESSION['user_id'];

        // Utilise la méthode de validation et de création du modèle
        $result = Vehicle::createWithValidation($data);

        if ($result['success']) {
            $_SESSION['success'] = 'Véhicule ajouté avec succès !';
        } else {
            $_SESSION['errors'] = $result['errors'];
        }

        $this->redirect('/vehicles');
    }*/
    public function create()
    {
        if (! isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ! $this->validateCsrfToken()) {
            $_SESSION['error'] = 'Requête invalide.';
            $this->redirect('/vehicles');
        }

        $userId = $_SESSION['user_id'];

        try {
            Vehicle::create([
                'user_id'                 => $userId,
                'brand_id'                => $_POST['brand_id'],
                'model'                   => $_POST['model'],
                'registration_number'     => $_POST['registration_number'],
                'first_registration_date' => $_POST['first_registration_date'],
                'color'                   => $_POST['color'] ?? null,
                'seats_available'         => $_POST['seats_available'],
                'energy_type'             => $_POST['energy_type'],
            ]);

            $_SESSION['success'] = 'Véhicule ajouté avec succès !';
        } catch (\Exception $e) {
            error_log("Erreur ajout véhicule: " . $e->getMessage());
            $_SESSION['error'] = 'Erreur lors de l\'ajout du véhicule';
        }

        $this->redirect('/vehicles');
    }

    /**
     * Affiche le formulaire d'édition d'un véhicule.
     *
     * @param int $vehicleId L'ID du véhicule à éditer.
     */
    public function showEdit(int $vehicleId)
    {
        if (! isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }

        $userId  = $_SESSION['user_id'];
        $vehicle = Vehicle::findWithBrand($vehicleId);

        // Vérifie si le véhicule existe et appartient à l'utilisateur
        if (! $vehicle || $vehicle['user_id'] != $userId) {
            $_SESSION['error'] = 'Véhicule introuvable ou vous n\'avez pas la permission.';
            $this->redirect('/vehicles');
        }

        $brands = Brand::all();

        $this->render('users/vehicles_edit', [
            'title'      => 'Modifier un véhicule',
            'cssFile'    => 'vehicles',
            'vehicle'    => $vehicle,
            'brands'     => $brands,
            'csrf_token' => $this->tokenManager->generateCsrfToken(),
        ]);
    }

    /**
     * Traite la mise à jour d'un véhicule.
     *
     * @param int $vehicleId L'ID du véhicule à mettre à jour.
     */
    public function update(int $vehicleId)
    {
        if (! isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }

        $userId  = $_SESSION['user_id'];
        $vehicle = Vehicle::find($vehicleId);

        if (! $vehicle || $vehicle['user_id'] != $userId) {
            $_SESSION['error'] = 'Véhicule introuvable ou vous n\'avez pas la permission.';
            $this->redirect('/vehicles');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ! $this->validateCsrfToken()) {
            $_SESSION['error'] = 'Requête invalide.';
            $this->redirect('/vehicles');
        }

        $data   = $_POST;
        $result = Vehicle::updateWithValidation($vehicleId, $data);

        if ($result['success']) {
            $_SESSION['success'] = 'Véhicule mis à jour avec succès !';
        } else {
            $_SESSION['errors'] = $result['errors'];
        }

        $this->redirect('/vehicles');
    }

    /**
     * Supprime un véhicule.
     *
     * @param int $vehicleId L'ID du véhicule à supprimer.
     */
    public function delete(int $vehicleId)
    {
        if (! isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }

        $userId  = $_SESSION['user_id'];
        $vehicle = Vehicle::find($vehicleId);

        if (! $vehicle || $vehicle['user_id'] != $userId) {
            $_SESSION['error'] = 'Véhicule introuvable ou vous n\'avez pas la permission.';
            $this->redirect('/vehicles');
        }

        if (Vehicle::safeDelete($vehicleId)) {
            $_SESSION['success'] = 'Véhicule supprimé avec succès !';
        } else {
            $_SESSION['error'] = 'Impossible de supprimer le véhicule car il est associé à des covoiturages actifs.';
        }

        $this->redirect('/vehicles');
    }
}
