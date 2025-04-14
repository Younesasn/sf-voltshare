<?php

namespace App\DataFixtures;

use App\Entity\Car;
use App\Entity\Timeslot;
use App\Entity\User;
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
            ->setEmail($faker->email)
            ->setPassword('test')
            ->setAdress($faker->address)
            ->setTel($faker->phoneNumber);
        $manager->persist($user);

        $car = new Car();
        $car->setModel('Tesla Model 3')
            ->setUser($user);
        $manager->persist($car);

        $manager->flush();
    }
}
