document.addEventListener("DOMContentLoaded", (event) => {

    // Sélectionner tous les éléments d'accordéon
    const accordionItems = document.querySelectorAll('.accordion-item');

    accordionItems.forEach((item) => {
        const collapseElement = item.querySelector('.accordion-collapse');

        // Événement déclenché lors de l'ouverture (shown)
        collapseElement.addEventListener('shown.bs.collapse', function () {
            // Retirer 'inactive' de tous les éléments, et les marquer comme inactifs
            accordionItems.forEach((el) => {
                el.classList.remove('active');
                el.classList.add('inactive');
            });

            // Ajouter la classe 'active' uniquement à l'élément ouvert
            item.classList.add('active');
            item.classList.remove('inactive');
        });

        // Événement déclenché lors de la fermeture (hidden)
        collapseElement.addEventListener('hidden.bs.collapse', function () {
            // Réinitialiser tous les éléments : supprimer 'active' et 'inactive'
            accordionItems.forEach((el) => {
                el.classList.remove('active');
                el.classList.remove('inactive');
            });
        });
    });
});