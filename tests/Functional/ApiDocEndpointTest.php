<?php
/**
 * © 2025 pet.restaurateur — Licensed under AGPL-3.0-or-later.
 * See LICENSE file or https://www.gnu.org/licenses/agpl-3.0.html for details.
 */

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ApiDocEndpointTest extends WebTestCase
{
    public function testApiDocHtmlResponse(): void
    {
        $client = ApiDocEndpointTest::createClient();
        $client->request('GET', '/api/doc');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'text/html; charset=UTF-8');
        $this->assertStringContainsString('<html', $client->getResponse()->getContent());
    }

    public function testApiDocJsonResponse(): void
    {
        $client = ApiDocEndpointTest::createClient();
        $client->request('GET', '/api/doc.json');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $this->assertJson($client->getResponse()->getContent());
    }
}
