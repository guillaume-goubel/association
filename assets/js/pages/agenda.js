document.addEventListener("DOMContentLoaded", (event) => {

    // ACCORDION PART ----------------------------------------------------------------
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

    // MAP PART ----------------------------------------------------------------
    // Initialisation de la carte avec différents paramètres selon si on est sur mobile ou non
    var map = L.map('map', {
        scrollWheelZoom: true, 
    });

    // Charge et affiche les tuiles OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Ajoute le contrôle plein écran en haut à droite
    L.control.fullscreen({
        position: 'topright' // Change la position du bouton plein écran
    }).addTo(map);

    // Appeler invalidateSize lorsque la modale est ouverte
    $('#mapModal').on('shown.bs.modal', function () {
        
        var marker = L.marker([50.52716064453125, 3.1717026233673096]).addTo(map);
        marker.bindPopup("<b>Lieu de rendez-vous</b><br>parking centre ville, rue d’Anchin, 59242 TEMPLEUVE EN PEVELE");
        map.setView([50.52716064453125, 3.1717026233673096], 13);

        map.invalidateSize();  // Recalculer les dimensions de la carte
    });

});