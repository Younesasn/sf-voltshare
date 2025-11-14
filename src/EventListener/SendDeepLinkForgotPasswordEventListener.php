<?php

namespace App\EventListener;

use CoopTilleuls\ForgotPasswordBundle\Event\CreateTokenEvent;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[AsEventListener(CreateTokenEvent::class, method: 'onCreateToken')]
class SendDeepLinkForgotPasswordEventListener
{
    public function __construct(
        private readonly MailerInterface $mailer,
        #[Autowire('%env(MAILER_FROM)%')]
        private readonly string $mailerFrom,
    ) {
    }

    public function onCreateToken(CreateTokenEvent $event): void
    {
        $passwordToken = $event->getPasswordToken();
        $user = $passwordToken->getUser();
        $deepLink = sprintf('exp://127.0.0.1:8081/--/forgot-password/reset/%s', $passwordToken->getToken());

        $message = (new Email())
          ->from($this->mailerFrom)
          ->to($user->getEmail())
          ->subject('Reset your password')
          ->html(sprintf(
              'Bonjour,<br><br>
            Cliquez sur le lien suivant pour réinitialiser votre mot de passe :<br>
            <a href="%s">Réinitialiser mon mot de passe</a><br><br>
            Ou copiez ce lien et ouvrez-le manuellement :<br>
            <code>%1$s</code><br><br>',
              $deepLink
          ));
        $this->mailer->send($message);
    }
}
