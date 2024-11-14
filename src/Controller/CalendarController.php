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
        // $yearChoice = $request->query->get('yearChoice') ?? date("Y");
        $activityChoice = $request->query->get('activityChoice') ?? "all";

        // $yearsList = $eventRepository->getDistincYearCreatedAtForAgendaView();
        $activityList = $activityRepository->findBy([], ['name' => 'ASC']);

        $events = $eventRepository->getEventListforCalendarFor12Months($activityChoice);
        $eventDateJson = $eventService->getEventListForCalendarEvents($events);

        return $this->render('calendar/index.html.twig', [
            'events' => $events,
            'eventDateJson' => $eventDateJson,
            // 'yearsList' => $yearsList,
            'activityList' => $activityList,
            // 'yearChoice' => $yearChoice,
            'activityChoice' => $activityChoice,
        ]);
    }
}