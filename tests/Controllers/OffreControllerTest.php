<?php
namespace Tests\Controllers;

use PHPUnit\Framework\TestCase;
use App\Controllers\OffreController;
use App\Models\OffreModel;

class OffreControllerTest extends TestCase
{
    public function testGetLatestOffersReturnsSixOrLess()
    {
        // Prepare a list of 8 offers to ensure slicing works
        $offers = [];
        for ($i = 1; $i <= 8; $i++) {
            $offers[] = ['id' => $i, 'title' => "Offer $i"];
        }

        // Mock the model to avoid DB access
        $model = $this->createMock(OffreModel::class);
        $model->method('getAllOffers')->willReturn($offers);

        // Mock Twig environment (not used by getLatestOffers)
        $twig = $this->createMock(\Twig\Environment::class);

        $controller = new OffreController($twig);

        // Inject the mocked model
        $controller->model = $model;

        $result = $controller->getLatestOffers();

        $this->assertIsArray($result);
        $this->assertCount(6, $result);
        $this->assertEquals('Offer 1', $result[0]['title']);
        $this->assertEquals('Offer 6', $result[5]['title']);
    }
}
