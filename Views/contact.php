<!-- PAGE DE CONTACT -->

<!-- START HERO-->
<section class="hero">
    <div class="hero-content">
        <h1 class="hero-title">Contactez-nous</h1>
    </div>
</section>
<!-- END HERO-->

<div class="container mt-4 mb-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Informations de contact -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3>Nos coordonnées</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Adresse</h4>
                            <p class="mb-3">
                                <strong>Ecoride SAS</strong><br>
                                123 route de Lyon<br>
                                69000 LYON<br>
                                France
                            </p>

                            <h4>Contact</h4>
                            <p class="mb-3">
                                <strong>Email :</strong> contact@ecoride.com<br>
                                <strong>Téléphone :</strong> 00 00 00 00 00<br>
                                <strong>SIRET :</strong> 000 000 000 00000
                            </p>
                        </div>

                        <div class="col-md-6">
                            <h4>Horaires d'ouverture</h4>
                            <p class="mb-3">
                                <strong>Du lundi au vendredi :</strong><br>
                                9h00 - 18h00<br><br>
                                <strong>Samedi :</strong><br>
                                10h00 - 16h00<br><br>
                                <strong>Dimanche :</strong> Fermé
                            </p>

                            <h4>Direction</h4>
                            <p class="mb-0">
                                <strong>Directeur de la publication :</strong>
                                José JOSE
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulaire de contact -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3>Votre message</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="/contact" id="contactForm">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?>">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nom *</label>
                                <input type="text" name="name" id="name" class="form-control"value="<?php echo htmlspecialchars($_POST['name'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" name="email" id="email" class="form-control"value="<?php echo htmlspecialchars($_POST['email'] ?? '') ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label">Sujet *</label>
                            <select name="subject" id="subject" class="form-control" required>
                                <option value="">Choisissez un sujet</option>
                                <option value="question_generale"<?php echo(($_POST['subject'] ?? '') === 'question_generale') ? ' selected' : '' ?>>
                                    Question générale
                                </option>
                                <option value="probleme_technique"<?php echo(($_POST['subject'] ?? '') === 'probleme_technique') ? ' selected' : '' ?>>
                                    Problème technique
                                </option>
                                <option value="probleme_reservation"<?php echo(($_POST['subject'] ?? '') === 'probleme_reservation') ? ' selected' : '' ?>>
                                    Problème de réservation
                                </option>
                                <option value="litige_covoiturage"<?php echo(($_POST['subject'] ?? '') === 'litige_covoiturage') ? ' selected' : '' ?>>
                                    Litige avec un covoiturage
                                </option>
                                <option value="suggestion"<?php echo(($_POST['subject'] ?? '') === 'suggestion') ? ' selected' : '' ?>>
                                    Suggestion d'amélioration
                                </option>
                                <option value="autre"<?php echo(($_POST['subject'] ?? '') === 'autre') ? ' selected' : '' ?>>
                                    Autre
                                </option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Votre message *</label>
                            <textarea name="message" id="message" class="form-control" rows="6"placeholder="Décrivez votre demande en détail..." required><?php echo htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                            <small class="form-text text-muted">
                                Minimum 10 caractères, maximum 2000 caractères.
                            </small>
                        </div>

                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn">
                                Envoyer
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Navigation -->
            <div class="row mt-4">
                <div class="col-12 text-center">
                    <a href="/" class="btn btn-outline-secondary me-2">
                        Retour à l'accueil
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>