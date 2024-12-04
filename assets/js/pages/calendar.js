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

    // Fonction pour formater une date
    function formatDate(date, format) {
        return FullCalendar.formatDate(date, format);
    }

    // Fonction pour formater une date en français
    function formatFrenchDate(date) {
        return new Intl.DateTimeFormat('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' }).format(date);
    }

    // Fonction pour récupérer les événements d'une date donnée
    function getEventsForDate(calendar, date) {
        const formattedDate = formatDate(date, { year: 'numeric', month: '2-digit', day: '2-digit' });
        return calendar.getEvents().filter(event => {
            const eventStartDate = formatDate(event.start, { year: 'numeric', month: '2-digit', day: '2-digit' });
            return eventStartDate === formattedDate;
        });
    }

    // Fonction pour personnaliser le contenu des événements dans multiMonthYear
    function renderEventContent(arg) {
        const eventDate = formatDate(arg.event.start, { year: 'numeric', month: '2-digit', day: '2-digit' });
        const eventCount = getEventsForDate(calendar, new Date(eventDate)).length;
        return { html: `<span>${eventCount}</span>` };
    }

    // Fonction pour gérer le clic sur une date
    function handleDateClick(info) {
        const modalFooter = document.getElementById('calendarModalFooter');
        const clickedDate = new Date(info.date);
        const currentDate = new Date();

        clickedDate.setHours(0, 0, 0, 0);
        currentDate.setHours(0, 0, 0, 0);

        const selectedDateEvents = getEventsForDate(calendar, clickedDate);
        const isFutureOrToday = clickedDate >= currentDate;

        if (!selectedDateEvents.length && clickedDate < currentDate) return;

        // Mise à jour du lien et du modal footer
        if (eventLinkDefault) {
            const formattedDate = `${String(clickedDate.getMonth() + 1).padStart(2, '0')}/${String(clickedDate.getDate()).padStart(2, '0')}/${clickedDate.getFullYear()}`;
            const eventLink = document.getElementById('eventCreatedAt');
            eventLink.href = `${eventLinkDefault}?creationDate=${encodeURIComponent(formattedDate)}&from=admin_calendar_index`;

            modalFooter.classList.toggle('d-none', !isFutureOrToday);
        }

        // Mise à jour du titre de la modal
        document.getElementById('eventModalTitle').innerHTML = formatFrenchDate(clickedDate, { day: '2-digit', month: '2-digit', year: 'numeric' });

        // Affichage des détails des événements
        if (selectedDateEvents.length || isAdminIsLogged) {
            const eventDetails = selectedDateEvents.map(event => renderEventDetails(event, currentDate)).join('');
            document.getElementById('eventDetails').innerHTML = eventDetails;
            openModal();
        }
    }

    // Fonction pour afficher les détails d'un événement
    function renderEventDetails(event, currentDate) {
        const isPassed = new Date(event.start) < currentDate;
        const pathComplete = `/blog/${event.extendedProps.id}/index?is_passed=${isPassed}`;
        const eventProps = event.extendedProps;

        if (eventProps.duration == 'long') {
            return `
                <div class="col-12 col-md-10 mb-4">
                    <div>
                        <strong class="me-1">${eventProps.genre}</strong>
                        <div>${eventProps.title || ''}</div>
                        <div class="fs-14">Evénement sur ${eventProps.durationTotal} jours. <span>(${eventProps.durationDayNumber})</span></div>
                        <div class="fs-14">
                            <span class="me-2"><span class="fs-12">Date début: </span>${eventProps.eventDateStartAt}</span>
                            <span><span class="fs-12">Date fin: </span>${eventProps.eventDateEndAt}</span>
                        </div>

                        ${!isPassed && eventProps.rdv ? `<span class="fs-12">Rendez-vous: </span><span class="fs-14">${eventProps.rdv}</span><br>` : ''}
                    </div>
                    <a href="${pathComplete}" class="btn btn-very-small btn-yellow btn-box-shadow btn-round-edge border-1 w-150px">Plus d'informations</a>
                </div>`;
        }else{
            return `
            <div class="col-12 col-md-10 mb-4">
                <div>
                    <strong class="me-1">${eventProps.genre}</strong>
                    <div>${eventProps.title || ''}</div>
                    <div class="fs-14">
                        ${!isPassed && eventProps.rdvTime ? `<span class="me-2"><span class="fs-12">Horaire début:</span> ${eventProps.rdvTime}</span>` : ''}
                        ${!isPassed && eventProps.rdvTimeEnd ? `<span><span class="fs-12"> Horaire fin:</span> ${eventProps.rdvTimeEnd}</span>` : ''}
                    </div>
                    ${!isPassed && eventProps.rdv ? `<span class="fs-12">Rendez-vous: </span><span class="fs-14">${eventProps.rdv}</span><br>` : ''}
                </div>
                <a href="${pathComplete}" class="btn btn-very-small btn-yellow btn-box-shadow btn-round-edge border-1 w-150px">Plus d'informations</a>
            </div>`;
        }

    }

    // Initialiser le calendrier
    const calendar = new FullCalendar.Calendar(calendarEl, {
        timeZone: 'UTC',
        initialView: 'multiMonthYear',
        initialDate: calendarInitialDate,
        locale: 'fr',
        firstDay: 1,
        contentHeight: 'auto',
        events: events,
        views: {
            multiMonthYear: {
                type: 'multiMonth',
                duration: calendarDuration,
                eventLimit: true,
                dayMaxEventRows: true,
                eventContent: renderEventContent
            },
            dayGridMonth: {
                eventLimit: true,
                dayMaxEventRows: true,
            },
            timeGridDay: {}
        },
        headerToolbar: {
            left: '',
            center: 'title',
            right: ''
        },
        moreLinkText: n => n,
        buttonText: {
            today: "Aujourd'hui",
            year: "Année",
            month: "Mois",
            week: "Semaine",
            day: "Jour",
            list: "Liste"
        },
        dateClick: handleDateClick
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