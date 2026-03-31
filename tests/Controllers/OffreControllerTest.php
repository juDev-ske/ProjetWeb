<?php
namespace Tests\Controllers;

use PHPUnit\Framework\TestCase;
use App\Controllers\OffreController;
use App\Models\OffreModel;

class OffreControllerTest extends TestCase
{
    /**
     * Test getLatestOffers()
     * - Prépare une liste de 8 offres
     * - Injecte un modèle factice retournant ces offres
     * - Vérifie que la méthode renvoie au plus 6 éléments (slice)
     */
    public function testGetLatestOffersReturnsSixOrLess()
    {
        // Prepare a list of 8 offers to ensure slicing works
        $offers = [];
        for ($i = 1; $i <= 8; $i++) {
            $offers[] = ['id' => $i, 'title' => "Offer $i"];
        }

        // Use a lightweight stub to avoid DB access and ensure deterministic output
        $model = new class($offers) {
            private $offers;
            public function __construct($offers) { $this->offers = $offers; }
            public function getAllOffers() { return $this->offers; }
        };

        // Create controller without running its constructor (avoids real DB model creation)
        $controller = $this->getMockBuilder(OffreController::class)
            ->disableOriginalConstructor()
            ->onlyMethods([]) // do not mock any methods so original methods execute
            ->getMock();

        // Inject the mocked model into the protected property using reflection
        $ref = new \ReflectionClass('App\\Controllers\\Controller');
        $prop = $ref->getProperty('model');
        $prop->setAccessible(true);
        $prop->setValue($controller, $model);

        $result = $controller->getLatestOffers();

        $this->assertIsArray($result);
        $this->assertCount(6, $result);
        $this->assertEquals('Offer 1', $result[0]['title']);
        $this->assertEquals('Offer 6', $result[5]['title']);
    }

    /**
     * Test index() avec pagination
     * - Simule $_GET['page'] = 2
     * - Stub le modèle pour fournir le total et les résultats paginés
     * - Vérifie que render() est appelé avec les bonnes clés (pages, offres)
     */
    public function testIndexUsesPaginationAndRenders()
    {
        // Simulate page 2
        $_GET['page'] = '2';

        // Stub model to provide counts and paginated results
        $par_page = 10;
        $total = 35; // 4 pages
        $pagedResults = [ ['id'=>11,'title'=>'Offer 11'] ];

        $model = new class($total, $pagedResults) {
            private $total; private $paged;
            public function __construct($t, $p) { $this->total=$t; $this->paged=$p; }
            public function countAllOffers() { return $this->total; }
            public function getOffersPaginated($limit, $offset) { return $this->paged; }
        };

            // Crée un mock du contrôleur en remplaçant `render` pour capturer les données passées
        $controller = $this->getMockBuilder(OffreController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['render'])
            ->getMock();

        $ref = new \ReflectionClass('App\\Controllers\\Controller');
        $prop = $ref->getProperty('model');
        $prop->setAccessible(true);
        $prop->setValue($controller, $model);

        $controller->expects($this->once())->method('render')
            ->with('offres.html.twig', $this->callback(function($data) use ($total, $par_page) {
                // pages = ceil(total/par_page)
                return isset($data['pages']) && $data['pages'] === (int) ceil($total / $par_page)
                    && isset($data['offres']) && is_array($data['offres']);
            }))
            ->willReturn('rendered-index');

        $out = $controller->index();
        $this->assertEquals('rendered-index', $out);
    }

    /**
     * Test show() quand l'offre n'existe pas
     * - Le modèle retourne null
     * - On attend la chaîne d'erreur exacte et le code HTTP 404
     */
    public function testShowNotFoundReturns404String()
    {
        // Stub model returning null for offer
        $model = new class {
            public function getOfferById($id) { return null; }
        };

        $controller = $this->getMockBuilder(OffreController::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $ref = new \ReflectionClass('App\\Controllers\\Controller');
        $prop = $ref->getProperty('model');
        $prop->setAccessible(true);
        $prop->setValue($controller, $model);

        $result = $controller->show(9999);
        $this->assertEquals('404 - Offre non trouvée', $result);
        $this->assertEquals(404, http_response_code());
    }

    /**
     * Test getLatestOffers() avec moins de 6 offres
     * - Vérifie que la méthode retourne toutes les offres disponibles (pas d'erreur de slicing)
     */
    public function testGetLatestOffersWithFewerThanSix()
    {
        $offers = [];
        for ($i = 1; $i <= 3; $i++) {
            $offers[] = ['id' => $i, 'title' => "Offer $i"];
        }

        $model = new class($offers) {
            private $offers;
            public function __construct($offers) { $this->offers = $offers; }
            public function getAllOffers() { return $this->offers; }
        };

        $controller = $this->getMockBuilder(OffreController::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $ref = new \ReflectionClass('App\\Controllers\\Controller');
        $prop = $ref->getProperty('model');
        $prop->setAccessible(true);
        $prop->setValue($controller, $model);

        $result = $controller->getLatestOffers();

        $this->assertIsArray($result);
        $this->assertCount(3, $result);
        $this->assertEquals('Offer 1', $result[0]['title']);
        $this->assertEquals('Offer 3', $result[2]['title']);
    }

    /**
     * Test index() bornes de page
     * - Vérifie que page < 1 devient 1
     * - Vérifie que page > pages devient pages (borne supérieure)
     * - Rend attendu via la méthode render()
     */
    public function testIndexPageLowerAndUpperBounds()
    {
        // Test page lower bound (0 -> 1)
        $_GET['page'] = '0';

        $par_page = 10;
        $total = 5; // 1 page
        $pagedResults = [ ['id'=>1,'title'=>'Offer 1'] ];

        $model = new class($total, $pagedResults) {
            private $total; private $paged;
            public function __construct($t, $p) { $this->total=$t; $this->paged=$p; }
            public function countAllOffers() { return $this->total; }
            public function getOffersPaginated($limit, $offset) { return $this->paged; }
        };

        $controller = $this->getMockBuilder(OffreController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['render'])
            ->getMock();

        $ref = new \ReflectionClass('App\\Controllers\\Controller');
        $prop = $ref->getProperty('model');
        $prop->setAccessible(true);
        $prop->setValue($controller, $model);

        $controller->expects($this->exactly(2))->method('render')
            ->with('offres.html.twig', $this->callback(function($data) use ($total, $par_page) {
                return isset($data['pages']) && $data['pages'] === (int) ceil($total / $par_page)
                    && isset($data['page_courante']) && $data['page_courante'] === 1
                    && isset($data['offres']) && is_array($data['offres']);
            }))
            ->willReturn('rendered-index');

        $out = $controller->index();
        $this->assertEquals('rendered-index', $out);

        // Test page upper bound (too large -> pages)
        $_GET['page'] = '999';
        $out = $controller->index();
        $this->assertEquals('rendered-index', $out);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        if (isset($_GET['page'])) {
            unset($_GET['page']);
        }
        http_response_code(200);
    }

    /**
     * Test show() quand l'offre existe
     * - Le modèle retourne l'offre, ses compétences et le nombre de candidatures
     * - Vérifie que render est appelé avec l'offre enrichie (`competences`, `nb_candidatures`)
     */
    public function testShowRendersDetailWhenFound()
    {
        $offer = ['id'=>1,'title'=>'O1'];
        $skills = [ ['name'=>'PHP'], ['name'=>'MySQL'] ];

        $model = new class($offer, $skills) {
            private $off; private $skills;
            public function __construct($o,$s){$this->off=$o;$this->skills=$s;}
            public function getOfferById($id){return $this->off;}
            public function getOfferSkills($id){return $this->skills;}
            public function getOfferApplicationCount($id){return 2;}
        };

        $controller = $this->getMockBuilder(OffreController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['render'])
            ->getMock();

        $ref = new \ReflectionClass('App\\Controllers\\Controller');
        $prop = $ref->getProperty('model');
        $prop->setAccessible(true);
        $prop->setValue($controller, $model);

        $controller->expects($this->once())->method('render')
            ->with('offre-detail.html.twig', $this->callback(function($data){
                return isset($data['offre']) && isset($data['offre']['competences'])
                    && $data['offre']['nb_candidatures'] === 2;
            }))
            ->willReturn('rendered-detail');

        $out = $controller->show(1);
        $this->assertEquals('rendered-detail', $out);
    }
}