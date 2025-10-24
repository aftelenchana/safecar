const ctxEvolution = document.getElementById('chequesEvolutionChart').getContext('2d');
const chequesEvolutionChart = new Chart(ctxEvolution, {
    type: 'line',
    data: {
        labels: ['1 Jul', '5 Jul', '10 Jul', '15 Jul', '20 Jul', '25 Jul', '30 Jul'],
        datasets: [
            {
                label: 'Pendientes',
                data: [12, 19, 15, 22, 18, 25, 20],
                borderColor: '#fd7e14',
                backgroundColor: 'rgba(253, 126, 20, 0.1)',
                tension: 0.3,
                fill: true
            },
            {
                label: 'Cobrados',
                data: [8, 12, 10, 15, 20, 18, 25],
                borderColor: '#20c997',
                backgroundColor: 'rgba(32, 201, 151, 0.1)',
                tension: 0.3,
                fill: true
            },
            {
                label: 'Rechazados',
                data: [2, 3, 1, 4, 2, 3, 1],
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                tension: 0.3,
                fill: true
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            tooltip: {
                mode: 'index',
                intersect: false,
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        },
        interaction: {
            mode: 'nearest',
            axis: 'x',
            intersect: false
        }
    }
});

// Gráfico de donut para bancos
const ctxDonut = document.getElementById('bancosDonutChart').getContext('2d');
const bancosDonutChart = new Chart(ctxDonut, {
    type: 'doughnut',
    data: {
        labels: ['Banco Nacional', 'Banco Continental', 'Banco de Crédito', 'Otros'],
        datasets: [{
            data: [42, 28, 20, 10],
            backgroundColor: [
                '#0d6efd',
                '#198754',
                '#ffc107',
                '#6c757d'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'right',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return `${context.label}: ${context.raw}% ($${(context.raw * 3456200 / 100).toLocaleString()})`;
                    }
                }
            }
        },
        cutout: '70%'
    }
});

// Gráfico de barras para empresas
const ctxEmpresas = document.getElementById('empresasBarChart').getContext('2d');
const empresasBarChart = new Chart(ctxEmpresas, {
    type: 'bar',
    data: {
        labels: ['Import. XYZ', 'Distrib. ABC', 'Comercio 123', 'Servicios S.A.', 'Otros'],
        datasets: [{
            label: 'Cantidad de cheques',
            data: [38, 32, 25, 18, 43],
            backgroundColor: [
                'rgba(13, 110, 253, 0.7)',
                'rgba(25, 135, 84, 0.7)',
                'rgba(255, 193, 7, 0.7)',
                'rgba(220, 53, 69, 0.7)',
                'rgba(108, 117, 125, 0.7)'
            ],
            borderColor: [
                'rgba(13, 110, 253, 1)',
                'rgba(25, 135, 84, 1)',
                'rgba(255, 193, 7, 1)',
                'rgba(220, 53, 69, 1)',
                'rgba(108, 117, 125, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    afterLabel: function(context) {
                        return `Monto estimado: $${(context.raw * 22155).toLocaleString()}`;
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
