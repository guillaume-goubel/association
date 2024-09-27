import '../../styles/pages/home.css';

document.addEventListener("DOMContentLoaded", (event) => {
    var blogIndexUrl = document.getElementById('eventDetails').getAttribute('data-blog-url');
    
    var calendarEl = document.getElementById('calendar');
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
        events: [
            {
                title: 'Randonnée à Bouvines',
                start: '2024-10-17T14:00:00',
                end: '2024-10-17T16:00:00',
                color: '#275997',
                extendedProps: {
                    type: 'activity'
                }
            },
            {
                title: 'Randonnée à Bouvines2',
                start: '2024-10-17T14:00:00',
                end: '2024-10-17T16:00:00',
                color: '#275997',
                extendedProps: {
                    type: 'activity'
                }
            },
            {
                title: 'Randonnée à Templeuve-en-Pévèle',
                start: '2024-10-24T11:00:00',
                color: '#275997',
                extendedProps: {
                    type: 'activity'
                }
            },
            {
                title: 'Gymnastique à Lambersart',
                start: '2024-10-26T14:00:00',
                color: '#275997',
                extendedProps: {
                    type: 'activity'
                }
            },
            {
                title: 'Randonnée à Zillebeck (Belgique)',
                start: '2024-10-31T14:00:00',
                color: '#275997',
                extendedProps: {
                    type: 'activity'
                }
            },
            {
                title: 'Séjour au marché de Noël Strasbourg',
                start: '2024-12-16',
                end: '2024-12-16',
                color: '#275997',
                extendedProps: {
                    type: 'journey'
                }
            },
            {
                title: 'Séjour au marché de Noël Strasbourg',
                start: '2024-12-17',
                end: '2024-12-17',
                color: '#275997',
                extendedProps: {
                    type: 'journey'
                }
            },
            {
                title: 'Séjour au marché de Noël Strasbourg',
                start: '2024-12-18',
                end: '2024-12-18',
                color: '#275997',
                extendedProps: {
                    type: 'journey'
                }
            },
            {
                title: 'Séjour au marché de Noël Strasbourg',
                start: '2024-12-19',
                end: '2024-12-19',
                color: '#275997',
                extendedProps: {
                    type: 'journey'
                }
            },
            {
                title: 'Séjour au marché de Noël Strasbourg',
                start: '2024-12-20',
                end: '2024-12-20',
                color: '#275997',
                extendedProps: {
                    type: 'journey'
                }
            },
            {
                title: 'Séjour au marché de Noël Strasbourg',
                start: '2024-12-21',
                end: '2024-12-21',
                color: '#275997',
                extendedProps: {
                    type: 'journey'
                }
            },
        ],
        dayCellDidMount: function(info) {
            // Vérifier s'il y a des événements pour ce jour
            var events = calendar.getEvents();
            var eventExists = events.some(function(event) {
                var eventDate = FullCalendar.formatDate(event.start, { 
                    year: 'numeric', 
                    month: '2-digit', 
                    day: '2-digit' 
                });
                var cellDate = FullCalendar.formatDate(info.date, { 
                    year: 'numeric', 
                    month: '2-digit', 
                    day: '2-digit' 
                });
                return eventDate === cellDate;
            });

            if (eventExists) {
                info.el.classList.add('highlight-day');
        
                // Compter le nombre d'événements pour ce jour
                var eventCount = events.filter(function(event) {
                    var eventDate = FullCalendar.formatDate(event.start, { 
                        year: 'numeric', 
                        month: '2-digit', 
                        day: '2-digit' 
                    });
                    return eventDate === FullCalendar.formatDate(info.date, { 
                        year: 'numeric', 
                        month: '2-digit', 
                        day: '2-digit' 
                    });
                });
        
                // Ajouter le compteur d'événements dans la cellule uniquement si le nombre d'événements est supérieur à 1
                if (eventCount.length > 1) {
                    var countEl = document.createElement('div');
                    countEl.classList.add('event-number');
                    countEl.innerText = '+ ' + eventCount.length;
                    info.el.appendChild(countEl);
                }
        
                // Ajouter les classes de type d'événement si défini
                eventCount.forEach(function(event) {
                    if (event.extendedProps && event.extendedProps.type) {  // Vérifiez si event.extendedProps.type est défini
                        info.el.classList.add(event.extendedProps.type); // Ajoute la classe du type d'événement
                    }
                });
            }
        },
        dateClick: function(info) {
            // Trouver les événements pour cette date
            var events = calendar.getEvents();
            var selectedDateEvents = events.filter(function(event) {
                var eventDate = FullCalendar.formatDate(event.start, { 
                    year: 'numeric', 
                    month: '2-digit', 
                    day: '2-digit' 
                });
                var clickedDate = FullCalendar.formatDate(info.date, { 
                    year: 'numeric', 
                    month: '2-digit', 
                    day: '2-digit' 
                });
                return eventDate === clickedDate;
            });

            // Si des événements existent pour cette date, les afficher dans la modale
            if (selectedDateEvents.length > 0) {
                var eventDetails = selectedDateEvents.map(function(event) {
                    return `
                        <p>
                            <strong>${event.title}</strong><br>
                            Début: ${event.start.toLocaleString()}<br>
                            Fin: ${event.end ? event.end.toLocaleString() : 'Non défini'}<br>
                            <a href="${blogIndexUrl}" class="btn btn-small btn-transparent-light-gray btn-box-shadow btn-round-edge border-1 w-100">Plus d'informations</a>
                        </p>`;
                }).join('');

                document.getElementById('eventDetails').innerHTML = eventDetails;
                openModal();
            }
        }
    });

    calendar.render();

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



// document.addEventListener("DOMContentLoaded", (event) => {
//     var blogIndexUrl = document.getElementById('eventDetails').getAttribute('data-blog-url');
    
//     var calendarEl = document.getElementById('calendar');
//     var calendar = new FullCalendar.Calendar(calendarEl, {
//         timeZone: 'UTC',
//         initialView: 'multiMonthYear',
//         locale: 'fr',
//         firstDay: 1,
//         buttonText: {
//             today: "Aujourd'hui",
//             month: "Mois",
//             week: "Semaine",
//             day: "Jour",
//             list: "Liste"
//         },
//         events: [
//             {
//                 title: 'Randonnée à Bouvines',
//                 start: '2024-10-17T14:00:00',
//                 end: '2024-10-17T16:00:00',
//                 color: '#275997',
//                 type : 'activity'
//             },
//             {
//                 title: 'Randonnée à Templeuve-en-Pévèle',
//                 start: '2024-10-24T11:00:00',
//                 color: '#275997',
//                 type : 'activity'
//             },
//             {
//                 title: 'Gymnastique à Lambersart',
//                 start: '2024-10-26T14:00:00',
//                 color: '#275997',
//                 type : 'activity'
//             },
//             {
//                 title: 'Randonnée à Zillebeck (Belgique)',
//                 start: '2024-10-31T14:00:00',
//                 color: '#275997',
//                 type : 'journey'
//             },
//             {
//                 title: 'Séjour au marché de Noël Strasbourg',
//                 start: '2024-12-16',
//                 end: '2024-12-16',
//                 color: '#275997',
//                 type : 'journey'
//             },
//             {
//                 title: 'Séjour au marché de Noël Strasbourg',
//                 start: '2024-12-17',
//                 end: '2024-12-17',
//                 color: '#275997',
//                 type : 'journey'
//             },
//             {
//                 title: 'Séjour au marché de Noël Strasbourg',
//                 start: '2024-12-18',
//                 end: '2024-12-18',
//                 color: '#275997',
//                 type : 'journey'
//             },
//             {
//                 title: 'Séjour au marché de Noël Strasbourg',
//                 start: '2024-12-19',
//                 end: '2024-12-19',
//                 color: '#275997',
//                 type : 'journey'
//             },
//             {
//                 title: 'Séjour au marché de Noël Strasbourg',
//                 start: '2024-12-20',
//                 end: '2024-12-20',
//                 color: '#275997',
//                 type : 'journey'
//             },
//             {
//                 title: 'Séjour au marché de Noël Strasbourg',
//                 start: '2024-12-21',
//                 end: '2024-12-21',
//                 color: '#275997',
//                 type : 'journey'
//             },
//         ],
//         dayCellDidMount: function(info) {
//             // Vérifier s'il y a des événements pour ce jour
//             var events = calendar.getEvents();
//             var eventExists = events.some(function(event) {
//                 var eventDate = FullCalendar.formatDate(event.start, { 
//                     year: 'numeric', 
//                     month: '2-digit', 
//                     day: '2-digit' 
//                 });
//                 var cellDate = FullCalendar.formatDate(info.date, { 
//                     year: 'numeric', 
//                     month: '2-digit', 
//                     day: '2-digit' 
//                 });
//                 return eventDate === cellDate;
//             });

//             if (eventExists) {
//                 info.el.classList.add('highlight-day');

//                 // Compter le nombre d'événements pour ce jour
//                 var eventCount = events.filter(function(event) {
//                     var eventDate = FullCalendar.formatDate(event.start, { 
//                         year: 'numeric', 
//                         month: '2-digit', 
//                         day: '2-digit' 
//                     });
//                     return eventDate === FullCalendar.formatDate(info.date, { 
//                         year: 'numeric', 
//                         month: '2-digit', 
//                         day: '2-digit' 
//                     });
//                 });

//                 // Ajouter le compteur d'événements dans la cellule uniquement si le nombre d'événements est supérieur à 1
//                 if (eventCount.length > 1) {
//                     var countEl = document.createElement('div');
                    
//                     countEl.classList.add('event-number');

//                     countEl.innerText = '+ ' + eventCount.length;
//                     info.el.appendChild(countEl);
//                 }

//                 // Ajouter les classes de type d'événement
//                 eventCount.forEach(function(event) {
//                     info.el.classList.add(event.type); // Ajoute la classe du type d'événement
//                 });
//             }
//         },
//         dateClick: function(info) {
//             // Trouver les événements pour cette date
//             var events = calendar.getEvents();
//             var selectedDateEvents = events.filter(function(event) {
//                 var eventDate = FullCalendar.formatDate(event.start, { 
//                     year: 'numeric', 
//                     month: '2-digit', 
//                     day: '2-digit' 
//                 });
//                 var clickedDate = FullCalendar.formatDate(info.date, { 
//                     year: 'numeric', 
//                     month: '2-digit', 
//                     day: '2-digit' 
//                 });
//                 return eventDate === clickedDate;
//             });

//             // Si des événements existent pour cette date, les afficher dans la modale
//             if (selectedDateEvents.length > 0) {
//                 var eventDetails = selectedDateEvents.map(function(event) {
//                     return `
//                         <p>
//                             <strong>${event.title}</strong><br>
//                             Début: ${event.start.toLocaleString()}<br>
//                             Fin: ${event.end ? event.end.toLocaleString() : 'Non défini'}<br>
//                             <a href="${blogIndexUrl}" class="btn btn-small btn-transparent-light-gray btn-box-shadow btn-round-edge border-1 w-100">Plus d'informations</a>
//                         </p>`;
//                 }).join('');

//                 document.getElementById('eventDetails').innerHTML = eventDetails;
//                 openModal();
//             }
//         }
//     });

//     calendar.render();

//     // Fonctions pour gérer l'affichage de la modale
//     function openModal() {
//         var modal = document.getElementById('eventModal');
//         modal.style.display = 'block';
//     }

//     function closeModal() {
//         var modal = document.getElementById('eventModal');
//         modal.style.display = 'none';
//     }

//     // Gérer la fermeture de la modale au clic sur le bouton "close"
//     document.querySelector('.close').addEventListener('click', closeModal);

//     // Gérer la fermeture de la modale en cliquant en dehors du contenu
//     window.addEventListener('click', function(event) {
//         var modal = document.getElementById('eventModal');
//         if (event.target == modal) {
//             modal.style.display = 'none';
//         }
//     });
// });