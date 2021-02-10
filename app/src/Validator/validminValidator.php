<?php

namespace App\Validator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class validminValidator extends ConstraintValidator{
    public function validate($value,Constraint $constraint)
    {
        if(!(is_numeric($value)&&intval($value)>=$constraint->minimum))
        {
     
            throw new UnexpectedValueException($value,'string');
           // $this->setMessage($constraint->message, array('%string%' => $value));
        }
        else{
            return true;
        }
    }
}