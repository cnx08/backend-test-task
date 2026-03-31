<?php

namespace App\Request;

use App\Validator\ValidCoupon;
use App\Validator\ValidPaymentProcessor;
use App\Validator\ValidProduct;
use App\Validator\ValidTaxNumber;
use Symfony\Component\Validator\Constraints as Assert;

class PurchaseRequest
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    #[ValidProduct]
    public int $product;

    #[Assert\NotBlank]
    #[ValidTaxNumber]
    public string $taxNumber;

    #[ValidCoupon]
    public ?string $couponCode = null;

    #[Assert\NotBlank]
    #[ValidPaymentProcessor]
    public string $paymentProcessor;

}
