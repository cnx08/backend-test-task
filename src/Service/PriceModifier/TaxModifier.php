<?php

namespace App\Service\PriceModifier;

class TaxModifier implements PriceModifierInterface
{
    public function __construct(private readonly float $taxRate)
    {
    }

    public function apply(float $price): float
    {
        return $price * (1 + $this->taxRate);
    }
}
