<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use App\Entity\User;

class PasswordMatchValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var PasswordMatch $constraint */

        if (!$value instanceof User) {
            return; // On valide uniquement les objets User
        }

        // Vérifie si les mots de passe sont identiques
        if ($value->getPlainPassword() !== $value->getPlainPasswordRepeat()) {
            $this->context->buildViolation($constraint->message)
                ->atPath('plainPasswordRepeat') // L'erreur est affichée sur ce champ
                ->addViolation();
        }
    }
}
