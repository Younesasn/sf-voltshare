<?php

namespace App\Command;

use App\Entity\Station;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:create-deleted-station')]
class CreateDeletedStationCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    private function createPassword(): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $result = '';
        for ($i = 0; $i < 15; ++$i) {
            $result .= $chars[random_int(0, strlen($chars) - 1)];
        }

        return $result;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = new User();
        $user->setFirstname('Anonymous')
          ->setLastname('Anonymous')
          ->setEmail('anonymous@anonymous.com')
          ->setPassword($this->createPassword())
          ->setAdress('Anonymous')
          ->setTel('0000000000')
          ->setIsDeleted(false);
        $this->em->persist($user);

        $station = new Station();
        $station->setName('Borne Supprimée')
          ->setAdress('Adresse Inconnue')
          ->setLatitude(0.0)
          ->setLongitude(0.0)
          ->setDescription('Borne supprimée')
          ->setPower(0.0)
          ->setPrice(0.0)
          ->setDefaultMessage('Anonymous')
          ->setUser($user);
        $this->em->persist($station);
        $this->em->flush();

        $output->writeln('Anonymous Data has created !');

        return Command::SUCCESS;
    }
}
