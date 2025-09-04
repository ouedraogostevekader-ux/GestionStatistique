window.addEventListener("DOMContentLoaded", (event) => {
    // Navbar shrink function
    var navbarShrink = function () {
        const navbarCollapsible = document.body.querySelector("#mainNav");
        if (!navbarCollapsible) {
            return;
        }
        if (window.scrollY === 0) {
            navbarCollapsible.classList.add("navbar-shrink");
        } else {
            navbarCollapsible.classList.add("navbar-shrink");
        }
    };

    // Shrink the navbar
    navbarShrink();

    // Shrink the navbar when page is scrolled
    document.addEventListener("scroll", navbarShrink);

    // Activate Bootstrap scrollspy on the main nav element
    const mainNav = document.body.querySelector("#mainNav");
    if (mainNav) {
        new bootstrap.ScrollSpy(document.body, {
            target: "#mainNav",
            rootMargin: "0px 0px -40%",
        });
    }

    // Collapse responsive navbar when toggler is visible
    const navbarToggler = document.body.querySelector(".navbar-toggler");
    const responsiveNavItems = [].slice.call(
        document.querySelectorAll("#navbarResponsive .nav-link")
    );
    responsiveNavItems.map(function (responsiveNavItem) {
        responsiveNavItem.addEventListener("click", () => {
            if (window.getComputedStyle(navbarToggler).display !== "none") {
                navbarToggler.click();
            }
        });
    });

    // Activate SimpleLightbox plugin for portfolio items
    new SimpleLightbox({
        elements: "#portfolio a.portfolio-box",
    });
});

document.addEventListener("DOMContentLoaded", function () {
    // Récupérer les éléments
    const verifierBtn = document.getElementById("verifierBtn");
    const modal = new bootstrap.Modal(
        document.getElementById("candidatureModal")
    );
    const searchBtn = document.getElementById("searchBtn");
    const candidatureIdInput = document.getElementById("candidatureId");
    const erreur = document.getElementById("erreurId");

    // Ouvrir le modal lors du clic sur le bouton de navigation
    verifierBtn.addEventListener("click", function (e) {
        e.preventDefault();
        modal.show();
    });

    // Gérer la recherche
    searchBtn.addEventListener("click", function () {
        const regex = /^\d{17}$/;
        if (candidatureIdInput.value.trim().match(regex) === null) {
            candidatureIdInput.focus();
            erreur.innerHTML = `<p style="color: red;"><i class="fas fa-info-circle"></i>Le NIP doit être 17 chiffres</p>`;
        } else {
            erreur.innerHTML = `<p style="color: green;"><i class="fas fa-info-circle"></i>Recherche lancée ...</p>`;
            window.location.href =
                "/candidatures/" + candidatureIdInput.value.trim() + "/verife";
        }
    });

    // Permettre la recherche avec la touche Entrée
    candidatureIdInput.addEventListener("keypress", function (e) {
        if (e.key === "Enter") {
            searchBtn.click();
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const alphabetisationSelect = document.querySelector(
        'select[name="alphabetisation"]'
    );
    const niveauInstructionSelect = document.querySelector(
        'select[name="niveau_instruction"]'
    );

    if (alphabetisationSelect && niveauInstructionSelect) {
        // Quand l'utilisateur choisit Non à l'alphabétisation
        alphabetisationSelect.addEventListener("change", function () {
            if (this.value === "0") {
                niveauInstructionSelect.value = "Aucun";
            } else {
                niveauInstructionSelect.value = "";
            }
        });

        // Quand l'utilisateur change le niveau d'instruction
        niveauInstructionSelect.addEventListener("change", function () {
            if (this.value !== "Aucun") {
                alphabetisationSelect.value = "1";
            } else {
                alphabetisationSelect.value = "0";
            }
        });
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const alphabetisationSelect = document.querySelector(
        'select[name="alphabetisation"]'
    );
    const niveauInstructionSelect = document.querySelector(
        'select[name="niveau_instruction"]'
    );
    const optionAucun = document.getElementById("optionAucun");
    const autresOptions = document.querySelectorAll(".autre-Aucun");

    function updateInstructionVisibility() {
        const value = alphabetisationSelect.value;

        if (value === "1") {
            // Oui : cacher "Aucun", montrer les autres
            optionAucun.style.display = "none";
            autresOptions.forEach((option) => (option.style.display = ""));
            if (niveauInstructionSelect.value === "Aucun") {
                niveauInstructionSelect.value = "";
            }
        } else if (value === "0") {
            // Non : montrer "Aucun", cacher les autres
            optionAucun.style.display = "";
            autresOptions.forEach((option) => (option.style.display = "none"));
            if (
                ![...niveauInstructionSelect.options].some(
                    (opt) => opt.value === "Aucun" && opt.selected
                )
            ) {
                niveauInstructionSelect.value = "Aucun";
            }
        } else {
            // Choix vide : tout visible
            optionAucun.style.display = "";
            autresOptions.forEach((option) => (option.style.display = ""));
        }
    }

    // Initialisation au chargement
    updateInstructionVisibility();

    // Événement changement
    alphabetisationSelect.addEventListener(
        "change",
        updateInstructionVisibility
    );
});

document.addEventListener("DOMContentLoaded", function () {
    const situationSelect = document.querySelector(
        'select[name="situation_sociale_id"]'
    );
    const numCarteContainer = document.getElementById("numCarteContainer");
    const numCarteInput = document.getElementById("num_carte");

    function toggleNumCarteField() {
        const selectedOption =
            situationSelect.options[situationSelect.selectedIndex];
        const isPDI = selectedOption.dataset.type === "pdi";

        if (isPDI) {
            numCarteContainer.style.display = "block";
            numCarteInput.required = true;
        } else {
            numCarteContainer.style.display = "none";
            numCarteInput.value = ""; // reset si pas PDI
        }
    }

    situationSelect.addEventListener("change", toggleNumCarteField);
    toggleNumCarteField(); // exécute au chargement
});

// const input1 = document.querySelector("#telephone1");
// const input2 = document.querySelector("#telephone2");

// const iti1 = window.intlTelInput(input1, {
//     initialCountry: "bf",
//     onlyCountries: ["bf"],
//     nationalMode: false, // 👉 Montre +226 dans le champ
//     utilsScript:
//         "https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/utils.js",
// });

// const iti2 = window.intlTelInput(input2, {
//     initialCountry: "bf",
//     onlyCountries: ["bf"],
//     nationalMode: false,
//     utilsScript:
//         "https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/utils.js",
// });

// // Optionnel : mettre à jour les champs au format international avant soumission
// document.querySelector("form").addEventListener("submit", function () {
//     input1.value = iti1.getNumber(); // enregistre en +226...
//     input2.value = iti2.getNumber();
// });

$("#myTable").DataTable({
    responsive: true,
    language: {
        decimal: "",
        emptyTable: "Aucune donnée disponible dans le tableau",
        info: "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
        infoEmpty: "Affichage de 0 à 0 sur 0 entrées",
        infoFiltered: "(filtré à partir de _MAX_ entrées au total)",
        lengthMenu: "Afficher _MENU_ entrées",
        loadingRecords: "Chargement...",
        processing: "Traitement...",
        search: "Rechercher :",
        zeroRecords: "Aucun enregistrement correspondant trouvé",
        paginate: {
            first: "Premier",
            last: "Dernier",
            next: "Suivant",
            previous: "Précédent",
        },
    },
});
