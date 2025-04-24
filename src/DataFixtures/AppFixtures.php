<?php

namespace App\DataFixtures;

use App\Entity\Car;
use App\Entity\Station;
use App\Entity\Timeslot;
use App\Entity\User;
use App\Enum\Weekday;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private const STATIONS = [
        [
            'latitude' => 45.74519292993862,
            'longitude' => 4.854484694734533,
            'power' => 7.4,
            'tarif' => 4.50,
            'name' => 'IZIVIA Grand Lyon - Vénissieux',
            'address' => '1 rue de la République',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam.',
            'type' => 'Type 2',
            'image' => 'https://picsum.photos/seed/696/3000/2000',
        ],
        [
            'latitude' => 45.743741067963704,
            'longitude' => 4.883059683930613,
            'power' => 3.7,
            'tarif' => 2.50,
            'name' => 'Powerdot - Saint-Fons',
            'address' => '1 rue de la République',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam.',
            'type' => 'Type 2',
            'image' => 'https://picsum.photos/seed/696/3000/2000',
        ],
        [
            'latitude' => 45.783877845070556,
            'longitude' => 4.860547559339414,
            'power' => 7,
            'tarif' => 4.00,
            'name' => 'IZIVIA Grand Lyon - Feyzin',
            'address' => '1 rue de la République',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam.',
            'type' => 'Type 2',
            'image' => 'https://picsum.photos/seed/696/3000/2000',
        ],
        [
            'latitude' => 45.76141210025036,
            'longitude' => 4.874351252172824,
            'power' => 2.3,
            'tarif' => 1.90,
            'name' => 'Carrefour Vénissieux - Vénissieux',
            'address' => '1 rue de la République',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam.',
            'type' => 'Type 2',
            'image' => 'https://picsum.photos/seed/696/3000/2000',
        ],
        [
            'latitude' => 45.77757483744476,
            'longitude' => 4.884841976899503,
            'power' => 22,
            'tarif' => 5.60,
            'name' => 'Lyon 8ème',
            'address' => '1 rue de la République',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam.',
            'type' => 'Type 2',
            'image' => 'https://picsum.photos/seed/696/3000/2000',
        ],

        // 📍 Bornes fictives à Lyon
        [
            'latitude' => 45.7597,
            'longitude' => 4.8422,
            'power' => 11,
            'tarif' => 3.90,
            'name' => 'VoltShare Lyon - Bellecour',
            'address' => '2 Place Bellecour, 69002 Lyon',
            'description' => 'Borne située au cœur de la place Bellecour, idéale pour les courts arrêts.',
            'type' => 'Type 2',
            'image' => 'https://picsum.photos/seed/123/3000/2000',
        ],
        [
            'latitude' => 45.764043,
            'longitude' => 4.835659,
            'power' => 22,
            'tarif' => 6.00,
            'name' => 'GreenCharge Lyon - Hôtel de Ville',
            'address' => 'Place de la Comédie, 69001 Lyon',
            'description' => 'Chargez votre véhicule pendant que vous explorez le centre historique.',
            'type' => 'Type 2',
            'image' => 'https://picsum.photos/seed/124/3000/2000',
        ],
        [
            'latitude' => 45.7754,
            'longitude' => 4.8055,
            'power' => 3.7,
            'tarif' => 2.00,
            'name' => 'E-Park Lyon - Gorge de Loup',
            'address' => '10 Rue de la Navigation, 69009 Lyon',
            'description' => 'Idéal pour les résidents de Vaise, parking gratuit 1h.',
            'type' => 'Type 2',
            'image' => 'https://picsum.photos/seed/125/3000/2000',
        ],
        [
            'latitude' => 45.7485,
            'longitude' => 4.8467,
            'power' => 7.4,
            'tarif' => 3.50,
            'name' => 'VoltSpot Lyon - Perrache',
            'address' => '1 Cours de Verdun, 69002 Lyon',
            'description' => 'Proche de la gare de Perrache, accessible 24/7.',
            'type' => 'Type 2',
            'image' => 'https://picsum.photos/seed/126/3000/2000',
        ],
        [
            'latitude' => 45.7620,
            'longitude' => 4.8800,
            'power' => 11,
            'tarif' => 4.20,
            'name' => 'Lyon Est - Bron',
            'address' => 'Avenue Franklin Roosevelt, 69500 Bron',
            'description' => 'Borne en périphérie Est de Lyon, parking sécurisé.',
            'type' => 'Type 2',
            'image' => 'https://picsum.photos/seed/127/3000/2000',
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $user = new User();
        $user->setFirstname($faker->firstName)
            ->setLastname($faker->lastName)
            ->setEmail('test@test.com')
            ->setPassword('test')
            ->setAdress($faker->address)
            ->setTel($faker->phoneNumber);
        $manager->persist($user);

        $users = [];
        for ($i = 0; $i < 11; $i++) {
            $user = new User();
            $user->setFirstname($faker->firstName)
                ->setLastname($faker->lastName)
                ->setEmail($faker->unique()->safeEmail)
                ->setPassword('test')
                ->setAdress($faker->address)
                ->setTel($faker->phoneNumber);
            $manager->persist($user);
            $users[] = $user;
        }
        $availableUsers = $users;
        shuffle($availableUsers);
        foreach (self::STATIONS as $index => $item) {
            $station = new Station();
            $station->setName($item["name"])
                ->setDescription($item["description"])
                ->setPicture($item["image"])
                ->setLatitude($item["latitude"])
                ->setLongitude($item["longitude"])
                ->setPower($item["power"])
                ->setPrice($item["tarif"])
                ->setAdress($item['address'])
                ->setType($item['type'])
                ->setUser($availableUsers[$index] ?? $availableUsers[array_rand($availableUsers)])
            ;
            $manager->persist($station);
        }

        foreach ($users as $user) {
            $car = new Car();
            $car->setModel($faker->randomElement(['Tesla Model 3', 'Tesla Model Y', 'Tesla Model X']))
                ->setUser($user);
            $manager->persist($car);
        }

        $manager->flush();
        
        foreach ($manager->getRepository(Station::class)->findAll() as $station) {
            foreach (Weekday::cases() as $weekday) {
                $timeslot = new Timeslot();
                $timeslot->setWeekday($weekday);
                $timeslot->setStartTime(new \DateTime("08:00"));
                $timeslot->setEndTime(new \DateTime(("20:00")));
                $timeslot->setStation($station);

                $manager->persist($timeslot);
            }
        }

        $manager->flush();
    }
}
