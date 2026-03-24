<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ValidCoupon extends Constraint
{
    public string $message = 'Coupon "{{ value }}" does not exist.';
}
