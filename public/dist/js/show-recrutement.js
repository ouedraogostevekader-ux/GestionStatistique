$(document).ready(function () {
    let candidatsTable, brigadiersTable, listeAttenteTable, remplacementsTable;

    // Configuration DataTable pour candidats
    function initCandidatsTable() {
        candidatsTable = $("#candidats-table").DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [
                [5, 10, 25, 50, -1],
                [5, 10, 25, 50, "Tout"],
            ],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json",
            },
        });
    }

    // Configuration DataTable pour brigadiers
    function initBrigadiersTable() {
        brigadiersTable = $("#brigadiers-table").DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [
                [5, 10, 25, 50, -1],
                [5, 10, 25, 50, "Tout"],
            ],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json",
            },
        });
    }

    // Configuration DataTable pour liste d'attente
    function initListeAttenteTable() {
        listeAttenteTable = $("#liste-attente-table").DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [
                [5, 10, 25, 50, -1],
                [5, 10, 25, 50, "Tout"],
            ],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json",
            },
        });
    }

    // Configuration DataTable pour remplacements
    function initRemplacementsTable() {
        remplacementsTable = $("#remplacements-table").DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [
                [5, 10, 25, 50, -1],
                [5, 10, 25, 50, "Tout"],
            ],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json",
            },
        });
    }

    // Fonctions d'affichage des listes
    function showCandidats() {
        const container = $("#list-container");
        const template = $("#candidats-template").html();

        container.html(template).hide().fadeIn();
        initCandidatsTable();

        $("#btn-candidats")
            .html('<i class="bi bi-eye-slash"></i> Masquer')
            .removeClass("btn-primary")
            .addClass("btn-outline-primary");
        $("#btn-brigadiers")
            .html('<i class="bi bi-eye"></i> Voir')
            .removeClass("btn-outline-success")
            .addClass("btn-success");
        $("#btn-liste-attente")
            .html('<i class="bi bi-eye"></i> Voir')
            .removeClass("btn-outline-warning")
            .addClass("btn-warning");
        $("#btn-remplacements")
            .html('<i class="bi bi-eye"></i> Voir')
            .removeClass("btn-outline-secondary")
            .addClass("btn-secondary");
    }

    function showBrigadiers() {
        const container = $("#list-container");
        const template = $("#brigadiers-template").html();

        container.html(template).hide().fadeIn();
        initBrigadiersTable();

        $("#btn-brigadiers")
            .html('<i class="bi bi-eye-slash"></i> Masquer')
            .removeClass("btn-success")
            .addClass("btn-outline-success");
        $("#btn-candidats")
            .html('<i class="bi bi-eye"></i> Voir')
            .removeClass("btn-outline-primary")
            .addClass("btn-primary");
        $("#btn-liste-attente")
            .html('<i class="bi bi-eye"></i> Voir')
            .removeClass("btn-outline-warning")
            .addClass("btn-warning");
        $("#btn-remplacements")
            .html('<i class="bi bi-eye"></i> Voir')
            .removeClass("btn-outline-secondary")
            .addClass("btn-secondary");
    }

    function showListeAttente() {
        const container = $("#list-container");
        const template = $("#liste-attente-template").html();

        container.html(template).hide().fadeIn();
        initListeAttenteTable();

        $("#btn-liste-attente")
            .html('<i class="bi bi-eye-slash"></i> Masquer')
            .removeClass("btn-warning")
            .addClass("btn-outline-warning");
        $("#btn-candidats")
            .html('<i class="bi bi-eye"></i> Voir')
            .removeClass("btn-outline-primary")
            .addClass("btn-primary");
        $("#btn-brigadiers")
            .html('<i class="bi bi-eye"></i> Voir')
            .removeClass("btn-outline-success")
            .addClass("btn-success");
        $("#btn-remplacements")
            .html('<i class="bi bi-eye"></i> Voir')
            .removeClass("btn-outline-secondary")
            .addClass("btn-secondary");
    }

    function showRemplacements() {
        const container = $("#list-container");
        const template = $("#remplacements-template").html();

        container.html(template).hide().fadeIn();
        initRemplacementsTable();

        $("#btn-remplacements")
            .html('<i class="bi bi-eye-slash"></i> Masquer')
            .removeClass("btn-secondary")
            .addClass("btn-outline-secondary");
        $("#btn-candidats")
            .html('<i class="bi bi-eye"></i> Voir')
            .removeClass("btn-outline-primary")
            .addClass("btn-primary");
        $("#btn-brigadiers")
            .html('<i class="bi bi-eye"></i> Voir')
            .removeClass("btn-outline-success")
            .addClass("btn-success");
        $("#btn-liste-attente")
            .html('<i class="bi bi-eye"></i> Voir')
            .removeClass("btn-outline-warning")
            .addClass("btn-warning");
    }

    function hideLists() {
        $("#list-container").fadeOut().empty();
        $("#btn-candidats")
            .html('<i class="bi bi-eye"></i> Voir')
            .removeClass("btn-outline-primary")
            .addClass("btn-primary");
        $("#btn-brigadiers")
            .html('<i class="bi bi-eye"></i> Voir')
            .removeClass("btn-outline-success")
            .addClass("btn-success");
        $("#btn-liste-attente")
            .html('<i class="bi bi-eye"></i> Voir')
            .removeClass("btn-outline-warning")
            .addClass("btn-warning");
        $("#btn-remplacements")
            .html('<i class="bi bi-eye"></i> Voir')
            .removeClass("btn-outline-secondary")
            .addClass("btn-secondary");

        // Détruire les tables DataTable si elles existent
        if (candidatsTable) {
            candidatsTable.destroy();
            candidatsTable = null;
        }
        if (brigadiersTable) {
            brigadiersTable.destroy();
            brigadiersTable = null;
        }
        if (listeAttenteTable) {
            listeAttenteTable.destroy();
            listeAttenteTable = null;
        }
        if (remplacementsTable) {
            remplacementsTable.destroy();
            remplacementsTable = null;
        }
    }

    // Event listeners
    $(document).on("click", "#btn-candidats", function () {
        if ($(this).hasClass("btn-outline-primary")) {
            hideLists();
        } else {
            showCandidats();
        }
    });

    $(document).on("click", "#btn-brigadiers", function () {
        if ($(this).hasClass("btn-outline-success")) {
            hideLists();
        } else {
            showBrigadiers();
        }
    });

    $(document).on("click", "#btn-liste-attente", function () {
        if ($(this).hasClass("btn-outline-warning")) {
            hideLists();
        } else {
            showListeAttente();
        }
    });

    $(document).on("click", "#btn-remplacements", function () {
        if ($(this).hasClass("btn-outline-secondary")) {
            hideLists();
        } else {
            showRemplacements();
        }
    });

    $(document).on(
        "click",
        "#close-candidats, #close-brigadiers, #close-liste-attente, #close-remplacements",
        function () {
            hideLists();
        }
    );
});
