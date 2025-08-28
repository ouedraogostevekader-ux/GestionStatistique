// public/js/delete-modal.js
document.addEventListener("DOMContentLoaded", function () {
    const deleteModal = document.getElementById("deleteModal");
    const deleteForm = document.getElementById("deleteForm");
    const deleteMessage = document.getElementById("deleteMessage");
    const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");

    if (deleteModal) {
        // Écouteur pour tous les boutons de suppression
        document.addEventListener("click", function (e) {
            if (e.target.matches(".delete-btn, .delete-btn *")) {
                e.preventDefault();

                // Trouver le bouton parent si on a cliqué sur une icône
                const deleteBtn = e.target.closest(".delete-btn");

                // Récupérer les données
                const deleteUrl = deleteBtn.getAttribute("data-url");
                const itemName =
                    deleteBtn.getAttribute("data-item") || "cet élément";
                const customMessage = deleteBtn.getAttribute("data-message");

                // Configurer le modal
                if (customMessage) {
                    deleteMessage.innerHTML = customMessage;
                } else {
                    deleteMessage.innerHTML = `Êtes-vous sûr de vouloir supprimer "<strong>${itemName}</strong>" ?`;
                }

                deleteForm.action = deleteUrl;

                // Afficher le modal
                const modal = new bootstrap.Modal(deleteModal);
                modal.show();
            }
        });

        // Gérer la soumission du formulaire
        deleteForm.addEventListener("submit", function (e) {
            e.preventDefault();

            // Désactiver le bouton et afficher le loader
            const originalText = confirmDeleteBtn.innerHTML;
            confirmDeleteBtn.disabled = true;
            confirmDeleteBtn.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                Suppression...
            `;

            // Créer et soumettre le formulaire
            const formData = new FormData(this);

            fetch(this.action, {
                method: "POST",
                body: formData,
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": document.querySelector(
                        'input[name="_token"]'
                    ).value,
                },
            })
                .then((response) => {
                    if (response.ok) {
                        // Fermer le modal
                        const modal = bootstrap.Modal.getInstance(deleteModal);
                        modal.hide();

                        // Afficher message de succès
                        if (typeof Swal !== "undefined") {
                            Swal.fire({
                                title: "Supprimé !",
                                text: "L'élément a été supprimé avec succès.",
                                icon: "success",
                                timer: 2000,
                            });
                        }

                        // Recharger la page après un délai
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        throw new Error("Erreur lors de la suppression");
                    }
                })
                .catch((error) => {
                    console.error("Erreur:", error);

                    if (typeof Swal !== "undefined") {
                        Swal.fire({
                            title: "Erreur !",
                            text: "Une erreur est survenue lors de la suppression.",
                            icon: "error",
                        });
                    } else {
                        alert(
                            "Une erreur est survenue lors de la suppression."
                        );
                    }
                })
                .finally(() => {
                    // Réactiver le bouton
                    confirmDeleteBtn.disabled = false;
                    confirmDeleteBtn.innerHTML = originalText;
                });
        });

        // Réinitialiser le modal à la fermeture
        deleteModal.addEventListener("hidden.bs.modal", function () {
            deleteMessage.textContent =
                "Êtes-vous sûr de vouloir supprimer cet élément ?";
            deleteForm.action = "";
            confirmDeleteBtn.disabled = false;
            confirmDeleteBtn.innerHTML =
                '<i class="bi bi-trash"></i> Supprimer';
        });
    }
});
