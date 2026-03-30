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
        $total        = count($candidatures);
        $par_page     = 10;
        $page         = max(1, (int) ($_GET['page'] ?? 1));
        $pages        = max(1, (int) ceil($total / $par_page));
        $page         = min($page, $pages);
        $offset       = ($page - 1) * $par_page;

        return $this->render('mes-candidatures.html.twig', [
            'candidatures'  => array_slice($candidatures, $offset, $par_page),
            'pages'         => $pages,
            'page_courante' => $page,
        ]);
    }

    public function piloteView(): string
    {
        $pilotId = $_SESSION['user_id'] ?? 0;
        return $this->render('candidatures-pilote.html.twig', [
            'candidatures' => $this->model->getApplicationsByPilot($pilotId),
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
        $message   = $_POST['message'] ?? '';
        $cvPath    = '';
        $lmPath    = '';

        if (!empty($_FILES['cv']['tmp_name'])) {
            $cvPath = $this->saveFile($_FILES['cv'], 'cv');
        }
        if (!empty($_FILES['lm']['tmp_name'])) {
            $lmPath = $this->saveFile($_FILES['lm'], 'lm');
        }

        if (!$this->model->hasAlreadyApplied($studentId, $offerId)) {
            $this->model->createApplication($studentId, $offerId, $message, $cvPath, $lmPath);
        }

        $this->redirect('/mes-candidatures');
    }

    private function saveFile(array $file, string $prefix): string
    {
        $uploadDir = '/var/www/html/public/uploads/';
        $ext       = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed   = ['pdf', 'doc', 'docx'];

        if (!in_array($ext, $allowed)) {
            return '';
        }

        $filename = $prefix . '_' . uniqid() . '.' . $ext;
        move_uploaded_file($file['tmp_name'], $uploadDir . $filename);
        return 'public/uploads/' . $filename;
    }

    public function spontaneous(): string
    {
        return $this->render('candidature-spontanee.html.twig');
    }
}
