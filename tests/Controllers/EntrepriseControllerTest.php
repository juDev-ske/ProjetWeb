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
            public function countAllCompanies() { return $this->total; }
            public function getCompaniesPaginated($limit, $offset) { return $this->paged; }
        };

        $controller = $this->getMockBuilder(EntrepriseController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['render'])
            ->getMock();

        $ref = new \ReflectionClass('App\\Controllers\\Controller');
        $prop = $ref->getProperty('model');
        $prop->setAccessible(true);
        $prop->setValue($controller, $model);

        $controller->expects($this->once())->method('render')
            ->with('entreprises.html.twig', $this->callback(function($data) use ($total, $par_page) {
                return isset($data['pages']) && $data['pages'] === (int) ceil($total / $par_page)
                    && isset($data['entreprises']) && is_array($data['entreprises']);
            }))
            ->willReturn('rendered-index');

        $out = $controller->index();
        $this->assertEquals('rendered-index', $out);
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
            public function countAllCompanies() { return $this->total; }
            public function getCompaniesPaginated($limit, $offset) { return $this->paged; }
        };

        $controller = $this->getMockBuilder(EntrepriseController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['render'])
            ->getMock();

        $ref = new \ReflectionClass('App\\Controllers\\Controller');
        $prop = $ref->getProperty('model');
        $prop->setAccessible(true);
        $prop->setValue($controller, $model);

        $controller->expects($this->exactly(2))->method('render')
            ->with('entreprises.html.twig', $this->callback(function($data) {
                return isset($data['page_courante']) && $data['page_courante'] === 1;
            }))
            ->willReturn('rendered-index');

        $out = $controller->index();
        $this->assertEquals('rendered-index', $out);

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
        $controller = $this->getMockBuilder(EntrepriseController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['render'])
            ->getMock();

        $controller->expects($this->once())->method('render')
            ->with('creation-entreprise.html.twig', [])
            ->willReturn('rendered-create');

        $out = $controller->createPage();
        $this->assertEquals('rendered-create', $out);
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

        $controller = $this->getMockBuilder(EntrepriseController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['redirect'])
            ->getMock();

        $ref = new \ReflectionClass('App\\Controllers\\Controller');
        $prop = $ref->getProperty('model');
        $prop->setAccessible(true);
        $prop->setValue($controller, $model);

        $controller->expects($this->once())->method('redirect')->with('/dashboard');

        $controller->create();

        $this->assertTrue(isset($called->create));
        $this->assertEquals('Acme', $called->create['name']);
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

        $controller = $this->getMockBuilder(EntrepriseController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['redirect'])
            ->getMock();

        $ref = new \ReflectionClass('App\\Controllers\\Controller');
        $prop = $ref->getProperty('model');
        $prop->setAccessible(true);
        $prop->setValue($controller, $model);

        $controller->expects($this->exactly(2))->method('redirect')->with('/dashboard');

        $controller->update(5);
        $controller->delete(7);

        $this->assertEquals(5, $called->update['id']);
        $this->assertEquals('NewName', $called->update['data']['name']);
        $this->assertEquals(7, $called->delete);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        if (isset($_GET['page'])) unset($_GET['page']);
        if (isset($_POST)) $_POST = [];
    }
}
