<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Activity;
use App\Validator\PasswordMatch;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class AdministratorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('isEnabled', CheckboxType::class, [
                'label' => "Compte activé",
                'attr' => ['class' => 'form-check-input'], // Classe Bootstrap pour les switches
                'required' => false,
                'help' => 'Cochez pour activer ou désactiver le compte.',
            ])
            ->add('email', EmailType::class, [
                'label' => "Email de connexion *",
                'attr' => ['class' => 'form-control', 'maxlength' => 75],
                'required' => true
            ])   
            ->add('lastName', TextType::class, [
                'label' => "NOM *",
                'attr' => ['class' => 'form-control'],
                'required' => true
            ])
            ->add('firstName', TextType::class, [
                'label' => "Prénom *",
                'attr' => ['class' => 'form-control', 'maxlength' => 75],
                'required' => true
            ])
            ->add('plainPassword', PasswordType::class, array(
                'label' => "Mot de passe *",
                'attr' => [
                    'placeholder' => '...',
                    'maxlength' => 32,
                    'class' => 'form-control'
                ],
                'required' => $options['is_new_user'],
                'help' => "8 caractères min / 32 caractères max, 1 majuscule + 1 chiffre",
            ))
            ->add('plainPasswordRepeat', PasswordType::class, array(
                'label' => "Répéter le mot de passe *",
                'attr' => [
                    'placeholder' => '...',
                    'maxlength' => 32,
                    'class' => 'form-control'
                ],
                'required' => $options['is_new_user'],
                'help' => "Doit être identique au mot de passe",
                'constraints' => [
                    new PasswordMatch(),
                ],
            ))
            ->add('activities', EntityType::class, [
                'class' => Activity::class,
                'label' => "Choisir une ou des activités liées ",
                'choice_label' => 'name',
                'multiple' => true,
                'attr' => [
                    'class' => 'form-control'
                ],
                'query_builder' => function (EntityRepository $er)  {
                    return $er->createQueryBuilder('a')
                        ->orderBy('a.name', 'ASC');
                },
                'help' => 'Appuyer sur la touche "CONTROL" en cliquant pour faire des sélections multiples',
            ])
            // ->add('animator', EntityType::class, [
            //     'class' => Animator::class,
            //     'choice_label' => 'id',
            //     'data' => null
            // ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_new_user' => true
        ]);
    }
    
}
