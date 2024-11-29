document.addEventListener("DOMContentLoaded", (event) => {
    
    // Fonction pour détecter si l'utilisateur est sur mobile
    function isMobile() {
        return /Android|iPhone|iPad|iPod|Opera Mini|IEMobile|WPDesktop/i.test(navigator.userAgent);
    }
    
    // HEADER LINKS
    $(document).on('click', '.header-link', function (e) {
        e.preventDefault();
        let anchor = $(this).data('anchor');
        let menuBtn = document.querySelector('.navbar-toggler.float-start');
    
        if (anchor) {
            const targetElement = document.getElementById(anchor);
            if (targetElement) {
                if (isMobile()) {
                    // Calculer la position avec un ajustement de 50px
                    const offset = targetElement.getBoundingClientRect().top + window.scrollY - 130;
    
                    // Effectuer le défilement manuel
                    window.scrollTo({ top: offset, behavior: 'auto' });
    
                    // Fermer le menu après un délai si nécessaire
                    if (menuBtn) {
                        setTimeout(() => {
                            menuBtn.click();
                        }, 500);
                    }
                } else {
                    // Défilement fluide sans ajustement
                    targetElement.scrollIntoView({ behavior: 'smooth' });
                }
            } else {
                console.error(`Ancre introuvable : ${anchor}`);
            }
        }
    });
    

    // BACK HISTORIC LINK
    const backButton = document.getElementById('backButton');
    if (backButton) {
        $(document).on('click', '#backButton', function (e) {
            e.preventDefault();
            window.history.back();
        });
    }

    // GET animators INFOS
    document.querySelectorAll('.animators-infos-link').forEach(function(element) {
        element.addEventListener('click', function(e) {
            e.preventDefault();
            
            const url = document.getElementById('animatorInfosPATH').getAttribute('data-path');

            let data = new FormData();
            data.append('eventId', this.getAttribute('data-id'));
        
            fetch(url, {
                method: 'POST',
                body: data,
            })
            .then(response => response.json())
            .then(data => {
                                
                const contentContainer = document.getElementById('animatorsModalcontent');
                contentContainer.innerHTML = '';
                
                contentContainer.insertAdjacentHTML('beforeend', data.content);
    
                const modal = new bootstrap.Modal(document.getElementById('animatorsModal'));
                modal.show();
    
            })
            .catch(error => {
                console.error('Erreur lors de la requête AJAX :', error);
            });
        });
    });

    // GET activity by filters
    const monthChoice = document.getElementById('monthChoice');
    const yearChoice = document.getElementById('yearChoice');
    const activityChoice = document.getElementById('activityChoice');

    function updateActivities() {
        const month = monthChoice.value;
        const year = yearChoice.value;
        const url = document.getElementById('activitydynamicPATH').getAttribute('data-path');

        let data = new FormData();
        data.append('month', month);
        data.append('year', year);

        // Effectue une requête GET avec les paramètres month et year
        fetch(url, {
            method: 'POST',
            body: data,
        })
        .then(response => response.json())
        .then(data => {

            // Vider le select de `activityChoice`
            activityChoice.innerHTML = '';

            // Ajouter l'option "Toutes activités" par défaut
            const defaultOption = document.createElement('option');
            defaultOption.value = 'all';
            defaultOption.textContent = 'Toutes activités';
            activityChoice.appendChild(defaultOption);

            // Ajouter les nouvelles options d'activités
            data.activities.forEach(activity => {
                const option = document.createElement('option');
                option.value = activity.id;
                option.textContent = activity.name;
                activityChoice.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Erreur lors de la requête AJAX :', error);
        });
    }

    // Ajoute les événements de changement aux sélecteurs `monthChoice` et `yearChoice`
    if (monthChoice) {
        monthChoice.addEventListener('change', updateActivities);
    }
    if (yearChoice) {
        yearChoice.addEventListener('change', updateActivities);
    }

    // CHANGE EVENT STATUS ----------------------------
    const eventStatusChangemodal = new bootstrap.Modal(document.getElementById('disabledModal'));
    const disabledEventLink = document.getElementById('disabledEventLink');
    const currentStatusVerbatim = document.getElementById('currentStatusVerbatim');
    const currentStatusQuestion = document.getElementById('currentStatusQuestion');
    const eventChangeStatusBtn = document.getElementById('eventChangeStatusBtn');
    const eventChangeStatusConfirmation = document.getElementById('eventChangeStatusConfirmation');

    document.querySelectorAll('.change-event-status').forEach(function(element) {
        element.addEventListener('click', function(e) {
            e.preventDefault();

            // PARAMS
            let currentStatus = element.getAttribute('data-current-status');
            let eventId = element.getAttribute('data-id');

            if (currentStatus == 'disabled') {
                currentStatusVerbatim.innerHTML = "Cet événement est actuellement <strong class='text-red'>invisible</strong>";
                currentStatusQuestion.innerHTML = "Confirmer son activation ?";
            }else{
                currentStatusVerbatim.innerHTML = "Cet événement est actuellement <strong class='text-green'>visible</strong>";
                currentStatusQuestion.innerHTML = "Confirmer sa désactivation ?";
            }

            disabledEventLink.setAttribute('data-current-status', currentStatus);
            disabledEventLink.setAttribute('data-id', eventId);

            eventStatusChangemodal.show();
        });
    });

    disabledEventLink.addEventListener('click', function(e) {
        e.preventDefault();
        eventChangeStatusBtn.classList.add('d-none');
        const url = this.getAttribute('data-path');
        const eventId = this.getAttribute('data-id');

        let data = new FormData();
        data.append('eventId', eventId);
    
        fetch(url, {
            method: 'POST',
            body: data,
        })
        .then(response => response.json())
        .then(data => {
            
            let newStatus = data.event.isEnabled;

            let eventIcon = document.querySelector(`.event-icon[data-id="${eventId}"]`);
            let eventActionContainer = document.querySelector(`.event-action-container[data-id="${eventId}"]`);
            let changeEventStatusLink = document.querySelector(`.change-event-status[data-id="${eventId}"]`);

            // Mettre à jour la classe de l'icône en fonction du statut
            if (eventIcon) {
                if (newStatus) {
                    // Si l'événement est activé, icône "check"
                    eventIcon.classList.remove('fa-eye-slash');
                    eventIcon.classList.add('fa-eye');
                } else {
                    // Si l'événement est désactivé, icône "xmark"
                    eventIcon.classList.remove('fa-eye');
                    eventIcon.classList.add('fa-eye-slash');
                }
            }
            // Mettre à jour la classe de 'event-action-container' en fonction du statut
            if (eventActionContainer) {
                if (newStatus) {
                    // Si l'événement est activé, ajouter la classe 'enabled'
                    eventActionContainer.classList.remove('disabled');
                    eventActionContainer.classList.add('enabled');
                } else {
                    // Si l'événement est désactivé, ajouter la classe 'disabled'
                    eventActionContainer.classList.remove('enabled');
                    eventActionContainer.classList.add('disabled');
                }
            }

            if (changeEventStatusLink) {
                if (newStatus) {
                    // Si l'événement est activé, mettre à jour l'attribut "data-current-status" à "enabled"
                    changeEventStatusLink.setAttribute('data-current-status', 'enabled');
                } else {
                    // Si l'événement est désactivé, mettre à jour l'attribut "data-current-status" à "disabled"
                    changeEventStatusLink.setAttribute('data-current-status', 'disabled');
                }
            }

            eventChangeStatusConfirmation.classList.remove('d-none');
                setTimeout(() => {
                    eventStatusChangemodal.hide();
                    eventChangeStatusConfirmation.classList.add('d-none');
                    eventChangeStatusBtn.classList.remove('d-none');
                }, 1200);
            })

            // remise à defaut de la modale
        .catch(error => {
            console.error('Erreur lors de la requête AJAX :', error);
        });

    });

    // CHANGE CARD OR LIST VIEW EVENT RENDERING -----------------------------------------------------------------
    const eventCardsElmtRender = document.getElementById("events-cards-render");
    const eventListElmtRender = document.getElementById("events-list-render");
    
    // Fonction pour détecter si l'utilisateur est sur mobile
    function isMobile() {
        return /Android|iPhone|iPad|iPod|Opera Mini|IEMobile|WPDesktop/i.test(navigator.userAgent);
    }
    
    if (eventCardsElmtRender && eventListElmtRender) {
        
        if (isMobile()) {
            // eventCardsElmtRender.classList.remove('d-flex');
            // eventCardsElmtRender.classList.add('d-none');
    
            eventListElmtRender.classList.remove('d-none');
            eventListElmtRender.classList.add('d-flex');
    
        }else{
            eventCardsElmtRender.classList.remove('d-none');
            eventCardsElmtRender.classList.add('d-flex');
    
            // eventListElmtRender.classList.remove('d-flex');
            // eventListElmtRender.classList.add('d-none');
        }
        // setTimeout(() => {
        //     document.getElementById('loader-container').style.display = 'none'
        // }, 500);
    }

    // GET activities by user change
    const userChoiceElmt = document.getElementById('event_user');
    const getViewTypeElmt = document.getElementById('getViewTypePARAM');

    var getViewTypeParam; // Déclarer la variable avec `let` ou `var` à l'extérieur des blocs
    if (getViewTypeElmt) {
        getViewTypeParam = getViewTypeElmt.getAttribute('data-param');   
    } else {
        getViewTypeParam = null;
    }

    if (userChoiceElmt) {     
        userChoiceElmt.addEventListener('change', function(e) {
            
            e.preventDefault(); 
            
            const url = document.getElementById('getActivitiesByUserPARAM').getAttribute('data-path');
            const userId = userChoiceElmt.value;  
            
            let data = new FormData();
            data.append('userId', userId); 

            fetch(url, {
                method: 'POST',
                body: data,
            })
            .then(response => response.json())  
            .then(data => {
                
                // Récupère l'élément select avec l'ID 'event_activity'
                const activitiesElmt = document.getElementById('event_activity');
                
                // Vider le select avant de rajouter les nouvelles options
                activitiesElmt.innerHTML = '';

                // Ajouter l'option "all" si on est dans la vue admin event index
                if (getViewTypeParam === 'admin_event_index') {
                    const allOption = document.createElement('option');
                    allOption.value = 'all'; // La valeur de l'option "all"
                    allOption.textContent = 'Toutes les activités'; // Texte visible pour l'option
                    activitiesElmt.appendChild(allOption); // Ajouter l'option au select
                }
                
                // Parcourt les activités renvoyées et ajoute une option pour chaque activité
                data.activitiesArray.forEach(activity => {
                    const option = document.createElement('option');
                    option.value = activity.id; // Utilise l'ID de l'activité comme valeur
                    option.textContent = activity.name; // Affiche le nom de l'activité comme texte
                    activitiesElmt.appendChild(option); // Ajoute l'option au select
                });

            })
            .catch(error => {
                console.error('Erreur lors de la requête AJAX :', error);
            });
        });
    }

    // EVENT LIST alternative background class function event loop 
    // Récupérer l'élément parent
    const eventListContainer = document.getElementById('events-list-render');

    // Vérifier si l'élément existe
    if (eventListContainer) {
        // Récupérer tous les enfants directs (divs)
        const eventItems = eventListContainer.children;

        // Parcourir les enfants et alterner les classes
        Array.from(eventItems).forEach((child, index) => {
            // Ajouter une classe différente selon l'index pair ou impair
            if (index % 2 === 0) {
                child.classList.add('event-background-light'); // Si pair
            } else {
                child.classList.add('event-background-dark'); // Si impair
            }
        });
    }

});