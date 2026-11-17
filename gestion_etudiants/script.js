document.addEventListener('DOMContentLoaded', () => {
    // Confirmation avant la suppression d'un étudiant.
    document.querySelectorAll('a[data-confirm]').forEach((lien) => {
        lien.addEventListener('click', (event) => {
            if (!confirm(lien.dataset.confirm || 'Confirmer cette action ?')) {
                event.preventDefault();
            }
        });
    });

    // Filtrage instantané des lignes déjà affichées dans le tableau.
    const champRecherche = document.querySelector('#q');
    const tableau = document.querySelector('#students-table');

    if (champRecherche && tableau) {
        champRecherche.addEventListener('input', () => {
            const recherche = champRecherche.value.toLocaleLowerCase().trim();
            tableau.querySelectorAll('tbody tr').forEach((ligne) => {
                const texte = ligne.textContent.toLocaleLowerCase();
                ligne.hidden = recherche !== '' && !texte.includes(recherche);
            });
        });
    }
});
