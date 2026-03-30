<?php
namespace App\Controllers;

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
        $par_page    = 10;
        $page        = max(1, (int) ($_GET['page'] ?? 1));
        $total       = $this->model->countPilots();
        $pages       = max(1, (int) ceil($total / $par_page));
        $page        = min($page, $pages);
        $offset      = ($page - 1) * $par_page;

        return $this->render('pilotes.html.twig', [
            'pilotes'       => $this->model->getPilotsPaginated($par_page, $offset),
            'pages'         => $pages,
            'page_courante' => $page,
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
        $this->redirect('/dashboard');
    }

    public function delete(int $id): void
    {
        $this->model->deleteUser($id);
        $this->redirect('/dashboard');
    }

    public function promotions(): string
    {
        $promotionModel = new PromotionModel();
        $promotions     = $promotionModel->getAllPromotions();

        return $this->render('promotions.html.twig', [
            'promotions'   => $promotions,
            'pages'        => 1,
            'page_courante'=> 1
        ]);
    }

    public function promotionDetail(int $id): string
    {
        $promotionModel = new PromotionModel();
        $promotion      = $promotionModel->getPromotionById($id);
        $etudiants      = $promotionModel->getStudentsByPromotion($id);

        return $this->render('promotion-detail.html.twig', [
            'promotion' => $promotion,
            'etudiants' => $etudiants
        ]);
    }
}
