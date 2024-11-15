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
    monthChoice.addEventListener('change', updateActivities);
    yearChoice.addEventListener('change', updateActivities);

});