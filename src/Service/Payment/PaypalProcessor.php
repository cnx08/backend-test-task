<?php

namespace App\Service\Payment;

use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;

class PaypalProcessor implements PaymentProcessorInterface
{
    public function __construct(private readonly PaypalPaymentProcessor $processor)
    {
    }

    public function getName(): string
    {
        return 'paypal';
    }

    public function pay(float $price): void
    {
        $this->processor->pay((int) round($price * 100));
    }
}
