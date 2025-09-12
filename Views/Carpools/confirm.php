<!-- VUE DE CONFIRMATION DE RÉSERVATION (US 6) -->

<!-- START HERO-->
<section class="hero">
    <div class="hero-content">
        <h1 class="hero-title">Confirmation de réservation</h1>
    </div>
</section>
<!-- END HERO-->

<div class="container mt-4 mb-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header">
                    <h2>
                        Confirmation de réservation
                    </h2>
                </div>

                <div class="card-body">
                    <div class="alert">
                        <h4>Double confirmation requise</h4>
                        <p class="mb-0">Veuillez confirmer que vous souhaitez réserver ce covoiturage avec les détails ci-dessous.</p>
                    </div>

                    <!-- Détails du covoiturage -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3>Détails du trajet</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Départ :</strong>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               <?php echo htmlspecialchars($carpool['departure']) ?></p>
                                    <p><strong>Arrivée :</strong>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             <?php echo htmlspecialchars($carpool['arrival']) ?></p>
                                    <p><strong>Date de départ :</strong>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               <?php echo date('d/m/Y H:i', strtotime($carpool['departure_time'])) ?></p>
                                    <p><strong>Date d'arrivée :</strong>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               <?php echo date('d/m/Y H:i', strtotime($carpool['arrival_time'])) ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Conducteur :</strong>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         <?php echo htmlspecialchars($carpool['username'] ?? 'Non spécifié') ?></p>
                                    <p><strong>Véhicule :</strong>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           <?php echo htmlspecialchars(($carpool['name_brand'] ?? 'Marque inconnue') . ' ' . ($carpool['model'] ?? 'Modèle inconnu')) ?></p>
                                    <p><strong>Type d'énergie :</strong>
                                        <?php echo htmlspecialchars($carpool['energy_type']) ?>
                                        <?php if ($carpool['energy_type'] === 'electric'): ?>
                                            <span class="ms-2">
                                                <img src="/assets/images/pictoEcologie.png" alt="Écologique" class="picto me-1">
                                            </span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Détails de la réservation -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3>Votre réservation</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <h4>Nombre de places</h4>
                                        <p><strong><?php echo $booking_data['seats_booked'] ?></strong></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <h4>Prix par place</h4>
                                        <p><strong><?php echo $carpool['price_per_seat'] ?> crédits</strong></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <h4>Total à payer</h4>
                                        <h4><?php echo $booking_data['total_price'] ?> crédits</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vérification des crédits -->
                    <?php
                        $user             = \App\Models\User::find($_SESSION['user_id']);
                        $hasEnoughCredits = $user['credits'] >= $booking_data['total_price'];
                    ?>

                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h6>Vos crédits actuels :</h6>
                                    <span class="fs-5 fw-bold                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   <?php echo $hasEnoughCredits ? 'text-success' : 'text-danger' ?>">
                                        <?php echo $user['credits'] ?> crédits
                                    </span>
                                </div>
                                <div class="col-md-6">
                                    <p>Crédits après réservation :</p>
                                    <span class="fs-5 fw-bold                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   <?php echo $hasEnoughCredits ? 'text-info' : 'text-danger' ?>">
                                        <?php echo $user['credits'] - $booking_data['total_price'] ?> crédits
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex justify-content-between">
                        <a href="/carpools/<?php echo $carpool['carpool_id'] ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour aux détails
                        </a>

                        <?php if ($hasEnoughCredits): ?>
                            <form method="POST" action="/carpools/<?php echo $carpool['carpool_id'] ?>/book" class="d-inline">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token) ?>">
                                <input type="hidden" name="seats_booked" value="<?php echo $booking_data['seats_booked'] ?>">
                                <input type="hidden" name="confirmed" value="true">

                                <button type="submit" class="btn btn-success btn-lg"
                                        onclick="return confirm('Êtes-vous sûr de vouloir confirmer cette réservation ?')">
                                    <i class="fas fa-check"></i>
                                    Confirmer la réservation (<?php echo $booking_data['total_price'] ?> crédits)
                                </button>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-success btn-lg" disabled>
                                <i class="fas fa-times"></i>
                                Crédits insuffisants
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
