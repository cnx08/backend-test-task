<?php

namespace App\Controller;

use App\Request\PurchaseRequest;
use App\Service\Payment\PaymentProcessorRegistry;
use App\Service\PriceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class PurchaseController extends AbstractController
{
    public function __construct(
        private readonly PriceService $priceService,
        private readonly PaymentProcessorRegistry $paymentRegistry,
    ) {
    }

    #[Route('/purchase', methods: ['POST'])]
    public function purchase(#[MapRequestPayload] PurchaseRequest $dto): JsonResponse
    {
        $price = $this->priceService->calculatePrice($dto->product, $dto->taxNumber, $dto->couponCode);

        try {
            $this->paymentRegistry->get($dto->paymentProcessor)->pay($price);
        } catch (\Exception $e) {
            return $this->json(['errors' => ['payment' => $e->getMessage()]], 400);
        }

        return $this->json(['message' => 'Payment successful']);
    }
}
