<?php

namespace App\Controller;

use App\Repository\EventRepository;
use App\Repository\ActivityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('', name: 'home_')]

class HomeController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ActivityRepository $activityRepository, EventRepository $eventRepository): Response
    {   
        // $eventRepository->findLastPastEvent()
        $lastPastEventList = $eventRepository->findLastPastEventsList();
        $lastPastEvent = null;

        foreach ($lastPastEventList as $event) {
            if ($event !== null) {
                $lastPastEvent = $event;
                break; // On arrête la boucle dès qu'on trouve un élément non nul
            }
        } 

        return $this->render('home/index.html.twig', [
            "activities" => $activityRepository->findBy(["isEnabled"=>1], ['ordering' => 'ASC']),
            'isEventActionsButtonVisible' => false,
            'nextUpcomingEvent' => $eventRepository->findNextUpcomingEvent(),
            'nextUpcomingList' => $eventRepository->findNextUpcomingList(),
            'lastPastEvent' => $lastPastEvent,
            'lastPastEventList' => $lastPastEventList
        ]);
    }

    #[Route('/activity/infos', name: 'activity_infos', methods: ['POST'])]

    public function activityInfos(Request $request, ActivityRepository $activityRepository):JsonResponse
    {
        $activityId = $request->request->get('activityId');
        $activity = $activityRepository->findOneBy(['id' => $activityId]);

        // dd('Activity')

        return new JsonResponse([
            'content' => $this->renderView('home/components/_activity_infos.html.twig', [
                'activity' => $activity,
            ])
        ]);
    }

}
