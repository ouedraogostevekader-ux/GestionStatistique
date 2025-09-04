/**
 * Script JavaScript pour la page d'importation CSV
 */

document.addEventListener("DOMContentLoaded", function () {
    initializeImportFunctionality();
});

/**
 * Initialise toute la fonctionnalité d'importation
 */
function initializeImportFunctionality() {
    // Gestionnaires d'événements
    setupFileInputHandlers();
    setupFormSubmissionHandlers();
    setupUIHandlers();

    // Initialiser les tooltips Bootstrap si disponibles
    if (typeof bootstrap !== "undefined") {
        var tooltipTriggerList = [].slice.call(
            document.querySelectorAll('[data-bs-toggle="tooltip"]')
        );
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
}

/**
 * Configure les gestionnaires pour les inputs de fichiers
 */
function setupFileInputHandlers() {
    const fileInputs = document.querySelectorAll(".csv-file");

    fileInputs.forEach((input) => {
        input.addEventListener("change", function (e) {
            const file = e.target.files[0];
            const formId = this.closest("form").id;
            const submitButton = this.closest("form").querySelector(
                'button[type="submit"]'
            );
            const previewDiv = document.getElementById(
                formId.replace("Form", "_preview")
            );

            if (file) {
                // Valider le fichier
                if (validateFile(file)) {
                    // Activer le bouton de soumission
                    submitButton.disabled = false;
                    submitButton.classList.remove("btn-secondary");

                    // Ajouter la classe de couleur appropriée
                    const cardType = this.closest(".import-card").dataset.type;
                    const colorClass = getButtonColorClass(cardType);
                    submitButton.classList.add(colorClass);

                    // Prévisualiser le fichier
                    previewFile(file, previewDiv, cardType);
                } else {
                    // Désactiver le bouton et masquer la prévisualisation
                    submitButton.disabled = true;
                    previewDiv.style.display = "none";
                }
            } else {
                submitButton.disabled = true;
                previewDiv.style.display = "none";
            }
        });
    });
}

/**
 * Valide un fichier CSV
 */
function validateFile(file) {
    // Vérifier la taille (10MB max)
    const maxSize = 10 * 1024 * 1024; // 10MB en bytes
    if (file.size > maxSize) {
        showAlert(
            "Erreur",
            "Le fichier est trop volumineux. Taille maximum: 10MB",
            "danger"
        );
        return false;
    }

    // Vérifier l'extension
    const allowedExtensions = [".csv", ".txt"];
    const fileName = file.name.toLowerCase();
    const hasValidExtension = allowedExtensions.some((ext) =>
        fileName.endsWith(ext)
    );

    if (!hasValidExtension) {
        showAlert(
            "Erreur",
            "Format de fichier non supporté. Utilisez .csv ou .txt",
            "danger"
        );
        return false;
    }

    return true;
}

/**
 * Prévisualise un fichier CSV
 */
function previewFile(file, previewDiv, type) {
    const reader = new FileReader();

    reader.onload = function (e) {
        const csvContent = e.target.result;
        const lines = csvContent
            .split("\n")
            .filter((line) => line.trim() !== "");

        if (lines.length === 0) {
            previewDiv.innerHTML =
                '<div class="alert alert-warning">Fichier vide</div>';
            previewDiv.style.display = "block";
            return;
        }

        // Traiter les 6 premières lignes pour la prévisualisation
        const previewLines = lines.slice(0, 6);
        const headers = parseCSVLine(previewLines[0]);

        let previewHTML = '<div class="table-responsive">';
        previewHTML += '<table class="table table-sm table-bordered">';
        previewHTML += '<thead class="table-light"><tr>';

        headers.forEach((header) => {
            previewHTML += `<th>${escapeHtml(header.trim())}</th>`;
        });

        previewHTML += "</tr></thead><tbody>";

        // Ajouter les lignes de données (maximum 5)
        for (let i = 1; i < Math.min(previewLines.length, 6); i++) {
            const cells = parseCSVLine(previewLines[i]);
            previewHTML += "<tr>";

            headers.forEach((header, index) => {
                const cellValue = cells[index] || "";
                previewHTML += `<td>${escapeHtml(cellValue.trim())}</td>`;
            });

            previewHTML += "</tr>";
        }

        previewHTML += "</tbody></table>";

        if (lines.length > 6) {
            previewHTML += `<div class="text-muted text-center"><small>... et ${
                lines.length - 6
            } autres lignes</small></div>`;
        }

        previewHTML += "</div>";

        // Ajouter les statistiques du fichier
        previewHTML += `<div class="row mt-2">
            <div class="col-6"><small><strong>Lignes totales:</strong> ${
                lines.length - 1
            }</small></div>
            <div class="col-6"><small><strong>Colonnes:</strong> ${
                headers.length
            }</small></div>
        </div>`;

        previewDiv.innerHTML = previewHTML;
        previewDiv.style.display = "block";

        // Valider les en-têtes
        validateHeaders(headers, type, previewDiv);
    };

    reader.readAsText(file);
}

/**
 * Parse une ligne CSV (gestion basique des guillemets)
 */
function parseCSVLine(line) {
    const result = [];
    let current = "";
    let inQuotes = false;

    for (let i = 0; i < line.length; i++) {
        const char = line[i];

        if (char === '"') {
            inQuotes = !inQuotes;
        } else if (char === "," && !inQuotes) {
            result.push(current);
            current = "";
        } else {
            current += char;
        }
    }

    result.push(current);
    return result.map((cell) => cell.replace(/^"(.*)"$/, "$1")); // Enlever les guillemets externes
}

/**
 * Valide les en-têtes selon le type d'importation
 */
function validateHeaders(headers, type, previewDiv) {
    const requiredHeaders = {
        structures: ["nom", "sigle", "type_structure"],
        "objectifs-strategiques": ["libelle", "programme_budgetaire"],
        "objectifs-specifiques": ["libelle", "action"],
    };

    const required = requiredHeaders[type] || [];
    const missing = required.filter((header) => !headers.includes(header));

    if (missing.length > 0) {
        const alertHTML = `<div class="alert alert-danger mt-2">
            <i class="bi bi-exclamation-triangle"></i>
            <strong>En-têtes manquants:</strong> ${missing.join(", ")}
        </div>`;
        previewDiv.innerHTML += alertHTML;

        // Désactiver le bouton de soumission
        const form = previewDiv.closest("form");
        const submitButton = form.querySelector('button[type="submit"]');
        submitButton.disabled = true;
    } else {
        const successHTML = `<div class="alert alert-success mt-2">
            <i class="bi bi-check-circle"></i>
            <strong>En-têtes valides!</strong> Prêt pour l'importation.
        </div>`;
        previewDiv.innerHTML += successHTML;
    }
}

/**
 * Configure les gestionnaires de soumission de formulaires
 */
function setupFormSubmissionHandlers() {
    const forms = document.querySelectorAll(".import-form");

    forms.forEach((form) => {
        form.addEventListener("submit", function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;

            // Désactiver le bouton et montrer le chargement
            submitButton.disabled = true;
            submitButton.innerHTML =
                '<i class="bi bi-hourglass-split"></i> Importation...';

            // Montrer le modal de progression
            showProgressModal();

            // Simuler la progression (optionnel)
            simulateProgress();

            // Soumettre le formulaire
            fetch(this.action, {
                method: "POST",
                body: formData,
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
            })
                .then((response) => response.text())
                .then((html) => {
                    // Masquer le modal de progression
                    hideProgressModal();

                    // Remplacer le contenu de la page avec la réponse
                    document.open();
                    document.write(html);
                    document.close();
                })
                .catch((error) => {
                    hideProgressModal();
                    console.error("Erreur:", error);

                    // Restaurer le bouton
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;

                    showAlert(
                        "Erreur",
                        "Une erreur est survenue lors de l'importation",
                        "danger"
                    );
                });
        });
    });
}

/**
 * Configure les gestionnaires d'interface utilisateur
 */
function setupUIHandlers() {
    // Bouton d'instructions
    const showInstructionsBtn = document.getElementById("showInstructions");
    const instructionsCard = document.getElementById("instructionsCard");

    if (showInstructionsBtn && instructionsCard) {
        showInstructionsBtn.addEventListener("click", function () {
            if (instructionsCard.style.display === "none") {
                instructionsCard.style.display = "block";
                this.innerHTML =
                    '<i class="bi bi-eye-slash"></i> Masquer les instructions';
            } else {
                instructionsCard.style.display = "none";
                this.innerHTML =
                    '<i class="bi bi-question-circle"></i> Instructions';
            }
        });
    }

    // Bouton de rafraîchissement des statistiques
    const refreshStatsBtn = document.getElementById("refreshStats");
    if (refreshStatsBtn) {
        refreshStatsBtn.addEventListener("click", function () {
            refreshStatistics();
        });
    }
}

/**
 * Affiche le modal de progression
 */
function showProgressModal() {
    const modal = document.getElementById("progressModal");
    if (modal && typeof bootstrap !== "undefined") {
        const bsModal = new bootstrap.Modal(modal, {
            backdrop: "static",
            keyboard: false,
        });
        bsModal.show();
    }
}

/**
 * Masque le modal de progression
 */
function hideProgressModal() {
    const modal = document.getElementById("progressModal");
    if (modal && typeof bootstrap !== "undefined") {
        const bsModal = bootstrap.Modal.getInstance(modal);
        if (bsModal) {
            bsModal.hide();
        }
    }
}

/**
 * Simule la progression d'importation
 */
function simulateProgress() {
    const progressBar = document.getElementById("progressBar");
    const progressText = document.getElementById("progressText");

    if (!progressBar || !progressText) return;

    let progress = 0;
    const messages = [
        "Lecture du fichier CSV...",
        "Validation des données...",
        "Insertion en base de données...",
        "Finalisation...",
    ];

    const interval = setInterval(() => {
        progress += Math.random() * 30;
        if (progress > 95) progress = 95;

        progressBar.style.width = progress + "%";

        const messageIndex = Math.min(
            Math.floor(progress / 25),
            messages.length - 1
        );
        progressText.textContent = messages[messageIndex];

        if (progress >= 95) {
            clearInterval(interval);
        }
    }, 500);
}

/**
 * Rafraîchit les statistiques
 */
function refreshStatistics() {
    // Cette fonction pourrait faire un appel AJAX pour récupérer les stats à jour
    const refreshBtn = document.getElementById("refreshStats");
    if (refreshBtn) {
        refreshBtn.innerHTML = '<i class="bi bi-hourglass-split"></i>';

        setTimeout(() => {
            refreshBtn.innerHTML = '<i class="bi bi-arrow-clockwise"></i>';
            // Ici, on pourrait mettre à jour les compteurs
        }, 1000);
    }
}

/**
 * Retourne la classe de couleur appropriée pour le bouton
 */
function getButtonColorClass(type) {
    const colorMap = {
        structures: "btn-primary",
        "objectifs-strategiques": "btn-success",
        "objectifs-specifiques": "btn-warning",
    };

    return colorMap[type] || "btn-primary";
}

/**
 * Affiche une alerte
 */
function showAlert(title, message, type = "info") {
    const alertDiv = document.createElement("div");
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <i class="bi bi-${getAlertIcon(type)}"></i>
        <strong>${escapeHtml(title)}</strong> ${escapeHtml(message)}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    // Insérer l'alerte au début de la section content
    const contentSection = document.querySelector(".content");
    if (contentSection) {
        contentSection.insertBefore(alertDiv, contentSection.firstChild);

        // Auto-masquer après 5 secondes
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
}

/**
 * Retourne l'icône appropriée pour le type d'alerte
 */
function getAlertIcon(type) {
    const icons = {
        success: "check-circle",
        danger: "exclamation-triangle",
        warning: "exclamation-triangle",
        info: "info-circle",
    };

    return icons[type] || "info-circle";
}

/**
 * Échappe les caractères HTML
 */
function escapeHtml(text) {
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Gestion du drag & drop (bonus)
 */
function setupDragAndDrop() {
    const cards = document.querySelectorAll(".import-card");

    cards.forEach((card) => {
        const fileInput = card.querySelector(".csv-file");

        card.addEventListener("dragover", function (e) {
            e.preventDefault();
            this.classList.add("drag-over");
        });

        card.addEventListener("dragleave", function (e) {
            e.preventDefault();
            this.classList.remove("drag-over");
        });

        card.addEventListener("drop", function (e) {
            e.preventDefault();
            this.classList.remove("drag-over");

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                fileInput.dispatchEvent(new Event("change", { bubbles: true }));
            }
        });
    });
}

// Initialiser le drag & drop au chargement
document.addEventListener("DOMContentLoaded", function () {
    setupDragAndDrop();
});
