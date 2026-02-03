<?php

namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class StationApiTest extends WebTestCase
{
    private const API_BASE_URL = '/api/stations';

    public function testGetStationsCollection(): void
    {
        $client = static::createClient();

        // Test de récupération de la collection de stations
        $client->request('GET', self::API_BASE_URL);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);

        // Vérification de la structure de la réponse API Platform
        $this->assertArrayHasKey('@context', $data);
        $this->assertArrayHasKey('@id', $data);
        $this->assertArrayHasKey('@type', $data);
        $this->assertArrayHasKey('member', $data);
        $this->assertIsArray($data['member']);
    }

    public function testGetStationById(): void
    {
        $client = static::createClient();

        // Test avec un ID existant (nécessite des fixtures)
        $client->request('GET', self::API_BASE_URL.'/1');

        // Si la station existe, vérifier la structure
        if (Response::HTTP_OK === $client->getResponse()->getStatusCode()) {
            $this->assertResponseIsSuccessful();
            $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

            $data = json_decode($client->getResponse()->getContent(), true);

            $this->assertArrayHasKey('@id', $data);
            $this->assertArrayHasKey('id', $data);
            $this->assertArrayHasKey('name', $data);
            $this->assertArrayHasKey('latitude', $data);
            $this->assertArrayHasKey('longitude', $data);
            $this->assertArrayHasKey('price', $data);
            $this->assertArrayHasKey('power', $data);
        } else {
            // Si la station n'existe pas, vérifier que c'est une 404
            $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        }
    }

    public function testGetStationByIdNotFound(): void
    {
        $client = static::createClient();

        // Test avec un ID inexistant
        $client->request('GET', self::API_BASE_URL.'/99999');

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCreateStationRequiresAuthentication(): void
    {
        $client = static::createClient();

        // Tentative de création sans authentification
        $client->request('POST', self::API_BASE_URL, [
            'name' => 'Test Station',
            'latitude' => 45.7621,
            'longitude' => 4.8779,
            'adress' => '123 Test Street',
            'price' => 5.0,
            'power' => 22.0,
            'description' => 'Test description',
            'defaultMessage' => 'Test message',
        ]);

        // Devrait retourner 401 Unauthorized
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testGetStationsWithActiveFilter(): void
    {
        $client = static::createClient();

        // Test avec filtre isActive=true
        $client->request('GET', self::API_BASE_URL.'?isActive=true');

        $this->assertResponseIsSuccessful();

        $data = json_decode($client->getResponse()->getContent(), true);

        if (isset($data['member']) && is_array($data['member'])) {
            // Vérifier que toutes les stations retournées sont actives
            foreach ($data['member'] as $station) {
                if (isset($station['active'])) {
                    $this->assertTrue($station['active']);
                }
            }
        }
    }
}
