<?php

namespace App\Controller;

use App\Repository\EventRepository;
use App\Repository\ActivityRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home_index')]
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
            'events' => $eventRepository->findEventsForHomePage(),
            'isEventActionsButtonVisible' => false,
            'nextUpcomingEvent' => $eventRepository->findNextUpcomingEvent(),
            'lastPastEvent' => $lastPastEvent,
            'lastPastEventList' => $lastPastEventList
        ]);
    }

}
