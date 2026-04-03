/* Carrousel de la page statistiques */
(function () {
  // Récupération des éléments du DOM
  var track = document.getElementById('statsCarouselTrack'); // Le conteneur qui glisse (la "piste")
  var prev  = document.getElementById('statsCarouselPrev');  // Bouton précédent
  var next  = document.getElementById('statsCarouselNext');  // Bouton suivant
  var dots  = document.getElementById('statsCarouselDots');  // Conteneur des points de navigation
  
  // Sécurité : si les éléments ne sont pas sur la page, on arrête l'exécution
  if (!track || !prev || !next) return;

  var slides   = track.querySelectorAll('.stats-carousel-slide'); // Liste des diapositives
  var total    = slides.length; // Nombre total de slides
  var current  = 0;             // Index de la slide actuellement affichée
  var timer    = null;          // Stocke l'identifiant du défilement automatique

  /**
   * Génère dynamiquement les petits points de navigation en bas du carrousel.
   * Ajoute la classe 'active' au point correspondant à la slide actuelle.
   */
  function construireDots() {
    dots.innerHTML = ''; // Vide les points existants
    for (var i = 0; i < total; i++) {
      var dot = document.createElement('button');
      dot.className = 'stats-carousel-dot' + (i === current ? ' active' : '');
      dot.setAttribute('aria-label', 'Slide ' + (i + 1));
      dot.dataset.index = i; // Stocke l'index pour savoir sur lequel on clique
      dots.appendChild(dot);
    }
  }

  /**
   * Déplace le carrousel vers une slide précise.
   * @param {number} index - L'index de la destination (0, 1, 2...)
   */
  function allerA(index) {
    // Force l'index à rester entre 0 et le maximum possible
    current = Math.max(0, Math.min(index, total - 1));
    
    // Déplacement horizontal de la "piste" via CSS (100% par slide)
    track.style.transform = 'translateX(-' + (current * 100) + '%)';
    
    mettreAJourBoutons();
    construireDots();
  }

  /**
   * Grise et désactive les flèches si on est au début ou à la fin.
   */
  function mettreAJourBoutons() {
    // Bouton précédent : inactif si on est sur la première slide
    prev.style.opacity = current <= 0 ? '0.3' : '1';
    prev.style.pointerEvents = current <= 0 ? 'none' : 'auto';
    
    // Bouton suivant : inactif si on est sur la dernière slide
    next.style.opacity = current >= total - 1 ? '0.3' : '1';
    next.style.pointerEvents = current >= total - 1 ? 'none' : 'auto';
  }

  /**
   * Lance le défilement automatique toutes le 5 secondes.
   */
  function lancerAuto() {
    arreterAuto(); // On nettoie l'ancien timer avant d'en créer un nouveau
    timer = setInterval(function () {
      // Si on est à la fin, on revient à 0, sinon on passe à la suivante
      allerA(current >= total - 1 ? 0 : current + 1);
    }, 5000); // 5000ms = 5 secondes
  }

  /**
   * Arrête le défilement automatique (utile lors d'une interaction manuelle).
   */
  function arreterAuto() {
    if (timer) clearInterval(timer);
  }

  // --- Événements ---

  // Clic flèche gauche
  prev.addEventListener('click', function () { 
    arreterAuto(); 
    allerA(current - 1); 
    lancerAuto(); 
  });

  // Clic flèche droite
  next.addEventListener('click', function () { 
    arreterAuto(); 
    allerA(current + 1); 
    lancerAuto(); 
  });

  // Délégation d'événement sur les points de navigation (dots)
  dots.addEventListener('click', function (e) {
    var btn = e.target.closest('.stats-carousel-dot');
    if (btn) { 
      arreterAuto(); 
      allerA(parseInt(btn.dataset.index)); 
      lancerAuto(); 
    }
  });

  // --- Initialisation ---
  allerA(0);    // Affiche la première slide au chargement
  lancerAuto(); // Démarre l'autoplay
})();