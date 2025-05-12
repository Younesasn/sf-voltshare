<?php

namespace App\Controller;

use App\Entity\Station;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class StarredStationController extends AbstractController
{
  public function __invoke(Station $station, Security $security, EntityManagerInterface $em): JsonResponse
  {
    $user = $security->getUser();

    if (!$user) {
      return $this->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
    }

    if(!$station) {
      return $this->json(["error" => "Station not found"], Response::HTTP_NOT_FOUND);
    }

    if($user->getStationStarred()->contains($station)) {
      return $this->json(["error" => "Station already starred"], Response::HTTP_BAD_REQUEST);
    }

    $user->addStationStarred($station);
    $em->persist($user); 
    $em->flush();

    return $this->json([
      "success" => true,
      "stationStarred" => $station->getId()
    ]);
  }
}