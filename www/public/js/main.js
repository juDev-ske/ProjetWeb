/* ==============================================
   CesiTonJob — main.js
   ============================================== */

document.addEventListener('DOMContentLoaded', function () {

  // ── Menu burger mobile ──────────────────────────────────────
  var burger     = document.getElementById('navBurger');
  var mobileMenu = document.getElementById('navMobile');
  var closeBtn   = document.getElementById('navMobileClose');
  var backdrop   = document.getElementById('navMobileBackdrop');

  if (burger && mobileMenu) {

    function openMenu() {
      mobileMenu.classList.add('is-open');
      document.body.style.overflow = 'hidden';
      burger.setAttribute('aria-expanded', 'true');
      burger.querySelector('i').className = 'fa-solid fa-xmark';
    }

    function closeMenu() {
      mobileMenu.classList.remove('is-open');
      document.body.style.overflow = '';
      burger.setAttribute('aria-expanded', 'false');
      burger.querySelector('i').className = 'fa-solid fa-bars';
    }

    burger.addEventListener('click', function () {
      mobileMenu.classList.contains('is-open') ? closeMenu() : openMenu();
    });

    if (closeBtn) closeBtn.addEventListener('click', closeMenu);
    if (backdrop) backdrop.addEventListener('click', closeMenu);

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closeMenu();
    });

    mobileMenu.querySelectorAll('.nav-mobile-item').forEach(function (link) {
      link.addEventListener('click', closeMenu);
    });
  }

  // ── Toggle filtres offres (mobile) ──────────────────────────
  var filterToggle = document.getElementById('filterToggle');
  var filterBody   = document.getElementById('filterBody');

  if (filterToggle && filterBody) {
    filterToggle.addEventListener('click', function () {
      var isOpen = filterBody.classList.toggle('is-open');
      filterToggle.classList.toggle('is-open', isOpen);
      filterToggle.setAttribute('aria-expanded', String(isOpen));
    });
  }

});
