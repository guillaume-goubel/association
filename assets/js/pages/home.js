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

    // HEADER LINKS
    $(document).on('click', '.header-link', function (e) {
        e.preventDefault();
        let data = $(this).data('anchor');

        if (data) {
            document.getElementById(data).scrollIntoView({ behavior: 'smooth' });;
        }

    });

});