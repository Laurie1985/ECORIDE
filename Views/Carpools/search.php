<!-- VUE DE RECHERCHE DES COVOITURAGES (US 3) -->

<!-- START HERO-->
<section class="hero">
    <div class="hero-content">
        <h1 class="hero-title">Réservez dès maintenant !</h1>
    </div>
</section>
<!-- END HERO-->

<section class="container mb-4">
    <!-- Formulaire de recherche principal -->
    <div class="search-form w-100" >
        <div class="form-container">
            <h3>Où voulez-vous aller ?</h3>
            <div class="form-carpool d-flex justify-content-center align-items-center flex-column">
                <form id="searchForm" class="form needs-validation row g-3" novalidate>
                    <div class="inputs-group">
                        <div>
                            <input type="text" name="departure" placeholder="Départ" class="form-control" id="departure" required>
                        </div>
                        <div>
                            <input type="text" name="arrival" placeholder="Arrivée" class="form-control" id="arrival" required>
                        </div>
                        <div>
                            <input type="date" name="date" class="form-control" min="<?php echo date('Y-m-d') ?>" id="date" required>
                        </div>
                    </div>
                    <div>
                        <button type="submit" class="btn">Rechercher</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Filtres avancés (US 4) -->
    <div class="row mb-4" id="filtersSection" style="display: none;">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Filtres avancés</h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="filterEcological" class="form-label">Type de trajet</label>
                            <select id="filterEcological" class="form-control">
                                <option value="">Tous les trajets</option>
                                <option value="1">Écologiques uniquement</option>
                                <option value="0">Trajets classiques</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filterMaxPrice" class="form-label">Prix maximum</label>
                            <input type="number" id="filterMaxPrice" class="form-control"placeholder="Ex: 25" min="0" step="0.5">
                        </div>
                        <div class="col-md-3">
                            <label for="filterMaxDuration" class="form-label">Durée maximum (heures)</label>
                            <input type="number" id="filterMaxDuration" class="form-control"placeholder="Ex: 4" min="1" max="12">
                        </div>
                        <div class="col-md-3">
                            <label for="filterMinRating" class="form-label">Note conducteur minimum</label>
                            <select id="filterMinRating" class="form-control">
                                <option value="">Toutes les notes</option>
                                <option value="4.5">4.5/5 et plus</option>
                                <option value="4.0">4.0/5 et plus</option>
                                <option value="3.5">3.5/5 et plus</option>
                                <option value="3.0">3.0/5 et plus</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <button type="button" id="applyFilters" class="btn me-2">
                                Appliquer les filtres
                            </button>
                            <button type="button" id="clearFilters" class="btn btn-outline-secondary">
                                Effacer les filtres
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Zone d'affichage des résultats -->
    <div class="row">
        <div class="col-12">
            <!-- Message initial -->
            <div id="initialMessage" class="card text-center">
                <div class="card-body">
                    <h3>Recherchez votre covoiturage</h3>
                    <p class="text-muted">Saisissez votre ville de départ, d'arrivée et votre date de voyage pour découvrir les covoiturages disponibles.</p>
                </div>
            </div>

            <!-- Loading -->
            <div id="loadingMessage" class="card text-center" style="display: none;">
                <div class="card-body">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                    <p class="mt-2">Recherche en cours...</p>
                </div>
            </div>

            <!-- Aucun résultat -->
            <div id="noResultsMessage" class="card text-center" style="display: none;">
                <div class="card-body">
                    <h4>Aucun covoiturage trouvé</h4>
                    <p class="text-muted" id="noResultsText">Aucun covoiturage ne correspond à vos critères.</p>
                    <div id="suggestionSection" style="display: none;">
                        <p class="mt-3">
                            <strong>Suggestion :</strong>
                            <span id="suggestionText"></span>
                        </p>
                        <button type="button" id="acceptSuggestion" class="btn btn-outline-primary">
                            Essayer cette date
                        </button>
                    </div>
                </div>
            </div>

            <!-- Liste des résultats -->
            <div id="resultsSection" style="display: none;">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 id="resultsTitle">Covoiturages disponibles</h3>
                        <div>
                            <button type="button" id="toggleFilters" class="btn btn-outline-secondary btn-sm">
                                Filtres avancés
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="carpoolsList">
                            <!-- Les résultats fournis par javascript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>