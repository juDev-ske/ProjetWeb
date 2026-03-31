<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\OffreModel;
use App\Models\EntrepriseModel;
use App\Models\UserModel;

class DashboardController extends Controller
{
    public function index(): string
    {
        $offreModel      = new OffreModel();
        $entrepriseModel = new EntrepriseModel();
        $userModel       = new UserModel();

        return $this->render('dashboard.html.twig', [
            'stats' => [
                'offres'      => $offreModel->countAllOffers(),
                'entreprises' => $entrepriseModel->countAllCompanies(),
                'etudiants'   => $userModel->countStudents(),
                'pilotes'     => $userModel->countPilots(),
            ],
            'dernières_offres'      => array_slice($offreModel->getAllOffers(), 0, 5),
            'dernières_entreprises' => array_slice($entrepriseModel->getAllCompanies(), 0, 5),
            'derniers_etudiants'    => array_slice($userModel->getAllStudents(), 0, 5),
        ]);
    }

    public function statistiques(): string
    {
        $offreModel = new OffreModel();
        return $this->render('statistiques.html.twig', [
            'stats' => [
                'total'            => $offreModel->countTotal(),
                'moy_candidatures' => $offreModel->avgCandidaturesParOffre(),
                'par_type'         => $offreModel->repartitionParType(),
                'top_wishlist'     => $offreModel->topWishlist(5),
            ],
        ]);
    }
}
