// Dashboard JavaScript - dashboard.js

// Configuration globale pour Chart.js
Chart.defaults.font.family = "Inter, system-ui, sans-serif";
Chart.defaults.font.size = 12;
Chart.defaults.color = "#6B7280";

// Variables globales pour les graphiques
let activitesPeriodeChart, evolutionSuivisChart, coutsProgrammesChart;

// Initialisation des graphiques du tableau de bord
function initDashboardCharts(data) {
    initActivitesPeriodeChart(data.activites_periode);
    initEvolutionSuivisChart(data.evolution_suivis);
    initCoutsProgrammesChart(data.couts_programmes);
    initProgressCircles();
}

// Graphique des activités par période (Doughnut)
function initActivitesPeriodeChart(data) {
    const ctx = document.getElementById("activitesPeriodeChart");
    if (!ctx) return;

    const labels = Object.keys(data);
    const values = Object.values(data);

    const colors = generateColors(labels.length);

    activitesPeriodeChart = new Chart(ctx, {
        type: "doughnut",
        data: {
            labels: labels,
            datasets: [
                {
                    data: values,
                    backgroundColor: colors,
                    borderColor: colors.map((color) =>
                        color.replace("0.8", "1")
                    ),
                    borderWidth: 2,
                    hoverOffset: 4,
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
                        padding: 20,
                        usePointStyle: true,
                        pointStyle: "circle",
                    },
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const total = context.dataset.data.reduce(
                                (a, b) => a + b,
                                0
                            );
                            const percentage = (
                                (context.parsed * 100) /
                                total
                            ).toFixed(1);
                            return `${context.label}: ${context.parsed} (${percentage}%)`;
                        },
                    },
                },
            },
            cutout: "60%",
            animation: {
                animateRotate: true,
                duration: 1000,
            },
        },
    });
}

// Graphique d'évolution des suivis (Line)
function initEvolutionSuivisChart(data) {
    const ctx = document.getElementById("evolutionSuivisChart");
    if (!ctx) return;

    const labels = Object.keys(data);
    const values = Object.values(data);

    evolutionSuivisChart = new Chart(ctx, {
        type: "line",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Nombre de suivis",
                    data: values,
                    borderColor: "#3B82F6",
                    backgroundColor: "rgba(59, 130, 246, 0.1)",
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: "#3B82F6",
                    pointBorderColor: "#FFFFFF",
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: "index",
            },
            plugins: {
                legend: {
                    display: false,
                },
                tooltip: {
                    backgroundColor: "rgba(0, 0, 0, 0.8)",
                    titleColor: "#FFFFFF",
                    bodyColor: "#FFFFFF",
                    borderColor: "#3B82F6",
                    borderWidth: 1,
                },
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                    },
                    ticks: {
                        maxTicksLimit: 6,
                    },
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: "rgba(0, 0, 0, 0.1)",
                    },
                    ticks: {
                        precision: 0,
                    },
                },
            },
            animation: {
                duration: 1000,
                easing: "easeInOutQuart",
            },
        },
    });
}

// Graphique des coûts par programme (Horizontal Bar)
function initCoutsProgrammesChart(data) {
    const ctx = document.getElementById("coutsProgrammesChart");
    if (!ctx) return;

    const labels = data.map((item) => truncateText(item.nom, 25));
    const values = data.map((item) => item.cout);
    const colors = generateColors(labels.length);

    coutsProgrammesChart = new Chart(ctx, {
        type: "bar",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Budget (FCFA)",
                    data: values,
                    backgroundColor: colors,
                    borderColor: colors.map((color) =>
                        color.replace("0.8", "1")
                    ),
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false,
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
                    callbacks: {
                        label: function (context) {
                            return `Budget: ${formatCurrency(
                                context.parsed.x
                            )} FCFA`;
                        },
                    },
                },
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: {
                        color: "rgba(0, 0, 0, 0.1)",
                    },
                    ticks: {
                        callback: function (value) {
                            return formatCurrency(value);
                        },
                    },
                },
                y: {
                    grid: {
                        display: false,
                    },
                },
            },
            animation: {
                duration: 1000,
                easing: "easeInOutQuart",
            },
        },
    });
}

// Initialiser les cercles de progression
function initProgressCircles() {
    const circles = document.querySelectorAll(".circle-progress");

    circles.forEach((circle) => {
        const value = parseFloat(circle.dataset.value);
        const radius = 45;
        const circumference = 2 * Math.PI * radius;
        const strokeDasharray = circumference;
        const strokeDashoffset = circumference - (value / 100) * circumference;

        // Créer le SVG si il n'existe pas
        if (!circle.querySelector("svg")) {
            const svg = `
                <svg width="100" height="100" class="progress-ring">
                    <circle class="progress-ring-circle-bg" cx="50" cy="50" r="${radius}" 
                            fill="transparent" stroke="#e5e7eb" stroke-width="8"/>
                    <circle class="progress-ring-circle" cx="50" cy="50" r="${radius}"
                            fill="transparent" stroke="#3b82f6" stroke-width="8"
                            stroke-dasharray="${strokeDasharray}"
                            stroke-dashoffset="${strokeDashoffset}"
                            stroke-linecap="round"
                            transform="rotate(-90 50 50)"/>
                </svg>
            `;
            circle.insertAdjacentHTML("afterbegin", svg);
        }

        // Animation du cercle
        setTimeout(() => {
            const circleElement = circle.querySelector(".progress-ring-circle");
            circleElement.style.transition = "stroke-dashoffset 1s ease-in-out";
            circleElement.style.strokeDashoffset = strokeDashoffset;
        }, 500);
    });
}

// Fonctions utilitaires
function generateColors(count) {
    const baseColors = [
        "rgba(59, 130, 246, 0.8)", // Blue
        "rgba(16, 185, 129, 0.8)", // Emerald
        "rgba(245, 158, 11, 0.8)", // Amber
        "rgba(239, 68, 68, 0.8)", // Red
        "rgba(139, 92, 246, 0.8)", // Violet
        "rgba(236, 72, 153, 0.8)", // Pink
        "rgba(20, 184, 166, 0.8)", // Teal
        "rgba(251, 146, 60, 0.8)", // Orange
    ];

    const colors = [];
    for (let i = 0; i < count; i++) {
        colors.push(baseColors[i % baseColors.length]);
    }
    return colors;
}

function formatCurrency(value) {
    return new Intl.NumberFormat("fr-FR", {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value);
}

function truncateText(text, maxLength) {
    return text.length > maxLength
        ? text.substring(0, maxLength) + "..."
        : text;
}

// Actualisation du tableau de bord
function refreshDashboard() {
    const refreshBtn = document.getElementById("refreshDashboard");
    const originalHtml = refreshBtn.innerHTML;

    refreshBtn.innerHTML =
        '<i class="bi bi-arrow-clockwise spin"></i> Actualisation...';
    refreshBtn.disabled = true;

    // Simuler un rechargement
    setTimeout(() => {
        window.location.reload();
    }, 1000);
}

// Filtrage par période
function filterByPeriod(days) {
    fetch(`/api/admin/dashboard/stats?period=${days}`)
        .then((response) => response.json())
        .then((data) => {
            updateStatsCards(data);
            showNotification("Données actualisées", "success");
        })
        .catch((error) => {
            console.error("Erreur lors du filtrage:", error);
            showNotification("Erreur lors de l'actualisation", "error");
        });
}

function updateStatsCards(data) {
    // Mettre à jour les cartes de statistiques
    const statsCards = {
        activities: data.activities,
        suivis: data.suivis,
        budget: formatCurrency(data.budget),
    };

    Object.entries(statsCards).forEach(([key, value]) => {
        const element = document.querySelector(`[data-stat="${key}"]`);
        if (element) {
            animateCounter(element, value);
        }
    });
}

function animateCounter(element, target) {
    const start = parseInt(element.textContent.replace(/\D/g, "")) || 0;
    const increment = (target - start) / 30;
    let current = start;

    const timer = setInterval(() => {
        current += increment;
        if (
            (increment > 0 && current >= target) ||
            (increment < 0 && current <= target)
        ) {
            current = target;
            clearInterval(timer);
        }
        element.textContent = Math.floor(current).toLocaleString();
    }, 50);
}

function showNotification(message, type = "info") {
    const notification = document.createElement("div");
    notification.className = `alert alert-${
        type === "error" ? "danger" : type
    } alert-dismissible fade show position-fixed`;
    notification.style.top = "20px";
    notification.style.right = "20px";
    notification.style.zIndex = "9999";
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Gestionnaires d'événements
document.addEventListener("DOMContentLoaded", function () {
    // Bouton d'actualisation
    const refreshBtn = document.getElementById("refreshDashboard");
    if (refreshBtn) {
        refreshBtn.addEventListener("click", refreshDashboard);
    }

    // Filtres de période
    const periodLinks = document.querySelectorAll("[data-period]");
    periodLinks.forEach((link) => {
        link.addEventListener("click", function (e) {
            e.preventDefault();
            const period = this.dataset.period;
            filterByPeriod(period);
        });
    });

    // Animation des cartes au scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: "0px 0px -50px 0px",
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.style.animation = "slideInUp 0.6s ease forwards";
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observer les cartes pour l'animation
    document.querySelectorAll(".card, .small-box").forEach((card) => {
        observer.observe(card);
    });

    // Redimensionnement des graphiques
    let resizeTimeout;
    window.addEventListener("resize", function () {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            if (activitesPeriodeChart) activitesPeriodeChart.resize();
            if (evolutionSuivisChart) evolutionSuivisChart.resize();
            if (coutsProgrammesChart) coutsProgrammesChart.resize();
        }, 250);
    });
});

// Styles CSS dynamiques
const dynamicStyles = `
    .spin {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .progress-ring {
        position: relative;
        transform: rotate(-90deg);
    }
    
    .circle-progress {
        position: relative;
        display: inline-block;
    }
    
    .circle-progress span {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 16px;
        font-weight: bold;
        color: #1f2937;
    }
    
    .small-box {
        transition: all 0.3s ease;
    }
    
    .small-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .card {
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .card:hover {
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
`;

// Injecter les styles
const styleSheet = document.createElement("style");
styleSheet.textContent = dynamicStyles;
document.head.appendChild(styleSheet);
