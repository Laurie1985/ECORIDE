/**
 * Recherche de covoiturages avec filtres
 */

//Variables globales
let currentResults = [];
let currentFilters = {};

document.addEventListener('DOMContentLoaded', function () {
    initializeSearch();
    initializeFilters();

    const urlParams = new URLSearchParams(window.location.search);
    const departure = urlParams.get('departure');
    const arrival = urlParams.get('arrival');
    const date = urlParams.get('date');

    if (departure && arrival && date) {
        document.getElementById('departure').value = departure;
        document.getElementById('arrival').value = arrival;
        document.getElementById('date').value = date;

        performSearch();
    }
});

/**
 * Initialise la recherche principale
 */
function initializeSearch() {
    const searchForm = document.getElementById('searchForm');

    searchForm.addEventListener('submit', function (e) {
        e.preventDefault();
        performSearch();
    });
}

/**
 * Initialise les filtres avancés
 */
function initializeFilters() {
    // Bouton d'application des filtres
    document.getElementById('applyFilters').addEventListener('click', applyFilters);

    // Bouton de réinitialisation des filtres
    document.getElementById('clearFilters').addEventListener('click', clearFilters);

    // Suggestion de date alternative
    const acceptSuggestionBtn = document.getElementById('acceptSuggestion');
    if (acceptSuggestionBtn) {
        acceptSuggestionBtn.addEventListener('click', acceptSuggestion);
    }
}

/**
 * Effectue la recherche principale
 */
async function performSearch() {
    const departure = document.getElementById('departure').value.trim();
    const arrival = document.getElementById('arrival').value.trim();
    const date = document.getElementById('date').value;

    if (!departure || !arrival || !date) {
        alert('Veuillez remplir tous les champs de recherche');
        return;
    }

    showLoading();

    try {
        const params = new URLSearchParams({
            departure: departure,
            arrival: arrival,
            date: date
        });

        const response = await fetch(`/api/carpools/search?${params}`);
        const data = await response.json();

        if (data.success) {
            currentResults = data.carpools;
            displayResults(currentResults);

            if (currentResults.length === 0 && data.suggested_date) {
                showSuggestion(data.suggestion_message, data.suggested_date);
            }
        } else {
            showError('Erreur lors de la recherche');
        }

    } catch (error) {
        console.error('Erreur recherche:', error);
        showError('Erreur de connexion');
    }
}

/**
 * Affiche l'état de chargement
 */
function showLoading() {
    hideAllSections();
    document.getElementById('loadingMessage').style.display = 'block';
}

/**
 * Affiche les résultats
 */
function displayResults(carpools) {
    hideAllSections();

    if (carpools.length === 0) {
        document.getElementById('noResultsMessage').style.display = 'block';
        return;
    }

    const resultsSection = document.getElementById('resultsSection');
    const carpoolsList = document.getElementById('carpoolsList');
    const resultsTitle = document.getElementById('resultsTitle');

    // Mise à jour du titre
    const departure = document.getElementById('departure').value;
    const arrival = document.getElementById('arrival').value;
    resultsTitle.textContent = `${departure} -> ${arrival}`;

    // Vider les résultats précédents
    carpoolsList.innerHTML = '';

    // Générer les éléments de résultats
    carpools.forEach(carpool => {
        const carpoolElement = createCarpoolElement(carpool);
        carpoolsList.appendChild(carpoolElement);
    });

    resultsSection.style.display = 'block';
}

/**
 * Crée un élément de covoiturage à partir du template
 */
function createCarpoolElement(carpool) {
    const template = document.getElementById('carpoolTemplate');
    const clone = template.content.cloneNode(true);

    // Photo du conducteur
    const driverImage = clone.querySelector('.driver-image');
    if (driverImage) {
        // Nettoyer le chemin au cas où il serait pollué
        let photoPath = carpool.driver_photo || '/assets/images/default.jpeg';

        // Supprimer data:image/jpeg;base64, si présent
        photoPath = photoPath.replace(/^data:image\/[^;]+;base64,/, '');

        driverImage.src = photoPath;
        driverImage.alt = `Photo de ${carpool.driver_username}`;
    }

    // Informations du conducteur
    clone.querySelector('.driver-name').textContent = carpool.driver_username;
    clone.querySelector('.rating-value').textContent = `${carpool.driver_rating}/5`;

    // Détails du trajet
    clone.querySelector('.trip-route').textContent = `${carpool.departure} -> ${carpool.arrival}`;
    clone.querySelector('.departure-time').textContent = formatDateTime(carpool.departure_time);
    clone.querySelector('.arrival-time').textContent = formatDateTime(carpool.arrival_time);
    clone.querySelector('.duration').textContent = `${carpool.duration_hours}h`;
    clone.querySelector('.seats-available').textContent = `${carpool.seats_available} place(s)`;
    clone.querySelector('.vehicle-info').textContent = `${carpool.name_brand} ${carpool.vehicle_model}`;

    // Badge écologique
    if (carpool.is_ecological) {
        clone.querySelector('.ecological-badge').style.display = 'block';
    }

    // Préférences
    if (carpool.smoking_allowed) {
        clone.querySelector('.smoking-allowed').style.display = 'inline-block';
    }
    if (carpool.animals_allowed) {
        clone.querySelector('.animals-allowed').style.display = 'inline-block';
    }

    // Prix
    clone.querySelector('.price-amount').textContent = `${carpool.price_per_seat} crédits`;

    // Lien vers les détails
    clone.querySelector('.details-btn').href = `/carpools/${carpool.carpool_id}`;

    return clone;
}

/**
 * Applique les filtres aux résultats actuels
 */
function applyFilters() {
    if (currentResults.length === 0) {
        alert('Effectuez d\'abord une recherche');
        return;
    }

    // Récupérer les valeurs des filtres
    currentFilters = {
        ecological: document.getElementById('filterEcological').value,
        maxPrice: parseFloat(document.getElementById('filterMaxPrice').value) || null,
        maxDuration: parseFloat(document.getElementById('filterMaxDuration').value) || null,
        minRating: parseFloat(document.getElementById('filterMinRating').value) || null
    };

    // Filtrer les résultats
    const filteredResults = currentResults.filter(carpool => {
        // Filtre écologique
        if (currentFilters.ecological !== '') {
            const isEco = currentFilters.ecological === '1';
            if (carpool.is_ecological !== isEco) {
                return false;
            }
        }

        // Filtre prix maximum
        if (currentFilters.maxPrice && carpool.price_per_seat > currentFilters.maxPrice) {
            return false;
        }

        // Filtre durée maximum
        if (currentFilters.maxDuration && carpool.duration_hours > currentFilters.maxDuration) {
            return false;
        }

        // Filtre note minimum
        if (currentFilters.minRating && carpool.driver_rating < currentFilters.minRating) {
            return false;
        }

        return true;
    });

    displayResults(filteredResults);
}

/**
 * Efface tous les filtres
 */
function clearFilters() {
    document.getElementById('filterEcological').value = '';
    document.getElementById('filterMaxPrice').value = '';
    document.getElementById('filterMaxDuration').value = '';
    document.getElementById('filterMinRating').value = '';

    currentFilters = {};

    if (currentResults.length > 0) {
        displayResults(currentResults);
    }
}

/**
 * Affiche une suggestion de date alternative
 */
function showSuggestion(message, suggestedDate) {
    const suggestionSection = document.getElementById('suggestionSection');
    const suggestionText = document.getElementById('suggestionText');

    suggestionText.textContent = message;
    suggestionSection.style.display = 'block';

    // Stocker la date suggérée pour l'utiliser plus tard
    document.getElementById('acceptSuggestion').dataset.suggestedDate = suggestedDate;
}

/**
 * Accepte la suggestion de date et relance la recherche
 */
function acceptSuggestion() {
    const suggestedDate = document.getElementById('acceptSuggestion').dataset.suggestedDate;
    if (suggestedDate) {
        document.getElementById('date').value = suggestedDate;
        performSearch();
    }
}

/**
 * Affiche un message d'erreur
 */
function showError(message) {
    hideAllSections();
    document.getElementById('noResultsMessage').style.display = 'block';
    document.getElementById('noResultsText').textContent = message;
}

/**
 * Cache toutes les sections de résultats
 */
function hideAllSections() {
    document.getElementById('initialMessage').style.display = 'none';
    document.getElementById('loadingMessage').style.display = 'none';
    document.getElementById('noResultsMessage').style.display = 'none';
    document.getElementById('resultsSection').style.display = 'none';
    document.getElementById('suggestionSection').style.display = 'none';
}

/**
 * Formate une date/heure pour l'affichage
 */
function formatDateTime(dateTimeString) {
    const date = new Date(dateTimeString);
    return date.toLocaleDateString('fr-FR', {
        weekday: 'short',
        day: 'numeric',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit'
    });
}