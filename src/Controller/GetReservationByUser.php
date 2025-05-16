<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class GetReservationByUser extends AbstractController
{
  public function __invoke(Security $security, EntityManagerInterface $em): JsonResponse
  {
    $user = $security->getUser();

    if (!$user) {
      return $this->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
    }
    
    $reservations = $em->getRepository(Reservation::class)->findByUser($user);

    return $this->json($reservations);
  }
}