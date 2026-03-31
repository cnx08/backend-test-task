<?php

namespace App\Tests\Service;

use App\Service\PriceCalculator;
use App\Service\PriceModifier\CouponModifier;
use App\Service\PriceModifier\TaxModifier;
use App\Entity\Coupon;
use PHPUnit\Framework\TestCase;

class PriceCalculatorTest extends TestCase
{
    private PriceCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new PriceCalculator();
    }

    public function testCalculateWithTax(): void
    {
        $result = $this->calculator->calculate(10000, new TaxModifier(24));

        $this->assertSame(12400, $result);
    }

    public function testCalculateWithPercentCouponAndTax(): void
    {
        $coupon = $this->createStub(Coupon::class);
        $coupon->method('getType')->willReturn('P');
        $coupon->method('getValue')->willReturn(6);

        $result = $this->calculator->calculate(
            10000,
            new CouponModifier($coupon),
            new TaxModifier(24),
        );

        $this->assertSame(11656, $result);
    }

    public function testCalculateWithFixedCouponAndTax(): void
    {
        $coupon = $this->createStub(Coupon::class);
        $coupon->method('getType')->willReturn('D');
        $coupon->method('getValue')->willReturn(15);

        $result = $this->calculator->calculate(
            10000,
            new CouponModifier($coupon),
            new TaxModifier(19),
        );

        $this->assertSame(10115, $result);
    }
}
