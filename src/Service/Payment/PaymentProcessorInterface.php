<?php

namespace App\Service\Payment;

interface PaymentProcessorInterface
{
    public function getName(): string;

    /** @throws \Exception on payment failure */
    public function pay(int $priceInCents): void;
}
