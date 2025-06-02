<?php
/**
 * © 2025 pet.restaurateur — Licensed under AGPL-3.0-or-later.
 * See LICENSE file or https://www.gnu.org/licenses/agpl-3.0.html for details.
 */

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\HttpFoundation\Response;

final class RestaurantControllerTest extends WebTestCase
{
    private const string BASE_URI = '/api/v1/restaurants';

    private ?AbstractBrowser $client = null;

    private array $validRestaurantData = [
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

    protected function setUp(): void
    {
        $this->client = $this->createClient();
    }

    public function testCreateRestaurant(): void
    {
        $responseData = $this->createRestaurant($this->validRestaurantData);

        $this->assertArrayHasKey('id', $responseData);
        $this->assertIsString($responseData['id']);
        $this->assertNotEmpty($responseData['id']);

        unset($responseData['id']);
        $this->assertEquals($this->validRestaurantData, $responseData);
    }

    public function testGetRestaurant(): void
    {
        $createdRestaurant = $this->createRestaurant($this->validRestaurantData);

        $this->client->request('GET', self::BASE_URI.'/'.$createdRestaurant['id']);

        $this->assertResponseIsSuccessful();
        $responseData = $this->getResponseData();

        $this->assertEquals($createdRestaurant, $responseData);
    }

    public function testUpdateRestaurant(): void
    {
        $createdRestaurant = $this->createRestaurant($this->validRestaurantData);

        $updateData = [
            'description' => 'Updated description',
            'address' => '456 Updated St',
            'coordinates' => [
                'latitude' => 46.0,
                'longitude' => -74.5,
            ],
            'phone' => '+9876543210',
            'working_hours' => [
                [
                    'day_of_week' => 2,
                    'open_time' => '10:00',
                    'close_time' => '20:00',
                ],
            ],
        ];

        $this->client->jsonRequest(
            'POST',
            self::BASE_URI.'/'.$createdRestaurant['id'],
            $updateData
        );

        $this->assertResponseIsSuccessful();
        $responseData = $this->getResponseData();

        $this->assertEquals($createdRestaurant['id'], $responseData['id']);
        $this->assertEquals($this->validRestaurantData['name'], $responseData['name']);

        unset($responseData['id'], $responseData['name']);
        $this->assertEquals($updateData, $responseData);
    }

    public function testDeleteRestaurant(): void
    {
        $createdRestaurant = $this->createRestaurant($this->validRestaurantData);

        $this->client->request('DELETE', self::BASE_URI.'/'.$createdRestaurant['id']);
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        $this->client->request('GET', self::BASE_URI.'/'.$createdRestaurant['id']);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testGetNonExistentRestaurant(): void
    {
        $nonExistentId = '00000000-0000-0000-0000-000000000000';
        $this->client->request('GET', self::BASE_URI.'/'.$nonExistentId);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCreateRestaurantWithInvalidData(): void
    {
        $invalidData = [
            'name' => '',
            'address' => '123 Test St',
        ];

        $this->client->jsonRequest('POST', self::BASE_URI, $invalidData);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);

        $responseData = $this->getResponseData();
        $this->assertArrayHasKey('violations', $responseData);
    }

    private function createRestaurant(array $data): array
    {
        $this->client->jsonRequest('POST', self::BASE_URI, $data);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        return $this->getResponseData();
    }

    private function getResponseData(): array
    {
        $this->assertResponseFormatSame('json');

        return json_decode($this->client->getResponse()->getContent(), true);
    }
}
