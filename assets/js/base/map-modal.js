document.addEventListener("DOMContentLoaded", (event) => {

    let markers = [];
    
    // MAP MODALE PART ----------------------------------------------------------------
    var map = L.map('map', {
        scrollWheelZoom: true, 
    });
    
    // OPEN MODAL
    document.querySelectorAll('.link-to-map').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
    
            markers.forEach(marker => map.removeLayer(marker));
            markers = []; 
    
            let rdv = link.getAttribute('data-rdv');
            let lat = parseFloat(link.getAttribute('data-lat')); 
            let long = parseFloat(link.getAttribute('data-long')); 

            var modalTitle = document.getElementById('modalTitle');
            modalTitle.textContent = rdv;
    
            var marker = L.marker([lat, long]).addTo(map);
            markers.push(marker); // Ajouter le marqueur au tableau
    
            map.setView([lat, long], 17);
            marker.bindPopup(`<b>Lieu du rendez-vous</b><br>${rdv}`);

            // Renseigner l'URL Google Maps avec les bonnes coordonnées
            const googleMapsLink = document.getElementById('gmLink');
            if (googleMapsLink) {
                googleMapsLink.href = `https://www.google.com/maps?q=${lat},${long}`;
            }
    
            const modal = new bootstrap.Modal(document.getElementById('mapModal'));
            modal.show();
            
        });
    });

    // Événement pour réinitialiser le contenu ou autre chose lorsque la modale est fermée
    document.getElementById('mapModal').addEventListener('hidden.bs.modal', function () {
        // Supprimez la classe 'modal-backdrop fade' si elle est présente
        var backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.classList.remove('fade');
            backdrop.remove(); // Supprime le backdrop complètement
        }
        // Supprimer la classe modal-open manuellement
        document.body.classList.remove('modal-open');
        // Réinitialiser le style overflow et padding-right
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
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
        map.invalidateSize();
    });

});