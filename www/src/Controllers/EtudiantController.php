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
        $par_page = 10;
        $search   = $_GET['search'] ?? '';
        $page     = max(1, (int) ($_GET['page'] ?? 1));
        $total    = $this->model->countStudentsFiltered($search);
        $pages    = max(1, (int) ceil($total / $par_page));
        $page     = min($page, $pages);
        $offset   = ($page - 1) * $par_page;

        return $this->render('etudiants.html.twig', [
            'etudiants'     => $this->model->getStudentsFiltered($par_page, $offset, $search),
            'pages'         => $pages,
            'page_courante' => $page,
            'search'        => $search,
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
        $this->redirect('/etudiants');
    }

    public function editPage(int $id): string
    {
        $etudiant = $this->model->getUserById($id);
        if (!$etudiant) {
            http_response_code(404);
            return '404 - Étudiant non trouvé';
        }
        return $this->render('modifier-etudiant.html.twig', ['etudiant' => $etudiant]);
    }

    public function edit(int $id): void
    {
        $this->model->updateProfile($id, [
            'first_name' => $_POST['prenom'] ?? '',
            'last_name'  => $_POST['nom']    ?? '',
        ]);
        if (!empty($_POST['email'])) {
            $this->model->updateEmail($id, $_POST['email']);
        }
        $allowed = ['searching', 'found', 'not_searching'];
        $status  = $_POST['stage_status'] ?? 'searching';
        if (in_array($status, $allowed)) {
            $this->model->updateStageStatus($id, $status);
        }
        $this->redirect('/etudiants');
    }

    public function delete(int $id): void
    {
        $this->model->deleteUser($id);
        $this->redirect('/etudiants');
    }
}
