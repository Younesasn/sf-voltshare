<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Events;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsDoctrineListener(Events::prePersist)]
class HashPasswordUserListener
{
  public function __construct(private UserPasswordHasherInterface $hasher,)
  {
  }
  public function prePersist(object $event): void
  {
    $entity = $event->getObject();

    if (!$entity instanceof User) {
      return;
    }

    $entity
      ->setPassword($this->hasher->hashPassword($entity, $entity->getPassword()));
  }
}