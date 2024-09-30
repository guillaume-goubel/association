import '../../styles/pages/home.css';

document.addEventListener("DOMContentLoaded", (event) => {
    
    // MAP ----------------------------------------------------------------
    // Initialisation de la carte avec différents paramètres selon si on est sur mobile ou non
    var map = L.map('map', {
        scrollWheelZoom: true, 
    });

    // Charge et affiche les tuiles OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    map.setView([50.52716064453125, 3.1717026233673096], 13);

    // Ajoute le contrôle plein écran en haut à droite
    L.control.fullscreen({
        position: 'topright' // Change la position du bouton plein écran
    }).addTo(map);
    

     //  ----------------------------------------------------------------

    var blogIndexUrl = document.getElementById('eventUrlInfos').getAttribute('data-blog-url');
    var calendarEl = document.getElementById('calendar');
    
    var events = [
        {
            title: 'Randonnée à Bouvines',
            start: '2024-10-17T14:00:00',
            end: '2024-10-17T16:00:00',
            extendedProps: { genre: 'Randonnée', rdv: 'Parking de la mairie, chaussée, Brunehaut', type: 'activity', infosDisplay: false }
        },
        {
            title: 'Randonnée à Templeuve-en-Pévèle',
            start: '2024-10-24T11:00:00',
            extendedProps: { genre: 'Randonnée', rdv: 'Parking centre ville, rue d’Anchin', type: 'activity', infosDisplay: false }
        },
        {
            title: 'Gymnastique à Lambersart',
            start: '2024-10-26T14:00:00',
            extendedProps: { genre: 'Gymnastique', rdv: 'Salle des sports Norbert Segard', type: 'activity', infosDisplay: true }
        },
        {
            title: 'Randonnée à Zillebeck (Belgique)',
            start: '2024-10-31T14:00:00',
            extendedProps: { genre: 'Randonnée', rdv: 'Salle des sports Norbert Segard', type: 'activity', infosDisplay: false }
        },
        {
            title: 'Séjour au marché de Noël Strasbourg',
            start: '2024-12-16',
            end: '2024-12-21',
            extendedProps: { genre: 'Séjour culture', rdv: false, type: 'journey', infosDisplay: false }
        },
    ];
    
    var calendar = new FullCalendar.Calendar(calendarEl, {
        timeZone: 'UTC',
        initialView: 'multiMonthYear',
        locale: 'fr',
        firstDay: 1,
        buttonText: {
            today: "Aujourd'hui",
            month: "Mois",
            week: "Semaine",
            day: "Jour",
            list: "Liste"
        },
        events: events,
        dayCellDidMount: function(info) {
            var eventCount = calendar.getEvents().filter(function(event) {
                return FullCalendar.formatDate(event.start, { year: 'numeric', month: '2-digit', day: '2-digit' }) === 
                    FullCalendar.formatDate(info.date, { year: 'numeric', month: '2-digit', day: '2-digit' });
            });
    
            // Si des événements existent, ajouter les styles
            if (eventCount.length > 0) {
                info.el.classList.add('highlight-day');
                if (eventCount.length > 1) {
                    var countEl = document.createElement('div');
                    countEl.classList.add('event-number');
                    countEl.innerText = '+ ' + eventCount.length;
                    info.el.appendChild(countEl);
                }
                // Ajouter les classes de type d'événement si défini
                eventCount.forEach(function(event) {
                    if (event.extendedProps && event.extendedProps.type) {
                        info.el.classList.add(event.extendedProps.type);
                    }
                });
            }
        },
        dateClick: function(info) {
            var selectedDateEvents = calendar.getEvents().filter(function(event) {
                return FullCalendar.formatDate(event.start, { year: 'numeric', month: '2-digit', day: '2-digit' }) === 
                    FullCalendar.formatDate(info.date, { year: 'numeric', month: '2-digit', day: '2-digit' });
            });
    
            if (selectedDateEvents.length > 0) {
                var eventDetails = selectedDateEvents.map(function(event) {
                    return `
                        <p>
                            <strong>${event.title}</strong><br>
                            Rendez-vous : ${event.extendedProps.rdv || 'Non spécifié'}<br>
                            ${new Date(event.start).toLocaleString()}<br>
                            ${event.extendedProps.infosDisplay ? `<a href="${blogIndexUrl}" class="btn btn-small btn-yellow btn-box-shadow btn-round-edge border-1 w-100 mt-4">Plus d'informations</a>` : ''}
                        </p>`;
                }).join('');
    
                document.getElementById('eventDetails').innerHTML = eventDetails;
                openModal();  // Assurez-vous que cette fonction est définie dans votre code
            }
        }
    });
    
    calendar.render();
    

    // Fonctions pour gérer l'affichage de la modale
    function openModal() {
        
        var modal = document.getElementById('eventModal');
        modal.style.display = 'block';

        var marker = L.marker([50.52716064453125, 3.1717026233673096]).addTo(map);
        marker.bindPopup("<b>Lieu de rendez-vous</b><br>parking centre ville, rue d’Anchin, 59242 TEMPLEUVE EN PEVELE");
        map.setView([50.52716064453125, 3.1717026233673096], 13);
        map.invalidateSize();
    }

    function closeModal() {
        var modal = document.getElementById('eventModal');
        modal.style.display = 'none';
    }

    // Gérer la fermeture de la modale au clic sur le bouton "close"
    document.querySelector('.close').addEventListener('click', closeModal);

    // Gérer la fermeture de la modale en cliquant en dehors du contenu
    window.addEventListener('click', function(event) {
        var modal = document.getElementById('eventModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    });

});