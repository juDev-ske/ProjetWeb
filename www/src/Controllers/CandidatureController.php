<?php
namespace App\Controllers;

use App\Models\CandidatureModel;
use App\Models\OffreModel;

class CandidatureController extends Controller
{
    public function __construct($twig)
    {
        parent::__construct($twig);
        $this->model = new CandidatureModel();
    }

    public function index(): string
    {
        $studentId    = $_SESSION['user_id'] ?? 0;
        $candidatures = $this->model->getApplicationsByStudent($studentId);

        return $this->render('mes-candidatures.html.twig', [
            'candidatures' => $candidatures,
            'pages'        => 1,
            'page_courante'=> 1
        ]);
    }

    public function applyPage(int $offerId): string
    {
        $offreModel = new OffreModel();
        $offre      = $offreModel->getOfferById($offerId);

        if (!$offre) {
            http_response_code(404);
            return '404 - Offre non trouvée';
        }

        return $this->render('postuler.html.twig', ['offre' => $offre]);
    }

    public function apply(int $offerId): void
    {
        $studentId = $_SESSION['user_id'] ?? 0;
        $message   = $_POST['message']    ?? '';

        if (!$this->model->hasAlreadyApplied($studentId, $offerId)) {
            $this->model->createApplication($studentId, $offerId, $message);
        }

        $this->redirect('/mes-candidatures');
    }

    public function spontaneous(): string
    {
        return $this->render('candidature-spontanee.html.twig');
    }
}
