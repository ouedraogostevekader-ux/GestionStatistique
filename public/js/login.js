function togglePassword(id, btn) {
    const input = document.getElementById(id);
    const icon = btn.querySelector("i");
    const isPwd = input.type === "password";
    input.type = isPwd ? "text" : "password";
    icon.classList.toggle("bi-eye-fill", !isPwd);
    icon.classList.toggle("bi-eye-slash-fill", isPwd);
}
