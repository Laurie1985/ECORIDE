// Validation du formulaire de création d'employé
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form[action="/admin/employees/create"]');
    if (!form) return;

    const password = document.getElementById('password');
    const submitBtn = document.getElementById('submitBtn');

    // Validation en temps réel du mot de passe
    if (password) {
        password.addEventListener('input', function () {
            const value = password.value;

            // Vérification des critères de mot de passe
            if (value.length >= 9 &&
                /[A-Z]/.test(value) &&
                /[a-z]/.test(value) &&
                /[0-9]/.test(value)) {
                password.classList.add('is-valid');
                password.classList.remove('is-invalid');
            } else {
                password.classList.add('is-invalid');
                password.classList.remove('is-valid');
            }
        });
    }

    // Validation à la soumission
    form.addEventListener('submit', function (e) {
        // Désactiver le bouton pour éviter double soumission
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Création en cours...';
        }
    });
});