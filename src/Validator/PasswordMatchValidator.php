<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PasswordMatchValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var PasswordMatch $constraint */

        if (!$value) {
            return; // On valide uniquement les objets User
        }

        $entity = $this->context->getObject(); 

        if (!$entity instanceof \App\Entity\User) {
            return;
        }

        // Vérifie si les mots de passe sont identiques
        if ($entity->getPlainPassword() !== $entity->getPlainPasswordRepeat()) {
            $this->context->buildViolation($constraint->message)
                ->atPath('plainPasswordRepeat')
                ->addViolation();
        }
    }
}
