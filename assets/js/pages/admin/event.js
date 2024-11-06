import '../../../styles/pages/admin/event.css';

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