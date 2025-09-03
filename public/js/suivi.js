// Animation au chargement
document.addEventListener('DOMContentLoaded', function() {
    // Animation des cartes statistiques
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Interaction avec les boutons d'expansion
    const expandBtns = document.querySelectorAll('.expand-btn');
    expandBtns.forEach(btn => {
        const target = btn.getAttribute('data-bs-target');
        const collapse = document.querySelector(target);
        
        collapse.addEventListener('show.bs.collapse', function() {
            // collapse.collapse('toggle');
            console.log(target);
            btn.innerHTML = '<i class="bi bi-eye-slash-fill me-1"></i>';
        });
        
        collapse.addEventListener('hide.bs.collapse', function() {
            btn.innerHTML = '<i class="bi bi-eye-fill me-1"></i>';
        });
    });

    // Effet de survol sur les lignes du tableau
    const tableRows = document.querySelectorAll('tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.boxShadow = '0 4px 15px rgba(102, 126, 234, 0.15)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.boxShadow = 'none';
        });
    });


    
});
// Ouvrir le modal de création
function openCreateModal(indicateurId) {
    document.getElementById('create_indicateur_id').value = indicateurId;
    document.getElementById('createForm').reset();
    new bootstrap.Modal(document.getElementById('createModal'), {
        keyboard: false
    }).show();
}

// Ouvrir le modal d'édition
async function openEditModal(suiviId) {
    try {
        const response = await fetch(`/suivis/${suiviId}`);
        const suivi = await response.json();
        
        document.getElementById('edit_suivi_id').value = suivi.id;
        document.getElementById('edit_resultat_atteint').value = suivi.resultat_atteint;
        document.getElementById('edit_cout_effectif').value = suivi.cout_effectif;
        document.getElementById('edit_periode').value = suivi.periode;
        document.getElementById('edit_observation').value = suivi.observation || '';
        
        new bootstrap.Modal(document.getElementById('editModal')).show();
    } catch (error) {
        Swal.fire({
            title: "Erreur !",
            text: "Erreur lors du chargement des données.",
            icon: "error",
        });
        //alert('Erreur lors du chargement des données');
    }
}


// Gestion du formulaire de création
document.getElementById('createForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    console.log(...formData);
    try {
        const response = await fetch('/suivis', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
            },
            body: formData
        });

        const result = await response.json();
        
        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('createModal')).hide();
            Swal.fire({
                title: "Création !",
                text: "L'élément a été cré avec succès.",
                icon: "success",
                timer: 2000,
            });
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            Swal.fire({
                title: "Erreur !",
                text: "Une erreur est survenue lors de la creation.",
                icon: "error",
            });
        }
    } catch (error) {
        console.error('Erreur:', error);
        Swal.fire({
            title: "Erreur !",
            text: "Une erreur est survenue lors de la requête.",
            icon: "error",
        });
    }
});

// Gestion du formulaire d'édition
document.getElementById('editForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const suiviId = document.getElementById('edit_suivi_id').value;
    const formData = new FormData(this);
    
    try {
        const response = await fetch(`/suivis/${suiviId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-HTTP-Method-Override': 'PUT'
            },
            body: formData
        });

        const result = await response.json();
        
        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
            Swal.fire({
                title: "Modifié !",
                text: "L'élément a été modifié avec succès.",
                icon: "success",
                timer: 2000,
            });
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            Swal.fire({
                title: "Erreur !",
                text: "Une erreur est survenue lors de la modification.",
                icon: "error",
            });
            //alert('Erreur lors de la modification');
        }
    } catch (error) {
        Swal.fire({
            title: "Erreur !",
            text: "Une erreur est survenue pendant la requête.",
            icon: "error",
        });
        //alert('Erreur lors de la modification');
    }
});
