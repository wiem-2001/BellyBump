<?php

// src/Validator/Constraints/AfterTodayValidator.php
namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AfterTodayValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof \DateTimeInterface) {
            return;
        }

        $today = new \DateTime();

        if ($value <= $today) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}

