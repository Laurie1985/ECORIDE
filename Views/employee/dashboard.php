<!-- DASHBOARD EMPLOYÉ -->

<!-- START HERO-->
<section class="hero">
    <div class="hero-content">
        <h1 class="hero-title">Espace Employé</h1>
    </div>
</section>
<!-- END HERO-->

<div class="container mt-4 mb-4">

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
                    <p>Plaintes à traiter</p>
                    <a href="/employee/complaints" class="btn">
                        Traiter les plaintes
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
                                    Résoudre les plaintes
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
                                <li>Vérifier le contenu approprié</li>
                                <li>Rejeter les avis inappropriés</li>
                                <li>Approuver les avis conformes</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h4>Gestion des plaintes :</h4>
                            <ul>
                                <li>Analyser les conflits passager/conducteur</li>
                                <li>Décider du remboursement ou paiement</li>
                                <li>Documenter les résolutions</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>