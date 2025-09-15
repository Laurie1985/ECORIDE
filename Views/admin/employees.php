<!-- GESTION DES EMPLOYÉS -->

<!-- START HERO-->
<section class="hero">
    <div class="hero-content">
        <h1 class="hero-title">Gestion des employés</h1>
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
                    <h3 class="stat-title">Total employés</h3>
                    <h2 class="stat-number"><?php echo count($employees) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="stat-title">Actifs</h3>
                    <h2 class="stat-number text-success">
                        <?php echo count(array_filter($employees, fn($emp) => $emp['status'] === 'active')) ?>
                    </h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="stat-title">Suspendus</h3>
                    <h2 class="stat-number text-danger">
                        <?php echo count(array_filter($employees, fn($emp) => $emp['status'] === 'banned')) ?>
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
                            <a href="/admin/employees/create" class="btn">
                                Créer un employé
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="/admin/users" class="btn">
                                Gérer les utilisateurs
                            </a>
                        </div>
                        <div class="col-md-3">
                            <input type="text" id="searchUsers" class="form-control" placeholder="Rechercher un employé...">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des employés -->
    <div class="row">
        <div class="col-12">
            <?php if (empty($employees)): ?>
                <!-- État vide -->
                <div class="card">
                    <div class="card-body text-center py-5">
                        <h3 class="text-muted">Aucun employé</h3>
                        <p class="text-muted mb-4">Il n'y a encore aucun compte employé sur la plateforme.</p>
                        <a href="/admin/employees/create" class="btn">
                            Créer le premier employé
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Filtres -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="filters d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-2">Filtrer les employés</h4>
                            </div>
                            <div class="filter-buttons">
                                <button class="btn" data-filter="all">Tous</button>
                                <button class="btn" data-filter="active">Actifs</button>
                                <button class="btn" data-filter="banned">Suspendus</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grille des employés -->
                <div class="row" id="employeesGrid">
                    <?php foreach ($employees as $employee): ?>
                        <div class="col-md-6 col-lg-4 mb-4 employee-card user-row"
                            data-status="<?php echo $employee['status'] ?>"
                            data-username="<?php echo strtolower($employee['username']) ?>"
                            data-email="<?php echo strtolower($employee['email']) ?>">
                            <div class="card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <h4 class="mb-0"><?php echo htmlspecialchars($employee['firstname'] . ' ' . $employee['name']) ?></h4>
                                            <small class="text-muted"><?php echo htmlspecialchars($employee['username']) ?></small>
                                        </div>
                                    </div>
                                    <div>
                                        <?php if ($employee['status'] === 'active'): ?>
                                            <span class="badge bg-success">Actif</span>
                                        <?php elseif ($employee['status'] === 'banned'): ?>
                                            <span class="badge bg-danger">Suspendu</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><?php echo ucfirst($employee['status']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="employee-info">
                                        <p class="mb-2">
                                            <?php echo htmlspecialchars($employee['email']) ?>
                                        </p>
                                        <p class="mb-2">
                                            Créé le
                                            <?php echo date('d/m/Y', strtotime($employee['created_at'])) ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="card-footer bg-transparent">
                                    <?php if ($employee['status'] === 'active'): ?>
                                        <form method="POST" action="/admin/employees/suspend/<?php echo $employee['user_id'] ?>"
                                            onsubmit="return confirm('Êtes-vous sûr de vouloir suspendre :<?php echo htmlspecialchars($employee['username']) ?> ?')">
                                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?>">
                                            <button type="submit" class="btn">
                                                Suspendre
                                            </button>
                                        </form>
                                    <?php elseif ($employee['status'] === 'banned'): ?>
                                        <form method="POST" action="/admin/employees/activate/<?php echo $employee['user_id'] ?>"
                                            onsubmit="return confirm('Êtes-vous sûr de vouloir réactiver :<?php echo htmlspecialchars($employee['username']) ?> ?')">
                                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?>">
                                            <button type="submit" class="btn">
                                                Réactiver
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>