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
        
        //var_dump('DUMP1',$val);
        $this->minimum=$val['value'];
    }
}