<?php
namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends Controller
{
    public function __construct($twig)
    {
        parent::__construct($twig);
        $this->model = new UserModel();
    }

    public function loginPage(): string
    {
        return $this->render('connexion.html.twig');
    }

    public function login(): void
    {
        $email    = $_POST['email']    ?? '';
        $password = $_POST['password'] ?? '';

        $user = $this->model->getUserByEmail($email);

        if (!$user || !$this->model->verifyPassword($password, $user['password'])) {
            $this->redirect('/connexion?error=1');
            return;
        }

        if (!$user['is_active']) {
            $this->redirect('/connexion?error=2');
            return;
        }

        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['first_name'];

        $this->redirect('/');
    }

    public function logout(): void
    {
        session_destroy();
        $this->redirect('/');
    }
}
