<?php

namespace App\Service\Payment;

class PaymentProcessorRegistry
{
    /** @var array<string, PaymentProcessorInterface> */
    private array $processors = [];

    /** @param iterable<PaymentProcessorInterface> $processors */
    public function __construct(iterable $processors)
    {
        foreach ($processors as $processor) {
            $this->processors[$processor->getName()] = $processor;
        }
    }

    public function get(string $name): ?PaymentProcessorInterface
    {
        return $this->processors[$name] ?? null;
    }
}
