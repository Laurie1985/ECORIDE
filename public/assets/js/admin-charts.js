// Variables globales
let carpoolsChart = null;
let earningsChart = null;

// Démarrage
document.addEventListener('DOMContentLoaded', function () {
    loadData();

    // Événements
    document.getElementById('periodSelect').addEventListener('change', loadData);
    document.getElementById('refreshBtn').addEventListener('click', loadData);
});

// Charger toutes les données
function loadData() {
    const period = document.getElementById('periodSelect').value;

    loadCarpools(period);
    loadEarnings(period);
}

// Charger les covoiturages
function loadCarpools(period) {
    fetch(`/admin/api/daily-carpools?period=${period}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                createCarpoolsChart(data.data);
                updateCarpoolsSummary(data.data);
            }
        })
        .catch(error => console.error('Erreur covoiturages:', error));
}

// Charger les revenus
function loadEarnings(period) {
    fetch(`/admin/api/daily-earnings?period=${period}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                createEarningsChart(data.data);
                updateEarningsSummary(data.data);
            }
        })
        .catch(error => console.error('Erreur revenus:', error));
}

// Créer graphique covoiturages
function createCarpoolsChart(data) {
    // Masquer le chargement et afficher le graphique
    document.getElementById('carpoolsLoading').style.display = 'none';
    document.getElementById('carpoolsChart').parentElement.style.display = 'block';

    const ctx = document.getElementById('carpoolsChart').getContext('2d');

    // Détruire l'ancien graphique
    if (carpoolsChart) carpoolsChart.destroy();

    carpoolsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map(item => formatDate(item.date)),
            datasets: [{
                label: 'Covoiturages effectués',
                data: data.map(item => item.count),
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            },
            plugins: { legend: { display: false } }
        }
    });
}

// Créer graphique revenus
function createEarningsChart(data) {
    // Masquer le chargement et afficher le graphique
    document.getElementById('earningsLoading').style.display = 'none';
    document.getElementById('earningsChart').parentElement.style.display = 'block';

    const ctx = document.getElementById('earningsChart').getContext('2d');

    // Détruire l'ancien graphique
    if (earningsChart) earningsChart.destroy();

    earningsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(item => formatDate(item.date)),
            datasets: [{
                label: 'Revenus',
                data: data.map(item => parseFloat(item.amount || item.daily_total || 0)),
                backgroundColor: '#28a745'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            return value + ' crédits';
                        }
                    }
                }
            },
            plugins: { legend: { display: false } }
        }
    });
}

// Mettre à jour résumé covoiturages
function updateCarpoolsSummary(data) {
    const total = data.reduce((sum, item) => sum + parseInt(item.count), 0);
    const average = data.length > 0 ? (total / data.length).toFixed(1) : 0;

    document.getElementById('totalCarpools').textContent = total;
    document.getElementById('avgCarpools').textContent = average;
}

// Mettre à jour résumé revenus
function updateEarningsSummary(data) {
    const total = data.reduce((sum, item) => sum + parseFloat(item.amount || item.daily_total || 0), 0);
    const average = data.length > 0 ? (total / data.length).toFixed(2) : 0;

    document.getElementById('totalEarnings').textContent = total.toFixed(2) + ' crédits';
    document.getElementById('avgEarnings').textContent = average + ' crédits';
}

// Formater une date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' });
}