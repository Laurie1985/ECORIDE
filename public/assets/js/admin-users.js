/**
 * gestion des utilisateurs admin
 */
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchUsers'); //champ de recherche
    const filterButtons = document.querySelectorAll('[data-filter]'); //boutons de filtres
    const userRows = document.querySelectorAll('.user-row'); //lignes du tableau

    let currentFilter = 'all'; //filtre appliqué actuellement

    // Fonction de recherche en temps réel
    if (searchInput) {
        searchInput.addEventListener('input', function () { //écoute chaque entrée dans le champ de recherche
            const searchTerm = this.value.toLowerCase(); //convertit le texte en minuscules pour ne pas être sensible à la casse
            filterAndSearch(currentFilter, searchTerm); //appelle la fonction combinée
        });
    }

    // Fonction de filtrage par statut
    filterButtons.forEach(button => {
        button.addEventListener('click', function () {
            // Mettre à jour les boutons actifs
            filterButtons.forEach(btn => btn.classList.remove('active')); //retire la classe "active" de tous les boutons
            this.classList.add('active'); //ajoute "active" au bouton sélectionné

            currentFilter = this.getAttribute('data-filter');
            const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
            filterAndSearch(currentFilter, searchTerm);
        });
    });

    /**
     * Fonction combinée de filtrage et recherche
     */
    function filterAndSearch(filter, searchTerm) {
        let visibleCount = 0;

        userRows.forEach(row => {
            const status = row.getAttribute('data-status');
            const username = row.getAttribute('data-username');
            const email = row.getAttribute('data-email');

            // Vérifier le filtre de statut
            const statusMatch = filter === 'all' || status === filter;

            // Vérifier la recherche
            const searchMatch = searchTerm === '' ||
                username.includes(searchTerm) ||
                email.includes(searchTerm);

            // Afficher/masquer la ligne
            if (statusMatch && searchMatch) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Afficher un message si aucun résultat
        updateNoResultsMessage(visibleCount);
    }

    /**
     * Affiche un message si aucun utilisateur ne correspond aux critères
     */
    function updateNoResultsMessage(count) {
        let noResultsRow = document.getElementById('noResultsRow');

        if (count === 0) {
            //crée et affiche un message si aucun résultat
            if (!noResultsRow) {
                noResultsRow = document.createElement('tr');
                noResultsRow.id = 'noResultsRow';
                noResultsRow.innerHTML = `
                    <td colspan="7" class="text-center py-4">
                        <p class="text-muted mb-0">Aucun utilisateur ne correspond à vos critères</p>
                    </td>
                `;
                document.getElementById('usersTableBody').appendChild(noResultsRow);
            }
            noResultsRow.style.display = '';
        } else {
            //cache le message si des résultats existent
            if (noResultsRow) {
                noResultsRow.style.display = 'none';
            }
        }
    }

    /**
     * Fonction pour confirmer les actions de suspension/activation
     */
    window.confirmUserAction = function (action, username) {
        const messages = {
            suspend: `Êtes-vous sûr de vouloir suspendre ${username} ?`,
            activate: `Êtes-vous sûr de vouloir réactiver ${username} ?`
        };

        return confirm(messages[action] || 'Êtes-vous sûr de vouloir effectuer cette action ?');
    };

    /**
     * Fonction pour réinitialiser les filtres
     */
    window.resetFilters = function () {
        if (searchInput) {
            searchInput.value = '';
        }

        filterButtons.forEach(btn => btn.classList.remove('active'));
        const allButton = document.querySelector('[data-filter="all"]');
        if (allButton) {
            allButton.classList.add('active');
        }

        currentFilter = 'all';
        filterAndSearch('all', '');
    };

    // Initialisation : appliquer le filtre par défaut
    filterAndSearch('all', '');
});