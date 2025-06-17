<?php

namespace App\Controller;

use Stripe\Customer;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class StripeCheckoutController
{
  #[Route('/api/create-checkout-session', name: 'create_checkout_session', methods: ['POST'])]
  public function createSession(Request $request): JsonResponse
  {
    Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

    $data = json_decode($request->getContent(), true);
    $user = $data['user'];

    $searchCustomer = Customer::search([
      'query' => "email: '" . $user["email"] . "'"
    ]);

    if (empty($searchCustomer["data"][0])) {
      $customer = Customer::create([
        'name' => $user["firstname"] . " " . $user["lastname"],
        'email' => $user["email"],
      ]);
    }

    $session = Session::create([
      'customer' => $searchCustomer["data"][0] ?? $customer,
      'payment_method_types' => ['card'],
      'line_items' => [
        [
          'price_data' => [
            'currency' => 'eur',
            'product_data' => ['name' => $data['product_name']],
            'unit_amount' => floor($data['amount'] * 100),
          ],
          'quantity' => 1,
        ]
      ],
      'mode' => 'payment',
      'success_url' => $_ENV["STRIPE_SUCCESS_URL"] . '?session_id={CHECKOUT_SESSION_ID}&timestamp=' . time(),
      'cancel_url' => $_ENV["STRIPE_CANCEL_URL"] . '?timestamp=' . time(),
    ]);

    return new JsonResponse(['url' => $session->url]);
  }
}
