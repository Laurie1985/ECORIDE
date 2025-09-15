<!-- AVIS SUR UN CONDUCTEUR -->

<!-- START HERO-->
<section class="hero">
    <div class="hero-content">
        <h1 class="d-flex justify-content-center">Les avis de votre conducteur</h1>
    </div>
</section>
<!-- END HERO-->

<div class="container mt-4 mb-4">

    <!-- Profil du conducteur -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            <img src="<?php echo htmlspecialchars($driver['photo']) ?>" alt="Photo de<?php echo htmlspecialchars($driver['username']) ?>" class="profil-photo">
                        </div>
                        <div class="col-md-6">
                            <h3 class="mb-2"><?php echo htmlspecialchars($driver['username']) ?></h3>
                            <p class="text-muted mb-1">
                                Membre depuis :<?php echo date('M Y', strtotime($driver['created_at'])) ?>
                            </p>
                        </div>
                        <div class="col-md-4 text-center">
                            <?php if ($totalReviews > 0): ?>
                                <div class="mb-2">
                                    <span class="display-4 text-warning"><?php echo $averageRating ?></span>
                                    <span class="text-muted">/5</span>
                                </div>
                                <div class="mb-2">
                                    <strong>Note moyenne</strong>
                                </div>
                                <small class="text-muted"><?php echo $totalReviews ?> avis</small>
                            <?php else: ?>
                                <div class="text-muted">
                                    <span class="h2">-</span>
                                    <p class="mt-2">Aucun avis</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($totalReviews > 0): ?>
        <!-- Liste des avis -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3>Avis des passagers</h3>
                    </div>
                    <div class="card-body">
                        <?php foreach ($reviews as $review): ?>
                            <div class="border-bottom pb-3 mb-3">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="d-flex align-items-center mb-2">
                                            <strong class="me-3"><?php echo htmlspecialchars($review['reviewer_name']) ?></strong>
                                            <span class="badge bg-primary"><?php echo $review['rating'] ?>/5</span>
                                        </div>
                                        <p class="mb-2"><?php echo nl2br(htmlspecialchars($review['comment'])) ?></p>
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

    <?php else: ?>
        <!-- Aucun avis -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <h4>Aucun avis pour le moment</h4>
                        <p class="text-muted">Ce conducteur n'a pas encore reçu d'avis de la part des passagers.</p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Navigation -->
    <div class="row mt-4">
        <div class="col-12 text-center">
            <a href="/carpools" class="btn">
                Retour aux covoiturages
            </a>
            <a href="/dashboard" class="btn ms-4">
                Tableau de bord
            </a>
        </div>
    </div>
</div>