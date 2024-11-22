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

});