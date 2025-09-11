// Gestion améliorée de la recherche de covoiturages
class CarpoolSearch {
    constructor() {
        this.allCarpools = [];
        this.filteredCarpools = [];
        this.isInitialized = false;
        this.init();
    }

    /**
     * Initialisation avec vérification que le DOM est prêt
     */
    init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setup());
        } else {
            this.setup();
        }
    }

    /**
     * Configuration des événements et vérification des éléments
     */
    setup() {
        // Vérifier que nous sommes sur la bonne page
        if (!document.getElementById('searchForm')) {
            console.warn('CarpoolSearch: Page de recherche non détectée');
            return;
        }

        this.bindEvents();
        this.loadInitialResults();
        this.isInitialized = true;
        console.log('CarpoolSearch: Initialisé avec succès');
    }

    /**
     * Liaison des événements avec vérification d'existence des éléments
     */
    bindEvents() {
        // Recherche principale - utilise l'ID de ta vue
        const searchForm = document.getElementById('searchForm');
        if (searchForm) {
            searchForm.addEventListener('submit', (e) => this.handleSearch(e));
        } else {
            console.error('Formulaire de recherche non trouvé');
        }

        // Filtres avancés - adaptation aux IDs de ta vue
        this.bindFilterEvents();

        // Boutons d'action
        this.bindActionButtons();
    }

    /**
     * Liaison spécifique des filtres avec gestion d'erreur
     */
    bindFilterEvents() {
        const filterSelectors = [
            'filterEcological',
            'filterMaxPrice',
            'filterMaxDuration',
            'filterMinRating'
        ];

        filterSelectors.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.addEventListener('input', () => this.debounceFilter());
                element.addEventListener('change', () => this.applyFilters());
            }
        });
    }

    /**
     * Liaison des boutons d'action
     */
    bindActionButtons() {
        const applyBtn = document.getElementById('applyFilters');
        if (applyBtn) {
            applyBtn.addEventListener('click', () => this.applyFilters());
        }

        const clearBtn = document.getElementById('clearFilters');
        if (clearBtn) {
            clearBtn.addEventListener('click', () => this.resetFilters());
        }

        const toggleBtn = document.getElementById('toggleFilters');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => this.toggleFiltersPanel());
        }
    }

    /**
     * Debounce pour éviter trop d'appels lors de la saisie
     */
    debounceFilter() {
        clearTimeout(this.filterTimeout);
        this.filterTimeout = setTimeout(() => this.applyFilters(), 300);
    }

    /**
     * Gestion améliorée de la recherche avec validation
     */
    async handleSearch(e) {
        e.preventDefault();

        const formData = this.getSearchFormData();

        if (!this.validateSearchData(formData)) {
            return;
        }

        this.showSection('loading');

        try {
            const data = await this.performSearchRequest(formData);
            this.handleSearchResponse(data);
        } catch (error) {
            this.handleSearchError(error);
        }
    }

    /**
     * Récupération sécurisée des données du formulaire
     */
    getSearchFormData() {
        const departure = this.getElementValue('departure');
        const arrival = this.getElementValue('arrival');
        const date = this.getElementValue('date');

        return { departure, arrival, date };
    }

    /**
     * Utilitaire pour récupérer la valeur d'un élément
     */
    getElementValue(id) {
        const element = document.getElementById(id);
        return element ? element.value.trim() : '';
    }

    /**
     * Validation des données de recherche
     */
    validateSearchData({ departure, arrival, date }) {
        if (!departure || !arrival || !date) {
            this.showUserError('Veuillez remplir tous les champs de recherche');
            return false;
        }

        // Validation de la date (ne peut pas être dans le passé)
        const selectedDate = new Date(date);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        if (selectedDate < today) {
            this.showUserError('La date de départ ne peut pas être dans le passé');
            return false;
        }

        return true;
    }

    /**
     * Requête de recherche avec gestion d'erreur réseau
     */
    async performSearchRequest({ departure, arrival, date }) {
        const params = new URLSearchParams({
            departure,
            arrival,
            date
        });

        const response = await fetch(`/api/carpools/search?${params}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`Erreur serveur: ${response.status} ${response.statusText}`);
        }

        const data = await response.json();

        if (!data.success) {
            throw new Error(data.error || 'Erreur lors de la recherche');
        }

        return data;
    }

    /**
     * Traitement de la réponse de recherche
     */
    handleSearchResponse(data) {
        this.allCarpools = Array.isArray(data.carpools) ? data.carpools : [];
        this.filteredCarpools = [...this.allCarpools];

        if (this.allCarpools.length === 0) {
            this.handleNoResults(data);
        } else {
            this.displayResults();
            this.showSection('results');
            this.updateResultsCount();
        }
    }

    /**
     * Gestion des cas sans résultats
     */
    handleNoResults(data) {
        this.showSection('noResults');

        if (data.suggested_date) {
            this.showDateSuggestion(data.suggested_date, data.suggestion_message);
        }
    }

    /**
     * Gestion d'erreur avec distinction des types
     */
    handleSearchError(error) {
        console.error('Erreur de recherche:', error);

        if (error.name === 'TypeError' && error.message.includes('fetch')) {
            this.showUserError('Problème de connexion. Vérifiez votre connexion Internet.');
        } else if (error.message.includes('serveur')) {
            this.showUserError('Le serveur rencontre des difficultés. Réessayez dans quelques instants.');
        } else {
            this.showUserError(error.message || 'Erreur inattendue lors de la recherche');
        }

        this.showSection('noResults');
    }

    /**
     * Application des filtres avec validation
     */
    applyFilters() {
        if (!this.allCarpools.length) {
            return;
        }

        const filters = this.getFilterValues();

        this.filteredCarpools = this.allCarpools.filter(carpool =>
            this.passesAllFilters(carpool, filters)
        );

        this.displayResults();
        this.updateResultsCount();
    }

    /**
     * Récupération sécurisée des valeurs de filtres
     */
    getFilterValues() {
        return {
            ecological: this.getCheckedValue('filterEcological'),
            maxPrice: this.getNumericValue('filterMaxPrice'),
            minRating: this.getNumericValue('filterMinRating'),
            maxDuration: this.getNumericValue('filterMaxDuration')
        };
    }

    getCheckedValue(id) {
        const element = document.getElementById(id);
        return element && element.value === '1';
    }

    getNumericValue(id) {
        const element = document.getElementById(id);
        if (!element || !element.value) return null;
        const value = parseFloat(element.value);
        return isNaN(value) ? null : value;
    }

    /**
     * Test si un covoiturage passe tous les filtres
     */
    passesAllFilters(carpool, filters) {
        // Filtre écologique
        if (filters.ecological && !carpool.is_ecological) {
            return false;
        }

        // Filtre prix maximum
        if (filters.maxPrice !== null && carpool.price_per_seat > filters.maxPrice) {
            return false;
        }

        // Filtre note conducteur minimum
        if (filters.minRating !== null && carpool.driver_rating < filters.minRating) {
            return false;
        }

        // Filtre durée maximum
        if (filters.maxDuration !== null && carpool.duration_hours > filters.maxDuration) {
            return false;
        }

        return true;
    }

    /**
     * Affichage des résultats avec template sécurisé
     */
    displayResults() {
        const container = document.getElementById('carpoolsList');
        if (!container) {
            console.error('Container de résultats non trouvé');
            return;
        }

        if (this.filteredCarpools.length === 0) {
            container.innerHTML = this.getNoResultsTemplate();
            return;
        }

        const carpoolsHTML = this.filteredCarpools
            .map(carpool => this.createCarpoolCard(carpool))
            .join('');

        container.innerHTML = carpoolsHTML;
    }

    /**
     * Template pour aucun résultat
     */
    getNoResultsTemplate() {
        return `
            <div class="card text-center">
                <div class="card-body">
                    <h3>Aucun covoiturage trouvé</h3>
                    <p class="text-muted">Essayez de modifier vos critères de recherche ou vos filtres</p>
                </div>
            </div>
        `;
    }

    /**
     * Création d'une carte covoiturage avec échappement HTML
     */
    createCarpoolCard(carpool) {
        const departure = this.escapeHtml(carpool.departure);
        const arrival = this.escapeHtml(carpool.arrival);
        const driverName = this.escapeHtml(carpool.driver_username);
        const vehicleInfo = this.escapeHtml(`${carpool.name_brand} ${carpool.vehicle_model}`);

        const ecoBadge = carpool.is_ecological ?
            '<span class="badge bg-success ms-2">Écologique</span>' : '';

        const starsHtml = this.generateStarsHtml(carpool.driver_rating);
        const preferenceTags = this.createPreferenceTags(carpool);

        return `
            <div class="card mb-3 col-6 carpool-card" data-carpool-id="${carpool.carpool_id}">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <img src="/assets/images/default-avatar.png" alt="Photo" 
                                         class="rounded-circle" width="50" height="50">
                                </div>
                                <div>
                                    <h5 class="mb-1">
                                        <span class="route">${departure} → ${arrival}</span>
                                        ${ecoBadge}
                                    </h5>
                                    <p class="mb-1">
                                        <strong>Conducteur :</strong> ${driverName} 
                                        <span class="text-warning">
                                            ${starsHtml} (${carpool.driver_rating}/5)
                                        </span>
                                    </p>
                                    <p class="mb-0 text-muted">
                                        <small>${vehicleInfo}</small>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <p class="mb-1">
                                    <strong>${this.formatTime(carpool.departure_time)}</strong><br>
                                    <small class="text-muted">→ ${this.formatTime(carpool.arrival_time)}</small>
                                </p>
                                <p class="mb-0">
                                    <small class="text-muted">Durée: ${carpool.duration_hours}h</small>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-primary mb-1">${carpool.price_per_seat}€</h4>
                                <p class="mb-2">
                                    <small>${carpool.seats_available} place(s) disponible(s)</small>
                                </p>
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-sm" 
                                            onclick="viewCarpoolDetails(${carpool.carpool_id})">
                                        Voir détails
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <div class="preferences-tags">
                                ${preferenceTags}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Échappement HTML pour éviter les injections XSS
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Génération des étoiles pour la note
     */
    generateStarsHtml(rating) {
        const fullStars = Math.floor(rating);
        const hasHalfStar = rating % 1 >= 0.5;
        let starsHtml = '';

        for (let i = 0; i < fullStars; i++) {
            starsHtml += '★';
        }

        if (hasHalfStar) {
            starsHtml += '☆';
        }

        return starsHtml;
    }

    /**
     * Création des tags de préférences
     */
    createPreferenceTags(carpool) {
        const tags = [];

        if (carpool.smoking_allowed) {
            tags.push('<span class="badge bg-secondary me-1">Fumeurs acceptés</span>');
        }

        if (carpool.animals_allowed) {
            tags.push('<span class="badge bg-secondary me-1">Animaux acceptés</span>');
        }

        return tags.join('');
    }

    /**
     * Mise à jour du compteur de résultats
     */
    updateResultsCount() {
        const titleElement = document.getElementById('resultsTitle');
        if (titleElement) {
            const count = this.filteredCarpools.length;
            titleElement.textContent = `${count} covoiturage(s) trouvé(s)`;
        }
    }

    /**
     * Gestion de l'affichage des sections
     */
    showSection(section) {
        const sections = {
            'initial': 'initialMessage',
            'loading': 'loadingMessage',
            'noResults': 'noResultsMessage',
            'results': 'resultsSection'
        };

        // Masquer toutes les sections
        Object.values(sections).forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.style.display = 'none';
            }
        });

        // Afficher la section demandée
        const targetElement = document.getElementById(sections[section]);
        if (targetElement) {
            targetElement.style.display = 'block';
        }
    }

    /**
     * Affichage d'erreur utilisateur
     */
    showUserError(message) {
        // Utiliser le système d'alerte de Bootstrap si disponible
        if (typeof bootstrap !== 'undefined') {
            // Créer une alerte Bootstrap temporaire
            this.showBootstrapAlert(message, 'danger');
        } else {
            // Fallback avec alert() standard
            alert(message);
        }
    }

    /**
     * Affichage d'alerte Bootstrap
     */
    showBootstrapAlert(message, type = 'info') {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${this.escapeHtml(message)}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

        const container = document.querySelector('.container');
        if (container) {
            container.insertAdjacentHTML('afterbegin', alertHtml);
        }
    }

    /**
     * Toggle du panneau de filtres
     */
    toggleFiltersPanel() {
        const filtersSection = document.getElementById('filtersSection');
        const toggleBtn = document.getElementById('toggleFilters');

        if (filtersSection && toggleBtn) {
            const isVisible = filtersSection.style.display !== 'none';
            filtersSection.style.display = isVisible ? 'none' : 'block';
            toggleBtn.textContent = isVisible ? 'Filtres avancés' : 'Masquer les filtres';
        }
    }

    /**
     * Reset des filtres avec nettoyage complet
     */
    resetFilters() {
        const filterIds = ['filterEcological', 'filterMaxPrice', 'filterMaxDuration', 'filterMinRating'];

        filterIds.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.value = '';
            }
        });

        if (this.allCarpools.length > 0) {
            this.filteredCarpools = [...this.allCarpools];
            this.displayResults();
            this.updateResultsCount();
        }
    }

    /**
     * Affichage de suggestion de date
     */
    showDateSuggestion(suggestedDate, message) {
        const suggestionSection = document.getElementById('suggestionSection');
        const suggestionText = document.getElementById('suggestionText');
        const acceptBtn = document.getElementById('acceptSuggestion');

        if (suggestionSection && suggestionText) {
            suggestionText.textContent = message;
            suggestionSection.style.display = 'block';

            if (acceptBtn) {
                // Nettoyer les anciens listeners
                const newBtn = acceptBtn.cloneNode(true);
                acceptBtn.parentNode.replaceChild(newBtn, acceptBtn);

                // Ajouter le nouveau listener
                newBtn.addEventListener('click', () => {
                    document.getElementById('date').value = suggestedDate;
                    this.handleSearch(new Event('submit'));
                });
            }
        }
    }

    /**
     * Formatage de l'heure
     */
    formatTime(datetime) {
        try {
            const date = new Date(datetime);
            return date.toLocaleTimeString('fr-FR', {
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch (error) {
            console.warn('Erreur formatage date:', error);
            return datetime;
        }
    }

    /**
     * Chargement initial des résultats depuis l'URL
     */
    loadInitialResults() {
        const urlParams = new URLSearchParams(window.location.search);
        const departure = urlParams.get('departure');
        const arrival = urlParams.get('arrival');
        const date = urlParams.get('date');

        if (departure && arrival && date) {
            // Pré-remplir le formulaire
            this.setElementValue('departure', departure);
            this.setElementValue('arrival', arrival);
            this.setElementValue('date', date);

            // Lancer la recherche directement plutôt qu'avec setTimeout
            this.handleSearch(new Event('submit'));
        }
    }

    /**
     * Utilitaire pour définir la valeur d'un élément
     */
    setElementValue(id, value) {
        const element = document.getElementById(id);
        if (element) {
            element.value = value;
        }
    }
}

// Fonction globale pour les détails (conservée pour compatibilité)
function viewCarpoolDetails(carpoolId) {
    if (carpoolId && Number.isInteger(carpoolId)) {
        window.location.href = `/carpools/${carpoolId}`;
    } else {
        console.error('ID de covoiturage invalide:', carpoolId);
    }
}

// Initialisation sécurisée
let carpoolSearchInstance = null;

document.addEventListener('DOMContentLoaded', () => {
    try {
        carpoolSearchInstance = new CarpoolSearch();
    } catch (error) {
        console.error('Erreur initialisation CarpoolSearch:', error);
    }
});