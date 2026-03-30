<?php
namespace App\Controllers;

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

        return $this->render('wishlist.html.twig', ['offres' => $offres]);
    }

    public function add(int $offerId): void
    {
        $studentId = $_SESSION['user_id'] ?? 0;
        $this->model->addToWishlist($studentId, $offerId);
        $this->redirect('/wishlist');
    }

    public function remove(int $offerId): void
    {
        $studentId = $_SESSION['user_id'] ?? 0;
        $this->model->removeFromWishlist($studentId, $offerId);
        $this->redirect('/wishlist');
    }
}
