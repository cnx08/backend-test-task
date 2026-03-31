<?php

namespace App\Service\PriceModifier;

use App\Entity\Coupon;

class CouponModifier implements PriceModifierInterface
{
    public function __construct(private readonly Coupon $coupon)
    {
    }

    public function apply(int $priceInCents): int
    {
        return match($this->coupon->getType()) {
            'P' => (int) round($priceInCents * (100 - $this->coupon->getValue()) / 100),
            'D' => $priceInCents - $this->coupon->getValue() * 100,
            default => $priceInCents,
        };
    }
}
