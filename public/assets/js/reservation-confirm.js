/**
 * Gestion du formulaire de confirmation post-trajet
 */
document.addEventListener('DOMContentLoaded', function () {
    const radioGood = document.getElementById('trip_good');
    const radioProblem = document.getElementById('trip_problem');
    const problemDetails = document.getElementById('problemDetails');
    const reviewSection = document.getElementById('reviewSection');
    const commentTextarea = document.getElementById('comment');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');

    /**
     * Met à jour l'affichage selon la sélection utilisateur
     */
    function updateVisibility() {
        if (radioGood.checked) {
            // Trajet réussi : afficher section avis
            problemDetails.classList.add('d-none');
            reviewSection.classList.remove('d-none');
            commentTextarea.removeAttribute('required');
            submitBtn.className = 'btn btn-success btn-lg';
            submitText.textContent = 'Confirmer le trajet';
        } else if (radioProblem.checked) {
            // Problème : afficher section signalement
            reviewSection.classList.add('d-none');
            problemDetails.classList.remove('d-none');
            commentTextarea.setAttribute('required', '');
            submitBtn.className = 'btn btn-warning btn-lg';
            submitText.textContent = 'Signaler le problème';
        }
    }

    // Écouteurs d'événements sur les boutons radio
    radioGood.addEventListener('change', updateVisibility);
    radioProblem.addEventListener('change', updateVisibility);

    /**
     * Validation du formulaire avant soumission
     */
    document.getElementById('confirmationForm').addEventListener('submit', function (e) {
        if (radioProblem.checked && !commentTextarea.value.trim()) {
            e.preventDefault();
            alert('Veuillez décrire le problème rencontré.');
            commentTextarea.focus();
            return false;
        }
    });
});