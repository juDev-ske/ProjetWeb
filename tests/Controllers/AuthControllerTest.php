<?php
namespace Tests\Controllers;

use PHPUnit\Framework\TestCase;
use App\Controllers\AuthController;

/**
 * Tests unitaires pour le contrôleur d'authentification (AuthController)
 */
class AuthControllerTest extends TestCase
{
    // Nettoyage des variables globales après chaque test pour éviter les effets de bord
    protected function tearDown(): void
    {
        $_POST = [];
        $_SESSION = [];
    }

    /**
     * Teste qu'une connexion réussie redirige vers l'accueil
     * et initialise correctement la session.
     */
    public function testLoginSuccessRedirectsToHome()
    {
        // Simulation des données saisies dans le formulaire
        $_POST['email'] = 'etudiant@cesitionjob.fr';
        $_POST['password'] = 'password';

        $user = [
            'id' => 3,
            'role' => 'etudiant',
            'first_name' => 'Jean',
            'password' => 'hashed',
            'is_active' => true
        ];

        // Création d'un "Mock" du contrôleur pour intercepter la redirection sans quitter le script
        $controller = new class extends AuthController {
            public $redirectTo = null;
            public function __construct() {}
            public function setModelPublic($m) { $this->model = $m; }
            public function redirect(string $url): void { $this->redirectTo = $url; }
        };

        // Simulation (Stub) du modèle pour simuler une réponse positive de la base de données
        $model = new class($user) {
            private $user;
            public function __construct($user) { $this->user = $user; }
            public function getUserByEmail($email) { return $this->user; }
            public function verifyPassword($given, $hash) { return true; }
        };

        $controller->setModelPublic($model);

        try {
            $controller->login();
        } catch (\RuntimeException $e) {
            // On capture l'arrêt de l'exécution normalement provoqué par le redirect
        }

        // Vérifications (Assertions)
        $this->assertEquals('/', $controller->redirectTo); // Redirection vers accueil ?
        $this->assertEquals(3, $_SESSION['user_id']);       // ID en session ?
        $this->assertEquals('etudiant', $_SESSION['user_role']);
        $this->assertEquals('Jean', $_SESSION['user_name']);
    }

    /**
     * Teste qu'un identifiant invalide renvoie vers la page de connexion avec une erreur.
     */
    public function testLoginInvalidCredentialsRedirectsToLoginWithError()
    {
        $_POST['email'] = 'unknown@x.com';
        $_POST['password'] = 'bad';

        // Simulation d'un utilisateur non trouvé (retourne null)
        $model = new class { public function getUserByEmail($email) { return null; } };

        $controller = new class extends AuthController {
            public $redirectTo = null;
            public function __construct() {}
            public function setModelPublic($m) { $this->model = $m; }
            public function redirect(string $url): void { $this->redirectTo = $url; }
        };

        $controller->setModelPublic($model);

        try {
            $controller->login();
        } catch (\RuntimeException $e) {
            // expected
        }

        // Vérifie que l'URL contient bien le paramètre d'erreur
        $this->assertEquals('/connexion?error=1', $controller->redirectTo);
    }
}