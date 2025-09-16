<!-- DASHBOARD EMPLOYÉ -->

<!-- START HERO-->
<section class="hero">
    <div class="hero-content">
        <h1 class="hero-title">Espace Employé</h1>
    </div>
</section>
<!-- END HERO-->

<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-12">
            <p class="lead">Bonjour :
                <strong><?php echo htmlspecialchars($_SESSION['username']) ?></strong>
            </p>
        </div>
    </div>
    <!-- Statistiques rapides -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <h2 class="text-warning"><?php echo $pendingReviewsCount ?></h2>
                    <p>Avis en attente</p>
                    <a href="/employee/reviews" class="btn">
                        Modérer les avis
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <h2 class="text-danger"><?php echo $complaintsCount ?></h2>
                    <p>Litiges à traiter</p>
                    <a href="/employee/complaints" class="btn">
                        Traiter les litiges
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Actions rapides</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3 d-flex justify-content-center">
                            <div class="d-flex">
                                <a href="/employee/reviews" class="btn">
                                    Gérer les avis
                                </a>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3 d-flex justify-content-center">
                            <div class="d-flex">
                                <a href="/employee/complaints" class="btn">
                                    Résoudre les litiges
                                </a>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3 d-flex justify-content-center">
                            <div class="d-flex">
                                <a href="/dashboard" class="btn">
                                    Retour accueil
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations importantes -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Votre rôle en tant qu'employé</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Modération des avis :</h4>
                            <ul>
                                <li>
                                    <p>Vérifier le contenu approprié</p>
                                </li>
                                <li>
                                    <p>Rejeter les avis inappropriés</p>
                                </li>
                                <li>
                                    <p>Approuver les avis conformes</p>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h4>Gestion des litiges :</h4>
                            <ul>
                                <li>
                                    <p>Analyser les conflits passager/conducteur</p>
                                </li>
                                <li>
                                    <p>Décider du remboursement ou paiement</p>
                                </li>
                                <li>
                                    <p>Documenter les résolutions</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>