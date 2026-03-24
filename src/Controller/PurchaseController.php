<?php

namespace App\Controller;

use App\Entity\Coupon;
use App\Entity\Product;
use App\Enum\TaxCountry;
use App\Request\PurchaseRequest;
use App\Service\Payment\PaymentProcessorRegistry;
use App\Service\PriceCalculator;
use App\Service\PriceModifier\CouponModifier;
use App\Service\PriceModifier\TaxModifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PurchaseController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PriceCalculator $calculator,
        private readonly ValidatorInterface $validator,
        private readonly PaymentProcessorRegistry $paymentRegistry,
    ) {
    }

    #[Route('/purchase', methods: ['POST'])]
    public function purchase(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $dto = PurchaseRequest::fromArray($data ?? []);

        $violations = $this->validator->validate($dto);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }

            return new JsonResponse(['errors' => $errors], 422);
        }

        $product = $this->em->find(Product::class, $dto->product);
        $processor = $this->paymentRegistry->get($dto->paymentProcessor);
        $country = TaxCountry::fromTaxNumber($dto->taxNumber);
        $modifiers = [];

        if ($dto->couponCode !== null) {
            $coupon = $this->em->getRepository(Coupon::class)->findOneBy(['code' => $dto->couponCode]);
            $modifiers[] = new CouponModifier($coupon);
        }

        $modifiers[] = new TaxModifier($country->getTaxRate());

        $price = $this->calculator->calculate((float) $product->getPrice(), ...$modifiers);

        try {
            $processor->pay($price);
        } catch (\Exception $e) {
            return new JsonResponse(['errors' => ['payment' => $e->getMessage()]], 400);
        }

        return new JsonResponse(['message' => 'Payment successful'], 200);
    }
}
