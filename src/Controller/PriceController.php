<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class PriceController
{
    #[Route('/calculate-price', methods: ['POST'])]
    public function calculatePrice(): JsonResponse
    {
        return new JsonResponse(['price' => 0]);
    }
}
