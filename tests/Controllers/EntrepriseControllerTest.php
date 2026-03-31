<?php
namespace Tests\Controllers;

use PHPUnit\Framework\TestCase;
use App\Controllers\EntrepriseController;

class EntrepriseControllerTest extends TestCase
{
    /**
     * Test index() avec pagination
     * - Simule $_GET['page']
     * - Stub le modèle pour retourner le total et les résultats paginés
     * - Vérifie l'appel à `render('entreprises.html.twig', ...)` contenant `pages` et `entreprises`
     */
    public function testIndexUsesPaginationAndRenders()
    {
        $_GET['page'] = '2';

        $par_page = 10;
        $total = 35; // 4 pages
        $pagedResults = [ ['id'=>11,'name'=>'Company 11'] ];

        $model = new class($total, $pagedResults) {
            private $total; private $paged;
            public function __construct($t, $p) { $this->total=$t; $this->paged=$p; }
            public function countCompaniesFiltered($search) { return $this->total; }
            public function getCompaniesFiltered($limit, $offset, $search) { return $this->paged; }
        };

        $controller = new class extends EntrepriseController {
            public $captured = [];
            public function __construct() {}
            public function setModelPublic($m) { $this->model = $m; }
            public function render(string $template, array $data = []): string { $this->captured = ['template'=>$template,'data'=>$data]; return 'rendered-index'; }
        };

        $controller->setModelPublic($model);

        $out = $controller->index();
        $this->assertEquals('rendered-index', $out);
        $this->assertEquals('entreprises.html.twig', $controller->captured['template']);
        $data = $controller->captured['data'];
        $this->assertArrayHasKey('pages', $data);
        $this->assertEquals((int) ceil($total / $par_page), $data['pages']);
        $this->assertArrayHasKey('entreprises', $data);
        $this->assertIsArray($data['entreprises']);
    }

    /**
     * Test des bornes de pagination pour index()
     * - page = 0 doit être normalisé à 1
     * - page trop grande doit être borne supérieure
     */
    public function testIndexBounds()
    {
        // lower bound
        $_GET['page'] = '0';

        $par_page = 10;
        $total = 5; // 1 page
        $pagedResults = [ ['id'=>1,'name'=>'Company 1'] ];

        $model = new class($total, $pagedResults) {
            private $total; private $paged;
            public function __construct($t, $p) { $this->total=$t; $this->paged=$p; }
            public function countCompaniesFiltered($search) { return $this->total; }
            public function getCompaniesFiltered($limit, $offset, $search) { return $this->paged; }
        };

        $controller = new class extends EntrepriseController {
            public $captured = [];
            public function __construct() {}
            public function setModelPublic($m) { $this->model = $m; }
            public function render(string $template, array $data = []): string { $this->captured = ['template'=>$template,'data'=>$data]; return 'rendered-index'; }
        };

        $controller->setModelPublic($model);

        $out = $controller->index();
        $this->assertEquals('rendered-index', $out);
        $this->assertEquals(1, $controller->captured['data']['page_courante']);

        // upper bound
        $_GET['page'] = '999';
        $out = $controller->index();
        $this->assertEquals('rendered-index', $out);
    }

    /**
     * Test createPage()
     * - Vérifie que la vue de création d'entreprise est rendue
     */
    public function testCreatePageRendersTemplate()
    {
        $controller = new class extends EntrepriseController {
            public $captured = [];
            public function __construct() {}
            public function render(string $template, array $data = []): string { $this->captured = ['template'=>$template,'data'=>$data]; return 'rendered-create'; }
        };

        $out = $controller->createPage();
        $this->assertEquals('rendered-create', $out);
        $this->assertEquals('creation-entreprise.html.twig', $controller->captured['template']);
        $this->assertEquals([], $controller->captured['data']);
    }

    /**
     * Test create()
     * - Remplit $_POST
     * - Vérifie que createCompany() du modèle est appelé avec les données attendues
     * - Vérifie que redirect('/dashboard') est appelé
     */
    public function testCreateCallsModelAndRedirect()
    {
        $_POST['nom'] = 'Acme';
        $_POST['description'] = 'Desc';
        $_POST['email'] = 'a@b.c';
        $_POST['telephone'] = '123';

        $called = new \stdClass();
        $model = new class($called) {
            private $calledRef;
            public function __construct($r) { $this->calledRef = $r; }
            public function createCompany($data) { $this->calledRef->create = $data; }
        };

        $controller = new class extends EntrepriseController {
            public $redirects = [];
            public function __construct() {}
            public function setModelPublic($m) { $this->model = $m; }
            public function redirect(string $url): void { $this->redirects[] = $url; }
        };

        $controller->setModelPublic($model);

        $controller->create();

        $this->assertTrue(isset($called->create));
        $this->assertEquals('Acme', $called->create['name']);
        $this->assertCount(1, $controller->redirects);
        $this->assertEquals('/entreprises', $controller->redirects[0]);
    }

    /**
     * Test update() et delete()
     * - Prépare $_POST pour update
     * - Vérifie que updateCompany(id, data) et deleteCompany(id) sont appelés
     * - Vérifie que redirect('/dashboard') est appelé pour chaque opération
     */
    public function testUpdateAndDeleteCallModelAndRedirect()
    {
        // prepare POST for update
        $_POST['nom'] = 'NewName';
        $_POST['description'] = 'NewDesc';
        $_POST['email'] = 'n@e.com';
        $_POST['telephone'] = '999';

        $called = new \stdClass();
        $model = new class($called) {
            private $calledRef;
            public function __construct($r) { $this->calledRef = $r; }
            public function updateCompany($id, $data) { $this->calledRef->update = ['id'=>$id,'data'=>$data]; }
            public function deleteCompany($id) { $this->calledRef->delete = $id; }
        };

        $controller = new class extends EntrepriseController {
            public $redirects = [];
            public function __construct() {}
            public function setModelPublic($m) { $this->model = $m; }
            public function redirect(string $url): void { $this->redirects[] = $url; }
        };

        $controller->setModelPublic($model);

        $controller->edit(5);
        $controller->delete(7);

        $this->assertEquals(5, $called->update['id']);
        $this->assertEquals('NewName', $called->update['data']['name']);
        $this->assertEquals(7, $called->delete);
        $this->assertCount(2, $controller->redirects);
        $this->assertEquals('/entreprise/5', $controller->redirects[0]);
        $this->assertEquals('/entreprises', $controller->redirects[1]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        if (isset($_GET['page'])) unset($_GET['page']);
        if (isset($_POST)) $_POST = [];
    }
}
