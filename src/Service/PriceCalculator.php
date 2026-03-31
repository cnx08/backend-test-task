<?php

namespace App\Service;

use App\Service\PriceModifier\PriceModifierInterface;

class PriceCalculator
{
    public function calculate(int $basePriceInCents, PriceModifierInterface ...$modifiers): int
    {
        $price = $basePriceInCents;

        foreach ($modifiers as $modifier) {
            $price = $modifier->apply($price);
        }

        return $price;
    }
}
