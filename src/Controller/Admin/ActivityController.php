<?php

namespace App\Controller\Admin;

use App\Entity\Activity;
use App\Form\ActivityType;
use App\Repository\ActivityRepository;
use App\Repository\UserRepository;
use App\Service\CheckAuthorizationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin', name: 'admin_activity_')]

class ActivityController extends AbstractController
{
    #[Route('/activity', name: 'index')]
    public function index(ActivityRepository $activityRepository, Request $request, UserRepository $userRepository): Response
    {   
        $user = $this->getUser();
        $userChoice = $request->query->get('userChoice') ?? $user->getId();
        if(in_array('ROLE_SUPER_ADMIN', $user->getRoles()) && $request->query->get('userChoice') == null) {
            $userChoice = 'all';
        }

        $activities = $activityRepository->getActivitiesByUser($userChoice);

        $userActivityArray = [];
        $noUserActivityArray = [];
        foreach ($activities as $activity) {
            if ($activity->isActivityInUserControl($user) === true) {
                $activity->setIsActivityInUserControl(true);
                array_push($userActivityArray, $activity);
            }else{
                $activity->setIsActivityInUserControl(false);
                array_push($noUserActivityArray, $activity);
            }
        }

        // Récupérer tous les identifiants d'utilisateurs sans doublons
        $userList = [];
        $tempUserArray = [];
        foreach ($activityRepository->findAll() as $activity) {
            foreach ($activity->getUsers() as $user) {
                $userId = $user->getId();
                // Si l'utilisateur n'a pas encore été ajouté, on l'ajoute
                if (!isset($tempUserArray[$userId])) {
                    $tempUserArray[$userId] = [
                        'id' => $userId,
                        'name' => ucfirst(strtolower($user->getLastName())) . ' ' . ucfirst(strtolower($user->getFirstName())),
                        'lastName' => $user->getLastName(), // Stocker aussi LastName pour le tri
                        'firstName' => $user->getFirstName() // Optionnel si tri secondaire nécessaire
                    ];
                }
            }
        }
        usort($tempUserArray, function($a, $b) {
            return strcmp($a['lastName'], $b['lastName']); // Comparaison alphabétique des noms de famille
        });
        $userList = array_map(function($user) {
            return [
                'id' => $user['id'],
                'name' => $user['name']
            ];
        }, $tempUserArray);

        $userChoiceInfos = ($userChoice !== 'all') ? $userRepository->findOneBy(['id' => $userChoice]) : 'all';

        return $this->render('admin/activity/index.html.twig', [
            "userActivityArray" => $userActivityArray,
            "noUserActivityArray" => $noUserActivityArray,
            'userList' => $userList,
            'userChoice' => $userChoice,
            'userChoiceInfos' => $userChoiceInfos,
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
    public function edit(Request $request, Activity $activity, EntityManagerInterface $entityManager, CheckAuthorizationService $checkAuthorizationService): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            if ($checkAuthorizationService->checkProcess($this->getUser(), 'admin_activity_edit', null)) {
                $entityManager->flush();
                $this->addFlash('success', 'Opération effectuée');    
            }

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
