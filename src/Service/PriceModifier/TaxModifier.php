<?php

namespace App\Service\PriceModifier;

class TaxModifier implements PriceModifierInterface
{
    public function __construct(private readonly int $taxRatePercent)
    {
    }

    public function apply(int $priceInCents): int
    {
        return (int) round($priceInCents * (100 + $this->taxRatePercent) / 100);
    }
}
