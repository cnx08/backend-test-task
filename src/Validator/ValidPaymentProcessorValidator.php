<?php

namespace App\Validator;

use App\Service\Payment\PaymentProcessorRegistry;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidPaymentProcessorValidator extends ConstraintValidator
{
    public function __construct(private readonly PaymentProcessorRegistry $registry)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if ($value === null || $value === '') {
            return;
        }

        if ($this->registry->get($value) === null) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
