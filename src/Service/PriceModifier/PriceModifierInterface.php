<?php

namespace App\Service\PriceModifier;

interface PriceModifierInterface
{
    public function apply(int $priceInCents): int;
}
