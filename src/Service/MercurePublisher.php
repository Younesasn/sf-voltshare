<?php

namespace App\Service;

use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class MercurePublisher
{
    public function __construct(
        private readonly HubInterface $hub,
    ) {
    }

    public function publish(string $topic, array $data): void
    {
        $update = new Update(
            $topic,
            json_encode($data)
        );

        $this->hub->publish($update);
    }
}
