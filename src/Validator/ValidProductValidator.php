<?php

namespace App\Validator;

use App\Repository\ProductRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidProductValidator extends ConstraintValidator
{
    public function __construct(private readonly ProductRepository $repository)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if ($value === null || $value === 0) {
            return;
        }

        if ($this->repository->find($value) === null) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
