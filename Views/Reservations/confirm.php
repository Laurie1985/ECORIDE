<!-- VUE DE CONFIRMATION POST-TRAJET -->

<!-- START HERO-->
<section class="hero">
    <div class="hero-content">
        <h1 class="hero-title">Confirmer le trajet effectué</h1>
    </div>
</section>
<!-- END HERO-->

<div class="container mt-4 mb-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header">
                    <h2>Validation du trajet terminé</h2>
                </div>

                <div class="card-body">
                    <div class="alert alert-info">
                        <h4>Trajet terminé</h4>
                        <p class="mb-0">Le conducteur a indiqué que le trajet est terminé. Merci de confirmer que tout s'est bien passé pour finaliser la transaction.</p>
                    </div>

                    <!-- Récapitulatif du trajet effectué -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3>Récapitulatif du trajet</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Départ :</strong>
                                        <?php echo htmlspecialchars($carpool['departure']) ?>
                                    </p>
                                    <p><strong>Arrivée :</strong>
                                        <?php echo htmlspecialchars($carpool['arrival']) ?>
                                    </p>
                                    <p><strong>Date de départ :</strong>
                                        <?php echo date('d/m/Y H:i', strtotime($carpool['departure_time'])) ?>
                                    </p>
                                    <p><strong>Date d'arrivée :</strong>
                                        <?php echo date('d/m/Y H:i', strtotime($carpool['arrival_time'])) ?>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Conducteur :</strong>
                                        <?php echo htmlspecialchars($carpool['username'] ?? 'Non spécifié') ?>
                                        <?php if (isset($carpool['rating'])): ?>
                                            <span class="ms-2">
                                                <?php echo number_format($carpool['rating'], 1) ?>
                                            </span>
                                        <?php endif; ?>
                                    </p>
                                    <p><strong>Véhicule :</strong>
                                        <?php echo htmlspecialchars(($carpool['name_brand'] ?? 'Marque inconnue') . ' ' . ($carpool['model'] ?? 'Modèle inconnu')) ?>
                                    </p>
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

                    <!-- Détails de votre réservation -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3>Votre réservation</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h4>Places réservées</h4>
                                        <p><?php echo $reservation['seats_booked'] ?></p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h4>Prix payé</h4>
                                        <p><?php echo $reservation['amount_paid'] ?> crédits</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h4>Date de réservation</h4>
                                        <p><?php echo date('d/m/Y H:i', strtotime($reservation['created_at'])) ?></p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h4>Statut</h4>
                                        <span class="badge bg-warning">En attente confirmation</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulaire de confirmation -->
                    <form method="POST" action="/reservations/confirm/<?php echo $reservation['reservation_id'] ?>" id="confirmationForm">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token) ?>">

                        <div class="card mb-4">
                            <div class="card-header">
                                <h3>Comment s'est passé le trajet ?</h3>
                            </div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" name="trip_went_well" id="trip_good" value="1" required>
                                        <label class="form-check-label fs-5" for="trip_good">
                                            <p><strong>Le trajet s'est bien passé</strong></p>
                                        </label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" name="trip_went_well" id="trip_problem" value="0" required>
                                        <label class="form-check-label fs-5" for="trip_problem">
                                            <p><strong>Il y a eu un problème</strong></p>
                                        </label>
                                    </div>
                                </div>

                                <!-- Zone commentaire pour problèmes -->
                                <div id="problemDetails" class="d-none">
                                    <div class="alert alert-warning">
                                        <h4>Signaler un problème</h4>
                                        <p>Veuillez décrire le problème rencontré. Un employé examinera votre signalement avant de finaliser le paiement au conducteur.</p>
                                    </div>
                                    <div class="mb-3">
                                        <label for="comment" class="form-label">Description du problème *</label>
                                        <textarea class="form-control" id="comment" name="comment" rows="4"
                                                placeholder="Décrivez précisément ce qui s'est mal passé..."></textarea>
                                    </div>
                                </div>

                                <!-- Zone avis pour trajet réussi -->
                                <div id="reviewSection" class="d-none">
                                    <div class="alert alert-success">
                                        <h4>Laisser un avis</h4>
                                        <p>Votre avis aidera les futurs passagers à faire leur choix.</p>
                                    </div>
                                    <div class="mb-3">
                                        <label for="rating" class="form-label">Note du conducteur</label>
                                        <select class="form-select" id="rating" name="rating">
                                            <option value="">Choisir une note</option>
                                            <option value="5">Excellent</option>
                                            <option value="4">Très bien</option>
                                            <option value="3">Bien</option>
                                            <option value="2">Moyen</option>
                                            <option value="1">Décevant</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="review_comment" class="form-label">Commentaire sur le conducteur</label>
                                        <textarea class="form-control" id="review_comment" name="review_comment" rows="3"
                                                placeholder="Partagez votre expérience avec ce conducteur..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="/reservations" class="btn btn-secondary">
                                Retour à mes réservations
                            </a>

                            <div class="text-center">
                                <button type="submit" class="btn btn-success btn-lg" id="submitBtn">
                                    <i class="fas fa-check"></i>
                                    <span id="submitText">Confirmer le trajet</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>