/* Toggle affichage mot de passe */
var shownPw = false;

function togglePw() {
  shownPw = !shownPw;
  var pw  = document.getElementById('pw');
  var eye = document.getElementById('eye');

  pw.type = shownPw ? 'text' : 'password';
  eye.innerHTML = shownPw
    ? '<path d="M17.94 17.94A10 10 0 0 1 12 20c-7 0-11-8-11-8a18 18 0 0 1 5.06-5.94M9.9 4.24A9 9 0 0 1 12 4c7 0 11 8 11 8a18 18 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>'
    : '<circle cx="12" cy="12" r="3"></circle><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"></path>';
}
