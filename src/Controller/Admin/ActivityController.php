<?php

namespace App\Controller\Admin;

use App\Repository\ActivityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin', name: 'admin_')]

class ActivityController extends AbstractController
{
    #[Route('/activity', name: 'activity')]
    public function index(ActivityRepository $activityRepository, Request $request): Response
    {   
        $user = $this->getUser();
        $userChoice = $request->query->get('userChoice') ?? $user->getId();

        // if(in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
        //     $activities = $activityRepository->findAll();
        // }else{
        //     $activities = $activityRepository->getActivitiesByUser($userChoice);
        // }

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
}
