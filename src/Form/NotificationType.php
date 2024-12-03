<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Notification;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class NotificationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextareaType::class, [
                'label' => "Votre message *Obligatoire",
                'required' => true,
                'attr' => [
                    'rows' => '6' , 
                    'class' => 'form-control', 
                    'style' => 'resize: vertical;height: 300px;'
                ],
            ])
            ->add('name', TextType::class, [
                'label' => "L'objet du message",
                'attr' => ['class' => 'form-control', 'maxlength' => 75],
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 25,
                        'maxMessage' => "L'objet ne peut pas dépasser {{ limit }} caractères."
                    ])
                ]
            ])
            ->add('isEnabled', CheckboxType::class, [
                'label' => "Activer / Désactiver",
                'attr' => ['class' => 'form-check-input'],
                'help' => 'Cochez pour activer ou désactiver la notification.',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Notification::class,
        ]);
    }
}
