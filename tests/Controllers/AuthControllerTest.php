<?php
namespace Tests\Controllers;

use PHPUnit\Framework\TestCase;
use App\Controllers\AuthController;

class AuthControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        // Nettoie les superglobales entre les tests
        $_POST = [];
        $_SESSION = [];
    }

    public function testLoginSuccessRedirectsToHome()
    {
        // Prépare les données POST
        $_POST['email'] = 'etudiant@cesitionjob.fr';
        $_POST['password'] = 'password';

        $user = [
            'id' => 3,
            'role' => 'etudiant',
            'first_name' => 'Jean',
            'password' => 'hashed',
            'is_active' => true
        ];

        // Crée une instance de contrôleur de test qui capture le redirect
        $controller = new class extends AuthController {
            public $redirectTo = null;
            public function __construct() {}
            public function setModelPublic($m) { $this->model = $m; }
            public function redirect(string $url): void { $this->redirectTo = $url; }
        };

        // Stub du modèle pour retourner l'utilisateur et valider le mot de passe
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
            // expected to stop execution (redirect)
        }

        $this->assertEquals('/', $controller->redirectTo);

        $this->assertEquals(3, $_SESSION['user_id']);
        $this->assertEquals('etudiant', $_SESSION['user_role']);
        $this->assertEquals('Jean', $_SESSION['user_name']);
    }

    public function testLoginInvalidCredentialsRedirectsToLoginWithError()
    {
        $_POST['email'] = 'unknown@x.com';
        $_POST['password'] = 'bad';

        // Le modèle retourne null (utilisateur introuvable)
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

        $this->assertEquals('/connexion?error=1', $controller->redirectTo);
    }
}
