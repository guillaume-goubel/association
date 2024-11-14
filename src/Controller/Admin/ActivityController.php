<?php

namespace App\Controller\Admin;

use App\Entity\Activity;
use App\Form\ActivityType;
use App\Repository\ActivityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin', name: 'admin_activity_')]

class ActivityController extends AbstractController
{
    #[Route('/activity', name: 'index')]
    public function index(ActivityRepository $activityRepository, Request $request): Response
    {   
        $user = $this->getUser();
        $userChoice = $request->query->get('userChoice') ?? $user->getId();
        if(in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            $userChoice = 'all';
        }

        $activities = $activityRepository->getActivitiesByUser($userChoice);

        $userActivityArray = [];
        $noUserActivityArray = [];
        foreach ($activities as $activity) {
            if ($activity->isActivityInUserControl($user) === true) {
                array_push($userActivityArray, $activity);
            }else{
                array_push($noUserActivityArray, $activity);
            }
        }

        $userList = [];
        $tempUserArray = [];
        
        // Récupérer tous les identifiants d'utilisateurs sans doublons
        foreach ($activityRepository->findAll() as $activity) {
            foreach ($activity->getUsers() as $user) {
                $userId = $user->getId();
                // Si l'utilisateur n'a pas encore été ajouté, on l'ajoute
                if (!isset($tempUserArray[$userId])) {
                    $tempUserArray[$userId] = [
                        'id' => $userId,
                        'name' => $user->getFirstName() . ' ' . $user->getLastName()
                    ];
                }
            }
        }
        
        // Transformer le tableau temporaire en tableau final
        $userList = array_values($tempUserArray);

        return $this->render('admin/activity/index.html.twig', [
            "activities" => $activities,
            "userActivityArray" => $userActivityArray,
            "noUserActivityArray" => $noUserActivityArray,
            'userList' => $userList,
            'userChoice' => $userChoice,
        ]);

    }

    #[Route('/activity/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $activity = new Activity();
        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($activity);
            $entityManager->flush();

            $this->addFlash('success', 'Opération effectuée');
            return $this->redirectToRoute('admin_activity_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/activity/new.html.twig', [
            'activity' => $activity,
            'form' => $form,
        ]);
    }

    #[Route('/activity/{id}', name: 'show', methods: ['GET'])]
    public function show(Activity $activity): Response
    {
        return $this->render('admin/activity/show.html.twig', [
            'activity' => $activity,
        ]);
    }

    #[Route('/activity/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Activity $activity, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Opération effectuée');
            return $this->redirectToRoute('admin_activity_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/activity/edit.html.twig', [
            'activity' => $activity,
            'form' => $form,
            'isUserActivityOwner' => $activity->isActivityInUserControl($user)
        ]);
    }

    #[Route('/activity/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Activity $activity, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$activity->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($activity);
            $entityManager->flush();
        }
        $this->addFlash('success', 'Opération effectuée');
        return $this->redirectToRoute('admin_activity_index', [], Response::HTTP_SEE_OTHER);
    }
    
}
