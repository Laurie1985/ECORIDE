document.addEventListener('DOMContentLoaded', function () {
    // Récupération des données depuis window.carpoolData
    if (!window.carpoolData) {
        console.error('Données du covoiturage non trouvées');
        return;
    }

    const driverId = window.carpoolData.driverId;
    const pricePerSeat = parseInt(window.carpoolData.pricePerSeat);

    // Initialisation
    loadDriverReviews(driverId);
    initPriceCalculator(pricePerSeat);
    initBookingModal();
});

/**
 * Charge les avis du conducteur
 */
function loadDriverReviews(driverId) {
    const reviewsSection = document.getElementById('reviewsSection');

    if (!reviewsSection) {
        return;
    }

    fetch(`/api/reviews/driver/${driverId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            displayReviews(data, driverId, reviewsSection);
        })
        .catch(error => {
            console.error('Erreur chargement avis:', error);
            reviewsSection.innerHTML = '<p class="text-muted text-center">Impossible de charger les avis</p>';
        });
}

/**
 * Affiche les avis dans la section dédiée
 */
function displayReviews(data, driverId, reviewsSection) {
    if (data.success && data.reviews.length > 0) {
        let reviewsHtml = '';

        // Affichage des 3 premiers avis
        data.reviews.slice(0, 3).forEach(review => {
            reviewsHtml += createReviewHtml(review);
        });

        // Lien vers tous les avis si plus de 3
        if (data.reviews.length > 3) {
            reviewsHtml += `
                <div class="text-center">
                    <a href="/reviews/driver/${driverId}" class="btn btn-sm">
                        Voir tous les avis (${data.reviews.length})
                    </a>
                </div>
            `;
        }

        reviewsSection.innerHTML = reviewsHtml;
    } else {
        reviewsSection.innerHTML = '<p class="text-muted text-center">Aucun avis pour ce conducteur</p>';
    }
}

/**
 * Crée le HTML pour un avis
 */
function createReviewHtml(review) {
    const ratingDisplay = `${review.rating}/5`;
    const reviewDate = new Date(review.created_at).toLocaleDateString('fr-FR');

    return `
        <div class="border-bottom pb-3 mb-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-warning"><strong>${ratingDisplay}</strong></div>
                    <p class="mb-1">${escapeHtml(review.comment)}</p>
                    <small class="text-muted">Par un passager • ${reviewDate}</small>
                </div>
            </div>
        </div>
    `;
}

/**
 * Affiche le prix en fonction du nombre de places souhaitées
 */
function initPriceCalculator(pricePerSeat) {
    const seatsSelect = document.getElementById('seats_booked');
    const totalPriceElement = document.getElementById('totalPrice');

    if (!seatsSelect || !totalPriceElement) {
        return;
    }

    seatsSelect.addEventListener('change', function () {
        const seats = parseInt(this.value);
        const total = seats * pricePerSeat;
        totalPriceElement.textContent = total + ' crédits';
    });
}

/**
 * Échappe les caractères HTML pour éviter les injections XSS
 */
function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

/**
 * Initialise la modal de confirmation de réservation
 */
function initBookingModal() {
    const modalButton = document.querySelector('[data-bs-target="#bookingModal"]');
    const seatsSelect = document.getElementById('seats_booked');
    const modalSeats = document.getElementById('modalSeats');
    const modalTotal = document.getElementById('modalTotal');
    const hiddenInput = document.getElementById('hiddenSeatsBooked');

    if (!modalButton || !seatsSelect || !modalSeats || !modalTotal || !hiddenInput) {
        return; // Éléments non trouvés
    }

    const pricePerSeat = parseInt(window.carpoolData.pricePerSeat);

    // Synchroniser avec la modal au clic sur le bouton de réservation
    modalButton.addEventListener('click', function () {
        const seats = parseInt(seatsSelect.value);
        const total = seats * pricePerSeat;

        modalSeats.textContent = seats;
        modalTotal.textContent = total;
        hiddenInput.value = seats;
    });
}