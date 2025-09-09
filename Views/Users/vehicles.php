<!-- GESTION DES VÉHICULES -->

<!-- START HERO-->
<section class="hero">
    <div class="hero-content"></div>
</section>
<!-- END HERO-->

<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-12">
            <h1 class="dash-title">Mes véhicules</h1>
        </div>
    </div>

    <!-- Véhicules existants -->
    <?php if (! empty($vehicles)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Mes véhicules enregistrés</h3>
                </div>
                <div class="card-body">
                    <div class="row gy-3">
                        <?php foreach ($vehicles as $vehicle): ?>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-8">
                                            <h3 class="card-title">
                                                <?php echo htmlspecialchars($vehicle['name_brand']) ?>
                                                <?php echo htmlspecialchars($vehicle['model']) ?>
                                            </h3>
                                            <p class="card-text">
                                                <strong>Immatriculation :</strong>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   <?php echo htmlspecialchars($vehicle['registration_number']) ?><br>
                                                <strong>Couleur :</strong>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           <?php echo htmlspecialchars($vehicle['color'] ?? 'Non spécifiée') ?><br>
                                                <strong>Places :</strong>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          <?php echo $vehicle['seats_available'] ?><br>
                                                <strong>Énergie :</strong>
                                                <?php
                                                    $energyTypes = [
                                                        'electric' => 'electrique',
                                                        'hybrid'   => 'Hybride',
                                                        'diesel'   => 'Diesel',
                                                        'essence'  => 'Essence',
                                                    ];
                                                    echo $energyTypes[$vehicle['energy_type']] ?? $vehicle['energy_type'];
                                                ?>
                                                <?php if ($vehicle['energy_type'] === 'electric'): ?>
                                                <img src="/assets/images/pictoEcologie.png" alt="Pictogramme feuilles" class="picto">
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                        <div class="col-4 text-end">
                                            <a href="/vehicles/<?php echo $vehicle['vehicle_id'] ?>/edit" class="btn btn-sm mb-2">
                                                Modifier
                                            </a>
                                            <form method="POST" action="/vehicles/<?php echo $vehicle['vehicle_id'] ?>/delete" class="d-inline">
                                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Supprimer ce véhicule ?')">
                                                    Supprimer
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Formulaire d'ajout -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Ajouter un véhicule</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="/vehicles/create">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?>">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="brand_id" class="form-label">Marque *</label>
                                <select name="brand_id" id="brand_id" class="form-control" required>
                                    <option value="">Sélectionner une marque</option>
                                    <?php foreach ($brands as $brand): ?>
                                    <option value="<?php echo $brand['brand_id'] ?>">
                                        <?php echo htmlspecialchars($brand['name_brand']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="model" class="form-label">Modèle *</label>
                                <input type="text" name="model" id="model" class="form-control"placeholder="Ex: Clio, Golf, Model 3..." required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="registration_number" class="form-label">Plaque d'immatriculation *</label>
                                <input type="text" name="registration_number" id="registration_number"class="form-control" placeholder="Ex: AB-123-CD" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="color" class="form-label">Couleur</label>
                                <input type="text" name="color" id="color" class="form-control"placeholder="Ex: Blanc, Rouge, Gris...">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="first_registration_date" class="form-label">Date de première immatriculation *</label>
                                <input type="date" name="first_registration_date" id="first_registration_date"class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="seats_available" class="form-label">Nombre de places disponibles *</label>
                                <select name="seats_available" id="seats_available" class="form-control" required>
                                    <option value="">Sélectionner</option>
                                    <option value="1">1 place</option>
                                    <option value="2">2 places</option>
                                    <option value="3">3 places</option>
                                    <option value="4">4 places</option>
                                    <option value="5">5 places</option>
                                    <option value="6">6 places</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="energy_type" class="form-label">Type d'énergie *</label>
                                <select name="energy_type" id="energy_type" class="form-control" required>
                                    <option value="">Sélectionner</option>
                                    <option value="electric">Électrique (Écologique)</option>
                                    <option value="hybrid">Hybride</option>
                                    <option value="essence">Essence</option>
                                    <option value="diesel">Diesel</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn">Ajouter ce véhicule</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Actions rapides</h3>
                </div>
                <div class="card-body">
                    <div class="gy-2 d-flex justify-content-center">
                        <div class="col-md-3">
                            <a href="/preferences" class="btn">
                                Mes préférences de conduite
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="/carpools/create" class="btn">
                                Créer un covoiturage
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="/dashboard" class="btn">
                                Mon tableau de bord
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
