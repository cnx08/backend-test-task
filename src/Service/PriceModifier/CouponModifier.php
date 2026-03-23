<?php

namespace App\Service\PriceModifier;

use App\Entity\Coupon;

class CouponModifier implements PriceModifierInterface
{
    public function __construct(private readonly Coupon $coupon)
    {
    }

    public function apply(float $price): float
    {
        return match($this->coupon->getType()) {
            'P' => $price * (1 - $this->coupon->getValue() / 100),
            'D' => $price - $this->coupon->getValue(),
            default => $price,
        };
    }
}
