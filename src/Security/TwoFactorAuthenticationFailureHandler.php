<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

class TwoFactorAuthenticationFailureHandler implements AuthenticationFailureHandlerInterface
{
  public function onAuthenticationFailure(Request $request, AuthenticationException $exception):JsonResponse
  {
    return new JsonResponse(["error" => "Code invalide", "two_factor_complete" => false]);
  }
}
