<?php

namespace App\EventListener;

use CoopTilleuls\ForgotPasswordBundle\Event\CreateTokenEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[AsEventListener(CreateTokenEvent::class, method: "onCreateToken")]
class SendDeepLinkForgotPasswordEventListener
{
  public function __construct(private readonly MailerInterface $mailer)
  {
  }

  public function onCreateToken(CreateTokenEvent $event): void
  {
    $passwordToken = $event->getPasswordToken();
    $user = $passwordToken->getUser();

    $message = (new Email())
      ->from('noreply@voltshare.com')
      ->to($user->getEmail())
      ->subject('Reset your password')
      ->html(
        sprintf('exp://127.0.0.1:8081/--/forgot-password/%s', $passwordToken->getToken())
      );
    $this->mailer->send($message);
  }
}