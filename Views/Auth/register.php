<!--FORMULAIRE D'INSCRIPTION-->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h3 class="text-center title">Je crée mon compte :</h3>
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="/register">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?>">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nom *</label>
                                <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($_POST['name'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="firstname" class="form-label">Prénom *</label>
                                <input type="text" name="firstname" id="firstname" class="form-control" value="<?php echo htmlspecialchars($_POST['firstname'] ?? '') ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="username" class="form-label">Nom d'utilisateur *</label>
                            <input type="text" name="username" id="username" class="form-control" value="<?php echo htmlspecialchars($_POST['username'] ?? '') ?>" required>
                            <div class="form-text">Ce nom sera visible par les autres utilisateurs</div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Adresse email *</label>
                            <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($_POST['email'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe *</label>
                            <input type="password" name="password" id="password" class="form-control" required>
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

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirmer le mot de passe *</label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                        </div>

                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn">S'incrire</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Informations sur les crédits -->
            <div class="alert alert-info mt-3">
                <h6>🎉 Bonus de bienvenue</h6>
                <p class="mb-0">En créant votre compte, vous bénéficiez automatiquement de <strong>20 crédits gratuits</strong> pour reserver vos premiers trajets !</p>
            </div>
        </div>
    </div>
</div>