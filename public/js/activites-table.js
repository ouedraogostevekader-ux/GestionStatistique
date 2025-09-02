document.addEventListener("DOMContentLoaded", function () {
    let table = new DataTable("#example1", {
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        pageLength: 10,
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json",
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
