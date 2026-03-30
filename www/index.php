<?php
session_start();
require 'vendor/autoload.php';
require 'src/auth.php';

use App\Controllers\AuthController;
use App\Controllers\OffreController;
use App\Controllers\EntrepriseController;
use App\Controllers\CandidatureController;
use App\Controllers\WishlistController;
use App\Controllers\EtudiantController;
use App\Controllers\PiloteController;
use App\Controllers\DashboardController;
use App\Models\UserModel;

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig   = new \Twig\Environment($loader, ['debug' => true]);

$twig->addGlobal('session', [
    'user_id'   => $_SESSION['user_id']   ?? null,
    'user_role' => $_SESSION['user_role'] ?? null,
    'user_name' => $_SESSION['user_name'] ?? null,
]);

$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri    = rtrim($uri, '/');
if ($uri === '') $uri = '/';
$method = $_SERVER['REQUEST_METHOD'];

// =============================================
// ROUTEUR
// =============================================

// --- Authentification ---
if ($uri === '/connexion' && $method === 'GET') {
    $controller = new AuthController($twig);
    echo $controller->loginPage();

} elseif ($uri === '/connexion' && $method === 'POST') {
    $controller = new AuthController($twig);
    $controller->login();

} elseif ($uri === '/deconnexion') {
    $controller = new AuthController($twig);
    $controller->logout();

// --- Accueil ---
} elseif ($uri === '/') {
    $controller = new OffreController($twig);
    echo $twig->render('accueil.html.twig', ['offres' => $controller->getLatestOffers()]);

// --- Offres ---
} elseif ($uri === '/offres' && $method === 'GET') {
    $controller = new OffreController($twig);
    echo $controller->index();

} elseif (preg_match('#^/offre/(\d+)$#', $uri, $m) && $method === 'GET') {
    $controller = new OffreController($twig);
    echo $controller->show((int) $m[1]);

} elseif ($uri === '/creation-offre' && $method === 'GET') {
    requireRole(['admin', 'pilote']);
    $controller = new OffreController($twig);
    echo $controller->createPage();

} elseif ($uri === '/creation-offre' && $method === 'POST') {
    requireRole(['admin', 'pilote']);
    $controller = new OffreController($twig);
    $controller->create();

} elseif (preg_match('#^/offre/(\d+)/modifier$#', $uri, $m) && $method === 'GET') {
    requireRole(['admin', 'pilote']);
    $controller = new OffreController($twig);
    echo $controller->editPage((int) $m[1]);

} elseif (preg_match('#^/offre/(\d+)/modifier$#', $uri, $m) && $method === 'POST') {
    requireRole(['admin', 'pilote']);
    $controller = new OffreController($twig);
    $controller->edit((int) $m[1]);

} elseif (preg_match('#^/offre/(\d+)/supprimer$#', $uri, $m)) {
    requireRole(['admin', 'pilote']);
    $controller = new OffreController($twig);
    $controller->delete((int) $m[1]);

// --- Candidatures ---
} elseif ($uri === '/mes-candidatures') {
    requireRole('etudiant');
    $controller = new CandidatureController($twig);
    echo $controller->index();

} elseif ($uri === '/candidatures-pilote') {
    requireRole(['admin', 'pilote']);
    $controller = new CandidatureController($twig);
    echo $controller->piloteView();

} elseif (preg_match('#^/postuler/(\d+)$#', $uri, $m) && $method === 'GET') {
    requireRole('etudiant');
    $controller = new CandidatureController($twig);
    echo $controller->applyPage((int) $m[1]);

} elseif (preg_match('#^/postuler/(\d+)$#', $uri, $m) && $method === 'POST') {
    requireRole('etudiant');
    $controller = new CandidatureController($twig);
    $controller->apply((int) $m[1]);

} elseif ($uri === '/candidature-spontanee') {
    $controller = new CandidatureController($twig);
    echo $controller->spontaneous();

// --- Wishlist ---
} elseif ($uri === '/wishlist') {
    requireRole('etudiant');
    $controller = new WishlistController($twig);
    echo $controller->index();

} elseif (preg_match('#^/wishlist/ajouter/(\d+)$#', $uri, $m)) {
    requireRole('etudiant');
    $controller = new WishlistController($twig);
    $controller->add((int) $m[1]);

} elseif (preg_match('#^/wishlist/retirer/(\d+)$#', $uri, $m)) {
    requireRole('etudiant');
    $controller = new WishlistController($twig);
    $controller->remove((int) $m[1]);

// --- Dashboard & Statistiques ---
} elseif ($uri === '/dashboard') {
    requireRole(['admin', 'pilote']);
    $controller = new DashboardController($twig);
    echo $controller->index();

} elseif ($uri === '/statistiques') {
    requireRole(['admin', 'pilote']);
    $controller = new DashboardController($twig);
    echo $controller->statistiques();

// --- Entreprises ---
} elseif ($uri === '/entreprises' && $method === 'GET') {
    requireRole(['admin', 'pilote']);
    $controller = new EntrepriseController($twig);
    echo $controller->index();

} elseif (preg_match('#^/entreprise/(\d+)$#', $uri, $m) && $method === 'GET') {
    requireRole(['admin', 'pilote']);
    $controller = new EntrepriseController($twig);
    echo $controller->show((int) $m[1]);

} elseif ($uri === '/creation-entreprise' && $method === 'GET') {
    requireRole(['admin', 'pilote']);
    $controller = new EntrepriseController($twig);
    echo $controller->createPage();

} elseif ($uri === '/creation-entreprise' && $method === 'POST') {
    requireRole(['admin', 'pilote']);
    $controller = new EntrepriseController($twig);
    $controller->create();

} elseif (preg_match('#^/entreprise/(\d+)/modifier$#', $uri, $m) && $method === 'GET') {
    requireRole(['admin', 'pilote']);
    $controller = new EntrepriseController($twig);
    echo $controller->editPage((int) $m[1]);

} elseif (preg_match('#^/entreprise/(\d+)/modifier$#', $uri, $m) && $method === 'POST') {
    requireRole(['admin', 'pilote']);
    $controller = new EntrepriseController($twig);
    $controller->edit((int) $m[1]);

} elseif (preg_match('#^/entreprise/(\d+)/evaluer$#', $uri, $m) && $method === 'POST') {
    requireRole(['admin', 'pilote', 'etudiant']);
    $controller = new EntrepriseController($twig);
    $controller->rate((int) $m[1]);

} elseif (preg_match('#^/entreprise/(\d+)/supprimer$#', $uri, $m)) {
    requireRole(['admin', 'pilote']);
    $controller = new EntrepriseController($twig);
    $controller->delete((int) $m[1]);

// --- Étudiants ---
} elseif ($uri === '/etudiants' && $method === 'GET') {
    requireRole(['admin', 'pilote']);
    $controller = new EtudiantController($twig);
    echo $controller->index();

} elseif ($uri === '/creation-etudiant' && $method === 'GET') {
    requireRole(['admin', 'pilote']);
    $controller = new EtudiantController($twig);
    echo $controller->createPage();

} elseif ($uri === '/creation-etudiant' && $method === 'POST') {
    requireRole(['admin', 'pilote']);
    $controller = new EtudiantController($twig);
    $controller->create();

} elseif (preg_match('#^/etudiant/(\d+)/modifier$#', $uri, $m) && $method === 'GET') {
    requireRole(['admin', 'pilote']);
    $controller = new EtudiantController($twig);
    echo $controller->editPage((int) $m[1]);

} elseif (preg_match('#^/etudiant/(\d+)/modifier$#', $uri, $m) && $method === 'POST') {
    requireRole(['admin', 'pilote']);
    $controller = new EtudiantController($twig);
    $controller->edit((int) $m[1]);

} elseif (preg_match('#^/etudiant/(\d+)/supprimer$#', $uri, $m)) {
    requireRole(['admin', 'pilote']);
    $controller = new EtudiantController($twig);
    $controller->delete((int) $m[1]);

// --- Pilotes ---
} elseif ($uri === '/pilotes' && $method === 'GET') {
    requireRole('admin');
    $controller = new PiloteController($twig);
    echo $controller->index();

} elseif ($uri === '/creation-pilote' && $method === 'GET') {
    requireRole('admin');
    $controller = new PiloteController($twig);
    echo $controller->createPage();

} elseif ($uri === '/creation-pilote' && $method === 'POST') {
    requireRole('admin');
    $controller = new PiloteController($twig);
    $controller->create();

} elseif (preg_match('#^/pilote/(\d+)/modifier$#', $uri, $m) && $method === 'GET') {
    requireRole('admin');
    $controller = new PiloteController($twig);
    echo $controller->editPage((int) $m[1]);

} elseif (preg_match('#^/pilote/(\d+)/modifier$#', $uri, $m) && $method === 'POST') {
    requireRole('admin');
    $controller = new PiloteController($twig);
    $controller->edit((int) $m[1]);

} elseif (preg_match('#^/pilote/(\d+)/supprimer$#', $uri, $m)) {
    requireRole('admin');
    $controller = new PiloteController($twig);
    $controller->delete((int) $m[1]);

// --- Promotions ---
} elseif ($uri === '/promotions') {
    requireRole(['admin', 'pilote']);
    $controller = new PiloteController($twig);
    echo $controller->promotions();

} elseif (preg_match('#^/promotion/(\d+)$#', $uri, $m)) {
    requireRole(['admin', 'pilote']);
    $controller = new PiloteController($twig);
    echo $controller->promotionDetail((int) $m[1]);

// --- Profil ---
} elseif ($uri === '/profil') {
    requireLogin();
    $userModel = new UserModel();
    $user      = $userModel->getUserById($_SESSION['user_id']);
    echo $twig->render('profil.html.twig', ['user' => [
        'prenom' => $user['first_name'],
        'nom'    => $user['last_name'],
        'email'  => $user['email'],
        'role'   => $user['role'],
    ]]);

// --- Pages statiques ---
} elseif ($uri === '/a-propos') {
    echo $twig->render('a-propos.html.twig', ['equipe' => []]);

} elseif ($uri === '/contact') {
    echo $twig->render('contact.html.twig');

} elseif ($uri === '/mentions-legales') {
    echo $twig->render('mentions-legales.html.twig');

// --- 404 ---
} else {
    http_response_code(404);
    echo '404 - Page non trouvée';
}
