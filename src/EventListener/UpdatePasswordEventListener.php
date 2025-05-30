<?php

namespace App\EventListener;

use ApiPlatform\Validator\ValidatorInterface;
use CoopTilleuls\ForgotPasswordBundle\Event\UpdatePasswordEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsEventListener(UpdatePasswordEvent::class, method: "onUpdatePassword")]
class UpdatePasswordEventListener
{
  public function __construct(private EntityManagerInterface $em, private UserPasswordHasherInterface $hasher, private ValidatorInterface $validator)
  {
  }
  public function onUpdatePassword(UpdatePasswordEvent $event): void
  {
    $passwordToken = $event->getPasswordToken();
    $user = $passwordToken->getUser();
    $password = $event->getPassword();
    $user->setPassword($this->hasher->hashPassword($user, $password));
    $this->validator->validate($user);
    $this->em->persist($user);
    $this->em->flush();
  }
}
