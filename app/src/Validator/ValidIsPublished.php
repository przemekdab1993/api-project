<?php

namespace App\Validator;

use Doctrine\Common\Annotations\Annotation;
use Symfony\Component\Validator\Constraint;
use Doctrine\Common\Annotations\Annotation\Target;


#[Annotation()]
#[\Attribute]
#[Target(['CLASS'])]
class ValidIsPublished extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'The value "{{ value }}" is not valid.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT; 
    }
}
