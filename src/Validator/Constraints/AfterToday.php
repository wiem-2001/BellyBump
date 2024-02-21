<?php

// src/Validator/Constraints/AfterToday.php
// src/Validator/Constraints/AfterToday.php
namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class AfterToday extends Constraint
{
    public $message = 'La date de naissance doit être postérieure à aujourd\'hui.';
}
