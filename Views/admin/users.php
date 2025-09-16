<!-- GESTION DES UTILISATEURS -->

<!-- START HERO-->
<section class="hero">
    <div class="hero-content">
        <h1 class="hero-title">Gestion des utilisateurs</h1>
    </div>
</section>
<!-- END HERO-->

<div class="container mt-4 mb-4">

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

    <!-- Statistiques rapides -->
    <div class="row mb-4 mt-4 gy-4">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="stat-title">Total utilisateurs</h3>
                    <h2 class="stat-number"><?php echo count($users) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="stat-title">Actifs</h3>
                    <h2 class="stat-number text-success">
                        <?php
                            $count = 0;
                            foreach ($users as $user) {
                                if ($user['status'] === 'active') {
                                    $count++;
                                }
                            }
                            echo $count;
                        ?>
                    </h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="stat-title">Suspendus</h3>
                    <h2 class="stat-number text-danger">
                        <?php
                            $count = 0;
                            foreach ($users as $user) {
                                if ($user['status'] === 'banned') {
                                    $count++;
                                }
                            }
                            echo $count;
                        ?>
                    </h2>
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
                            <a href="/admin/dashboard" class="btn">
                                Retour au dashboard
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="/admin/employees" class="btn">
                                Gérer les employés
                            </a>
                        </div>
                        <div class="col-md-3">
                            <button class="btn" onclick="toggleFilter('active')">
                                Filtrer par statut
                            </button>
                        </div>
                        <div class="col-md-3">
                            <input type="text" id="searchUsers" class="form-control" placeholder="Rechercher un utilisateur...">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des utilisateurs -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Liste des utilisateurs</h3>
                    <div class="filter-buttons">
                        <button class="btn" data-filter="all">Tous</button>
                        <button class="btn" data-filter="active">Actifs</button>
                        <button class="btn" data-filter="banned">Suspendus</button>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($users)): ?>
                        <div class="text-center py-5">
                            <p class="text-muted">Aucun utilisateur trouvé</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</strong></th>
                                        <th>Utilisateur</th>
                                        <th>Email</th>
                                        <th>Crédits</th>
                                        <th>Statut</th>
                                        <th>Inscription</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="usersTableBody">
                                    <?php foreach ($users as $user): ?>
                                        <tr class="user-row" data-status="<?php echo $user['status'] ?>" data-username="<?php echo strtolower($user['username']) ?>" data-email="<?php echo strtolower($user['email']) ?>">
                                            <td><?php echo $user['user_id'] ?></td>
                                            <td>
                                                <div>
                                                    <strong><?php echo htmlspecialchars($user['username']) ?></strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?php echo htmlspecialchars($user['firstname'] . ' ' . $user['name']) ?>
                                                    </small>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($user['email']) ?></td>
                                            <td>
                                                <span class="badge bg-success">
                                                    <?php echo number_format($user['credits'], 0) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($user['status'] === 'active'): ?>
                                                    <span class="badge bg-success">Actif</span>
                                                <?php elseif ($user['status'] === 'banned'): ?>
                                                    <span class="badge bg-danger">Suspendu</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary"><?php echo ucfirst($user['status']) ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small><?php echo date('d/m/Y', strtotime($user['created_at'])) ?></small>
                                            </td>
                                            <td>
                                                <?php if ($user['status'] === 'active'): ?>
                                                    <form method="POST" action="/admin/users/suspend/<?php echo $user['user_id'] ?>" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir suspendre<?php echo htmlspecialchars($user['username']) ?> ?')">
                                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?>">
                                                        <button type="submit" class="btn">
                                                            Suspendre
                                                        </button>
                                                    </form>
                                                <?php elseif ($user['status'] === 'banned'): ?>
                                                    <form method="POST" action="/admin/users/activate/<?php echo $user['user_id'] ?>" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir réactiver<?php echo htmlspecialchars($user['username']) ?> ?')">
                                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?>">
                                                        <button type="submit" class="btn">
                                                            Réactiver
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

