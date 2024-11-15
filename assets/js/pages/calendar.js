import '../../styles/pages/calendar.css';

document.addEventListener("DOMContentLoaded", (event) => {
    
    var eventLinkDefault = null;
    const eventLinkDefaultElmt = document.getElementById('eventCreatedAt');
    if (eventLinkDefaultElmt) {
        eventLinkDefault = eventLinkDefaultElmt.href;
    }
    
    var isAdminIsLogged = document.getElementById('isAdminIsLogged').getAttribute('data-param');
    var yearChoice = document.getElementById('yearChoiceElmt').getAttribute('data-param');
   
    //  CALENDAR PART ----------------------------------------------------------------
    var calendarEl = document.getElementById('calendar');
    var calendarDuration = (yearChoice === 'yearDepth') ? { months: 12 } : { year: 1 };

    // Déclaration de `calendarInitialDate` en dehors des conditions
    let calendarInitialDate;
    
    // Vérifier la valeur de `yearChoice` et définir `calendarInitialDate` en conséquence
    if (yearChoice !== 'yearDepth') {
        calendarInitialDate = yearChoice + '-01-01'; // Par exemple, "2025-01-01"
    }
    // Initialiser le calendrier
    var calendar = new FullCalendar.Calendar(calendarEl, {
        timeZone: 'UTC',
        initialView: 'multiMonthYear',  // Vue annuelle
        initialDate: calendarInitialDate, // Utilisation de la date calculée
        locale: 'fr',  // Langue française
        firstDay: 1,   // Commencer la semaine le lundi
        contentHeight: 'auto',  // Hauteur automatique
        events: events,  // Vos événements ici
        views: {
            multiMonthYear: {  // Vue multiple mois (personnalisée)
                type: 'multiMonth',
                duration: calendarDuration, 
                eventLimit: true, 
                dayMaxEventRows: true,  
    
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
            center: 'title',
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
            const modalFooter = document.getElementById('calendarModalFooter');
            let clickedDate = new Date(info.date);
            let currentDate = new Date();  // Date actuelle
        
            // Réinitialiser l'heure de la date actuelle et de la clickedDate à 00:00 pour comparer uniquement les dates
            currentDate.setHours(0, 0, 0, 0); 
            clickedDate.setHours(0, 0, 0, 0);
        
            // Filtrer les événements pour la date sélectionnée
            let selectedDateEvents = calendar.getEvents().filter(event => {
                let eventDate = new Date(event.start);
                let eventFormattedDate = `${String(eventDate.getDate()).padStart(2, '0')}/${String(eventDate.getMonth() + 1).padStart(2, '0')}/${eventDate.getFullYear()}`;
                let formattedDate = `${String(clickedDate.getDate()).padStart(2, '0')}/${String(clickedDate.getMonth() + 1).padStart(2, '0')}/${clickedDate.getFullYear()}`;
                return eventFormattedDate === formattedDate;
            });
        
            // Si aucun événement n'est trouvé pour la date cliquée et que la date est dans le passé, on ne fait rien
            if (selectedDateEvents.length === 0 && clickedDate < currentDate) {
                return;  // Ne rien faire (empêcher l'interaction)
            }
        
            // Comparer clickedDate avec currentDate
            if (eventLinkDefault != null) {
                
                if (clickedDate >= currentDate) {
                    // Si la date cliquée est aujourd'hui ou dans le futur
                    let eventLink = document.getElementById('eventCreatedAt');
                    let day = String(clickedDate.getDate()).padStart(2, '0');
                    let month = String(clickedDate.getMonth() + 1).padStart(2, '0');
                    let year = clickedDate.getFullYear();
                    let clickedDateFormat = `${month}/${day}/${year}`;
                    
                    // Mettre à jour le lien
                    eventLink.href = `${eventLinkDefault}?creationDate=${encodeURIComponent(clickedDateFormat)}&from=calendar_index`;
                    
                    // Afficher le footer du modal (on peut créer un événement pour aujourd'hui ou dans le futur)
                    modalFooter.classList.remove('d-none');
                } else {
                    // Si la date cliquée est dans le passé
                    modalFooter.classList.add('d-none');
                }

            }

            // Formatage de la date pour affichage dans le titre
            let formattedDate = `${String(clickedDate.getDate()).padStart(2, '0')}/${String(clickedDate.getMonth() + 1).padStart(2, '0')}/${clickedDate.getFullYear()}`;
            document.getElementById('eventModalTitle').innerHTML = `${formattedDate}`;
        
            // Si des événements existent ou si l'admin est connecté
            if (selectedDateEvents.length > 0 || isAdminIsLogged) {
                // Générer les détails des événements
                let eventDetails = selectedDateEvents.map(event => {
                    let eventStartDate = new Date(event.start);
                    let isPassed = eventStartDate < currentDate;
                    let pathComplete = isPassed ? `/blog/${event.extendedProps.id}/index?is_passed=true` : `/blog/${event.extendedProps.id}/index?is_passed=false`;
        
                    return `
                        <div class="col-12 col-md-10 mb-4">
                            <div>
                                <strong class="me-1">${event.extendedProps.genre}</strong>
                                <div>
                                    ${event.extendedProps.title ? event.extendedProps.title : ''}
                                </div>
                                <div>
                                    ${!isPassed && event.extendedProps.rdvTime ? `<span><span class="fs-12">Horaire:</span> ${event.extendedProps.rdvTime}</span>` : ''}
                                    ${!isPassed && event.extendedProps.rdvTimeEnd ? `<span><span class="fs-12"> Fin:</span> ${event.extendedProps.rdvTimeEnd}</span>` : ''}
                                </div>
                                ${!isPassed && event.extendedProps.rdv ? `<span class="fs-12">Rendez-vous: </span><span class="fs-14">${event.extendedProps.rdv}</span><br>` : ''}
                            </div>
                            ${event.extendedProps.infosDisplay ? `<a href="${pathComplete}" class="btn btn-very-small btn-yellow btn-box-shadow btn-round-edge border-1 w-150px">Plus d'informations</a>` : ''}
                        </div>
                    `;
                }).join('');
        
                // Afficher les détails des événements dans la modal
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