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
    // Change name when change admin rattached
    const animatorUserElmt = document.getElementById('animator_user');
    if (animatorUserElmt) {
        animatorUserElmt.addEventListener('change', function() {
            // Récupérer la valeur sélectionnée dans le select
            var selectedValue = this.value;
            
            // Récupérer les éléments des inputs pour le prénom et le nom
            var firstNameInput = document.getElementById('animator_firstName');
            var lastNameInput = document.getElementById('animator_lastName');
            
            // Si l'option choisie est vide, effacer les champs prénom et nom
            if (selectedValue === "") {
                firstNameInput.value = '';
                lastNameInput.value = '';
            } else {
                // Récupérer le texte de l'option sélectionnée
                var selectedOptionText = this.options[this.selectedIndex].text;
                
                // Enlever le préfixe #id et séparer le nom et le prénom
                var nameParts = selectedOptionText.replace(/^#\d+\s*/, '').split(' ');
                
                // Assurer que le tableau contient bien 2 éléments : Nom et Prénom
                if (nameParts.length === 2) {
                    lastNameInput.value = nameParts[0];  // Nom
                    firstNameInput.value = nameParts[1]; // Prénom
                }
            }
        });
    }

    // Manage the EDIT authoriztion switch --------------------
    const isCrudEditSwitch = document.getElementById('administrator_isCrudEdit');
    const isCrudEventCancelerElmt = document.getElementById('isCrudEventCancelerElmt');

    // Fonction pour gérer l'état de la classe
    function toggleCrudEventCanceler() {
        if (!isCrudEditSwitch.checked) {
            // Si isCrudEdit est false, ajouter la classe d-none
            isCrudEventCancelerElmt.classList.add('d-none');
        } else {
            // Sinon, retirer la classe d-none
            isCrudEventCancelerElmt.classList.remove('d-none');
        }
    }

    // Ajout d'un événement sur le changement de l'état du switch
    isCrudEditSwitch.addEventListener('change', toggleCrudEventCanceler);

    // Appel initial pour définir l'état au chargement
    toggleCrudEventCanceler();

});