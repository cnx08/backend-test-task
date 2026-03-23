<?php

namespace App\Request;

use App\Validator\ValidTaxNumber;
use Symfony\Component\Validator\Constraints as Assert;

class CalculatePriceRequest
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $product;

    #[Assert\NotBlank]
    #[ValidTaxNumber]
    public string $taxNumber;

    public ?string $couponCode = null;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->product = (int) ($data['product'] ?? 0);
        $dto->taxNumber = (string) ($data['taxNumber'] ?? '');
        $dto->couponCode = $data['couponCode'] ?? null;

        return $dto;
    }
}
