<?php

namespace App\EventListener;

use App\Entity\Car;
use App\Repository\CarRepository;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::preRemove, method: "preRemove", entity: Car::class)]
class DeleteCarListener
{
  const ANONYMOUS = "Anonymous";
  public function __construct(private CarRepository $carRepository)
  {
  }

  public function preRemove(Car $car): void
  {
    $anonymous = $this->carRepository->findOneByModel(self::ANONYMOUS);
    foreach ($car->getReservations() as $reservation) {
      $reservation->setCar($anonymous);
    }
  }
}
