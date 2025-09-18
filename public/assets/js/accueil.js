
const ctx = document.getElementById('myChart').getContext('2d');
const myChart = new Chart(ctx, {
    type: 'bar', // Type de diagramme en barres
    data: {
        labels: ['2014', '2015', '2016', '2017', '2018', '2019', '2020', '2021', '2022', '2023'], // Années
        datasets: [
            {
                label: 'Bon',
                data: [23.4, 23.4, 30.1, 10.0, 10.1, 8.6, 12.7, 13.9, 16.4, 14.1],
                backgroundColor: 'rgba(75, 192, 192, 0.8)',
            },
            {
                label: 'Moyen',
                data: [54.6, 54.6, 54.6, 31.0, 31.0, 29.9, 26.9, 26.9, 28.8, 26.6],
                backgroundColor: 'rgba(153, 102, 255, 0.8)',
            },
            {
                label: 'Mauvais',
                data: [14.7, 14.6, 14.0, 56.2, 55.7, 46.4, 47.4, 47.4, 51.4, 53.4],
                backgroundColor: 'rgba(255, 99, 132, 0.8)',
            },
            {
                label: 'En travaux',
                data: [7.4, 7.4, 1.4, 2.7, 3.1, 15.0, 12.9, 11.8, 3.5, 5.9],
                backgroundColor: 'rgba(255, 206, 86, 0.8)',
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
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': ' + context.raw + '%'; // Affiche la valeur en %
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Pourcentage (%)'
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Années'
                }
            }
        }
    }
});
