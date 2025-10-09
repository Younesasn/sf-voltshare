<?php

namespace App\EventListener;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Pontedilana\PhpWeasyPrint\Pdf;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;
use Twig\Environment;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: Reservation::class)]
class GenerateInvoicePdfListener
{
    public function __construct(
        private MailerInterface $mailer,
        private Pdf $pdf,
        private Environment $twig,
        #[Autowire('%kernel.project_dir%/var/pdfs')]
        private $exportDir,
    ) {
    }

    public function postPersist(Reservation $reservation)
    {
        $client = $reservation->getUser();
        $date = (new \DateTime())->format('Y-m-d');
        $filename = sprintf(
            'facture-%d-%s-%s-%s.pdf',
            $reservation->getId(),
            strtolower($client->getLastname()),
            strtolower($client->getFirstname()),
            $date
        );
        $filename = preg_replace('/[^a-zA-Z0-9-_\\.]/', '', $filename);
        $filepath = $this->exportDir.'/'.$filename;

        $this->pdf->generateFromHtml($this->twig->render('/pdf.html.twig', [
            'reservation' => $reservation,
            'date' => new \DateTime(),
            'timestamp' => new \DateTime()->getTimestamp(),
        ]), $filepath);

        $email = (new Email())->addPart(new DataPart(new File($filepath)))
          ->from('noreply@voltshare.com')
          ->to($reservation->getUser()->getEmail())
          ->subject('Votre facture')
          ->text('Vous trouverez ci-joint votre facture pour votre réservation.');
        $this->mailer->send($email);

        unlink($filepath);
    }
}
