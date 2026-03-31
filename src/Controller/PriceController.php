<?php

namespace App\Controller;

use App\Request\CalculatePriceRequest;
use App\Service\PriceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class PriceController extends AbstractController
{
    public function __construct(private readonly PriceService $priceService)
    {
    }

    #[Route('/calculate-price', methods: ['POST'])]
    public function calculatePrice(#[MapRequestPayload] CalculatePriceRequest $dto): JsonResponse
    {
        $price = $this->priceService->calculatePrice($dto->product, $dto->taxNumber, $dto->couponCode);

        return $this->json(['price' => $price / 100]);
    }
}
