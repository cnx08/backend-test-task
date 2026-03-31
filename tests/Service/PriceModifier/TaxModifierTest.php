<?php

namespace App\Tests\Service\PriceModifier;

use App\Service\PriceModifier\TaxModifier;
use PHPUnit\Framework\TestCase;

class TaxModifierTest extends TestCase
{
    public function testApplyTax(): void
    {
        $modifier = new TaxModifier(19);

        $this->assertSame(11900, $modifier->apply(10000));
    }
}
