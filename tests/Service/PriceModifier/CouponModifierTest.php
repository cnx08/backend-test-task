<?php

namespace App\Tests\Service\PriceModifier;

use App\Entity\Coupon;
use App\Service\PriceModifier\CouponModifier;
use PHPUnit\Framework\TestCase;

class CouponModifierTest extends TestCase
{
    public function testApplyFixedDiscount(): void
    {
        $coupon = $this->makeCoupon('D', 15);
        $modifier = new CouponModifier($coupon);

        $this->assertSame(8500, $modifier->apply(10000));
    }

    public function testApplyPercentDiscount(): void
    {
        $coupon = $this->makeCoupon('P', 10);
        $modifier = new CouponModifier($coupon);

        $this->assertSame(9000, $modifier->apply(10000));
    }

    public function testApplyUnknownDiscount(): void
    {
        $coupon = $this->makeCoupon('X', 10);
        $modifier = new CouponModifier($coupon);

        $this->assertSame(10000, $modifier->apply(10000));
    }

    private function makeCoupon(string $type, int $value): Coupon
    {
        $coupon = $this->createStub(Coupon::class);
        $coupon->method('getType')->willReturn($type);
        $coupon->method('getValue')->willReturn($value);

        return $coupon;
    }
}
