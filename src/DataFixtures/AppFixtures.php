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
        $users[] = $user;
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
        $jsonData = file_get_contents(__DIR__ . '/stations.json');
        $stations = json_decode($jsonData, true);
        foreach ($stations as $index => $item) {
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

        $car = new Car();
        $car->setModel("Anonymous");
        $manager->persist($car);

        foreach ($users as $user) {
            $car = new Car();
            $car->setModel($faker->randomElement([
                'Tesla Model 3',
                'Tesla Model Y',
                'Tesla Model X',
                'Tesla Model S',
                'Renault Zoe',
                'Renault Megane E-Tech',
                'Peugeot e-208',
                'Peugeot e-2008',
                'Fiat 500e',
                'Volkswagen ID.5',
                'Volkswagen ID.Buzz',
                'BMW i3',
                'BMW i4',
                'BMW iX',
                'BMW iX3',
                'Hyundai Ioniq 5',
                'Hyundai Ioniq 6',
                'Hyundai Kona Electric',
                'Kia EV6',
                'Kia Niro EV',
                'Audi Q4 e-tron',
                'Audi e-tron GT',
                'Audi Q8 e-tron',
                'Mercedes EQS',
                'Nissan Leaf',
                'Nissan Ariya',
                'Skoda Enyaq iV',
                'MG4 Electric',
                'MG5 Electric',
                'Porsche Taycan',
                'Ford Mustang Mach-E',
                'Honda e',
                'Dacia Spring',
                'BYD Atto 3',
                'Smart #1',
                'Lucid Air',
                'Polestar 2',
                'Volvo XC40 Recharge',
                'Volvo C40 Recharge'
            ]))
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
