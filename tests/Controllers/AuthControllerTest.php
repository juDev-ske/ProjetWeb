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

        // Crée un mock du contrôleur en remplaçant `redirect`
        $controller = $this->getMockBuilder(AuthController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['redirect'])
            ->getMock();

        // Stub du modèle pour retourner l'utilisateur et valider le mot de passe
        $model = new class($user) {
            private $user;
            public function __construct($user) { $this->user = $user; }
            public function getUserByEmail($email) { return $this->user; }
            public function verifyPassword($given, $hash) { return true; }
        };

        // Injecte le modèle dans la propriété protégée
        $ref = new \ReflectionClass('App\\Controllers\\Controller');
        $prop = $ref->getProperty('model');
        $prop->setAccessible(true);
        $prop->setValue($controller, $model);

        // Attend un redirect vers la home et lève une exception pour stopper l'exécution (simule exit())
        $controller->expects($this->once())
            ->method('redirect')
            ->with('/')
            ->will($this->throwException(new \RuntimeException('redirect')));

        try {
            $controller->login();
        } catch (\RuntimeException $e) {
            // expected to stop execution
        }

        $this->assertEquals(3, $_SESSION['user_id']);
        $this->assertEquals('etudiant', $_SESSION['user_role']);
        $this->assertEquals('Jean', $_SESSION['user_name']);
    }

    public function testLoginInvalidCredentialsRedirectsToLoginWithError()
    {
        $_POST['email'] = 'unknown@x.com';
        $_POST['password'] = 'bad';

        $controller = $this->getMockBuilder(AuthController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['redirect'])
            ->getMock();

        // Le modèle retourne null (utilisateur introuvable)
        $model = new class {
            public function getUserByEmail($email) { return null; }
        };

        $ref = new \ReflectionClass('App\\Controllers\\Controller');
        $prop = $ref->getProperty('model');
        $prop->setAccessible(true);
        $prop->setValue($controller, $model);

        // Attend un redirect vers la page de connexion avec erreur=1 et lève une exception pour arrêter l'exécution
        $controller->expects($this->once())
            ->method('redirect')
            ->with('/connexion?error=1')
            ->will($this->throwException(new \RuntimeException('redirect')));

        try {
            $controller->login();
        } catch (\RuntimeException $e) {
            // expected
        }
    }
}
