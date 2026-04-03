document.addEventListener('DOMContentLoaded', function () {

  // ── Menu burger mobile ──────────────────────────────────────
  // Récupération des éléments nécessaires pour le menu latéral
  var burger     = document.getElementById('navBurger'); // Le bouton menu
  var mobileMenu = document.getElementById('navMobile'); // Le conteneur du menu
  var closeBtn   = document.getElementById('navMobileClose'); // Bouton de fermeture (X)
  var backdrop   = document.getElementById('navMobileBackdrop'); // Fond sombre cliquable

  // Si les éléments principaux existent sur la page
  if (burger && mobileMenu) {

    /**
     * Ouvre le menu mobile : ajoute la classe CSS, bloque le scroll du corps
     * et met à jour l'icône ainsi que l'accessibilité (Aria).
     */
    function openMenu() {
      mobileMenu.classList.add('is-open');
      document.body.style.overflow = 'hidden'; // Empêche de scroller la page derrière
      burger.setAttribute('aria-expanded', 'true');
      burger.querySelector('i').className = 'fa-solid fa-xmark'; // Change l'icône en croix
    }

    /**
     * Ferme le menu mobile : retire les classes et rétablit le scroll.
     */
    function closeMenu() {
      mobileMenu.classList.remove('is-open');
      document.body.style.overflow = ''; // Réautorise le scroll
      burger.setAttribute('aria-expanded', 'false');
      burger.querySelector('i').className = 'fa-solid fa-bars'; // Remet l'icône burger
    }

    // Toggle (bascule) : ouvre si fermé, ferme si ouvert au clic sur le burger
    burger.addEventListener('click', function () {
      mobileMenu.classList.contains('is-open') ? closeMenu() : openMenu();
    });

    // Fermeture lors du clic sur le bouton "Fermer", sur le fond sombre ou via la touche Échap
    if (closeBtn) closeBtn.addEventListener('click', closeMenu);
    if (backdrop) backdrop.addEventListener('click', closeMenu);

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closeMenu();
    });

    // Ferme automatiquement le menu quand on clique sur un lien interne au menu
    mobileMenu.querySelectorAll('.nav-mobile-item').forEach(function (link) {
      link.addEventListener('click', closeMenu);
    });
  }

  // ── Validation HTML5 — messages en français ─────────────────
  // Personnalise les bulles d'erreur par défaut du navigateur
  document.querySelectorAll('form:not([novalidate])').forEach(function (form) {
    form.querySelectorAll('input, textarea, select').forEach(function (field) {
      
      // Se déclenche au moment où l'utilisateur essaie de valider un champ invalide
      field.addEventListener('invalid', function () {
        if (field.validity.valueMissing) {
          field.setCustomValidity('Ce champ est obligatoire.');
        } else if (field.validity.typeMismatch && field.type === 'email') {
          field.setCustomValidity('Veuillez saisir une adresse email valide.');
        } else if (field.validity.tooShort) {
          field.setCustomValidity('Minimum ' + field.minLength + ' caractères requis.');
        } else if (field.validity.tooLong) {
          field.setCustomValidity('Maximum ' + field.maxLength + ' caractères autorisés.');
        } else if (field.validity.patternMismatch) {
          field.setCustomValidity('Le format saisi n\'est pas valide.');
        } else if (field.validity.rangeUnderflow || field.validity.rangeOverflow) {
          field.setCustomValidity('La valeur doit être entre ' + field.min + ' et ' + field.max + '.');
        } else {
          field.setCustomValidity('');
        }
      });

      // Réinitialise le message d'erreur dès que l'utilisateur recommence à taper
      field.addEventListener('input', function () {
        field.setCustomValidity('');
      });
    });
  });

  // ── Toggle filtres offres (mobile) ──────────────────────────
  // Gère l'affichage/masquage de la barre de filtres de recherche sur petit écran
  var filterToggle = document.getElementById('filterToggle');
  var filterBody   = document.getElementById('filterBody');

  if (filterToggle && filterBody) {
    filterToggle.addEventListener('click', function () {
      // Alterne la classe 'is-open' sur le corps des filtres
      var isOpen = filterBody.classList.toggle('is-open');
      // Alterne aussi sur le bouton pour changer son aspect (ex: rotation flèche)
      filterToggle.classList.toggle('is-open', isOpen);
      filterToggle.setAttribute('aria-expanded', String(isOpen));
    });
  }

});