<!-- HISTORIQUE DES TRAJETS -->

<!-- START HERO-->
<section class="hero">
    <div class="hero-content">
        <h1 class="hero-title">Mon historique</h1>
    </div>
</section>
<!-- END HERO-->

<div class="container mt-4 mb-4">

    <!-- Statistiques rapides -->
    <div class="row mb-4 gy-3">
        <?php if (in_array($_SESSION['user_type'] ?? 'passenger', ['both'])): ?>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h2><?php echo count($driverCarpools) + count($passengerReservations) ?></h2>
                    <p>Total trajets</p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (in_array($_SESSION['user_type'] ?? 'passenger', ['driver', 'both'])): ?>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h2><?php echo count($driverCarpools) ?></h2>
                    <p>Mes trajets proposés</p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (in_array($_SESSION['user_type'] ?? 'passenger', ['passenger', 'both'])): ?>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h2><?php echo count($passengerReservations) ?></h2>
                    <p>Mes réservations</p>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h2>
                        <?php
                            $ecoCount = 0;
                            foreach ($driverCarpools as $carpool) {
                                if ($carpool['is_ecological']) {
                                    $ecoCount++;
                                }

                            }
                            echo $ecoCount;
                        ?>
                    </h2>
                    <p>Mes trajets écologiques</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="/history" class="row g-3">
                        <div class="col-md-3">
                            <select name="filter_type" class="form-control">
                                <option value="">Tous les trajets</option>
                                <?php if (in_array($_SESSION['user_type'] ?? 'passenger', ['driver', 'both'])): ?>
                                <option value="driver"<?php echo($_GET['filter_type'] ?? '') === 'driver' ? 'selected' : '' ?>>
                                    Conducteur uniquement
                                </option>
                                <?php endif?>


                                <?php if (in_array($_SESSION['user_type'] ?? 'passenger', ['passenger', 'both'])): ?>
                                <option value="passenger"<?php echo($_GET['filter_type'] ?? '') === 'passenger' ? 'selected' : '' ?>>
                                    Passager uniquement
                                </option>
                                <?php endif?>
                                <option value="ecological"<?php echo($_GET['filter_type'] ?? '') === 'ecological' ? 'selected' : '' ?>>
                                    Ecologiques uniquement
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="filter_status" class="form-control">
                                <option value="">Tous les statuts</option>
                                <option value="completed"<?php echo($_GET['filter_status'] ?? '') === 'completed' ? 'selected' : '' ?>>
                                    Terminés
                                </option>
                                <option value="canceled"<?php echo($_GET['filter_status'] ?? '') === 'canceled' ? 'selected' : '' ?>>
                                    Annulés
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="month" name="filter_month" class="form-control"value="<?php echo $_GET['filter_month'] ?? '' ?>" placeholder="Mois">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn w-100">Filtrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Trajets en tant que conducteur -->
    <?php if (in_array($_SESSION['user_type'] ?? 'passenger', ['driver', 'both'])): ?>
    <?php if (! empty($driverCarpools)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Mes trajets en tant que conducteur</h3>
                </div>
                <div class="card-body">
                    <?php foreach ($driverCarpools as $carpool): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h4>
                                        <?php echo htmlspecialchars($carpool['departure']) ?>
                                        ->
                                        <?php echo htmlspecialchars($carpool['arrival']) ?>
                                        <?php if ($carpool['is_ecological']): ?>
                                        <span class="badge bg-success ms-2">Écologique</span>
                                        <?php endif; ?>
                                    </h4>
                                    <p class="mb-1">
                                        <strong>Date :</strong>
                                        <?php echo date('d/m/Y à H:i', strtotime($carpool['departure_time'])) ?>
                                    </p>
                                    <p class="mb-1">
                                        <strong>Places :</strong>
                                        <?php echo $carpool['seats_available'] ?> |
                                        <strong>Prix :</strong>
                                        <?php echo $carpool['price_per_seat'] ?> crédits/place
                                    </p>
                                </div>
                                <div class="col-md-3 text-center">
                                    <?php
                                        $statusBadges = [
                                            'scheduled'   => '<span class="badge bg-primary">Programmé</span>',
                                            'in_progress' => '<span class="badge bg-warning">En cours</span>',
                                            'finished'    => '<span class="badge bg-success">Terminé</span>',
                                            'canceled'    => '<span class="badge bg-danger">Annulé</span>',
                                        ];
                                        echo $statusBadges[$carpool['status']] ?? '<span class="badge bg-secondary">Inconnu</span>';
                                    ?>
                                </div>
                                <div class="col-md-3 text-end">
                                    <a href="/carpools/<?php echo $carpool['carpool_id'] ?>" class="btn">
                                        Voir détails
                                    </a>
                                    <?php if (in_array($carpool['status'], ['finished', 'completed'])): ?>
                                    <a href="/my-carpools/passengers" class="btn">
                                        Mes passagers
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>

    <!-- Trajets en tant que passager -->
    <?php if (in_array($_SESSION['user_type'] ?? 'passenger', ['passenger', 'both'])): ?>
    <?php if (! empty($passengerReservations)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Mes trajets en tant que passager</h3>
                </div>
                <div class="card-body">
                    <?php foreach ($passengerReservations as $reservation): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h4>
                                        <?php echo htmlspecialchars($reservation['departure']) ?>
                                        ->
                                        <?php echo htmlspecialchars($reservation['arrival']) ?>
                                    </h4>
                                    <p class="mb-1">
                                        <strong>Date :</strong>
                                        <?php echo date('d/m/Y à H:i', strtotime($reservation['departure_time'])) ?>
                                    </p>
                                    <p class="mb-1">
                                        <strong>Conducteur :</strong>
                                        <?php echo htmlspecialchars($reservation['driver_username']) ?> |
                                        <strong>Places :</strong>
                                        <?php echo $reservation['seats_booked'] ?> |
                                        <strong>Payé :</strong>
                                        <?php echo $reservation['amount_paid'] ?> crédits
                                    </p>
                                </div>
                                <div class="col-md-3 text-center">
                                    <?php
                                        $reservationBadges = [
                                            'confirmed'                       => '<span class="badge bg-primary">Confirmé</span>',
                                            'completed'                       => '<span class="badge bg-success">Terminé</span>',
                                            'canceled'                        => '<span class="badge bg-danger">Annulé</span>',
                                            'awaiting_passenger_confirmation' => '<span class="badge bg-warning">En attente</span>',
                                            'disputed'                        => '<span class="badge bg-danger">Litige</span>',
                                        ];
                                        echo $reservationBadges[$reservation['status']] ?? '<span class="badge bg-secondary">Inconnu</span>';
                                    ?>
                                </div>
                                <div class="col-md-3 text-end">
                                    <a href="/reservations/<?php echo $reservation['reservation_id'] ?>" class="btn mb-3">
                                        Mes reservations
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>

    <!-- Message si aucun trajet -->
    <?php if (empty($driverCarpools) && empty($passengerReservations)): ?>
    <div class="row">
        <div class="col-12">
            <div class="alert text-center">
                <h3>Aucun trajet dans votre historique</h3>
                <p>Commencez votre aventure Ecoride dès maintenant !</p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Actions rapides -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Actions rapides</h3>
                </div>
                <div class="card-body">
                    <div class="row gy-2">
                        <?php if (in_array($_SESSION['user_type'] ?? 'passenger', ['passenger', 'both'])): ?>
                        <div class="col-md-3">
                            <a href="/carpools" class="btn w-100">
                                Rechercher un trajet
                            </a>
                        </div>
                        <?php endif; ?>

                        <?php if (in_array($_SESSION['user_type'] ?? 'passenger', ['driver', 'both'])): ?>
                        <div class="col-md-3">
                            <a href="/carpools/create" class="btn w-100">
                                Créer un covoiturage
                            </a>
                        </div>
                        <?php endif; ?>

                        <?php if (in_array($_SESSION['user_type'] ?? 'passenger', ['passenger', 'both'])): ?>
                        <div class="col-md-3">
                            <a href="/reservations" class="btn w-100">
                                Mes réservations actives
                            </a>
                        </div>
                        <?php endif; ?>

                        <div class="col-md-3">
                            <a href="/dashboard" class="btn w-100">
                                Retour au tableau de bord
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>