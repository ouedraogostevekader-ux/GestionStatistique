document.addEventListener("DOMContentLoaded", function () {
    // Solution 1: Sans DataTable (plus simple et compatible avec la pagination Laravel)

    // Toggle des filtres
    const toggleFiltersBtn = document.getElementById("toggleFilters");
    const filtersSection = document.getElementById("filters-section");

    if (toggleFiltersBtn && filtersSection) {
        toggleFiltersBtn.addEventListener("click", function () {
            const isVisible = filtersSection.style.display !== "none";
            filtersSection.style.display = isVisible ? "none" : "block";

            const icon = this.querySelector("i");
            if (icon) {
                icon.className = isVisible
                    ? "bi bi-funnel"
                    : "bi bi-funnel-fill";
            }

            // Sauvegarder l'état dans localStorage
            localStorage.setItem("activites-filters-visible", !isVisible);
        });

        // Restaurer l'état des filtres
        const filtersVisible = localStorage.getItem(
            "activites-filters-visible"
        );
        if (filtersVisible === "true") {
            filtersSection.style.display = "block";
            const icon = toggleFiltersBtn.querySelector("i");
            if (icon) {
                icon.className = "bi bi-funnel-fill";
            }
        }
    }

    // Auto-submit des filtres principaux
    const autoSubmitFilters = ["annee", "trimestre", "programme_budgetaire_id"];
    autoSubmitFilters.forEach((filterId) => {
        const filterElement = document.getElementById(filterId);
        if (filterElement) {
            filterElement.addEventListener("change", function () {
                // Petit délai pour améliorer l'UX
                setTimeout(() => {
                    document.getElementById("filtersForm").submit();
                }, 100);
            });
        }
    });

    // Confirmation de suppression améliorée
    document.querySelectorAll('form[action*="/destroy"]').forEach((form) => {
        form.addEventListener("submit", function (e) {
            e.preventDefault();

            const activiteName = this.closest("tr")
                .querySelector("td:nth-child(2) .fw-semibold")
                .textContent.trim();
            const confirmMessage = `Êtes-vous sûr de vouloir supprimer l'activité :\n"${activiteName.substring(
                0,
                100
            )}..." ?`;

            if (confirm(confirmMessage)) {
                this.submit();
            }
        });
    });

    // Initialiser les tooltips Bootstrap si disponible
    if (typeof bootstrap !== "undefined" && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(
            document.querySelectorAll('[data-bs-toggle="tooltip"]')
        );
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Tri simple des colonnes (optionnel, sans DataTable)
    addSimpleTableSorting();

    // Recherche simple dans le tableau (optionnel)
    addSimpleTableSearch();
});

// Fonction pour ajouter un tri simple
function addSimpleTableSorting() {
    const table = document.getElementById("activitesTable");
    if (!table) return;

    const headers = table.querySelectorAll("th");
    headers.forEach((header, index) => {
        // Exclure certaines colonnes du tri
        if (index === headers.length - 1) return; // Colonne Actions

        header.style.cursor = "pointer";
        header.innerHTML +=
            ' <i class="bi bi-arrow-down-up ms-1 text-muted" style="font-size: 0.8em;"></i>';

        header.addEventListener("click", () => {
            sortTable(table, index);
        });
    });
}

// Fonction de tri simple
function sortTable(table, columnIndex) {
    const tbody = table.querySelector("tbody");
    const rows = Array.from(tbody.querySelectorAll("tr"));

    // Déterminer l'ordre de tri
    const header = table.querySelectorAll("th")[columnIndex];
    const isAscending = !header.classList.contains("sort-desc");

    // Supprimer les classes de tri précédentes
    table.querySelectorAll("th").forEach((th) => {
        th.classList.remove("sort-asc", "sort-desc");
        const icon = th.querySelector("i");
        if (icon) icon.className = "bi bi-arrow-down-up ms-1 text-muted";
    });

    // Ajouter la nouvelle classe
    header.classList.add(isAscending ? "sort-asc" : "sort-desc");
    const icon = header.querySelector("i");
    if (icon) {
        icon.className = isAscending
            ? "bi bi-arrow-up ms-1 text-primary"
            : "bi bi-arrow-down ms-1 text-primary";
    }

    // Trier les lignes
    rows.sort((a, b) => {
        const aVal = a.cells[columnIndex].textContent.trim();
        const bVal = b.cells[columnIndex].textContent.trim();

        // Tri numérique pour les colonnes de coût
        if (columnIndex === 4) {
            // Colonne coût
            const aNum =
                parseFloat(aVal.replace(/[^\d,]/g, "").replace(",", ".")) || 0;
            const bNum =
                parseFloat(bVal.replace(/[^\d,]/g, "").replace(",", ".")) || 0;
            return isAscending ? aNum - bNum : bNum - aNum;
        }

        // Tri alphabétique
        return isAscending
            ? aVal.localeCompare(bVal)
            : bVal.localeCompare(aVal);
    });

    // Réorganiser le tableau
    rows.forEach((row) => tbody.appendChild(row));

    // Remettre à jour les numéros de ligne
    updateRowNumbers();
}

// Fonction de recherche simple
function addSimpleTableSearch() {
    // Créer un champ de recherche
    const table = document.getElementById("activitesTable");
    if (!table) return;

    const searchContainer = document.createElement("div");
    searchContainer.className =
        "mb-3 d-flex justify-content-between align-items-center";

    const searchGroup = document.createElement("div");
    searchGroup.className = "d-flex align-items-center";

    const searchLabel = document.createElement("label");
    searchLabel.textContent = "Rechercher : ";
    searchLabel.className = "me-2 mb-0 fw-semibold";

    const searchInput = document.createElement("input");
    searchInput.type = "text";
    searchInput.className = "form-control form-control-sm";
    searchInput.placeholder = "Rechercher dans le tableau...";
    searchInput.style.width = "300px";

    // Ajouter les boutons d'export
    const exportButtons = createExportButtons();

    searchGroup.appendChild(searchLabel);
    searchGroup.appendChild(searchInput);
    searchContainer.appendChild(searchGroup);
    searchContainer.appendChild(exportButtons);

    // Insérer avant le tableau
    table.parentNode.insertBefore(searchContainer, table);

    // Fonction de recherche
    searchInput.addEventListener("input", function () {
        const searchTerm = this.value.toLowerCase();
        const rows = table.querySelectorAll("tbody tr");

        rows.forEach((row) => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? "" : "none";
        });

        updateRowNumbers();
    });
}

// Créer les boutons d'export
function createExportButtons() {
    const buttonGroup = document.createElement("div");
    buttonGroup.className = "btn-group btn-group-sm";

    const buttons = [
        {
            text: "CSV",
            icon: "bi-file-earmark-csv",
            class: "btn-outline-primary",
            action: "csv",
        },
        {
            text: "Excel",
            icon: "bi-file-earmark-excel",
            class: "btn-outline-success",
            action: "excel",
        },
        {
            text: "PDF",
            icon: "bi-file-earmark-pdf",
            class: "btn-outline-danger",
            action: "pdf",
        },
        {
            text: "Imprimer",
            icon: "bi-printer",
            class: "btn-outline-dark",
            action: "print",
        },
    ];

    buttons.forEach((btnConfig) => {
        const btn = document.createElement("button");
        btn.className = `btn ${btnConfig.class}`;
        btn.innerHTML = `<i class="bi ${btnConfig.icon} me-1"></i>${btnConfig.text}`;
        btn.addEventListener("click", () => exportTable(btnConfig.action));
        buttonGroup.appendChild(btn);
    });

    return buttonGroup;
}

// Fonctions d'export simplifiées
function exportTable(format) {
    const table = document.getElementById("activitesTable");
    if (!table) return;

    const visibleRows = Array.from(table.querySelectorAll("tbody tr")).filter(
        (row) => row.style.display !== "none"
    );
    const headers = Array.from(table.querySelectorAll("thead th")).slice(0, -1); // Exclure Actions

    switch (format) {
        case "csv":
            exportToCSV(headers, visibleRows);
            break;
        case "excel":
            exportToExcel(headers, visibleRows);
            break;
        case "pdf":
            exportToPDF(headers, visibleRows);
            break;
        case "print":
            printTable(headers, visibleRows);
            break;
    }
}

function exportToCSV(headers, rows) {
    let csv = headers.map((h) => `"${h.textContent}"`).join(",") + "\n";

    rows.forEach((row) => {
        const cells = Array.from(row.cells).slice(0, -1); // Exclure Actions
        csv +=
            cells.map((cell) => `"${cell.textContent.trim()}"`).join(",") +
            "\n";
    });

    downloadFile(
        csv,
        "activites_" + new Date().toISOString().split("T")[0] + ".csv",
        "text/csv"
    );
}

function exportToExcel(headers, rows) {
    // Version simplifiée - génère un CSV avec extension .xls
    let content = headers.map((h) => h.textContent).join("\t") + "\n";

    rows.forEach((row) => {
        const cells = Array.from(row.cells).slice(0, -1);
        content +=
            cells.map((cell) => cell.textContent.trim()).join("\t") + "\n";
    });

    downloadFile(
        content,
        "activites_" + new Date().toISOString().split("T")[0] + ".xls",
        "application/vnd.ms-excel"
    );
}

function exportToPDF(headers, rows) {
    // Ouvrir une nouvelle fenêtre pour l'impression PDF
    const printWindow = window.open("", "_blank");
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Liste des activités</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; font-weight: bold; }
                h1 { color: #333; }
            </style>
        </head>
        <body>
            <h1>Liste des activités - ${new Date().toLocaleDateString(
                "fr-FR"
            )}</h1>
            <table>
                <thead><tr>${headers
                    .map((h) => `<th>${h.textContent}</th>`)
                    .join("")}</tr></thead>
                <tbody>${rows
                    .map(
                        (row) =>
                            `<tr>${Array.from(row.cells)
                                .slice(0, -1)
                                .map(
                                    (cell) =>
                                        `<td>${cell.textContent.trim()}</td>`
                                )
                                .join("")}</tr>`
                    )
                    .join("")}</tbody>
            </table>
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

function printTable(headers, rows) {
    exportToPDF(headers, rows);
}

function downloadFile(content, filename, contentType) {
    const blob = new Blob([content], { type: contentType });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

// Mettre à jour les numéros de ligne
function updateRowNumbers() {
    const table = document.getElementById("activitesTable");
    if (!table) return;

    const visibleRows = Array.from(table.querySelectorAll("tbody tr")).filter(
        (row) => row.style.display !== "none"
    );
    visibleRows.forEach((row, index) => {
        const firstCell = row.cells[0];
        if (firstCell) {
            firstCell.textContent = index + 1;
        }
    });
}

// Fonction globale pour réinitialiser les filtres
function resetFilters() {
    const baseUrl = window.location.pathname;
    window.location.href = baseUrl;
}

// Gestion des messages de succès/erreur
document.addEventListener("DOMContentLoaded", function () {
    // Auto-masquer les alertes après 5 secondes
    const alerts = document.querySelectorAll(".alert");
    alerts.forEach((alert) => {
        if (!alert.classList.contains("alert-danger")) {
            setTimeout(() => {
                alert.style.transition = "opacity 0.5s";
                alert.style.opacity = "0";
                setTimeout(() => {
                    alert.remove();
                }, 500);
            }, 5000);
        }
    });
});
