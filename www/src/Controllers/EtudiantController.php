<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Validator;
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
        $prenom   = trim($_POST['prenom']   ?? '');
        $nom      = trim($_POST['nom']      ?? '');
        $email    = trim($_POST['email']    ?? '');
        $password = $_POST['password'] ?? '';

        $v = new Validator();
        $v->required('prenom', $prenom, 'Prénom')
          ->minLength('prenom', $prenom, 2, 'Prénom')
          ->maxLength('prenom', $prenom, 100, 'Prénom')
          ->required('nom', $nom, 'Nom')
          ->minLength('nom', $nom, 2, 'Nom')
          ->maxLength('nom', $nom, 100, 'Nom')
          ->required('email', $email, 'Email')
          ->email('email', $email)
          ->maxLength('email', $email, 255, 'Email')
          ->required('password', $password, 'Mot de passe')
          ->password('password', $password);

        if ($v->hasErrors()) {
            echo $this->render('creation-etudiant.html.twig', ['errors' => $v->getErrors()]);
            return;
        }

        $userId = $this->model->createUser($email, $password, 'etudiant');
        $this->model->createProfile($userId, $prenom, $nom);
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
        $prenom = trim($_POST['prenom'] ?? '');
        $nom    = trim($_POST['nom']    ?? '');
        $email  = trim($_POST['email']  ?? '');
        $status = $_POST['stage_status'] ?? 'searching';

        $v = new Validator();
        $v->required('prenom', $prenom, 'Prénom')
          ->minLength('prenom', $prenom, 2, 'Prénom')
          ->maxLength('prenom', $prenom, 100, 'Prénom')
          ->required('nom', $nom, 'Nom')
          ->minLength('nom', $nom, 2, 'Nom')
          ->maxLength('nom', $nom, 100, 'Nom')
          ->email('email', $email)
          ->maxLength('email', $email, 255, 'Email')
          ->inList('stage_status', $status, ['searching', 'found', 'not_searching'], 'Statut de stage');

        if ($v->hasErrors()) {
            $etudiant = $this->model->getUserById($id);
            echo $this->render('modifier-etudiant.html.twig', [
                'etudiant' => $etudiant,
                'errors'   => $v->getErrors(),
            ]);
            return;
        }

        $this->model->updateProfile($id, [
            'first_name' => $prenom,
            'last_name'  => $nom,
        ]);
        if ($email !== '') {
            $this->model->updateEmail($id, $email);
        }
        $this->model->updateStageStatus($id, $status);
        $this->redirect('/etudiants');
    }

    public function delete(int $id): void
    {
        $this->model->deleteUser($id);
        $this->redirect('/etudiants');
    }
}
