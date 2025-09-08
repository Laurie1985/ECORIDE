<!-- FORMULAIRE DE CONNEXION -->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 connexion">
            <h3 class="text-center title">Je me connecte :</h3>
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="/login">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?>">

                        <div class="mb-3">
                            <label for="login" class="form-label">Email ou nom d'utilisateur</label>
                            <input type="text" name="login" id="login" class="form-control"value="<?php echo htmlspecialchars($_POST['login'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>

                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn">Se connecter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>