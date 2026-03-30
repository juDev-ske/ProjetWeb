<?php
namespace App\Controllers;

use App\Models\OffreModel;
use App\Models\EntrepriseModel;

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

        return $this->render('offres.html.twig', [
            'offres'        => $this->model->getOffersFiltered($par_page, $offset, $filters),
            'total'         => $total,
            'pages'         => $pages,
            'page_courante' => $page,
            'filters'       => $filters,
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

        return $this->render('offre-detail.html.twig', ['offre' => $offre]);
    }

    public function createPage(): string
    {
        $entrepriseModel = new EntrepriseModel();
        return $this->render('creation-offre.html.twig', [
            'entreprises' => $entrepriseModel->getAllCompanies(),
        ]);
    }

    public function create(): void
    {
        $this->model->createOffer([
            'title'            => $_POST['title']       ?? '',
            'description'      => $_POST['description'] ?? '',
            'salary'           => $_POST['salary']      ?? '',
            'type'             => $_POST['type']        ?? '',
            'mode'             => $_POST['mode']        ?? '',
            'publication_date' => date('Y-m-d'),
            'company_id'       => (int) ($_POST['company_id']  ?? 0),
            'location_id'      => (int) ($_POST['location_id'] ?? 1),
        ]);
        $this->redirect('/offres');
    }

    public function editPage(int $id): string
    {
        $offre = $this->model->getOfferById($id);
        if (!$offre) {
            http_response_code(404);
            return '404 - Offre non trouvée';
        }
        return $this->render('modifier-offre.html.twig', ['offre' => $offre]);
    }

    public function edit(int $id): void
    {
        $this->model->updateOffer($id, [
            'title'       => $_POST['title']       ?? '',
            'description' => $_POST['description'] ?? '',
            'salary'      => $_POST['salary']      ?? '',
            'type'        => $_POST['type']        ?? '',
            'mode'        => $_POST['mode']        ?? '',
        ]);
        $this->redirect('/offre/' . $id);
    }

    public function delete(int $id): void
    {
        $this->model->deleteOffer($id);
        $this->redirect('/offres');
    }
}
