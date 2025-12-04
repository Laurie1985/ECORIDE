<!-- VUE DE RECHERCHE DES COVOITURAGES -->

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
                <form id="searchForm" action="/carpools" method="GET" class="form needs-validation row g-3" novalidate>
                    <div class="inputs-group">
                        <div>
                            <input type="text" name="departure" placeholder="Départ" class="form-control" id="departure" value="<?php echo htmlspecialchars($_GET['departure'] ?? ''); ?>" required>
                        </div>
                        <div>
                            <input type="text" name="arrival" placeholder="Arrivée" class="form-control" id="arrival" value="<?php echo htmlspecialchars($_GET['arrival'] ?? ''); ?>" required>
                        </div>
                        <div>
                            <input type="date" name="date" class="form-control" min="<?php echo date('Y-m-d') ?>" id="date" value="<?php echo htmlspecialchars($_GET['date'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <div>
                        <button type="submit" class="btn">Rechercher</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Filtres avancés -->
        <div class="col-lg-3 mb-4">
            <div class="card sticky-filters">
                <div class="card-header">
                    <h4 class="mb-0">Filtres avancés</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="filterEcological" class="form-label">Type de trajet</label>
                        <select id="filterEcological" class="form-control">
                            <option value="">Tous les trajets</option>
                            <option value="1">Écologiques uniquement</option>
                            <option value="0">Trajets classiques</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="filterMaxPrice" class="form-label">Prix maximum (crédits)</label>
                        <input type="number"
                            id="filterMaxPrice"
                            class="form-control"
                            placeholder="Ex: 25"
                            min="0"
                            step="1">
                    </div>

                    <div class="mb-3">
                        <label for="filterMaxDuration" class="form-label">Durée maximum (heures)</label>
                        <input type="number"
                            id="filterMaxDuration"
                            class="form-control"
                            placeholder="Ex: 4"
                            min="1"
                            max="12">
                    </div>

                    <div class="mb-3">
                        <label for="filterMinRating" class="form-label">Note conducteur minimum</label>
                        <select id="filterMinRating" class="form-control">
                            <option value="">Toutes les notes</option>
                            <option value="4.5">4.5/5 et plus</option>
                            <option value="4.0">4.0/5 et plus</option>
                            <option value="3.5">3.5/5 et plus</option>
                            <option value="3.0">3.0/5 et plus</option>
                        </select>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="button" id="applyFilters" class="btn">
                            Appliquer les filtres
                        </button>
                        <button type="button" id="clearFilters" class="btn btn-outline-secondary">
                            Effacer les filtres
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Zone d'affichage des résultats -->
        <div class="col-lg-9">
            <!-- Message initial -->
            <div id="initialMessage" class="card text-center">
                <div class="card-body py-5">
                    <h3>Recherchez votre covoiturage</h3>
                    <p class="text-muted">Saisissez votre ville de départ, d'arrivée et votre date de voyage pour découvrir les covoiturages disponibles.</p>
                </div>
            </div>

            <!-- Chargement -->
            <div id="loadingMessage" class="card text-center" style="display: none;">
                <div class="card-body py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                    <p class="mt-3">Recherche en cours...</p>
                </div>
            </div>

            <!-- Aucun résultat -->
            <div id="noResultsMessage" class="card text-center" style="display: none;">
                <div class="card-body py-5">
                    <h4>Aucun covoiturage trouvé</h4>
                    <p class="text-muted" id="noResultsText">Aucun covoiturage ne correspond à vos critères.</p>
                    <div id="suggestionSection" style="display: none;">
                        <div class="alert alert-info mt-4">
                            <h4>Suggestion</h4>
                            <p class="mb-3" id="suggestionText"></p>
                            <button type="button" id="acceptSuggestion" class="btn">
                                Essayer cette date
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liste des résultats -->
            <div id="resultsSection" style="display: none;">
                <div class="card">
                    <div class="card-header">
                        <h3 id="resultsTitle" class="mb-0">Covoiturages disponibles</h3>
                    </div>
                    <div class="card-body p-0">
                        <div id="carpoolsList">
                            <!-- Les résultats sont générés par JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Template pour un résultat de covoiturage -->
<template id="carpoolTemplate">
    <div class="carpool-item border-bottom p-4">
        <div class="row align-items-center">
            <!-- Photo et infos conducteur -->
            <div class="col-md-2 text-center">
                <div class="driver-photo mb-2">
                    <img src="" alt="" class="rounded-circle driver-image" width="60" height="60">
                </div>
                <h4 class="driver-name mb-1"></h4>
                <div class="driver-rating">
                    <span class="rating-value"></span>
                </div>
            </div>

            <!-- Détails du trajet -->
            <div class="col-md-6">
                <div class="row">
                    <div class="col-6">
                        <h4 class="trip-route mb-2"></h4>
                        <p class="mb-1">
                            <strong>Départ :</strong> <span class="departure-time"></span>
                        </p>
                        <p class="mb-1">
                            <strong>Arrivée :</strong> <span class="arrival-time"></span>
                        </p>
                        <p class="mb-0">
                            <strong>Durée :</strong> <span class="duration"></span>
                        </p>
                    </div>
                    <div class="col-6">
                        <p class="mb-1">
                            <strong>Places disponibles :</strong>
                            <span class="badge bg-primary seats-available"></span>
                        </p>
                        <p class="mb-1">
                            <strong>Véhicule :</strong> <span class="vehicle-info"></span>
                        </p>
                        <div class="ecological-badge" style="display: none;">
                            <img src="/assets/images/pictoEcologie.png" alt="Écologique"class="picto">
                        </div>
                        <div class="preferences mt-2">
                            <div class="smoking-allowed" style="display: none;">
                                <small class="badge bg-secondary">Fumeurs acceptés</small>
                            </div>
                            <div class="animals-allowed" style="display: none;">
                                <small class="badge bg-secondary">Animaux acceptés</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Prix et action -->
            <div class="col-md-4 text-end">
                <div class="price-section">
                    <h4 class="price-amount mb-2"></h4>
                    <p class="text-muted mb-3">par place</p>
                    <a href="#" class="btn details-btn">
                        Voir les détails
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>