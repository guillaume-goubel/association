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
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
                'label' => "Le nom de l'événement",
                'attr' => ['class' => 'form-control'],
                'required' => false,
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
                 'label' => "L'évènement comporte-t-il un parcours (en km) ",
                'help' => 'ex: comme pour une randonnée de 13 (km)',
                'attr' => ['class' => 'form-control'],
                'data' => null,
                'html5' => true,
                'required' => false,
            ])
            ->add('rdvPlaceName', TextType::class, [
                'label' => "Complément du lieu de rendez-vous",
                'attr' => ['class' => 'form-control'],
                'help' => "ex : devant la Mairie, en face du magasin de bricolage...",
                'required' => false,
            ])
            ->add('rdvAddress', TextType::class, [
                'label' => "Adresse complète du rendez-vous ",
                'attr' => ['class' => 'form-control'],
                'help' => "Peut être renseigné automatiquement via la recherche sur la carte ...",
                'required' => false,
            ])
            ->add('rdvCity', TextType::class, [
                'label' => "Ville du rendez-vous ",
                'attr' => ['class' => 'form-control'],
                'help' => "Peut être renseigné automatiquement via la recherche sur la carte ...",
                'required' => false,
            ])
            ->add('rdvLatitude', TextType::class, [
                'label' => "Coodonnées de latitude du rendez-vous ",
                'attr' => ['class' => 'form-control'],
                'help' => "Peut être renseigné automatiquement via la recherche sur la carte ...",
                'required' => false,
            ])
            ->add('rdvLongitude', TextType::class, [
                'label' => "Coodonnées de longitude du rendez-vous ",
                'attr' => ['class' => 'form-control'],
                'help' => "Peut être renseigné automatiquement via la recherche sur la carte ...",
                'required' => false,
            ])
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
                'required' => false,
            ])
            ->add('activity', EntityType::class, [
                'class' => Activity::class,
                'label' => "Rattacher à un type d'activité",
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
                'help' => 'Appuyer sur la touche "CONTROL" en cliquant pour faire des sélections multiples',
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
            ->add('mainPictureFile', FileType::class, [
                'label' => 'Image principale',
                'label_attr' => [
                    'class' => 'labelCustom'
                ],
                'attr' => ['class' => 'form-control'],
                'required' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => 'image/*',
                        'maxSize' => '2M',
                    ]),
                    new Image([
                        'maxHeight' => 1100,
                        'maxWidth' => 2000,
                    ])
                ]
            ])
            ->add('picture2File', FileType::class, [
                'label' => 'Image n°2',
                'label_attr' => [
                    'class' => 'labelCustom'
                ],
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new File([
                        'mimeTypes' => 'image/*',
                        'maxSize' => '2M',
                    ]),
                    new Image([
                        'maxHeight' => 1100,
                        'maxWidth' => 2000,
                    ])
                ]
            ])
            ->add('picture3File', FileType::class, [
                'label' => 'Image n°3',
                'label_attr' => [
                    'class' => 'labelCustom'
                ],
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new File([
                        'mimeTypes' => 'image/*',
                        'maxSize' => '2M',
                    ]),
                    new Image([
                        'maxHeight' => 1100,
                        'maxWidth' => 2000,
                    ])
                ]
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
