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

            <!-- FAQ rapide -->
            <div class="card">
                <div class="card-header">
                    <h3>Questions fréquentes</h3>
                </div>
                <div class="card-body">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq1">
                                    Comment créer un compte sur Ecoride ?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Cliquez sur "Inscription" en haut de la page, remplissez le formulaire avec vos informations personnelles.
                                    Vous recevrez automatiquement 20 crédits de bienvenue pour commencer !
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq2">
                                    Comment fonctionne le système de crédits ?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Les crédits servent à payer vos réservations. En tant que conducteur, vous gagnez des crédits
                                    (prix - 2 crédits de commission). En tant que passager, vous dépensez vos crédits pour réserver.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq3">
                                    Qu'est-ce qu'un trajet écologique ?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Un trajet écologique est un trajet effectué avec un véhicule électrique uniquement.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq4">
                                    Puis-je annuler ma réservation ?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Oui, vous pouvez annuler votre réservation jusqu'à 2 heures avant le départ.
                                    Vos crédits seront automatiquement remboursés.
                                </div>
                            </div>
                        </div>
                    </div>
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