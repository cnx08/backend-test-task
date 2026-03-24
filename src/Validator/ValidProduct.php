<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ValidProduct extends Constraint
{
    public string $message = 'Product with id {{ value }} does not exist.';
}
