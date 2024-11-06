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

    // ACTIVITY LIST PART
    $('.activity-container').on('click', function(e) {
        e.preventDefault(); // Empêche l'action par défaut du lien

        // Récupère la valeur de data-activity de l'élément cliqué
        var activity = $(this).data('activity');

        // Montre la popup en fonction de l'activité
        $.magnificPopup.open({
            showCloseBtn: false,
            items: {
                // Utilise une condition ou une logique pour changer le contenu de la popup en fonction de l'activité
                src: '#' + activity + '-popup' // Ici, on suppose que vous avez des éléments avec des IDs correspondants
            },
            type: 'inline',
            mainClass: 'my-mfp-zoom-in',
            callbacks: {
                close: function () {
                    if ($('#newsletter-off').is(':checked')) {
                        setCookie(cookieName, 'shown', expireDays);
                    }
                }
            }
        });
    });

    // MAP PART FOR RDV INFOS
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