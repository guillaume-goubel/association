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

});