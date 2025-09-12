<!-- MES COVOITURAGES -->

<!-- START HERO-->
<section class="hero">
    <div class="hero-content">
        <h1 class="hero-title">Mes covoiturages</h1>
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

    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1>Vos trajets</h1>
                <a href="/carpools/create" class="btn">
                    Nouveau covoiturage
                </a>
            </div>
        </div>
    </div>

    <?php if (empty($carpools)): ?>
    <!-- Aucun covoiturage -->
    <div class="row">
        <div class="col-12">
            <div class="card text-center">
                <div class="card-body py-5">
                    <h3 class="mb-3">Aucun covoiturage créé</h3>
                    <p class="text-muted mb-4">Commencez par créer votre premier trajet et partagez vos frais de route.</p>
                    <a href="/carpools/create" class="btn">
                        Créer mon premier covoiturage
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>

    <!-- Liste des covoiturages -->
    <div class="row">
        <?php foreach ($carpools as $carpool): ?>
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h2 class="mb-0">
                                <?php echo htmlspecialchars($carpool['departure']) ?>
                                →
                                <?php echo htmlspecialchars($carpool['arrival']) ?>
                            </h2>
                        </div>
                        <div class="col-4 text-end">
                            <?php
                                $statusBadges = [
                                    'scheduled'   => '<span class="badge bg-primary">Programmé</span>',
                                    'in_progress' => '<span class="badge bg-warning">En cours</span>',
                                    'finished'    => '<span class="badge bg-success">Terminé</span>',
                                    'canceled'    => '<span class="badge bg-danger">Annulé</span>',
                                ];
                                echo $statusBadges[$carpool['status']] ?? '<span class="badge bg-secondary">Inconnu</span>';
                            ?>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <p class="mb-1">
                                <strong>Date :</strong><br>
                                <small><?php echo date('d/m/Y', strtotime($carpool['departure_time'])) ?></small>
                            </p>
                            <p class="mb-1">
                                <strong>Départ :</strong><br>
                                <small><?php echo date('H:i', strtotime($carpool['departure_time'])) ?></small>
                            </p>
                        </div>
                        <div class="col-6">
                            <p class="mb-1">
                                <strong>Places :</strong><br>
                                <small>
                                    <?php
                                        $seatsBooked = ($carpool['seats_total'] ?? $carpool['seats_available']) - $carpool['seats_available'];
                                    echo $seatsBooked;
                                    ?>/<?php echo($carpool['seats_total'] ?? $carpool['seats_available']) ?> réservées
                                </small>
                            </p>
                            <p class="mb-1">
                                <strong>Prix :</strong><br>
                                <small><?php echo $carpool['price_per_seat'] ?> crédits/place</small>
                            </p>
                        </div>
                    </div>

                    <!-- Revenus estimés -->
                    <?php
                        $seatsBooked   = ($carpool['seats_total'] ?? $carpool['seats_available']) - $carpool['seats_available'];
                        $totalRevenue  = $seatsBooked * $carpool['price_per_seat'];
                        $commission    = $seatsBooked * 2; // 2 crédits par place
                        $driverRevenue = $totalRevenue - $commission;
                    ?>
                    <?php if ($seatsBooked > 0): ?>
                    <div class="alert alert-info py-2">
                        <small>
                            <strong>Revenus actuels :</strong>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         <?php echo $driverRevenue ?> crédits
                            <br><span class="text-muted">(<?php echo $totalRevenue ?> -<?php echo $commission ?> commission)</span>
                        </small>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Actions selon le statut -->
                <div class="card-footer">
                    <div class="d-flex gap-2 flex-wrap">
                        <!-- Voir les détails -->
                        <a href="/carpools/<?php echo $carpool['carpool_id'] ?>"
                           class="btn btn-sm">
                            Voir détails
                        </a>

                        <?php if ($carpool['status'] === 'scheduled'): ?>
                            <!-- Trajet programmé -->
                            <?php
                                $departureTime = new DateTime($carpool['departure_time']);
                                $now           = new DateTime();
                                $canStart      = $departureTime <= $now->add(new DateInterval('PT30M')); // 30 min avant
                            ?>

                            <?php if ($canStart): ?>
                                <!-- Bouton démarrer (30 min avant le départ) -->
                                <form method="POST" action="/carpools/<?php echo $carpool['carpool_id'] ?>/start" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?>">
                                    <button type="submit" class="btn btn-success btn-sm">
                                        Démarrer le trajet
                                    </button>
                                </form>
                            <?php else: ?>
                                <small class="text-muted align-self-center">
                                    Démarrage possible 30 min avant le départ
                                </small>
                            <?php endif; ?>

                            <!-- Bouton annuler -->
                            <button type="button"
                                    class="btn btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#cancelModal<?php echo $carpool['carpool_id'] ?>">
                                Annuler
                            </button>

                        <?php elseif ($carpool['status'] === 'in_progress'): ?>
                            <!-- Trajet en cours -->
                            <form method="POST" action="/carpools/<?php echo $carpool['carpool_id'] ?>/complete" class="d-inline">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?>">
                                <button type="submit" class="btn btn-success btn-sm">
                                    Arrivés à destination
                                </button>
                            </form>

                        <?php elseif ($carpool['status'] === 'finished'): ?>
                            <!-- Trajet terminé -->
                            <span class="badge bg-success">Trajet terminé</span>
                            <a href="/reviews" class="btn btn-sm">
                                Voir les avis
                            </a>

                        <?php elseif ($carpool['status'] === 'canceled'): ?>
                            <!-- Trajet annulé -->
                            <span class="badge bg-danger">Trajet annulé</span>

                        <?php endif; ?>

                        <!-- Voir les passagers (si réservations) -->
                        <?php if ($seatsBooked > 0): ?>
                            <a href="/my-passengers#carpool-<?php echo $carpool['carpool_id'] ?>"class="btn btn-outline-info btn-sm">
                                Voir passagers (<?php echo $seatsBooked ?>)
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal d'annulation pour chaque covoiturage -->
        <div class="modal fade" id="cancelModal<?php echo $carpool['carpool_id'] ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Annuler le covoiturage</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="/carpools/<?php echo $carpool['carpool_id'] ?>/cancel">
                        <div class="modal-body">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?>">

                            <div class="alert alert-warning">
                                <strong>Attention :</strong> Cette action est irréversible.
                                <?php if ($seatsBooked > 0): ?>
                                <br>Les<?php echo $seatsBooked ?> passager(s) seront automatiquement remboursés.
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="reason<?php echo $carpool['carpool_id'] ?>" class="form-label">
                                    Motif d'annulation (optionnel)
                                </label>
                                <textarea class="form-control"
                                        id="reason<?php echo $carpool['carpool_id'] ?>"
                                        name="reason"
                                        rows="3"
                                        placeholder="Ex: Problème de véhicule, empêchement..."></textarea>
                            </div>

                            <p><strong>Trajet :</strong>
                                <?php echo htmlspecialchars($carpool['departure']) ?> →<?php echo htmlspecialchars($carpool['arrival']) ?>
                            </p>
                            <p><strong>Date :</strong>
                                <?php echo date('d/m/Y H:i', strtotime($carpool['departure_time'])) ?>
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Annuler
                            </button>
                            <button type="submit" class="btn btn-danger">
                                Confirmer l'annulation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Statistiques rapides -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Statistiques</h4>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <?php
                            $stats = [
                                'total'         => count($carpools),
                                'scheduled'     => 0,
                                'in_progress'   => 0,
                                'finished'      => 0,
                                'canceled'      => 0,
                                'total_revenue' => 0,
                            ];

                            foreach ($carpools as $carpool) {
                                $stats[$carpool['status']]++;
                                if ($carpool['status'] === 'finished') {
                                    $seatsBooked  = ($carpool['seats_total'] ?? $carpool['seats_available']) - $carpool['seats_available'];
                                    $totalRevenue = $seatsBooked * $carpool['price_per_seat'];
                                    $commission   = $seatsBooked * 2;
                                    $stats['total_revenue'] += ($totalRevenue - $commission);
                                }
                            }
                        ?>

                        <div class="col-md-2">
                            <h4><?php echo $stats['total'] ?></h4>
                            <small class="text-muted">Total trajets</small>
                        </div>
                        <div class="col-md-2">
                            <h4><?php echo $stats['scheduled'] ?></h4>
                            <small class="text-muted">Programmés</small>
                        </div>
                        <div class="col-md-2">
                            <h4><?php echo $stats['in_progress'] ?></h4>
                            <small class="text-muted">En cours</small>
                        </div>
                        <div class="col-md-2">
                            <h4><?php echo $stats['finished'] ?></h4>
                            <small class="text-muted">Terminés</small>
                        </div>
                        <div class="col-md-2">
                            <h4><?php echo $stats['canceled'] ?></h4>
                            <small class="text-muted">Annulés</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Navigation -->
    <div class="row mt-4">
        <div class="col-12 text-center">
            <a href="/dashboard" class="btn me-2">
                Retour au tableau de bord
            </a>
            <a href="/my-passengers" class="btn">
                Voir mes passagers
            </a>
        </div>
    </div>
</div>