<?php

namespace App\Controller;

use App\Repository\ConversationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;

class ConversationController extends AbstractController
{
    public function __invoke(Security $security, ConversationRepository $conversationRepository): JsonResponse
    {
        $conversations = $conversationRepository->findAllConversationByUser($security->getUser());
        if (empty($conversations)) {
            return $this->json(['error' => 'Conversations not found'], 404);
        }

        return $this->json($conversations, context: ['groups' => 'conversation:read']);
    }
}
