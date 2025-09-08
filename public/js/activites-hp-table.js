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
                        "Liste des activités du " +
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
                        "Liste des activités du " +
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
                        "Liste des activités du " +
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
