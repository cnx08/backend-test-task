<?php

namespace App\Tests\Service\PriceModifier;

use App\Service\PriceModifier\TaxModifier;
use PHPUnit\Framework\TestCase;

class TaxModifierTest extends TestCase
{
    public function testApplyTax(): void
    {
        $modifier = new TaxModifier(0.19);

        $this->assertSame(119.0, $modifier->apply(100.0));
    }

}
