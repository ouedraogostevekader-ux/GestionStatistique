

// Animation au chargement
document.addEventListener('DOMContentLoaded', function() {
    // Animation des cartes statistiques
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Interaction avec les boutons d'expansion
    const expandBtns = document.querySelectorAll('.expand-btn');
    expandBtns.forEach(btn => {
        const target = btn.getAttribute('data-bs-target');
        const collapse = document.querySelector(target);
        
        collapse.addEventListener('show.bs.collapse', function() {
            // collapse.collapse('toggle');
            btn.innerHTML = '<i class="bi bi-eye-slash-fill me-1"></i>';
        });
        
        collapse.addEventListener('hide.bs.collapse', function() {
            btn.innerHTML = '<i class="bi bi-eye-fill me-1"></i>';
        });
    });

    // Effet de survol sur les lignes du tableau
    const tableRows = document.querySelectorAll('tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.boxShadow = '0 4px 15px rgba(102, 126, 234, 0.15)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.boxShadow = 'none';
        });
    });


    
});


document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("suiviModal");
    const modalContent = modal.querySelector(".modal-content");

    // Fonction pour sauvegarder la valeur au chargement de la page
    function restaurerValeur() {
        const valeurObjStrat = localStorage.getItem('valeurObjStrat');
        if (valeurObjStrat !== null) {
            document.getElementById('filterObjStrat').value = valeurStockee;
        }
        const valeurAction = localStorage.getItem('valeurAction');
        if (valeurAction !== null) {
            document.getElementById('filterAction').value = valeurAction;
        }
        const valeurObjectif = localStorage.getItem('valeurObjectif');
        if (valeurObjectif !== null) {
            document.getElementById('filterObjectif').value = valeurObjectif;
        }
        const valeurActivite = localStorage.getItem('valeurActivite');
        if (valeurActivite !== null) {
            document.getElementById('filterActivite').value = valeurActivite;
        }
    }

    function sauvegarderValeur() {
        const valeurObjStrat = document.getElementById('filterObjStrat').value;
        if(valeurObjStrat !== null && valeurObjStrat !== undefined && valeurObjStrat !== ''){
            localStorage.setItem('valeurObjStrat', valeurObjStrat);
        }else{
            localStorage.setItem('valeurObjStrat', null);
        } 
        const valeurAction = document.getElementById('filterAction').value;
        if(valeurAction !== null && valeurAction !== undefined && valeurAction !== ''){
            localStorage.setItem('valeurAction', valeurAction);
        }else{
            localStorage.setItem('valeurAction', null);
        }
        const valeurObjectif = document.getElementById('filterObjectif').value;
        if(valeurObjectif !== null && valeurObjectif !== undefined && valeurObjectif !== ''){
            localStorage.setItem('valeurObjectif', valeurObjectif);
        }else{
            localStorage.setItem('valeurObjectif', null);
        }
        const valeurActivite = document.getElementById('filterActivite').value;
        if(valeurActivite !== null && valeurActivite !== undefined && valeurActivite !== ''){
            localStorage.setItem('valeurActivite', valeurActivite);
        }else{
            localStorage.setItem('valeurActivite', null);
        }
    }

    modal.addEventListener("show.bs.modal", async function (event) {
        const button = event.relatedTarget;
        const url = button.getAttribute("data-url");

        // Afficher un loader
        modalContent.innerHTML = `
                <div class="modal-header">
                    <h5 class="modal-title">Chargement...</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement en cours...</span>
                    </div>
                    <p class="mt-2">Chargement du formulaire...</p>
                </div>
            `;

        try {
            // Récupérer le contenu via AJAX
            const response = await fetch(url, {
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                },
            });

            if (!response.ok) {
                throw new Error(
                    `Erreur ${response.status}: ${response.statusText}`
                );
            }

            const html = await response.text();

            // Injecter le contenu dans le modal
            modalContent.innerHTML = html;

            // Gérer la soumission du formulaire via AJAX
            const form = modalContent.querySelector("form");
            if (form) {
                form.addEventListener("submit", async function (e) {
                    e.preventDefault();

                    // Afficher un loader pendant la soumission
                    const submitButton = form.querySelector(
                        'button[type="submit"]'
                    );
                    const originalButtonText = submitButton.innerHTML;
                    submitButton.innerHTML = `
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Enregistrement...
                        `;
                    submitButton.disabled = true;

                    try {
                        const formData = new FormData(form);
                        const response = await fetch(form.action, {
                            method: form.method,
                            body: formData,
                            headers: {
                                "X-Requested-With": "XMLHttpRequest",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            },
                        });

                        const result = await response.json();

                        if (response.ok) {
                            // Fermer le modal et recharger la page
                            const modalInstance =
                                bootstrap.Modal.getInstance(modal);
                            modalInstance.hide();

                            // Afficher un message de succès
                            Swal.fire({
                                title: "Opération réussie !",
                                text: "Evaluation enregistrée avec succès",
                                icon: "success",
                            });

                            // Recharger la page après un délai
                            setTimeout(() => {
                                sauvegarderValeur();
                                // Recharger la page 
                                window.location.reload();
                                // ramener les infos des inputs
                                restaurerValeur();

                                
                            }, 1500);
                        } else {
                            // Afficher les erreurs de validation
                            let errorHtml =
                                '<div class="alert alert-danger mt-3"><ul>';
                            console.log(result);
                            if (result.message !== undefined) {
                                errorHtml += `<li>${result.message}</li>`;
                            }
                            for (const field in result.errors) {
                                result.errors[field].forEach((error) => {
                                    errorHtml += `<li>${error}</li>`;
                                });
                            }
                            errorHtml += "</ul></div>";

                            // Insérer les erreurs au-dessus du formulaire
                            form.insertAdjacentHTML("afterbegin", errorHtml);

                            // Réactiver le bouton
                            submitButton.innerHTML = originalButtonText;
                            submitButton.disabled = false;
                        }
                    } catch (error) {
                        //console.error('Erreur:', error);
                        Swal.fire({
                            icon: "error",
                            title: "Oups...",
                            text: "Une erreur est survenue lors de la soumission du formulaire.",
                        });
                        submitButton.innerHTML = originalButtonText;
                        submitButton.disabled = false;
                    }
                });
            }
        } catch (error) {
            //console.error('Erreur de chargement:', error);
            modalContent.innerHTML = `
                    <div class="modal-header">
                        <h5 class="modal-title">Erreur</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <p><strong>Impossible de charger le contenu</strong></p>
                            <p class="mb-0">${error.message}</p>
                        </div>
                        <div class="text-center">
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        </div>
                    </div>
                `;
        }
    });

    // Réinitialiser le modal quand il se ferme
    modal.addEventListener("hidden.bs.modal", function () {
        modalContent.innerHTML = "";
    });
});




