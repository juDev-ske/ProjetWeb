/* Carrousel de la page statistiques */
(function () {
  var track = document.getElementById('statsCarouselTrack');
  var prev  = document.getElementById('statsCarouselPrev');
  var next  = document.getElementById('statsCarouselNext');
  var dots  = document.getElementById('statsCarouselDots');
  if (!track || !prev || !next) return;

  var slides  = track.querySelectorAll('.stats-carousel-slide');
  var total   = slides.length;
  var current = 0;
  var timer   = null;

  function construireDots() {
    dots.innerHTML = '';
    for (var i = 0; i < total; i++) {
      var dot = document.createElement('button');
      dot.className = 'stats-carousel-dot' + (i === current ? ' active' : '');
      dot.setAttribute('aria-label', 'Slide ' + (i + 1));
      dot.dataset.index = i;
      dots.appendChild(dot);
    }
  }

  function allerA(index) {
    current = Math.max(0, Math.min(index, total - 1));
    track.style.transform = 'translateX(-' + (current * 100) + '%)';
    mettreAJourBoutons();
    construireDots();
  }

  function mettreAJourBoutons() {
    prev.style.opacity = current <= 0 ? '0.3' : '1';
    prev.style.pointerEvents = current <= 0 ? 'none' : 'auto';
    next.style.opacity = current >= total - 1 ? '0.3' : '1';
    next.style.pointerEvents = current >= total - 1 ? 'none' : 'auto';
  }

  function lancerAuto() {
    arreterAuto();
    timer = setInterval(function () {
      allerA(current >= total - 1 ? 0 : current + 1);
    }, 5000);
  }

  function arreterAuto() {
    if (timer) clearInterval(timer);
  }

  prev.addEventListener('click', function () { arreterAuto(); allerA(current - 1); lancerAuto(); });
  next.addEventListener('click', function () { arreterAuto(); allerA(current + 1); lancerAuto(); });

  dots.addEventListener('click', function (e) {
    var btn = e.target.closest('.stats-carousel-dot');
    if (btn) { arreterAuto(); allerA(parseInt(btn.dataset.index)); lancerAuto(); }
  });

  allerA(0);
  lancerAuto();
})();
