<!-- DASHBOARD CONDUCTEUR -->

<!-- START HERO-->
<section class="hero">
    <div class="hero-content">
        <h1 class="dash-title">Mon espace Conducteur</h1>
    </div>
</section>
<!-- END HERO-->

<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-12">
            <p class="lead">Bonjour<?php echo htmlspecialchars($user['username']) ?></p>
        </div>
    </div>

    <!-- Statistiques conducteur -->
    <div class="row mb-4 mt-4 gy-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="credit-title">Crédits obtenus</h3>
                    <h2 class="credit-number"><?php echo $user['credits'] ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="carpool-title">Mes covoiturages</h3>
                    <h2 class="carpool-number"><?php echo $driverCarpoolCount ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="vehicle-title">Mes véhicules</h3>
                    <h2 class="vehicle-number"><?php echo $vehicleCount ?></h2>
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
                <div class="card-body d-flex justify-content-center">
                    <div class="row gy-2">
                        <div class="col-md-6">
                            <a href="/carpools/create" class="btn">
                                Créer un trajet
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="/vehicles" class="btn">
                                Ajouter un véhicule
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gestion des trajets -->
    <div class="row mb-4 gy-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3>Mes trajets</h3>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="/my-carpools" class="list-group-item list-group-item-action">
                            <p>Mes covoiturages actifs (<?php echo $driverCarpoolCount ?>)</p>
                        </a>
                        <a href="/my-carpools/passengers" class="list-group-item list-group-item-action">
                            <p>Réservations sur mes trajets</p>
                        </a>
                        <a href="/history" class="list-group-item list-group-item-action">
                            <p>Historique de mes trajets</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3>Mon compte</h3>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="/vehicles" class="list-group-item list-group-item-action">
                            <p>Mes véhicules (<?php echo $vehicleCount ?>)</p>
                        </a>
                        <a href="/preferences" class="list-group-item list-group-item-action">
                            <p>Mes préférences de conduite</p>
                        </a>
                        <a href="/reviews/about-me" class="list-group-item list-group-item-action">
                            <p>Avis sur mes trajets</p>
                        </a>
                        <a href="/profile" class="list-group-item list-group-item-action">
                            <p>Modifier mon profil</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Conseils écologiques -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Votre impact écologique</h3>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>En tant que conducteur Ecoride, vous contribuez à :</strong></p>
                    <ul class="list mb-0">
                        <li><p class="mb-2">Réduire les émissions de CO2</p></li>
                        <li><p class="mb-2">Optimiser l'utilisation des véhicules</p></li>
                        <li><p class="mb-2">Créer du lien social</p></li>
                        <li><p class="mb-2">Promouvoir la mobilité durable</p></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>