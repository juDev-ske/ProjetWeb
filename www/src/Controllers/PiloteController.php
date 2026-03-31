<?php
namespace App\Controllers;

use App\Core\Controller;
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
        $userId = $this->model->createUser(
            $_POST['email']    ?? '',
            $_POST['password'] ?? '',
            'pilote'
        );
        $this->model->createProfile($userId, $_POST['prenom'] ?? '', $_POST['nom'] ?? '');
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
        $this->model->updateProfile($id, [
            'first_name' => $_POST['prenom'] ?? '',
            'last_name'  => $_POST['nom']    ?? '',
        ]);
        if (!empty($_POST['email'])) {
            $this->model->updateEmail($id, $_POST['email']);
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

    public function promotionDetail(int $id): string
    {
        $promotionModel = new PromotionModel();
        return $this->render('promotion-detail.html.twig', [
            'promotion' => $promotionModel->getPromotionById($id),
            'etudiants' => $promotionModel->getStudentsByPromotion($id),
        ]);
    }
}
