<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ValidTaxNumber extends Constraint
{
    public string $message = 'The tax number "{{ value }}" is invalid.';
}
