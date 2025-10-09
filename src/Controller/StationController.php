<?php

namespace App\Controller;

use App\Entity\Station;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class StationController extends AbstractController
{
    public function __invoke(
        Request $request,
        EntityManagerInterface $em,
        Security $security,
    ): JsonResponse {
        $name = $request->get('name');
        $adress = $request->get('adress');
        $latitude = (float) $request->get('latitude');
        $longitude = (float) $request->get('longitude');
        $price = (float) $request->get('price');
        $power = (float) $request->get('power');
        $description = $request->get('description');
        $defaultMessage = $request->get('defaultMessage');

        /** @var \Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile */
        $imageFile = $request->files->get('imageFile');

        if (!$security->getUser()) {
            return $this->json(['error' => 'User not found'], Response::HTTP_BAD_REQUEST);
        }

        $station = new Station();
        $station->setName($name);
        $station->setAdress($adress);
        $station->setLatitude($latitude);
        $station->setLongitude($longitude);
        $station->setPrice($price);
        $station->setPower($power);
        $station->setDescription($description);
        $station->setDefaultMessage($defaultMessage);
        $station->setUser($security->getUser());
        $station->setIsActive(true);

        if ($imageFile) {
            $station->setImageFile($imageFile);
        }

        $em->persist($station);
        $em->flush();

        return $this->json(['station' => $station], Response::HTTP_CREATED, context: ['groups' => 'station:read']);
    }
}
