/* Zones d'upload de fichier (CV / LM) */
document.addEventListener('DOMContentLoaded', function () {
  [['cv', 'cv-zone', 'cv-label'], ['lm', 'lm-zone', 'lm-label']].forEach(function (ids) {
    var input  = document.getElementById(ids[0]);
    var zoneEl = document.getElementById(ids[1]);
    var lblEl  = document.getElementById(ids[2]);

    if (!input || !zoneEl || !lblEl) return;

    zoneEl.addEventListener('click', function () { input.click(); });

    input.addEventListener('change', function () {
      var file = this.files[0];
      if (file) {
        lblEl.textContent = file.name;
        zoneEl.classList.add('has-file');
      } else {
        lblEl.textContent = 'Choisir un fichier PDF ou Word';
        zoneEl.classList.remove('has-file');
      }
    });
  });
});
