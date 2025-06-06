<?php
namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;



class TwoFactorSuccessHandler implements AuthenticationSuccessHandlerInterface
{
  public function __construct(private JWTTokenManagerInterface $jwtManager)
  {
  }

  public function onAuthenticationSuccess(Request $request, TokenInterface $token): ?Response {
    return new Response('{"login": "success", "two_factor_complete": false}');
  }
}
