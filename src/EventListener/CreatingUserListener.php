<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsEntityListener(Events::prePersist, method: 'prePersist', entity: User::class)]
class CreatingUserListener
{
  public function __construct(private UserPasswordHasherInterface $hasher)
  {
  }
  public function prePersist(User $user): void
  {
    $user
      ->setIsDeleted(false)
      ->setPassword($this->hasher->hashPassword($user, $user->getPassword()));
  }
}
