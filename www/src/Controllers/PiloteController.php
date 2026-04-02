<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Validator;
use App\Models\UserModel;
use App\Models\PromotionModel;

class PiloteController extends Controller
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
        $total    = $this->model->countPilotsFiltered($search);
        $pages    = max(1, (int) ceil($total / $par_page));
        $page     = min($page, $pages);
        $offset   = ($page - 1) * $par_page;

        return $this->render('pilotes.html.twig', [
            'pilotes'       => $this->model->getPilotsFiltered($par_page, $offset, $search),
            'pages'         => $pages,
            'page_courante' => $page,
            'search'        => $search,
        ]);
    }

    public function createPage(): string
    {
        return $this->render('creation-pilote.html.twig');
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
            echo $this->render('creation-pilote.html.twig', ['errors' => $v->getErrors()]);
            return;
        }

        $userId = $this->model->createUser($email, $password, 'pilote');
        $this->model->createProfile($userId, $prenom, $nom);
        $this->redirect('/pilotes');
    }

    public function editPage(int $id): string
    {
        $pilote = $this->model->getUserById($id);
        if (!$pilote) {
            http_response_code(404);
            return '404 - Pilote non trouvé';
        }
        return $this->render('modifier-pilote.html.twig', ['pilote' => $pilote]);
    }

    public function edit(int $id): void
    {
        $prenom = trim($_POST['prenom'] ?? '');
        $nom    = trim($_POST['nom']    ?? '');
        $email  = trim($_POST['email']  ?? '');

        $v = new Validator();
        $v->required('prenom', $prenom, 'Prénom')
          ->minLength('prenom', $prenom, 2, 'Prénom')
          ->maxLength('prenom', $prenom, 100, 'Prénom')
          ->required('nom', $nom, 'Nom')
          ->minLength('nom', $nom, 2, 'Nom')
          ->maxLength('nom', $nom, 100, 'Nom')
          ->email('email', $email)
          ->maxLength('email', $email, 255, 'Email');

        if ($v->hasErrors()) {
            $pilote = $this->model->getUserById($id);
            echo $this->render('modifier-pilote.html.twig', [
                'pilote' => $pilote,
                'errors' => $v->getErrors(),
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
        $this->redirect('/pilotes');
    }

    public function delete(int $id): void
    {
        $this->model->deleteUser($id);
        $this->redirect('/pilotes');
    }

    public function promotions(): string
    {
        $promotionModel = new PromotionModel();
        return $this->render('promotions.html.twig', [
            'promotions'    => $promotionModel->getAllPromotions(),
            'pages'         => 1,
            'page_courante' => 1,
        ]);
    }

    public function createPromotionPage(): string
    {
        $pilotes = $this->model->getAllPilots();
        return $this->render('creation-promotion.html.twig', [
            'pilotes' => $pilotes,
        ]);
    }

    public function createPromotion(): void
    {
        $name    = trim($_POST['name'] ?? '');
        $year    = $_POST['year']     ?? date('Y');
        $pilotId = $_POST['pilot_id'] ?? '0';

        $v = new Validator();
        $v->required('name', $name, 'Nom de la promotion')
          ->minLength('name', $name, 2, 'Nom de la promotion')
          ->maxLength('name', $name, 255, 'Nom de la promotion')
          ->intRange('year', $year, 2020, 2040, 'Année')
          ->positiveInt('pilot_id', $pilotId, 'Pilote');

        if ($v->hasErrors()) {
            $pilotes = $this->model->getAllPilots();
            echo $this->render('creation-promotion.html.twig', [
                'pilotes' => $pilotes,
                'errors'  => $v->getErrors(),
            ]);
            return;
        }

        $promotionModel = new PromotionModel();
        $promotionModel->createPromotion($name, (int) $year, (int) $pilotId);
        $this->redirect('/promotions');
    }

    public function promotionDetail(int $id): string
    {
        $promotionModel = new PromotionModel();
        return $this->render('promotion-detail.html.twig', [
            'promotion' => $promotionModel->getPromotionById($id),
            'etudiants' => $promotionModel->getStudentsByPromotion($id),
        ]);
    }
}
