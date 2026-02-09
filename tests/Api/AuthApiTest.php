<?php

namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests basiques liés à l'authentification de l'API.
 *
 * ils vérifient que l'API reste protégée quand on n'est pas connecté.
 */
class AuthApiTest extends WebTestCase
{
    public function testAccessApiRootWithoutAuthenticationIsDenied(): void
    {
        $client = static::createClient();

        // Tentative d'accès à la racine de l'API sans JWT
        $client->request('GET', '/api');

        $statusCode = $client->getResponse()->getStatusCode();

        $this->assertTrue(
            \in_array($statusCode, [Response::HTTP_UNAUTHORIZED, Response::HTTP_FORBIDDEN], true),
            sprintf('Accès à /api sans authentification doit être refusé, code reçu: %d', $statusCode)
        );
    }

    public function testAccessProtectedEndpointWithoutTokenIsDenied(): void
    {
        $client = static::createClient();

        // Exemple de ressource protégée derrière le firewall "api"
        $client->request('GET', '/api/reservations');

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
}
