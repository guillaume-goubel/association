<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\ActivityMessage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ActivityMessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextareaType::class, [
                'label' => "Le contenu du message",
                'required' => true,
                'attr' => [
                    'rows' => '5' , 
                    'class' => 'form-control', 
                    // 'placeholder' => "vous pouvez mettre ici une description - NON OBLIGATOIRE",
                    'style' => 'resize: vertical;height: 230px;'
                ],
            ])    
            ->add('name', TextType::class, [
                'label' => "Le nom du message",
                'attr' => ['class' => 'form-control'],
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ActivityMessage::class,
        ]);
    }
}
