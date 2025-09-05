document.addEventListener("DOMContentLoaded", function () {
    const deleteModal = document.getElementById("deleteModal");
    const deleteForm = document.getElementById("deleteForm");
    const deleteMessage = document.getElementById("deleteMessage");
    const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");

    if (deleteModal) {
        /**
         * Cas 1 : Suppression normale (programme actif)
         */
        document.addEventListener("click", function (e) {
            if (e.target.matches(".delete-btn, .delete-btn *")) {
                e.preventDefault();

                const deleteBtn = e.target.closest(".delete-btn");
                const deleteUrl = deleteBtn.getAttribute("data-url");
                const customMessage = deleteBtn.getAttribute("data-message");

                // Message
                deleteMessage.innerHTML = customMessage || "Êtes-vous sûr de vouloir supprimer cet élément ?";
                deleteForm.action = deleteUrl;

                // Bouton supprimer visible
                confirmDeleteBtn.style.display = "inline-block";

                // Afficher le modal
                const modal = new bootstrap.Modal(deleteModal);
                modal.show();
            }
        });

        /**
         * Cas 2 : Programme verrouillé (edit ou delete)
         */
        document.addEventListener("click", function (e) {
            if (e.target.matches(".locked-program, .locked-program *")) {
                e.preventDefault();

                const lockedBtn = e.target.closest(".locked-program");
                const message = lockedBtn.getAttribute("data-message");

                // Message d’alerte
                deleteMessage.innerHTML = message || "Ce programme est verrouillé et ne peut pas être modifié/supprimé.";

                // On cache le bouton supprimer
                confirmDeleteBtn.style.display = "none";

                // Afficher le modal
                const modal = new bootstrap.Modal(deleteModal);
                modal.show();
            }
        });

        /**
         * Soumission de suppression AJAX
         */
        deleteForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const originalText = confirmDeleteBtn.innerHTML;
            confirmDeleteBtn.disabled = true;
            confirmDeleteBtn.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                Suppression...
            `;

            const formData = new FormData(this);

            fetch(this.action, {
                method: "POST",
                body: formData,
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
                },
            })
                .then((response) => {
                    if (response.ok) {
                        const modal = bootstrap.Modal.getInstance(deleteModal);
                        modal.hide();

                        if (typeof Swal !== "undefined") {
                            Swal.fire({
                                title: "Supprimé !",
                                text: "L'élément a été supprimé avec succès.",
                                icon: "success",
                                timer: 2000,
                            });
                        }

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
                        alert("Une erreur est survenue lors de la suppression.");
                    }
                })
                .finally(() => {
                    confirmDeleteBtn.disabled = false;
                    confirmDeleteBtn.innerHTML = originalText;
                });
        });

        /**
         * Reset du modal
         */
        deleteModal.addEventListener("hidden.bs.modal", function () {
            deleteMessage.textContent = "Êtes-vous sûr de vouloir supprimer cet élément ?";
            deleteForm.action = "";
            confirmDeleteBtn.disabled = false;
            confirmDeleteBtn.innerHTML = '<i class="bi bi-trash"></i> Supprimer';
            confirmDeleteBtn.style.display = "inline-block"; // On le réaffiche par défaut
        });
    }
});
