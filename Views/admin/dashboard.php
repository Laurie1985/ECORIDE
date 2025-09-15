<!-- DASHBOARD ADMINISTRATEUR -->

<!-- START HERO-->
<section class="hero">
    <div class="hero-content">
        <h1 class="hero-title">Espace administrateur</h1>
    </div>
</section>
<!-- END HERO-->

<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-12">
            <p class="lead">Bonjour :
                <strong><?php echo htmlspecialchars($_SESSION['username']) ?></strong>
            </p>
        </div>
    </div>

    <!-- Messages flash -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Statistiques globales -->
    <div class="row mb-4 mt-4 gy-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="stat-title">Utilisateurs</h3>
                    <h2 class="stat-number"><?php echo isset($totalUsers) ? $totalUsers : 0 ?></h2>
                    <p>Actifs sur la plateforme</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="stat-title">Covoiturages</h3>
                    <h2 class="stat-number"><?php echo isset($totalCarpools) ? $totalCarpools : 0 ?></h2>
                    <p>Trajets créés</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="stat-title">Réservations</h3>
                    <h2 class="stat-number"><?php echo isset($totalReservations) ? $totalReservations : 0 ?></h2>
                    <p>Total</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="stat-title">Revenus</h3>
                    <h2 class="stat-number"><?php echo isset($platformEarnings) ? number_format($platformEarnings, 2) : '0.00' ?></h2>
                    <p>Gains plateforme</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Actions rapides</h3>
                </div>
                <div class="card-body">
                    <div class="row col-12 gy-2 d-flex justify-content-center">
                        <div class="col-md-3">
                            <a href="/admin/users" class="btn">
                                Gérer les utilisateurs
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="/admin/employees" class="btn">
                                Gérer les employés
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="/admin/stats" class="btn">
                                Statistiques détaillées
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="/admin/employees/create" class="btn">
                                Créer un employé
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row gy-4">
        <!-- Section Gestion des utilisateurs -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h3>Gestion des utilisateurs</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="text-center">
                                <h2><?php echo isset($totalUsers) ? $totalUsers : 0 ?></h2>
                                <p>Utilisateurs actifs</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h2><?php echo isset($totalCarpools) ? $totalCarpools : 0 ?></h2>
                                <p>Covoiturages créés</p>
                            </div>
                        </div>
                    </div>

                    <div class="list-group list-group-flush">
                        <a href="/admin/users" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <p>Voir tous les utilisateurs</p>
                        </a>
                        <a href="/admin/employees" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <p>Gérer les employés</p>
                        </a>
                        <a href="/admin/employees/create" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <p>Créer un nouvel employé</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Statistiques et réservations -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h3>Statistiques de la plateforme</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="text-center">
                                <h2><?php echo isset($totalReservations) ? $totalReservations : 0 ?></h2>
                                <p>Réservations totales</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h2><?php echo isset($platformEarnings) ? number_format($platformEarnings, 0) : 0 ?></h2>
                                <p>Revenus générés</p>
                            </div>
                        </div>
                    </div>

                    <div class="list-group list-group-flush">
                        <a href="/admin/stats" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <p>Voir les graphiques détaillés</p>
                        </a>

                        <!-- Réservations par statut -->
                        <?php if (isset($reservationsByStatus) && ! empty($reservationsByStatus)): ?>
                            <?php foreach ($reservationsByStatus as $status => $count): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <p>
                                        <strong>
                                        <?php
                                            $statusLabels = [
                                                'awaiting_passenger_confirmation' => 'En attente de confirmation',
                                                'completed'                       => 'Terminées',
                                                'disputed'                        => 'En litige',
                                                'canceled'                        => 'Annulées',
                                                'confirmed'                       => 'Confirmées',
                                            ];
                                            echo $statusLabels[$status] ?? ucfirst(str_replace('_', ' ', $status));
                                        ?>
                                        </strong>
                                    </p>
                                    <span class="badge bg-primary rounded-pill"><?php echo is_numeric($count) ? $count : 0 ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Avis et MongoDB -->
    <?php if (isset($reviewsStats) && $reviewsStats['total'] > 0): ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Statistiques des avis</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Avis des utilisateurs :</h4>
                            <ul class="list-unstyled">
                                <li><p class="mb-2"><strong>Total d'avis :</strong>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         <?php echo $reviewsStats['total'] ?></p></li>
                                <li><p class="mb-2"><strong>Note moyenne :</strong>
                                    <?php if (isset($reviewsStats['average']) && $reviewsStats['average'] > 0): ?>
                                        <?php echo number_format($reviewsStats['average'], 1) ?>/5
                                    <?php else: ?>
                                        Aucune note disponible
                                    <?php endif; ?>
                                </p></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h4>Gestion des avis :</h4>
                            <ul class="list-unstyled">
                                <li><p class="mb-2">Les avis sont modérés par les employés</p></li>
                                <li><p class="mb-2">Système de validation en place</p></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>