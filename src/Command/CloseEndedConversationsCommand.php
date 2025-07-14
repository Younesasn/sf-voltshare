<?php

namespace App\Command;

use App\Entity\Conversation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:close-ended-conversations')]
class CloseEndedConversationsCommand extends Command
{
  public function __construct(private EntityManagerInterface $em)
  {
    parent::__construct();
  }

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $now = new \DateTimeImmutable();

    $conversations = $this->em->getRepository(Conversation::class)
      ->createQueryBuilder('c')
      ->join('c.reservation', 'r')
      ->where('c.isOpen = true')
      ->andWhere('r.endTime < :today')
      ->setParameter('today', $now)
      ->getQuery()
      ->getResult();

    foreach ($conversations as $conv) {
      $conv->setIsOpen(false);
    }

    $this->em->flush();
    $output->writeln('[' . $now->format(DATE_RSS) . '] ' . count($conversations) . ' conversation(s) fermée(s).');

    return Command::SUCCESS;
  }
}
