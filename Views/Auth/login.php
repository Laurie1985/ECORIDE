<!--FORMULAIRE DE CONNEXION-->

<form method="POST" action="/login">
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?>">

    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Mot de passe" required>
    <button type="submit">Se connecter</button>
</form>