<?php

namespace App\Controller;

use App\Enum\TaxCountry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class PriceController
{
    #[Route('/calculate-price', methods: ['POST'])]
    public function calculatePrice(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $taxNumber = $data['taxNumber'] ?? null;
        $country = $taxNumber ? TaxCountry::fromTaxNumber($taxNumber) : null;

        if ($country === null) {
            return new JsonResponse(['error' => 'Invalid taxNumber'], 400);
        }

        return new JsonResponse([
            'taxRate' => $country->getTaxRate(),
        ]);
    }
}
