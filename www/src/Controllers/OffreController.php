<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Validator;
use App\Models\OffreModel;
use App\Models\EntrepriseModel;
use App\Models\WishlistModel;

class OffreController extends Controller
{
    public function __construct($twig)
    {
        parent::__construct($twig);
        $this->model = new OffreModel();
    }

    public function getLatestOffers(): array
    {
        return array_slice($this->model->getAllOffers(), 0, 6);
    }

    public function index(): string
    {
        $par_page = 10;
        $page     = max(1, (int) ($_GET['page'] ?? 1));

        $filters = [
            'search' => $_GET['search'] ?? '',
            'type'   => $_GET['type']   ?? [],
            'mode'   => $_GET['mode']   ?? [],
            'ville'  => $_GET['ville']  ?? '',
        ];

        $total  = $this->model->countOffersFiltered($filters);
        $pages  = max(1, (int) ceil($total / $par_page));
        $page   = min($page, $pages);
        $offset = ($page - 1) * $par_page;

        $wishlistIds = [];
        if (($_SESSION['user_role'] ?? '') === 'etudiant') {
            $wishlistIds = (new WishlistModel())->getWishlistOfferIds($_SESSION['user_id'] ?? 0);
        }

        return $this->render('offres.html.twig', [
            'offres'        => $this->model->getOffersFiltered($par_page, $offset, $filters),
            'total'         => $total,
            'pages'         => $pages,
            'page_courante' => $page,
            'filters'       => $filters,
            'wishlist_ids'  => $wishlistIds,
        ]);
    }

    public function show(int $id): string
    {
        $offre = $this->model->getOfferById($id);
        if (!$offre) {
            http_response_code(404);
            return '404 - Offre non trouvée';
        }
        $skills                   = $this->model->getOfferSkills($id);
        $offre['competences']     = array_column($skills, 'name');
        $offre['nb_candidatures'] = $this->model->getOfferApplicationCount($id);

        $inWishlist = false;
        if (($_SESSION['user_role'] ?? '') === 'etudiant') {
            $inWishlist = (new WishlistModel())->isInWishlist($_SESSION['user_id'] ?? 0, $id);
        }

        return $this->render('offre-detail.html.twig', [
            'offre'       => $offre,
            'in_wishlist' => $inWishlist,
        ]);
    }

    public function createPage(): string
    {
        $entrepriseModel = new EntrepriseModel();
        return $this->render('creation-offre.html.twig', [
            'entreprises' => $entrepriseModel->getAllCompanies(),
            'locations'   => $this->model->getAllLocations(),
            'skills'      => $this->model->getAllSkills(),
        ]);
    }

    public function create(): void
    {
        $title       = trim($_POST['title']       ?? '');
        $description = trim($_POST['description'] ?? '');
        $salary      = trim($_POST['salary']      ?? '');
        $type        = $_POST['type']        ?? '';
        $mode        = $_POST['mode']        ?? '';
        $companyId   = $_POST['company_id']  ?? '0';
        $locationId  = $_POST['location_id'] ?? '0';

        $v = new Validator();
        $v->required('title', $title, 'Titre')
          ->minLength('title', $title, 3, 'Titre')
          ->maxLength('title', $title, 255, 'Titre')
          ->maxLength('description', $description, 5000, 'Description')
          ->maxLength('salary', $salary, 100, 'Rémunération')
          ->required('type', $type, 'Type de contrat')
          ->inList('type', $type, ['internship', 'apprenticeship'], 'Type de contrat')
          ->required('mode', $mode, 'Mode de travail')
          ->inList('mode', $mode, ['on_site', 'remote', 'hybrid'], 'Mode de travail')
          ->positiveInt('company_id', $companyId, 'Entreprise')
          ->positiveInt('location_id', $locationId, 'Localisation');

        if ($v->hasErrors()) {
            $entrepriseModel = new EntrepriseModel();
            echo $this->render('creation-offre.html.twig', [
                'entreprises' => $entrepriseModel->getAllCompanies(),
                'locations'   => $this->model->getAllLocations(),
                'skills'      => $this->model->getAllSkills(),
                'errors'      => $v->getErrors(),
            ]);
            return;
        }

        $offerId = $this->model->createOffer([
            'title'            => $title,
            'description'      => $description,
            'salary'           => $salary,
            'type'             => $type,
            'mode'             => $mode,
            'publication_date' => date('Y-m-d'),
            'company_id'       => (int) $companyId,
            'location_id'      => (int) $locationId,
        ]);

        $skillNames = array_filter(array_map('trim', explode(',', $_POST['skills'] ?? '')));
        foreach ($skillNames as $name) {
            $skillId = $this->model->createSkill($name);
            $this->model->addSkillToOffer($offerId, $skillId);
        }

        $this->redirect('/offres');
    }

    public function editPage(int $id): string
    {
        $offre = $this->model->getOfferById($id);
        if (!$offre) {
            http_response_code(404);
            return '404 - Offre non trouvée';
        }
        $offreSkills = array_column($this->model->getOfferSkills($id), 'name');
        return $this->render('modifier-offre.html.twig', [
            'offre'        => $offre,
            'locations'    => $this->model->getAllLocations(),
            'skills'       => $this->model->getAllSkills(),
            'offre_skills' => $offreSkills,
        ]);
    }

    public function edit(int $id): void
    {
        $title       = trim($_POST['title']       ?? '');
        $description = trim($_POST['description'] ?? '');
        $salary      = trim($_POST['salary']      ?? '');
        $type        = $_POST['type']        ?? '';
        $mode        = $_POST['mode']        ?? '';
        $locationId  = $_POST['location_id'] ?? '0';

        $v = new Validator();
        $v->required('title', $title, 'Titre')
          ->minLength('title', $title, 3, 'Titre')
          ->maxLength('title', $title, 255, 'Titre')
          ->maxLength('description', $description, 5000, 'Description')
          ->maxLength('salary', $salary, 100, 'Rémunération')
          ->required('type', $type, 'Type de contrat')
          ->inList('type', $type, ['internship', 'apprenticeship'], 'Type de contrat')
          ->required('mode', $mode, 'Mode de travail')
          ->inList('mode', $mode, ['on_site', 'remote', 'hybrid'], 'Mode de travail')
          ->positiveInt('location_id', $locationId, 'Localisation');

        if ($v->hasErrors()) {
            $offre       = $this->model->getOfferById($id);
            $offreSkills = array_column($this->model->getOfferSkills($id), 'name');
            echo $this->render('modifier-offre.html.twig', [
                'offre'        => $offre,
                'locations'    => $this->model->getAllLocations(),
                'skills'       => $this->model->getAllSkills(),
                'offre_skills' => $offreSkills,
                'errors'       => $v->getErrors(),
            ]);
            return;
        }

        $this->model->updateOffer($id, [
            'title'       => $title,
            'description' => $description,
            'salary'      => $salary,
            'type'        => $type,
            'mode'        => $mode,
            'location_id' => (int) $locationId,
        ]);

        $this->model->clearSkillsFromOffer($id);
        $skillNames = array_filter(array_map('trim', explode(',', $_POST['skills'] ?? '')));
        foreach ($skillNames as $name) {
            $skillId = $this->model->createSkill($name);
            $this->model->addSkillToOffer($id, $skillId);
        }

        $this->redirect('/offre/' . $id);
    }

    public function delete(int $id): void
    {
        $this->model->deleteOffer($id);
        $this->redirect('/offres');
    }
}
