/* Notation par etoiles (detail entreprise) */
(function () {
  var picker = document.getElementById('starPicker');
  if (!picker) return;

  var input  = document.getElementById('noteInput');
  var label  = document.getElementById('starLabel');
  var stars  = picker.querySelectorAll('.star-js');
  var current = parseFloat(picker.dataset.value) || 0;

  function paint(val) {
    stars.forEach(function (star) {
      var full = parseFloat(star.dataset.full);
      var half = parseFloat(star.dataset.half);
      var icon = star.querySelector('i');
      icon.className = 'fa-solid fa-star';
      if (val >= full) {
        icon.className = 'fa-solid fa-star star-full';
      } else if (val >= half) {
        icon.className = 'fa-solid fa-star-half-stroke star-half';
      } else {
        icon.className = 'fa-regular fa-star star-empty';
      }
    });
  }

  function valueFromEvent(star, e) {
    var rect = star.getBoundingClientRect();
    var x    = e.clientX - rect.left;
    return x < rect.width / 2
      ? parseFloat(star.dataset.half)
      : parseFloat(star.dataset.full);
  }

  stars.forEach(function (star) {
    star.addEventListener('mousemove', function (e) {
      paint(valueFromEvent(star, e));
    });
    star.addEventListener('click', function (e) {
      var val = valueFromEvent(star, e);
      current = val;
      input.value = val;
      paint(val);
      label.textContent = 'Note selectionnee : ' + val + ' / 5';
    });
  });

  picker.addEventListener('mouseleave', function () {
    paint(current);
  });

  paint(current);
})();
