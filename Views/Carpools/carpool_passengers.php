<!-- PASSAGERS D'UN COVOITURAGE -->

<!-- START HERO-->
<section class="hero">
    <div class="hero-content">
        <h1 class="hero-title">Passagers du trajet</h1>
    </div>
</section>
<!-- END HERO-->

<div class="container mt-4 mb-4">
    <!-- Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo htmlspecialchars($_SESSION['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php echo htmlspecialchars($_SESSION['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Retour -->
    <div class="mb-3">
        <a href="/my-carpools" class="btn btn-secondary">
            Retour à mes covoiturages
        </a>
    </div>

    <!-- Informations du trajet -->
    <div class="card mb-4">
        <div class="card-header">
            <h3>Informations du trajet</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Trajet :</strong>
                        <?php echo htmlspecialchars($carpool['departure']) ?> -
                        <?php echo htmlspecialchars($carpool['arrival']) ?>
                    </p>
                    <p><strong>Date :</strong>
                        <?php echo date('d/m/Y', strtotime($carpool['departure_time'])) ?>
                    </p>
                    <p><strong>Départ :</strong>
                        <?php echo date('H:i', strtotime($carpool['departure_time'])) ?>
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>Places totales :</strong>
                        <?php echo $carpool['seats_total'] ?? $carpool['seats_available'] ?>
                    </p>
                    <p><strong>Places réservées :</strong>
                        <?php echo count($confirmedPassengers) ?>
                    </p>
                    <p><strong>Statut :</strong>
                        <?php
                            $statusLabels = [
                                'scheduled'   => '<span class="badge bg-primary">Programmé</span>',
                                'in_progress' => '<span class="badge bg-warning">En cours</span>',
                                'finished'    => '<span class="badge bg-success">Terminé</span>',
                                'canceled'    => '<span class="badge bg-danger">Annulé</span>',
                            ];
                            echo $statusLabels[$carpool['status']] ?? $carpool['status'];
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Passagers confirmés -->
    <?php if (! empty($confirmedPassengers)): ?>
    <div class="card mb-4">
        <div class="card-header">
            <h3>Passagers confirmés (<?php echo count($confirmedPassengers) ?>)</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Passager</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Places</th>
                            <th>Montant</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($confirmedPassengers as $passenger): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($passenger['username']) ?></td>
                            <td><?php echo htmlspecialchars($passenger['email']) ?></td>
                            <td><?php echo htmlspecialchars($passenger['phone']) ?></td>
                            <td><?php echo $passenger['seats_booked'] ?> place(s)</td>
                            <td><?php echo $passenger['amount_paid'] ?> crédits</td>
                            <td>
                                <?php
                                    $statusBadges = [
                                        'confirmed'                       => '<span class="badge bg-success">Confirmé</span>',
                                        'awaiting_passenger_confirmation' => '<span class="badge bg-warning">En attente</span>',
                                        'completed'                       => '<span class="badge bg-primary">Terminé</span>',
                                    ];
                                    echo $statusBadges[$passenger['status']] ?? $passenger['status'];
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="alert alert-info">
        Aucun passager confirmé pour ce trajet.
    </div>
    <?php endif; ?>

    <!-- Passagers annulés -->
    <?php if (! empty($canceledPassengers)): ?>
    <div class="card mb-4">
        <div class="card-header">
            <h3>Réservations annulées (<?php echo count($canceledPassengers) ?>)</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Passager</th>
                            <th>Places</th>
                            <th>Montant remboursé</th>
                            <th>Date d'annulation</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($canceledPassengers as $passenger): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($passenger['username']) ?></td>
                            <td><?php echo $passenger['seats_booked'] ?> place(s)</td>
                            <td><?php echo $passenger['amount_paid'] ?> crédits</td>
                            <td>
                                <?php
                                    echo $passenger['cancellation_date']
                                        ? date('d/m/Y H:i', strtotime($passenger['cancellation_date']))
                                        : 'N/A';
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Actions -->
    <div class="text-center">
        <a href="/my-carpools" class="btn">
            Retour à mes covoiturages
        </a>
    </div>
</div>