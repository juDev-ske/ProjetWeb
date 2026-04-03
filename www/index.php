<?php
// Sécurisation stricte des cookies de session
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', 1);
ini_set('session.use_only_cookies', 1);
session_start();

// Headers de sécurité contre les injections et le détournement de contenu
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

$baseDir = defined('BASE_DIR') ? BASE_DIR : __DIR__;
require $baseDir . '/vendor/autoload.php';
require $baseDir . '/src/auth.php';

use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\OffreController;
use App\Controllers\EntrepriseController;
use App\Controllers\CandidatureController;
use App\Controllers\WishlistController;
use App\Controllers\EtudiantController;
use App\Controllers\PiloteController;
use App\Controllers\DashboardController;
use App\Core\Csrf;
use App\Models\UserModel;
use App\Models\WishlistModel;

// Initialisation de Twig et injection des données de session globales
$loader = new \Twig\Loader\FilesystemLoader($baseDir . '/templates');
$twig   = new \Twig\Environment($loader, ['debug' => true]);
$twig->addGlobal('session', [
    'user_id'   => $_SESSION['user_id']   ?? null,
    'user_role' => $_SESSION['user_role'] ?? null,
    'user_name' => $_SESSION['user_name'] ?? null,
]);
$twig->addGlobal('csrf_token', Csrf::generate());

// Vérification du jeton CSRF sur toutes les requêtes de modification (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !Csrf::verify()) {
    http_response_code(403);
    echo $twig->render('erreur.html.twig', ['code' => 403, 'message' => 'Jeton CSRF invalide. Veuillez réessayer.']);
    exit;
}

// Nettoyage de l'URL pour le routage
$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$url = rtrim($url, '/');
if ($url === '') $url = '/';

$router = new Router($url);

$router->get('/connexion', function () use ($twig) {
    echo (new AuthController($twig))->loginPage();
});

$router->post('/connexion', function () use ($twig) {
    (new AuthController($twig))->login();
});

$router->get('/deconnexion', function () use ($twig) {
    (new AuthController($twig))->logout();
});

$router->get('/', function () use ($twig) {
    $controller = new OffreController($twig);
    $wishlistIds = [];
    if (($_SESSION['user_role'] ?? '') === 'etudiant') {
        $wishlistIds = (new WishlistModel())->getWishlistOfferIds($_SESSION['user_id'] ?? 0);
    }
    echo $twig->render('accueil.html.twig', [
        'offres'       => $controller->getLatestOffers(),
        'wishlist_ids' => $wishlistIds,
    ]);
});

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
    echo (new CandidatureController($twig))->spontaneous();
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

$router->get('/dashboard', function () use ($twig) {
    requireRole(['admin', 'pilote']);
    echo (new DashboardController($twig))->index();
});

$router->get('/statistiques', function () use ($twig) {
    requireRole(['admin', 'pilote']);
    echo (new DashboardController($twig))->statistiques();
});

$router->get('/entreprises', function () use ($twig) {
    requireRole(['admin', 'pilote']);
    echo (new EntrepriseController($twig))->index();
});

$router->get('/entreprise/:id', function ($id) use ($twig) {
    requireRole(['admin', 'pilote', 'etudiant']);
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
    requireRole(['admin', 'pilote']);
    (new EntrepriseController($twig))->rate((int) $id);
});

$router->get('/entreprise/:id/supprimer', function ($id) use ($twig) {
    requireRole(['admin', 'pilote']);
    (new EntrepriseController($twig))->delete((int) $id);
});

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

// Déclenchement de la route correspondante
$router->run();