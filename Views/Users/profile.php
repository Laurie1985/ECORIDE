<!-- PROFIL UTILISATEUR ADAPTATIF -->

<!-- START HERO-->
<section class="hero">
    <div class="hero-content">
        <h1 class="hero-title">Mon profil</h1>
    </div>
</section>
<!-- END HERO-->

<div class="container mt-4 mb-4">

    <div class="row gy-4">
        <!-- Informations personnelles -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3>Informations personnelles</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="/profile/update" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?>">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nom</label>
                                <input type="text" name="name" id="name" class="form-control"value="<?php echo htmlspecialchars($user['name'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="firstname" class="form-label">Prénom</label>
                                <input type="text" name="firstname" id="firstname" class="form-control"value="<?php echo htmlspecialchars($user['firstname'] ?? '') ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="username" class="form-label">Nom d'utilisateur</label>
                            <input type="text" class="form-control"value="<?php echo htmlspecialchars($user['username'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control"value="<?php echo htmlspecialchars($user['email'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Téléphone</label>
                            <input type="tel" name="phone" id="phone" class="form-control"value="<?php echo htmlspecialchars($user['phone'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Adresse</label>
                            <textarea name="address" id="address" class="form-control" rows="2"><?php echo htmlspecialchars($user['address'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="photo" class="form-label">Photo de profil</label>
                            <input type="file" name="photo" id="photo" class="form-control" accept="image/*">
                            <small class="form-text text-muted">Formats acceptés : JPG, PNG, GIF (max 2Mo)</small>
                        </div>

                        <button type="submit" class="btn">Mettre à jour</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Type d'utilisateur -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3>Type d'utilisateur</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="/profile/role">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?>">

                        <div class="mb-4">
                            <p><strong>Je suis :</strong></p>

                            <div class="form-check mb-3">
                                <input type="radio" name="user_type" value="passenger" id="passenger"class="form-check-input"<?php echo($_SESSION['user_type'] ?? 'passenger') === 'passenger' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="passenger">
                                    <strong>Passager uniquement</strong><br>
                                    <small class="text-muted">Je recherche des trajets pour mes déplacements</small>
                                </label>
                            </div>

                            <div class="form-check mb-3">
                                <input type="radio" name="user_type" value="driver" id="driver"
                                    class="form-check-input"
                                    <?php echo($_SESSION['user_type'] ?? '') === 'driver' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="driver">
                                    <strong>Conducteur uniquement</strong><br>
                                    <small class="text-muted">Je propose mes trajets</small>
                                </label>
                            </div>

                            <div class="form-check mb-3">
                                <input type="radio" name="user_type" value="both" id="both"
                                    class="form-check-input"
                                    <?php echo($_SESSION['user_type'] ?? '') === 'both' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="both">
                                    <strong>Les deux</strong><br>
                                    <small class="text-muted">Je propose et recherche des trajets selon mes besoins</small>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn">Mettre à jour mon statut</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations du compte en fonction du type d'utilisateur -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Informations générales</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3><?php echo $user['credits'] ?></h3>
                                <p class="text-muted">Crédits disponibles</p>
                            </div>
                        </div>

                        <?php if (in_array($_SESSION['user_type'] ?? 'passenger', ['driver', 'both'])): ?>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3><?php echo number_format($user['rating'], 1) ?>/5</h3>
                                <p class="text-muted">Note conducteur</p>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="col-md-3">
                            <div class="text-center">
                                <h3><?php echo ucfirst($user['status'] ?? 'active') ?></h3>
                                <p class="text-muted">Statut du compte</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3><?php echo date('d/m/Y', strtotime($user['created_at'])) ?></h3>
                                <p class="text-muted">Membre depuis</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides adaptées à l'utilisateur -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Actions rapides</h3>
                </div>
                <div class="card-body">
                    <div class="row gy-2">
                        <!-- Actions communes -->
                        <div class="col-md-3">
                            <a href="/history" class="btn w-100">
                                Mon historique
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="/dashboard" class="btn w-100">
                                Mon tableau de bord
                            </a>
                        </div>

                        <!-- Actions spécifiques aux conducteurs -->
                        <?php if (in_array($_SESSION['user_type'] ?? 'passenger', ['driver', 'both'])): ?>
                        <div class="col-md-3">
                            <a href="/vehicles" class="btn w-100">
                                Gérer mes véhicules
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="/preferences" class="btn w-100">
                                Mes préférences
                            </a>
                        </div>
                        <?php endif; ?>

                        <!-- Actions spécifiques aux passagers -->
                        <?php if (in_array($_SESSION['user_type'] ?? 'passenger', ['passenger', 'both'])): ?>
                        <div class="col-md-3">
                            <a href="/carpools" class="btn w-100">
                                Rechercher un trajet
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="/reservations" class="btn w-100">
                                Mes réservations
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Invitation conditionnelle pour les passagers -->
    <?php if (($_SESSION['user_type'] ?? 'passenger') === 'passenger'): ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Devenir conducteur</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <p class="mb-2"><strong>Pourquoi devenir conducteur EcoRide ?</strong></p>
                            <ul class="list">
                                <li><p class="mb-0">Rentabilisez vos déplacements quotidiens</p></li>
                                <li><p class="mb-0">Gagnez des crédits pour vos futurs trajets</p></li>
                                <li><p class="mb-0">Participez à la mobilité durable</p></li>
                                <li><p class="mb-0">Rencontrez des voyageurs partageant vos valeurs</p></li>
                            </ul>
                        </div>
                        <div class="col-md-4 text-center">
                            <p class="mb-2">Changez votre statut ci-dessus puis :</p>
                            <a href="/vehicles" class="btn btn-lg">
                                Ajouter un véhicule
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>