import '../../../styles/pages/admin/event.css';

document.addEventListener("DOMContentLoaded", (event) => {

    const eventCardsElmtRender = document.getElementById("events-cards-render");
    const eventListElmtRender = document.getElementById("events-list-render");
    
    // Fonction pour détecter si l'utilisateur est sur mobile
    function isMobile() {
        return /Android|iPhone|iPad|iPod|Opera Mini|IEMobile|WPDesktop/i.test(navigator.userAgent);
    }
    
    if (eventCardsElmtRender && eventListElmtRender) {
        
        if (isMobile()) {
            eventCardsElmtRender.classList.remove('d-flex');
            eventCardsElmtRender.classList.add('d-none');
    
            eventListElmtRender.classList.remove('d-none');
            eventListElmtRender.classList.add('d-flex');
    
        }else{
            eventCardsElmtRender.classList.remove('d-none');
            eventCardsElmtRender.classList.add('d-flex');
    
            eventListElmtRender.classList.remove('d-flex');
            eventListElmtRender.classList.add('d-none');
        }
        setTimeout(() => {
            document.getElementById('loader-container').style.display = 'none'
        }, 500);

    }

    // GET activities by user change
    const userChoiceElmt = document.getElementById('event_user');

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

});