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
    
});