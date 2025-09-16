<!-- DASHBOARD PASSAGER -->

<!-- START HERO-->
<section class="hero">
    <div class="hero-content">
        <h1 class="dash-title">Mon espace Passager</h1>
    </div>
</section>
<!-- END HERO-->

<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-12">
            <p class="lead">Bonjour
                <strong><?php echo htmlspecialchars($user['username']) ?></strong>
            </p>
        </div>
    </div>

    <!-- Statistiques passager -->
    <div class="row mb-4 mt-4 gy-4">
        <div class="col-md-6">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="credit-title">Crédits disponibles</h3>
                    <h2 class="credit-number"><?php echo $user['credits'] ?></h2>
                    <p class="credit-text">Pour vos prochains trajets</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="reservation-title">Mes réservations</h3>
                    <h2 class="reservation-number"><?php echo $reservationCount ?></h2>
                    <p class="reservation-text">Trajets réservés</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions principales -->
    <div class="row mb-4 gy-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3>Mes Réservations</h3>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="/reservations" class="list-group-item list-group-item-action">
                            <p>Gérer mes réservations (<?php echo $reservationCount ?>)</p>
                        </a>
                        <a href="/history" class="list-group-item list-group-item-action">
                            <p>Historique de mes voyages</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h3>Mon compte</h3>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="/profile" class="list-group-item list-group-item-action">
                            <p>Modifier mon profil</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Invitation à devenir conducteur -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Envie de proposer vos trajets ?</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <p class="mb-2"><strong>Devenez conducteur et participez activement à la mobilité durable :</strong></p>
                            <ul class="list">
                                <li><p class="mb-0">Rentabilisez vos déplacements quotidiens</p></li>
                                <li><p class="mb-0">Gagnez des crédits pour vos futurs trajets</p></li>
                                <li><p class="mb-0">Rencontrez des voyageurs partageant vos valeurs</p></li>
                                <li><p class="mb-0">Contribuez à réduire l'empreinte carbone</p></li>
                            </ul>
                        </div>
                        <div class="col-md-4 text-center">
                            <a href="/profile" class="btn btn-lg">
                                Devenir conducteur
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>