<!-- GESTION DES AVIS EMPLOYÉ -->

<!-- START HERO-->
<section class="hero">
    <div class="hero-content">
        <h1 class="hero-title">Modération des avis</h1>
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
                            <h3 class="mb-2">Avis en attente de validation</h3>
                            <p class="mb-0"><?php echo count($reviews) ?> avis à traiter</p>
                        </div>
                        <a href="/employee" class="btn">
                            Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des avis -->
    <?php if (empty($reviews)): ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <h3>Aucun avis en attente</h3>
                        <p>Tous les avis ont été traités !</p>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($reviews as $review): ?>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h4 class="mb-2">
                                        Avis pour le covoiturage :
                                        <?php echo $review['carpool_id'] ?>
                                    </h4>
                                    <small>
                                        crée le
                                        <?php echo $review['created_at']->toDateTime()->format('d/m/Y H:i') ?>
                                    </small>
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="rating">
                                        <h3 class="ms-2 fw-bold"><?php echo $review['rating'] ?>/5</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h4>Évaluateur :</h4>
                                    <p class="mb-2"><strong><?php echo htmlspecialchars($review['reviewer_username']) ?></strong></p>
                                    <p>ID :
                                        <?php echo $review['reviewer_id'] ?></p>
                                </div>
                                <div class="col-md-6">
                                    <h4>Conducteur évalué :</h4>
                                    <p class="mb-2"><strong><?php echo htmlspecialchars($review['reviewed_user_username']) ?></strong></p>
                                    <p>ID :
                                        <?php echo $review['reviewed_user_id'] ?></p>
                                </div>
                            </div>

                            <div class="mb-3">
                                <h4>Commentaire :</h4>
                                <p class="border p-3 bg-light rounded"><?php echo htmlspecialchars($review['comment']) ?></p>
                            </div>

                            <div class="row ">
                                <!-- Approuver -->
                                <div class="col-md-6 mb-2">
                                    <form method="POST" action="/employee/reviews/<?php echo $review['_id'] ?>/approve" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?>">
                                        <button type="submit" class="btn" onclick="return confirm('Approuver cet avis ?')">
                                            Approuver
                                        </button>
                                    </form>
                                </div>

                                <!-- Rejeter -->
                                <div class="col-md-6">
                                    <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#rejectModal<?php echo $review['_id'] ?>">
                                        Rejeter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal de rejet -->
            <div class="modal fade" id="rejectModal<?php echo $review['_id'] ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title">Rejeter l'avis</h3>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="POST" action="/employee/reviews/<?php echo $review['_id'] ?>/reject">
                            <div class="modal-body">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?>">
                                <div class="mb-3">
                                    <label for="reason<?php echo $review['_id'] ?>" class="form-label">Raison du rejet :</label>
                                    <select class="form-select" name="reason" id="reason<?php echo $review['_id'] ?>" required>
                                        <option value="">Sélectionner une raison</option>
                                        <option value="Contenu inapproprié">Contenu inapproprié</option>
                                        <option value="Langage offensant">Langage offensant</option>
                                        <option value="Spam ou faux avis">Spam ou faux avis</option>
                                        <option value="Hors sujet">Hors sujet</option>
                                        <option value="Autre">Autre</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn">Rejeter l'avis</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>