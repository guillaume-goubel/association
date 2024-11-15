import '../../../styles/pages/admin/event.css';

document.addEventListener("DOMContentLoaded", (event) => {

    // // CHANGE EVENT STATUS ----------------------------
    // const eventStatusChangemodal = new bootstrap.Modal(document.getElementById('disabledModal'));
    // const disabledEventLink = document.getElementById('disabledEventLink');
    // const currentStatusVerbatim = document.getElementById('currentStatusVerbatim');
    // const currentStatusQuestion = document.getElementById('currentStatusQuestion');
    // const eventChangeStatusBtn = document.getElementById('eventChangeStatusBtn');
    // const eventChangeStatusConfirmation = document.getElementById('eventChangeStatusConfirmation');

    // document.querySelectorAll('.change-event-status').forEach(function(element) {
    //     element.addEventListener('click', function(e) {
    //         e.preventDefault();

    //         // PARAMS
    //         let currentStatus = element.getAttribute('data-current-status');
    //         let eventId = element.getAttribute('data-id');

    //         console.log(eventId);
    //         console.log(currentStatus);
            
    //         if (currentStatus == 'disabled') {
    //             currentStatusVerbatim.innerHTML = "Cet événement est actuellement inactif";
    //             currentStatusQuestion.innerHTML = "Confirmer son activation ?";
    //         }else{
    //             currentStatusVerbatim.innerHTML = "Cet événement est actuellement actif";
    //             currentStatusQuestion.innerHTML = "Confirmer sa désactivation ?";
    //         }

    //         disabledEventLink.setAttribute('data-current-status', currentStatus);
    //         disabledEventLink.setAttribute('data-id', eventId);

    //         eventStatusChangemodal.show();
    //     });
    // });

    // disabledEventLink.addEventListener('click', function(e) {
    //     e.preventDefault();
    //     eventChangeStatusBtn.classList.add('d-none');
    //     const url = this.getAttribute('data-path');

    //     let data = new FormData();
    //     data.append('eventId', this.getAttribute('data-id'));
    
    //     fetch(url, {
    //         method: 'POST',
    //         body: data,
    //     })
    //     .then(response => response.json())
    //     .then(data => {
            
    //         let eventId = data.event.id;
    //         let newStatus = data.event.isEnabled;

    //         let eventIcon = document.querySelector(`a[data-id="${eventId}"] i`);
    //         let eventActionContainer = document.querySelector(`div[data-id="${eventId}"]`);
    //         let changeEventStatusLink = document.querySelector(`a[data-id="${eventId}"]`);

    //         // Mettre à jour la classe de l'icône en fonction du statut
    //         if (eventIcon) {
    //             if (newStatus) {
    //                 // Si l'événement est activé, icône "check"
    //                 eventIcon.classList.remove('fa-circle-xmark');
    //                 eventIcon.classList.add('fa-circle-check');
    //             } else {
    //                 // Si l'événement est désactivé, icône "xmark"
    //                 eventIcon.classList.remove('fa-circle-check');
    //                 eventIcon.classList.add('fa-circle-xmark');
    //             }
    //         }
    //         // Mettre à jour la classe de 'event-action-container' en fonction du statut
    //         if (eventActionContainer) {
    //             if (newStatus) {
    //                 // Si l'événement est activé, ajouter la classe 'enabled'
    //                 eventActionContainer.classList.remove('disabled');
    //                 eventActionContainer.classList.add('enabled');
    //             } else {
    //                 // Si l'événement est désactivé, ajouter la classe 'disabled'
    //                 eventActionContainer.classList.remove('enabled');
    //                 eventActionContainer.classList.add('disabled');
    //             }
    //         }

    //         // Mettre à jour l'attribut "data-current-status" de "change-event-status"
    //         if (changeEventStatusLink) {
    //             if (newStatus) {
    //                 // Si l'événement est activé, mettre à jour l'attribut "data-current-status" à "enabled"
    //                 changeEventStatusLink.setAttribute('data-current-status', 'enabled');
    //             } else {
    //                 // Si l'événement est désactivé, mettre à jour l'attribut "data-current-status" à "disabled"
    //                 changeEventStatusLink.setAttribute('data-current-status', 'disabled');
    //             }
    //         }

    //         eventChangeStatusConfirmation.classList.remove('d-none');
    //             setTimeout(() => {
    //                 eventStatusChangemodal.hide();
    //                 eventChangeStatusConfirmation.classList.add('d-none');
    //                 eventChangeStatusBtn.classList.remove('d-none');
    //             }, 1200);
    //         })

    //         // remise à defaut de la modale
    //     .catch(error => {
    //         console.error('Erreur lors de la requête AJAX :', error);
    //     });

    // });

});