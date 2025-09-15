<!-- CRÉATION D'EMPLOYÉ -->

<!-- START HERO-->
<section class="hero">
    <div class="hero-content">
        <h1 class="hero-title">Créer un employé</h1>
    </div>
</section>
<!-- END HERO-->

<div class="container mt-4 mb-4">

    <!-- Messages flash -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Formulaire de création -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-2">Informations de l'employé</h3>
                </div>

                <form method="POST" action="/admin/employees/create" class="needs-validation" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?>">

                    <div class="card-body">
                        <p class="mb-4 mt-2">Remplissez tous les champs pour créer le compte employé</p>
                        <!-- Informations personnelles -->
                        <div class="mb-4">
                            <h4 class="mb-3">
                                Informations personnelles
                            </h4>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">
                                        Nom *
                                    </label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Nom de famille" value="<?php echo htmlspecialchars($_POST['name'] ?? '') ?>" required>
                                    <div class="invalid-feedback">
                                        Veuillez saisir le nom de famille.
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="firstname" class="form-label">
                                        Prénom *
                                    </label>
                                    <input type="text" class="form-control" id="firstname" name="firstname" placeholder="Prénom" value="<?php echo htmlspecialchars($_POST['firstname'] ?? '') ?>" required>
                                    <div class="invalid-feedback">
                                        Veuillez saisir le prénom.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informations de connexion -->
                        <div class="mb-4">
                            <h4 class="mb-3">
                                Informations de connexion
                            </h4>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="username" class="form-label">
                                        Nom d'utilisateur *
                                    </label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="username" name="username" placeholder="nom.utilisateur" value="<?php echo htmlspecialchars($_POST['username'] ?? '') ?>" required>
                                        <div class="invalid-feedback">
                                            Nom d'utilisateur requis.
                                        </div>
                                    </div>
                                    <div class="form-text">Identifiant unique pour se connecter</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">
                                        Adresse email *
                                    </label>
                                    <div class="input-group">
                                        <input type="email" class="form-control" id="email" name="email" placeholder="employe@ecoride.com" value="<?php echo htmlspecialchars($_POST['email'] ?? '') ?>" required>
                                        <div class="invalid-feedback">
                                            Veuillez saisir une adresse email valide.
                                        </div>
                                    </div>
                                    <div class="form-text">Adresse email professionnelle</div>
                                </div>
                            </div>
                        </div>

                        <!-- Mot de passe -->
                        <div class="mb-4">
                            <h4 class="mb-3">
                                Sécurité
                            </h4>

                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="password" class="form-label">
                                        Mot de passe *
                                    </label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Minimum 9 caractères" minlength="9" required>
                                        <div class="invalid-feedback">
                                            Le mot de passe doit contenir au moins 9 caractères,.
                                        </div>
                                    </div>
                                    <div class="form-text">
                                        <small>Le mot de passe doit contenir au moins :</small>
                                        <ul class="small">
                                            <li>9 caractères minimum</li>
                                            <li>Une majuscule</li>
                                            <li>Une minuscule</li>
                                            <li>Un chiffre</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions du formulaire -->
                    <div class="d-flex justify-content-center mb-4">
                        <a href="/admin/employees" class="btn ">Annuler</a>
                        <button type="submit" class="btn ms-4" id="submitBtn">Créer l'employé</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

