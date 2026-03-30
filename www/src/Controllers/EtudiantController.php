<?php
namespace App\Controllers;

use App\Models\UserModel;

class EtudiantController extends Controller
{
    public function __construct($twig)
    {
        parent::__construct($twig);
        $this->model = new UserModel();
    }

    public function index(): string
    {
        $par_page    = 10;
        $page        = max(1, (int) ($_GET['page'] ?? 1));
        $total       = $this->model->countStudents();
        $pages       = max(1, (int) ceil($total / $par_page));
        $page        = min($page, $pages);
        $offset      = ($page - 1) * $par_page;

        return $this->render('etudiants.html.twig', [
            'etudiants'     => $this->model->getStudentsPaginated($par_page, $offset),
            'pages'         => $pages,
            'page_courante' => $page,
        ]);
    }

    public function createPage(): string
    {
        return $this->render('creation-etudiant.html.twig');
    }

    public function create(): void
    {
        $userId = $this->model->createUser(
            $_POST['email']    ?? '',
            $_POST['password'] ?? '',
            'etudiant'
        );

        $this->model->createProfile($userId, $_POST['prenom'] ?? '', $_POST['nom'] ?? '');
        $this->redirect('/dashboard');
    }

    public function update(int $id): void
    {
        $this->model->updateProfile($id, [
            'first_name' => $_POST['prenom'] ?? '',
            'last_name'  => $_POST['nom']    ?? '',
        ]);
        $this->redirect('/dashboard');
    }

    public function delete(int $id): void
    {
        $this->model->deleteUser($id);
        $this->redirect('/dashboard');
    }
}
