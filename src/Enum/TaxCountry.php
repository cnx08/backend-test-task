<?php

namespace App\Enum;

enum TaxCountry: string
{
    case Germany = 'DE';
    case Italy   = 'IT';
    case Greece  = 'GR';
    case France  = 'FR';

    public function getPattern(): string
    {
        return match($this) {
            self::Germany => '/^DE\d{9}$/',
            self::Italy   => '/^IT\d{11}$/',
            self::Greece  => '/^GR\d{9}$/',
            self::France  => '/^FR[A-Z]{2}\d{9}$/',
        };
    }

    public function getTaxRate(): int
    {
        return match($this) {
            self::Germany => 19,
            self::Italy   => 22,
            self::Greece  => 24,
            self::France  => 20,
        };
    }

    public static function fromTaxNumber(string $taxNumber): ?self
    {
        foreach (self::cases() as $country) {
            if (preg_match($country->getPattern(), $taxNumber)) {
                return $country;
            }
        }

        return null;
    }
}
