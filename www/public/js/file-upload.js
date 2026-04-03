// Attend que le contenu HTML soit complètement chargé avant d'exécuter le script
document.addEventListener('DOMContentLoaded', function () {
  
  // Boucle sur un tableau de configurations [id_input, id_zone_cliquable, id_label_texte]
  // Permet de traiter le CV et la Lettre de Motivation avec le même code
  [['cv', 'cv-zone', 'cv-label'], ['lm', 'lm-zone', 'lm-label']].forEach(function (ids) {
    
    // Récupération des trois éléments HTML nécessaires pour chaque zone
    var input  = document.getElementById(ids[0]); // Le champ <input type="file"> (caché)
    var zoneEl = document.getElementById(ids[1]); // La zone visuelle (le rectangle d'upload)
    var lblEl  = document.getElementById(ids[2]); // Le texte affiché dans la zone

    // Sécurité : si l'un des éléments n'existe pas sur la page actuelle, on passe au suivant
    if (!input || !zoneEl || !lblEl) return;

    // Rend la zone visuelle cliquable : cliquer sur le rectangle ouvre la fenêtre de sélection de fichier
    zoneEl.addEventListener('click', function () { 
      input.click(); 
    });

    // Écoute le changement d'état de l'input (quand l'utilisateur a choisi un fichier)
    input.addEventListener('change', function () {
      var file = this.files[0]; // Récupère le premier fichier sélectionné
      
      if (file) {
        // Si un fichier est présent : affiche son nom et ajoute une classe CSS de succès
        lblEl.textContent = file.name;
        zoneEl.classList.add('has-file');
      } else {
        // Si aucun fichier (annulation) : remet le texte par défaut et retire la classe CSS
        lblEl.textContent = 'Choisir un fichier PDF ou Word';
        zoneEl.classList.remove('has-file');
      }
    });
  });
});