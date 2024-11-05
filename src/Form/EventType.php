<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Event;
use App\Entity\Activity;
use App\Entity\Animator;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        $activityIds = $options['activity_ids'];

        $builder
            ->add('name', TextType::class, [
                'label' => "Le nom de l'événement - OBLIGATOIRE",
                'attr' => ['class' => 'form-control'],
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => "La description de l'événement",
                'required' => false,
                'attr' => [
                    'rows' => '5' , 
                    'class' => 'form-control', 
                    // 'placeholder' => "vous pouvez mettre ici une description - NON OBLIGATOIRE",
                    'style' => 'resize: vertical;height: 200px;'
                ],
            ])
            ->add('preparationInfos', TextareaType::class, [
                'label' => "Les informations importantes pour préparer l'événement ",
                'required' => false,
                'attr' => [
                    'rows' => '5' , 
                    'class' => 'form-control', 
                    // 'placeholder' => "vous pouvez mettre ici des informations utiles pour l'évènement - NON OBLIGATOIRE",
                    'style' => 'resize: vertical;height: 200px;'
                ],
            ])
            ->add('eventDistance', NumberType::class, [
                'label' => "L'évènement comporte-t-il une distance en km ",
                'help' => '(comme pour les randonnées par exemple : 13km)',
                'attr' => ['class' => 'form-control'],
                'data' => null,
                'html5' => true,
                'required' => false,
            ])
            ->add('rdvPlaceName', TextType::class, [
                'label' => "Le lieux de rendez-vous ",
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('rdvLatitude')
            ->add('rdvLongitude')
            ->add('timeStartAt', TimeType::class, [
                'label' => 'Commence à  ... ',
                'attr' => ['class' => 'form-control'],
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('timeEndAt', null, [
                'label' => 'Fini à  ... ',
                'attr' => ['class' => 'form-control'],
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('dateStartAt', null, [
                'label' => 'Commence le ... ',
                'attr' => ['class' => 'form-control'],
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('dateEndAt', null, [
                'label' => 'Termine le ... ',
                'attr' => ['class' => 'form-control'],
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('activity', EntityType::class, [
                'class' => Activity::class,
                'label' => "Rattacher à un type d'activité - OBLIGATOIRE",
                'choice_label' => 'name',
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'query_builder' => function (EntityRepository $er) use ($activityIds) {
                    return $er->createQueryBuilder('a')
                        ->where('a.id IN (:ids)')
                        ->setParameter('ids', $activityIds)
                        ->andwhere('a.isEnabled = :enabled')
                        ->setParameter('enabled', true);
                },
    
            ])
            ->add('animators', EntityType::class, [
                'label' => 'Rattacher à un/des animateurs',
                'help' => '(Appuyer sur la touche "CONTROL" pour faire les sélections)',
                'attr' => ['class' => 'form-control'],
                'class' => Animator::class,
                'choice_label' => 'completeName',
                'multiple' => true,
                'required' => false,
            ])
            ->add('user', EntityType::class, [
                'label' => 'Rattacher un administrateur',
                'attr' => ['class' => 'form-control'],
                'class' => User::class,
                'choice_label' => 'completeName',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
            'activity_ids' => [],
        ]);
    }
}
