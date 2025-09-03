document.addEventListener("DOMContentLoaded", function () {
    // Initialize the DataTable
    $("#example2").DataTable({
        responsive: true,
        pageLength: 10,
        lengthMenu: [
            [5, 10, 25, 50, -1],
            [5, 10, 25, 50, "Tout"],
        ],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json",
        },
    });
});
