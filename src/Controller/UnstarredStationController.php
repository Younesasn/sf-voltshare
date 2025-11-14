<?php

namespace App\Controller;

use App\Entity\Station;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UnstarredStationController extends AbstractController
{
    public function __invoke(Station $station, Security $security, EntityManagerInterface $em): JsonResponse
    {
        $user = $security->getUser();

        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        if (!$station) {
            return $this->json(['error' => 'Station not found'], Response::HTTP_NOT_FOUND);
        }

        if (!$user->getStationStarred()->contains($station)) {
            return $this->json(['error' => 'Station already unstarred'], Response::HTTP_BAD_REQUEST);
        }

        $user->removeStationStarred($station);
        $em->persist($user);
        $em->flush();

        return $this->json([
            'success' => true,
            'message' => 'Successfully removed from favourites',
        ]);
    }
}
