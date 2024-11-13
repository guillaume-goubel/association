import '../../styles/pages/home.css';

document.addEventListener("DOMContentLoaded", (event) => {
    
    // MAP ----------------------------------------------------------------
    // Initialisation de la carte avec différents paramètres selon si on est sur mobile ou non
    // var map = L.map('map', {
    //     scrollWheelZoom: true, 
    // });

    // // Charge et affiche les tuiles OpenStreetMap
    // L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    //     maxZoom: 19,
    //     attribution: '© OpenStreetMap contributors'
    // }).addTo(map);

    // map.setView([50.52716064453125, 3.1717026233673096], 13);

    // // Ajoute le contrôle plein écran en haut à droite
    // L.control.fullscreen({
    //     position: 'topright' // Change la position du bouton plein écran
    // }).addTo(map);
    
    //  CALENDAR PART ----------------------------------------------------------------
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        timeZone: 'UTC',
        initialView: 'multiMonthYear',  // Vue annuelle
        locale: 'fr',  // Langue française
        firstDay: 1,   // Commencer la semaine le lundi
        contentHeight: 'auto',  // Hauteur automatique
        events: events,  // Vos événements ici
    
        views: {
            multiMonthYear: {  // Vue multiple mois (personnalisée)
                type: 'multiMonth',
                duration: { months: 12 }, // Durée sur 1 an
                eventLimit: true, // Permet de limiter l'affichage des événements
                dayMaxEventRows: true,  // Limiter le nombre d'événements par jour
    
                // Personnalisation de l'affichage des événements
                eventContent: function(arg) {
                    // On ne montre que le nombre d'événements
                    var eventDate = FullCalendar.formatDate(arg.event.start, { year: 'numeric', month: '2-digit', day: '2-digit' });
    
                    // Compter les événements pour la date sélectionnée
                    var eventCount = 0;
                    calendar.getEvents().forEach(function(event) {
                        var eventStartDate = FullCalendar.formatDate(event.start, { year: 'numeric', month: '2-digit', day: '2-digit' });
                        if (eventStartDate === eventDate) {
                            eventCount++;
                        }
                    });
    
                    return { html: `<span>${eventCount}</span>` };
                }
            },
            dayGridMonth: {  // Vue mensuelle classique
                eventLimit: true, // Permet de limiter l'affichage des événements
                dayMaxEventRows: true,  // Limiter le nombre d'événements par jour
            },
            timeGridDay: {  // Vue quotidienne
                // Vous pouvez personnaliser ici si besoin
            }
        },
        headerToolbar: {  // Boutons pour basculer entre les vues
            left: '',
            center: '',
            right: ''
        },
        moreLinkText: function(n) {
            return n;  // Texte pour plus d'événements
        },
    
        buttonText: {
            today: "Aujourd'hui",
            year: "Année",
            month: "Mois",
            week: "Semaine",
            day: "Jour",
            list: "Liste"
        },
        
        dateClick: function(info) {

            var selectedDateEvents = calendar.getEvents().filter(function(event) {
                return FullCalendar.formatDate(event.start, { year: 'numeric', month: '2-digit', day: '2-digit' }) === 
                    FullCalendar.formatDate(info.date, { year: 'numeric', month: '2-digit', day: '2-digit' });
            });
        
            if (selectedDateEvents.length > 0) {
                var eventDetails = selectedDateEvents.map(function(event) {
                    
                    // Convertir les dates en objets Date pour comparaison
                    let eventStartDate = new Date(event.start);  // Assurez-vous que `event.start` est une date valide
                    let currentDate = new Date();
                    
                    // Vérifiez si l'événement est passé
                    let isPassed = eventStartDate < currentDate; 
                    console.log("Event Start Date:", eventStartDate);
                    console.log("Current Date:", currentDate);
                    console.log("Is Passed:", isPassed);
        
                    // Dynamiser le path en fonction de l'état de l'événement
                    let pathComplete = isPassed ? `/blog/${event.extendedProps.id}/index?is_passed=true` : `/blog/${event.extendedProps.id}/index?is_passed=false`;
                    
                    // Construction du contenu HTML des détails de l'événement
                    return `
                        <div class="col-12 col-md-10 mb-4">
                            <div>
                                <div>
                                    ${!isPassed && event.extendedProps.rdvDate ? `<span>Le ${event.extendedProps.rdvDate}</span>` : ''}
                                    ${!isPassed && event.extendedProps.rdvTime ? `<span>à ${event.extendedProps.rdvTime}</span>` : ''}
                                </div>

                                <strong class="me-1">${event.extendedProps.genre}</strong>
                                <div>${event.extendedProps.title ? event.extendedProps.title : ''}</div>
                
                                ${!isPassed && event.extendedProps.rdv ? `<span class="fs-12">Rendez-vous </span><span class="fs-14">${event.extendedProps.rdv}</span><br>` : ''}
                
                            </div>
                            ${event.extendedProps.infosDisplay ? `<a href="${pathComplete}" class="btn btn-very-small btn-yellow btn-box-shadow btn-round-edge border-1 w-150px">Plus d'informations</a>` : ''}
                        </div>
                    `;
                }).join('');
        
                // Affichage des détails dans le modal
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

        // var marker = L.marker([50.52716064453125, 3.1717026233673096]).addTo(map);
        // marker.bindPopup("<b>Lieu de rendez-vous</b><br>parking centre ville, rue d’Anchin, 59242 TEMPLEUVE EN PEVELE");
        // map.setView([50.52716064453125, 3.1717026233673096], 13);
        // map.invalidateSize();
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