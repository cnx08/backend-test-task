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
        $result = $this->calculator->calculate(100.0, new TaxModifier(0.24));

        $this->assertSame(124.0, $result);
    }

    public function testCalculateWithPercentCouponAndTax(): void
    {
        $coupon = $this->createStub(Coupon::class);
        $coupon->method('getType')->willReturn('P');
        $coupon->method('getValue')->willReturn(6);

        $result = $this->calculator->calculate(
            100.0,
            new CouponModifier($coupon),
            new TaxModifier(0.24),
        );

        $this->assertSame(116.56, $result);
    }

    public function testCalculateWithFixedCouponAndTax(): void
    {
        $coupon = $this->createStub(Coupon::class);
        $coupon->method('getType')->willReturn('D');
        $coupon->method('getValue')->willReturn(15);

        $result = $this->calculator->calculate(
            100.0,
            new CouponModifier($coupon),
            new TaxModifier(0.19),
        );

        $this->assertSame(101.15, $result);
    }
}
