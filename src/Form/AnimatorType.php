<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Event;
use App\Entity\Animator;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AnimatorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        $currentUser = $options['current_user'];

        $builder
            ->add('isEnabled', CheckboxType::class, [
                'label' => "Activer / Désactiver",
                'attr' => ['class' => 'form-check-input'], // Classe Bootstrap pour les switches
                'required' => false,
                'help' => "Cochez pour activer ou désactiver l'animateur.",
            ])
            ->add('firstName', TextType::class, [
                'label' => "Prénom *",
                'attr' => ['class' => 'form-control'],
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => "Courte présentation (190 caractères max)",
                'required' => false,
                'attr' => [
                    'rows' => '3' , 
                    'class' => 'form-control', 'maxlength' => 190, 
                    // 'placeholder' => "vous pouvez mettre ici une description - NON OBLIGATOIRE",
                    'style' => 'resize: vertical;height: 120px;'
                ],
                                'constraints' => [
                    new Length([
                        'max' => 190,
                        'maxMessage' => "La description ne peut pas dépasser {{ limit }} caractères."
                    ])
                ]
            ])
            ->add('lastName', TextType::class, [
                'label' => "Nom *",
                'attr' => ['class' => 'form-control'],
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => "Email",
                'attr' => ['class' => 'form-control', 'maxlength' => 75],
                'required' => false
            ])   
            ->add('phone', TextType::class, [
                'label' => "Numéro de téléphone",
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('pictureFile', FileType::class, [
                'label' => 'Image',
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
        ->add('user', EntityType::class, [
            'label' => 'Administrateur rattaché',
            'class' => User::class,
            'choice_label' => function (User $user) {
                return '#'.$user->getId()." ".$user->getCompleteNameByLastName(); // Appel de la méthode pour obtenir le nom complet
            },
            'attr' => ['class' => 'form-control'],
            'query_builder' => function (EntityRepository $er) use ($options) {
                // Récupérez le `user` actuellement rattaché
                $currentUser = $options['current_user'];
        
                // Construisez la requête
                $qb = $er->createQueryBuilder('u')
                    ->leftJoin('u.animator', 'a')
                    ->where('u.roles LIKE :role')  // Cherche les utilisateurs avec ROLE_ADMIN
                    ->setParameter('role', '%ROLE_ADMIN%')
                    ->orderBy('u.lastName', 'ASC');
        
                if ($currentUser) {
                    $qb->andWhere('a.id IS NULL OR u.id = :currentUser')
                    ->setParameter('currentUser', $currentUser->getId());
                } else {
                    $qb->andWhere('a.id IS NULL');
                }
        
                return $qb;
            },
            'placeholder' => '...',
            'required' => false,
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Animator::class,
            'current_user' => null,
        ]);
    }
}
