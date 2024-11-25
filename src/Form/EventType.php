<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Event;
use App\Entity\Activity;
use App\Entity\Animator;
use App\Validator\DateRange;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        $activityIds = $options['activity_ids'];
        $selectedActivity = $options['selected_activity'];
        $creationDate = $options['creation_date'];

        $builder
            ->add('isCanceled', CheckboxType::class, [
                'label' => "Annuler",
                'attr' => ['class' => 'form-check-input'], // Classe Bootstrap pour les switches
                'required' => false,
                'help' => 'Cochez pour annuler ou restaurer l\'événement.',
            ])
            ->add('name', TextType::class, [
                'label' => "Un titre ou une très courte description (75 caractères max)",
                'attr' => ['class' => 'form-control', 'maxlength' => 75],
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 75,
                        'maxMessage' => "Le nom ou la description ne peut pas dépasser {{ limit }} caractères."
                    ])
                ]
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
            ->add('cityPlace', TextType::class, [
                'label' => "Le nom de la localité où se déroule l'événement *",
                'attr' => ['class' => 'form-control'],
                'required' => true,
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
                'help' => 'La distance doit être un nombre entier supérieur à zéro',
                'attr' => ['class' => 'form-control', 'min' => 1, 'step' => 1],
                'data' => null,
                'html5' => true,
                'required' => false,
                'constraints' => [
                    new Assert\Positive([
                        'message' => "La distance de l'événement doit être un nombre strictement positif.",
                    ]),
                    new Type([
                        'type' => 'integer',
                        'message' => "La distance doit être un nombre entier.",
                    ])
                ],
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
                'label' => 'Commence à  ... *',
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
                'label' => 'Commence le ... *',
                'attr' => ['class' => 'form-control', 'min' => (new \DateTime())->format('Y-m-d')],
                'widget' => 'single_text',
                'required' => true,
                // 'data' => $creationDate instanceof \DateTime ? $creationDate : ($creationDate ? new \DateTime($creationDate) : null),
                'data' => $creationDate instanceof \DateTime 
                    ? $creationDate 
                    : ($creationDate ? new \DateTime($creationDate) : ($options['data']->getDateStartAt() ?? null)),
            ])
            ->add('dateEndAt', null, [
                'label' => 'Termine le ... *',
                'attr' => ['class' => 'form-control', 'min' => (new \DateTime())->format('Y-m-d')],
                'widget' => 'single_text',
                'required' => true,
                'constraints' => [
                    new DateRange(),
                ],
            ])
            ->add('activity', EntityType::class, [
                'class' => Activity::class,
                'label' => "Rattacher à un type d'activité *",
                'choice_label' => 'name',
                'attr' => ['class' => 'form-control'],
                'required' => true,
                 // Définit la valeur par défaut
                'query_builder' => function (EntityRepository $er) use ($activityIds) {
                    return $er->createQueryBuilder('a')
                        ->where('a.id IN (:ids)')
                        ->setParameter('ids', $activityIds)
                        ->andwhere('a.isEnabled = :enabled')
                        ->setParameter('enabled', true)
                        ->orderBy('a.name', 'ASC');
                },
                'data' => isset($selectedActivity) ? $selectedActivity : null,
            ])
            ->add('animators', EntityType::class, [
                'label' => 'Rattacher à un/des animateurs',
                'help' => 'Appuyer sur la touche "CONTROL" en cliquant pour faire des sélections multiples',
                'attr' => ['class' => 'form-control'],
                'class' => Animator::class,
                'choice_label' => function (Animator $user) {
                    return $user->getCompleteNameByLastName(); // Appel de la méthode pour obtenir le nom complet
                },
                'multiple' => true,
                'required' => false,
            ])
            ->add('user', EntityType::class, [
                'label' => 'Rattacher à un administrateur',
                'attr' => ['class' => 'form-control'],
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return $user->getCompleteNameByLastName(); // Appel de la méthode pour obtenir le nom complet
                },
                'required' => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.roles LIKE :role')  // Cherche les utilisateurs avec ROLE_ADMIN dans le tableau des rôles
                        ->setParameter('role', '%ROLE_ADMIN%')  // Recherche la chaîne 'ROLE_ADMIN' dans la colonne roles
                        ->orderBy('u.lastName', 'ASC'); // Vous pouvez aussi trier par firstName ou lastName
                },
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
                    // new Image([
                    //     'maxHeight' => 1100,
                    //     'maxWidth' => 2000,
                    // ])
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
                    // new Image([
                    //     'maxHeight' => 1100,
                    //     'maxWidth' => 2000,
                    // ])
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
                    // new Image([
                    //     'maxHeight' => 1100,
                    //     'maxWidth' => 2000,
                    // ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
            'activity_ids' => [],
            'selected_activity' => null,
            'creation_date' => null,
        ]);
    }
}
