document.addEventListener("DOMContentLoaded", (event) => {

        // Fonction pour détecter si l'utilisateur est sur mobile
        function isMobile() {
            return /Android|iPhone|iPad|iPod|Opera Mini|IEMobile|WPDesktop/i.test(navigator.userAgent);
        }

        // Initialisation de la carte avec différents paramètres selon si on est sur mobile ou non
        var map = L.map('map', {
            center: [50.65, 3.2], // Coordonnées de Paris
            zoom: 13,
            scrollWheelZoom: false, // Désactive le zoom automatique à la molette sur desktop
            touchZoom: isMobile() // Active le pinch-to-zoom uniquement sur mobile
        });

        // Charge et affiche les tuiles OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Ajoute un marqueur (facultatif)
        var marker = L.marker([50.65, 3.2]).addTo(map);
        marker.bindPopup("<b>Lieu de rendez-vous</b><br>15 Allée des champs, 59510 Hem").openPopup();

        // Ajoute le contrôle plein écran en haut à droite
        L.control.fullscreen({
            position: 'topright' // Change la position du bouton plein écran
        }).addTo(map);

        // Gère l'événement pour entrer/sortir du mode plein écran
        map.on('enterFullscreen', function(){
            map.scrollWheelZoom.enable(); // Active le zoom à la molette en plein écran
            document.getElementById('mapOverlay').style.display = 'none'; // Cache l'overlay en plein écran
        });

        map.on('exitFullscreen', function(){
            map.scrollWheelZoom.enable(); // Réactive le zoom à la molette en sortie de plein écran
            document.getElementById('mapOverlay').style.display = 'flex'; // Affiche l'overlay en dehors du plein écran
        });

        // Affiche le message d'information de zoom
        var overlay = document.getElementById('mapOverlay');

        function showZoomNotification() {
            if (!document.fullscreenElement) { // Vérifie si le mode plein écran n'est pas activé
                overlay.style.display = 'flex'; // Affiche l'overlay
                setTimeout(function() {
                    overlay.style.display = 'none'; // Cache l'overlay après 3 secondes
                }, 3000);
            }
        }

        // Gestion du zoom avec Ctrl + molette ou molette uniquement
        if (!isMobile()) {
            // Activation du zoom avec Ctrl sur desktop
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey) {
                    map.scrollWheelZoom.enable(); // Active le zoom si Ctrl est enfoncé
                }
            });

            document.addEventListener('keyup', function(e) {
                if (!e.ctrlKey) {
                    map.scrollWheelZoom.disable(); // Désactive le zoom si Ctrl est relâché
                }
            });

            // Désactive le zoom quand la souris quitte la carte
            map.on('mouseout', function() {
                map.scrollWheelZoom.disable();
            });

            // Gérer l'événement de défilement de la molette directement sur l'élément #map
            document.getElementById('map').addEventListener('wheel', function(e) {
                if (!document.fullscreenElement) {
                    // Si l'utilisateur n'est pas en plein écran
                    if (!e.ctrlKey) {
                        // Si Ctrl n'est pas appuyé et l'overlay n'est pas affiché, afficher l'overlay
                        showZoomNotification();
                    }
                }else{
                    map.scrollWheelZoom.enable();
                }
            }, { passive: true }); // Ajouter { passive: true } pour améliorer les performances
        }

});