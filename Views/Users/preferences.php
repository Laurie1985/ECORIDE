<!-- PRÉFÉRENCES DE CONDUITE -->

<!-- START HERO-->
<section class="hero">
    <div class="hero-content"></div>
</section>
<!-- END HERO-->

<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-12">
            <h1 class="dash-title">Mes préférences de conduite</h1>
        </div>
    </div>

    <div class="row mt-4 mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Configurez vos préférences</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="/preferences/update">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?>">

                        <!-- Préférences standard -->
                        <div class="mb-4">
                            <h4>Préférences générales</h4>
                            <div class="form-check mb-3">
                                <input type="checkbox" name="smoking_allowed" value="1" id="smoking_allowed"
                                    class="form-check-input"
                                    <?php echo(isset($preferences['smoking_allowed']) && $preferences['smoking_allowed']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="smoking_allowed">
                                    <strong>J'accepte les fumeurs</strong><br>
                                    <small class="text-muted">Les passagers peuvent fumer dans le véhicule ou lors des pauses</small>
                                </label>
                            </div>

                            <div class="form-check mb-3">
                                <input type="checkbox" name="animals_allowed" value="1" id="animals_allowed"
                                    class="form-check-input"
                                    <?php echo(isset($preferences['animals_allowed']) && $preferences['animals_allowed']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="animals_allowed">
                                    <strong>J'accepte les animaux</strong><br>
                                    <small class="text-muted">Les passagers peuvent voyager avec leurs animaux de compagnie</small>
                                </label>
                            </div>
                        </div>

                        <!-- Préférences personnalisées -->
                        <div class="mb-4">
                            <h4>Préférences personnalisées</h4>
                            <label for="personalized_preferences" class="form-label">
                                Ajoutez vos propres préférences et règles de conduite
                            </label>
                            <textarea name="personalized_preferences" id="personalized_preferences"
                                    class="form-control" rows="6"
                                    placeholder="Exemples : &#10;- Pas de musique forte&#10;- Pas de conversations téléphoniques&#10;- Bagages légers uniquement&#10;- Ponctualité exigée&#10;- Partage des frais d'autoroute..."><?php echo htmlspecialchars($preferences['personalized_preferences'] ?? '') ?></textarea>
                            <small class="form-text text-muted">
                                Ces préférences seront visibles par les passagers avant qu'ils réservent.
                            </small>
                        </div>

                        <div class="d-grid gap-2 d-md-flex">
                            <button type="submit" class="btn">Enregistrer mes préférences</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

            <!-- Aperçu actuel -->
            <?php if (isset($preferences) && ($preferences['smoking_allowed'] || $preferences['animals_allowed'] || ! empty($preferences['personalized_preferences']))): ?>
            <div class="card">
                <div class="card-header">
                    <h3>Aperçu de vos préférences</h3>
                </div>
                <div class="card-body">
                    <?php if ($preferences['smoking_allowed']): ?>
                    <span class="badge me-1 mb-2">Fumeurs acceptés</span>
                    <?php endif; ?>

                    <?php if ($preferences['animals_allowed']): ?>
                    <span class="badge me-1 mb-2">Animaux acceptés</span>
                    <?php endif; ?>

                    <?php if (! empty($preferences['personalized_preferences'])): ?>
                    <div class="mt-2">
                        <strong class="text-muted">Préférences personnalisées :</strong>
                        <p class="small mt-1"><?php echo nl2br(htmlspecialchars(substr($preferences['personalized_preferences'], 0, 100))) ?><?php echo strlen($preferences['personalized_preferences']) > 100 ? '...' : '' ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>