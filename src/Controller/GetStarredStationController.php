<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class GetStarredStationController extends AbstractController
{
    public function __invoke(Security $security): JsonResponse
    {
        $user = $security->getUser();

        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $stations = $user->getStationStarred();

        return $this->json($stations);
    }
}
