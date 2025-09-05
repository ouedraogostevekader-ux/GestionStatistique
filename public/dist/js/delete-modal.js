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
    }
});
