<!-- VUE CRÉATION D'UN COVOITURAGE -->

<!-- START HERO-->
<section class="hero">
    <div class="hero-content">
        <h1 class="hero-title">Créer un covoiturage</h1>
    </div>
</section>
<!-- END HERO-->

<div class="container mt-4 mb-4">
    <!-- Affichage des messages d'erreurs-->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_SESSION['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_SESSION['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0">Informations du trajet</h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="/carpools/create" id="createCarpoolForm">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?>">

                        <!-- Trajet -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="mb-3">Itinéraire</h4>
                            </div>
                            <div class="col-md-6">
                                <label for="departure" class="form-label">Ville de départ *</label>
                                <input type="text"
                                    class="form-control"
                                    id="departure"
                                    name="departure"
                                    placeholder="Ex: Paris"
                                    value="<?php echo htmlspecialchars($_POST['departure'] ?? '') ?>"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label for="arrival" class="form-label">Ville d'arrivée *</label>
                                <input type="text"
                                    class="form-control"
                                    id="arrival"
                                    name="arrival"
                                    placeholder="Ex: Lyon"
                                    value="<?php echo htmlspecialchars($_POST['arrival'] ?? '') ?>"
                                    required>
                            </div>
                        </div>

                        <!-- Horaires -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="mb-3">Horaires</h4>
                            </div>
                            <div class="col-md-6">
                                <label for="departure_time" class="form-label">Date et heure de départ *</label>
                                <input type="datetime-local"
                                    class="form-control"
                                    id="departure_time"
                                    name="departure_time"
                                    value="<?php echo $_POST['departure_time'] ?? '' ?>"
                                    min="<?php echo date('Y-m-d\TH:i') ?>"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label for="arrival_time" class="form-label">Date et heure d'arrivée *</label>
                                <input type="datetime-local"
                                    class="form-control"
                                    id="arrival_time"
                                    name="arrival_time"
                                    value="<?php echo $_POST['arrival_time'] ?? '' ?>"
                                    required>
                            </div>
                        </div>

                        <!-- Véhicule -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="mb-3">Véhicule</h4>
                            </div>
                            <div class="col-12">
                                <label for="vehicle_id" class="form-label">Choisir un véhicule *</label>
                                <select class="form-control" id="vehicle_id" name="vehicle_id" required>
                                    <option value="">Sélectionnez votre véhicule</option>
                                    <?php foreach ($vehicles as $vehicle): ?>
                                    <option value="<?php echo $vehicle['vehicle_id'] ?>"
                                            data-energy="<?php echo $vehicle['energy_type'] ?>"
                                            <?php echo(isset($_POST['vehicle_id']) && $_POST['vehicle_id'] == $vehicle['vehicle_id']) ? 'selected' : '' ?>>
                                        <?php echo htmlspecialchars($vehicle['name_brand']) ?>
                                        <?php echo htmlspecialchars($vehicle['model']) ?>
                                        -<?php echo htmlspecialchars($vehicle['color'] ?? 'Couleur non spécifiée') ?>
                                        (<?php $energyTypes = [
        'electric' => 'Électrique',
        'hybrid'   => 'Hybride',
        'diesel'   => 'Diesel',
        'essence'  => 'Essence',
    ];
echo $energyTypes[$vehicle['energy_type']] ?? $vehicle['energy_type'];
?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>

                                <div class="mt-2">
                                    <a href="/vehicles" class="btn btn-sm">
                                        Gérer mes véhicules
                                    </a>
                                </div>

                                <!-- Alerte véhicule écologique -->
                                <div id="ecoAlert" class="alert alert-success mt-3" style="display: none;">
                                    <div class="d-flex align-items-center">
                                        <img src="/assets/images/pictoEcologie.png" alt="Écologique" class="picto me-2">
                                        <div>
                                            <strong>Trajet écologique !</strong><br>
                                            <small>Votre véhicule électrique contribue à réduire les émissions de CO2</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Places et prix -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="mb-3">Places et tarif</h4>
                            </div>
                            <div class="col-md-6">
                                <label for="seats_available" class="form-label">Nombre de places disponibles *</label>
                                <select class="form-control" id="seats_available" name="seats_available" required>
                                    <option value="">Choisir le nombre de places</option>
                                    <?php for ($i = 1; $i <= 4; $i++): ?>
                                    <option value="<?php echo $i ?>"
                                            <?php echo(isset($_POST['seats_available']) && $_POST['seats_available'] == $i) ? 'selected' : '' ?>>
                                        <?php echo $i ?> place<?php echo $i > 1 ? 's' : '' ?>
                                    </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="price_per_seat" class="form-label">Prix par place (en crédits) *</label>
                                <input type="number"
                                        class="form-control"
                                        id="price_per_seat"
                                        name="price_per_seat"
                                        min="3"
                                        step="1"
                                        placeholder="Ex: 15"
                                        value="<?php echo $_POST['price_per_seat'] ?? '' ?>"
                                        required>
                                <small class="form-text text-muted">
                                    Minimum 3 crédits (2 crédits de commission + 1 crédit minimum pour vous)
                                </small>
                            </div>
                        </div>

                        <!-- Préférences -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="mb-3">Préférences de voyage (optionnel)</h4>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mb-2">
                                    <input class="form-check-input"
                                            type="checkbox"
                                            id="smoking_allowed"
                                            name="smoking_allowed"
                                            value="1"
                                            <?php echo(isset($_POST['smoking_allowed']) && $_POST['smoking_allowed']) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="smoking_allowed">
                                        Fumeurs acceptés
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input"
                                            type="checkbox"
                                            id="animals_allowed"
                                            name="animals_allowed"
                                            value="1"
                                            <?php echo(isset($_POST['animals_allowed']) && $_POST['animals_allowed']) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="animals_allowed">
                                        Animaux acceptés
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="personalized_preferences" class="form-label">Préférences personnalisées</label>
                                <textarea class="form-control"
                                            id="personalized_preferences"
                                            name="personalized_preferences"
                                            rows="3"
                                            maxlength="500"
                                            placeholder="Ex: Musique classique uniquement, pas de conversation, arrêt pause possible..."><?php echo htmlspecialchars($_POST['personalized_preferences'] ?? '') ?></textarea>
                                <small class="form-text text-muted">Maximum 500 caractères</small>
                            </div>
                        </div>

                        <!-- Résumé du trajet -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5 class="card-title">Résumé de votre trajet</h5>
                                        <div id="tripSummary">
                                            <p class="text-muted">Remplissez les informations ci-dessus pour voir le résumé</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="/dashboard" class="btn">
                                        Annuler
                                    </a>
                                    <button type="submit" class="btn" id="submitBtn">
                                        Créer le covoiturage
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script pour les fonctionnalités interactives -->
<script>
    window.carpoolCreateData = {
        minDate: '<?php echo date('Y-m-d\TH:i') ?>'
    };
</script>