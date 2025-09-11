document.addEventListener('DOMContentLoaded', function () {
    setupConfirmations();
});

/**
 * Confirmations pour les actions importantes
 */
function setupConfirmations() {
    // Confirmation pour démarrer un trajet
    const startForms = document.querySelectorAll('form[action*="/start"]');
    startForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            if (!confirm('Démarrer ce trajet ?')) {
                e.preventDefault();
            }
        });
    });

    // Confirmation pour terminer un trajet
    const completeForms = document.querySelectorAll('form[action*="/complete"]');
    completeForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            if (!confirm('Marquer ce trajet comme terminé ?')) {
                e.preventDefault();
            }
        });
    });

    // Validation pour l'annulation
    const cancelModals = document.querySelectorAll('[id^="cancelModal"]');
    cancelModals.forEach(modal => {
        const form = modal.querySelector('form');
        if (form) {
            form.addEventListener('submit', function (e) {
                if (!confirm('Confirmer l\'annulation de ce covoiturage ?')) {
                    e.preventDefault();
                }
            });
        }
    });
}