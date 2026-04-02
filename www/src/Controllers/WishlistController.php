<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\WishlistModel;

class WishlistController extends Controller
{
    public function __construct($twig)
    {
        parent::__construct($twig);
        $this->model = new WishlistModel();
    }

    public function index(): string
    {
        $studentId = $_SESSION['user_id'] ?? 0;
        $offres    = $this->model->getWishlistByStudent($studentId);

        foreach ($offres as &$offre) {
            $offre['competences'] = !empty($offre['skill_names'])
                ? explode('|', $offre['skill_names'])
                : [];
        }

        return $this->render('wishlist.html.twig', ['offres' => $offres]);
    }

    public function add(int $offerId): void
    {
        $studentId = $_SESSION['user_id'] ?? 0;
        $this->model->addToWishlist($studentId, $offerId);
        $referer = $_SERVER['HTTP_REFERER'] ?? '/offres';
        $this->redirect($referer);
    }

    public function remove(int $offerId): void
    {
        $studentId = $_SESSION['user_id'] ?? 0;
        $this->model->removeFromWishlist($studentId, $offerId);
        $referer = $_SERVER['HTTP_REFERER'] ?? '/wishlist';
        $this->redirect($referer);
    }
}
