<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Validator;
use App\Models\UserModel;

/**
 * Gestionnaire d'authentification (Connexion / Déconnexion)
 */
class AuthController extends Controller
{
    public function __construct($twig)
    {
        parent::__construct($twig);
        $this->model = new UserModel(); // Chargement du modèle utilisateur
    }

    // Affiche le formulaire de connexion
    public function loginPage(): string
    {
        return $this->render('connexion.html.twig', [
            'error' => $_GET['error'] ?? null, // Récupère d'éventuels codes d'erreur (URL)
        ]);
    }

    // Traite la soumission du formulaire de connexion
    public function login(): void
    {
        $email    = trim($_POST['email']    ?? '');
        $password = $_POST['password'] ?? '';

        // Validation des champs
        $v = new Validator();
        $v->required('email', $email, 'Email')
          ->email('email', $email)
          ->required('password', $password, 'Mot de passe');

        if ($v->hasErrors()) {
            $this->redirect('/connexion?error=1');
            return;
        }

        // Vérification de l'utilisateur en base de données
        $user = $this->model->getUserByEmail($email);

        // Vérification identifiants + mot de passe haché
        if (!$user || !$this->model->verifyPassword($password, $user['password'])) {
            $this->redirect('/connexion?error=1');
            return;
        }

        // Vérifie si le compte est actif (ex: pas banni)
        if (!$user['is_active']) {
            $this->redirect('/connexion?error=2');
            return;
        }

        // Initialisation de la session utilisateur
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['first_name'];

        $this->redirect('/'); // Redirection vers l'accueil
    }

    // Détruit la session et déconnecte l'utilisateur
    public function logout(): void
    {
        session_destroy();
        $this->redirect('/');
    }
}