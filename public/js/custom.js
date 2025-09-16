// JavaScript pour gérer la sidebar responsive
document.addEventListener("DOMContentLoaded", function () {
    // Bouton de toggle pour mobile (si vous en avez un)
    const sidebarToggle = document.querySelector("[data-sidebar-toggle]");
    const sidebar = document.querySelector(".sidebar-wrapper");

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener("click", function () {
            sidebar.classList.toggle("show");
        });
    }

    // Fermer la sidebar en cliquant en dehors sur mobile
    document.addEventListener("click", function (e) {
        if (window.innerWidth <= 768) {
            if (
                !sidebar.contains(e.target) &&
                !sidebarToggle.contains(e.target)
            ) {
                sidebar.classList.remove("show");
            }
        }
    });

    // Gestion du redimensionnement de la fenêtre
    window.addEventListener("resize", function () {
        if (window.innerWidth > 768) {
            sidebar.classList.remove("show");
        }
    });
});
