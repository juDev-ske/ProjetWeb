// Burger menu
const burger = document.querySelector('.nav-burger');
const navLinks = document.querySelector('.nav-links');

if (burger && navLinks) {
    burger.addEventListener('click', () => {
        navLinks.classList.toggle('nav-open');
        const isOpen = navLinks.classList.contains('nav-open');
        burger.setAttribute('aria-expanded', isOpen);
    });

    // Ferme le menu si on clique en dehors
    document.addEventListener('click', (e) => {
        if (!burger.contains(e.target) && !navLinks.contains(e.target)) {
            navLinks.classList.remove('nav-open');
            burger.setAttribute('aria-expanded', false);
        }
    });
}
