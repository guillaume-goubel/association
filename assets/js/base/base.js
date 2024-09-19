document.addEventListener("DOMContentLoaded", (event) => {
    
    // HEADER LINKS
    $(document).on('click', '.header-link', function (e) {
        e.preventDefault();
        let data = $(this).data('anchor');
        if (data) {
            document.getElementById(data).scrollIntoView({ behavior: 'smooth' });;
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

});