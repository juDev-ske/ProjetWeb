<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Validator;
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
        $message   = trim($_POST['message'] ?? '');
        $cvFile    = $_FILES['cv'] ?? [];
        $lmFile    = $_FILES['lm'] ?? [];

        $v = new Validator();
        $v->maxLength('message', $message, 2000, 'Message')
          ->file('cv', $cvFile, ['pdf', 'doc', 'docx'], 5, 'CV', true)
          ->file('lm', $lmFile, ['pdf', 'doc', 'docx'], 5, 'Lettre de motivation', true);

        if ($v->hasErrors()) {
            $offreModel = new OffreModel();
            $offre      = $offreModel->getOfferById($offerId);
            echo $this->render('postuler.html.twig', [
                'offre'  => $offre,
                'errors' => $v->getErrors(),
            ]);
            return;
        }

        $cvPath = '';
        $lmPath = '';

        if (!empty($cvFile['tmp_name'])) {
            $cvPath = $this->saveFile($cvFile, 'cv');
        }
        if (!empty($lmFile['tmp_name'])) {
            $lmPath = $this->saveFile($lmFile, 'lm');
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

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = $prefix . '_' . uniqid() . '.' . $ext;
        move_uploaded_file($file['tmp_name'], $uploadDir . $filename);
        return 'public/uploads/' . $filename;
    }

    public function spontaneous(): string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $prenom    = trim($_POST['prenom']     ?? '');
            $nom       = trim($_POST['nom']        ?? '');
            $email     = trim($_POST['email']      ?? '');
            $entreprise = trim($_POST['entreprise'] ?? '');
            $poste     = trim($_POST['poste']      ?? '');
            $contrat   = $_POST['contrat']   ?? '';
            $message   = trim($_POST['message']    ?? '');
            $cvFile    = $_FILES['cv'] ?? [];
            $lmFile    = $_FILES['lm'] ?? [];

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
              ->maxLength('entreprise', $entreprise, 255, 'Entreprise ciblée')
              ->maxLength('poste', $poste, 255, 'Poste visé')
              ->inList('contrat', $contrat, ['', 'stage', 'alternance', 'cdi', 'cdd'], 'Type de contrat')
              ->maxLength('message', $message, 2000, 'Message')
              ->file('cv', $cvFile, ['pdf', 'doc', 'docx'], 5, 'CV', true)
              ->file('lm', $lmFile, ['pdf', 'doc', 'docx'], 5, 'Lettre de motivation', false);

            if ($v->hasErrors()) {
                return $this->render('candidature-spontanee.html.twig', ['errors' => $v->getErrors()]);
            }

            $cvPath = '';
            $lmPath = '';

            if (!empty($cvFile['tmp_name'])) {
                $cvPath = $this->saveFile($cvFile, 'cv');
            }
            if (!empty($lmFile['tmp_name'])) {
                $lmPath = $this->saveFile($lmFile, 'lm');
            }

            $studentId = $_SESSION['user_id'] ?? 0;
            $this->model->createSpontaneousApplication(
                $studentId, $entreprise, $poste, $contrat, $message, $cvPath, $lmPath
            );

            return $this->render('candidature-spontanee.html.twig', ['success' => true]);
        }

        return $this->render('candidature-spontanee.html.twig');
    }
}
