<?php

namespace App\EventListener;

use App\Entity\Message;
use App\Service\MercurePublisher;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(Events::postPersist)]
class SendingMessageListener
{
    public function __construct(private MercurePublisher $mercurePublisher)
    {
    }

    public function postPersist(PostPersistEventArgs $event)
    {
        $entity = $event->getObject();

        if (!$entity instanceof Message) {
            return;
        }

        // Définis un topic unique pour la conversation (ex: "messages/{user1_id}_{user2_id}")
        $topic = sprintf('messages/%d_%d', min($entity->getSender()->getId(), $entity->getReceiver()->getId()), max($entity->getSender()->getId(), $entity->getReceiver()->getId()));

        $this->mercurePublisher->publish($topic, [
            'id' => $entity->getId(),
            'sender' => $entity->getSender()->toArray(),
            'receiver' => $entity->getReceiver()->toArray(),
            'content' => $entity->getContent(),
            'sendAt' => $entity->getSendAt(),
        ]);
    }
}
