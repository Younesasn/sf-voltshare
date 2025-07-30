<?php

namespace App\EventListener;

use App\Entity\Station;
use App\Repository\ConversationRepository;
use App\Repository\StationRepository;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::preRemove, method: "preRemove", entity: Station::class)]
class DeleteStationListener
{

  public function __construct(
    private EntityManagerInterface $em,
    private StationRepository $stationRepository,
    private ConversationRepository $conversationRepository
  ) {
  }
  public function preRemove(Station $station): void
  {
    $reservations = $station->getReservations();

    foreach ($reservations as $reservation) {
      $conversations = $this->conversationRepository->findByReservation($reservation);
      foreach ($conversations as $conversation) {
        $messages = $conversation->getMessages();
        foreach ($messages as $message) {
          $this->em->remove($message);
        }
        $this->em->remove($conversation);
      }
    }

    $deletedStation = $this->stationRepository->findOneByName("Borne Supprimée");
    foreach ($reservations as $reservation) {
      $reservation->setStation($deletedStation);
    }
  }
}
