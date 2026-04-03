/* Notation par étoiles (détail entreprise) */
(function () {
  // Récupération de l'élément parent qui contient les étoiles
  var picker = document.getElementById('starPicker');
  if (!picker) return;

  // Éléments liés au formulaire et à l'affichage
  var input  = document.getElementById('noteInput'); // Input caché pour envoyer la note en PHP
  var label  = document.getElementById('starLabel'); // Texte affichant la note sélectionnée
  var stars  = picker.querySelectorAll('.star-js');  // Liste des icônes d'étoiles
  // Note actuelle (soit celle déjà enregistrée, soit 0 par défaut)
  var current = parseFloat(picker.dataset.value) || 0;

  /**
   * Met à jour l'apparence visuelle des étoiles (vide, moitié, pleine)
   * @param {number} val - La valeur de la note à afficher (ex: 3.5)
   */
  function paint(val) {
    stars.forEach(function (star) {
      var full = parseFloat(star.dataset.full); // Valeur si l'étoile est pleine (ex: 4)
      var half = parseFloat(star.dataset.half); // Valeur si l'étoile est à moitié (ex: 3.5)
      var icon = star.querySelector('i');
      
      // Réinitialisation de la classe de base de l'icône
      icon.className = 'fa-solid fa-star';

      if (val >= full) {
        // L'étoile est totalement atteinte par la note
        icon.className = 'fa-solid fa-star star-full';
      } else if (val >= half) {
        // La note atteint au moins la moitié de cette étoile
        icon.className = 'fa-solid fa-star-half-stroke star-half';
      } else {
        // La note est inférieure à cette étoile : elle reste vide
        icon.className = 'fa-regular fa-star star-empty';
      }
    });
  }

  /**
   * Calcule la valeur (demi ou entière) en fonction de la position de la souris sur une étoile
   * @param {HTMLElement} star - L'élément étoile survolé
   * @param {MouseEvent} e - L'événement de la souris
   */
  function valueFromEvent(star, e) {
    var rect = star.getBoundingClientRect(); // Position de l'étoile sur l'écran
    var x    = e.clientX - rect.left;         // Position X de la souris dans l'étoile
    // Si la souris est dans la moitié gauche de l'étoile, on renvoie la valeur "half"
    return x < rect.width / 2
      ? parseFloat(star.dataset.half)
      : parseFloat(star.dataset.full);
  }

  // Ajout des écouteurs sur chaque étoile
  stars.forEach(function (star) {
    // Effet de survol (hover) : on prévisualise la note sans la valider
    star.addEventListener('mousemove', function (e) {
      paint(valueFromEvent(star, e));
    });

    // Clic : on enregistre définitivement la note
    star.addEventListener('click', function (e) {
      var val = valueFromEvent(star, e);
      current = val;       // Met à jour la mémoire de la note choisie
      input.value = val;   // Met à jour la valeur de l'input pour le formulaire
      paint(val);          // Fixe l'affichage visuel
      label.textContent = 'Note sélectionnée : ' + val + ' / 5';
    });
  });

  // Quand la souris quitte la zone de notation, on réaffiche la dernière note validée
  picker.addEventListener('mouseleave', function () {
    paint(current);
  });

  // Initialisation de l'affichage au chargement de la page
  paint(current);
})();