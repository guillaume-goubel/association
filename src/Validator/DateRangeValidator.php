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

        // Récupère l'objet "Event" complet (entité)
        $entity = $this->context->getObject();  // Ceci donne l'objet "Event"

        if (!$entity instanceof \App\Entity\Event) {
            return;
        }

        // Récupère les dates de début et de fin
        $startDate = $entity->getDateStartAt(); // Accéder à dateStartAt avec son getter
        $endDate = $value; // La valeur de dateEndAt est passée au validateur

        // Vérifie que dateEndAt est bien supérieur ou égal à dateStartAt
        if ($startDate && $endDate && $endDate < $startDate) {
            $this->context->buildViolation($constraint->message)
                ->addViolation(); // Ajoute l'erreur
        }
    }
}
