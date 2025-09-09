<!-- DASHBOARD PASSAGER -->

<!-- START HERO-->
<section class="hero">
    <div class="hero-content"></div>
</section>
<!-- END HERO-->

<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-12">
            <h1 class="dash-title">Mon espace Passager</h1>
            <p class="lead">Bonjour<?php echo htmlspecialchars($user['username']) ?></p>
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

    <!-- Recherche trajets -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card search-form">
                <div class="card-header">
                    <h3>Chercher un trajet</h3>
                </div>
                <div class="card-body d-flex justify-content-center">
                    <form action="/carpools" method="GET" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="departure" class="form-control" placeholder="Départ" required>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="arrival" class="form-control" placeholder="Arrivée" required>
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="date" class="form-control" min="<?php echo date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn">Rechercher</button>
                        </div>
                    </form>
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
            <div class="card">
                <div class="card-header">
                    <h3>Mon compte</h3>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="/profile" class="list-group-item list-group-item-action">
                            <p>Modifier mon profil</p>
                        </a>
                        <a href="/reviews/my-reviews" class="list-group-item list-group-item-action">
                            <p>Mes avis laissés</p>
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