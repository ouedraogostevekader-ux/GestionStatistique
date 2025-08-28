// Validation des dates côté client
document.getElementById("date_debut").addEventListener("change", function () {
    const dateDebut = this.value;
    const dateFin = document.getElementById("date_fin");

    if (dateDebut) {
        dateFin.min = dateDebut;
        if (dateFin.value && dateFin.value <= dateDebut) {
            dateFin.value = "";
        }
    }
});

document.getElementById("date_fin").addEventListener("change", function () {
    const dateFin = this.value;
    const dateDebut = document.getElementById("date_debut").value;

    if (dateDebut && dateFin && dateFin <= dateDebut) {
        alert("La date de fin doit être postérieure à la date de début");
        this.value = "";
    }
});
