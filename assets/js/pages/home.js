import '../../styles/pages/home.css';

document.addEventListener("DOMContentLoaded", (event) => {
    
    // MENU POPUP
    // if (typeof $.fn.magnificPopup === 'function') {
    //     if ($('#subscribe-popup').length > 0) {

    //         $(document).on('click', '.popup-modal-menu', function (e) {
    //             e.preventDefault();
    //             $.magnificPopup.open({
    //                 showCloseBtn: false,
    //                 items: {
    //                     src: '#subscribe-popup'
    //                 },
    //                 type: 'inline',
    //                 mainClass: 'my-mfp-zoom-in'
    //             });
    //         });
    //     }
    // }

    // ACTIVITY LIST PART
    // $('.activity-container').on('click', function(e) {
    //     e.preventDefault(); // Empêche l'action par défaut du lien

    //     // Récupère la valeur de data-activity de l'élément cliqué
    //     var activity = $(this).data('activity');

    //     // Montre la popup en fonction de l'activité
    //     $.magnificPopup.open({
    //         showCloseBtn: false,
    //         items: {
    //             // Utilise une condition ou une logique pour changer le contenu de la popup en fonction de l'activité
    //             src: '#' + activity + '-popup' // Ici, on suppose que vous avez des éléments avec des IDs correspondants
    //         },
    //         type: 'inline',
    //         mainClass: 'my-mfp-zoom-in',
    //         callbacks: {
    //             close: function () {
    //                 if ($('#newsletter-off').is(':checked')) {
    //                     setCookie(cookieName, 'shown', expireDays);
    //                 }
    //             }
    //         }
    //     });
    // });

    document.querySelectorAll('.activity-infos-link').forEach(function(element) {
        element.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Récupère l'attribut 'data-id' de l'élément cliqué

            // Récupère le chemin du data-path de l'élément #activityInfosPATH
            const url = document.getElementById('activityInfosPATH').getAttribute('data-path');

            let data = new FormData();
            data.append('activityId', this.getAttribute('data-id'));
        
            fetch(url, {
                method: 'POST',
                body: data,
            })
            .then(response => response.json())
            .then(data => {
                                
                const contentContainer = document.getElementById('activityModalcontent');
                contentContainer.innerHTML = '';
                
                contentContainer.insertAdjacentHTML('beforeend', data.content);
    
                const modal = new bootstrap.Modal(document.getElementById('activityModal'));
                modal.show();
    
            })
            .catch(error => {
                console.error('Erreur lors de la requête AJAX :', error);
            });
        });
    });

});