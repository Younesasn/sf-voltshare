<?php

namespace App\Controller;

use App\Entity\Station;
use avadim\FastExcelWriter\Excel;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ExportDataController extends AbstractController
{
  /**
   * Helper pour "slugifier" (remplacer espaces/accents par _)
   * @param mixed $text
   * @return string
   */
  private function slugify($text)
  {
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    $text = preg_replace('~[^\w]+~', '_', $text);
    $text = trim($text, '_');
    $text = strtolower($text);
    return $text;
  }

  public function __invoke(Station $station, MailerInterface $mailer)
  {
    $exportDir = $this->getParameter('kernel.project_dir') . '/var/exports';
    Excel::setTempDir($exportDir);

    $stationName = $this->slugify($station->getName());
    $date = (new DateTime())->format('Y-m-d_H-i');
    $filename = "export_{$stationName}_{$date}.xlsx";
    $filepath = $exportDir . '/' . $filename;

    $excel = Excel::create([$station->getName()]);
    $sheet = $excel->getSheet();

    $sheet->writeHeader([
      "Jour de la réservation" => "@datetime",
      "Début de la réservation" => "@datetime",
      "Fin de la réservation" => "@datetime",
      "Client" => "@string",
      "Voiture du client" => "@string",
      "Prix payé" => "@money"
    ]);

    foreach ($station->getReservations() as $reservation) {
      $format = "l j F Y H:i";
      $data = [
        $reservation->getDate()->format("l j F Y"),
        $reservation->getStartTime()->format($format),
        $reservation->getEndTime()->format($format),
        $reservation->getUser()->getEmail(),
        $reservation->getCar()->getModel(),
        $reservation->getPrice()
      ];
      $sheet->writeRow($data);
    }

    $colCount = 6;
    for ($i = 0; $i < $colCount; $i++) {
      $colLetter = chr(832 + $i);
      $sheet->setColAutoWidth($colLetter);
    }
    $excel->save($filepath);

    $email = (new Email())->addPart(new DataPart(new File($filepath)))
      ->from('noreply@voltshare.com')
      ->to($reservation->getUser()->getEmail())
      ->subject('Vos données en format Excel')
      ->text('Vous trouverez ci-joint le fichier demandé sur Voltshare.');
    $mailer->send($email);

    unlink($filepath);

    return $this->json(["success" => "Data Sent correctly !"]);
  }
}
