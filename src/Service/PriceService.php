<?php

namespace App\Service;

use App\Entity\Coupon;
use App\Entity\Product;
use App\Enum\TaxCountry;
use App\Service\PriceModifier\CouponModifier;
use App\Service\PriceModifier\TaxModifier;
use Doctrine\ORM\EntityManagerInterface;

class PriceService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PriceCalculator $calculator,
    ) {
    }

    public function calculatePrice(int $productId, string $taxNumber, ?string $couponCode): float
    {
        $product = $this->em->find(Product::class, $productId);
        $country = TaxCountry::fromTaxNumber($taxNumber);
        $modifiers = [];

        if ($couponCode !== null) {
            $coupon = $this->em->getRepository(Coupon::class)->findOneBy(['code' => $couponCode]);
            $modifiers[] = new CouponModifier($coupon);
        }

        $modifiers[] = new TaxModifier($country->getTaxRate());

        $basePriceInCents = (int) round((float) $product->getPrice() * 100);

        return $this->calculator->calculate($basePriceInCents, ...$modifiers);
    }
}
