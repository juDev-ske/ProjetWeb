/* Toggle affichage mot de passe */
var shownPw = false;

/**
 * Alterne la visibilité du mot de passe dans le champ de saisie
 * et met à jour l'icône de l'œil correspondante.
 */
function togglePw() {
  shownPw = !shownPw;

  var pw  = document.getElementById('pw');  // Le champ input
  var eye = document.getElementById('eye'); // l'icône)

  pw.type = shownPw ? 'text' : 'password';

  // Mise à jour de l'icône SVG 
  eye.innerHTML = shownPw
    ? // Code SVG pour l'icône "œil barré" (mot de passe visible)
      '<path d="M17.94 17.94A10 10 0 0 1 12 20c-7 0-11-8-11-8a18 18 0 0 1 5.06-5.94M9.9 4.24A9 9 0 0 1 12 4c7 0 11 8 11 8a18 18 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>'
    : // Code SVG pour l'icône "œil ouvert" (mot de passe masqué)
      '<circle cx="12" cy="12" r="3"></circle><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"></path>';
}