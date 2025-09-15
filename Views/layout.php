<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Ecoride, votre plateforme de covoiturage écologique">
    <title><?php echo htmlspecialchars($title ?? 'ECORIDE') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.typekit.net/evb6gin.css">
    <link rel="stylesheet" href="https://use.typekit.net/evb6gin.css">
    <link rel="stylesheet" href="/assets/css/layout.css">
    <?php if (isset($cssFile)): ?>
        <link rel="stylesheet" href="/assets/css/<?php echo $cssFile ?>.css">
    <?php endif; ?>
</head>
<body>
    <header>
        <!-- START HEADER -->
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <a class="navbar-brand" href="/">
                    <img src="/assets/images/logo2.png" alt="logo Ecoride" class="logo">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="/">Accueil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/carpools">Covoiturages</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/contact">Contact</a>
                        </li>
                    </ul>
                    <div class="d-flex align-items-center flex-nowrap">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="/dashboard" class="btn me-4">Compte</a>
                            <form method="POST" action="/logout" class="d-inline">
                                <button class="btn" type="submit">Déconnexion</button>
                            </form>
                        <?php else: ?>
                            <a href="/login" class="btn me-4">Connexion</a>
                            <a href="/register" class="btn">Inscription</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
        <!-- END HEADER -->
    </header>


    <main>
        <!-- START MAIN -->
        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error'];unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success'];unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <?php echo $content ?? '' ?>
        <!-- END MAIN -->
    </main>

    <footer>
        <!-- START FOOTER-->
        <div class="footer-content d-flex flex-column align-items-center">
            <div class="footer-contact">
                <p>Pour nous contacter : contact@ecoride.com</p>
            </div>
            <div class="footer-bottom d-flex align-items-center">
                <a href="#" data-bs-toggle="modal" data-bs-target="#mentionsLegalesModal" class="link">Mentions légales.</a>
                <div class="footer-copy">
                    &copy; 2025 ECORIDE | Tous droits réservés. Laurie Berthon
                </div>
            </div>
        </div>
        <!-- END FOOTER-->
    </footer>


    <!-- MODAL MENTIONS LEGALES -->
    <div class="modal fade" id="mentionsLegalesModal" tabindex="-1" aria-labelledby="mentionsLegalesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="mentionsLegalesModalLabel">Mentions légales</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h4>Editeur du site</h4>
                    <p><strong>Raison sociale :</strong> Ecoride</p>
                    <p><strong>Siège social :</strong> 123 route de Lyon 69000 LYON</p>
                    <p><strong>SIRET :</strong> 000 000 000 00000</p>
                    <p><strong>Forme juridique :</strong> Société à responsabilité limitée (SARL)</p>
                    <p><strong>Directeur de la publication :</strong> José JOSE</p><br>
                    <h4>Hébergement</h4>
                    <p><strong>Hébergeur :</strong> Heroku</p>
                    <p><strong>Adresse :</strong> Salesforce Tower, 415 Mission Street, 3rd Floor, San Francisco, CA 94105, United States</p><br>
                    <h4>Contact</h4>
                    <p><strong>Email :</strong> contact@ecoride.com</p>
                    <p><strong>Telephone :</strong> 00 00 00 00 00</p><br>
                    <h4>Propriété intellectuelle</h4>
                    <p>Le contenu de ce site (textes, images, logos, graphismes) est la propriété exclusive de Ecoride, sauf mention contraire. Toute reproduction, même partielle, est interdite sans autorisation préalable.</p>
                    <h4>Responsabilité</h4>
                    <p>Ecoride s'efforce d'assurer l'exactitude des informations publiées sur ce site, mais ne peut garantir leur exhaustivité ou leur mise à jour. Ecoride décline toute responsabilité en cas d'erreurs ou d'omissions.</p>
                    <h4>Données personnelles</h4>
                    <p>Conformément à la réglementation en vigueur, vous disposez d'un droit d'accès, de rectification et de suppression des données vous concernant. Pour exercer ce droit, veuillez nous contacter.</p>
                    <h4>Cookies</h4>
                    <p>Aucun cookie de suivi ou traceur publicitaire n’est utilisé. Seuls les cookies de session nécessaires au bon fonctionnement du site sont créés automatiquement, puis supprimés à la fin de la navigation.</p>
                    <h4>Droit applicable</h4>
                    <p>Les présentes mentions légales sont régies par le droit français. En cas de litige, les tribunaux français seront seuls compétents.</p>
                    <p class="mise-a-jour">Dernière mise à jour : sept 2025</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php if (isset($jsFile)): ?>
        <script src="/assets/js/<?php echo $jsFile ?>.js"></script>
    <?php endif; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>