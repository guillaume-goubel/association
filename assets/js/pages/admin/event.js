import '../../../styles/pages/admin/event.css';

document.addEventListener("DOMContentLoaded", (event) => {

    // GET activity message by activity change ------------------------------------------------------------------------------
    const activityChoiceElmt = document.getElementById('event_activity');
    const activityMessageChoiceElmt = document.getElementById('event_activityMessage');

    if (activityChoiceElmt && activityMessageChoiceElmt) {     
        activityChoiceElmt.addEventListener('change', function(e) {
            
            e.preventDefault(); 
            
            const url = document.getElementById('getActivityMessageByActivityPARAM').getAttribute('data-path');
            const activityId = activityChoiceElmt.value;  
            
            let data = new FormData();
            data.append('activityId', activityId); 

            fetch(url, {
                method: 'POST',
                body: data,
            })
            .then(response => response.json())  
            .then(data => {
                
                // Vider le select avant de rajouter les nouvelles options
                activityMessageChoiceElmt.innerHTML = '';

                const nullOption = document.createElement('option');
                nullOption.value = ''; // La valeur de l'option "all"
                nullOption.textContent = ''; // Texte visible pour l'option
                activityMessageChoiceElmt.appendChild(nullOption); // Ajouter l'option au select
                
                // Parcourt les activités renvoyées et ajoute une option pour chaque activité
                data.activityMessagesArray.forEach(message => {
                    const option = document.createElement('option');
                    option.value = message.id; // Utilise l'ID de l'activité comme valeur
                    option.textContent = message.name; // Affiche le nom de l'activité comme texte
                    activityMessageChoiceElmt.appendChild(option); // Ajoute l'option au select
                });

            })
            .catch(error => {
                console.error('Erreur lors de la requête AJAX :', error);
            });
        });
    }

    // GET activity content by activity message change ------------------------------------------------------------------------------
    const activityMessageContentElmt = document.getElementById('activityMessageContent');
    const activityMessageContentContainer = document.getElementById('activityMessageContentContainer');

    if (activityMessageChoiceElmt && activityMessageContentElmt) {     
        activityMessageChoiceElmt.addEventListener('change', function(e) {
            
            e.preventDefault(); 
            activityMessageContentElmt.innerHTML = '';
            
            const url = document.getElementById('getActivitycontentByActivityMessagePARAM').getAttribute('data-path');
            const activityMessageId = activityMessageChoiceElmt.value;  

            if (activityMessageId != '') {
                
                let data = new FormData();
                data.append('activityMessageId', activityMessageId); 
    
                fetch(url, {
                    method: 'POST',
                    body: data,
                })
                .then(response => response.json())  
                .then(data => {
                    
                    // Parcourt les activités renvoyées et ajoute une option pour chaque activité
                    data.activityMessagesArray.forEach(message => {
                        // Remplace les espaces par des espaces insécables
                        const formattedText = message.description.replace(/ /g, '&nbsp;'); 
                        activityMessageContentElmt.innerHTML += formattedText + '<br>'; // Ajoute un saut de ligne
                    });
    
                    activityMessageContentContainer.disabled = true;
                    activityMessageContentContainer.classList.remove('d-none');
    
                })
                .catch(error => {
                    console.error('Erreur lors de la requête AJAX :', error);
                });

            }else{
                activityMessageContentContainer.classList.add('d-none');
            }
            

        });
    }

});