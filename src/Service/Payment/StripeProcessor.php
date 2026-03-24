<?php

namespace App\Service\Payment;

use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;

class StripeProcessor implements PaymentProcessorInterface
{
    public function __construct(private readonly StripePaymentProcessor $processor)
    {
    }

    public function getName(): string
    {
        return 'stripe';
    }

    public function pay(float $price): void
    {
        $result = $this->processor->processPayment($price);

        if (!$result) {
            throw new \Exception('Stripe payment failed');
        }
    }
}
