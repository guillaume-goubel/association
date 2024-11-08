<?php

namespace App\Controller;

use App\Repository\ActivityRepository;
use App\Repository\EventRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/agenda', name: 'agenda_')]
class AgendaController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(Request $request, EventRepository $eventRepository, ActivityRepository $activityRepository): Response
    {   

        $yearChoice = $request->query->get('yearChoice') ?? date("Y");
        $monthChoice = $request->query->get('monthChoice') ?? date("m");
        $activityChoice = $request->query->get('activityChoice') ?? "all";

        // Distinct month / year createdAt for select
        $yearsList = $eventRepository->getDistincYearCreatedAt();
        $monthsList = $eventRepository->getDistinctMonthCreatedAt($yearChoice);
        $activityList = $activityRepository->findAll();

        return $this->render('agenda/index.html.twig', [
            
            'events' => $eventRepository->getEventListforAgenda($yearChoice, $monthChoice, $activityChoice),
            'yearsList' => $yearsList,
            'monthsList' => $monthsList,
            'activityList' => $activityList,
            'yearChoice' => $yearChoice,
            'monthChoice' => $monthChoice,
            'activityChoice' => $activityChoice,
            'isEventActionsButtonVisible' => false
            
        ]);
    }
}
