<?php


namespace App\Form\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * Class AllowedUsername
 * @package App\Form\Validator\Constraint
 */
class AllowedUsername extends Constraint
{
    public $message = 'This username cannot be used.';
}