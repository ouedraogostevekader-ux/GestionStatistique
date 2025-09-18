// public/js/tableau.js
class TableauManager {
    constructor(tableauId) {
        this.tableauId = tableauId;
        this.apiUrl = '/api/tableaux';
        this.initEventListeners();
    }

    initEventListeners() {
        // Auto-calcul des totaux lors de la saisie
        $(document).on('input', 'input[type="number"]', (e) => {
            this.calculateTotals();
        });

        // Sauvegarde automatique toutes les 30 secondes
        setInterval(() => {
            this.autoSave();
        }, 30000);

        // Validation avant soumission
        $('form').on('submit', (e) => {
            if (!this.validateData()) {
                e.preventDefault();
            }
        });

        // Raccourcis clavier
        $(document).keydown((e) => {
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                this.saveData();
            }
        });
    }

    calculateTotals() {
        const self = this;
        
        // Calculer les totaux par ligne
        $('tbody tr:not(.table-primary)').each(function() {
            let total = 0;
            $(this).find('input[type="number"]').each(function() {
                const value = parseFloat($(this).val()) || 0;
                total += value;
            });
            $(this).find('.total-ligne').text(total.toFixed(2));
        });

        // Calculer les totaux par colonne
        const columnCount = $('thead th:not(:first-child):not(:last-child)').length;
        
        for (let i = 0; i < columnCount; i++) {
            let total = 0;
            $('tbody tr:not(.table-primary)').each(function() {
                const input = $(this).find('input[type="number"]').eq(i);
                const value = parseFloat(input.val()) || 0;
                total += value;
            });
            $('.total-colonne').eq(i).text(total.toFixed(2));
        }

        // Calculer le total général
        let totalGeneral = 0;
        $('.total-colonne').each(function() {
            totalGeneral += parseFloat($(this).text()) || 0;
        });
        $('.total-general').text(totalGeneral.toFixed(2));

        // Appliquer les règles de validation
        this.applyValidationRules();
    }

    applyValidationRules() {
        // Validation des valeurs négatives
        $('input[type="number"]').each(function() {
            const value = parseFloat($(this).val());
            if (value < 0) {
                $(this).addClass('is-invalid');
                $(this).attr('title', 'Les valeurs négatives ne sont pas autorisées');
            } else {
                $(this).removeClass('is-invalid');
                $(this).removeAttr('title');
            }
        });

        // Validation des totaux cohérents
        $('tbody tr:not(.table-primary)').each(function() {
            const inputs = $(this).find('input[type="number"]');
            const totalElement = $(this).find('.total-ligne');
            
            if (inputs.length > 0) {
                let sum = 0;
                inputs.each(function() {
                    sum += parseFloat($(this).val()) || 0;
                });
                
                const displayedTotal = parseFloat(totalElement.text()) || 0;
                if (Math.abs(sum - displayedTotal) > 0.01) {
                    totalElement.addClass('text-danger');
                } else {
                    totalElement.removeClass('text-danger');
                }
            }
        });
    }

    async saveData() {
        const formData = new FormData();
        const structureId = $('#structure_id').val();
        const assignationId = $('#assignation_id').val();
        
        if (!structureId || !assignationId) {
            this.showNotification('Veuillez sélectionner une structure et une assignation', 'warning');
            return;
        }

        const donnees = {};
        $('input[name^="donnees["]').each(function() {
            const name = $(this).attr('name');
            const celluleId = name.match(/\[([^\]]+)\]/)[1];
            const value = $(this).val();
            if (value && value !== '') {
                donnees[celluleId] = value;
            }
        });

        try {
            const response = await fetch(`${this.apiUrl}/${this.tableauId}/save-data`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    structure_id: structureId,
                    assignation_id: assignationId,
                    donnees: donnees
                })
            });

            const result = await response.json();
            
            if (result.success) {
                this.showNotification('Données sauvegardées avec succès', 'success');
                this.markAsSaved();
            } else {
                this.showNotification('Erreur lors de la sauvegarde: ' + result.message, 'error');
            }
        } catch (error) {
            this.showNotification('Erreur de connexion', 'error');
            console.error('Save error:', error);
        }
    }

    async autoSave() {
        if (this.hasUnsavedChanges()) {
            await this.saveData();
        }
    }

    hasUnsavedChanges() {
        return $('input[type="number"]').toArray().some(input => {
            return $(input).data('original-value') !== $(input).val();
        });
    }

    markAsSaved() {
        $('input[type="number"]').each(function() {
            $(this).data('original-value', $(this).val());
        });
    }

    validateData() {
        let isValid = true;
        
        // Vérifier les champs obligatoires
        if (!$('#structure_id').val()) {
            this.showNotification('Veuillez sélectionner une structure', 'warning');
            isValid = false;
        }

        if (!$('#assignation_id').val()) {
            this.showNotification('Veuillez sélectionner une assignation', 'warning');
            isValid = false;
        }

        // Vérifier les valeurs numériques
        $('input[type="number"]').each(function() {
            const value = $(this).val();
            if (value && (isNaN(value) || value < 0)) {
                $(this).addClass('is-invalid');
                isValid = false;
            }
        });

        return isValid;
    }

    showNotification(message, type) {
        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        }[type] || 'alert-info';

        const notification = $(`
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 1050; min-width: 300px;">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);

        $('body').append(notification);
        
        setTimeout(() => {
            notification.fadeOut(() => notification.remove());
        }, 5000);
    }

    async exportToExcel() {
        try {
            const response = await fetch(`${this.apiUrl}/${this.tableauId}/export`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                }
            });

            if (response.ok) {
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `tableau_${this.tableauId}.xlsx`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
            }
        } catch (error) {
            this.showNotification('Erreur lors de l\'export', 'error');
        }
    }

    // Fonctions utilitaires pour la structure
    addRow(parentId = null) {
        const modal = $('#modalAjouterLigne');
        if (parentId) {
            modal.find('#parent_id').val(parentId);
        }
        modal.modal('show');
    }

    addColumn(parentId = null) {
        const modal = $('#modalAjouterColonne');
        if (parentId) {
            modal.find('#parent_id').val(parentId);
        }
        modal.modal('show');
    }

    async deleteRow(rowId) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer cette ligne ?')) {
            return;
        }

        try {
            const response = await fetch(`/admin/lignes/${rowId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                location.reload();
            } else {
                this.showNotification('Erreur lors de la suppression', 'error');
            }
        } catch (error) {
            this.showNotification('Erreur de connexion', 'error');
        }
    }

    async deleteColumn(columnId) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer cette colonne ?')) {
            return;
        }

        try {
            const response = await fetch(`/admin/colonnes/${columnId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                location.reload();
            } else {
                this.showNotification('Erreur lors de la suppression', 'error');
            }
        } catch (error) {
            this.showNotification('Erreur de connexion', 'error');
        }
    }
}

// Classe utilitaire pour la navigation entre cellules
class TableauNavigation {
    constructor() {
        this.initKeyboardNavigation();
    }

    initKeyboardNavigation() {
        $(document).on('keydown', 'input[type="number"]', (e) => {
            const currentInput = $(e.target);
            const allInputs = $('input[type="number"]:visible');
            const currentIndex = allInputs.index(currentInput);

            switch(e.key) {
                case 'Tab':
                    // Comportement normal du Tab
                    break;
                
                case 'Enter':
                    e.preventDefault();
                    this.moveToNext(allInputs, currentIndex, 'down');
                    break;
                
                case 'ArrowUp':
                    e.preventDefault();
                    this.moveToNext(allInputs, currentIndex, 'up');
                    break;
                
                case 'ArrowDown':
                    e.preventDefault();
                    this.moveToNext(allInputs, currentIndex, 'down');
                    break;
                
                case 'ArrowLeft':
                    if (currentInput[0].selectionStart === 0) {
                        e.preventDefault();
                        this.moveToNext(allInputs, currentIndex, 'left');
                    }
                    break;
                
                case 'ArrowRight':
                    if (currentInput[0].selectionStart === currentInput.val().length) {
                        e.preventDefault();
                        this.moveToNext(allInputs, currentIndex, 'right');
                    }
                    break;
            }
        });
    }

    moveToNext(allInputs, currentIndex, direction) {
        let nextIndex;
        const rowCount = $('tbody tr:not(.table-primary)').length;
        const colCount = $('input[type="number"]:visible').length / rowCount;

        switch(direction) {
            case 'up':
                nextIndex = currentIndex - colCount;
                break;
            case 'down':
                nextIndex = currentIndex + colCount;
                break;
            case 'left':
                nextIndex = currentIndex - 1;
                break;
            case 'right':
                nextIndex = currentIndex + 1;
                break;
        }

        if (nextIndex >= 0 && nextIndex < allInputs.length) {
            allInputs.eq(nextIndex).focus().select();
        }
    }
}

// Classe pour l'impression et l'export
class TableauExporter {
    constructor(tableauId) {
        this.tableauId = tableauId;
    }

    printTableau() {
        const printContent = this.generatePrintContent();
        const printWindow = window.open('', '_blank');
        
        printWindow.document.write(`
            <html>
                <head>
                    <title>Impression Tableau</title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
                    <style>
                        @media print {
                            .no-print { display: none !important; }
                            body { font-size: 12px; }
                            table { font-size: 10px; }
                        }
                        .table-bordered th, .table-bordered td {
                            border: 1px solid #000 !important;
                        }
                    </style>
                </head>
                <body>
                    ${printContent}
                </body>
            </html>
        `);
        
        printWindow.document.close();
        printWindow.print();
    }

    generatePrintContent() {
        const tableElement = $('.table-responsive').clone();
        
        // Remplacer les inputs par leur valeur
        tableElement.find('input').each(function() {
            const value = $(this).val() || '-';
            $(this).replaceWith(`<span>${value}</span>`);
        });

        // Ajouter en-tête
        const title = $('h2').first().text();
        const date = new Date().toLocaleDateString('fr-FR');
        
        return `
            <div class="container-fluid">
                <div class="text-center mb-4">
                    <h3>${title}</h3>
                    <p>Imprimé le ${date}</p>
                </div>
                ${tableElement.html()}
            </div>
        `;
    }

    async exportToPDF() {
        // Utilisation de jsPDF ou similaire
        try {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('l', 'mm', 'a4'); // Paysage

            // Ajouter le titre
            doc.setFontSize(16);
            doc.text($('h2').first().text(), 20, 20);

            // Générer le tableau
            doc.autoTable({
                html: '.table',
                startY: 30,
                styles: { fontSize: 8 },
                headStyles: { fillColor: [240, 240, 240] }
            });

            doc.save(`tableau_${this.tableauId}.pdf`);
        } catch (error) {
            console.error('Erreur export PDF:', error);
        }
    }
}

// Initialisation globale
$(document).ready(function() {
    // Initialiser le gestionnaire de tableau si on est sur une page de tableau
    const tableauId = $('body').data('tableau-id');
    if (tableauId) {
        window.tableauManager = new TableauManager(tableauId);
        window.tableauNavigation = new TableauNavigation();
        window.tableauExporter = new TableauExporter(tableauId);
    }

    // Initialiser les tooltips Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Auto-resize des textareas
    $('textarea').on('input', function() {
        this.style.height = 'auto';
        this.style.height = this.scrollHeight + 'px';
    });
});

// Fonctions globales pour les actions des boutons
function supprimerLigne(ligneId) {
    if (window.tableauManager) {
        window.tableauManager.deleteRow(ligneId);
    }
}

function supprimerColonne(colonneId) {
    if (window.tableauManager) {
        window.tableauManager.deleteColumn(colonneId);
    }
}

function exporterExcel() {
    if (window.tableauManager) {
        window.tableauManager.exportToExcel();
    }
}

function imprimerTableau() {
    if (window.tableauExporter) {
        window.tableauExporter.printTableau();
    }
}

function exporterPDF() {
    if (window.tableauExporter) {
        window.tableauExporter.exportToPDF();
    }
}