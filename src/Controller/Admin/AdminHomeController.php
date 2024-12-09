<?php

namespace App\Controller\Admin;

use App\Repository\ActivityRepository;
use App\Repository\AnimatorRepository;
use App\Repository\EventRepository;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'admin_')]

class AdminHomeController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(EventRepository $eventRepository, ActivityRepository $activityRepository, 
    AnimatorRepository $animatorRepository, UserRepository $userRepository, NotificationRepository $notificationRepository): Response
    {   
        $user = $this->getUser();
        
        if(in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            $activitiesForThisUser = $activityRepository->findBy([], ['name' => 'ASC']);
            $notifications = $notificationRepository->findBy([], ['createdAt' => 'DESC']);
        }else{
            $activitiesForThisUser = $user->getActivitiesByName();
            $notifications = $notificationRepository->getAllNotificationsEnabled();
        }

        return $this->render('admin/index.html.twig', [
            'activitiesForThisUser' => $activitiesForThisUser,
            'lastEventCreated' => $eventRepository->findLastEventsforAdmin(3),
            'administratorsKpi' => count($userRepository->adminsList()),
            'animatorsKpi' => $animatorRepository->getAllAnimatorsCount(),
            'eventsKpi' => $eventRepository->getAllEventsCount(),
            'activitiesKpi' => $activityRepository->getAllActivitiesCount(),
            'notifications' => $notifications,
        ]);
    }

}
