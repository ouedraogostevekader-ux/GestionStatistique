document.addEventListener("DOMContentLoaded", function () {
      
    const lockModal = new bootstrap.Modal(document.getElementById("lockModal"));
    const lockModalMessage = document.getElementById("lockModalMessage");
    const confirmLockBtn = document.getElementById("confirmLockBtn");
    let currentForm = null;

    // Boutons toggle
    document.querySelectorAll(".toggle-program-btn").forEach(button => {
        button.addEventListener("click", function () {
            const programmeId = this.dataset.id;
            const programmeLibelle = this.dataset.libelle;
            const isActive = this.dataset.active === "1" || this.dataset.active === true;

            lockModalMessage.innerHTML = isActive
                ? `Voulez-vous vraiment verrouiller le programme <strong>${programmeLibelle}</strong> ?`
                : `Voulez-vous vraiment déverrouiller le programme <strong>${programmeLibelle}</strong> ?`;

            currentForm = document.getElementById(`toggle-form-${programmeId}`);
            lockModal.show();
        });
    });

    confirmLockBtn.addEventListener("click", function () {
        if (currentForm) {
            currentForm.submit();
        }
    });

    // Gestion Edit/Delete sur programmes verrouillés
    document.querySelectorAll(".locked-program").forEach(button => {
        button.addEventListener("click", function (e) {
            e.preventDefault();
            const message = this.dataset.message || "Action impossible : le programme est verrouillé.";
            // Affiche un toast ou alert
            if (typeof Swal !== "undefined") {
                Swal.fire({
                    title: "Attention",
                    html: message,
                    icon: "warning",
                    timer: 2500,
                    showConfirmButton: false
                });
            } else {
                alert(message);
            }
        });
    });
});
