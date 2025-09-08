<!-- TABLEAU DE BORD UTILISATEUR ADAPTATIF -->
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h1>Bienvenue                          <?php echo htmlspecialchars($user['username']) ?></h1>
            <p class="lead">Votre espace personnel Ecoride</p>
        </div>
    </div>

    <!-- Informations communes -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title">Mes crédits</h5>
                    <h2 class="text-success"><?php echo $user['credits'] ?></h2>
                    <small class="text-muted">Crédits disponibles</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title">Note moyenne</h5>
                    <h2><?php echo number_format($user['rating'], 1) ?>/5</h2>
                    <small class="text-muted">Évaluation communauté</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title">Statut</h5>
                    <h2 class="text-info">
                        <?php
                            switch ($_SESSION['user_type']) {
                                case 'driver':echo 'Conducteur';
                                    break;
                                case 'passenger':echo 'Passager';
                                    break;
                                case 'both':echo 'Conducteur & Passager';
                                    break;
                                default: echo 'Passager';
                                    break;
                            }
                        ?>
                    </h2>
                    <a href="/profile" class="btn btn-sm">Modifier</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides communes -->
    <div class="row mb-4">
        <div class="col-12">
            <h3>Actions rapides</h3>
            <div class="d-flex gap-2 flex-wrap">
                <a href="/carpools" class="btn">Rechercher un covoiturage</a>
                <a href="/profile" class="btn">Mon profil</a>
                <a href="/history" class="btn">Historique complet</a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Section PASSAGER -->
        <?php if (in_array($_SESSION['user_type'], ['passenger', 'both'])): ?>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Espace Passager</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6>Mes réservations actives</h6>
                        <p class="h5"><?php echo $reservationCount ?> trajets</p>
                    </div>

                    <div class="list-group list-group-flush">
                        <a href="/reservations" class="list-group-item list-group-item-action">
                            Gérer mes réservations
                        </a>
                        <a href="/carpools" class="list-group-item list-group-item-action">
                            Rechercher un nouveau trajet
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Section CONDUCTEUR -->
        <?php if (in_array($_SESSION['user_type'], ['driver', 'both'])): ?>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Espace Conducteur</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <h6>Mes covoiturages</h6>
                            <p class="h5"><?php echo $driverCarpoolCount ?> trajets</p>
                        </div>
                        <div class="col-6">
                            <h6>Mes véhicules</h6>
                            <p class="h5"><?php echo $vehicleCount ?> véhicules</p>
                        </div>
                    </div>

                    <div class="list-group list-group-flush">
                        <a href="/my-carpools" class="list-group-item list-group-item-action">
                            Gérer mes covoiturages
                        </a>
                        <a href="/carpools/create" class="list-group-item list-group-item-action">
                            Créer un nouveau trajet
                        </a>
                        <a href="/vehicles" class="list-group-item list-group-item-action">
                            Gérer mes véhicules
                        </a>
                        <a href="/preferences" class="list-group-item list-group-item-action">
                            Mes préférences de conduite
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Si l'utilisateur n'est que passager, étendre la section -->
        <?php if ($_SESSION['user_type'] === 'passenger'): ?>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">Devenir Conducteur</h4>
                </div>
                <div class="card-body">
                    <p>Proposez vos trajets et gagnez des crédits !</p>
                    <ul class="list-unstyled">
                        <li>✓ Rentabilisez vos déplacements</li>
                        <li>✓ Participez à la mobilité durable</li>
                        <li>✓ Rencontrez de nouveaux voyageurs</li>
                    </ul>
                    <a href="/profile" class="btn btn-warning">Devenir conducteur</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Activité récente -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Activité récente</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted">Vos dernières actions sur la plateforme apparaîtront ici.</p>
                    <div class="d-flex gap-2">
                        <a href="/history" class="btn btn-outline-primary">Voir l'historique complet</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>