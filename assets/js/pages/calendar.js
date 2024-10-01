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
    

    //  CALENDAR PART ----------------------------------------------------------------

    var blogIndexUrl = document.getElementById('eventUrlInfos').getAttribute('data-blog-url');
    var calendarEl = document.getElementById('calendar');

    var events = [
        {
            start: '2024-10-17', // Date/Heure au format ISO
            end: '2024-10-17',
            backgroundColor: '#4581cc',
            extendedProps: { title: 'Randonnée à Bouvines', genre: 'Randonnée', rdv: 'Parking de la mairie, chaussée, Brunehaut', type: 'activity', infosDisplay: false }
        },
        {
            
            start: '2024-10-24',
            backgroundColor: '#4581cc',
            extendedProps: { title: 'Randonnée à Templeuve-en-Pévèle', genre: 'Randonnée', rdv: 'Parking centre ville, rue d’Anchin', type: 'activity', infosDisplay: false }
        },
        {
            
            start: '2024-10-26',
            backgroundColor: '#4581cc',
            extendedProps: { title: 'Gymnastique à Lambersart', genre: 'Gymnastique', rdv: 'Salle des sports Norbert Segard', type: 'activity', infosDisplay: true }
        },
        {
            start: '2024-10-31',
            backgroundColor: '#4581cc',
            extendedProps: { title: 'Randonnée à Zillebeck (Belgique)', genre: 'Randonnée', rdv: 'Salle des sports Norbert Segard', type: 'activity', infosDisplay: false }
        },
        {
            start: '2024-12-18',
            backgroundColor: '#EF991F',
            extendedProps: { title: 'Séjour au marché de Noël Strasbourg', genre: 'Séjour culture', rdv: false, type: 'journey', infosDisplay: true }
        },
        {
            start: '2024-12-19',
            backgroundColor: '#EF991F',
            extendedProps: { title: 'Séjour au marché de Noël Strasbourg', genre: 'Séjour culture', rdv: false, type: 'journey', infosDisplay: true }
        },
        {
            start: '2024-12-20',
            backgroundColor: '#EF991F',
            extendedProps: { title: 'Séjour au marché de Noël Strasbourg', genre: 'Séjour culture', rdv: false, type: 'journey', infosDisplay: true }
        },
        {
            start: '2024-12-21',
            backgroundColor: '#EF991F',
            extendedProps: { title: 'Séjour au marché de Noël Strasbourg', genre: 'Séjour culture', rdv: false, type: 'journey', infosDisplay: true }
        },
        {
            start: '2024-12-22',
            backgroundColor: '#EF991F',
            extendedProps: { title: 'Séjour au marché de Noël Strasbourg', genre: 'Séjour culture', rdv: false, type: 'journey', infosDisplay: true }
        },
    ];

    var calendar = new FullCalendar.Calendar(calendarEl, {
        timeZone: 'UTC',
        initialView: 'multiMonthYear',
        locale: 'fr',
        firstDay: 1,
        views: {
            timeGrid: {
              dayMaxEventRows: 6 // adjust to 6 only for timeGridWeek/timeGridDay
            }
        },
        buttonText: {
            today: "Aujourd'hui",
            month: "Mois",
            week: "Semaine",
            day: "Jour",
            list: "Liste"
        },
        events: events,
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
                openModal();  
            }
        }
    });


    calendar.render();
    calendar.updateSize();
    document.getElementById('loader-container').style.display = 'none' 

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