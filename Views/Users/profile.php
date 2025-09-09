<!-- Dans profile.php -->
<form method="POST" action="/profile/role">
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token?>">

    <div class="mb-3">
        <label class="form-label">Je souhaite être :</label>
        <div class="form-check">
            <input type="radio" name="user_type" value="passenger" <?php echo $_SESSION['user_type'] === 'passenger' ? 'checked' : ''?>>
            <label>Passager uniquement</label>
        </div>
        <div class="form-check">
            <input type="radio" name="user_type" value="driver"<?php echo $_SESSION['user_type'] === 'driver' ? 'checked' : ''?>>
            <label>Conducteur uniquement</label>
        </div>
        <div class="form-check">
            <input type="radio" name="user_type" value="both"<?php echo $_SESSION['user_type'] === 'both' ? 'checked' : ''?>>
            <label>Les deux</label>
        </div>
    </div>

    <button type="submit" class="btn">Mettre à jour</button>
</form>