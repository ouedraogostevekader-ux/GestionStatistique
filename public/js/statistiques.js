/**
 * Fichier JavaScript pour la page des statistiques
 */

// Variables globales
let charts = {};

/**
 * Initialise tous les graphiques de la page statistiques
 */
function initStatistiquesCharts(data) {
    try {
        initEvolutionActivitesChart(data.chartData);
        initRepartitionStatutChart(data.chartData);
        initBudgetTrimestreChart(data.financialStats);
        initPerformanceTrimestreChart(data.performanceStats);
        initBudgetProgrammesChart(data.chartData);
        initCircularProgressBars();
        initEventListeners();
    } catch (error) {
        console.error("Erreur lors de l'initialisation des graphiques:", error);
    }
}

/**
 * Graphique d'évolution mensuelle des activités
 */
function initEvolutionActivitesChart(chartData) {
    const ctx = document.getElementById("evolutionActivitesChart");
    if (!ctx || !chartData.evolution_activites) return;

    charts.evolutionActivites = new Chart(ctx, {
        type: "line",
        data: {
            labels: chartData.mois || [],
            datasets: [
                {
                    label: "Nombre d'activités",
                    data: chartData.evolution_activites || [],
                    borderColor: "rgb(54, 162, 235)",
                    backgroundColor: "rgba(54, 162, 235, 0.1)",
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: "rgb(54, 162, 235)",
                    pointBorderColor: "#fff",
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: false,
                },
                legend: {
                    display: false,
                },
                tooltip: {
                    backgroundColor: "rgba(0,0,0,0.8)",
                    titleColor: "#fff",
                    bodyColor: "#fff",
                    borderColor: "rgb(54, 162, 235)",
                    borderWidth: 1,
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: "rgba(0,0,0,0.1)",
                    },
                    ticks: {
                        stepSize: 1,
                    },
                },
                x: {
                    grid: {
                        display: false,
                    },
                },
            },
            elements: {
                point: {
                    hoverRadius: 8,
                },
            },
        },
    });
}

/**
 * Graphique de répartition par statut (doughnut)
 */
function initRepartitionStatutChart(chartData) {
    const ctx = document.getElementById("repartitionStatutChart");
    if (!ctx || !chartData.repartition_statut) return;

    charts.repartitionStatut = new Chart(ctx, {
        type: "doughnut",
        data: {
            labels: chartData.labels_statut || [],
            datasets: [
                {
                    data: chartData.repartition_statut || [],
                    backgroundColor: [
                        "rgba(108, 117, 125, 0.8)", // Non commencées - gris
                        "rgba(255, 193, 7, 0.8)", // En cours - jaune
                        "rgba(40, 167, 69, 0.8)", // Terminées - vert
                    ],
                    borderColor: [
                        "rgb(108, 117, 125)",
                        "rgb(255, 193, 7)",
                        "rgb(40, 167, 69)",
                    ],
                    borderWidth: 2,
                    hoverOffset: 10,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: "bottom",
                    labels: {
                        usePointStyle: true,
                        pointStyle: "circle",
                        padding: 20,
                    },
                },
                tooltip: {
                    backgroundColor: "rgba(0,0,0,0.8)",
                    titleColor: "#fff",
                    bodyColor: "#fff",
                    borderWidth: 1,
                    callbacks: {
                        label: function (context) {
                            const total = context.dataset.data.reduce(
                                (a, b) => a + b,
                                0
                            );
                            const percentage = (
                                (context.parsed / total) *
                                100
                            ).toFixed(1);
                            return `${context.label}: ${context.parsed} (${percentage}%)`;
                        },
                    },
                },
            },
            cutout: "60%",
        },
    });
}

/**
 * Graphique budget par trimestre
 */
function initBudgetTrimestreChart(financialStats) {
    const ctx = document.getElementById("budgetTrimestreChart");
    if (!ctx || !financialStats.budget_par_trimestre) return;

    const trimestres = Object.keys(financialStats.budget_par_trimestre);
    const budgets = Object.values(financialStats.budget_par_trimestre);

    charts.budgetTrimestre = new Chart(ctx, {
        type: "bar",
        data: {
            labels: trimestres,
            datasets: [
                {
                    label: "Budget (FCFA)",
                    data: budgets,
                    backgroundColor: [
                        "rgba(54, 162, 235, 0.8)",
                        "rgba(255, 99, 132, 0.8)",
                        "rgba(255, 206, 86, 0.8)",
                        "rgba(75, 192, 192, 0.8)",
                    ],
                    borderColor: [
                        "rgb(54, 162, 235)",
                        "rgb(255, 99, 132)",
                        "rgb(255, 206, 86)",
                        "rgb(75, 192, 192)",
                    ],
                    borderWidth: 2,
                    borderRadius: 4,
                    hoverBackgroundColor: [
                        "rgba(54, 162, 235, 1)",
                        "rgba(255, 99, 132, 1)",
                        "rgba(255, 206, 86, 1)",
                        "rgba(75, 192, 192, 1)",
                    ],
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                },
                tooltip: {
                    backgroundColor: "rgba(0,0,0,0.8)",
                    titleColor: "#fff",
                    bodyColor: "#fff",
                    borderWidth: 1,
                    callbacks: {
                        label: function (context) {
                            return `Budget: ${new Intl.NumberFormat(
                                "fr-FR"
                            ).format(context.parsed.y)} FCFA`;
                        },
                    },
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: "rgba(0,0,0,0.1)",
                    },
                    ticks: {
                        callback: function (value) {
                            return (
                                new Intl.NumberFormat("fr-FR", {
                                    notation: "compact",
                                    compactDisplay: "short",
                                }).format(value) + " FCFA"
                            );
                        },
                    },
                },
                x: {
                    grid: {
                        display: false,
                    },
                },
            },
        },
    });
}

/**
 * Graphique performance par trimestre
 */
function initPerformanceTrimestreChart(performanceStats) {
    const ctx = document.getElementById("performanceTrimestreChart");
    if (!ctx || !performanceStats.performance_par_trimestre) return;

    const trimestres = Object.keys(performanceStats.performance_par_trimestre);
    const performances = Object.values(
        performanceStats.performance_par_trimestre
    );

    charts.performanceTrimestre = new Chart(ctx, {
        type: "line",
        data: {
            labels: trimestres,
            datasets: [
                {
                    label: "Performance (%)",
                    data: performances,
                    borderColor: "rgb(40, 167, 69)",
                    backgroundColor: "rgba(40, 167, 69, 0.1)",
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: "rgb(40, 167, 69)",
                    pointBorderColor: "#fff",
                    pointBorderWidth: 3,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                },
                tooltip: {
                    backgroundColor: "rgba(0,0,0,0.8)",
                    titleColor: "#fff",
                    bodyColor: "#fff",
                    borderColor: "rgb(40, 167, 69)",
                    borderWidth: 1,
                    callbacks: {
                        label: function (context) {
                            return `Performance: ${context.parsed.y}%`;
                        },
                    },
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: {
                        color: "rgba(0,0,0,0.1)",
                    },
                    ticks: {
                        callback: function (value) {
                            return value + "%";
                        },
                    },
                },
                x: {
                    grid: {
                        display: false,
                    },
                },
            },
        },
    });
}

/**
 * Graphique budget par programmes
 */
function initBudgetProgrammesChart(chartData) {
    const ctx = document.getElementById("budgetProgrammesChart");
    if (
        !ctx ||
        !chartData.budget_programmes ||
        !chartData.budget_programmes.labels.length
    )
        return;

    charts.budgetProgrammes = new Chart(ctx, {
        type: "bar",
        data: {
            labels: chartData.budget_programmes.labels,
            datasets: [
                {
                    label: "Budget (FCFA)",
                    data: chartData.budget_programmes.data,
                    backgroundColor: "rgba(54, 162, 235, 0.8)",
                    borderColor: "rgb(54, 162, 235)",
                    borderWidth: 2,
                    hoverBackgroundColor: "rgba(54, 162, 235, 1)",
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: "y",
            plugins: {
                legend: {
                    display: false,
                },
                tooltip: {
                    backgroundColor: "rgba(0,0,0,0.8)",
                    titleColor: "#fff",
                    bodyColor: "#fff",
                    borderWidth: 1,
                    callbacks: {
                        label: function (context) {
                            return `Budget: ${new Intl.NumberFormat(
                                "fr-FR"
                            ).format(context.parsed.x)} FCFA`;
                        },
                    },
                },
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: {
                        color: "rgba(0,0,0,0.1)",
                    },
                    ticks: {
                        callback: function (value) {
                            return (
                                new Intl.NumberFormat("fr-FR", {
                                    notation: "compact",
                                    compactDisplay: "short",
                                }).format(value) + " FCFA"
                            );
                        },
                    },
                },
                y: {
                    grid: {
                        display: false,
                    },
                    ticks: {
                        maxRotation: 0,
                        callback: function (value, index) {
                            const label = this.getLabelForValue(value);
                            return label.length > 30
                                ? label.substring(0, 30) + "..."
                                : label;
                        },
                    },
                },
            },
        },
    });
}

/**
 * Initialise les barres de progression circulaires
 */
function initCircularProgressBars() {
    const progressBars = document.querySelectorAll(".circle-progress");

    progressBars.forEach((bar) => {
        const value = parseInt(bar.dataset.value || 0);
        const circumference = 2 * Math.PI * 45; // rayon de 45px

        // Créer l'élément SVG
        const svg = document.createElementNS(
            "http://www.w3.org/2000/svg",
            "svg"
        );
        svg.setAttribute("width", "120");
        svg.setAttribute("height", "120");
        svg.style.transform = "rotate(-90deg)";

        // Cercle de fond
        const backgroundCircle = document.createElementNS(
            "http://www.w3.org/2000/svg",
            "circle"
        );
        backgroundCircle.setAttribute("cx", "60");
        backgroundCircle.setAttribute("cy", "60");
        backgroundCircle.setAttribute("r", "45");
        backgroundCircle.setAttribute("fill", "none");
        backgroundCircle.setAttribute("stroke", "#e9ecef");
        backgroundCircle.setAttribute("stroke-width", "8");

        // Cercle de progression
        const progressCircle = document.createElementNS(
            "http://www.w3.org/2000/svg",
            "circle"
        );
        progressCircle.setAttribute("cx", "60");
        progressCircle.setAttribute("cy", "60");
        progressCircle.setAttribute("r", "45");
        progressCircle.setAttribute("fill", "none");
        progressCircle.setAttribute("stroke", getProgressColor(value));
        progressCircle.setAttribute("stroke-width", "8");
        progressCircle.setAttribute("stroke-linecap", "round");
        progressCircle.setAttribute("stroke-dasharray", circumference);
        progressCircle.setAttribute(
            "stroke-dashoffset",
            circumference - (value / 100) * circumference
        );

        svg.appendChild(backgroundCircle);
        svg.appendChild(progressCircle);

        // Insérer le SVG avant le span
        const span = bar.querySelector("span");
        bar.insertBefore(svg, span);

        // Animation
        setTimeout(() => {
            progressCircle.style.transition =
                "stroke-dashoffset 1.5s ease-in-out";
            progressCircle.setAttribute(
                "stroke-dashoffset",
                circumference - (value / 100) * circumference
            );
        }, 100);
    });
}

/**
 * Retourne la couleur appropriée selon la valeur de progression
 */
function getProgressColor(value) {
    if (value >= 80) return "#28a745"; // Vert
    if (value >= 60) return "#ffc107"; // Jaune
    if (value >= 40) return "#fd7e14"; // Orange
    return "#dc3545"; // Rouge
}

/**
 * Initialise les événements
 */
function initEventListeners() {
    // Actualiser les statistiques
    const refreshBtn = document.getElementById("refreshStats");
    if (refreshBtn) {
        refreshBtn.addEventListener("click", function () {
            location.reload();
        });
    }

    // Exporter les statistiques
    const exportBtn = document.getElementById("exportStats");
    if (exportBtn) {
        exportBtn.addEventListener("click", function () {
            exportStatistics();
        });
    }

    // Auto-soumission du formulaire de filtre
    const filterForm = document.getElementById("filterForm");
    const filterSelects = filterForm?.querySelectorAll("select");

    if (filterSelects) {
        filterSelects.forEach((select) => {
            select.addEventListener("change", function () {
                // Petit délai pour améliorer l'UX
                setTimeout(() => {
                    filterForm.submit();
                }, 100);
            });
        });
    }

    // Animation au scroll pour les cartes
    const cards = document.querySelectorAll(".card");
    const observerOptions = {
        threshold: 0.1,
        rootMargin: "0px 0px -50px 0px",
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.style.animation = "fadeInUp 0.6s ease forwards";
            }
        });
    }, observerOptions);

    cards.forEach((card) => {
        card.style.opacity = "0";
        card.style.transform = "translateY(20px)";
        observer.observe(card);
    });
}

/**
 * Exporte les statistiques en PDF (fonction basique)
 */
function exportStatistics() {
    // Cette fonction pourrait être étendue pour utiliser jsPDF ou une autre solution
    const printWindow = window.open("", "_blank");
    const content = document.querySelector(".content").innerHTML;

    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Statistiques - Export</title>
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    margin: 20px; 
                    color: #333; 
                }
                .card { 
                    border: 1px solid #ddd; 
                    margin: 10px 0; 
                    padding: 15px; 
                    border-radius: 5px; 
                }
                .small-box { 
                    display: inline-block; 
                    margin: 10px; 
                    padding: 15px; 
                    border: 1px solid #ddd; 
                    min-width: 150px; 
                }
                .table { 
                    width: 100%; 
                    border-collapse: collapse; 
                }
                .table th, .table td { 
                    border: 1px solid #ddd; 
                    padding: 8px; 
                    text-align: left; 
                }
                .progress { 
                    display: none; 
                }
                canvas { 
                    display: none; 
                }
                .btn, .dropdown { 
                    display: none; 
                }
                @media print {
                    body { margin: 0; }
                    .no-print { display: none !important; }
                }
            </style>
        </head>
        <body>
            <h1>Rapport Statistiques</h1>
            <p>Généré le: ${new Date().toLocaleDateString("fr-FR")}</p>
            ${content}
        </body>
        </html>
    `);

    printWindow.document.close();

    // Attendre que le contenu soit chargé avant d'imprimer
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 1000);
}

/**
 * Met à jour les graphiques avec de nouvelles données
 */
function updateCharts(newData) {
    try {
        // Détruire les anciens graphiques
        Object.values(charts).forEach((chart) => {
            if (chart && typeof chart.destroy === "function") {
                chart.destroy();
            }
        });

        // Réinitialiser
        charts = {};

        // Recréer avec les nouvelles données
        initStatistiquesCharts(newData);
    } catch (error) {
        console.error("Erreur lors de la mise à jour des graphiques:", error);
    }
}

/**
 * Fonction utilitaire pour formater les nombres
 */
function formatNumber(num, decimals = 0) {
    return new Intl.NumberFormat("fr-FR", {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals,
    }).format(num);
}

/**
 * Fonction utilitaire pour formater les montants en FCFA
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat("fr-FR").format(amount) + " FCFA";
}

/**
 * Ajoute des animations CSS personnalisées
 */
function addCustomAnimations() {
    const style = document.createElement("style");
    style.textContent = `
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }
        
        .circle-progress {
            position: relative;
            display: inline-block;
            margin: 10px;
        }
        
        .circle-progress span {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        
        .small-box:hover {
            animation: pulse 0.6s ease-in-out;
            cursor: pointer;
        }
        
        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .progress-bar {
            transition: width 1s ease-in-out;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .badge {
            transition: transform 0.2s ease;
        }
        
        .badge:hover {
            transform: scale(1.1);
        }
    `;
    document.head.appendChild(style);
}

// Initialiser les animations personnalisées au chargement
document.addEventListener("DOMContentLoaded", function () {
    addCustomAnimations();
});

// Gestion responsive des graphiques
window.addEventListener("resize", function () {
    Object.values(charts).forEach((chart) => {
        if (chart && typeof chart.resize === "function") {
            chart.resize();
        }
    });
});

// Export des fonctions pour utilisation externe
if (typeof module !== "undefined" && module.exports) {
    module.exports = {
        initStatistiquesCharts,
        updateCharts,
        formatNumber,
        formatCurrency,
    };
}
