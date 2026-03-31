<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\EntrepriseModel;

class EntrepriseController extends Controller
{
    public function __construct($twig)
    {
        parent::__construct($twig);
        $this->model = new EntrepriseModel();
    }

    public function index(): string
    {
        $par_page = 10;
        $search   = $_GET['search'] ?? '';
        $page     = max(1, (int) ($_GET['page'] ?? 1));
        $total    = $this->model->countCompaniesFiltered($search);
        $pages    = max(1, (int) ceil($total / $par_page));
        $page     = min($page, $pages);
        $offset   = ($page - 1) * $par_page;

        return $this->render('entreprises.html.twig', [
            'entreprises'   => $this->model->getCompaniesFiltered($par_page, $offset, $search),
            'pages'         => $pages,
            'page_courante' => $page,
            'search'        => $search,
        ]);
    }

    public function show(int $id): string
    {
        $entreprise = $this->model->getCompanyById($id);
        if (!$entreprise) {
            http_response_code(404);
            return '404 - Entreprise non trouvée';
        }
        return $this->render('entreprise-detail.html.twig', [
            'entreprise' => $entreprise,
            'offres'     => $this->model->getOffersByCompany($id),
        ]);
    }

    public function createPage(): string
    {
        return $this->render('creation-entreprise.html.twig');
    }

    public function create(): void
    {
        $this->model->createCompany([
            'name'        => $_POST['nom']         ?? '',
            'description' => $_POST['description'] ?? '',
            'email'       => $_POST['email']       ?? '',
            'phone'       => $_POST['telephone']   ?? '',
        ]);
        $this->redirect('/entreprises');
    }

    public function editPage(int $id): string
    {
        $entreprise = $this->model->getCompanyById($id);
        if (!$entreprise) {
            http_response_code(404);
            return '404 - Entreprise non trouvée';
        }
        return $this->render('modifier-entreprise.html.twig', ['entreprise' => $entreprise]);
    }

    public function edit(int $id): void
    {
        $this->model->updateCompany($id, [
            'name'        => $_POST['nom']         ?? '',
            'description' => $_POST['description'] ?? '',
            'email'       => $_POST['email']       ?? '',
            'phone'       => $_POST['telephone']   ?? '',
        ]);
        $this->redirect('/entreprise/' . $id);
    }

    public function rate(int $id): void
    {
        $note = max(1, min(5, (float) ($_POST['note'] ?? 0)));
        $this->model->updateRating($id, $note);
        $this->redirect('/entreprise/' . $id);
    }

    public function delete(int $id): void
    {
        $this->model->deleteCompany($id);
        $this->redirect('/entreprises');
    }
}
