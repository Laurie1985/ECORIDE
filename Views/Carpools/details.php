<!-- VUE DÉTAILLÉE D'UN COVOITURAGE (US 5) -->

<!-- START HERO-->
<section class="hero">
    <div class="hero-content">
        <h1 class="hero-title">Détails du covoiturage</h1>
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

    <!-- Informations principales du trajet -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-0">
                                <?php echo htmlspecialchars($carpool['departure']) ?>
                                ->
                                <?php echo htmlspecialchars($carpool['arrival']) ?>
                                <?php if ($carpool['energy_type'] === 'electric'): ?>
                                <span class="ms-2">
                                    <img src="/assets/images/pictoEcologie.png" alt="Écologique" class="picto me-1">
                                </span>
                                <?php endif; ?>
                            </h2>
                        </div>
                        <div class="col-md-4 text-end">
                            <h3 class="mb-0 mt-0"><?php echo $carpool['price_per_seat'] ?> crédits</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Informations du trajet</h4>
                            <p class="mb-2">
                                <strong>Date de départ :</strong>
                                <?php echo date('d/m/Y', strtotime($carpool['departure_time'])) ?>
                            </p>
                            <p class="mb-2">
                                <strong>Heure de départ :</strong>
                                <?php echo date('H:i', strtotime($carpool['departure_time'])) ?>
                            </p>
                            <p class="mb-2">
                                <strong>Heure d'arrivée :</strong>
                                <?php echo date('H:i', strtotime($carpool['arrival_time'])) ?>
                            </p>
                            <p class="mb-2">
                                <strong>Durée estimée :</strong>
                                <?php
                                    $duration = (strtotime($carpool['arrival_time']) - strtotime($carpool['departure_time'])) / 3600;
                                    echo round($duration, 1) . ' heures';
                                ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h4>Disponibilité</h4>
                            <p class="mb-2">
                                <strong>Places disponibles :</strong>
                                <span class="badge bg-primary"><?php echo $carpool['seats_available'] ?> place(s)</span>
                            </p>
                            <p class="mb-2">
                                <strong>Prix par place :</strong>
                                <?php echo $carpool['price_per_seat'] ?> crédits
                            </p>
                            <p class="mb-2">
                                <strong>Statut :</strong>
                                <?php
                                    $statusBadges = [
                                        'scheduled'   => '<span class="badge bg-primary">Programmé</span>',
                                        'in_progress' => '<span class="badge bg-warning">En cours</span>',
                                        'finished'    => '<span class="badge bg-success">Terminé</span>',
                                        'canceled'    => '<span class="badge bg-danger">Annulé</span>',
                                    ];
                                    echo $statusBadges[$carpool['status']] ?? '<span class="badge bg-secondary">Inconnu</span>';
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Informations sur le conducteur -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h3>Votre conducteur</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            <?php if (! empty($carpool['photo'])): ?>
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($carpool['photo']) ?>"alt="Photo<?php echo htmlspecialchars($carpool['username']) ?>"class="rounded-circle" width="60" height="60">
                            <?php else: ?>
                            <img src="/assets/images/default-avatar.png"alt="Photo<?php echo htmlspecialchars($carpool['username']) ?>"class="rounded-circle" width="60" height="60">
                            <?php endif; ?>
                        </div>
                        <div>
                            <h4 class="mb-1"><?php echo htmlspecialchars($carpool['username']) ?></h4>
                            <div class=" mb-1">
                                <span class="text-dark ms-1">(<?php echo number_format($carpool['rating'], 1) ?>/5)</span>
                            </div>
                            <?php if (! empty($carpool['phone'])): ?>
                            <small class="text-muted">Contact disponible après réservation</small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Bouton pour voir les avis -->
                    <div class="text-center">
                        <a href="/reviews/driver/<?php echo $carpool['driver_id'] ?>"class="btn btn-outline-primary btn-sm">
                            Voir les avis sur ce conducteur
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations sur le véhicule -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h3>Véhicule</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <p class="mb-2">
                                <strong>Marque :</strong><br>
                                <?php echo htmlspecialchars($carpool['name_brand']) ?>
                            </p>
                            <p class="mb-2">
                                <strong>Modèle :</strong><br>
                                <?php echo htmlspecialchars($carpool['model']) ?>
                            </p>
                        </div>
                        <div class="col-6">
                            <p class="mb-2">
                                <strong>Couleur :</strong><br>
                                <?php echo htmlspecialchars($carpool['color'] ?? 'Non spécifiée') ?>
                            </p>
                            <p class="mb-2">
                                <strong>Énergie :</strong><br>
                                <?php
                                    $energyTypes = [
                                        'electric' => 'Électrique',
                                        'hybrid'   => 'Hybride',
                                        'diesel'   => 'Diesel',
                                        'essence'  => 'Essence',
                                    ];
                                    echo $energyTypes[$carpool['energy_type']] ?? $carpool['energy_type'];
                                ?>
                            </p>
                        </div>
                    </div>

                    <?php if ($carpool['energy_type'] === 'electric'): ?>
                    <div class="alert alert-success">
                        <h6 class="mb-1">Trajet écologique</h6>
                        <small>Ce véhicule électrique contribue à réduire les émissions de CO2</small>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Préférences du conducteur -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Préférences du conducteur</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Règles générales</h4>
                            <div class="mb-2">
                                <?php if ($carpool['smoking_allowed']): ?>
                                <span class="badge bg-success me-2">Fumeurs acceptés</span>
                                <?php else: ?>
                                <span class="badge bg-danger me-2">Fumeurs non acceptés</span>
                                <?php endif; ?>
                            </div>
                            <div class="mb-2">
                                <?php if ($carpool['animals_allowed']): ?>
                                <span class="badge bg-success me-2">Animaux acceptés</span>
                                <?php else: ?>
                                <span class="badge bg-danger me-2">Animaux non acceptés</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <?php if (! empty($carpool['personalized_preferences'])): ?>
                            <h4>Préférences personnalisées</h4>
                            <div class=" ps-3">
                                <p class="mb-0">
                                    <?php echo(htmlspecialchars($carpool['personalized_preferences'])) ?>
                                </p>
                            </div>
                            <?php else: ?>
                            <p>Aucune préférence personnalisée spécifiée</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Avis du conducteur -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Avis des passagers</h3>
                </div>
                <div class="card-body">
                    <div id="reviewsSection">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Chargement des avis...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section de réservation ou gestion pour le conducteur -->
    <?php if ($carpool['status'] === 'scheduled' && $carpool['seats_available'] > 0): ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <?php if (! isset($_SESSION['user_id'])): ?>
                <div class="card-header">
                    <h3>Participer à ce covoiturage</h3>
                </div>
                <div class="card-body">
                    <!-- Visiteur non connecté -->
                    <div class="alert alert-info text-center">
                        <h4>Connectez-vous pour participer</h4>
                        <p class="mb-3">Pour réserver ce covoiturage, vous devez être connecté à votre compte Ecoride.</p>
                        <div class="d-flex gap-2 justify-content-center">
                            <a href="/login" class="btn">Se connecter</a>
                            <a href="/register" class="btn">Créer un compte</a>
                        </div>
                    </div>
                </div>

                <?php elseif ($carpool['driver_id'] == $_SESSION['user_id']): ?>
                <!-- C'est le conducteur de ce covoiturage -->
                <div class="card-header">
                    <h3>Gestion de votre covoiturage</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info text-center">
                        <h4>Votre covoiturage</h4>
                        <p class="mb-3">Vous êtes le conducteur de ce trajet.</p>
                        <div class="d-flex gap-2 justify-content-center">
                            <a href="/my-carpools" class="btn">Gérer mes covoiturages</a>
                            <a href="/my-passengers" class="btn btn-outline-info">Voir mes passagers</a>
                        </div>
                    </div>
                </div>

                <?php else: ?>
                    <!-- Utilisateur connecté qui n'est pas le conducteur -->
                <div class="card-header">
                    <h3>Réserver ce covoiturage</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="/carpools/<?php echo $carpool['carpool_id'] ?>/book" id="reservationForm">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?>">
                        <input type="hidden" name="carpool_id" value="<?php echo $carpool['carpool_id'] ?>">

                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <label for="seats_booked" class="form-label">Nombre de places</label>
                                <select name="seats_booked" id="seats_booked" class="form-control" required>
                                    <?php for ($i = 1; $i <= min($carpool['seats_available'], 4); $i++): ?>
                                    <option value="<?php echo $i ?>"><?php echo $i ?> place(s)</option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Prix total</label>
                                <div class="form-control bg-light">
                                    <span id="totalPrice"><?php echo $carpool['price_per_seat'] ?>crédits</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn" id="reserveBtn">
                                    Participer
                                </button>
                            </div>
                        </div>

                        <div class="mt-3">
                            <small class="text-muted">
                                <strong>Remarque :</strong> Une double confirmation vous sera demandée avant le paiement.
                            </small>
                        </div>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php elseif ($carpool['status'] === 'scheduled' && $carpool['seats_available'] === 0): ?>
    <!-- Plus de places disponibles -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Disponibilité</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning text-center">
                        <h4>Covoiturage complet</h4>
                        <p class="mb-0">Toutes les places ont été réservées pour ce trajet.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php elseif ($carpool['status'] !== 'scheduled'): ?>
    <!-- Trajet non disponible -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Statut du covoiturage</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info text-center">
                        <h4>Trajet non disponible</h4>
                        <p class="mb-0">Ce covoiturage n'est plus disponible à la réservation.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Actions de navigation -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center">
                    <?php if ($carpool['driver_id'] != $_SESSION['user_id']): ?>
                    <!-- Pour les passagers/visiteurs -->
                    <a href="/carpools" class="btn btn-outline-secondary me-2">
                        Nouvelle recherche
                    </a>
                    <a href="/" class="btn btn-outline-secondary">
                        Retour à l'accueil
                    </a>
                    <?php else: ?>
                    <!-- Pour le conducteur -->
                    <a href="/my-carpools" class="btn btn-outline-secondary me-2">
                        Mes covoiturages
                    </a>
                    <a href="/dashboard" class="btn btn-outline-secondary">
                        Mon tableau de bord
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.carpoolData = {
        driverId:<?php echo $carpool['driver_id'] ?>,
        pricePerSeat:<?php echo $carpool['price_per_seat'] ?>,
        carpoolId:<?php echo $carpool['carpool_id'] ?>
    };
</script>