<?php

namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReservationApiTest extends WebTestCase
{
    private const API_BASE_URL = '/api/reservations';

    public function testCreateReservationWithoutRequiredFieldsShouldFail(): void
    {
        $client = static::createClient();

        // Tentative de création avec un payload incomplet
        $client->request('POST', self::API_BASE_URL, [
            'price' => 10.5,
        ]);

        $this->assertFalse(
            $client->getResponse()->isSuccessful(),
            'La création de réservation avec un payload incomplet ne devrait pas réussir.'
        );
    }
}
