<?php

namespace App\Service\PriceModifier;

interface PriceModifierInterface
{
    public function apply(float $price): float;
}
