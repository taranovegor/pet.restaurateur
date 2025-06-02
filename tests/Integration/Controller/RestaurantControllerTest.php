<?php
/**
 * © 2025 pet.restaurateur — Licensed under AGPL-3.0-or-later.
 * See LICENSE file or https://www.gnu.org/licenses/agpl-3.0.html for details.
 */

namespace App\Tests\Integration\Controller;

use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class RestaurantControllerTest extends WebTestCase
{
    private RestaurantRepository $repository;
    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get(RestaurantRepository::class);

        foreach ($this->repository->findAll() as $restaurant) {
            static::getContainer()->get(EntityManagerInterface::class)->remove($restaurant);
        }
    }

    public function testFullRestaurantLifecycle(): void
    {
        $restaurantData = $this->getValidRestaurantData();
        $this->client->jsonRequest('POST', '/api/v1/restaurants', $restaurantData);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $response = $this->client->getResponse();
        $createdRestaurant = json_decode($response->getContent(), true);
        $restaurantId = $createdRestaurant['id'];

        $dbRestaurant = $this->repository->find(Uuid::fromString($restaurantId));
        $this->assertNotNull($dbRestaurant);
        $this->assertEquals($restaurantData['name'], $dbRestaurant->getName());
        $this->assertCount(1, $dbRestaurant->getWorkingHours());

        $this->client->jsonRequest('GET', "/api/v1/restaurants/$restaurantId");
        $this->assertResponseIsSuccessful();
        $retrievedRestaurant = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertEquals($createdRestaurant, $retrievedRestaurant);

        $updateData = [
            'description' => 'Updated description',
            'address' => '456 Updated St',
            'coordinates' => ['latitude' => 46.0, 'longitude' => -74.5],
            'phone' => '+9876543210',
            'working_hours' => [
                [
                    'day_of_week' => 2,
                    'open_time' => '10:00',
                    'close_time' => '20:00',
                ],
            ],
        ];

        $this->client->jsonRequest('POST', "/api/v1/restaurants/$restaurantId", $updateData);
        $this->assertResponseIsSuccessful();

        $updatedDbRestaurant = $this->repository->find(Uuid::fromString($restaurantId));
        $this->assertEquals($updateData['description'], $updatedDbRestaurant->getDescription());
        $this->assertCount(1, $updatedDbRestaurant->getWorkingHours());

        $this->client->jsonRequest('DELETE', "/api/v1/restaurants/$restaurantId");
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        $this->client->jsonRequest('GET', "/api/v1/restaurants/$restaurantId");
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testValidationErrors(): void
    {
        $invalidData = $this->getValidRestaurantData();
        $invalidData['coordinates']['latitude'] = 100; // Invalid value

        $this->client->jsonRequest('POST', '/api/v1/restaurants', $invalidData);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('violations', $response);
        $this->assertStringContainsString('latitude', $response['detail']);
    }

    public function testNotFoundScenarios(): void
    {
        $nonExistentId = Uuid::v7();

        $this->client->jsonRequest('GET', "/api/v1/restaurants/$nonExistentId");
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);

        $this->client->jsonRequest('POST', "/api/v1/restaurants/$nonExistentId");
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->client->jsonRequest('POST', "/api/v1/restaurants/$nonExistentId", $this->getValidRestaurantData());
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);

        $this->client->jsonRequest('DELETE', "/api/v1/restaurants/$nonExistentId");
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    private function getValidRestaurantData(): array
    {
        return [
            'name' => 'Test Restaurant',
            'description' => 'Test description',
            'address' => '123 Test St',
            'coordinates' => [
                'latitude' => 45.0,
                'longitude' => -73.5,
            ],
            'phone' => '+1234567890',
            'working_hours' => [
                [
                    'day_of_week' => 1,
                    'open_time' => '09:00',
                    'close_time' => '18:00',
                ],
            ],
        ];
    }
}
