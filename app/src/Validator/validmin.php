<?php

namespace App\Validator;
use Symfony\Component\Validator\Constraint;
/**
 * @Annotation
 */
class validmin extends Constraint{
    public $minimum;
    public $message="Value is below or not a number";
    function __construct(array $val)
    {
        $this->minimum=$val['value'];
    }
}