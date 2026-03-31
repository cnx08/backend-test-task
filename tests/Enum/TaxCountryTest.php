<?php

namespace App\Tests\Enum;

use App\Enum\TaxCountry;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TaxCountryTest extends TestCase
{
    #[DataProvider('validTaxNumberProvider')]
    public function testFromTaxNumberReturnsCountry(string $taxNumber, TaxCountry $expected): void
    {
        $this->assertSame($expected, TaxCountry::fromTaxNumber($taxNumber));
    }

    #[DataProvider('invalidTaxNumberProvider')]
    public function testFromTaxNumberReturnsNullForInvalid(string $taxNumber): void
    {
        $this->assertNull(TaxCountry::fromTaxNumber($taxNumber));
    }

    #[DataProvider('taxRateProvider')]
    public function testGetTaxRate(TaxCountry $country, int $expectedRate): void
    {
        $this->assertSame($expectedRate, $country->getTaxRate());
    }

    public static function validTaxNumberProvider(): array
    {
        return [
            'Germany' => ['DE123456789', TaxCountry::Germany],
            'Italy'   => ['IT12345678900', TaxCountry::Italy],
            'Greece'  => ['GR123456789', TaxCountry::Greece],
            'France'  => ['FRAN123456789', TaxCountry::France],
        ];
    }

    public static function invalidTaxNumberProvider(): array
    {
        return [
            'empty string'            => [''],
            'unknown prefix'          => ['US123456789'],
            'DE too short'            => ['DE12345678'],
            'DE too long'             => ['DE1234567890'],
            'DE with letters'         => ['DE12345678A'],
            'IT too short'            => ['IT1234567890'],
            'GR too short'            => ['GR12345678'],
            'FR digits instead of YY' => ['FR12123456789'],
            'FR one letter'           => ['FRA123456789'],
        ];
    }

    public static function taxRateProvider(): array
    {
        return [
            'Germany' => [TaxCountry::Germany, 19],
            'Italy'   => [TaxCountry::Italy, 22],
            'Greece'  => [TaxCountry::Greece, 24],
            'France'  => [TaxCountry::France, 20],
        ];
    }
}
