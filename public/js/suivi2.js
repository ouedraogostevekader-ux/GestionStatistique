document.addEventListener('DOMContentLoaded', function() {
    // Éléments du DOM
    const filterObjStrat = document.getElementById('filterObjStrat');
    const filterAction = document.getElementById('filterAction');
    const filterObjectif = document.getElementById('filterObjectif');
    const filterActivite = document.getElementById('filterActivite');
    const resetFiltersBtn = document.getElementById('resetFilters');
    const indicateurRows = document.querySelectorAll('.indicateur-row');
    const indicateurDetails = document.querySelectorAll('.indicateur-details');
    const indicateurCount = document.getElementById('indicateurCount');
    const noIndicatorsMessage = document.getElementById('noIndicatorsMessage');

    // Fonction pour mettre à jour les options des selects enfants
    function updateChildOptions(parentSelect, childSelect, dataAttribute) {
        const parentValue = parentSelect.value;
        const childOptions = childSelect.querySelectorAll('option[data-parent]');
        
        // Réinitialiser le select enfant
        childSelect.value = '';
        
        // Cacher toutes les options avec data-parent
        childOptions.forEach(option => {
            option.style.display = 'none';
        });
        
        if (parentValue) {
            // Montrer seulement les options correspondant au parent
            childOptions.forEach(option => {
                if (option.getAttribute('data-parent') === parentValue) {
                    option.style.display = 'block';
                }
            });
            childSelect.disabled = false;
        } else {
            // Si aucun parent sélectionné, montrer toutes les options
            childOptions.forEach(option => {
                option.style.display = 'block';
            });
            childSelect.disabled = true;
        }
    }

    // Fonction pour filtrer les indicateurs
    function filterIndicateurs() {
        const objStratValue = filterObjStrat.value;
        const actionValue = filterAction.value;
        const objSpecValue = filterObjectif.value;
        const activiteValue = filterActivite.value;
        
        let visibleCount = 0;
        
        indicateurRows.forEach((row, index) => {
            const detailRow = indicateurDetails[index];
            let showRow = true;
            
            // Filtre par objectif stratégique
            if (objStratValue && !actionValue && row.dataset.objStratId !== objStratValue) {
                showRow = false;
            }
            
            // Filtre par action
            if (actionValue && !objSpecValue && row.dataset.actionId !== actionValue) {
                showRow = false;
            }
            
            // Filtre par objectif spécifique
            if (objSpecValue && !activiteValue && row.dataset.objSpecId !== objSpecValue) {
                showRow = false;
            }
            //console.log(""+objSpecValue)
            //console.log(row.dataset.objSpecId);
            
            // Filtre par activité
            if (activiteValue && row.dataset.activiteId !== activiteValue) {
                showRow = false;
            }
            
            // Afficher/cacher la ligne
            if (showRow) {
                row.style.display = '';
                if (detailRow) detailRow.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
                if (detailRow) detailRow.style.display = 'none';
                // Fermer le collapse s'il est ouvert
                const collapseElement = detailRow.querySelector('.collapse');
                if (collapseElement && collapseElement.classList.contains('show')) {
                    const bsCollapse = new bootstrap.Collapse(collapseElement, {
                        toggle: false
                    });
                    bsCollapse.hide();
                }
            }
        });
        
        // Mettre à jour le compteur
        indicateurCount.textContent = visibleCount;
        
        // Afficher/cacher le message "aucun indicateur"
        // if (visibleCount === 0) {
        //     noIndicatorsMessage.style.display = 'block';
        // } else {
        //     noIndicatorsMessage.style.display = 'none';
        // }
    }

    // Fonction pour réinitialiser les filtres
    function resetFilters() {
        filterObjStrat.value = '';
        filterAction.value = '';
        filterObjectif.value = '';
        filterActivite.value = '';
        
        filterAction.disabled = true;
        filterObjectif.disabled = true;
        filterActivite.disabled = true;
        
        // Réafficher toutes les options
        filterAction.querySelectorAll('option[data-parent]').forEach(option => {
            option.style.display = 'block';
        });
        filterObjectif.querySelectorAll('option[data-parent]').forEach(option => {
            option.style.display = 'block';
        });
        filterActivite.querySelectorAll('option[data-parent]').forEach(option => {
            option.style.display = 'block';
        });
        
        filterIndicateurs();
    }

    // Event listeners pour la hiérarchie : Objectif Stratégique → Action → Objectif Spécifique → Activité
    filterObjStrat.addEventListener('change', function() {
        updateChildOptions(this, filterAction, 'data-parent');
        // Réinitialiser les filtres suivants
        filterObjectif.value = '';
        filterActivite.value = '';
        filterObjectif.disabled = true;
        filterActivite.disabled = true;
        filterObjectif.querySelectorAll('option[data-parent]').forEach(option => {
            option.style.display = 'block';
        });
        filterActivite.querySelectorAll('option[data-parent]').forEach(option => {
            option.style.display = 'block';
        });
        filterIndicateurs();
    });

    filterAction.addEventListener('change', function() {
        updateChildOptions(this, filterObjectif, 'data-parent');
        // Réinitialiser le filtre activité
        filterActivite.value = '';
        filterActivite.disabled = true;
        filterActivite.querySelectorAll('option[data-parent]').forEach(option => {
            option.style.display = 'block';
        });
        filterIndicateurs();
    });

    filterObjectif.addEventListener('change', function() {
        updateChildOptions(this, filterActivite, 'data-parent');
        filterIndicateurs();
    });

    filterActivite.addEventListener('change', function() {
        filterIndicateurs();
    });

    resetFiltersBtn.addEventListener('click', resetFilters);

    // Initialisation
    filterIndicateurs();
});