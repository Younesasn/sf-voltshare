<?php

namespace App\Controller;

use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;

class MessageController extends AbstractController
{
  public function __invoke(
    int $id,
    UserRepository $userRepository,
    MessageRepository $messageRepository,
    Security $security
  ): JsonResponse {
    $otherUser = $userRepository->findOneById($id);
    if (!$otherUser) {
      return $this->json(['error' => 'User not found'], 404);
    }
    $messages = $messageRepository->findConversation($security->getUser(), $otherUser);
    return $this->json($messages);
  }
}
