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
                deleteMessage.innerHTML =
                    customMessage ||
                    "Êtes-vous sûr de vouloir supprimer cet élément ?";
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

document.addEventListener("DOMContentLoaded", function () {
    const activeModal = document.getElementById("activeModal");
    const activeForm = document.getElementById("activeForm");
    const activeMessage = document.getElementById("activeMessage");
    const confirmActiveBtn = document.getElementById("confirmActiveBtn");

    if (activeModal) {
        /**
         * Cas 1 : Suppression normale (programme actif)
         */
        document.addEventListener("click", function (e) {
            if (e.target.matches(".active-btn, .active-btn *")) {
                e.preventDefault();

                const activeBtn = e.target.closest(".active-btn");
                const activeUrl = activeBtn.getAttribute("data-url");
                const customMessage = activeBtn.getAttribute("data-message");

                // Message
                activeMessage.innerHTML =
                    customMessage ||
                    "Êtes-vous sûr de vouloir activé cet élément ?";
                activeForm.action = activeUrl;

                // Bouton supprimer visible
                confirmActiveBtn.style.display = "inline-block";

                // Afficher le modal
                const modal = new bootstrap.Modal(activeModal);
                modal.show();
            }
        });
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const desactiveModal = document.getElementById("desactiveModal");
    const desactiveForm = document.getElementById("desactiveForm");
    const desactiveMessage = document.getElementById("desactiveMessage");
    const confirmDesactiveBtn = document.getElementById("confirmDesactiveBtn");

    if (desactiveModal) {
        /**
         * Cas 1 : Suppression normale (programme actif)
         */
        document.addEventListener("click", function (e) {
            if (e.target.matches(".desactive-btn, .desactive-btn *")) {
                e.preventDefault();

                const desactiveBtn = e.target.closest(".desactive-btn");
                const desactiveUrl = desactiveBtn.getAttribute("data-url");
                const customMessage = desactiveBtn.getAttribute("data-message");

                // Message
                desactiveMessage.innerHTML =
                    customMessage ||
                    "Êtes-vous sûr de vouloir activé cet élément ?";
                desactiveForm.action = desactiveUrl;

                // Bouton supprimer visible
                confirmDesactiveBtn.style.display = "inline-block";

                // Afficher le modal
                const modal = new bootstrap.Modal(desactiveModal);
                modal.show();
            }
        });
    }
});
