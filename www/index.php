<?php
session_start();
require 'vendor/autoload.php';
require 'src/auth.php';

use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\OffreController;
use App\Controllers\EntrepriseController;
use App\Controllers\CandidatureController;
use App\Controllers\WishlistController;
use App\Controllers\EtudiantController;
use App\Controllers\PiloteController;
use App\Controllers\DashboardController;
use App\Models\UserModel;

// --- Twig ---
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig   = new \Twig\Environment($loader, ['debug' => true]);
$twig->addGlobal('session', [
    'user_id'   => $_SESSION['user_id']   ?? null,
    'user_role' => $_SESSION['user_role'] ?? null,
    'user_name' => $_SESSION['user_name'] ?? null,
]);

// --- URL ---
$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$url = rtrim($url, '/');
if ($url === '') $url = '/';

$router = new Router($url);

// =============================================
// AUTHENTIFICATION
// =============================================
$router->get('/connexion', function () use ($twig) {
    echo (new AuthController($twig))->loginPage();
});

$router->post('/connexion', function () use ($twig) {
    (new AuthController($twig))->login();
});

$router->get('/deconnexion', function () use ($twig) {
    (new AuthController($twig))->logout();
});

// =============================================
// ACCUEIL
// =============================================
$router->get('/', function () use ($twig) {
    $controller = new OffreController($twig);
    echo $twig->render('accueil.html.twig', ['offres' => $controller->getLatestOffers()]);
});

// =============================================
// OFFRES
// =============================================
$router->get('/offres', function () use ($twig) {
    echo (new OffreController($twig))->index();
});

$router->get('/offre/:id', function ($id) use ($twig) {
    echo (new OffreController($twig))->show((int) $id);
});

$router->get('/creation-offre', function () use ($twig) {
    requireRole(['admin', 'pilote']);
    echo (new OffreController($twig))->createPage();
});

$router->post('/creation-offre', function () use ($twig) {
    requireRole(['admin', 'pilote']);
    (new OffreController($twig))->create();
});

$router->get('/offre/:id/modifier', function ($id) use ($twig) {
    requireRole(['admin', 'pilote']);
    echo (new OffreController($twig))->editPage((int) $id);
});

$router->post('/offre/:id/modifier', function ($id) use ($twig) {
    requireRole(['admin', 'pilote']);
    (new OffreController($twig))->edit((int) $id);
});

$router->get('/offre/:id/supprimer', function ($id) use ($twig) {
    requireRole(['admin', 'pilote']);
    (new OffreController($twig))->delete((int) $id);
});

// =============================================
// CANDIDATURES
// =============================================
$router->get('/mes-candidatures', function () use ($twig) {
    requireRole('etudiant');
    echo (new CandidatureController($twig))->index();
});

$router->get('/candidature-spontanee', function () use ($twig) {
    requireRole('etudiant');
    echo (new CandidatureController($twig))->spontaneous();
});

$router->post('/candidature-spontanee', function () use ($twig) {
    requireRole('etudiant');
    (new CandidatureController($twig))->spontaneous();
});

$router->get('/candidatures-pilote', function () use ($twig) {
    requireRole(['admin', 'pilote']);
    echo (new CandidatureController($twig))->piloteView();
});

$router->get('/postuler/:id', function ($id) use ($twig) {
    requireRole('etudiant');
    echo (new CandidatureController($twig))->applyPage((int) $id);
});

$router->post('/postuler/:id', function ($id) use ($twig) {
    requireRole('etudiant');
    (new CandidatureController($twig))->apply((int) $id);
});

// =============================================
// WISHLIST
// =============================================
$router->get('/wishlist', function () use ($twig) {
    requireRole('etudiant');
    echo (new WishlistController($twig))->index();
});

$router->get('/wishlist/ajouter/:id', function ($id) use ($twig) {
    requireRole('etudiant');
    (new WishlistController($twig))->add((int) $id);
});

$router->get('/wishlist/retirer/:id', function ($id) use ($twig) {
    requireRole('etudiant');
    (new WishlistController($twig))->remove((int) $id);
});

// =============================================
// DASHBOARD & STATISTIQUES
// =============================================
$router->get('/dashboard', function () use ($twig) {
    requireRole(['admin', 'pilote']);
    echo (new DashboardController($twig))->index();
});

$router->get('/statistiques', function () use ($twig) {
    requireRole(['admin', 'pilote']);
    echo (new DashboardController($twig))->statistiques();
});

// =============================================
// ENTREPRISES
// =============================================
$router->get('/entreprises', function () use ($twig) {
    requireRole(['admin', 'pilote']);
    echo (new EntrepriseController($twig))->index();
});

$router->get('/entreprise/:id', function ($id) use ($twig) {
    echo (new EntrepriseController($twig))->show((int) $id);
});

$router->get('/creation-entreprise', function () use ($twig) {
    requireRole(['admin', 'pilote']);
    echo (new EntrepriseController($twig))->createPage();
});

$router->post('/creation-entreprise', function () use ($twig) {
    requireRole(['admin', 'pilote']);
    (new EntrepriseController($twig))->create();
});

$router->get('/entreprise/:id/modifier', function ($id) use ($twig) {
    requireRole(['admin', 'pilote']);
    echo (new EntrepriseController($twig))->editPage((int) $id);
});

$router->post('/entreprise/:id/modifier', function ($id) use ($twig) {
    requireRole(['admin', 'pilote']);
    (new EntrepriseController($twig))->edit((int) $id);
});

$router->post('/entreprise/:id/evaluer', function ($id) use ($twig) {
    requireRole(['admin', 'pilote', 'etudiant']);
    (new EntrepriseController($twig))->rate((int) $id);
});

$router->get('/entreprise/:id/supprimer', function ($id) use ($twig) {
    requireRole(['admin', 'pilote']);
    (new EntrepriseController($twig))->delete((int) $id);
});

// =============================================
// ÉTUDIANTS
// =============================================
$router->get('/etudiants', function () use ($twig) {
    requireRole(['admin', 'pilote']);
    echo (new EtudiantController($twig))->index();
});

$router->get('/creation-etudiant', function () use ($twig) {
    requireRole(['admin', 'pilote']);
    echo (new EtudiantController($twig))->createPage();
});

$router->post('/creation-etudiant', function () use ($twig) {
    requireRole(['admin', 'pilote']);
    (new EtudiantController($twig))->create();
});

$router->get('/etudiant/:id/modifier', function ($id) use ($twig) {
    requireRole(['admin', 'pilote']);
    echo (new EtudiantController($twig))->editPage((int) $id);
});

$router->post('/etudiant/:id/modifier', function ($id) use ($twig) {
    requireRole(['admin', 'pilote']);
    (new EtudiantController($twig))->edit((int) $id);
});

$router->get('/etudiant/:id/supprimer', function ($id) use ($twig) {
    requireRole(['admin', 'pilote']);
    (new EtudiantController($twig))->delete((int) $id);
});

// =============================================
// PILOTES
// =============================================
$router->get('/pilotes', function () use ($twig) {
    requireRole('admin');
    echo (new PiloteController($twig))->index();
});

$router->get('/creation-pilote', function () use ($twig) {
    requireRole('admin');
    echo (new PiloteController($twig))->createPage();
});

$router->post('/creation-pilote', function () use ($twig) {
    requireRole('admin');
    (new PiloteController($twig))->create();
});

$router->get('/pilote/:id/modifier', function ($id) use ($twig) {
    requireRole('admin');
    echo (new PiloteController($twig))->editPage((int) $id);
});

$router->post('/pilote/:id/modifier', function ($id) use ($twig) {
    requireRole('admin');
    (new PiloteController($twig))->edit((int) $id);
});

$router->get('/pilote/:id/supprimer', function ($id) use ($twig) {
    requireRole('admin');
    (new PiloteController($twig))->delete((int) $id);
});

// =============================================
// PROMOTIONS
// =============================================
$router->get('/promotions', function () use ($twig) {
    requireRole(['admin', 'pilote']);
    echo (new PiloteController($twig))->promotions();
});

$router->get('/creation-promotion', function () use ($twig) {
    requireRole(['admin', 'pilote']);
    echo (new PiloteController($twig))->createPromotionPage();
});

$router->post('/creation-promotion', function () use ($twig) {
    requireRole(['admin', 'pilote']);
    (new PiloteController($twig))->createPromotion();
});

$router->get('/promotion/:id', function ($id) use ($twig) {
    requireRole(['admin', 'pilote']);
    echo (new PiloteController($twig))->promotionDetail((int) $id);
});

// =============================================
// PROFIL
// =============================================
$router->get('/profil', function () use ($twig) {
    requireLogin();
    $user = (new UserModel())->getUserById($_SESSION['user_id']);
    echo $twig->render('profil.html.twig', ['user' => [
        'prenom' => $user['first_name'],
        'nom'    => $user['last_name'],
        'email'  => $user['email'],
        'role'   => $user['role'],
    ]]);
});

// =============================================
// PAGES STATIQUES
// =============================================
$router->get('/a-propos', function () use ($twig) {
    echo $twig->render('a-propos.html.twig', ['equipe' => [
        ['nom' => 'Julien VOLTZ',      'role' => 'Chef de projet',          'couleur' => '#1800ad'],
        ['nom' => 'Raphaël LINARD',    'role' => 'Développeur Front-end',   'couleur' => '#00703c'],
        ['nom' => 'Esteban HUYGHE',    'role' => 'Développeur Back-end',    'couleur' => '#bd0000'],
        ['nom' => 'Bilal ALLOUCH',     'role' => 'Développeur Back-end',    'couleur' => '#0d7aa5'],
    ]]);
});

$router->get('/contact', function () use ($twig) {
    echo $twig->render('contact.html.twig');
});

$router->post('/contact', function () use ($twig) {
    echo $twig->render('contact.html.twig', ['success' => true]);
});

$router->get('/mentions-legales', function () use ($twig) {
    echo $twig->render('mentions-legales.html.twig');
});

// =============================================
// LANCEMENT
// =============================================
$router->run();
