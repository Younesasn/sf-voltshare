<?php

namespace App\Command;

use App\Entity\Car;
use App\Entity\Station;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'app:load-initial-data',
    description: 'Charge les données initiales nécessaires pour le fonctionnement de l\'application en production'
)]
class LoadInitialDataCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        #[Autowire('%kernel.project_dir%')]
        private string $projectDir,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Force le chargement même si des données existent déjà'
            )
            ->addOption(
                'stations-file',
                null,
                InputOption::VALUE_OPTIONAL,
                'Chemin vers le fichier JSON des stations',
                null
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Vérifier si des données existent déjà
        if (!$input->getOption('force')) {
            $existingStations = $this->em->getRepository(Station::class)->count([]);
            $existingCars = $this->em->getRepository(Car::class)->count([]);

            if ($existingStations > 0 || $existingCars > 0) {
                $io->warning('Des données existent déjà dans la base de données.');
                $io->note('Utilisez l\'option --force pour forcer le chargement.');

                return Command::FAILURE;
            }
        }

        $io->title('Chargement des données initiales');

        // 1. Créer la voiture "Anonymous" par défaut
        $io->section('Création de la voiture Anonymous');
        $anonymousCar = $this->em->getRepository(Car::class)->findOneBy(['model' => 'Anonymous']);
        if (!$anonymousCar) {
            $anonymousCar = new Car();
            $anonymousCar->setModel('Anonymous');
            $this->em->persist($anonymousCar);
            $io->success('Voiture "Anonymous" créée');
        } else {
            $io->info('Voiture "Anonymous" existe déjà');
        }

        // 2. Créer un utilisateur système pour les stations (si nécessaire)
        $io->section('Création de l\'utilisateur système pour les stations');
        $systemUser = $this->em->getRepository(User::class)->findOneBy(['email' => 'system@voltshare.com']);
        if (!$systemUser) {
            $systemUser = new User();
            $systemUser->setFirstname('System')
                ->setLastname('Voltshare')
                ->setEmail('system@voltshare.com')
                ->setPassword(bin2hex(random_bytes(32))) // Le password sera hashé automatiquement par CreatingUserListener
                ->setAdress('System')
                ->setTel('0000000000');
            // isDeleted sera défini automatiquement à false par CreatingUserListener
            $this->em->persist($systemUser);
            $io->success('Utilisateur système créé');
        } else {
            $io->info('Utilisateur système existe déjà');
        }

        // 3. Charger les stations depuis le fichier JSON
        $io->section('Chargement des stations');
        $stationsFile = $input->getOption('stations-file') ?? $this->projectDir.'/config/stations.json';

        if (!file_exists($stationsFile)) {
            $io->error(sprintf('Le fichier %s n\'existe pas', $stationsFile));
            $io->note('Assurez-vous que le fichier stations.json est présent dans le répertoire config/');

            return Command::FAILURE;
        }

        $jsonData = file_get_contents($stationsFile);
        $stationsData = json_decode($jsonData, true);

        if (!is_array($stationsData)) {
            $io->error('Le fichier JSON des stations est invalide');

            return Command::FAILURE;
        }

        $stationsCreated = 0;
        $stationsSkipped = 0;

        foreach ($stationsData as $index => $item) {
            // Vérifier si la station existe déjà (par nom ou coordonnées)
            $existingStations = $this->em->getRepository(Station::class)
                ->createQueryBuilder('s')
                ->where('s.name = :name')
                ->orWhere('(s.latitude = :lat AND s.longitude = :lng)')
                ->setParameter('name', $item['name'])
                ->setParameter('lat', $item['latitude'])
                ->setParameter('lng', $item['longitude'])
                ->getQuery()
                ->getResult();

            if (count($existingStations) > 0) {
                $io->writeln(sprintf('  ⏭  Station "%s" existe déjà', $item['name']));
                ++$stationsSkipped;
                continue;
            }

            $station = new Station();
            $station->setName($item['name'])
                ->setDescription($item['description'] ?? '')
                ->setPicture($item['image'] ?? 'ev.jpg')
                ->setLatitude($item['latitude'])
                ->setLongitude($item['longitude'])
                ->setPower($item['power'])
                ->setPrice($item['tarif'])
                ->setAdress($item['address'])
                ->setDefaultMessage("Bonjour ! Merci d'avoir réservé notre borne ! 😁")
                ->setIsActive(true)
                ->setUser($systemUser);

            $this->em->persist($station);
            ++$stationsCreated;
        }

        $this->em->flush();

        if ($stationsCreated > 0) {
            $io->success(sprintf('%d station(s) créée(s)', $stationsCreated));
        }
        if ($stationsSkipped > 0) {
            $io->info(sprintf('%d station(s) ignorée(s) (déjà existantes)', $stationsSkipped));
        }

        $io->newLine();
        $io->success('Données initiales chargées avec succès !');

        return Command::SUCCESS;
    }
}
