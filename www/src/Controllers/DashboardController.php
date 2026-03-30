<?php
namespace App\Controllers;

use App\Models\OffreModel;
use App\Models\EntrepriseModel;
use App\Models\UserModel;

class DashboardController extends Controller
{
    public function index(): string
    {
        $offreModel     = new OffreModel();
        $entrepriseModel = new EntrepriseModel();
        $userModel      = new UserModel();

        return $this->render('dashboard.html.twig', [
            'user'                 => ['prenom' => $_SESSION['user_name'] ?? 'Admin'],
            'stats'                => [
                'offres'      => count($offreModel->getAllOffers()),
                'entreprises' => count($entrepriseModel->getAllCompanies()),
                'etudiants'   => count($userModel->getAllStudents()),
                'pilotes'     => count($userModel->getAllPilots()),
            ],
            'dernières_offres'      => array_slice($offreModel->getAllOffers(), 0, 3),
            'dernières_entreprises' => array_slice($entrepriseModel->getAllCompanies(), 0, 3),
            'derniers_etudiants'    => array_slice($userModel->getAllStudents(), 0, 3),
        ]);
    }
}
