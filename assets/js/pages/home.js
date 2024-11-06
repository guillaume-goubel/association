import '../../styles/pages/home.css';

document.addEventListener("DOMContentLoaded", (event) => {
    
    // MENU POPUP
    if (typeof $.fn.magnificPopup === 'function') {
        if ($('#subscribe-popup').length > 0) {

            $(document).on('click', '.popup-modal-menu', function (e) {
                e.preventDefault();
                $.magnificPopup.open({
                    showCloseBtn: false,
                    items: {
                        src: '#subscribe-popup'
                    },
                    type: 'inline',
                    mainClass: 'my-mfp-zoom-in'
                });
            });
        }
    }

    // ACTIVITY LIST PART
    $('.activity-container').on('click', function(e) {
        e.preventDefault(); // Empêche l'action par défaut du lien

        // Récupère la valeur de data-activity de l'élément cliqué
        var activity = $(this).data('activity');

        // Montre la popup en fonction de l'activité
        $.magnificPopup.open({
            showCloseBtn: false,
            items: {
                // Utilise une condition ou une logique pour changer le contenu de la popup en fonction de l'activité
                src: '#' + activity + '-popup' // Ici, on suppose que vous avez des éléments avec des IDs correspondants
            },
            type: 'inline',
            mainClass: 'my-mfp-zoom-in',
            callbacks: {
                close: function () {
                    if ($('#newsletter-off').is(':checked')) {
                        setCookie(cookieName, 'shown', expireDays);
                    }
                }
            }
        });
    });

});