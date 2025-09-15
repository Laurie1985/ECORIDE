<!-- STATISTIQUES ADMINISTRATEUR -->

<!-- START HERO-->
<section class="hero">
    <div class="hero-content">
        <h1 class="hero-title">Statistiques de la plateforme</h1>
    </div>
</section>
<!-- END HERO-->

<div class="container mt-4 mb-4">

    <!-- Navigation rapide -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Actions rapides</h3>
                </div>
                <div class="card-body">
                    <div class="row col-12 gy-2 d-flex justify-content-center">
                        <div class="col-md-3">
                            <a href="/admin/dashboard" class="btn">
                                Retour au dashboard
                            </a>
                        </div>
                        <div class="col-md-3">
                            <select id="periodSelect" class="form-select">
                                <option value="7">7 derniers jours</option>
                                <option value="30" selected>30 derniers jours</option>
                                <option value="90">90 derniers jours</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button id="refreshBtn" class="btn">
                                Actualiser les données
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="row gy-4">
        <!-- Graphique des covoiturages -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h3>Covoiturages effectués</h3>
                    <p class="mb-0 text-muted">Évolution quotidienne</p>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="carpoolsChart"></canvas>
                    </div>
                    <div id="carpoolsLoading" class="text-center py-4">
                        <p class="text-muted">Chargement des données...</p>
                    </div>
                    <div id="carpoolsError" class="text-center py-4 d-none">
                        <p class="text-danger">Erreur lors du chargement des données</p>
                        <button class="btn" onclick="loadData()">
                            Réessayer
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphique des revenus -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h3>Revenus de la plateforme</h3>
                    <p class="mb-0 text-muted">Gains quotidiens en crédits</p>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="earningsChart"></canvas>
                    </div>
                    <div id="earningsLoading" class="text-center py-4">
                        <p class="text-muted">Chargement des données...</p>
                    </div>
                    <div id="earningsError" class="text-center py-4 d-none">
                        <p class="text-danger">Erreur lors du chargement des données</p>
                        <button class="btn" onclick="loadData()">
                            Réessayer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Résumé des données -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Résumé de la période sélectionnée</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 id="totalCarpools">-</h4>
                                <p>Covoiturages effectués</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 id="avgCarpools">-</h4>
                                <p>Moyenne par jour</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 id="totalEarnings">-</h4>
                                <p>Revenus totaux</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 id="avgEarnings"></h4>
                                <p>Moyenne par jour</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
