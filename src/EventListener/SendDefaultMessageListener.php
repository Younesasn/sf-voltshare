<?php

namespace App\EventListener;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(Events::postPersist)]
class SendDefaultMessageListener
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function postPersist(PostPersistEventArgs $event)
    {
        $entity = $event->getObject();

        if (!$entity instanceof Reservation) {
            return;
        }

        $host = $entity->getStation()->getUser();
        $customer = $entity->getUser();

        $conversation = new Conversation();
        $conversation->setHost($host)
          ->setCustomer($customer)
          ->setReservation($entity)
          ->setIsOpen(true);

        $this->em->persist($conversation);

        $message = new Message();
        $message->setContent($entity->getStation()->getDefaultMessage())
          ->setSender($host)
          ->setReceiver($customer)
          ->setSendAt(new \DateTime())
          ->setConversation($conversation)
        ;

        $this->em->persist($message);
        $this->em->flush();
    }
}
