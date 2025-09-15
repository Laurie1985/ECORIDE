<!-- START HERO-->
<section class="hero">
    <div class="hero-content">
        <h1 class="d-flex justify-content-center">Mes avis reçus</h1>
    </div>
</section>
<!-- END HERO-->

<div class="container mt-4 mb-4">

    <!-- Résumé de mes avis -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center text-center">
                        <div class="col-md-4">
                            <h3 class="mb-2"><?php echo htmlspecialchars($user['username']) ?></h3>
                            <p class="text-muted">Conducteur</p>
                        </div>
                        <div class="col-md-4">
                            <?php if ($totalReviews > 0): ?>
                                <div class="mb-2">
                                    <span class="display-4 text-warning"><?php echo $averageRating ?></span>
                                    <span class="text-muted">/5</span>
                                </div>
                                <p><strong>Note moyenne</strong></p>
                            <?php else: ?>
                                <div class="text-muted">
                                    <span class="h2">-</span>
                                    <p class="mt-2">Aucune note</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-2">
                                <h3><?php echo $totalReviews ?></h3>
                            </div>
                            <p><strong>Avis reçu<?php echo $totalReviews > 1 ? 's' : '' ?></strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($totalReviews > 0): ?>
        <!-- Liste de mes avis reçus -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3>Vos avis de passagers</h3>
                    </div>
                    <div class="card-body">
                        <?php foreach ($reviews as $review): ?>
                            <div class="border-bottom pb-3 mb-3">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="d-flex align-items-center mb-2">
                                            <strong class="me-3"><?php echo htmlspecialchars($review['reviewer_name']) ?></strong>
                                            <span class="badge bg-<?php echo $review['rating'] >= 4 ? 'success' : ($review['rating'] >= 3 ? 'warning' : 'danger') ?>">
                                                <?php echo $review['rating'] ?>/5
                                            </span>
                                        </div>
                                        <p class="mb-1"><?php echo nl2br(htmlspecialchars($review['comment'])) ?></p>
                                        <small class="text-muted">
                                            Covoiturage #<?php echo $review['carpool_id'] ?>
                                        </small>
                                    </div>
                                    <div class="col-md-4 text-md-end">
                                        <small class="text-muted">
                                            <?php echo $review['formatted_date'] ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques simples -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Répartition de vos notes</h4>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <?php
                                $ratingCounts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
                                foreach ($reviews as $review) {
                                    $ratingCounts[$review['rating']]++;
                                }
                            ?>

                            <div class="col">
                                <div class="h4 text-success"><?php echo $ratingCounts[5] ?></div>
                                <small class="text-muted">Excellent (5/5)</small>
                            </div>
                            <div class="col">
                                <div class="h4 text-success"><?php echo $ratingCounts[4] ?></div>
                                <small class="text-muted">Très bien (4/5)</small>
                            </div>
                            <div class="col">
                                <div class="h4 text-warning"><?php echo $ratingCounts[3] ?></div>
                                <small class="text-muted">Bien (3/5)</small>
                            </div>
                            <div class="col">
                                <div class="h4 text-warning"><?php echo $ratingCounts[2] ?></div>
                                <small class="text-muted">Moyen (2/5)</small>
                            </div>
                            <div class="col">
                                <div class="h4 text-danger"><?php echo $ratingCounts[1] ?></div>
                                <small class="text-muted">Décevant (1/5)</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- Aucun avis reçu -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <h4>Aucun avis reçu pour le moment</h4>
                        <p class="text-muted">Vous n'avez pas encore reçu d'avis de la part de vos passagers.</p>
                        <p class="text-muted">Continuez à offrir un excellent service pour recevoir vos premiers avis !</p>
                        <div class="mt-3">
                            <a href="/my-carpools" class="btn btn-outline-primary">
                                Mes covoiturages
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Navigation -->
    <div class="row mt-4">
        <div class="col-12 text-center">
            <a href="/dashboard" class="btn btn-outline-primary me-2">
                Tableau de bord
            </a>
            <a href="/my-carpools" class="btn btn-outline-secondary">
                Mes covoiturages
            </a>
        </div>
    </div>
</div>