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

    public function pay(int $priceInCents): void
    {
        $this->processor->pay($priceInCents);
    }
}
