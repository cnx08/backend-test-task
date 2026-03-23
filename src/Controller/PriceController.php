<?php

namespace App\Controller;

use App\Entity\Coupon;
use App\Entity\Product;
use App\Enum\TaxCountry;
use App\Request\CalculatePriceRequest;
use App\Service\PriceCalculator;
use App\Service\PriceModifier\CouponModifier;
use App\Service\PriceModifier\TaxModifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PriceController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PriceCalculator $calculator,
        private readonly ValidatorInterface $validator,
    ) {
    }

    #[Route('/calculate-price', methods: ['POST'])]
    public function calculatePrice(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $dto = CalculatePriceRequest::fromArray($data ?? []);

        $violations = $this->validator->validate($dto);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }

            return new JsonResponse(['errors' => $errors], 422);
        }

        $product = $this->em->find(Product::class, $dto->product);

        if ($product === null) {
            return new JsonResponse(['errors' => ['product' => 'Product not found']], 422);
        }

        $country = TaxCountry::fromTaxNumber($dto->taxNumber);
        $modifiers = [];

        if ($dto->couponCode !== null) {
            $coupon = $this->em->getRepository(Coupon::class)->findOneBy(['code' => $dto->couponCode]);

            if ($coupon === null) {
                return new JsonResponse(['errors' => ['couponCode' => 'Coupon not found']], 422);
            }

            $modifiers[] = new CouponModifier($coupon);
        }

        $modifiers[] = new TaxModifier($country->getTaxRate());

        $price = $this->calculator->calculate((float) $product->getPrice(), ...$modifiers);

        return new JsonResponse(['price' => $price]);
    }
}
