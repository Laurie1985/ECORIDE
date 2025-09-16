<!-- DASHBOARD CONDUCTEUR & PASSAGER -->

<!-- START HERO-->
<section class="hero">
    <div class="hero-content">
        <h1 class="dash-title">Mon espace</h1>
    </div>
</section>
<!-- END HERO-->

<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-12">
            <p class="lead">Bonjour
                <strong><?php echo htmlspecialchars($user['username']) ?></strong>, vous êtes conducteur et passager
            </p>
        </div>
    </div>

    <!-- Statistiques globales -->
    <div class="row mb-4 mt-4 gy-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="credit-title">Crédits</h3>
                    <h2 class="credit-number"><?php echo $user['credits'] ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="carpool-title">Mes trajets</h3>
                    <h2 class="carpool-number"><?php echo $driverCarpoolCount ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="reservation-title">Réservations</h3>
                    <h2 class="reservation-number"><?php echo $reservationCount ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="rating-title">Ma note</h3>
                    <h2 class="rating-number"><?php echo number_format($user['rating'], 1) ?>/5</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Actions rapides</h3>
                </div>
                <div class="card-body">
                    <div class="row col-12 gy-2 d-flex justify-content-center">
                        <div class="col-md-3">
                            <a href="/carpools/create" class="btn">
                                Créer un trajet
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="/carpools" class="btn">
                                Rechercher un trajet
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="/vehicles" class="btn">
                                Gérer mes véhicules
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="/profile" class="btn">
                                Mon profil
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row gy-4">
        <!-- Section Conducteur -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h3>Espace Conducteur</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="text-center">
                                <h2><?php echo $driverCarpoolCount ?></h2>
                                <p class="carpool-text">Covoiturages proposés</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h2><?php echo $vehicleCount ?></h2>
                                <p class="vehicle-text">Véhicules enregistrés</p>
                            </div>
                        </div>
                    </div>

                    <div class="list-group list-group-flush">
                        <a href="/my-carpools" class="list-group-item list-group-item-action">
                            <p>Mes covoiturages actifs (<?php echo $driverCarpoolCount ?>)</p>
                        </a>
                        <a href="/my-carpools/passengers" class="list-group-item list-group-item-action">
                            <p>Réservations sur mes trajets</p>
                        </a>
                        <a href="/vehicles" class="list-group-item list-group-item-action">
                            <p>Mes véhicules (<?php echo $vehicleCount ?>)</p>
                        </a>
                        <a href="/preferences" class="list-group-item list-group-item-action">
                            <p>Mes préférences de conduite</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Passager -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h3>Espace Passager</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="text-center">
                                <h2><?php echo $reservationCount ?></h2>
                                <p class="reservation-text">Réservations actives</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h2><?php echo $user['credits'] ?></h2>
                                <p class="credit-text">Crédits disponibles</p>
                            </div>
                        </div>
                    </div>

                    <div class="list-group list-group-flush">
                        <a href="/reservations" class="list-group-item list-group-item-action">
                            <p>Gérer mes réservations (<?php echo $reservationCount ?>)</p>
                        </a>
                        <a href="/carpools" class="list-group-item list-group-item-action">
                            <p>Rechercher un trajet</p>
                        </a>
                        <a href="/history" class="list-group-item list-group-item-action">
                            <p>Historique complet</p>
                        </a>
                        <a href="/profile" class="list-group-item list-group-item-action">
                            <p>Modifier mon profil</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Impact écologique -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Votre impact écologique</h3>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>En tant qu'utilisateur Ecoride, vous contribuez doublement à :</strong></p>
                    <div class="row">
                        <div class="col-md-6">
                            <h4>En tant que conducteur :</h4>
                            <ul class="list">
                                <li><p class="mb-2">Réduire les émissions de CO2</p></li>
                                <li><p class="mb-2">Optimiser l'utilisation des véhicules</p></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h4>En tant que passager :</h4>
                            <ul class="list">
                                <li><p class="mb-2">Créer du lien social</p></li>
                                <li><p class="mb-2">Promouvoir la mobilité durable</p></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>