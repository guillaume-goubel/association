<?php

namespace App\Controller;

use App\Repository\ActivityRepository;
use App\Repository\EventRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/agenda', name: 'agenda_')]
class AgendaController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(Request $request, EventRepository $eventRepository, ActivityRepository $activityRepository, PaginatorInterface $paginator): Response
    {   

        $yearChoice = $request->query->get('yearChoice') ?? date("Y");
        $monthChoice = $request->query->get('monthChoice') ?? 'all';

        $activityChoice = $request->query->get('activityChoice') ?? "all";

        // Distinct month / year createdAt for select
        $yearsList = $eventRepository->getDistincYearCreatedAtForAgendaView();
        $monthsList = $eventRepository->getDistinctMonthCreatedAtForAgendaView($yearChoice);
        $activityList = $activityRepository->findByMonthAndYearForAgenda($monthChoice, $yearChoice, );

        // Pagination
        $eventsQuery = $eventRepository->getEventListforAgenda($yearChoice, $monthChoice, $activityChoice);
        // Définir la page actuelle (par défaut, la page 1)
        $page = $request->query->getInt('page', 1); 

        // Paginer les résultats
        $pagination = $paginator->paginate(
            $eventsQuery, // la requête ou liste d'objets
            $page,        // page actuelle
            8    // nombre d'événements par page (vous pouvez ajuster ce chiffre)
        );

        return $this->render('agenda/index.html.twig', [
            'events' => $pagination,
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
