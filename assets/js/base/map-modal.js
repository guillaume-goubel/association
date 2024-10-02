document.addEventListener("DOMContentLoaded", (event) => {

    // OPEN MODAL
    document.querySelectorAll('.link-to-map').forEach(function(link) {
        link.addEventListener('click', function(e) {
            // Empêche le comportement par défaut
            e.preventDefault();
    
            // Récupère le lieu de rendez-vous depuis l'attribut data-rdv
            var lieuRdv = link.getAttribute('data-rdv');
    
            // Sélectionne le titre de la modale et le met à jour avec le lieu de rendez-vous
            var modalTitle = document.getElementById('modalTitle');
            modalTitle.textContent = lieuRdv;
    
            // Ouvre la modale
            const modal = new bootstrap.Modal(document.getElementById('mapModal'));
            modal.show();
        });
    });

    // Événement pour réinitialiser le contenu ou autre chose lorsque la modale est fermée
    document.getElementById('mapModal').addEventListener('hidden.bs.modal', function () {
        // Supprimez la classe 'modal-backdrop fade' si elle est présente
        var backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.classList.remove('fade');
            backdrop.remove(); // Supprime le backdrop complètement
        }
        // Supprimer la classe modal-open manuellement
        document.body.classList.remove('modal-open');
        // Réinitialiser le style overflow et padding-right
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    });

});