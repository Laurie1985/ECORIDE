<!-- MES PASSAGERS -->

<!-- START HERO-->
<section class="hero">
    <div class="hero-content">
        <h1 class="hero-title">Réservations sur mes trajets</h1>
    </div>
</section>
<!-- END HERO-->

<div class="container mt-4 mb-4">

    <?php if (empty($carpoolsWithPassengers)): ?>
    <div class="row card mb-5">
        <h4 class="mt-4 mb-4">Aucun trajet avec des réservations</h4>
        <p>Dès que des passagers réserveront vos covoiturages, vous les verrez ici.</p>
        <div class="d-flex justify-content-center">
            <a href="/carpools/create" class="btn mt-4 mb-4">Créer un covoiturage</a>
        </div>
    </div>
    <?php else: ?>

        <!-- Tableau récapitulatif par covoiturage -->
        <?php foreach ($carpoolsWithPassengers as $carpool): ?>
        <div class="card mb-3">
            <div class="card-header">
                <h3><?php echo htmlspecialchars($carpool['departure']) ?> ->
                <?php echo htmlspecialchars($carpool['arrival']) ?></h3>
                <p><?php echo date('d/m/Y H:i', strtotime($carpool['departure_time'])) ?></p>
            </div>
            <div class="card-body">
                <!-- Réservations confirmées -->
                <?php if (! empty($carpool['confirmed_reservations'])): ?>
                <h4>Passagers confirmés</h4>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Passager</th>
                                <th>Places</th>
                                <th>Montant</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($carpool['confirmed_reservations'] as $reservation): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($reservation['passenger_username']) ?></td>
                                <td><?php echo $reservation['seats_booked'] ?></td>
                                <td><?php echo $reservation['amount_paid'] ?> crédits</td>
                                <td><span class="badge bg-success">Confirmé</span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>

                <!-- Annulations -->
                <?php if (! empty($carpool['canceled_reservations'])): ?>
                <h4 class="mt-3">Annulations</h4>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Passager</th>
                                <th>Places annulées</th>
                                <th>Date d'annulation</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($carpool['canceled_reservations'] as $cancellation): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($cancellation['passenger_username']) ?></td>
                                <td><?php echo $cancellation['seats_booked'] ?></td>
                                <td><?php echo date('d/m/Y', strtotime($cancellation['cancellation_date'])) ?></td>
                                <td><span class="badge bg-danger">Annulé</span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>

                <!-- Si aucune réservation sur ce covoiturage -->
                <?php if (empty($carpool['confirmed_reservations']) && empty($carpool['canceled_reservations'])): ?>
                <p class="text-muted">Aucune réservation pour ce trajet</p>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>