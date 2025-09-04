//Gestion des filtres côté client
class CarpoolSearch {
    constructor() {
        this.allCarpools = [];
        this.filteredCarpools = [];
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadInitialResults();
    }

    bindEvents() {
        // Recherche principale
        const searchForm = document.getElementById('search-form');
        if (searchForm) {
            searchForm.addEventListener('submit', (e) => this.handleSearch(e));
        }

        // Filtres avancés
        const filterInputs = document.querySelectorAll('.filter-input');
        filterInputs.forEach(input => {
            input.addEventListener('input', () => this.applyFilters());
            input.addEventListener('change', () => this.applyFilters());
        });

        // Reset des filtres
        const resetBtn = document.getElementById('reset-filters');
        if (resetBtn) {
            resetBtn.addEventListener('click', () => this.resetFilters());
        }
    }

    async handleSearch(e) {
        e.preventDefault();

        const departure = document.getElementById('departure').value;
        const arrival = document.getElementById('arrival').value;
        const date = document.getElementById('date').value;

        if (!departure || !arrival || !date) {
            this.showError('Veuillez remplir tous les champs de recherche');
            return;
        }

        this.showLoading(true);

        try {
            const response = await fetch(`/carpools/api/search?departure=${encodeURIComponent(departure)}&arrival=${encodeURIComponent(arrival)}&date=${encodeURIComponent(date)}`);
            const data = await response.json();

            if (data.success) {
                this.allCarpools = data.carpools;
                this.filteredCarpools = [...this.allCarpools];
                this.displayResults();
                this.showFiltersPanel();

                // Suggestion de date alternative
                if (data.carpools.length === 0 && data.suggested_date) {
                    this.showDateSuggestion(data.suggested_date, data.suggestion_message);
                }
            } else {
                this.showError(data.error || 'Erreur lors de la recherche');
            }
        } catch (error) {
            console.error('Erreur de recherche:', error);
            this.showError('Erreur de connexion au serveur');
        } finally {
            this.showLoading(false);
        }
    }

    applyFilters() {
        // Récupération des valeurs des filtres
        const filters = {
            ecological: document.getElementById('filter-ecological')?.checked,
            maxPrice: parseFloat(document.getElementById('filter-max-price')?.value) || null,
            minRating: parseFloat(document.getElementById('filter-min-rating')?.value) || null,
            maxDuration: parseInt(document.getElementById('filter-max-duration')?.value) || null,
            smokingAllowed: document.getElementById('filter-smoking')?.checked,
            animalsAllowed: document.getElementById('filter-animals')?.checked
        };

        this.filteredCarpools = this.allCarpools.filter(carpool => {
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

            // Filtres préférences
            if (filters.smokingAllowed && !carpool.smoking_allowed) {
                return false;
            }

            if (filters.animalsAllowed && !carpool.animals_allowed) {
                return false;
            }

            return true;
        });

        this.displayResults();
        this.updateResultsCount();
    }

    displayResults() {
        const container = document.getElementById('carpools-results');
        if (!container) return;

        if (this.filteredCarpools.length === 0) {
            container.innerHTML = `
                <div class="no-results">
                    <i class="fas fa-search"></i>
                    <h3>Aucun covoiturage trouvé</h3>
                    <p>Essayez de modifier vos critères de recherche ou vos filtres</p>
                </div>
            `;
            return;
        }

        const carpoolsHTML = this.filteredCarpools.map(carpool => `
            <div class="carpool-card" data-id="${carpool.carpool_id}">
                <div class="carpool-header">
                    <div class="route">
                        <i class="fas fa-map-marker-alt text-success"></i>
                        <span>${carpool.departure}</span>
                        <i class="fas fa-arrow-right mx-2"></i>
                        <i class="fas fa-map-marker-alt text-danger"></i>
                        <span>${carpool.arrival}</span>
                    </div>
                    ${carpool.is_ecological ? '<span class="badge badge-success ecological-badge"><i class="fas fa-leaf"></i> Écologique</span>' : ''}
                </div>
                
                <div class="carpool-info">
                    <div class="time-info">
                        <i class="fas fa-clock"></i>
                        Départ: ${carpool.departure_time} • Arrivée: ${carpool.arrival_time}
                        <small class="text-muted">(${carpool.duration_hours}h)</small>
                    </div>
                    
                    <div class="driver-info">
                        <div class="driver-avatar">
                            ${carpool.driver_photo ?
                `<img src="/uploads/profiles/${carpool.driver_photo}" alt="Photo conducteur">` :
                '<i class="fas fa-user"></i>'
            }
                        </div>
                        <div>
                            <strong>${carpool.driver_username}</strong>
                            <div class="rating">
                                ${this.generateStars(carpool.driver_rating)}
                                <span>(${carpool.driver_rating}/5)</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="vehicle-info">
                        <i class="fas fa-car"></i>
                        ${carpool.name_brand} ${carpool.vehicle_model} (${carpool.vehicle_color})
                        <span class="energy-badge energy-${carpool.energy_type}">${this.getEnergyLabel(carpool.energy_type)}</span>
                    </div>
                </div>
                
                <div class="carpool-footer">
                    <div class="seats-price">
                        <span class="seats"><i class="fas fa-users"></i> ${carpool.seats_available} places</span>
                        <span class="price">${carpool.price_per_seat}€ /personne</span>
                    </div>
                    
                    <a href="/carpools/${carpool.carpool_id}" class="btn btn-primary">
                        <i class="fas fa-info-circle"></i> Détails
                    </a>
                </div>
            </div>
        `).join('');

        container.innerHTML = carpoolsHTML;
    }

    generateStars(rating) {
        const fullStars = Math.floor(rating);
        const hasHalfStar = rating % 1 !== 0;
        let stars = '';

        for (let i = 0; i < fullStars; i++) {
            stars += '<i class="fas fa-star text-warning"></i>';
        }

        if (hasHalfStar) {
            stars += '<i class="fas fa-star-half-alt text-warning"></i>';
        }

        const emptyStars = 5 - Math.ceil(rating);
        for (let i = 0; i < emptyStars; i++) {
            stars += '<i class="far fa-star text-muted"></i>';
        }

        return stars;
    }

    getEnergyLabel(energyType) {
        const labels = {
            'electric': 'Électrique',
            'hybrid': 'Hybride',
            'diesel': 'Diesel',
            'essence': 'Essence'
        };
        return labels[energyType] || energyType;
    }

    updateResultsCount() {
        const countElement = document.getElementById('results-count');
        if (countElement) {
            const total = this.allCarpools.length;
            const filtered = this.filteredCarpools.length;
            countElement.textContent = `${filtered} résultat${filtered > 1 ? 's' : ''} sur ${total}`;
        }
    }

    showFiltersPanel() {
        const filtersPanel = document.getElementById('filters-panel');
        if (filtersPanel) {
            filtersPanel.style.display = 'block';
        }
    }

    resetFilters() {
        // Reset tous les filtres
        document.querySelectorAll('.filter-input').forEach(input => {
            if (input.type === 'checkbox') {
                input.checked = false;
            } else {
                input.value = '';
            }
        });

        // Réafficher tous les résultats
        this.filteredCarpools = [...this.allCarpools];
        this.displayResults();
        this.updateResultsCount();
    }

    showDateSuggestion(suggestedDate, message) {
        const suggestionDiv = document.getElementById('date-suggestion');
        if (suggestionDiv) {
            suggestionDiv.innerHTML = `
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    ${message}
                    <button class="btn btn-sm btn-outline-primary ml-2" onclick="document.getElementById('date').value='${suggestedDate}'; this.closest('.alert').style.display='none';">
                        Essayer cette date
                    </button>
                </div>
            `;
            suggestionDiv.style.display = 'block';
        }
    }

    showLoading(show) {
        const spinner = document.getElementById('loading-spinner');
        if (spinner) {
            spinner.style.display = show ? 'block' : 'none';
        }
    }

    showError(message) {
        const errorDiv = document.getElementById('error-message');
        if (errorDiv) {
            errorDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    ${message}
                </div>
            `;
            errorDiv.style.display = 'block';
        }
    }

    loadInitialResults() {
        // Si on arrive sur la page avec des paramètres d'URL, charger automatiquement
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('departure') && urlParams.get('arrival') && urlParams.get('date')) {
            document.getElementById('departure').value = urlParams.get('departure');
            document.getElementById('arrival').value = urlParams.get('arrival');
            document.getElementById('date').value = urlParams.get('date');

            // Déclencher la recherche automatiquement
            setTimeout(() => {
                document.getElementById('search-form').dispatchEvent(new Event('submit'));
            }, 100);
        }
    }
}

// Initialisation quand le DOM est chargé
document.addEventListener('DOMContentLoaded', () => {
    new CarpoolSearch();
});