import '../../styles/pages/home.css';

document.addEventListener("DOMContentLoaded", (event) => {
    
    // MENU POPUP
    if (typeof $.fn.magnificPopup === 'function') {
        if ($('#subscribe-popup').length > 0) {

            $(document).on('click', '.popup-modal-menu', function (e) {
                e.preventDefault();
                $.magnificPopup.open({
                    showCloseBtn: false,
                    items: {
                        src: '#subscribe-popup'
                    },
                    type: 'inline',
                    mainClass: 'my-mfp-zoom-in'
                });
            });
        }
    }

    // MAP PART

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

    // // Sélectionner tous les éléments avec la classe 'text-too-long'
    // var longTexts = document.querySelectorAll('.text-too-long');

    // // Boucle à travers chaque texte long
    // longTexts.forEach(function(text) {
    //     // Sélectionner le bouton associé
    //     var toggleBtn = text.nextElementSibling; // Le bouton est juste après le texte long

    //     // Ajouter un event listener pour le bouton
    //     toggleBtn.addEventListener('click', function() {
    //         // Vérifier si le texte est actuellement tronqué ou non
    //         if (text.classList.contains('text-expanded')) {
    //             // Si le texte est actuellement étendu, le replier
    //             text.classList.remove('text-expanded');
    //             toggleBtn.textContent = 'Voir plus';  // Changer le bouton en "Voir plus"
    //         } else {
    //             // Si le texte est tronqué, l'étendre
    //             text.classList.add('text-expanded');
    //             toggleBtn.textContent = 'Voir moins'; // Changer le bouton en "Voir moins"
    //         }
    //     });
    // });
});