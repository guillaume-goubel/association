<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DateRangeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var \App\Validator\DateRange $constraint */

        if (!$value) {
            return;
        }
        
        $entity = $this->context->getObject();  

        if (!$entity instanceof \App\Entity\Event) {
            return;
        }

        // Récupère les dates de début et de fin
        $startDate = $entity->getDateStartAt(); 
        $endDate = $value; 

        // Vérifie que dateEndAt est bien supérieur ou égal à dateStartAt
        if ($startDate && $endDate && $endDate < $startDate) {
            $this->context->buildViolation($constraint->message)
                ->addViolation(); // Ajoute l'erreur
        }
    }
}
