document.addEventListener("DOMContentLoaded", function () {
    let table = new DataTable("#example1", {
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        lengthMenu: [
            [10, 40, 50, 100, 200, -1],
            [10, 40, 50, 100, 200, "Tous"],
        ],
        pageLength: 10,
        dom: "Bfrtip", // <-- ajoute la barre de boutons
        buttons: [
            {
                extend: "csvHtml5",
                text: "Exporter CSV",
                title: function () {
                    return (
                        "Liste des structures du " +
                        new Date().toLocaleDateString()
                    );
                },
                className: "btn btn-outline-primary btn-sm",
            },
            {
                extend: "excelHtml5",
                text: "Exporter Excel",
                title: function () {
                    return (
                        "Liste des structures du " +
                        new Date().toLocaleDateString()
                    );
                },
                className: "btn btn-outline-success btn-sm",
            },
            {
                extend: "pdfHtml5",
                text: "Exporter PDF",
                title: function () {
                    return (
                        "Liste des structures du " +
                        new Date().toLocaleDateString()
                    );
                },
                className: "btn btn-outline-danger btn-sm",
            },
            {
                extend: "print",
                text: "Imprimer",
                className: "btn btn-outline-dark btn-sm",
            },
        ],
        columnDefs: [
            { orderable: false, targets: -1 }, // Désactiver le tri sur la colonne actions
        ],
        language: {
            // url: "",
            decimal: ",",
            thousands: " ",
            search: "Rechercher :",
            lengthMenu: "Afficher _MENU_ enregistrements",
            info: "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
            infoEmpty: "Aucune donnée disponible",
            infoFiltered: "(filtré à partir de _MAX_ entrées totales)",
            loadingRecords: "Chargement...",
            zeroRecords: "Aucun enregistrement correspondant trouvé",
            paginate: {
                first: "Premier",
                last: "Dernier",
                next: "Suivant",
                previous: "Précédent",
            },
        },
    });

    // Filtre par type de structure
    $("#filterType").on("change", function () {
        const selectedType = this.value;

        if (selectedType === "") {
            table.column(3).search("").draw(); // Colonne 3 = Type de structure
        } else {
            table.column(3).search(selectedType).draw();
        }
    });

    // Fonction de recherche personnalisée
    $("#customSearch").on("keyup", function () {
        table.search(this.value).draw();
    });

    // Confirmation de suppression avec SweetAlert (si disponible)
    $(document).on("click", '.btn-danger[type="submit"]', function (e) {
        e.preventDefault();
        const form = $(this).closest("form");

        if (typeof Swal !== "undefined") {
            Swal.fire({
                title: "Êtes-vous sûr ?",
                text: "Cette action ne pourra pas être annulée !",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Oui, supprimer !",
                cancelButtonText: "Annuler",
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        } else {
            if (confirm("Supprimer cette structure ?")) {
                form.submit();
            }
        }
    });

    // Animation des alertes
    $(".alert").each(function () {
        const alert = $(this);
        setTimeout(() => {
            alert.fadeOut("slow");
        }, 5000);
    });

    // Tooltip pour les boutons d'action
    $('[data-bs-toggle="tooltip"]').tooltip();

    // Badge animation au survol
    $(".badge").hover(
        function () {
            $(this).addClass("shadow-sm").css("transform", "scale(1.05)");
        },
        function () {
            $(this).removeClass("shadow-sm").css("transform", "scale(1)");
        }
    );

    // Statistiques rapides (si nécessaire)
    function updateStats() {
        const totalRows = table.data().length;
        const filteredRows = table.rows({ search: "applied" }).data().length;

        console.log(
            `Total structures: ${totalRows}, Affichées: ${filteredRows}`
        );
    }

    // Mettre à jour les stats après chaque recherche
    table.on("search.dt", updateStats);

    // Initialiser les stats
    updateStats();

    // Gestion responsive des boutons
    function handleResponsiveButtons() {
        if ($(window).width() < 768) {
            $(".btn-group").addClass("btn-group-vertical");
            $(".btn-xs").removeClass("btn-xs").addClass("btn-sm");
        } else {
            $(".btn-group").removeClass("btn-group-vertical");
            $(".btn-sm").removeClass("btn-sm").addClass("btn-xs");
        }
    }

    // Appliquer au chargement et au redimensionnement
    handleResponsiveButtons();
    $(window).resize(handleResponsiveButtons);

    // Export personnalisé
    $("#exportBtn").on("click", function () {
        const exportType = $("#exportType").val();

        switch (exportType) {
            case "excel":
                table.button(".buttons-excel").trigger();
                break;
            case "pdf":
                table.button(".buttons-pdf").trigger();
                break;
            case "csv":
                table.button(".buttons-csv").trigger();
                break;
            default:
                table.button(".buttons-copy").trigger();
        }
    });

    // Réinitialiser tous les filtres
    $("#resetFilters").on("click", function () {
        $("#filterType").val("").trigger("change");
        $("#customSearch").val("");
        table.search("").columns().search("").draw();
    });

    // Sauvegarde des préférences de filtres dans le localStorage
    $("#filterType").on("change", function () {
        localStorage.setItem("structures_filter_type", this.value);
    });

    // Restaurer les filtres sauvegardés
    const savedFilterType = localStorage.getItem("structures_filter_type");
    if (savedFilterType) {
        $("#filterType").val(savedFilterType).trigger("change");
    }

    // Animation d'entrée des lignes du tableau
    $("tbody tr").each(function (index) {
        $(this)
            .delay(index * 50)
            .fadeIn(300);
    });

    // 🔹 Filtre par période
    document
        .getElementById("filterPeriode")
        .addEventListener("change", function () {
            table
                .column(2) // colonne "Période" (index 2 → commence à 0)
                .search(this.value)
                .draw();
        });

    // 🔹 Filtre par objectif spécifique
    document
        .getElementById("filterObjectif")
        .addEventListener("change", function () {
            table
                .column(4) // colonne "Objectif spécifique" (index 4)
                .search(this.value)
                .draw();
        });
});
