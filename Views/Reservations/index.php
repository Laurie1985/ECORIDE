<!-- MES RÉSERVATIONS -->

<!-- START HERO-->
<section class="hero">
    <div class="hero-content">
        <h1 class="hero-title">Mes réservations</h1>
    </div>
</section>
<!-- END HERO-->

<div class="container mt-4 mb-4">
    <!-- Affichage des messages -->
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

    <!-- Navigation rapide -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1>Vos trajets</h1>
                <a href="/carpools" class="btn">
                    Nouvelle recherche
                </a>
            </div>
        </div>
    </div>

    <!-- Réservations en attente de confirmation -->
    <?php if (! empty($awaitingConfirmation)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Trajets à confirmer</h3>
                </div>
                <div class="card-body">
                    <p class="mb-3">Vous avez des trajets terminés qui nécessitent votre confirmation :</p>
                    <?php foreach ($awaitingConfirmation as $reservation): ?>
                    <div class="alert alert-info">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?php echo htmlspecialchars($reservation['departure']) ?> ->
                                <?php echo htmlspecialchars($reservation['arrival']) ?></strong>
                                <br><small>Trajet du
                                    <?php echo date('d/m/Y', strtotime($reservation['departure_time'])) ?></small>
                            </div>
                            <div>
                                <a href="/reservations/confirm/<?php echo $reservation['reservation_id'] ?>"class="btn">
                                    Confirmer le trajet
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (empty($reservations) && empty($awaitingConfirmation)): ?>
    <!-- Aucune réservation -->
    <div class="row">
        <div class="col-12">
            <div class="card text-center">
                <div class="card-body py-5">
                    <h3 class="mb-3">Aucune réservation</h3>
                    <p class="text-muted mb-4">Vous n'avez pas encore réservé de covoiturage.</p>
                    <a href="/carpools" class="btn">
                        Rechercher un covoiturage
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>

    <!-- Liste des réservations -->
    <div class="card">
        <?php
            // Grouper les réservations par statut pour l'affichage
            $groupedReservations = [];
            foreach ($reservations as $reservation) {
                $status = $reservation['status'];
                if (! isset($groupedReservations[$status])) {
                    $groupedReservations[$status] = [];
                }
                $groupedReservations[$status][] = $reservation;
            }

            // Ordre d'affichage des statuts
            $statusOrder  = ['confirmed', 'awaiting_passenger_confirmation', 'completed', 'canceled', 'disputed'];
            $statusLabels = [
                'confirmed'                       => 'Réservations confirmées',
                'awaiting_passenger_confirmation' => 'En attente de confirmation',
                'completed'                       => 'Trajets terminés',
                'canceled'                        => 'Réservations annulées',
                'disputed'                        => 'Litiges en cours',
            ];
        ?>


        <?php foreach ($statusOrder as $status): ?>
            <?php if (isset($groupedReservations[$status]) && ! empty($groupedReservations[$status])): ?>
            <div class="mb-4">
                <h3 class="card-header"><?php echo $statusLabels[$status] ?></h3>

                <?php foreach ($groupedReservations[$status] as $reservation): ?>

                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <!-- Informations du trajet -->
                            <div class="col-md-6">
                                <h4 class="mb-2">
                                    <?php echo htmlspecialchars($reservation['departure']) ?>
                                    ->
                                    <?php echo htmlspecialchars($reservation['arrival']) ?>
                                </h4>
                                <p class="mb-1">
                                    <strong>Date :</strong>
                                    <?php echo date('d/m/Y', strtotime($reservation['departure_time'])) ?>
                                </p>
                                <p class="mb-1">
                                    <strong>Départ :</strong>
                                    <?php echo date('H:i', strtotime($reservation['departure_time'])) ?>
                                </p>
                                <p class="mb-1">
                                    <strong>Conducteur :</strong>
                                    <?php echo htmlspecialchars($reservation['driver_username']) ?>
                                </p>
                            </div>

                            <!-- Détails de la réservation -->
                            <div class="col-md-3">
                                <p class="mb-1">
                                    <strong>Places :</strong>
                                    <?php echo $reservation['seats_booked'] ?> place(s)
                                </p>
                                <p class="mb-1">
                                    <strong>Montant :</strong>
                                    <?php echo $reservation['amount_paid'] ?> crédits
                                </p>
                                <p class="mb-1">
                                    <strong>Statut :</strong>
                                    <?php
                                        $statusBadges = [
                                            'confirmed'                       => '<span class="badge bg-success">Confirmé</span>',
                                            'awaiting_passenger_confirmation' => '<span class="badge bg-warning">À confirmer</span>',
                                            'completed'                       => '<span class="badge bg-primary">Terminé</span>',
                                            'canceled'                        => '<span class="badge bg-danger">Annulé</span>',
                                            'disputed'                        => '<span class="badge bg-secondary">Litige</span>',
                                        ];
                                        echo $statusBadges[$reservation['status']] ?? '<span class="badge bg-secondary">Inconnu</span>';
                                    ?>
                                </p>
                                <?php if ($reservation['status'] === 'canceled' && $reservation['cancellation_date']): ?>
                                <p class="mb-0">
                                    <small class="text-muted">
                                        <strong>Annulé le :</strong><?php echo date('d/m/Y', strtotime($reservation['cancellation_date'])) ?>
                                    </small>
                                </p>
                                <?php endif; ?>
                            </div>

                            <!-- Actions -->
                            <div class="col-md-3 text-end">
                                <?php if ($reservation['status'] === 'confirmed'): ?>
                                <?php
                                    $now           = time();
                                    $departureTime = strtotime($reservation['departure_time']);
                                    $canCancel     = $departureTime > $now; //Annulation possible tant que pas encore parti
                                ?>

                                <?php if ($canCancel): ?>
                                    <button type="button"
                                            class="btn mb-2"
                                            data-bs-toggle="modal"
                                            data-bs-target="#cancelModal<?php echo $reservation['reservation_id'] ?>">
                                        Annuler
                                    </button>
                                <?php else: ?>
                                    <small class="text-muted">Annulation impossible<br>(le trajet est déjà commencé)</small>
                                <?php endif; ?>

                                <?php elseif ($reservation['status'] === 'awaiting_passenger_confirmation'): ?>
                                    <a href="/reservations/confirm/<?php echo $reservation['reservation_id'] ?>" class="btn">
                                        Confirmer le trajet
                                    </a>

                                <?php elseif ($reservation['status'] === 'completed'): ?>
                                    <small class="text-success">Trajet confirmé</small>

                                <?php endif; ?>

                                <!-- Contact du conducteur (si réservation active) -->
                                <?php if (in_array($reservation['status'], ['confirmed', 'awaiting_passenger_confirmation']) && ! empty($reservation['driver_phone'])): ?>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            Contact :
                                            <?php echo htmlspecialchars($reservation['driver_phone']) ?>
                                        </small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Modal d'annulation pour chaque réservation -->
                <?php if ($reservation['status'] === 'confirmed'): ?>
                <div class="modal fade" id="cancelModal<?php echo $reservation['reservation_id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="modal-title">Annuler la réservation</h3>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST" action="/reservations/<?php echo $reservation['reservation_id'] ?>/cancel">
                                <div class="modal-body">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?>">

                                    <div class="alert alert-warning">
                                        <strong>Attention :</strong> Vous allez annuler votre réservation.
                                        Vos crédits seront remboursés automatiquement.
                                    </div>

                                    <p><strong>Trajet :</strong>
                                        <?php echo htmlspecialchars($reservation['departure']) ?> →<?php echo htmlspecialchars($reservation['arrival']) ?>
                                    </p>
                                    <p><strong>Date :</strong>
                                        <?php echo date('d/m/Y H:i', strtotime($reservation['departure_time'])) ?>
                                    </p>
                                    <p><strong>Montant à rembourser :</strong>
                                        <?php echo $reservation['amount_paid'] ?> crédits
                                    </p>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn">
                                        Annuler ma réservation
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        <?php endforeach; ?>

    <!-- Statistiques rapides -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Vos statistiques</h3>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <?php
                            $stats = [
                                'total'       => count($reservations),
                                'confirmed'   => 0,
                                'completed'   => 0,
                                'canceled'    => 0,
                                'disputed'    => 0,
                                'total_spent' => 0,
                            ];

                            foreach ($reservations as $reservation) {
                                $stats[$reservation['status']]++;

                                if (isset($stats[$reservation['status']])) {
                                    $stats[$reservation['status']]++;
                                }
                                //Compter tous les crédits dépensés sauf les annulations qui sont remboursées
                                if ($reservation['status'] !== 'canceled') {
                                    $stats['total_spent'] += $reservation['amount_paid'];
                                }
                            }
                        ?>

                        <div class="col-md-2">
                            <h4><?php echo $stats['total'] ?></h4>
                            <p class="text-muted">Total réservations</p>
                        </div>
                        <div class="col-md-2">
                            <h4><?php echo $stats['confirmed'] ?></h4>
                            <p class="text-muted">En cours</p>
                        </div>
                        <div class="col-md-2">
                            <h4><?php echo $stats['completed'] ?></h4>
                            <p class="text-muted">Terminés</p>
                        </div>
                        <div class="col-md-2">
                            <h4><?php echo $stats['canceled'] ?></h4>
                            <p class="text-muted">Annulés</p>
                        </div>
                        <div class="col-md-2">
                            <h4><?php echo $stats['total_spent'] ?></h4>
                            <p class="text-muted">Crédits dépensés</p>
                        </div>
                        <div class="col-md-2">
                            <h4><?php echo $user['credits']; ?></h4>
                            <p class="text-muted">Crédits restants</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Navigation -->
    <div class="row mt-2 mb-4">
        <div class="col-12 text-center">
            <a href="/dashboard" class="btn">
                Tableau de bord
            </a>
            <a href="/carpools" class="btn">
                Rechercher un covoiturage
            </a>
        </div>
    </div>
</div>