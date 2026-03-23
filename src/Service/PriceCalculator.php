<?php

namespace App\Service;

use App\Service\PriceModifier\PriceModifierInterface;

class PriceCalculator
{
    public function calculate(float $basePrice, PriceModifierInterface ...$modifiers): float
    {
        $price = $basePrice;

        foreach ($modifiers as $modifier) {
            $price = $modifier->apply($price);
        }

        return round($price, 2);
    }
}
