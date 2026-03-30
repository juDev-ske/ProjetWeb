<?php
namespace App\Controllers;

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
        $par_page    = 10;
        $page        = max(1, (int) ($_GET['page'] ?? 1));
        $total       = $this->model->countAllCompanies();
        $pages       = max(1, (int) ceil($total / $par_page));
        $page        = min($page, $pages);
        $offset      = ($page - 1) * $par_page;

        return $this->render('entreprises.html.twig', [
            'entreprises'   => $this->model->getCompaniesPaginated($par_page, $offset),
            'pages'         => $pages,
            'page_courante' => $page,
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
        $this->redirect('/dashboard');
    }

    public function update(int $id): void
    {
        $this->model->updateCompany($id, [
            'name'        => $_POST['nom']         ?? '',
            'description' => $_POST['description'] ?? '',
            'email'       => $_POST['email']       ?? '',
            'phone'       => $_POST['telephone']   ?? '',
        ]);
        $this->redirect('/dashboard');
    }

    public function delete(int $id): void
    {
        $this->model->deleteCompany($id);
        $this->redirect('/dashboard');
    }
}
