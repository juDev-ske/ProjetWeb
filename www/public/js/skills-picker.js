/* Sélecteur de compétences (création / modification offre)
   Les données sont passées via data-all-skills et data-selected-skills
   sur l'élément #skills-container */
document.addEventListener('DOMContentLoaded', function () {
  // Vérification de la présence du conteneur de compétences sur la page
  var container = document.getElementById('skills-container');
  if (!container) return;

  // Récupération des éléments DOM nécessaires
  var skillsInput   = document.getElementById('skills-input');   // Champ de saisie texte
  var skillsOptions = document.getElementById('skills-options'); // Liste de suggestions (dropdown)
  var skillsTags    = document.getElementById('skills-tags');    // Zone d'affichage des badges (tags)
  var skillsHidden  = document.getElementById('skills-hidden');  // Champ caché pour l'envoi au serveur

  // Désérialisation des listes de compétences (toutes et déjà sélectionnées) depuis les attributs data
  var allSkills      = JSON.parse(container.dataset.allSkills || '[]');
  var selectedSkills = JSON.parse(container.dataset.selectedSkills || '[]');

  /**
   * Met à jour la valeur du champ caché avec les compétences sélectionnées (séparées par des virgules)
   * pour que le formulaire PHP puisse les recevoir via $_POST.
   */
  function updateHidden() {
    skillsHidden.value = selectedSkills.join(',');
  }

  /**
   * Ajoute une compétence à la sélection et crée visuellement le tag (badge).
   * @param {string} name - Le nom de la compétence à ajouter
   */
  function addTag(name) {
    // Évite les doublons
    if (selectedSkills.indexOf(name) !== -1) return;
    
    selectedSkills.push(name);
    updateHidden();

    // Création de l'élément HTML du tag avec un bouton de suppression
    var tag = document.createElement('span');
    tag.className = 'skill-tag';
    tag.innerHTML = name + ' <button type="button" data-value="' + name + '">&times;</button>';
    
    // Événement pour supprimer le tag au clic sur la croix
    tag.querySelector('button').addEventListener('click', function () {
      // Filtre le tableau pour retirer la compétence
      selectedSkills = selectedSkills.filter(function (s) { 
        return s !== this.dataset.value; 
      }.bind(this));
      
      updateHidden();
      tag.remove();
    });

    skillsTags.appendChild(tag);
    skillsInput.value = ''; // Vide l'input après ajout
    skillsOptions.style.display = 'none'; // Cache les suggestions
    skillsInput.focus();
  }

  /**
   * Filtre et affiche la liste des suggestions en fonction de la saisie utilisateur.
   * @param {string} filter - Le texte saisi dans l'input
   */
  function renderOptions(filter) {
    skillsOptions.innerHTML = ''; // Réinitialise la liste

    // Filtre les compétences qui correspondent à la saisie et qui ne sont pas déjà choisies
    var filtered = allSkills.filter(function (s) {
      return (filter === '' || s.toLowerCase().indexOf(filter.toLowerCase()) !== -1)
        && selectedSkills.indexOf(s) === -1;
    });

    // Génère les éléments de liste pour chaque compétence filtrée
    filtered.forEach(function (skill) {
      var div = document.createElement('div');
      div.className = 'select-option-search';
      div.textContent = skill;
      // 'mousedown' est utilisé à la place de 'click' pour s'exécuter avant le 'blur' de l'input
      div.addEventListener('mousedown', function (e) {
        e.preventDefault();
        addTag(skill);
      });
      skillsOptions.appendChild(div);
    });

    // Option pour ajouter une nouvelle compétence personnalisée si elle n'existe pas dans la liste
    var exactMatch = allSkills.some(function (s) { return s.toLowerCase() === filter.toLowerCase(); });
    if (filter.length > 0 && !exactMatch) {
      var div = document.createElement('div');
      div.className = 'select-option-search select-option-new';
      div.innerHTML = '<i class="fa-solid fa-plus"></i> Ajouter "<strong>' + filter + '</strong>"';
      div.addEventListener('mousedown', function (e) {
        e.preventDefault();
        addTag(filter);
      });
      skillsOptions.appendChild(div);
    }

    // Affiche ou cache le menu déroulant si des options sont disponibles
    skillsOptions.style.display = skillsOptions.children.length > 0 ? 'block' : 'none';
  }

  // --- INITIALISATION ---

  // Pré-remplissage des tags si des compétences sont déjà sélectionnées (ex: modification d'offre)
  selectedSkills.forEach(function (name) {
    var tag = document.createElement('span');
    tag.className = 'skill-tag';
    tag.innerHTML = name + ' <button type="button" data-value="' + name + '">&times;</button>';
    tag.querySelector('button').addEventListener('click', function () {
      selectedSkills = selectedSkills.filter(function (s) { return s !== this.dataset.value; }.bind(this));
      updateHidden();
      tag.remove();
    });
    skillsTags.appendChild(tag);
  });
  updateHidden();

  // Écouteurs d'événements sur le champ de saisie
  skillsInput.addEventListener('input', function () {
    renderOptions(this.value.trim());
  });

  skillsInput.addEventListener('focus', function () {
    renderOptions(this.value.trim());
  });

  // Ferme la liste de suggestions si on clique à l'extérieur du composant
  document.addEventListener('click', function (e) {
    if (!e.target.closest('.skills-container')) {
      skillsOptions.style.display = 'none';
    }
  });
});