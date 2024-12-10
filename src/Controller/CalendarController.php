<?php

namespace App\Controller;

use App\Repository\ActivityRepository;
use App\Repository\EventRepository;
use App\Service\EventService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/calendar', name: 'calendar_')]
class CalendarController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(Request $request, EventRepository $eventRepository, ActivityRepository $activityRepository, EventService $eventService): Response
    {   
        $yearChoice = $request->query->get('yearChoice') ?? "yearDepth";
        $userChoice = $request->query->get('userChoice') ?? "all";
        $activityChoice = $request->query->get('activityChoice') ?? "all";
        $animatorChoice = $isPassedChoice = $isCanceledChoice = $isEnabledChoice = 'all';
        $eventIdChoice = null;
        
        $activityList = $activityRepository->findByMonthAndYearForCalendar($yearChoice);
        $userList = $eventRepository->getDistinctCreator();

        $justEnabledEvents = TRUE;
        $events = $eventRepository->getEventListforCalendarFor12Months(
            $activityChoice, 
            $userChoice,
            $yearChoice,
            $animatorChoice, 
            $isPassedChoice, 
            $isCanceledChoice, 
            $isEnabledChoice, 
            $eventIdChoice, 
            $justEnabledEvents
        );

        $eventDateJson = $eventService->getEventListForCalendarEvents($events);

        $dates = [];
        foreach ($events as $event) {
            $dates [] = $event->getDateStartAt()->format('m/d/Y');
        }
        $dateCounts = array_count_values($dates);

        return $this->render('calendar/index.html.twig', [
            'eventsCount' => count($events),
            'eventDateJson' => $eventDateJson,
            'dateCounts' => json_encode($dateCounts),
            'activityList' => $activityList,
            'activityChoice' => $activityChoice,
            'yearChoice' => $yearChoice,
            'yearsList' => $eventRepository->getDistincYearCreatedAt(),
            'userList' => $userList,
            'userChoice' => $userChoice
        ]);
    }
}