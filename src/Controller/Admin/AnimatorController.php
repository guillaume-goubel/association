<?php

namespace App\Controller\Admin;

use App\Entity\Animator;
use App\Form\AnimatorType;
use App\Service\MediaService;
use App\Service\AnimatorService;
use App\Repository\AnimatorRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\CheckAuthorizationService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/animator', name: 'admin_animator_')]
class AnimatorController extends AbstractController
{

    #[Route('/index', name: 'index')]
    public function index(AnimatorRepository $animatorRepository, Request $request, PaginatorInterface $paginator, CheckAuthorizationService $checkAuthorizationService): Response
    {
        
        $activityChoice = $request->query->get('activityChoice') ?? "all";
        $animatorChoice = $request->query->get('animatorChoice') ?? "all";

        $eventsQuery = $animatorRepository->queryOrderingByName($activityChoice, $animatorChoice);
        
        // Définir la page actuelle (par défaut, la page 1)
        $page = $request->query->getInt('page', 1); 

        // Paginer les résultats
        $pagination = $paginator->paginate(
            $eventsQuery, // la requête ou liste d'objets
            $page,        // page actuelle
            8      // nombre d'événements par page (vous pouvez ajuster ce chiffre)
        );

        $activityList = [];
        $uniqueKeys = []; // Tableau pour suivre les combinaisons uniques de "id" et "name"
        $animatorList = $animatorRepository->animatorsList();
        foreach ($animatorList as $animator) {
            foreach ($animator->getActivitiesUniqByNameAndId() as $activity) {
                // Générer une clé unique basée sur l'ID et le nom
                $key = $activity['id'] . '_' . $activity['name'];
        
                // Ajouter à la liste uniquement si la clé n'est pas déjà vue
                if (!in_array($key, $uniqueKeys, true)) {
                    $uniqueKeys[] = $key;
                    $activityList[] = $activity;
                }
            }
        }
        
        return $this->render('admin/animator/index.html.twig', [
            'animators' => $pagination,
            'animatorList' => $animatorList,
            'animatorChoice' => $animatorChoice,
            'activityList' => $activityList,
            'activityChoice' => $activityChoice
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, AnimatorService $animatorService, CheckAuthorizationService $checkAuthorizationService): Response
    {
        if (!$checkAuthorizationService->checkProcess($this->getUser(), 'admin_animator_new', null)) {
            // Redirige vers la liste des événements si les IDs ne correspondent pas
            return $this->redirectToRoute('admin_index');
        }
        
        $animator = new Animator();
        $form = $this->createForm(AnimatorType::class, $animator,[
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $animatorService->process($animator);
            
            $entityManager->persist($animator);
            $entityManager->flush();

            $this->addFlash('success', 'Opération effectuée');
            return $this->redirectToRoute('admin_animator_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/animator/new.html.twig', [
            'animator' => $animator,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Animator $animator): Response
    {
        return $this->render('admin/animator/show.html.twig', [
            'animator' => $animator,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Animator $animator, EntityManagerInterface $entityManager, AnimatorService $animatorService, CheckAuthorizationService $checkAuthorizationService): Response
    {
        if (!$checkAuthorizationService->checkProcess($this->getUser(), 'admin_animator_edit', null)) {
            // Redirige vers la liste des événements si les IDs ne correspondent pas
            return $this->redirectToRoute('admin_index');
        }
        
        $form = $this->createForm(AnimatorType::class, $animator,[
            'current_user' => $animator->getUser()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $animatorService->process($animator);

            $entityManager->flush();
            
            $this->addFlash('success', 'Opération effectuée');
            return $this->redirectToRoute('admin_animator_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/animator/edit.html.twig', [
            'animator' => $animator,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['GET'])]
    public function delete(Animator $animator, EntityManagerInterface $entityManager, AnimatorService $animatorService, CheckAuthorizationService $checkAuthorizationService): Response
    {
        if (!$checkAuthorizationService->checkProcess($this->getUser(), 'admin_animator_delete', null)) {
            // Redirige vers la liste des événements si les IDs ne correspondent pas
            return $this->redirectToRoute('admin_index');
        }
        
        // delete images
        $animatorService->deleteAllAnimatorPictures($animator); 

        $entityManager->remove($animator);
        $entityManager->flush();
        $this->addFlash('success', 'Opération effectuée');
        
        return $this->redirectToRoute('admin_animator_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/picture_delete', name: 'picture_delete')]
    public function pictureFileMainDelete(Animator $animator, MediaService $mediaService, EntityManagerInterface $em)
    {
        // Delete data
        $mediaService->deleteAnimatorPictures($animator->getPicture());

        // Delete on base
        $animator->setPicture(NULL);
        $em->flush();

        $this->addFlash('success', 'Opération effectuée');
        return $this->redirectToRoute('admin_animator_edit', ['id' => $animator->getId()], Response::HTTP_SEE_OTHER);

    }
}
