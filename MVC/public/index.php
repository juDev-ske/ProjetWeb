
<?php
//A sup quand on a fini de dev
ini_set('display_errors', 1);
error_reporting(E_ALL);

//Charge  les classes (Controller, Model, Twig)
require_once "vendor/autoload.php";

// 3. Import des classes nécessaires
use App\Controllers\TaskController;

// 4. Configuration de l'environnement de Vue (Twig)
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader, [
    'debug' => true,
    'cache' => false // Désactivé pour le développement
]);

// 5. Récupération et nettoyage de l'URL
// On récupère "uri", si vide on met "/", et on retire les "/" inutiles au début/fin
$uri = trim($_GET['uri'] ?? '/', '/');

// 6. Définition de la "Carte des Routes" (Le coeur de la personnalisation)
// Format : 'url_tapée' => 'nom_de_la_méthode_dans_le_controller'
$routes = [
    ''             => 'welcomePage',
    'add_task'     => 'addTask',
    'check_task'   => 'checkTask',
    'story'        => 'storyPage',
    'uncheck_task' => 'uncheckTask',
    'about'        => 'aboutPage',
];

// 7. Le Dispatcher (L'aiguilleur)
$controller = new TaskController($twig);

if (array_key_exists($uri, $routes)) {
    // On récupère le nom de la méthode associée à l'URL
    $method = $routes[$uri];
    
    // On appelle dynamiquement la méthode sur l'objet controller
    $controller->$method(); 
} else {
    // Gestion de l'erreur 404
    http_response_code(404);
    echo $twig->render('errors/404.html.twig', ['uri' => $uri]);
}