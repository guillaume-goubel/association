<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Activity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ActivityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('description', TextareaType::class, [
            'label' => "La description de l'activité",
            'required' => false,
            'attr' => [
                'rows' => '10' , 
                'class' => 'form-control', 
                // 'placeholder' => "vous pouvez mettre ici une description - NON OBLIGATOIRE",
                'style' => 'resize: vertical;height: 400px;'
            ],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Activity::class,
        ]);
    }
}
