document.addEventListener("DOMContentLoaded", function () {
    // Initialize the DataTable
    let table = new DataTable("#example2", {
        responsive: true,
        pageLength: 10,
        lengthMenu: [
            [5, 10, 25, 50, -1],
            [5, 10, 25, 50, "Tout"],
        ],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json",
        },
    });
});

document.addEventListener("DOMContentLoaded", function () {
    // Initialize the DataTable
    let table = new DataTable("#activiteEx", {
        // responsive: true,
        pageLength: 10,
        lengthMenu: [
            [5, 10, 25, 50, -1],
            [5, 10, 25, 50, "Tout"],
        ],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json",
        },
    });
});

document.addEventListener("DOMContentLoaded", function () {
    // Initialize the DataTable
    let table = new DataTable("#stratEx", {
        // responsive: true,
        pageLength: 10,
        lengthMenu: [
            [5, 10, 25, 50, -1],
            [5, 10, 25, 50, "Tout"],
        ],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json",
        },
    });
});

document.addEventListener("DOMContentLoaded", function () {
    // Initialize the DataTable
    let table = new DataTable("#specEx", {
        // responsive: true,
        pageLength: 10,
        lengthMenu: [
            [5, 10, 25, 50, -1],
            [5, 10, 25, 50, "Tout"],
        ],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json",
        },
    });
});

// Masquer automatiquement les alertes après 10 secondes (10000 ms)
setTimeout(() => {
    let alerts = document.querySelectorAll(".alert");
    alerts.forEach((alert) => {
        let bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 10000);
