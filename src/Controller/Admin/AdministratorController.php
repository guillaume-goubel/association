<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\AdministratorType;
use App\Repository\UserRepository;
use App\Repository\ActivityRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\CheckAuthorizationService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/admin/administrator', name: 'admin_administrator_')]
class AdministratorController extends AbstractController
{
    #[Route('/index', name: 'index', methods: ['GET'])]
    public function index(UserRepository $userRepository, ActivityRepository $activityRepository, Request $request, PaginatorInterface $paginator, CheckAuthorizationService $checkAuthorizationService): Response
    {   
        
        if (!$checkAuthorizationService->checkProcess($this->getUser(), 'admin_administrator_index', null)) {
            // Redirige vers la liste des événements si les IDs ne correspondent pas
            return $this->redirectToRoute('admin_index');
        }
        
        $activityChoice = $request->query->get('activityChoice') ?? "all";
        $adminChoice = $request->query->get('adminChoice') ?? "all";

        $eventsQuery = $userRepository->findAdmins($activityChoice, $adminChoice);
        
        // Définir la page actuelle (par défaut, la page 1)
        $page = $request->query->getInt('page', 1); 

        // Paginer les résultats
        $pagination = $paginator->paginate(
            $eventsQuery, // la requête ou liste d'objets
            $page,        // page actuelle
            8      // nombre d'événements par page (vous pouvez ajuster ce chiffre)
        );
        
        $adminsList = $userRepository->adminsList();
        
        return $this->render('admin/administrator/index.html.twig', [
            'admins' => $pagination,
            'adminsList' => $adminsList,
            'adminChoice' => $adminChoice,
            'activityList' => $activityRepository->findActivitiesWithUsers(),
            'activityChoice' => $activityChoice,
        ]);

    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, CheckAuthorizationService $checkAuthorizationService): Response
    {
        if (!$checkAuthorizationService->checkProcess($this->getUser(), 'admin_administrator_new', null)) {
            // Redirige vers la liste des événements si les IDs ne correspondent pas
            return $this->redirectToRoute('admin_index');
        }
        
        $user = new User();
        $form = $this->createForm(AdministratorType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $plaintextPassword = $form->get('plainPassword')->getData();
            
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $plaintextPassword
            );
            $user->setPassword($hashedPassword);

            // set Role
            $user->setRoles(['ROLE_ADMIN']);
            
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Opération effectuée');
            return $this->redirectToRoute('admin_administrator_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/administrator/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, CheckAuthorizationService $checkAuthorizationService): Response
    {
        if (!$checkAuthorizationService->checkProcess($this->getUser(), 'admin_administrator_delete', null)) {
            // Redirige vers la liste des événements si les IDs ne correspondent pas
            return $this->redirectToRoute('admin_index');
        }
        
        $isNewUser = !$user->getId();
        
        $form = $this->createForm(AdministratorType::class, $user, [
            'is_new_user' => $isNewUser
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            // Si le mot de passe est rempli, on le met à jour
            if ($user->getPlainPassword()) {
                $hashedPassword = $passwordHasher->hashPassword($user, $user->getPlainPassword());
                $user->setPassword($hashedPassword);
            }
            
            $entityManager->flush();
            
            $this->addFlash('success', 'Opération effectuée');
            return $this->redirectToRoute('admin_administrator_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/administrator/edit.html.twig', [
            'admin' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['GET'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($user);
        $entityManager->flush();
        
        $this->addFlash('success', 'Opération effectuée');
        return $this->redirectToRoute('admin_administrator_index', [], Response::HTTP_SEE_OTHER);
    }
}
