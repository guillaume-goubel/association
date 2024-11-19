import '../../../styles/pages/admin/administrator.css';

document.addEventListener("DOMContentLoaded", (event) => {

    // Sélectionne tous les boutons de type "display-password-btn"
    document.querySelectorAll('.display-password-btn').forEach(button => {
        button.addEventListener('click', () => {
            // Récupère l'ID de l'input lié à ce bouton
            const inputId = button.getAttribute('data-input-id');
            const input = document.getElementById(inputId);

            if (input) {
                // Change le type de l'input entre "password" et "text"
                if (input.type === 'password') {
                    input.type = 'text';
                } else {
                    input.type = 'password';
                }
            }
        });
    });

    // open and change password field
    const changePassWordBtn = document.getElementById("changePassWordBtn");
    const passWordContainer = document.getElementById("passWordContainer");
    const plainPasswordInput = document.getElementById("administrator_plainPassword");
    const plainPasswordRepeatInput = document.getElementById("administrator_plainPasswordRepeat");

    if (changePassWordBtn) {
        changePassWordBtn.addEventListener("click", function () {
            // Toggle between 'd-none' and 'd-flex' classes
            passWordContainer.classList.toggle("d-none");
            passWordContainer.classList.toggle("d-flex");

            // If container is hidden, clear the input values
            if (passWordContainer.classList.contains("d-none")) {
                if (plainPasswordInput) plainPasswordInput.value = "";
                if (plainPasswordRepeatInput) plainPasswordRepeatInput.value = "";
            }
        });
    }
    
});