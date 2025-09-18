function togglePassword(id, btn) {
    const input = document.getElementById(id);
    const icon = btn.querySelector("i");
    const isPwd = input.type === "password";
    input.type = isPwd ? "text" : "password";
    icon.classList.toggle("bi-eye-fill", !isPwd);
    icon.classList.toggle("bi-eye-slash-fill", isPwd);
}

function togglePassword(fieldId, button) {
    const input = document.getElementById(fieldId);
    const icon = button.querySelector("i");

    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("bi-eye-fill");
        icon.classList.add("bi-eye-slash-fill");
    } else {
        input.type = "password";
        icon.classList.remove("bi-eye-slash-fill");
        icon.classList.add("bi-eye-fill");
    }
}

document.addEventListener("DOMContentLoaded", function () {
    const nom = document.getElementById("nom");
    const prenom = document.getElementById("prenom");
    const Name = document.getElementById("name");

    function updateName() {
        Name.value = nom.value.trim() + " " + prenom.value.trim();
    }

    nom.addEventListener("input", updateName);
    prenom.addEventListener("input", updateName);

    // Initialiser si déjà rempli
    updateName();
});
