<!-- GESTION DES PLAINTES EMPLOYÉ -->

<!-- START HERO-->
<section class="hero">
    <div class="hero-content">
        <h1 class="hero-title">Traiter les litiges</h1>
    </div>
</section>
<!-- END HERO-->

<div class="container mt-4 mb-4">

    <!-- Navigation -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-2">Litiges en cours</h3>
                            <p class="mb-0"><?php echo count($complaints) ?> litiges à traiter</p>
                        </div>
                        <a href="/employee" class="btn">
                            Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des plaintes -->
    <?php if (empty($complaints)): ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <h3>Aucun litige en cours</h3>
                        <p>Tous les trajets se sont bien déroulés !</p>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($complaints as $complaint): ?>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h4 class="mb-2">
                                        Litige - Réservation :
                                        <?php echo $complaint['reservation_id'] ?>
                                    </h4>
                                    <small>
                                        Trajet :
                                        <?php echo htmlspecialchars($complaint['departure']) ?> -><?php echo htmlspecialchars($complaint['arrival']) ?>
                                    </small>
                                </div>
                                <div class="col-md-4 text-end">
                                    <span class="badge bg-danger">À traiter</span>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <!-- Informations du trajet -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h4>Passager :</h4>
                                    <p class="mb-1"><?php echo htmlspecialchars($complaint['passenger_username']) ?></p>
                                    <p><?php echo htmlspecialchars($complaint['passenger_email']) ?></p>
                                </div>
                                <div class="col-md-6">
                                    <h4>Conducteur :</h4>
                                    <p class="mb-1"><?php echo htmlspecialchars($complaint['driver_username']) ?></p>
                                    <p><?php echo htmlspecialchars($complaint['driver_email']) ?></p>
                                </div>
                            </div>

                            <!-- Détails financiers -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h4>Montant payé :</h4>
                                    <p><?php echo $complaint['amount_paid'] ?> crédits</p>
                                </div>
                                <div class="col-md-6">
                                    <h4>Date du trajet :</h4>
                                    <p><?php echo date('d/m/Y H:i', strtotime($complaint['departure_time'])) ?></p>
                                </div>
                            </div>

                            <!-- Commentaire du litige -->
                            <div class="mb-4">
                                <h4>Motif du litige:</h4>
                                <div class="alert alert-warning">
                                    <?php echo nl2br(htmlspecialchars($complaint['complaint_comment'])) ?>
                                </div>
                            </div>

                            <!-- Actions de résolution -->
                            <div class="row">
                                <div class="col-md-6">
                                    <!-- Rembourser le passager -->
                                    <div class="card card-action">
                                        <div class="card-header">
                                            <h4 class="mb-0">Rembourser le passager</h4>
                                        </div>
                                        <div class="card-body">
                                            <p class="small text-muted">
                                                Le passager sera remboursé de
                                                <?php echo $complaint['amount_paid'] ?> crédits.
                                                Le conducteur ne sera pas payé.
                                            </p>
                                            <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#refundModal<?php echo $complaint['reservation_id'] ?>">
                                                Rembourser passager
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <!-- Payer le conducteur -->
                                    <div class="card card-action">
                                        <div class="card-header">
                                            <h4 class="mb-0">Payer le conducteur</h4>
                                        </div>
                                        <div class="card-body">
                                            <p class="small text-muted">
                                                Le conducteur recevra
                                                <?php echo $complaint['amount_paid'] - 2 ?> crédits.
                                                Le litige sera considéré non fondé.
                                            </p>
                                            <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#payDriverModal<?php echo $complaint['reservation_id'] ?>">
                                                Payer conducteur
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Remboursement -->
            <div class="modal fade" id="refundModal<?php echo $complaint['reservation_id'] ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title">Rembourser le passager</h3>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="POST" action="/employee/complaints/<?php echo $complaint['reservation_id'] ?>/resolve">
                            <div class="modal-body">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?>">
                                <input type="hidden" name="action" value="refund">

                                <div class="alert">
                                    <p><strong>Action :</strong> Le passager sera remboursé de
                                        <?php echo $complaint['amount_paid'] ?> crédits.
                                        Le conducteur ne recevra rien.
                                    </p>
                                </div>

                                <div class="mb-3">
                                    <label for="notes_refund<?php echo $complaint['reservation_id'] ?>" class="form-label">
                                        Motivation de la décision :
                                    </label>
                                    <textarea class="form-control" name="notes" id="notes_refund<?php echo $complaint['reservation_id'] ?>" rows="3" required placeholder="Expliquez la décision..."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn">Confirmer le remboursement</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Paiement conducteur -->
            <div class="modal fade" id="payDriverModal<?php echo $complaint['reservation_id'] ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title">Payer le conducteur</h3>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="POST" action="/employee/complaints/<?php echo $complaint['reservation_id'] ?>/resolve">
                            <div class="modal-body">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?>">
                                <input type="hidden" name="action" value="pay_driver">

                                <div class="alert">
                                    <p><strong>Action :</strong> Le conducteur recevra
                                        <?php echo $complaint['amount_paid'] - 2 ?> crédits.
                                        Le litige sera marqué comme non fondé.
                                    </p>
                                </div>

                                <div class="mb-3">
                                    <label for="notes_pay<?php echo $complaint['reservation_id'] ?>" class="form-label">
                                        Motivation de la décision :
                                    </label>
                                    <textarea class="form-control" name="notes" id="notes_pay<?php echo $complaint['reservation_id'] ?>" rows="3" required placeholder="Expliquez la décision..."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn">Confirmer le paiement</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>