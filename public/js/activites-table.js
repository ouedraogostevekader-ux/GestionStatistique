document.addEventListener("DOMContentLoaded", function () {
    console.log("JavaScript activités chargé");

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

    // Auto-submit des filtres principaux (NOUVEAU: inclut structure_id)
    const autoSubmitFilters = [
        "annee",
        "trimestre",
        "programme_budgetaire_id",
        "structure_id",
    ];
    autoSubmitFilters.forEach((filterId) => {
        const filterElement = document.getElementById(filterId);
        if (filterElement) {
            filterElement.addEventListener("change", function () {
                console.log(
                    "Changement de filtre détecté:",
                    filterId,
                    this.value
                );
                // Petit délai pour améliorer l'UX
                setTimeout(() => {
                    document.getElementById("filtersForm").submit();
                }, 100);
            });
        }
    });

    // Confirmation de suppression améliorée
    document.querySelectorAll('form[action*="destroy"]').forEach((form) => {
        form.addEventListener("submit", function (e) {
            e.preventDefault();

            const activiteName = this.closest("tr").querySelector(
                "td:nth-child(2) .fw-semibold"
            );
            const name = activiteName
                ? activiteName.textContent.trim()
                : "cette activité";
            const confirmMessage = `Êtes-vous sûr de vouloir supprimer l'activité :\n"${name.substring(
                0,
                100
            )}..." ?`;

            if (confirm(confirmMessage)) {
                this.submit();
            }
        });

        // Supprimer l'ancien onsubmit
        form.removeAttribute("onsubmit");
    });

    // Initialiser les tooltips Bootstrap si disponible
    if (typeof bootstrap !== "undefined" && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(
            document.querySelectorAll("[title]")
        );
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Vérifier si les liens de pagination existent et fonctionnent
    const paginationLinks = document.querySelectorAll(".pagination a");
    console.log("Liens de pagination trouvés:", paginationLinks.length);

    paginationLinks.forEach((link, index) => {
        link.addEventListener("click", function (e) {
            console.log(`Clic sur pagination ${index}:`, this.href);
            // Laisser le comportement par défaut se dérouler
        });
    });

    // Vérifier les erreurs JavaScript qui pourraient bloquer la pagination
    window.addEventListener("error", function (e) {
        console.error(
            "Erreur JavaScript détectée:",
            e.message,
            e.filename,
            e.lineno
        );
    });

    // Ajouter un tri simple aux colonnes du tableau
    addSimpleTableSorting();
});

// Fonction pour ajouter un tri simple aux colonnes
function addSimpleTableSorting() {
    const table = document.getElementById("activitesTable");
    if (!table) return;

    const headers = table.querySelectorAll("th");
    headers.forEach((header, index) => {
        // Exclure la colonne Actions (dernière colonne)
        if (index === headers.length - 1) return;

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

        // Tri numérique pour les colonnes de coût et année
        if (columnIndex === 2 || columnIndex === 4) {
            // Année ou Coût
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

// Mettre à jour les numéros de ligne après tri
function updateRowNumbers() {
    const table = document.getElementById("activitesTable");
    if (!table) return;

    const rows = table.querySelectorAll("tbody tr");
    rows.forEach((row, index) => {
        const firstCell = row.cells[0];
        if (firstCell) {
            firstCell.textContent = index + 1;
        }
    });
}

// Fonction globale pour réinitialiser les filtres
function resetFilters() {
    console.log("Reset des filtres");
    window.location.href = window.location.pathname;
}

// Gestion des messages d'alerte
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
